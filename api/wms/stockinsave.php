<?php
/*调用数据层类与配置*/
require('../../../initphp/initphp.php');
require('../../conf.inc.php');
require('wmsconf.php');
require('../../lib/nusoap/nusoap.php');

function StockInSave($backdata,$shipfare = '',$username,$token) {

	$C				= new C();//实例化控制层
	if(!$C->C->service('global')->checktoken($token)) return false;

	$backdata		= base64_decode($backdata);
	$backdata		= iconv('GBK','UTF-8',$backdata);

	/*将XML数据转换成数组*/
	$initobj		= new InitPHP();
	$backdataArr	= $initobj->getLibrary('xmlmod')->xmltoarray($backdata);
	$datalist		= $backdataArr['Table1'];

	$detail_id		= array();
	$quantity		= array();
	$comment		= array();

	/*提取内容组装成数组(防WEB)*/
	foreach($datalist as $val){
		$detail_id[]	= $val['detail_id'];
		$quantity[]		= $val['InQty'];
		$comment[]		= empty($val['Remark'])?'':$val['Remark'];
	}

	/*批量采购时对传过来的上级明细ID进行升序排序处理，否则可能数据不正确*/
	sort($detail_id);

	$process		= $C->S->dao('process');//实例化表
	$worder_id		= $C->C->service('warehouse')->get_maxorder_manay('进仓单','w',$process);//取最大采购进仓单号
	$username		= $C->S->dao('user')->D->get_one(array('username'=>$username),'eng_name');


	/*取得需复制数据*/
 	$strid 		= '('.implode(',',$detail_id).')';
 	$copydata 	= $process->D->get_allstr(' and id in'.$strid,'','id asc','id,quantity,countnum,provider_id,receiver_id,sku,fid,pid,product_name,price,coin_code,stage_rate,cuser');

	/*失败记录统计*/
	$error_num				= 0;
	$announce_array			= array();
	$announce_caiar			= array();
	$sucess_msg				= '入库成功';
	$failed_msg				= '入库失败，请重试';
	$C->C->service('exchange_rate');//实体化自动包含文件，用于成本转换
	$finance				= $C->C->service('finance');
	$product_cost			= $C->S->dao('product_cost');

	/*事务开始---------------START*/
	$process->D->query('BEGIN');
	$order_id = $process->D->get_one(array('id'=>$detail_id['0']),'order_id');//不同于WEB，需要获取订单号

 	/*有填写到付运费，回写采购单的产品采购成本*/
 	$rewrite_errornum 		= 0;
 	if(!empty($shipfare)){
		$backdata 			= $finance->stockorder_addfare($process,$order_id,'到付运费',$shipfare,'fee2');
		$rewrite_errornum	= $backdata['error'];
 	}

	/*插入进仓单，复制采购单部分内容,备注与数量根据页面填写传送过来,($sid插入进仓单,$cid回写累计执行量,$jid回写产品成本价)*/
 	for($i=0;$i<count($copydata);$i++){

 		if(!empty($quantity[$i])){

			/*移动加权平均更新产品成本(CNY)*/
			$backcount_cost 	= $finance->countnum_cost($process, $copydata[$i]['id'], $quantity[$i]);
			$jid 				= $finance->updatecost($copydata[$i]['pid'],$backcount_cost,1.05*$backcount_cost,date('Y-m-d H:i:s',time()));

			/*插入进仓单*/
		 	$sid 				= $process->D->insert(array('provider_id'=>$copydata[$i]['provider_id'],'receiver_id'=>$copydata[$i]['receiver_id'],'sku'=>$copydata[$i]['sku'],'detail_id'=>$detail_id[$i],'fid'=>$copydata[$i]['fid'],'pid'=>$copydata[$i]['pid'],'product_name'=>$copydata[$i]['product_name'],'price'=>$copydata[$i]['price'],'price2'=>$backcount_cost,'coin_code'=>$copydata[$i]['coin_code'],'stage_rate'=>$copydata[$i]['stage_rate'],'quantity'=>$quantity[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$username,'muser'=>$username,'ruser'=>$username,'active'=>'1','order_id'=>$worder_id,'property'=>'进仓单','protype'=>'采购','input'=>'1','comment'=>$comment[$i]));

			/*回写累计执行量*/
			$newcountnum		= $copydata[$i]['countnum']+$quantity[$i];
			$cid 				= $process->D->update_by_field(array('id'=>$detail_id[$i]),array('countnum'=>$newcountnum));

			/*累计执行量若小于原单数量，记录通知采购*/
			if($newcountnum < $copydata[$i]['quantity']){
				$announce_caiar[] = array('order_id'=>$order_id,'quantity'=>$quantity[$i],'squantity'=>$copydata[$i]['quantity'],'sku'=>$copydata[$i]['sku'],'countnum'=>$newcountnum,'cuser'=>$copydata[$i]['cuser']);
			}

			/*到货通知销售客服*/
			$announce_array[] 	= array('c_id'=>$detail_id[$i],'quantity'=>$quantity[$i],'countnum'=>$newcountnum);
			if(!$sid || !$cid || !$jid) $error_num++;
 		}
 	}

 	/*判断是否全部成功*/
 	if (empty($error_num) && empty($rewrite_errornum)){

 		/*统计订单中每条记录的累计执行量,如果相等，关闭该订单*/
 		$newbacklist	= $process->D->get_allstr(' and order_id="'.$order_id.'" and statu!="4" ','','','id,quantity,countnum');
 		$checkisover	= 0;//订单完成量标记
 		foreach($newbacklist as $val){
 			if($val['countnum'] < $val['quantity']){//如果累计执行量比原单数量要小
 				$calnums = $process->D->get_one(array('detail_id'=>$val['id'],'property'=>'采购单'),'sum(quantity)');
 				if($calnums){
	 				if(($val['quantity'] - $val['countnum']) > -$calnums) $checkisover++;//如需入库的数量比红单数量大，则算未完成入库。
 				}else{
 					$checkisover++;//不存在红单，则未算完成入库。
 				}
 			}
 		}

 		if(empty($checkisover)){//标记完成
 			$bid = $process->D->update_by_field(array('order_id'=>$order_id),array('isover'=>'Y'));
	 		if($bid){
	 			$process->D->query('COMMIT');//如果全部记录复制成功，并回写成功，再更新状态成功才提交。
				$C->C->service('global')->announce_restock($announce_array,$process,'upstock');//发起通知
				return 1;exit;
	 		}else{
	 			$process->D->query('ROLLBACK');
				return $failed_msg;exit;
	 		}
 		}else{
 			/*如果累计执行量不够，不关闭订单，直接提交。该订单会继续在预入库显示*/
			$process->D->query('COMMIT');
			$C->C->service('global')->announce_restock($announce_array,$process,'upstock');//发起通知
			$C->C->service('global')->announce_restock($announce_caiar,$process,'modstock');//通知采购入库数小于订单数
			return 1;;exit;
 		}

 	}else{
		/*失败回滚*/
 		$process->D->query('ROLLBACK');
		return $failed_msg;exit;
 	}
 	/*事务结束----------------END*/
}

//LK
$namespace = "http://erp.loftk.com.cn/erp/api/wms";
//MIU
//$namespace = "http://erp.miucolor.com/miu/api/wms";
$server = new soap_server();
$server->configureWSDL("StockInSaveService");
$server->wsdl->schemaTargetNamespace = $namespace;
$server->register(
    'StockInSave',
    array('backdata'=>'xsd:string','shipfare'=>'xsd:string','username'=>'xsd:string','token'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    '采购入库保存'
);

$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

$server->service($POST_DATA);
exit();
?>
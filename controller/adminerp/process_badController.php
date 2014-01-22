<?php
/*
 * Created on 2012-10-16
 * by hanson 不良品调拨
 */

if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'sku-s-l'=>'SKU:',
		'order_id-s-e'=>'调拨单号:',
		'prorder_id-s-e'=>'关联单号:',
		'provider_id-a-e'=>'仓库:',
	);

	/*join表查询替换*/
	$sqlstr = strtr($sqlstr,array('sku'=>'p1.sku','order_id'=>'p1.order_id','prorder_id'=>'p2.order_id','provider_id'=>'p1.provider_id'));

	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
	$provider_idarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$provider_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

	$InitPHP_conf['pageval'] 	= 15;
	$process					= $this->S->dao('process');
	$datalist					= $process->get_badlist($sqlstr);

	/*数据处理*/
	foreach($datalist as &$val){
		$val['preorder_id'] = empty($val['preorder_id']) ? '<font color=#bdbdbd>--</font>' : $val['preorder_id'];

		switch($val['comment2']){
			case 'defective_customer'	:$val['type'] = '客户损坏';break;
			case 'damaged_distributor'	:$val['type'] = '供应商损坏';break;
			case 'damaged_carrier'		:$val['type'] = '物流损坏';break;
			case 'damaged_warehouse'	:$val['type'] = '仓库损坏';break;
			case 'damaged'				:$val['type'] = '其它损坏';break;
		}
	}

	/*配置输出表单*/
	$displayarr = array();
	$tablewidth = '1150';

	$displayarr['both']			 = array('showname'=>'删除','url_d'=>'index.php?action=process_bad&detail=del&id={id}','url_e'=>'index.php?action=process_bad&detail=edit&id={id}','ajax'=>'1','width'=>'50');
	$displayarr['order_id'] 	 = array('showname'=>'调拨单号','width'=>'80');
	$displayarr['preorder_id'] 	 = array('showname'=>'关联单号','width'=>'80');
	$displayarr['sku'] 	 		 = array('showname'=>'SKU','width'=>'80');
	$displayarr['product_name']  = array('showname'=>'产品名称','width'=>'200');
	$displayarr['warehouse'] 	 = array('showname'=>'不良品归属仓','width'=>'100');
	$displayarr['quantity'] 	 = array('showname'=>'数量','width'=>'80');
	$displayarr['type']		 	 = array('showname'=>'不良情况','width'=>'100');
	$displayarr['ruser'] 		 = array('showname'=>'操作','width'=>'80');
	$displayarr['rdate'] 	 	 = array('showname'=>'时间','width'=>'90');

	$bannerstr = '<button onclick=window.location="index.php?action=process_bad&detail=damae" >进入调拨</button>';
	$this->V->mark(array('title'=>'不良品调拨明细'));
	$temp = 'pub_list';
}

/*保存编辑*/
elseif($detail == 'modedit'){

	$process	= $this->S->dao('process');
	$jump_url	= 'index.php?action=process_bad';
	$backnum	= $process->D->get_one(array('id'=>$detail_id),'quantity');

	/*判断数量是否大于原退货单*/
	if($detail_id && $quantity > $backnum){ $this->C->success('保存失败，数量不得大于原退货数量',$jump_url.'&detail=edit&id='.$id);exit; }

	$sid		= $process->D->update(array('id'=>$id),array('quantity'=>$quantity));
	$jump_url  .= '&detail=list';
	if($sid) {$this->C->success('保存成功',$jump_url);}else{$this->C->success('保存失败',$jump_url);}
}

/*编辑数量与损坏情况*/
elseif($detail == 'edit'){

	$disinputarr 				= array();
	$backdata					= $this->S->dao('process')->D->get_one(array('id'=>$id),'quantity,order_id,detail_id,ruser');
	if(!$this->C->service('admin_access')->checkResRight('r_w_backfedit','mod',$backdata['ruser'])){$this->C->sendmsg();}//权限判断

	$conform 					= array('method'=>'post','action'=>'index.php?action=process_bad&detail=modedit');
	$disinputarr['id']			= array('showname'=>'ID','value'=>$id,'datatype'=>'h');
	$disinputarr['detail_id']	= array('showname'=>'上级ID用于判断不得大于上级数量','value'=>$backdata['detail_id'],'datatype'=>'h');
	$disinputarr['order_id'] 	= array('showname'=>'单号','value'=>$backdata['order_id'],'inextra'=>'disabled','showtips'=>'<span class=tips>只供查看</span>');
	$disinputarr['quantity']	= array('showname'=>'数量','value'=>$backdata['quantity']);

	$this->V->mark(array('title'=>'编辑-不良品调拨明细(list)'));
	$temp = 'pub_edit';
}

 /*废品调拔--将某仓库的不良品转至不良品仓*/
 elseif($detail == 'damae'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_backfadd')){$this->C->sendmsg();}//权限判断
	$whouse = $this->C->service('warehouse')->get_whouse('houseid[]','name','id','id',$this->C->service('global')->sys_settings('badtrans_house','sys'));/*获得仓库*/

	$this->V->mark(array('whouse'=>$whouse,'title'=>'调拨-不良品调拨明细(list)'));
	$this->V->set_tpl('adminweb/process_damage_recstock');
	display();
 }

 /*执行保存不良品调拨*/
 elseif($detail =='moddamae'){

 	$error_sku	 = '';
	for ($i=0;$i<count($sku);$i++){
		if(empty($pid[$i]))	$error_sku .=  $sku[$i].',\n';
	}
	if($error_sku){
		$this->C->success('保存失败，存在无法获取产品ID的SKU:\n'.$error_sku.'请检查重试!','index.php?action=process_bad&detail=damae');
		exit();
	}

 	/*失败统计量*/
 	$failroll = 0;

	/*损坏情况数组*/
 	$damagedarr = array('defective_customer','damaged_distributor','damaged_carrier','damaged_warehouse','damaged');

	$msdate	= date('Y-m-d H:i:s',time());
	$process = $this->S->dao('process');

	/*实例化自动包含文件*/
	$this->C->service('exchange_rate');
	$finansvice 	= $this->C->service('finance');


	$process->D->query('begin');//开始一个事务

 	for($i=0;$i<count($sku);$i++){

		/*如各种损坏情况有写，则插入转仓单(接收仓(作为供应者)->不良仓(接收者为空)的转仓单即为不良品仓,comment2保存不同状态)*/
		for($j=0;$j<count($damagedarr);$j++){

			if(${$damagedarr[$j]}[$i]){
				$max_id = $this->C->service('warehouse')->get_maxorder('转仓单','f',$process);

				$insert_arr = array('provider_id'=>$houseid[$i],'sku'=>$sku[$i],'pid'=>$pid[$i],'product_name'=>$product_name[$i],'quantity'=>${$damagedarr[$j]}[$i],'cdate'=>$msdate,'mdate'=>$msdate,'rdate'=>$msdate,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','order_id'=>$max_id,'property'=>'转仓单','output'=>'1','input'=>'1','comment'=>$comment[$i],'comment2'=>$damagedarr[$j]);

				/*增加保存即时成本，期号，币别(CNY)*/
				$finansvice->rewrite_inorup_arr(&$insert_arr,$pid[$i]);

				$cid = $process->D->insert($insert_arr);

				if(!$cid) $failroll++;
			}
		}
 	}

	/*最后判断提交*/
	$overjump = 'index.php?action=process_bad&detail=damae';
 	if($failroll == 0){$process->D->query('commit');$this->C->success('操作成功',$overjump);}else{$process->D->query('rollback');$this->C->success('操作失败',$overjump);}

}

/*删除*/
elseif($detail == 'del'){

	$process = $this->S->dao('process');
	if(!$this->C->service('admin_access')->checkResRight('r_w_backfdel','mod',$process->D->get_one(array('id'=>$id),'ruser'))){$this->C->ajaxmsg();}//权限判断

	if($process->D->delete_by_field(array('id'=>$id))){echo '1';}else{echo '删除失败';}
}

if($detail == 'list'  || $detail == 'damae' || $detail == 'edit' ){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

<?php
/*
 * Created on 2012-1-18
 *
 *@title 出库明细表
 *
 *@author by hanson
 */
 if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'sku-s-l'=>'&nbsp; &nbsp; &nbsp; &nbsp; SKU：',
		'order_id-s-e'=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 单号：',
		'provider_id-a-e'=>'&nbsp; 发出仓库：',
		'muser-s-l'=>'&nbsp; &nbsp; 经办人：',
		'rece_type-a-e'=>'出库类型：',
        'sold_way-a-e'=>'&nbsp;&nbsp;&nbsp;销售渠道：',
		'mdate-t-t'=>'&nbsp;&nbsp;出库日期：',
	);

	/*显示入库类型*/
	$rece_typearr = array(''=>'=请选择=','xr'=>'销售下单出库','fr'=>'物料调拨出库','tr'=>'其它出库');

	if(!empty($rece_type)){
		switch($rece_type){
			case 'xr':$sqlstr = str_replace('and rece_type="xr"','and (p.protype="售出" or p.protype="重发")',$sqlstr);break;
			case 'fr':$sqlstr = str_replace('and rece_type="fr"','and p.property="转仓单"',$sqlstr);$sqlstr .= 'and p.receiver_id >0';break;
			case 'tr':$sqlstr = str_replace('and rece_type="tr"','and p.property="出仓单" and protype="其它"',$sqlstr);break;
		}
	}else{
		$sqlstr .= ' and (property="出仓单"  or property="转仓单") ';
	}

	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	$provider_idarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$provider_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

    /*取得转入仓库下拉-用于生成搜索条件*/
    if ($rece_type == 'fr'){

        $stypemu['receiver_id-a-e'] = '转入仓库：';
    	$receiver_idarr = array(''=>'=请选择=');
    	for($i = 0; $i < count($wdata); $i++){
    		$receiver_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
    	}
    }


    /*生成渠道搜索*/
	$sold_wayarr = $this->C->service('global')->get_sold_way(0,'sold_way','wayname');

	$InitPHP_conf['pageval'] = 20;
    $sqlstr = str_replace("sold_way","p.sold_way",$sqlstr);
	$datalist = $this->S->dao('process')->showoutstocklist($sqlstr);

	/*数据处理*/
	for($i=0;$i<count($datalist);$i++){

		/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd'] = '';
		}else{
			$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
		}
	}
    
    //统计列表总数量
    $_quantity = 0;
    foreach($datalist as $data){
        $_quantity  += $data['quantity'];
    }
    $datalist[] = array('order_idd'=>'<font color="red">总数量</font>','quantity'=>'<font color="red">'.$_quantity.'</font>');
    
	/*定义输出数组*/
	$displayarr = array();
	$tablewidth = '1100';

	$displayarr['order_id'] 	= array('showname'=>'checkbox','width'=>'45');
	$displayarr['delete'] 		= array('showname'=>'删除','ajax'=>'1','url'=>'index.php?action=process_outstock&detail=del&order_id={order_id}','width'=>'50');
	$displayarr['order_idd'] 	= array('showname'=>'单号','width'=>'80');
	$displayarr['sku'] 			= array('showname'=>'产品SKU','width'=>'80');
	$displayarr['jin_sku']		= array('showname'=>'金碟SKU','width'=>'80');
	$displayarr['product_name'] = array('showname'=>'产品名称','width'=>'300');
	$displayarr['warehouse'] 	= array('showname'=>'发出仓库','width'=>'100');
	$displayarr['quantity'] 	= array('showname'=>'数量','width'=>'50');
	$displayarr['muser'] 		= array('showname'=>'经办人','width'=>'80');
    $displayarr['comment2']     = array('showname'=>'物流追踪号','width'=>'120');
	$displayarr['mdate'] 		= array('showname'=>'出库日期','width'=>'100');
    $displayarr['comment']      = array('showname'=>'备注','width'=>'120');

 	$this->V->mark(array('title'=>'出库明细表'));
    $bannerstrarr[] = array('url'=>'index.php?action=process_outstock&detail=outport','value'=>'导出数据');
 	$temp = 'pub_list';
 }

 elseif($detail == 'del'){

	/*删除出库单权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_w_deloutorder')){$this->C->ajaxmsg(0,$msg);}

 	$sid = $this->S->dao('process')->D->delete_by_field(array('order_id'=>$order_id));
 	if($sid) {echo '删除成功';}else{echo '删除失败';}
 }

 /*导出数据*/
 elseif($detail == 'outport'){

    $process = $this->S->dao('process');
    $exchange_rate  = $this->C->service('exchange_rate');
    $sqlstr = str_replace("sold_way","p.sold_way",$sqlstr);

 	if(!empty($rece_type)){
		switch($rece_type){
		    //销售下单出库
			case 'xr':
                $sqlstr = str_replace('and rece_type="xr"','and (p.protype="售出" or p.protype="重发")',$sqlstr);
                $head_array = array('order_id'=>'单据编号','rdate'=>'单据日期','wayname'=>'购货单位','selltype'=>'销售方式','sku'=>'物料代码','erpname'=>'物料名称','weight'=>'规格型号','unit'=>'单位','warehouse'=>'发货仓库','quantity'=>'实发数量','price2'=>'单位成本','price'=>'销售价格','deal_id'=>'平台单号','fid'=>'第三方单号');
                break;
            //物料调拨出库
			case 'fr':
                $sqlstr .= ' and p.receiver_id >0';
                $sqlstr = str_replace('and rece_type="fr"','and p.property="转仓单"',$sqlstr);
                $head_array = array('order_id'=>'单据编号','mdate'=>'单据日期','type'=>'调拨类型','sku'=>'物料代码','erpname'=>'物料名称','weight'=>'规格型号','unit'=>'单位','warehouse'=>'调出仓库','sname'=>'调入仓库','price2'=>'调出单价','price22'=>'调入单价','quantity'=>'数量');
                break;
            //其它出库
			case 'tr':
                $sqlstr = str_replace('and rece_type="tr"','and p.property="出仓单" and protype="其它"',$sqlstr);
                $head_array = array('order_id'=>'单据编号','rdate'=>'单据日期','dept'=>'部门','sold_way'=>'客户','sku'=>'物料代码','erpname'=>'物料名称','weight'=>'规格型号','unit'=>'单位','warehouse'=>'发货仓库','price2'=>'单价','quantity'=>'数量','comment'=>'备注');
                break;
		}
	}else{
		$sqlstr .= ' and (property="出仓单"  or property="转仓单") ';
        $head_array = array('order_id'=>'单号','sku'=>'sku','jin_sku'=>'金碟sku','product_name'=>'产品名称','warehouse'=>'发出仓库','quantity'=>'数量','muser'=>'经办','comment2'=>'物流追踪号','mdate'=>'时间');
	}

	$datalist	= $process->showoutstocklist($sqlstr);
    for($i = 0; $i<count($datalist); $i++){

        $datalist[$i]['type']       = '同价调拨';
        $datalist[$i]['selltype']   = '赊销';
        $datalist[$i]['unit']       = 'pc';//单位
        $datalist[$i]['rdate']      = date('Y-m-d',strtotime($datalist[$i]['rdate']));//制单日期
        $datalist[$i]['mdate']      = date('Y-m-d',strtotime($datalist[$i]['mdate']));//制单日期
        $datalist[$i]['price22']    = $datalist[$i]['price2'];//单价
        $datalist[$i]['dept']       = '公司';//部门
        $datalist[$i]['sold_way']   = '默认';
        $datalist[$i]['erpname']    = '';
        $datalist[$i]['weight']     = '';
        if (in_array($rece_type,array('xr','fr','tr')))
            $datalist[$i]['sku']        = preg_replace('/-/', '.',$datalist[$i]['sku'],1);//替换字符串第一个字符

        //统一转化为人民币
        if($datalist[$i]['coin_code'] != 'CNY'){
            $datalist[$i]['price'] = $exchange_rate->change_rate($datalist[$i]['coin_code'],'CNY',$datalist[$i]['price']);
            $datalist[$i]['price'] = number_format($datalist[$i]['price'],2);
        }

    }
	$filename 	= 'outstock_'.date('Y-m-d',time());

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
 }


 /*模板定义*/
 if($detail == 'list'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>

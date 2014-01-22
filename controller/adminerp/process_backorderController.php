<?php
/*
 * Created on 2012-2-2
 *
 * 退款单明细表
 * by hanson
 */

 if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'ispay-h-e'		=>'是否退款:',
		'sku-s-l'		=>'&nbsp;&nbsp;&nbsp;SKU：',
		'fid-s-e'		=>'&nbsp;&nbsp;第三方单号：',
		'cuser-s-l'		=>'&nbsp;&nbsp;制单人：',
		'statu-a-e'		=>'&nbsp;&nbsp;类型：',
		'comment3-a-e'	=>'<br>&nbsp;&nbsp;&nbsp;原因：',
		'order_id-s-l'	=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;交易ID：',
		'sold_id-a-e'	=>'&nbsp;&nbsp;平台ID：',
	);

	$sold_idarr = $this->C->service('global')->get_sold_way(0,'sold_account','account_name');

	/*标签导航选项*/
	$tab_menu_stypemu = array(
		'ispay-0'=>'未退款',
		'ispay-1'=>'已退款',
	);

	$statuarr 		= array(''=>'=退单类型=','1'=>'全额退款','2'=>'部分退款');
	$comment3arr 	= array(''=>'=退单原因=','unsatisfactory_with_item'=>'Unsatisfactory with item','wrong_purchase'=>'Wrong purchase','sent_wrong_item'=>'Sent wrong item','sent_less_item'=>'Sent less item','warehouse_delay_shipment'=>'Warehouse Delay shipment','lost_on_the_way'=>'Lost on the way','damage_in_delivery'=>'Damage in delivery','carrier_delay_shipment'=>'Carrier Delay shipment','defective_item'=>'Defective item','provide_wrong_description_on_listing'=>'Provide wrong description on listing','mislead_customer_on_listing'=>'Mislead customer on listing','provide_wrong_product_info/tech_support'=>'Provide wrong product info/tech support','others'=>'others');

	if(empty($sqlstr) && !isset($ispay)){$ispay = "0";$sqlstr.=' and ispay="0"';}

	$sqlstr		   .=' and property="退款单" ';
	$datalist 		= $this->S->dao('process')->get_backorlist($sqlstr);

	for($i=0;$i<count($datalist);$i++){

		$datalist[$i]['dele']	  = '<a href=javascript:void(0);delitem("index.php?action=process_backorder&detail=delbackpro&id='.$datalist[$i]['id'].'")  title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';

		/*退单原因显示还原处理*/
		switch ($datalist[$i]['comment3']){
			case 'unsatisfactory_with_item'					:$datalist[$i]['comment3'] = 'Unsatisfactory with item';break;
			case 'wrong_purchase'			 				:$datalist[$i]['comment3'] = 'Wrong purchase';break;
			case 'sent_wrong_item'					 		:$datalist[$i]['comment3'] = 'Sent wrong item';break;
			case 'sent_less_item'			 				:$datalist[$i]['comment3'] = 'Sent less item';break;
			case 'warehouse_delay_shipment'					:$datalist[$i]['comment3'] = 'Warehouse Delay shipment';break;
			case 'lost_on_the_way'							:$datalist[$i]['comment3'] = 'Lost on the way';break;
			case 'damage_in_delivery'						:$datalist[$i]['comment3'] = 'Damage in delivery';break;
			case 'carrier_delay_shipment'					:$datalist[$i]['comment3'] = 'Carrier Delay shipment';break;
			case 'defective_item'							:$datalist[$i]['comment3'] = 'Defective item';break;
			case 'provide_wrong_description_on_listing'		:$datalist[$i]['comment3'] = 'Provide wrong description on listing';break;
			case 'mislead_customer_on_listing'				:$datalist[$i]['comment3'] = 'Mislead customer on listing';break;
			case 'provide_wrong_product_info/tech_support'	:$datalist[$i]['comment3'] = 'Provide wrong product info/tech support';break;
		}
	}

	$tablewidth = '1400';
	$displayarr = array();
	$displayarr['id']			= array('showname'=>'checkbox','width'=>'35');
	$displayarr['order_id']		= array('showname'=>'交易ID','width'=>'100');
	$displayarr['extends']		= array('showname'=>'退款帐号','width'=>'80');
	$displayarr['comment2']		= array('showname'=>'联系人','width'=>'80');
	$displayarr['sku']			= array('showname'=>'sku','width'=>'100');
	$displayarr['fid']			= array('showname'=>'第三方单号','width'=>'100');
	$displayarr['price']		= array('showname'=>'退款金款','width'=>'70');
	$displayarr['coin_code']	= array('showname'=>'币别','width'=>'50');
	$displayarr['wayname']		= array('showname'=>'渠道','width'=>'70');
	$displayarr['account_name']	= array('showname'=>'平台ID','width'=>'70');
	$displayarr['cuser']		= array('showname'=>'制单人','width'=>'70');
	if($ispay == "1"){$displayarr['muser']= array('showname'=>'经办人','width'=>'70');}
	$displayarr['cdate']		= array('showname'=>'时间','width'=>'70');
	$displayarr['comment']		= array('showname'=>'备注','width'=>'70');
	$displayarr['comment3']		= array('showname'=>'退款原因','width'=>'170');
	if($ispay == "0"){$displayarr['dele'] 		= array('showname'=>'删除','width'=>'45');}


	$this->V->mark(array('title'=>'退款列表'));

	/*数据流操作按钮*/
	$this->C->service('global')->disconnect_modbutton(array('0'=>&$mod_disabled_0,'1'=>&$mod_disabled_1),$ispay);

	$bannerstr .= '<button onclick=modrecorde("0") '.$mod_disabled_0.'>退款确认</button>';
    $bannerstr .= '<button onclick=window.location="index.php?action=process_backorder&detail=export&ispay='.$ispay.'&sku='.$sku.'&fid='.$fid.'&cuser='.$cuser.'&statu='.$statu.'&comment3='.$comment3.'&comment2='.$comment2.'&sold_id='.$sold_id.'&order_id='.$order_id.'">导出数据</button>';
    //$bannerstrarr[] = array('url'=>'index.php?action=process_backorder&detail=export','value'=>'导出数据');

	$jslink 	= "<script src='./staticment/js/process_shipment.js?v=1'></script>\n";
	$temp 		= 'pub_list';
 }
elseif ($detail == 'export') {
    if(empty($sqlstr) && !isset($ispay)){$ispay = "0";}

	$sqlstr.=' and property="退款单" and ispay="'.$ispay.'"';

    $datalist = $this->S->dao('process')->get_backorlist($sqlstr);


	for($i=0;$i<count($datalist);$i++){
		/*退单原因显示还原处理*/
		switch ($datalist[$i]['comment3']){
			case 'unsatisfactory_with_item'					:$datalist[$i]['comment3'] = 'Unsatisfactory with item';break;
			case 'wrong_purchase'			 				:$datalist[$i]['comment3'] = 'Wrong purchase';break;
			case 'sent_wrong_item'					 		:$datalist[$i]['comment3'] = 'Sent wrong item';break;
			case 'sent_less_item'			 				:$datalist[$i]['comment3'] = 'Sent less item';break;
			case 'warehouse_delay_shipment'					:$datalist[$i]['comment3'] = 'Warehouse Delay shipment';break;
			case 'lost_on_the_way'							:$datalist[$i]['comment3'] = 'Lost on the way';break;
			case 'damage_in_delivery'						:$datalist[$i]['comment3'] = 'Damage in delivery';break;
			case 'carrier_delay_shipment'					:$datalist[$i]['comment3'] = 'Carrier Delay shipment';break;
			case 'defective_item'							:$datalist[$i]['comment3'] = 'Defective item';break;
			case 'provide_wrong_description_on_listing'		:$datalist[$i]['comment3'] = 'Provide wrong description on listing';break;
			case 'mislead_customer_on_listing'				:$datalist[$i]['comment3'] = 'Mislead customer on listing';break;
			case 'provide_wrong_product_info/tech_support'	:$datalist[$i]['comment3'] = 'Provide wrong product info/tech support';break;
		}
	}
    $head_array = array();
	$head_array['order_id']		= '交易ID';
	$head_array['extends']		= '退款帐号';
	$head_array['comment2']		= '联系人';
	$head_array['sku']			= 'sku';
	$head_array['fid']			= '第三方单号';
	$head_array['price']		= '退款金款';
	$head_array['coin_code']	= '币别';
	$head_array['wayname']		= '渠道';
	$head_array['account_name']	= '平台ID';
	$head_array['cuser']		= '制单人';
	if($ispay == "1"){
        $head_array['muser']= '经办人';
    }
	$head_array['cdate']		= '时间';
	$head_array['comment']		= '备注';
	$head_array['comment3']		= '退款原因';

    $filename = 'process_backorder_'.date('Y-m-d-h-i-s',time());
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

 /*确认退款*/
 elseif($detail == 'surebackmy'){

 	if(!$this->C->service('admin_access')->checkResRight('r_t_backmonysure')){exit('对不起，你没有该权限！');}
	$strid	 = stripslashes($strid);
	$time	 = date('Y-m-d H:i:s',time());
 	$sid	 = $this->S->dao('process')->D->update_sql(' where id in('.$strid.')',array('ispay'=>"1",'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'mdate'=>$time,'rdate'=>$time));
 	if($sid){$this->C->ajaxmsg(1,'确认成功');}else{echo '确认失败';}

 }

  /*删除退款单(留作做错时用),权限-同组人*/
 elseif($detail == 'delbackpro'){

 	/*权限判断*/
 	$this->C->service('warehouse')->check_thegroup_right('id',$id,0);


	$sid = $this->S->dao('process')->D->delete_by_field(array('id'=>$id));
	if($sid){$this->C->ajaxmsg(1);}else{exit('删除失败!');}
 }

 /*模板定义*/
if($detail =='list' || $detail =='addextramony'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>

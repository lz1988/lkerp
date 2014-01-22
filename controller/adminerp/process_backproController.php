<?php
/*
 * Created on 2012-2-2
 *
 * 退货明细表
 * by hanson
 */

 if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'ispay-h-e'			=>'是否处理:',
		'sku-s-l'			=>'&nbsp;&nbsp;&nbsp;&nbsp;SKU：',
		'product_name-s-l'	=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;品名：',
		'order_id-s-e'		=>'&nbsp;&nbsp;单号：',
		'fid-s-e'			=>'&nbsp;&nbsp;第三方单号：',
		'cuser-s-l'			=>'<br>制单人：',
		'buyer_id-s-l'		=>'buyerid：',
		'comment3-a-e'		=>'&nbsp;&nbsp;原因：',
		'input-a-e'			=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;是否入库：',
		'sold_id-a-e'		=>'<br>平台ID：',
	);

	/*标签导航选项*/
	$tab_menu_stypemu = array(
		'ispay-0'=>'未处理',
		'ispay-1'=>'已处理',
	);

	$inputarr = array(''=>'=请选择=','0'=>' 未入库 ','1'=>' 已入库 ');

	if(empty($sqlstr) && !isset($ispay)){$ispay = "0";$sqlstr.= ' and ispay="0"';}

	$sold_idarr = $this->C->service('global')->get_sold_way(0,'sold_account','account_name');

	$InitPHP_conf['pageval'] = 20;
	$comment3arr 			 = array(''=>'=退货原因=','unsatisfactory_with_item'=>'Unsatisfactory with item','wrong_purchase'=>'Wrong purchase','sent_wrong_item'=>'Sent wrong item','sent_less_item'=>'Sent less item','warehouse_delay_shipment'=>'Warehouse Delay shipment','lost_on_the_way'=>'Lost on the way','damage_in_delivery'=>'Damage in delivery','carrier_delay_shipment'=>'Carrier Delay shipment','defective_item'=>'Defective item','provide_wrong_description_on_listing'=>'Provide wrong description on listing','mislead_customer_on_listing'=>'Mislead customer on listing','provide_wrong_product_info/tech_support'=>'Provide wrong product info/tech support');
	$sqlstr					.= ' and protype="退货" ';
	$datalist 				 = $this->S->dao('process')->get_backprolist($sqlstr);


	for($i=0;$i<count($datalist);$i++){

		if($datalist[$i]['input'] == "0") {
			$datalist[$i]['isinware'] = '未入库';
			$datalist[$i]['dele']	  = '<a href=javascript:void(0);delitem("index.php?action=process_backpro&detail=delbackpro&id='.$datalist[$i]['id'].'&order_id='.$datalist[$i]['order_id'].'")  title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
		}else{
			$datalist[$i]['isinware'] = '已入库';
			$datalist[$i]['dele'] 	  = '';
		}


		/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd'] 	= '';
			$datalist[$i]['dele'] 	  	= '';
			$datalist[$i]['comment2']	= '';
		}else{
			$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
			if(empty($datalist[$i]['comment2'])) $datalist[$i]['comment2'] = '--';
		}


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
		$datalist[$i]['cdate'] = date('Y-m-d',strtotime($datalist[$i]['cdate']));
		$datalist[$i]['comment'] = empty($datalist[$i]['comment'])?'--':$datalist[$i]['comment'];
	}

	$tablewidth = '1600';
	$displayarr = array();
	$displayarr['order_id']		= array('showname'=>'checkbox','width'=>'35');
	$displayarr['dele'] 		= array('showname'=>'删除','width'=>'50','title'=>'已入库的不再显示删除按钮');
	$displayarr['isinware']		= array('showname'=>'是否入库','width'=>'90');
	$displayarr['order_idd']	= array('showname'=>'单号','width'=>'80');
	$displayarr['fid']			= array('showname'=>'第三方单号','width'=>'100');
	$displayarr['sku']			= array('showname'=>'sku','width'=>'100');
	$displayarr['product_name']	= array('showname'=>'品名','width'=>'170');
	$displayarr['quantity']		= array('showname'=>'数量','width'=>'70');
	$displayarr['wayname']		= array('showname'=>'渠道','width'=>'70');
	$displayarr['account_name']	= array('showname'=>'平台ID','width'=>'80');
	$displayarr['comment2']		= array('showname'=>'物流跟踪号','width'=>'120','clickedit'=>'id','detail'=>'upwuliuno','title'=>'点击编辑');
	$displayarr['fee']			= array('showname'=>'费用','width'=>'70','clickedit'=>'id','detail'=>'upfee','title'=>'点击编辑');
	$displayarr['cuser']		= array('showname'=>'制单人','width'=>'70');
	$displayarr['buyer_id']		= array('showname'=>'Buyer_id','width'=>'80');
	$displayarr['comment3']		= array('showname'=>'退货原因','width'=>'170');
	$displayarr['comment']		= array('showname'=>'备注','width'=>'70','clickedit'=>'id','detail'=>'upcomment','title'=>'点击编辑');
	$displayarr['cdate']		= array('showname'=>'时间','width'=>'100');

	/*数据流操作按钮*/
	$this->C->service('global')->disconnect_modbutton(array('0'=>&$mod_disabled_0,'1'=>&$mod_disabled_1),$ispay);

	$bannerstr 		= '<button onclick=window.location="index.php?action=process_backpro&detail=neworder">添加退货</button>';
	$bannerstr     .= '<button onclick=modrecorde("1") '.$mod_disabled_0.'>确认处理</button>';
	$bannerstrarr[] = array('url'=>'index.php?action=process_backpro&detail=outorder','value'=>'导出数据');

	$jslink 	= "<script src='./staticment/js/process_shipment.js?v=1'></script>\n";
	$this->V->mark(array('title'=>'退货列表'));
	$temp = 'pub_list';
 }

/*退货处理*/
elseif($detail == 'suremod'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_t_backprosure')){exit('对不起，你没有该权限！');}
	$strid	 = stripslashes($strid);

 	$sid	 = $this->S->dao('process')->D->update(' and order_id in('.$strid.')',array('ispay'=>"1"));
 	if($sid){$this->C->ajaxmsg(1,'确认成功');}else{echo '确认失败';}
}

/*导出退货报表*/
elseif($detail == 'outorder'){

	$sqlstr		.=' and protype="退货" ';
	$datalist	 = $this->S->dao('process')->get_backprolist($sqlstr);

	$filename	 = 'backpro_'.date('Y-m-d').'_'.time();
	$head_array  = array('account_name'=>'平台ID','sku'=>'SKU','product_name'=>'产品名称','order_id'=>'退货单号','fid'=>'第三方单号','quantity'=>'数量','cdate'=>'日期');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

/*保存添加退货*/
elseif($detail == 'modneworder'){

	if(empty($pid) || empty($product_name) || empty($receiver_id) ){
		$this->C->sendmsg('产品ID或产品名称获取失败。<br>提交失败!');
	}

	/*取得渠道*/
	$backdata = $this->S->dao('sold_relation_conf')->D->get_one_by_field(array('account_id'=>$sold_account),'way_id');

	$process  = $this->S->dao('process');
	$max_r 	  = $this->C->service('warehouse')->get_maxorder_manay('进仓单','r',$process);

	/*插入退单,单号R开头,statu=3,price=0*/
	$sid = $process->D->insert(array('receiver_id'=>$receiver_id,'sku'=>$sku,'fid'=>$fid,'pid'=>$pid,'product_name'=>$product_name,'sold_way'=>$backdata['way_id'],'buyer_id'=>$buyer_id,'sold_id'=>$sold_account,'quantity'=>$quantity,'cdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'order_id'=>$max_r,'statu'=>'3','active'=>'1','property'=>'进仓单','protype'=>'退货','comment'=>$comment,'comment2'=>$comment2,'comment3'=>$comment3));

	if(empty($re_x) && $sid){
		$this->C->success('添加成功！','index.php?action=process_backpro&detail=list');
	}
}

/*添加退货*/
elseif($detail == 'neworder'){

	/*退款原因数组*/
	$reason_datastr = $this->C->service('warehouse')->get_backreaseon_html('comment3');
	$comment_datastr = '<textarea rows="3" cols="25" name=comment></textarea>';
	$receiver_id_datastr = $this->C->service('warehouse')->get_whouse('receiver_id','name','id');

	/*平台ID*/
	$sold_idstr	= $this->C->service('global')->get_sold_way(1,'sold_account','account_name');

	/*表单配置*/
	$conform  = array('method'=>'post','action'=>'index.php?action=process_backpro&detail=modneworder');
	$colwidth = array('1'=>'150','2'=>'250','3'=>'260');

	$disinputarr['fid'] 			= array('showname'=>'第三方单号');
	$disinputarr['sku'] 			= array('showname'=>'SKU','inextra'=>"onblur=get_pid()",'showtips'=>'<span class=tips>&nbsp*</span>');
	$disinputarr['product_name'] 	= array('showname'=>'产品名称','showtips'=>'<span class=tips>&nbsp*系统自动获取</span>');
	$disinputarr['pid'] 			= array('showname'=>'产品ID','datatype'=>'h');
	$disinputarr['quantity'] 		= array('showname'=>'数量','showtips'=>'<span class=tips>&nbsp*</span>');
	$disinputarr['comment2'] 		= array('showname'=>'物流跟踪号');
	$disinputarr['buyer_id'] 		= array('showname'=>'Buyer_ID');
	$disinputarr['sold_id'] 		= array('showname'=>'平台ID','datatype'=>'se','datastr'=>$sold_idstr);
	$disinputarr['receiver_id'] 	= array('showname'=>'接收仓库','datatype'=>'se','datastr'=>$receiver_id_datastr,'showtips'=>'<span class=tips>&nbsp*</span>');
	$disinputarr['comment3']		= array('showname'=>'退货原因','datatype'=>'se','datastr'=>$reason_datastr,'showtips'=>'<span class=tips>&nbsp*</span>');
	$disinputarr['comment']			= array('showname'=>'备注','datatype'=>'se','datastr'=>$comment_datastr);

	$jslink = "<script src='./staticment/js/process_shipment.js'></script>\n";
	$jslink.= "<script src='./staticment/js/jquery.js'></script>\n";
	$this->V->mark(array('title'=>'添加退货-退货列表(list)'));
	$temp = 'pub_edit';
}

/*AJAX更新物流跟踪号，费用，删除。权限-同组人*/
 elseif($detail == 'upwuliuno' || $detail == 'upfee' || $detail == 'delbackpro' || $detail == 'upcomment'){

	$process = $this->S->dao('process');
	$cuser	 = $process->D->get_one(array('id'=>$id),'cuser');

	if($detail == 'upwuliuno'){
		if(!$this->C->service('admin_access')->checkResRight('r_w_editbackpro','mod',$cuser)){$this->C->ajaxmsg(0);}
		if($process->D->update_by_field(array('id'=>$id),array('comment2'=>$comment2))){echo '1';}else{echo '更新失败！';}
	}elseif($detail == 'upfee'){
		if(!$this->C->service('admin_access')->checkResRight('r_w_editbackpro','mod',$cuser)){$this->C->ajaxmsg(0);}
		if($process->D->update_by_field(array('id'=>$id),array('fee'=>$fee))){echo '1';}else{echo '更新失败！';}
	}elseif($detail == 'delbackpro'){
		if(!$this->C->service('admin_access')->checkResRight('r_w_delbackpro','mod',$cuser)){$this->C->ajaxmsg(0);}
		$sid = $process->D->delete_by_field(array('order_id'=>$order_id));
		if($sid){$this->C->ajaxmsg(1);}else{exit('删除失败!');}
	}elseif($detail == 'upcomment'){
		if(!$this->C->service('admin_access')->checkResRight('r_w_editbackpro')){$this->C->ajaxmsg(0);}
		if($process->D->update_by_field(array('id'=>$id),array('comment'=>$comment))){echo '1';}else{echo '更新失败！';}
	}
 }


/*模板定义*/
if($detail =='list' || $detail == 'neworder'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>

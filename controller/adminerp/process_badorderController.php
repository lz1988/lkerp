<?php
/**
 * Created on 2012-1-17
 * @title 异常订单管理
 *
 * @author by hanson
 */

 /*异常订单列表*/
 if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'sku-s-l'		=>'&nbsp; &nbsp; &nbsp; &nbsp; SKU：',
		'order_id-s-l'	=>'&nbsp; &nbsp; 订单号：',
		'fid-s-l'		=>'第三方单号：',
		'cuser-s-l'		=>'制单人：',
		'buyer_id-s-l'	=>'Buyer_ID：',
		'deal_id-s-l'	=>'平台单号：',
		'comment3-a-e'	=>'取&nbsp;消&nbsp;原&nbsp;因&nbsp;：',
	);
	$InitPHP_conf['pageval'] 	= 15;
	$comment3arr 				= $this->C->service('warehouse')->get_canceltype(0);
	$sqlstr 				   .= ' and (property="出仓单" or property="转仓单") and isover="Y" ';
	$sqlstr						= strtr($sqlstr,array('cuser'=>'p.cuser'));

	$datalist 					= $this->S->dao('process')->get_badorder($sqlstr);

	/*数据解压等处理*/
 	for($i=0;$i<count($datalist);$i++){

		/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd'] 	= '';
			$datalist[$i]['back']		= '';
			$datalist[$i]['edit_order'] = '';
			$datalist[$i]['remoney'] 	= '';
		}else{
			$datalist[$i]['back'] = '<a href=javascript:void(0);delitem("index.php?action=process_badorder&detail=rollback&order_id='.$datalist[$i]['order_id'].'") ><font color=#828482>还原</font></a>';
			$datalist[$i]['edit_order'] = '<a href=index.php?action=process_badorder&detail=editorder&order_id='.$datalist[$i]['order_id'].' ><img src="./staticment/images/editbody.gif" border=0></a>';
			$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
			$datalist[$i]['remoney']  = '<a href="index.php?action=process_shipment&detail=remoneymode&detail_id='.$datalist[$i]['id'].'&order_id='.$datalist[$i]['order_id'].'&sku='.$datalist[$i]['sku'].'&is_jump_fbad=1" title=点击建立退款单><font color=#828482>退款</font></a>';
		}

		$datalist[$i]['comment3'] = $this->C->service('warehouse')->get_canceltype(1,$datalist[$i]['comment3']);
		$datalist[$i]['fid']	  = '<a href=index.php?action=process_badorder&detail=editsku&id='.$datalist[$i]['id'].'&order_id='.$datalist[$i]['order_id'].' title=点击更改SKU、发货仓库、数量>'.$datalist[$i]['fid'].'</a>';
	}


	$displayarr = array();
	$tablewidth = '1300';
	$displayarr['order_id'] 		= array('showname'=>'checkbox','width'=>'40','title'=>'反选');
	$displayarr['back'] 			= array('showname'=>'还原','width'=>'50','title'=>'还原操作只针对库存不足的订单!');
	$displayarr['remoney']   		= array('showname'=>'退款','width'=>'50');
	$displayarr['edit_order']		= array('showname'=>'编辑','width'=>'50');
	//$displayarr['delete']			= array('showname'=>'删除','width'=>'50','ajax'=>1,'url'=>'index.php?action=process_badorder&detail=deleteorder&order_id={order_id}');
	$displayarr['order_idd'] 		= array('showname'=>'订单号','width'=>'60');
	$displayarr['fid'] 				= array('showname'=>'第三方单号','width'=>'100');
	$displayarr['sku'] 				= array('showname'=>'SKU','width'=>'80');
	$displayarr['product_name'] 	= array('showname'=>'产品名称','width'=>'100');
	$displayarr['name']			 	= array('showname'=>'发货仓库','width'=>'80');
	$displayarr['quantity'] 		= array('showname'=>'数量','width'=>'50');
	$displayarr['wayname']			= array('showname'=>'渠道','width'=>'60');
	$displayarr['comment3'] 		= array('showname'=>'取消原因','width'=>'80');
	$displayarr['buyer_id']			= array('showname'=>'Buyer_ID','width'=>'60');
	$displayarr['cuser'] 			= array('showname'=>'制单人','width'=>'60');
	$displayarr['cdate'] 			= array('showname'=>'日期','width'=>'80');
	$displayarr['deal_id']	 		= array('showname'=>'平台单号','width'=>'100');

	$this->V->mark(array('title'=>'异常订单列表'));
	$temp = 'pub_list';
 }


/*保存修改SKU*/
elseif($detail == 'mod_editsku'){

	if(empty($sku) || empty($id)) exit('no SKU');

	$backdata = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name');
	if(!$backdata) exit('系统不存的SKU！');

	$process = $this->S->dao('process');

	$sid = $process->D->update_by_field(array('id'=>$id),array('sku'=>$sku,'quantity'=>$quantity,'pid'=>$backdata['pid'],'product_name'=>$backdata['product_name']));
	$cid = $process->D->update_by_field(array('order_id'=>$order_id),array('provider_id'=>$provider_id));//如果修改了发货仓库，修改的是整个订单，而不是一条记录

	if($sid && $cid) {$this->C->success('修改成功','index.php?action=process_badorder&detail=list&order_id='.$order_id);}else{exit('修改失败');}
}

/*修改产SKU*/
elseif($detail == 'editsku'){

	if(empty($id)) exit('没有ID');

	$warehouse_chk	= $this->C->service('warehouse');
	$back_sku		= $this->S->dao('process')->D->get_one_by_field(array('id'=>$id),'sku,fid,provider_id,quantity,cuser');

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_o_badorder_edit','mod',$back_sku['cuser'])){$this->C->sendmsg();}

	$provstr  = $warehouse_chk->get_whouse('provider_id','name','id','id',$back_sku['provider_id']);

	/*表单配置*/
	$conform  = array('method'=>'post','action'=>'index.php?action=process_badorder&detail=mod_editsku');
	$colwidth = array('1'=>'150','2'=>'250','3'=>'260');

	$disinputarr = array();
	$disinputarr['id'] 		= array('showname'=>'id','datatype'=>'h','value'=>$id);
	$disinputarr['order_id']= array('showname'=>'id','datatype'=>'h','value'=>$order_id);
	$disinputarr['fid']		= array('showname'=>'第三方单号','value'=>$back_sku['fid'],'inextra'=>'disabled');
	$disinputarr['sku'] 	= array('showname'=>'SKU','value'=>$back_sku['sku']);
	$disinputarr['quantity']= array('showname'=>'数量','value'=>$back_sku['quantity']);
	$disinputarr['provi_id']= array('showname'=>'发货仓库','value'=>$back_sku['provider_id'],'datatype'=>'se','datastr'=>$provstr);

	$this->V->mark(array('title'=>'更改SKU-异常订单列表(list)'));
	$temp = 'pub_edit';
}


 /*还原异常订单--需要权限'异常订单删除还原操作'*/
 elseif($detail == 'rollback'){

	/*成功统计量0*/
	$skucount 		= '';
	$process 		= $this->S->dao('process');
	$backdatalist	= $process->D->get_allstr(' and order_id="'.$order_id.'" and (comment3="1" or comment3="9") ','','','sku,provider_id,quantity,cuser');/*取得该异常订单库存不足的所有SKU与发货仓库*/

 	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_o_badorder_edit','mod',$backdatalist[0]['cuser'])){$this->C->ajaxmsg(0);}

	if(empty($backdatalist)) $this->C->ajaxmsg(0,'操作失败，只能还原库存不足或未到款的订单!');

	for($i=0;$i<count($backdatalist);$i++){
		/*库存判断*/
		$back_enough = $process->get_allw_allsku(' and temp.sku="'.$backdatalist[$i]['sku'].'" and temp.wid='.$backdatalist[$i]['provider_id']);
		if($back_enough['0']['sums'] < $backdatalist[$i]['quantity']){
			$skucount.= $backdatalist[$i]['sku'].',';
		}
	}

	/*如果有一条记录库存不足，则该订单不能还原*/
	if($skucount){
		$this->C->ajaxmsg(0,'还原失败，存在可发库存不足的SKU：'.$skucount.' 请查询后再操作！');
	}else{
		$sid = $process->D->update_by_field(array('order_id'=>$order_id),array('isover'=>'N','comment3'=>''));
		if($sid){$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0,'还原失败');}
	}
 }

/**删除
 elseif($detail == 'deleteorder'){
		$this->C->service('warehouse')->check_thegroup_right('order_id',$order_id,1);
		$sid = $this->S->dao('process')->D->delete_by_field(array('order_id'=>$order_id));
		if($sid){$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0,'删除失败');}
 }
**/

/*编辑原因*/
elseif($detail == 'editorder'){

	/*表单配置*/
	$conform	= array('method'=>'post','action'=>'index.php?action=process_badorder&detail=modeditorder');

	$backdata	= $this->S->dao('process')->D->get_one(array('order_id'=>$order_id),'comment3,cuser');

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_o_badorder_edit','mod',$backdata['cuser'])){$this->C->sendmsg();}

	/*取消原因数组*/
	$reason_datastr = $this->C->service('warehouse')->get_canceltype(2,'comment3',$backdata['comment3']);

	$disinputarr = array();
	$disinputarr['order_id'] = array('showname'=>'order_id','value'=>$order_id);
	$disinputarr['comment3'] = array('showname'=>'取消原因','datatype'=>'se','datastr'=>$reason_datastr);

	$this->V->mark(array('title'=>'修改取消原因-异常订单列表(list)'));
	$temp = 'pub_edit';
}

/*执行保存编辑*/
elseif($detail == 'modeditorder'){

	$sid = $this->S->dao('process')->D->update_by_field(array('order_id'=>$order_id),array('comment3'=>$comment3));
	$overjump = 'index.php?action=process_badorder&detail=list';
	if($sid){$this->C->success('修改成功',$overjump);}else{$this->C->success('修改失败',$overjump);}
}

 /*模板定义*/
 if($detail =='list' || $detail == 'editorder' || $detail == 'editsku'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>


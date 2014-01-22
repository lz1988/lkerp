<?php
/*
 * Created on 2012-3-2
 * 查出事务表中，影响库存的，但没有PID或P_name的数据
 *
 * by hanson
 */

 /*显示列表*/
 if($detail == 'list'){

 	$stypemu = array(
 		'sku-s-l'=>'SKU：',
 	);

	$sqlstr.= ' and (property="出仓单" or property="进仓单" or property="转仓单") and (pid="" or product_name="") and isover="N"';
	$datalist = $this->S->dao('process')->D->get_list($sqlstr,'','','isover,id,sku,order_id,cdate,property,protype,cuser,cdate');

	foreach($datalist as &$val){
		$val['uppid']	= '<a href=javascript:void(0);delitem("index.php?action=check_nonpid&detail=uppid&id='.$val['id'].'&sku='.$val['sku'].'") ><font color=#828482>更新PID</font></a>';
	}

	$displayarr = array();

	$displayarr['uppid']	= array('showname'=>'更新PID','width'=>'80');
	$displayarr['sku']		= array('showname'=>'SKU');
	$displayarr['order_id']	= array('showname'=>'订单号');
	$displayarr['property']	= array('showname'=>'第一属性');
	$displayarr['protype']	= array('showname'=>'第二属性');
	$displayarr['cuser']	= array('showname'=>'制单人');
	$displayarr['isover']	= array('showname'=>'关闭标志');
	$displayarr['cdate']	= array('showname'=>'制单日期');


	$this->V->mark(array('title'=>'异常数据检测'));
	$temp = 'pub_list';
 }

/*更新PID*/
elseif($detail == 'uppid'){

	$backdata = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name');
	if(empty($backdata['pid']) || empty($backdata['product_name'])) $this->C->ajaxmsg(0,'更新失败，获取原PID与名称失败');

	$sid = $this->S->dao('process')->D->update_by_field(array('id'=>$id),array('pid'=>$backdata['pid'],'product_name'=>$backdata['product_name']));
	if($sid){$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0,'更新失败');}
}

 /*模板定义*/
if($detail =='list'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>

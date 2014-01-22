<?php
if ($detail == 'list') {
	$shipping = $this->S->dao('shipping');
	$stypemu = array(
 		's_name-s-l'=>'发货方式：'
 	);


 	$bannerstr = '<button class="six" onclick="window.location=\'index.php?action=shipping_map&detail=list\'">发货映射列表</button>';
 	$bannerstr .= '<button class="six" onclick="window.location=\'index.php?action=shipping&detail=add\'">新增发货方式</button>';
 	$bannerstr .= '<button id="wall_delete_shipping">批量删除</button>';

 	$datalist = $shipping->get_all_list($sqlstr);


 	$tablewidth = '700';

 	$displayarr['id'] 			= array('showname'=>'checkbox','title'=>'全选','width'=>'50');
	$displayarr['s_name'] 		= array('showname'=>'发货方式','width'=>'150');
	$displayarr['s_user'] 		= array('showname'=>'最后修改', 'width'=>'100');
	$displayarr['s_date'] 		= array('showname'=>'修改时间', 'width'=>'100');
	$displayarr['both'] 		= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=shipping&detail=delete&id={id}','url_e'=>'index.php?action=shipping&detail=edit&id={id}');

	$this->V->view['title'] = '发货方式列表';
	$temp = 'pub_list';
	/*JS包含*/
	$jslink = "<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />\n";
	$jslink .= "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/jquery.autocomplete.js'></script>\n";
	$jslink .= "<script src='./staticment/js/shipping.js'></script>\n";
}
elseif ($detail == 'add' || $detail == 'edit') {


	if ($detail == 'add') {
		if(!$this->C->service('admin_access')->checkResRight('r_r_addmapping')){$this->C->sendmsg();}//权限判断
		$this->V->view['title'] = '添加发货方式-发货方式列表(list)';
		$jump = 'index.php?action=shipping&detail=addmod';
	}
	elseif ($detail == 'edit') {
		if(!$this->C->service('admin_access')->checkResRight('r_r_editmapping')){$this->C->sendmsg();}//权限判断
		if(empty($id))exit('没有ID!');
		$shipping  = $this->S->dao('shipping');
		$data = $shipping->get_one_by_id($id);
		$this->V->view['title'] = '编辑发货方式-发货方式列表(list)';
		$jump = 'index.php?action=shipping&detail=editmod';
	}

	$conform = array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'80');

	$disinputarr = array();
	$disinputarr['id'] 	 		= array('showname'=>'ID','value'=>$id,'datatype'=>'h');
	$disinputarr['s_name'] 		= array('showname'=>'发货方式','value'=>$data['s_name']);

	$temp = 'pub_edit';
}
elseif ($detail == 'addmod') {
	$sid = $this->S->dao('shipping')->D->insert(array('s_name'=>$s_name,'s_user'=>$_SESSION['eng_name'],'s_date'=>date('Y-m-d h:i:s', time())));
	if($sid) $this->C->success('添加成功','index.php?action=shipping&detail=list');
}
elseif ($detail == 'editmod') {
	$sid = $this->S->dao('shipping')->D->update_by_field(array('id'=>$id),array('s_name'=>$s_name,'s_user'=>$_SESSION['eng_name'],'s_date'=>date('Y-m-d h:i:s', time())));
	if($sid) $this->C->success('修改成功','index.php?action=shipping&detail=list');
}
elseif ($detail == 'delete') {
	if(!$this->C->service('admin_access')->checkResRight('r_r_delmapping')){$this->C->ajaxmsg(0);}//权限判断
	if($id){if($this->S->dao('shipping')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}
elseif ($detail == 'deletefull') {
	if(!$this->C->service('admin_access')->checkResRight('r_r_delmapping')){$this->C->ajaxmsg(0);}//删除权限判断
	$sid = $this->S->dao('shipping')->D->delete_sql('where id in('.$strid.')');
	if($sid) $this->C->ajaxmsg(1);
}

/*返回发货方式，用于销售下单点击修改发货方式--by hanson*/
elseif($detail == 'get_shipping'){
	$wdata = $this->S->dao('shipping')->D->get_allstr('','','','id,s_name');
	$wback = '';
	$wback = '<select class="ajax_select_shipping">';
	$wback.= '<option value=>=请选择=</option>';
	foreach ($wdata as $val){
		if($shipping){$selected = ($val['s_name']==$shipping)?'selected':'';}
		$wback.= '<option value='.$val['s_name'].' '.$selected.'>'.$val['s_name'].'</option>';
	}
	$wback.= '</select>';
	echo $wback;
	//$this->S->dao('')
}

if ($detail == 'list' || $detail == 'add' || $detail == 'edit') {
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>
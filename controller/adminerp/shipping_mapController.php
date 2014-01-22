<?php
if ($detail == 'list') {
	$shipping_map = $this->S->dao('shipping_map');
	$stypemu = array(
 		's_name-s-l'=>'发货方式：',
		'sm_type-s-l'=>'发货方式类型：',
 		'sm_name-s-l'=>'发货方式映射码：'
 	);

 	$bannerstr = '<button class="six" onclick="window.location=\'index.php?action=shipping&detail=list\'">发货方式列表</button>';
 	$bannerstr .= '<button onclick="window.location=\'index.php?action=shipping_map&detail=add\'">新增映射</button>';
 	$bannerstr .= '<button id="wall_delete_shipping_map">批量删除</button>';

 	$InitPHP_conf['pageval'] = 10;
 	$datalist = $shipping_map->get_all_list($sqlstr);


 	$tablewidth = '900';

 	$displayarr['id'] 			= array('showname'=>'checkbox','title'=>'全选','width'=>'50');
	$displayarr['s_name'] 		= array('showname'=>'发货方式','width'=>'150');
	$displayarr['sm_type'] 		= array('showname'=>'类型', 'width'=>'100');
	$displayarr['sm_name'] 		= array('showname'=>'发货方式映射码', 'width'=>'300');
	$displayarr['sm_user'] 		= array('showname'=>'最后修改', 'width'=>'100');
	$displayarr['sm_date'] 		= array('showname'=>'修改时间', 'width'=>'100');
	$displayarr['both'] 		= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=shipping_map&detail=delete&id={id}','url_e'=>'index.php?action=shipping_map&detail=edit&id={id}');

	$this->V->view['title'] = '发货映射列表';
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
		$this->V->view['title'] = '添加发货映射-发货映射列表(list)';
		$jump = 'index.php?action=shipping_map&detail=addmod';
	}
	elseif ($detail == 'edit') {
		if(!$this->C->service('admin_access')->checkResRight('r_r_editmapping')){$this->C->sendmsg();}//权限判断
		if(empty($id))exit('没有ID!');
		$shipping_map  = $this->S->dao('shipping_map');
		$data = $shipping_map->get_one_by_id($id);
		$this->V->view['title'] = '编辑发货映射-发货映射列表(list)';
		$jump = 'index.php?action=shipping_map&detail=editmod';
	}

	$shipping = $this->S->dao('shipping');
	$back_list = $shipping->get_all_list('');

	$shippingsel = '<select name="sid">';
	$shippingsel .= '<option value="">=选择发货方式=</option>';
	foreach($back_list as $val){
		$selected_b = $val['id'] == $data['sid']?'selected':'';
		$shippingsel .= '<option value='.$val['id'].' '.$selected_b.'>'.$val['s_name'].'</option>';
	}
	$shippingsel .= '</select>';

	$conform = array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'80');

	$disinputarr = array();
	$disinputarr['id'] = array('showname'=>'修改的ID','datatype'=>'h','value'=>$id);
	$disinputarr['sid'] = array('showname'=>'发货方式','datatype'=>'se','datastr'=>$shippingsel);
	$disinputarr['sm_type'] = array('showname'=>'映射类型','value'=>$data['sm_type']);
	$disinputarr['sm_name'] = array('showname'=>'映射名称','value'=>$data['sm_name']);
	$temp = 'pub_edit';

	$jslink = "<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />\n";
	$jslink .= "<script src='./staticment/js/jquery.autocomplete.js'></script>\n";
	$jslink .= "<script src='./staticment/js/shipping.js'></script>\n";
}
elseif ($detail == 'addmod') {
	$sid = $this->S->dao('shipping_map')->D->insert(array('sid'=>$sid,'sm_name'=>$sm_name,'sm_type'=>$sm_type,'sm_user'=>$_SESSION['eng_name'],'sm_date'=>date('Y-m-d h:i:s', time())));
	if($sid) $this->C->success('添加成功','index.php?action=shipping_map&detail=list');
}
elseif ($detail == 'editmod') {
	$sid = $this->S->dao('shipping_map')->D->update_by_field(array('id'=>$id),array('sid'=>$sid,'sm_name'=>$sm_name,'sm_type'=>$sm_type,'sm_user'=>$_SESSION['eng_name'],'sm_date'=>date('Y-m-d h:i:s', time())));
	if($sid) $this->C->success('修改成功','index.php?action=shipping_map&detail=list');
}
elseif ($detail == 'delete') {
	if(!$this->C->service('admin_access')->checkResRight('r_r_delmapping')){$this->C->ajaxmsg(0);}//删除权限判断
	if($id){if($this->S->dao('shipping_map')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}
elseif ($detail == 'deletefull') {
	if(!$this->C->service('admin_access')->checkResRight('r_r_delmapping')){$this->C->ajaxmsg(0);}//删除权限判断
	$sid = $this->S->dao('shipping_map')->D->delete_sql('where id in('.$strid.')');
	if($sid) $this->C->ajaxmsg(1);
}
elseif ($detail == 'getshipping') {
	$q = strtolower($_GET["q"]);
	if (!$q) return;
	$datalist = $this->S->dao('shipping_map')->get_type_list($q);
	foreach ($datalist as $val){
		echo $val['sm_type']."\n";
	}
}
if ($detail == 'list' || $detail == 'add' || $detail == 'edit') {
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>
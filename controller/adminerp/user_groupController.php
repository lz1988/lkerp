<?php
/*
 * Created on 2012-2-13
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * Title 部门管理
 */

/*部门列表*/
 if($detail == 'list'){

 	$stypemu = array(
 		'groupname-s-l'=>'部门名称：'
 	);

	$datalist = $this->S->dao('admin_group')->getgroup_list($sqlstr);
	$bannerstr = '<button onclick="window.location=\'index.php?action=user_group&detail=add\'">添加部门</button>';
	$bannerstr.= '<button onclick="window.location=\'index.php?action=user_group&detail=listcat_group\'">部门属性</button>';
	$bannerstr.= '<button onclick="window.location=\'index.php?action=user_group&detail=roles\'">角色查看</button>';

	$displayarr = array();
	$tablewidth = '90%';

	$displayarr['groupname']	= array('showname'=>'部门名称');
	$displayarr['desc']			= array('showname'=>'描述');
	$displayarr['catg_name']	= array('showname'=>'部门属性');
	$displayarr['both']			= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=user_group&detail=delete&id={id}','url_e'=>'index.php?action=user_group&detail=edit&id={id}');

	$this->V->view['title'] = '部门列表';
	$temp = 'pub_list';

}

/*角色*/
elseif($detail == 'roles'){

	$roleArr	= $this->C->service('admin_access')->checkResRight();
	$kdyid		= array_keys($roleArr['roles']);
	$datalist	= array();

	foreach($roleArr['roles'] as $key=>$val){
		$datalist[]	= array('rolename'=>$val,'isadmin'=>in_array($key, $roleArr['mans'])?'是':'否');
	}

	$displayarr = array();
	$tablewidth = '200';

	$displayarr['rolename']	= array('showname'=>'角色名称');
	$displayarr['isadmin']	= array('showname'=>'管理层');
	$this->V->view['title'] = '角色列表-部门列表(list)';
	$temp = 'pub_list';
}

/*删除部门组*/
elseif($detail == 'delgroupcat'){
    if(!$this->C->service('admin_access')->checkResRight('usergroup_del')){$this->C->ajaxmsg(0);}
	$sid = $this->S->dao('cat_group')->D->delete_by_field(array('id'=>$id));
	if($sid){$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg(0,'删除失败');}
}

/*保存新增分组*/
elseif($detail == 'modaddcat_group'){
    if(!$this->C->service('admin_access')->checkResRight('usergroup_add')){$this->C->sendmsg();}
	$sid = $this->S->dao('cat_group')->D->insert(array('catg_name'=>$catg_name));
	if($sid) $this->C->success('添加成功','index.php?action=user_group&detail=listcat_group');

}


/*保存编辑分组*/
elseif($detail =='modeditgroupcat'){
    if(!$this->C->service('admin_access')->checkResRight('usergroup_edit')){$this->C->sendmsg();}
	$sid = $this->S->dao('cat_group')->D->update_by_field(array('id'=>$id),array('catg_name'=>$catg_name));
	if($sid) $this->C->success('修改成功','index.php?action=user_group&detail=listcat_group');

}

/*添加分组*/
elseif($detail =='addcat_group' || $detail =='editgroupcat'){
    
	$conform = array('method'=>'post','action'=>'index.php?action=user_group&detail=mod'.$detail,'width'=>'490');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'80');
    if($detail == 'addcat_group'){
        if(!$this->C->service('admin_access')->checkResRight('usergroup_add')){$this->C->sendmsg();}
    }
	if($detail == 'editgroupcat'){
	    if(!$this->C->service('admin_access')->checkResRight('usergroup_edit')){$this->C->sendmsg();}   
		$backdata = $this->S->dao('cat_group')->D->get_one_by_field(array('id'=>$id),'catg_name');
	}

	$disinputarr = array();
	$disinputarr['id'] 	 		= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['catg_name']	= array('showname'=>'属性名称','value'=>$backdata['catg_name']);

	$temp = 'pub_edit';
	$this->V->view['title'] = '添加或编辑-部门属性列表(listcat_group)-部门列表(list)';

}

/*属性管理*/
elseif($detail =='listcat_group'){

	$datalist = $this->S->dao('cat_group')->D->get_list();
	$displayarr = array();
	$tablewidth = '300';
	$displayarr['catg_name'] 	= array('showname'=>'属性名称','width'=>'');
	$displayarr['both']			= array('showname'=>'操作','width'=>'60','ajax'=>'1','url_d'=>'index.php?action=user_group&detail=delgroupcat&id={id}','url_e'=>'index.php?action=user_group&detail=editgroupcat&id={id}');

	$bannerstr = '<button onclick="window.location=\'index.php?action=user_group&detail=addcat_group\'">添加属性</button>';
	$this->V->view['title'] = '部门属性列表-部门列表(list)';
	$temp = 'pub_list';
}

/*添加权限组操作*/
elseif($detail == 'addmod'){
    if(!$this->C->service('admin_access')->checkResRight('usergroup_add')){$this->C->ajaxmsg(0);}
	$sid = $this->S->dao('admin_group')->D->insert(array('groupname'=>$groupname, 'desc'=>$description,'catg_id'=>$catg_id));
	if($sid) $this->C->success('添加成功','index.php?action=user_group&detail=list');
}


/*删除*/
elseif($detail == 'delete'){
    if(!$this->C->service('admin_access')->checkResRight('usergroup_del')){$this->C->ajaxmsg(0);}
	if($id){if($this->S->dao('admin_group')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

/*编辑操作*/
elseif($detail == 'editmod'){
    if(!$this->C->service('admin_access')->checkResRight('usergroup_edit')){$this->C->ajaxmsg(0);}
	$sid = $this->S->dao('admin_group')->D->update_by_field(array('id'=>$id),array('groupname'=>$groupname, 'desc'=>$description,'catg_id'=>$catg_id));
	if($sid) $this->C->success('修改成功','index.php?action=user_group&detail=list');
}

/*添加或者编辑部门页面*/
elseif($detail == 'edit' || $detail == 'add'){


	if($detail == 'edit'){
	    if(!$this->C->service('admin_access')->checkResRight('usergroup_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$admin_group  = $this->S->dao('admin_group');
		$data = $admin_group->D->select('groupname,`desc`,catg_id','id='.$id);
		$this->V->view['title'] = '编辑部门-部门列表(list)';
		$jump = 'index.php?action=user_group&detail=editmod';
	}elseif($detail == 'add'){
	    if(!$this->C->service('admin_access')->checkResRight('usergroup_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '添加部门-部门列表(list)';
		$jump = 'index.php?action=user_group&detail=addmod';
	}

	/*读取所属组*/
	$datastr_catg_id = '<select name=catg_id><option value=>= 选择所属组 =</option>';
	$backdata_catg = $this->S->dao('cat_group')->D->get_allstr();
	foreach($backdata_catg as $val){
		$catg_id_selected = $val['id'] == $data['catg_id']?'selected':'';
		$datastr_catg_id.= '<option value='.$val['id'].' '.$catg_id_selected.'>'.$val['catg_name'].'</option>';
	}
	$datastr_catg_id.= '</select>';

	/*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'600');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'150');

	$disinputarr = array();
	$disinputarr['id'] 	 		= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['groupname']	= array('showname'=>'部门名称','value'=>$data['groupname'],'extra'=>'*','inextra'=>'class="check_notnull"',);
	$disinputarr['description'] = array('showname'=>'描述','value'=>$data['desc']);
	$disinputarr['catg_id']		= array('showname'=>'所属组','datatype'=>'se','datastr'=>$datastr_catg_id);



	$temp = 'pub_edit';
	$jslink .= "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
}

/*模板输出*/
 if($detail == 'list' || $detail == 'add' || $detail == 'edit' || $detail == 'listcat_group' || $detail =='addcat_group' || $detail == 'editgroupcat' || $detail == 'roles'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }

?>

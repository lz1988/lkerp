<?php
/*
 * Created on 2012-10-23
 *
 * by hanson 新权限分配控制器
 */

if($detail == 'edit'){

	$group		= $this->S->dao('admin_group');
	$group_list	= $group->D->get_all('', '', '', 'id,groupname');


	/*标记变量*/
	$this->V->mark(array(
		'title'=>'权限分配',
		'group_list'=>$group_list,
	));

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/access');
	if($this->C->service('admin_access')->checkResRight('m_qxszhi')){$this->V->view['mark'] = 1;}//判断是否要配置权限的权限
	display();
}

/*根据组ID展开用户ID*/
elseif($detail == 'extends'){

	$admin_access			= $this->C->service('admin_access');

	if(empty($isuser)) $backu_list = $admin_access->combine_userlist($objId);//若点击组显示组成员

	$backm_list				= $admin_access->combine_menulist($objId,$menuId,$isuser);//显示菜单

	$backstr				= array('userlist'=>$backu_list,'menulist'=>$backm_list);
	echo json_encode($backstr);

}

/*展开菜单栏下的权限*/
elseif($detail == 'rights'){

	$backdata = $this->C->service('admin_access')->showRights($mid,$guid,$isgu,$tab);
	echo $backdata;

}

/*保存修改*/
if($detail == 'edit' && $mod == 'edit'){

	if($isgroup && $thisid && ($menu_id || $rights_id)){
		$cid = $this->C->service('admin_access')->setRoleRightsNew($thisid, $menu_id, 'menu');//更新菜单权限
		$sid = $this->C->service('admin_access')->setRoleRightsNew($thisid, $rights_id,'group_access',$ismenu);//更新一组功能权限
	}elseif($thisid){
		$sid = $this->C->service('admin_access')->setRoleRightsNew($thisid, $menu_id, 'menu_access');//更新用户菜单权限
		$cid = $this->C->service('admin_access')->setRoleRightsNew($thisid, $rights_id, 'user_access',$ismenu);//更新用户权限
	}

	if($thisid){
		if($cid && $sid){
			echo "<script>parent.callback('1');</script>";
		}else{
			echo "<script>parent.callback('0');</script>";
		}
	}else{
		echo "<script>parent.callback('2');</script>";
	}

}
?>

<?php
/*
 *旧式的权限分配
 *
 **/
	$group = $this->S->dao('admin_group');
	$admin_access = $this->S->dao('admin_access');
	$groups = $group->D->get_all('', '', '', '*');


if($detail == 'edit'){

	if($step >= 2){
		$this->V->view['step'] = $step;
		if($step >= 3){
			if($step >= 4){//submit the modify
				if(!$this->C->service('admin_access')->checkResRight('m_qxszhi')){exit('<script>alert("对不起，你没有该权限");</script>');}

				if(empty($uid)) {
					$sid = $this->C->service('admin_access')->setRoleRights($gid, $right_ids,'access');//更新一组功能权限
					$cid = $this->C->service('admin_access')->setRoleRights($gid, $menu_ids,'menu_id');//更新菜单权限
				}else{
					$sid = $this->C->service('admin_access')->setRoleRights($uid, $right_ids,'uaccess');//只更新某用户功能权限
				}

				if($sid == 1 && $uid || $sid == 1 && $cid == 1){echo "<script>alert('修改成功')</script>";}
			}

			/*读出对象权限数组*/
			$all_rights = $this->C->service('admin_access')->getAllRights();
			$this->V->view['all_rights'] = $all_rights;

			/*读出该组用户*/
			$all_user = $this->S->dao('user')->D->get_allstr(' and groupid='.$gid,'','uid desc','uid,chi_name');

			if($uid){

				/*读出该用户的功能权限*/
				$cur_user_rights = $this->C->service('admin_access')->getRoleRights($uid,'uaccess');
				$this->V->view['cur_user_rights'] = $cur_user_rights;
			}

			/*读出该组功能权限*/
			$cur_role_rights = $this->C->service('admin_access')->getRoleRights($gid,'access');
			$this->V->view['cur_role_rights'] = $cur_role_rights;

			/*读出该组菜单权限ID*/
			$cur_menu_rights = $this->C->service('admin_access')->getRoleRights($gid,'menu_id');
			$this->V->view['cur_menu_rights'] = $cur_menu_rights;

			/*读出所有菜单名称与ID*/
			$all_menu   = $this->S->dao('menu')->D->get_all('','sort_id','asc','id,parent_id,name');
			$this->V->view['all_menu'] = $all_menu;

		}
	}

	if($this->C->service('admin_access')->checkResRight('m_qxszhi')){$this->V->view['mark'] = 1;}

	$this->V->mark(array('title'=>'资源权限','groups'=>$groups,'uid'=>$uid,'gid'=>$gid,'all_user'=>$all_user));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/useraccess');

	display();
}
?>

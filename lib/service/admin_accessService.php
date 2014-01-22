<?php
/*
 * Created on 2011-10-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class admin_accessService extends S{


	public function getAllRights(){
		return $this -> erp_all_rights;
	}

	/*修改用户或群组的权限*/
	public function setRoleRights($gid, $right_ids,$type){

		$right_json = json_encode($right_ids);
		if($type == 'uaccess'){
			return  $this->S->dao('user')->D->update_by_field(array('uid'=>$gid), array($type=>$right_json));
		}else{
			return  $this->S->dao('admin_group')->D->update_by_field(array('id'=>$gid), array($type=>$right_json));
		}
	}

	/*新的修改权限方法*/
	public function setRoleRightsNew($guid, $rights_id, $type, $ismenu = ''){

		if($type == 'menu'){//组菜单权限
			$right_json = json_encode($rights_id);
			return  $this->S->dao('admin_group')->D->update(array('id'=>$guid), array('menu_id'=>$right_json));
		}

		elseif($type == 'group_access'){//组操作权限

			$menuAllarr	 = $this->getMenuAllrights($ismenu);//查该菜单所有权限。
			$backGrupall = $this->getRights('access',$guid);//查出该组现有权限
			$unsetArr	 = empty($rights_id)?$menuAllarr:array_diff($menuAllarr, $rights_id);
			$right_json	 = $this->deloutaddRights($backGrupall, $unsetArr, $rights_id);

			return  $this->S->dao('admin_group')->D->update(array('id'=>$guid), array('access'=>$right_json));
		}

		elseif($type == 'user_access'){//个人操作权限
			$menuAllarr	 = $this->getMenuAllrights($ismenu);//查该菜单所有权限。
			$backGrupall = $this->getRoleRights($guid,'uaccess');//查出该用户个人权限。
			$unsetArr	 = empty($rights_id)?$menuAllarr:array_diff($menuAllarr, $rights_id);

			$right_json	 = $this->deloutaddRights($backGrupall, $unsetArr, $rights_id);

			return  $this->S->dao('user')->D->update(array('uid'=>$guid), array('uaccess'=>$right_json));
		}

		elseif($type == 'menu_access'){//个人菜单权限
			$right_json = json_encode($rights_id);
			return  $this->S->dao('user')->D->update(array('uid'=>$guid), array('maccess'=>$right_json));
		}
	}

	/**
	 * 权限增删 by hanson
	 * $backGrupall 权限数组
	 * $unsetArr 需要剔除的权限
	 * $rights_id 页面传来的权限命令
	 */
	public function deloutaddRights($backGrupall, $unsetArr, $rights_id){

		$allcount = count($backGrupall);//一定要先统计总数

		/*剔除权限*/
		if($unsetArr){
			for($ix = 0; $ix < $allcount; $ix++){
				if(in_array($backGrupall[$ix],$unsetArr)) {unset($backGrupall[$ix]);}
			}
		}

		/*增加权限*/
		foreach($rights_id as &$val){
			if(!in_array($val,$backGrupall)) $backGrupall[] = $val;
		}

		/*对权限数组重新组合，因unset处理过的数组，重新压缩的JSON格式会变*/
		$newBackGrupall = array();
		foreach($backGrupall as $v){
			$newBackGrupall[] = $v;
		}

		return json_encode($newBackGrupall);
	}

	/*获取用户或群组的权限*/
	public function getRoleRights($gid,$type){

		if($type == 'uaccess' || $type == 'maccess'){
			$access = $this->S->dao('user')->D->get_one_by_field(array('uid'=>$gid),$type);
		}else{
			$access = $this->S->dao('admin_group')->D->get_one_by_field(array('id'=>$gid),$type);
		}
		$rightary = json_decode($access[$type],true);
		return $rightary;
	}


	/**
	 *	@title	检查用户是否拥有某个操作的权限 by hanson
	 *  @desc	此方法可分四种调用
	 *			①、空参-返回角色数组。
	 *			②、只有权限代码$right_id，仅仅判断有没该权限。
	 *			③、有权限代码并且有指定是删改权限(mod)还是后续操作权限(follow)。
	 */
	public function checkResRight($right_id = '', $action = '', $eng_name){
		$roles		= array('10001'=>'超级管理员','10002'=>'总监','10003'=>'经理','10004'=>'经理助理','11000'=>'业务员','11001'=>'普通员工');//角色
		$mans		= array('10001','10002','10003');//管理级别
		$rolesArr	= array('roles'=>$roles,'mans'=>$mans);

		/** 情况①处理 **/
		if(empty($right_id) && empty($action)){
			return $rolesArr;
		}
		/** 情况②处理 **/
		elseif(!empty($right_id) && empty($action)){
			if($_SESSION['rid'] == '10001'){
				return 1;//超级管理员拥有所有权限

			}else{
				return $this->checkResRightmod($right_id);
			}
		}

		/** 情况③处理区别mod与follow **/
		elseif(!empty($right_id) && !empty($action)){

			/** 删除或编辑操作 **/
			if($action == 'mod')
			{
				if($_SESSION['rid'] == '10001'){
					$ret = 1;//超级管理员拥有所有权限
				}elseif($this->checkResRightmod($right_id)){

					/** 有权限并且是本人的记录，可以执行 **/
					if($eng_name == $_SESSION['eng_name']){
						$ret = 1;
					}

					/** 有权但非本人记录 **/
					else{
						$backcheck = $this->S->dao('user')->D->get_one(array('eng_name'=>$eng_name));
						if(!$backcheck){//若被删除
							$ret = 1;
						}else{
							if(in_array($_SESSION['rid'],$mans)){//管理级别
								if($_SESSION['groupid'] == $backcheck['groupid']){
									$ret = 1;
								}else{
									$ret = 0;
								}
							}else{
								$ret = 0;
							}
						}
					}
				}else{
					$ret = 0;
				}
			}

			/** 后续处理操作(如订单后续处理做退款等) **/
			elseif($action == 'follow')
			{
				if($_SESSION['rid'] == '10001'){
					$ret = 1;//超级管理员拥有所有权限
				}elseif($eng_name == $_SESSION['eng_name']){
					$ret = 1;//对本人记录操作可执行
				}else{
					$backcheck = $this->S->dao('user')->D->get_one(array('eng_name'=>$eng_name));
					if(!$backcheck){//若原账号被删除，则都有继续权限
						$ret = 1;
					}else{
						if(in_array($_SESSION['rid'],$mans)){//是否管理级别
							if($_SESSION['groupid'] == $backcheck['groupid']){
								$ret = 1;
							}else{
								$ret = 0;
							}
						}else{//订单操作人账号是否被关闭

							if($backcheck['isuse'] == '0'){//帐号被关闭
								if($backcheck['groupid'] == $_SESSION['groupid']){
									$ret = 1;//同部人有权限
								}else{
									$ret = 0;//否则无后续操作权限
								}
							}else{
								$ret = 0;//若帐号没有被关闭，表示此人在职，它人无权限
							}
						}
					}
				}
			}

			return $ret;
		}

		else{
			return false;
		}
	}


	/** 检查用户是否拥有某个操作的权限 **/
	public function checkResRightmod($right_id){
		$ret = 0;

		if(0){/** 调试的时候先不写到session中，待稳定后再开放 **/
			$ret = 1;
		}else{
			$ap = $this->S->dao('admin_group');
			$cur_access = $ap->getUserAccess();

			/** 获取用户的所有权限 **/
			$u_access = array();
			$arr_accs = json_decode($cur_access[0]['uaccess'],true);
			if(is_array($arr_accs)){
				$u_access = $arr_accs;
			}

			$rightary = array_merge(json_decode($cur_access[0]['access'],true),$u_access);
			if(in_array($right_id, $rightary)){
				$ret = 1;
				$basefun = $this->getLibrary('basefuns');
				$basefun->setsession('right_'.$right_id, 1);
			}
		}
		return $ret;
	}

	/*组成员显示*/
	public function combine_userlist($gid){

		/*查出组成员*/
		$user_list 	= $this->S->dao('user')->D->get_allstr(' and groupid='.$gid.' and isuse="1" ','','roleid asc','uid,chi_name,eng_name,groupid,roleid');
		$backu_list	= '<table cellpadding="0" cellspacing="0" class="ur_'.$gid.'">';

		/*角色*/
		$roleArr	= $this->checkResRight();
		foreach($user_list as $val){
			$tipsd = in_array($val['roleid'],$roleArr['mans'])?'<font color=#bdbdbd>('.$roleArr['roles'][$val['roleid']].')</font>':'';//管理层显示备注
			$backu_list.= '<tr><td>&nbsp; <label><input type=radio name=thisid value='.$val['uid'].' onclick="go('.$val['uid'].',1)"  ><span class=u_name>'.$val['chi_name'].'-'.$val['eng_name'].$tipsd.'</span></label></td></tr>';
		}
		$backu_list 		   .='</table>';
		return $backu_list;
	}


	/*根据组的菜单权限，显示菜单*/
	public function combine_menulist($gid, $menuId, $user = ''){

		/*读出所有菜单名称与ID*/
		$all_menu = $this->S->dao('menu')->D->get_all('','sort_id','asc','id,parent_id,name');


		/*点击组罗列该组的菜单权限*/
		if(empty($user)){

			/*查出组的菜单权限*/
			$cur_menu_rights = $this->C->service('admin_access')->getRoleRights($gid,'menu_id');

			$backm_list 	 = '<table cellpadding="0" cellspacing="0" class=mr_list>';
			foreach($all_menu as $val){
				$prenb		 = empty($val['parent_id'])?'':'&nbsp; ';//二级菜单前面加个空格
				$prencheck	 = empty($val['parent_id'])?('tr_'.$val['id']):('td_'.$val['parent_id']);
				$check		 = in_array($val['id'],$cur_menu_rights)?'checked':'';
				$selected    = ($val['id'] == $menuId)?'selectedbg':'';//高亮显示处理
				$backm_list .= '<tr><td>'.$prenb.'<input type=checkbox name=menu_id[] id="menul_'.$val['id'].'"  class="'.$prencheck.'" value="'.$val['id'].'" '.$check.'><span class="m_name '.$selected.'">'.$val['name'].'</span></td></tr>';
			}
			$backm_list			   .= '</table>';
		}

		/*点击成员，显示成员的菜单栏权限*/
		elseif($user){

			/*查出组的菜单权限*/
			$cur_menu_rights = $this->C->service('admin_access')->getRoleRights($this->S->dao('user')->D->get_one(array('uid'=>$gid),'groupid'),'menu_id');

			/*查出个人的菜单权限*/
			$user_menu_rights= $this->C->service('admin_access')->getRoleRights($gid,'maccess');

			$backm_list 	 = '<table cellpadding="0" cellspacing="0" class=mr_list>';
			foreach($all_menu as $val){
				$prenb		 = empty($val['parent_id'])?'':'&nbsp; ';
				$prencheck	 = empty($val['parent_id'])?('tr_'.$val['id']):('td_'.$val['parent_id']);
				$check		 = (in_array($val['id'],$cur_menu_rights) || in_array($val['id'],$user_menu_rights)) ? 'checked':'';
				$disbaled	 = in_array($val['id'],$cur_menu_rights)?'disabled':'';//组已经有，则灰色
				$selected    = ($val['id'] == $menuId)?'selectedbg':'';//高亮显示处理

				$backm_list .= '<tr><td>'.$prenb.'<input type=checkbox name=menu_id[] id="menul_'.$val['id'].'"  class="'.$prencheck.'" value="'.$val['id'].'" '.$check.' '.$disbaled.'><span class="m_name '.$selected.'">'.$val['name'].'</span></td></tr>';
			}
			$backm_list		.= '</table>';
		}

		return $backm_list;
	}

	/**显示的权限**/
	public function showRights($mid, $guid, $isgu, $tab){

		if($isgu == 'group'){
			$rightsArr 		= $this->getRights('access',$guid);//查组权限
			$cur_menu_rights= $this->getRoleRights($guid,'menu_id');//查菜单权限--切换组或用户需要
		}else{
			$gid			= $this->S->dao('user')->D->get_one(array('uid'=>$guid),'groupid');//查出所属组ID
			$rightsArr 		= $this->getRights('uaccess',$guid);//个人权限(已继承组)
			$rightGrup		= $this->getRights('access',$gid);//仅仅查组权限

			$cur_menu_rights= $this->getRoleRights($gid,'menu_id');//查菜单权限--切换组或用户需要
			$u_Menu_ris 	= $this->getRoleRights($guid,'maccess');//查个人额外菜单权限
			if($u_Menu_ris) $cur_menu_rights= array_merge($cur_menu_rights,$u_Menu_ris);
		}

		if(!in_array($mid,$cur_menu_rights) && $tab == 'tab') {return '0';}/*成员切换的时，若无该菜单权限，返回提示*/

		$back			= $this->S->dao('rights')->D->get_allstr(' and b_cat="menu" and b_id='.$mid,'','id asc','*');
		$backstr		= '<table cellpadding="0" cellspacing="0" class=rr_list>';
		foreach($back as $val){

			/*在已列出权限的状态下切换成员，选择了个人，已存在组的权限也为不可再配置*/
			$isismu	 = ((in_array($val['code'],$rightGrup) && $isgu != 'group') )?'disabled':'';
			$checks	 = in_array($val['code'],$rightsArr)?'checked':'';
			$backstr.= '<tr><td height=40><label class=right_list><input type=checkbox name=rights_id[] value="'.$val['code'].'" '.$checks.' '.$isismu.' ><span>'.$val['desc'].'</font></span> </label><br><span style="color:#bdbdbd;padding-left:20px">'.$val['comment'].'</span></td></tr>';
		}
		$backstr   .= '</table>';
		return $backstr;
	}


	/*取得组或用户权限代码，返回一维数组*/
	public function getRights($type,$guid){

		$user = $this->S->dao('user');
		$group= $this->S->dao('admin_group');

		if($type == 'uaccess'){//查用户权限，得继承组权限
			$useAes = $user->D->get_one(array('uid'=>$guid),'uaccess,groupid');
			$graAes = $group->D->get_one(array('id'=>$useAes['groupid']),'access');

			$u_access = array();
			$arr_accs = json_decode($useAes['uaccess'],true);//判断个人权限
			if(is_array($arr_accs)){ $u_access = $arr_accs; }

			$rightary = array_merge(json_decode($graAes,true),$u_access);//组合个人权限与组权限

		}elseif($type == 'access'){
			$access = $group->D->get_one(array('id'=>$guid),$type);
			$rightary = json_decode($access,true);
		}
		return $rightary;
	}

	/*查出该菜单下所有权限*/
	public function getMenuAllrights($ismenu){

		$menuAllarr	 = array();
		$backMenuall = $this->S->dao('rights')->D->get_allstr(' and b_id='.$ismenu,'','','code');
		foreach($backMenuall as $val){
			$menuAllarr[] = $val['code'];
		}
		return $menuAllarr;
	}

}
?>

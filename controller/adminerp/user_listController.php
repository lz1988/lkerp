<?php

$user = $this->S->dao('user');

if($detail == 'list'){
	$sqlstr= '';
	if($username)	{$sqlstr .= " and (eng_name like '%".$username."%' or chi_name like '%".$username."%') ";}
	if($groupid) 	{$sqlstr .= ' and g.id='.$groupid;}
	if($groupcatid)	{$sqlstr .= ' and c.id='.$groupcatid;}

	$InitPHP_conf['pageval'] = 15;
	$datalist = $user->getUserList($sqlstr);

	/*生成按部门搜索*/
	$backdatagroup			= $this->S->dao('admin_group')->D->get_allstr('','','','id,groupname');
	$shou_datagroup_html	= '<select name=groupid><option value=>=选择=</option>';
	foreach($backdatagroup as $val){
		$group_selected = $val['id'] == $groupid?'selected':'';
		$shou_datagroup_html.='<option value='.$val['id'].' '.$group_selected.'>'.$val['groupname'].'</option>';
	}
	$shou_datagroup_html 	.= '</select>';

	/*生成按部门属性*/
	$backdatacatg			= $this->S->dao('cat_group')->D->get_allstr();
	$backdatacatg_html		= '<select name=groupcatid><option value=>=选择=</option>';
	foreach($backdatacatg as $val){
		$groupc_selected = $val['id'] == $groupcatid?'selected':'';
		$backdatacatg_html.='<option value='.$val['id'].' '.$groupc_selected.'>'.$val['catg_name'].'</option>';
	}
	$backdatacatg_html		.= '</select>';

	/** 取得角色 **/
	$roleArr	= $this->C->service('admin_access')->checkResRight();

	for($i=0;$i<count($datalist);$i++){
		$datalist[$i]['role']	= $roleArr['roles'][$datalist[$i]['roleid']];
		$datalist[$i]['picurl']	= empty($datalist[$i]['picurl'])?'./data/users/face_default.png':$datalist[$i]['picurl'];
	}
	$pageshow = array('username'=>$username,'groupcatid'=>$groupcatid,'groupid'=>$groupid);
	$this->V->mark(array('title'=>'用户列表','datalist'=>$datalist,'username'=>$username,'detail'=>$detail,'shou_datagroup_html'=>$shou_datagroup_html,'backdatacatg_html'=>$backdatacatg_html));
}

elseif($detail == 'new'){

	if(!$this->C->service('admin_access')->checkResRight('r_u_add')){$this->C->sendmsg();}

	$roleArr	= $this->C->service('admin_access')->checkResRight();/** 取得角色 **/
	$datalist	= $this->S->dao('admin_group')->D->get_all('','','','id,groupname');

	$this->V->mark(array('title'=>'添加用户-用户列表(list)','detail'=>$detail,'datalist'=>$datalist,'roleArr'=>$roleArr['roles']));
	display();
}

elseif($detail == 'newmod'){
    $ctime = date('Y-m-d H:i:s');
	if(!$this->C->service('admin_access')->checkResRight('r_u_add')){$this->C->sendmsg();}
	$uid = $user->D->insert(array('username'=>$username,'password'=>md5($password),'eng_name'=>$eng_name,'chi_name'=>$chi_name,'telphone'=>$telphone,'msn'=>$msn,'email'=>$email,'picurl'=>$picurl,'groupid'=>$groupid,'roleid'=>$roleid,'lastmodify'=>$ctime));

	if($uid){
		echo "<script>alert('添加成功！');window.location='index.php?action=user_list&detail=list'</script>";
	}
}

elseif($detail == 'edit'){

	if(!$this->C->service('admin_access')->checkResRight('r_u_edit')){$this->C->sendmsg();}
	$data = $user->D->get_one_by_field(array('uid'=>$uid));
	$datalist = $this->S->dao('admin_group')->D->get_all('','','','id,groupname');

	$roleArr	= $this->C->service('admin_access')->checkResRight();/** 取得角色 **/

	$data['picurl'] = empty($data['picurl'])?'./data/users/face_default.png':$data['picurl'];
	$this->V->mark(array('title'=>'修改用户-用户列表(list)','detail'=>$detail,'uid'=>$uid,'data'=>$data,'datalist'=>$datalist,'roleArr'=>$roleArr['roles']));
}

elseif($detail == 'editmod'){

	if(!$this->C->service('admin_access')->checkResRight('r_u_edit')){$this->C->sendmsg();}
	$sid = $user->D->update_by_field(array('uid'=>$uid),array('username'=>$username,'eng_name'=>$eng_name,'chi_name'=>$chi_name,'telphone'=>$telphone,'email'=>$email,'picurl'=>$picurl,'msn'=>$msn,'groupid'=>$groupid,'roleid'=>$roleid,'lastmodify'=>date('Y-m-d H:i:s')));
	if($sid){
		echo "<script>alert('修改成功！');window.location='index.php?action=user_list&detail=list'</script>";
	}
}

/*删除帐号*/
elseif($detail == 'delete'){
	if(!$this->C->service('admin_access')->checkResRight('r_u_del')){
		exit('对不起，你没有该权限！');
	}
	$sid = $user->D->delete(array('uid'=>$uid));
	if($sid){echo '删除成功';}else{echo '删除失败';}
}

/*关闭帐号*/
elseif($detail == 'close'){
	if(!$this->C->service('admin_access')->checkResRight('r_u_del')){
		exit('对不起，你没有该权限！');
	}
	$sid = $user->D->update(array('uid'=>$uid),array('isuse'=>'0'));
	if($sid){echo '关闭成功';}else{echo '关闭失败';}
}

/*启用帐号*/
elseif($detail == 'enable'){
	if(!$this->C->service('admin_access')->checkResRight('r_u_del')){
		exit('对不起，你没有该权限！');
	}
	$sid = $user->D->update(array('uid'=>$uid),array('isuse'=>'1','lastmodify'=>date('Y-m-d H:i:s')));
	if($sid){echo '启用成功';}else{echo '启用失败';}
}

/*上传用户头像*/
elseif($detail == 'uploadfile'){
	$upload_dir = './data/users/';

	$file_name 	= $_FILES['userfile']['name'];
	$temp_arr 	= explode(".", $file_name);
	$file_ext 	= array_pop($temp_arr);
	$file_path 	= $upload_dir . time().'.'.$file_ext;
	$MAX_SIZE 	= 20000000;

	echo $_POST['buttoninfo'];
	if(!is_dir($upload_dir)){

	    if(!mkdir($upload_dir))
	        echo "文件上传目录不存在并且无法创建文件上传目录";
	    if(!chmod($upload_dir,0755))
	        echo "文件上传目录的权限无法设定为可读可写";
	}

	if($_FILES['userfile']['size']>$MAX_SIZE)
	    echo "上传的文件大小超过了规定大小";

	if($_FILES['userfile']['size'] == 0)
	    echo "请选择上传的文件";

	if(!move_uploaded_file( $_FILES['userfile']['tmp_name'], $file_path))
	    echo "复制文件失败，请重新上传";

		switch($_FILES['userfile']['error'])
		{
		    case 0:
		        echo "success,".$file_path;
		        break;
		    case 1:
		        echo "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值";
		        break;
		    case 2:
		        echo "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值";
		        break;
		    case 3:
		        echo "文件只有部分被上传";
		        break;
		    case 4:
		        echo "没有文件被上传";
		        break;
		}
}

/*重置密码*/
elseif($detail == 'resetpsw'){
	$sid = $this->S->dao('user')->D->update(array('uid'=>$uid),array('password'=>md5('lk123456')));
	if($sid){
		echo '1';
	}else{
		echo '0';
	}
}

if($detail != 'resetpsw' && $detail != 'editmod' && $detail != 'newmod' && $detail !='delete' && $detail !='close' && $detail!='uploadfile' && $detail!='enable'){

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/userlist');
	display();

}

?>

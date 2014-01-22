<?php
/*
 * Created on 2012-03-09
 *
 * by wall
 * @title 个人中心
 */


//个人中心页面
if ($detail == 'main') {
    
    if (!$this->C->service('admin_access')->checkResRight('r_announcement')){$this->C->sendmsg("无权限加载个人中心");}
	$group = $this->S->dao('admin_group');
	$user = $this->S->dao('user');
	$departlist = $group->D->get_all('', '', '', 'id,groupname');
	
	foreach ($departlist as &$val) {
		$val['user'] = $user->D->get_all(array('groupid'=>$val['id'],'isuse'=>1), '', '', 'eng_name,chi_name,email');
	}
	
	$this->V->mark(array('title'=>'个人中心', 'departlist'=>$departlist));
	$this->V->set_tpl('adminweb/person_main');

	display();
	//$this->V->set_tpl('admintag/tag_header','F');
	//$this->V->set_tpl('admintag/tag_footer','L');
}
?>
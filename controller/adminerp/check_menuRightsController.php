<?php
/**
 * 	Created on 2012-4-23
 *	@title 菜单权限判断
 */

$menu_url = 'index.php?action='.$_REQUEST['action'].'&detail='.$detail;
$back_mid = $this->S->dao('menu')->D->get_one_by_field(array('url'=>$menu_url),'id');
/*如果是菜单*/
if($back_mid){

	$back_menu 	= $this->S->dao('admin_group')->D->get_one(array('id'=>$_SESSION['groupid']),'menu_id');//组菜单权了
	$backumenu 	= $this->S->dao('user')->D->get_one(array('uid'=>$_SESSION['uid']),'maccess');//个人菜单权限
	$mArr		= json_decode($back_menu,true);
	$userMArr	= json_decode($backumenu,true);
	if($userMArr) $mArr = array_merge($mArr, $userMArr);

	if(!in_array($back_mid['id'],$mArr)) $this->C->sendmsg();

}
?>

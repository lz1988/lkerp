<?php
/**
 * 左导航控制器，配置权限。
 *
 */

if($detail == 'showleft'){


	$back_menu 	= $this->S->dao('admin_group')->D->get_one(array('id'=>$_SESSION['groupid']),'menu_id');//组菜单权了
	$backumenu 	= $this->S->dao('user')->D->get_one(array('uid'=>$_SESSION['uid']),'maccess');//个人菜单权限
	$mArr		= json_decode($back_menu,true);
	$userMArr	= json_decode($backumenu,true);
	if($userMArr) $mArr = array_merge($mArr, $userMArr);
	$menuidarr 	= implode(',',$mArr);

	$menulist 	= $this->S->dao('menu')->D->get_all_sql('select id,name,parent_id,url,sort_id from menu where id in ('.$menuidarr.') order by sort_id asc');

	$onemenu_list = array();
	$twomenu_list = array();

	foreach($menulist as $val){

		/*顶级菜单*/
		if($val['parent_id'] == '0'){
			$onemenu_list[] = array('name'=>$val['name'],'url'=>$val['url'],'id'=>$val['id']);
		}
		/*二级菜单*/
		else{
			$twomenu_list[$val['parent_id']][] = array('sort_id'=>$val['sort_id'],'name'=>$val['name'],'url'=>$val['url'],'id'=>$val['id']);
		}

	}

	$this->V->mark(array('onemenu_list'=>$onemenu_list,'twomenu_list'=>$twomenu_list));
	$this->V->set_tpl('admintag/tag_leftbanner');
	display();
}

/*取得标签链接与文字*/
elseif($detail == 'getTabmenu'){

	$backdata = $this->S->dao('menu')->D->get_one_by_field(array('id'=>$id),'name,url');
	echo json_encode($backdata);
}
?>
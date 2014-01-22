<?php
/*
 * Created on 2011-10-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class admin_groupDao extends D{
	public function getUserAccess(){//取用户权限的access
		$sql = 'select g.access,u.uaccess from user u, admin_group g where u.uid='.$_SESSION['uid'].' and u.groupid=g.id';
		$access = $this->D->get_all_sql($sql);
		return $access;
	}


	/*角色列表，加上所属组，联表*/
	public function getgroup_list($sqlstr){
		$sql = 'select g.id,g.groupname,g.desc,c.catg_name from admin_group as g left join cat_group as c on c.id=g.catg_id where 1 '.$sqlstr.' order by c.catg_name desc';
		return $this->D->query_array($sql);
	}

	/*
	 * create on 2012-06-12
	 * by wall
	 * @param $catg_id 部门属性id
	 * 获取描述
	 * */
	public function get_desc_by_catg_id($catg_id) {
		$sql = 'select `desc` from admin_group where catg_id='.$catg_id;
		return $this->D->query_array($sql);
	}
 }
?>

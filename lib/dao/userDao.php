<?php
/*
 * Created on 2011-10-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class userDao extends D{

	/*取得所有用户列表*/
	public function getUserList($sqlstr){

		$sql = 'select u.*,if(u.isuse="0","已关闭","正常") as status,g.groupname,g.id,c.catg_name from user as u left join admin_group g on u.groupid=g.id left join cat_group c on c.id=g.catg_id where 1 '.$sqlstr.' order by u.isuse desc,c.catg_name desc,g.id desc,u.uid desc';

		return $this->D->query_array($sql,'fetch_assoc');
	}
 }
?>

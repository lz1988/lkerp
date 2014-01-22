<?php
/*
 * Created on 2012-10-24
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class rightsDao extends D{

	/*查菜单权限关联*/
	public function get_MenuRgihts($sqlstr){

		$sql = 'select r.id, m.name, r.comment, r.code, r.desc from rights r left join menu m on r.b_id=m.id where 1 '.$sqlstr.' order by m.sort_id asc';
		return $this->D->query_array($sql);
	}
 }
?>

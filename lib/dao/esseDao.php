<?php
/*
 * Created on 2011-11-16
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class esseDao extends D{
 	/*
	 * Createed on 2012-4-13
	 * by wall
	 * 通过id获取记录信息
	 * @param $id esse_cat记录id
	 * */
 	public function get_one_by_id($id) {
 		$sql = 'select esseid,name from esse where id='.$id;
 		return $this->D->get_one_sql($sql);
 	}
 	
 	/*
 	 * create on 2012-06-11
 	 * update on 2012-06-13
 	 * by wall
 	 * 获取所有其他仓库ID
 	 * */
 	public function get_all_other_warehouse() {
 		$sql = 'select b.id as id,b.name,a.id as a_id from info_amazon as a left join esse as b on a.ia_houseid=b.id where ia_houseid <> 0';
 		return $this->D->query_array($sql);
 	}
 	
 	/*
 	 * create on 2012-06-11
 	 * update on 2012-06-13
 	 * by wall
 	 * @param $id 仓库id
 	 * 返回其他仓库的MWS帐号
 	 * */
 	public function get_extends_by_id($id) {
 		$sql = 'select b.id as id,b.name,a.id as a_id from info_amazon as a left join esse as b on a.ia_houseid=b.id where a.ia_houseid='.$id;
 		return $this->D->get_one_sql($sql);
 	}

 }
 

 	
?>

<?php
/*
 * Created on 2012-3-23
 *
 * by hanson
 */

 class esse_catDao extends D {
 /*
	 * Createed on 2012-4-13
	 * by wall
	 * 通过id获取记录信息
	 * @param $id esse_cat记录id
	 * */
 	public function get_one_by_id($id) {
 		$sql = 'select cat_name from esse_cat where id='.$id;
 		return $this->D->get_one_sql($sql);
 	}
 }
?>

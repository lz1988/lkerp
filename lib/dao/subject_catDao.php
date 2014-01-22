<?php
/*
 * Created on 2012-3-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class subject_catDao extends D{

	/*取得类别列表*/
	public function get_listcat(){
		$sql = 'SELECT ow. * , tw.parent_id AS have_son FROM  `subject_cat` AS ow LEFT JOIN subject_cat AS tw ON ow.id = tw.parent_id where ow.parent_id="" group by ow.id';
		return $this->D->query_array($sql);
	}
 }
?>

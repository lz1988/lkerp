<?php
/*
 * Created on 2012-3-21
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class subject_proDao extends D{

	public function get_listpro($sqlstr){
		$sql = 'select p.*,c.cat_name,e.cat_name as esse_cat_name from subject_pro as p left join subject_cat as c on c.id=p.cat_id  left join esse_cat as e on e.id=p.esse_cat_id where 1 '.$sqlstr.' order by pro_code asc ';
		return $this->D->query_array($sql);
	}
 }
?>

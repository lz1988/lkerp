<?php
/*
 * Created on 2012-06-27
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class send_listDao extends D{
	/*
	 * Create on 2012-06-28
	 * by wall
	 * @param $sl_id 留言id
	 * 返回指定留言id的所有邮件
	 * */
 	public function get_list_by_slid($sl_id) {
 		$sql = 'select * from send_list where sl_id='.$sl_id.' order by id asc';
 		return $this->D->query_array($sql);
 	}
 }
?>

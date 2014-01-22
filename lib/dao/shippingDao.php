<?php

class shippingDao extends D{
	/*
	 * create on 2012-05-21
	 * by wall
	 * 获取发货方式列表
	 * */
	public function get_all_list($sqlstr) {
		$sql = 'select * from shipping where 1 '.$sqlstr;
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-05-21
	 * by wall
	 * 获取某个ID发货方式的信息
	 * */
	public function get_one_by_id($id) {
		$sql = 'select * from shipping where id='.$id;
		return $this->D->get_one_sql($sql);
	}
		
}
?>
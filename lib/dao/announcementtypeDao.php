<?php
/* 
 * create on 2012-05-15
 * by wall
 * 公告类型dao
 * */


class announcementtypeDao extends D{
	/*
	 * create on 2012-05-15
	 * by wall
	 * param $sqlstr查询条件
	 * 获取公告类型列表
	 * */
	public function get_all_list($sqlstr) {
		return $this->D->get_list($sqlstr, '', 'id asc', '*');
	}
	
	/*
	 * create on 2012-05-15
	 * by wall
	 * param $id 目标类型id
	 * 获取公告类型详细信息名称、颜色
	 * */
	public function get_one_by_id($id) {
		$sql = 'select name,color from announcementtype where id='.$id;
		return $this->D->get_one_sql($sql);
	}
}

?>
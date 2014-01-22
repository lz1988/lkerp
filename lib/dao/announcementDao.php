<?php
/* 
 * create on 2012-05-15
 * by wall
 * 公告dao
 * */

class announcementDao extends D{
	/*
	 * create on 2012-05-15
	 * by wall
	 * param $sqlstr查询条件
	 * 获取公告列表
	 * */
	public function get_all_list($sqlstr) {
		$sql = 'select a.id,title,content,a.cuser,a.cdate,istop,color,name from ';
		$sql .= ' announcement as a left join announcementtype as b on a.atid=b.id ';		
		$sql .= ' where 1 '.$sqlstr;
		$sql .= ' order by istop desc,a.id desc ';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-05-30
	 * by wall
	 * param $uid 用户id
	 * param $sqlstr查询条件
	 * 获取公告列表(区分已经浏览和未浏览)
	 * */
	public function get_all_list_on_unread($uid,$sqlstr) {
		$sql = 'select a.id,title,content,a.cuser,a.cdate,istop,color,name,c.id as rid from ';
		$sql .= ' announcement as a left join announcementtype as b on a.atid=b.id ';
		$sql .= ' left join (select id,ml_msgid from msg_list where ml_uid='.$uid.' and ml_msgtype="0") as c on a.id=c.ml_msgid ';
		$sql .= ' where 1 '.$sqlstr;
		$sql .= ' order by istop desc,a.id desc ';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-05-16
	 * by wall
	 * param $atid公告类型id
	 * 获取公告类型数量
	 * */
	public function get_count_by_atid($atid) {
		$sql = 'select count(*) from announcement where atid='.$atid;
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-05-17
	 * by wall
	 * param $id 公告id
	 * 获取公告信息
	 * */
	public function get_one_by_id($id) {
		$sql = 'select a.id,title,content,a.cuser,a.cdate,istop,color,name from ';
		$sql .= ' announcement as a left join announcementtype as b on a.atid=b.id ';
		$sql .= ' where a.id='.$id;
		return $this->D->get_one_sql($sql);
	}
}

?>
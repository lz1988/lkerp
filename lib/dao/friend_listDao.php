<?php
/*
 * Created on 2012-07-05
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class friend_listDao extends D{
	/*
	 * create on 2012-07-05
	 * by wall
	 * @param $uid 目标用户id
	 * 条件:都加对方为好友且不是同时请求加好友
	 * 获取好友详细列表
	 * */
	public function get_friend_list_by_uid($uid) {
		$sql = 'select a.*,c.* from friend_list as a ';
		$sql .= ' left join friend_list as b on a.fl_uid=b.fl_friendid ';
		$sql .= ' left join user as c on a.fl_friendid=c.uid ';
		$sql .= ' where a.fl_friendid=b.fl_uid and a.fl_uid='.$uid.' and b.fl_status=1 and a.fl_status=1 and (a.fl_msgstatus <>1 or b.fl_msgstatus<>1)';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-07-06
	 * by wall
	 * @param $uid 目标用户id
	 * @param $eng_name 用户英文名	 * 
	 * 获取非好友详细列表
	 * */
	public function get_notfriend_list_by_uid_like_engname($uid, $eng_name) {
		$sql = 'select * from user where uid not in ';
		$sql .= ' (select a.fl_friendid from friend_list as a ';
		$sql .= ' left join friend_list as b on a.fl_uid=b.fl_friendid ';
		$sql .= ' left join user as c on a.fl_friendid=c.uid ';
		$sql .= ' where a.fl_friendid=b.fl_uid and a.fl_uid='.$uid.' and b.fl_status=1 and a.fl_status=1 and (a.fl_msgstatus <>1 or b.fl_msgstatus<>1)) and uid<>'.$uid.' and  eng_name like "%'.$eng_name.'%"';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-07-06
	 * by wall
	 * @param $uid 用户id
	 * @param $friendid 好友id
	 * @return 好友关系id,当前提示状态
	 * */
	public function get_id_by_uid_and_friendid($uid, $friendid) {
		$sql = 'select id,fl_msgstatus from friend_list where fl_uid='.$uid.' and fl_friendid='.$friendid;
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-07-09
	 * by wall
	 * @param $uid 用户id
	 * 获取指定用户的好友消息验证消息数量
	 * */
	public function get_msgcount_by_uid($uid) {
		$sql = 'select count(*) as num from friend_list where fl_uid='.$uid.' and fl_msgstatus<>0';
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-07-10
	 * by wall
	 * @param $uid 用户id
	 * 获取指定用户的好友消息验证消息列表
	 * */
	public function get_msglist_by_uid($uid) {
		$sql = 'select a.*,eng_name,chi_name from friend_list as a ';
		$sql .= ' left join user as b on a.fl_friendid=b.uid';
		$sql .= ' where fl_uid='.$uid.' and fl_msgstatus<>0';
		return $this->D->query_array($sql);
	}
}
?>

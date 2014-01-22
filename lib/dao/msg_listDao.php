<?php
/*
 * Created on 2012-05-29
 * */
class msg_listDao extends D{
	/*
	 * create on 2012-05-29
	 * by wall
	 * @param $uid 用户id
	 * @param $type 提示消息类型
	 * 获取指定用户的某种消息类型消息的数量
	 * */
	public function get_count_by_uid($uid, $type) {
		$sql = 'select count(*) as num from msg_list where ml_uid='.$uid.' and ml_msgtype="'.$type.'"';
		return $this->D->get_one_sql($sql);
	}	
	
	/*
	 * create on 2012-06-19
	 * by wall
	 * @param $uid 用户id
	 * @param $msgid 类型消息id（集合）
	 * @param $type 提示消息类型
	 * 获取指定用户的某种消息类型消息集合的数量
	 * */
	public function get_count_in_msgid($uid, $msgid, $type){
		$sql = 'select count(*) as num from msg_list where ml_uid='.$uid.' and ml_msgtype="'.$type.'" and ml_msgid in ('.$msgid.')';
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-06-19
	 * by wall
	 * @param $uid 用户id
	 * @param $msgid 类型消息id（集合）
	 * @param $type 提示消息类型
	 * 删除指定用户的某种消息类型消息集合
	 * */
	public function delete_in_msgid($uid, $msgid, $type) {
		$sql = 'delete from msg_list where ml_uid='.$uid.' and ml_msgtype="'.$type.'" and ml_msgid in ('.$msgid.')';
		return $this->D->query($sql);
	}
	
	/*
	 * create on 2012-05-29
	 * by wall
	 * @param $msgid 类型消息id
	 * @param $msgtype 消息类型
	 * 为每个用户插入一条消息提醒
	 * */
	public function insert_msg_by_all($msgid, $msgtype) {
		$sql = 'insert into msg_list(ml_uid,ml_msgid,ml_msgtype) select uid,'.$msgid.',"'.$msgtype.'" from user';
		return $this->D->query($sql);
	}
	
	/*
	 * creste on 2012-06-05
	 * by wall
	 * @param $id 提醒消息id
	 * 返回消息记录
	 * */
	public function get_one_by_id($id) {
		$sql = 'select * from msg_list where id='.$id;
		return $this->D->get_one_sql($sql);
	}
}
?>

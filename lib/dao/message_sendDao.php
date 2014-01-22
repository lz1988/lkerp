<?php
/*
 * Created on 2012-06-27
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class message_sendDao extends D{
	/*
	 * Create on 2012-06-28
	 * by wall
	 * @param $uid 发件人id
	 * 获取用户已发送留言列表
	 * */
	public function get_list_by_userid($uid) {
		$sql = 'select id,ms_receives,ms_content,ms_ctime,ms_failednum from message_send where ms_uid='.$uid.' order by id desc';
		return $this->D->query_array($sql);
	}	
	
	/*
	 * Create on 2012-06-28
	 * by wall
	 * @parame $id 留言id
	 * 获取指定留言的信息
	 * */
	public function get_row_by_id($id) {
		$sql = 'select id,ms_receives,ms_content,ms_ctime,ms_failednum,ms_uname from message_send where id='.$id;
		return $this->D->get_one_sql($sql);
	}
	
	
}
?>

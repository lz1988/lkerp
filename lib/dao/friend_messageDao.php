<?php
/*
 * Created on 2012-07-05
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class friend_messageDao extends D{
	/*
	 * create on 2012-07-12
	 * by wall
	 * @param $uid 用户id
	 * 获取指定用户所有顶级留言（用户第一条留言信息）
	 * */
	public function get_all_firstmessage_list_by_uid($uid) {
		$sql = 'select a.*,picurl from friend_message as a';
		$sql .= ' left join user as b on a.fm_friendid=b.uid';
		$sql .= ' where fm_previd=0 and fm_uid='.$uid.' order by a.id desc';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-07-12
	 * by wall
	 * @param $previd 第一条留言id
	 * 获取指定第一条留言id的所有留言
	 * */
	public function get_all_message_list_by_previd($previd) {
		$sql = 'select * from friend_message where fm_previd='.$previd.' order by id asc';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-07-12
	 * by wall
	 * @param $uid 用户id
	 * 获取指定用户所有顶级留言数量
	 * */
	public function get_all_firstmessage_count_by_uid($uid) {
		$sql = 'select count(*) as count from friend_message where fm_previd=0 and fm_uid='.$uid;
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-07-17
	 * by wall
	 * @param $uid 被留言用户id
	 * @param $friendid 留言用户id
	 * @param $friendname 留言用户全名
	 * @param $content 留言内容
	 * @param $previd 第一级留言id (0:当前为第一级留言,其他:第一级留言id)
	 * @param $time 留言时间
	 * @param $usertime 留言板主人查看时间
	 * @param $friendtime 留言好友查看时间 
	 * 插入一条新留言信息
	 * */
	public function insert_one_leave_message($uid, $friendid, $friendname, $content, $previd, $time, $usertime, $friendtime) {
	   
	    /*是否设置转接人*/
        $user_temp = $this->D->query_array("select user.eng_name,user_send.senduid from user_send  join `user` on `user`.uid=user_send.uid where `user`.uid=".$uid."");
        if (count($user_temp)>0){
            foreach($user_temp as $v){
                    $_sendid = $this->D->get_one_by_field(array('fm_content'=>$content.'(转发来自'.$v['eng_name'].')','fm_uid'=>$v['senduid']),'id');
                    if (empty($_sendid))
                        $this->D->insert(array('fm_uid'=>$v['senduid'], 'fm_friendid'=>$friendid, 'fm_friendname'=>$friendname, 'fm_content'=>$content.'(转发来自'.$v['eng_name'].')', 'fm_previd'=>$previd, 'fm_ctime'=>$time, 'fm_usertime'=>$usertime, 'fm_friendtime'=>$friendtime));
            } 
        }
        
        $did = $this->D->get_one_by_field(array('fm_content'=>$content,'fm_uid'=>$uid),'id');
        if (empty($did))
            return $this->D->insert(array('fm_uid'=>$uid, 'fm_friendid'=>$friendid, 'fm_friendname'=>$friendname, 'fm_content'=>$content, 'fm_previd'=>$previd, 'fm_ctime'=>$time, 'fm_usertime'=>$usertime, 'fm_friendtime'=>$friendtime));
            
	}
	
	/*
	 * create on 2012-07-17
	 * by wall
	 * @param $uid 被留言用户id
	 * @param $previd 第一级留言id
	 * 获取一级留言id中好友id(不包括本人)
	 * */
	public function get_other_friendid_by_previd($uid, $previd) {
		$sql = 'select fm_friendid from friend_message where fm_friendid<>'.$uid.' and (id='.$previd.' or fm_previd='.$previd.') group by fm_friendid';
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-07-17
	 * by wall
	 * @param $uid 用户id
	 * 获取指定用户的新好友留言数量（包括好友对自己留言、好友对自己留言的回复），不包括自己对自己的留言
	 * */
	public function get_msgcount_by_uid($uid) {
		$sql = 'select count(*) as num from (';
		$sql .= ' select * from friend_message where fm_uid<>fm_friendid and fm_uid='.$uid.' and fm_ctime>fm_usertime '; 
		$sql .= ' union all ';
		$sql .= ' select b.* from friend_message as a left join friend_message as b on a.id=b.fm_previd where a.fm_uid<>a.fm_friendid and a.fm_friendid='.$uid.' and b.fm_ctime>b.fm_friendtime ';
		$sql .= ' ) as tb';
		return $this->D->get_one_sql($sql);
	}	
	
	/*
	 * create on 2012-07-17
	 * by wall
	 * @param $uid 用户id
	 * 获取指定用户的新好友留言（包括好友对自己留言、好友对自己留言的回复），不包括自己对自己的留言
	 * */
	public function get_msglist_by_uid($uid) {
		$sql = 'select * from (';
		$sql .= ' select * from friend_message where fm_uid<>fm_friendid and fm_uid='.$uid.'  '; 
		$sql .= ' union all ';
		$sql .= ' select b.* from friend_message as a left join friend_message as b on a.id=b.fm_previd where a.fm_uid<>a.fm_friendid and a.fm_friendid='.$uid.'  ';
		$sql .= ' ) as tb order by id desc';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-07-17
	 * by wall
	 * @param $id 留言id
	 * 获取新留言id的第一级留言id
	 * */
	public function get_firstmessage_by_id($id) {
		$sql = 'select * from (';
		$sql .= ' select * from friend_message where id='.$id.' and fm_previd=0';
		$sql .= ' union all ';
		$sql .= ' select b.* from friend_message as a left join friend_message as b on b.id=a.fm_previd where a.id='.$id.' and a.fm_previd<>0';
		$sql .= ' ) as tb';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-07-18
	 * by wall
	 * @param $id 第一级留言id
	 * @param $time 修改时间
	 * 将与第一级留言id相关的所有留言用户查看时间修改
	 * */
	public function update_usertime_by_id($id, $time) {
		$sql = 'update friend_message set fm_usertime="'.$time.'" where fm_ctime>fm_usertime and (id='.$id.' or fm_previd='.$id.')';
		return $this->D->query($sql);
	}
	
	/*
	 * create on 2012-07-18
	 * by wall
	 * @param $id 第一级留言id
	 * @param $time 修改时间
	 * 将与第一级留言id相关的所有留言好友查看时间修改
	 * */
	public function update_friendtime_by_id($id, $time) {
		$sql = 'update friend_message set fm_friendtime="'.$time.'" where fm_ctime>fm_friendtime and (id='.$id.' or fm_previd='.$id.')';
		return $this->D->query($sql);
	}
	
	/*
	 * create on 2012-07-18
	 * by wall
	 * @param $uid 留言簿主人id
	 * @param $friendid 留言簿留言好友id
	 * 获取指定留言簿主人id的留言簿中指定好友id的所有第一级留言的信息
	 * */
	public function get_firstmessage_list_by_friendid_in_uid($uid, $friendid) {
		$sql = 'select distinct id from (';
		$sql .= ' select id from friend_message where fm_uid='.$uid.' and fm_friendid='.$friendid.' and fm_previd=0 ';
		$sql .= ' union all ';
		$sql .= ' select fm_previd as id from friend_message where fm_uid='.$uid.' and fm_friendid='.$friendid.' and fm_previd<>0 ';
		$sql .= ' ) as tb';
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-07-18
	 * by wall
	 * @param $uid 留言簿主人id
	 * @param $time 修改时间
	 * 将与留言簿主人的所有留言用户查看时间修改
	 * */
	public function update_usertime_by_uid($uid, $time) {
		$sql = 'update friend_message set fm_usertime="'.$time.'" where fm_ctime>fm_usertime and fm_uid='.$uid;
		return $this->D->query($sql);
	}
	
	/*
	 * create on 2012-07-18
	 * by wall
	 * @param $uid 留言簿主人id
	 * 获取留言簿主人留言簿中未读留言（包括好友回复）的数量
	 * */
	public function get_host_unread_count_by_uid($uid) {
		$sql = 'select count(*) as num from friend_message where fm_ctime>fm_usertime and fm_uid='.$uid;
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-07-18
	 * by wall
	 * @param $id 第一级留言id
	 * 获取第一级留言下所有留言簿主人未读留言（包括好友回复）数量
	 * */
	public function get_host_unread_count_by_id($id) {
		$sql = 'select count(*) as num from friend_message where fm_ctime>fm_usertime and (id='.$id.' or fm_previd='.$id.')';
		return $this->D->get_one_sql($sql); 
	}
	
	/*
	 * create on 2012-07-18
	 * by wall
	 * @param $id 第一级留言id
	 * 获取第一级留言下所有留言簿好友未读留言（包括好友回复）数量
	 * */
	public function get_friend_unread_count_by_id($id) {
		$sql = 'select count(*) as num from friend_message where fm_ctime>fm_friendtime and (id='.$id.' or fm_previd='.$id.')';
		return $this->D->get_one_sql($sql); 
	}
	
	/*
	 * create on 2012-07-30
	 * by wall
	 * @param $id 留言id
	 * @param $time 标记时间
	 * 标记指定id留言为已读
	 * */
	public function update_time_in_id($id, $time) {
		$sql = 'update friend_message set fm_usertime="'.$time.'",fm_friendtime="'.$time.'" where id in ('.$id.')';
		return $this->D->query($sql); 
	}
}
?>

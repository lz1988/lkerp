<?


if($detail == 'pronounce'){

	$back = empty($back)?'<a href="javascript:void(0);history.back(-1);">返回</a>':'<a href='.urldecode($back).'>返回</a>';
	$this->V->mark(array('title'=>'网站提醒','content'=>urldecode($content),'back'=>$back));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/pronounce');
	display();
}

elseif ($detail == 'list') {
	$num = 0;
	$count1 = $this->S->dao('msg_list')->get_count_by_uid($_SESSION['uid'], 0);
	$datalist[$num]['title'] = '<span src="index.php?action=announcement&detail=show_list&ml_msgid=2">公司公告</span>';
	$datalist[$num]['num'] = '<span src="index.php?action=announcement&detail=show_list&ml_msgid=2">'.$count1['num'].'</span>';
	$num++;
	$count2 = $this->S->dao('friend_list')->get_msgcount_by_uid($_SESSION['uid']);
	$datalist[$num]['title'] = '<span src="index.php?action=friend_communication&detail=check_message_list">验证消息</span>';
	$datalist[$num]['num'] = '<span src="index.php?action=friend_communication&detail=check_message_list">'.$count2['num'].'</span>';
	$num++;
	$count3 = $this->S->dao('friend_message')->get_msgcount_by_uid($_SESSION['uid']);
	$datalist[$num]['title'] = '<span src="index.php?action=friend_communication&detail=new_leave_message_list">好友留言</span>';
	$datalist[$num]['num'] = '<span src="index.php?action=friend_communication&detail=new_leave_message_list">'.$count3['num'].'</span>';
	$num++;

	$this->V->mark(array('title'=>'消息列表'));
	$tablewidth = '1100';
	$displayarr = array();
	$displayarr['title'] 	    = array('showname'=>'消息类型', 'width'=>'160');
	$displayarr['num'] 	    	= array('showname'=>'消息数量', 'width'=>'150');

	$jslink .= "<script type='text/javascript' src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/msg_list.js'></script>\n";

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}

/*
 * create on 2012-05-29
 * by wall
 * 获取用户未浏览的公告信息（条数）
 * */
/*
elseif ($detail == 'announcement') {
	$_SESSION['announcement_stypemu'] = json_encode(array('ml_msgid-s-e'=>' '));
	$count = $this->S->dao('msg_list')->get_count_by_uid($_SESSION['uid'], 0);
	$temp['name'] = '公司公告';
	$temp['count'] = $count['num'];
	$this->getLibrary('basefuns')->setsession('check_announcement_time', time());
	echo json_encode($temp);
}
*/
/*
 * create on 2012-05-28
 * by wall
 * 验证是否有新公告,有新公告发布，获取用户未浏览的公告信息（条数）
 * */
elseif ($detail == 'check_announcement') {
	$file = './data/file/announcement.msg';
	$fp = fopen($file,'rb');
	$result = 0;
	$time = fread($fp, filesize($file));
	fclose($fp);
	if ($time > $_SESSION['check_announcement_time']) {
		$_SESSION['announcement_stypemu'] = json_encode(array('ml_msgid-s-e'=>' '));
		$count = $this->S->dao('msg_list')->get_count_by_uid($_SESSION['uid'], 0);
		$temp['name'] = '公司公告';
		$temp['count'] = $count['num'];
		$result = json_encode($temp);
	}
	$this->getLibrary('basefuns')->setsession('check_announcement_time', time());
	echo $result;
}

/*
 * create on 2012-07-09
 * by wall
 * 验证是否有新好友信息
 * */
elseif ($detail == 'check_friend_add') {
	$file = './data/file/friendadd'.$_SESSION['uid'].'.msg';
	$result = 0;
	if (file_exists($file) && filemtime($file) > $_SESSION['check_friendadd_time']) {
		$count = $this->S->dao('friend_list')->get_msgcount_by_uid($_SESSION['uid']);
		$temp['name'] = '验证消息';
		$temp['count'] = $count['num'];
		$result = json_encode($temp);
	}
	$this->getLibrary('basefuns')->setsession('check_friendadd_time', time());
	echo $result;
}

/*
 * create on 2012-07-17
 * by wall
 * 验证是否有新好友留言
 * */
elseif ($detail == 'check_friend_msg') {
	$file = './data/file/friendmsg'.$_SESSION['uid'].'.msg';
	$result = 0;
	if (file_exists($file) && filemtime($file) > $_SESSION['check_friendmsg_time']) {
		$count = $this->S->dao('friend_message')->get_msgcount_by_uid($_SESSION['uid']);
		$temp['name'] = '好友留言';
		$temp['count'] = $count['num'];
		$result = json_encode($temp);
	}
	$this->getLibrary('basefuns')->setsession('check_friendmsg_time', time());
	echo $result;
}

/*
 * create on 2012-07-09
 * by wall
 * 获取用户未浏览的公告信息（条数）
 * 获取好友信息
 * 获取好友留言信息
 * */
elseif ($detail == 'msg') {
	$_SESSION['announcement_stypemu'] = json_encode(array('ml_msgid-s-e'=>' '));
	$count1 = $this->S->dao('msg_list')->get_count_by_uid($_SESSION['uid'], 0);
	$temp['announcement_name'] = '公司公告';
	$temp['announcement_count'] = $count1['num'];
	$count2 = $this->S->dao('friend_list')->get_msgcount_by_uid($_SESSION['uid']);
	$temp['friendadd_name'] = '验证消息';
	$temp['friendadd_count'] = $count2['num'];
	$count3 = $this->S->dao('friend_message')->get_msgcount_by_uid($_SESSION['uid']);
	$temp['friendmsg_name'] = '好友留言';
	$temp['friendmsg_count'] = $count3['num'];
	$this->getLibrary('basefuns')->setsession('check_announcement_time', time());
	$this->getLibrary('basefuns')->setsession('check_friendadd_time', time());
	$this->getLibrary('basefuns')->setsession('check_friendmsg_time', time());
	$temp['count'] = $count1['num'] + $count2['num'] + $count3['num'];
	echo json_encode($temp);
}
?>
<?php
/*
 * Created on 2012-07-05
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
if ($detail == 'list') {
	$InitPHP_conf['pageval'] = 9;
	$datalist = $this->S->dao('friend_list')->get_friend_list_by_uid($_SESSION['uid']);
	foreach($datalist as &$val) {
		$val['friendphoto'] = '<img class="friend_photo" src="'.$val['picurl'].'" width="20" height="20" />';
		$val['friendname'] = '<div style="float: left;">'.$val['eng_name'].'_'.$val['chi_name'].'</div>';
		$val['oper'] = '<div style="float: right;" a="'.$val['fl_friendid'].'"><img class="wall_leave_message wall_cursor_pointer" src="./staticment/images/leavemsg.png" title="好友留言板" width="16" height="16" style="vertical-align:middle;" />&nbsp;&nbsp;<img class="wall_delete_friend wall_cursor_pointer" src="./staticment/images/bg_button_del.gif" title="删除好友" width="16" height="16" style="vertical-align:middle;" /></div>';
		$val['friendinfo'] = '<div class="friend_information"><ul>';
		$val['friendinfo'] .= '<li><span>姓&nbsp;&nbsp;&nbsp;&nbsp;名：</span><span>'.$val['chi_name'].'</span></li>';
		$val['friendinfo'] .= '<li><span>英文名：</span><span>'.$val['eng_name'].'</span></li>';
		$val['friendinfo'] .= '<li><span>电&nbsp;&nbsp;&nbsp;&nbsp;话：</span><span>'.$val['telphone'].'</span></li>';
		$val['friendinfo'] .= '<li><span>&nbsp;&nbsp;MSN&nbsp;：</span><span>'.$val['msn'].'</span></li>';
		$val['friendinfo'] .= '<li><span>邮&nbsp;&nbsp;&nbsp;&nbsp;箱：</span><span>'.$val['email'].'</span></li>';
		$val['friendinfo'] .= '</ul></div>';
	}
	$datalist = $this->C->array_sort($datalist, 'mdate', 1);


	$displaykey = array('friendphoto', 'friendname', 'oper', 'friendinfo');

	/*模板显示*/
	$button_str = array(
		'url'=>'index.php?action=friend_communication&detail=leave_message_list&uid='.$_SESSION['uid']
		, 'value'=>'留言簿'
		, 'classname' => 'wall_jumppage');

	/*模板显示*/	
	$flush_url = 'index.php?action=friend_communication&detail=list';
	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'button_str'=>$button_str, 'flush_url'=>$flush_url));

	$this->V->set_tpl('adminweb/single_model');

	display();
}

/*
 * create on 2012-07-06
 * by wall
 * 个人中心好友添加
 * */
elseif ($detail == 'add_friend') {
	$InitPHP_conf['pageval'] = 9;
	$datalist = $this->S->dao('friend_list')->get_notfriend_list_by_uid_like_engname($_SESSION['uid'], $eng_name);
	
	foreach($datalist as &$val) {
		$val['checkbox'] = '<input type="checkbox" class="wall_friend_id" value="'.$val['uid'].'"/>';
		$val['userphoto'] = '<img src="'.$val['picurl'].'" width="30" height="30" />';
		$val['name'] = '<div style="float: left;">'.$val['eng_name'].'_'.$val['chi_name'].'</div>';
		$val['oper'] = '<img class="wall_add_friend wall_cursor_pointer" url="'.$val['uid'].'" src="./staticment/images/addNode.png" title="添加好友" width="16" height="16" style="vertical-align:middle;" />';
	}
	
	$displaykey = array('checkbox','userphoto','name','oper');
	/* create by wall
	 * 自定义input控件
	 * class = wall_submit 为提交
	 * 提交附加数据为type=text的
	 * 数组key为input属性名称
	 * 数组value为input属性值
	 * */
	$searchlist[] = array(
		'type'=>'text',
		'name'=>'eng_name',
		'value'=>$eng_name
		);
	$searchlist[] = array(
		'type'=>'button',
		'url'=>'index.php?action=friend_communication&detail=add_friend',
		'value'=>'查询',
		'class' => 'wall_submit'
		);


	/* create by wall
	 * 自定义input控件
	 * class = wall_submit_by_class 为提交
	 * 提交附加数据为dataclass的
	 * 数组key为input属性名称
	 * 数组value为input属性值
	 * 
	 * */	
	$inputlist[] = array(
		'type'=>'button',
		'url'=>'index.php?action=friend_communication&detail=add_mod',
		'value'=>'添加+',
		'dataclass'=>'wall_friend_id',
		'class' => 'wall_submit_by_class'
	);

	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'inputlist'=>$inputlist, 'searchlist'=>$searchlist));

	$this->V->set_tpl('adminweb/div_model');

	display();
}

elseif($detail == 'add_mod') {
	$friend_list = $this->S->dao('friend_list');
	$friend_list->D->query('begin');
	$iserror = 0;
	// 批量添加多个好友
	if (is_array($friendid)) {
		$arrlength = count($friendid);
		for ($i = 0; $i < $arrlength; $i++) {
			$res1 = $friend_list->get_id_by_uid_and_friendid($_SESSION['uid'], $friendid[$i]);
			if ($res1) {
				$fid = $friend_list->D->update_by_field(array('id'=>$res1['id']),array('fl_status'=>1));
			}
			else {
				$fid = $friend_list->D->insert(array('fl_uid'=>$_SESSION['uid'], 'fl_friendid'=>$friendid[$i], 'fl_status'=>1, 'fl_msgstatus'=>0));			
			}
			if (!$fid) {
				$iserror = 1;
			}
			$res2 = $friend_list->get_id_by_uid_and_friendid($friendid[$i], $_SESSION['uid']);
			if ($res2) {
				$fid = $friend_list->D->update_by_field(array('id'=>$res2['id']),array('fl_msgstatus'=>1, 'fl_ctime'=>date('Y-m-d H-i-s', time())));
			}
			else {
				$fid = $friend_list->D->insert(array('fl_uid'=>$friendid[$i], 'fl_friendid'=>$_SESSION['uid'], 'fl_status'=>0, 'fl_msgstatus'=>1, 'fl_ctime'=>date('Y-m-d H:i:s', time())));			
			}
			if (!$fid) {
				$iserror = 1;
			}	
			$file = './data/file/friendadd'.$friendid[$i].'.msg';
			write_file($file, time());
		}
	}
	// 单个添加好友
	else {
		$res1 = $friend_list->get_id_by_uid_and_friendid($_SESSION['uid'], $friendid);
		if ($res1) {
			$fid = $friend_list->D->update_by_field(array('id'=>$res1['id']),array('fl_status'=>1));
		}
		else {
			$fid = $friend_list->D->insert(array('fl_uid'=>$_SESSION['uid'], 'fl_friendid'=>$friendid, 'fl_status'=>1, 'fl_msgstatus'=>0));			
		}
		if (!$fid) {
			$iserror = 1;
		}
		$res2 = $friend_list->get_id_by_uid_and_friendid($friendid, $_SESSION['uid']);
		if ($res2) {
			$fid = $friend_list->D->update_by_field(array('id'=>$res2['id']),array('fl_msgstatus'=>1, 'fl_ctime'=>date('Y-m-d H-i-s', time())));
		}
		else {
			$fid = $friend_list->D->insert(array('fl_uid'=>$friendid, 'fl_friendid'=>$_SESSION['uid'], 'fl_status'=>0, 'fl_msgstatus'=>1, 'fl_ctime'=>date('Y-m-d H:i:s', time())));			
		}
		if (!$fid) {
			$iserror = 1;
		}
		$file = './data/file/friendadd'.$friendid.'.msg';
		write_file($file, time());
	}
	if ($iserror) {
		$friend_list->D->query('rollback');
		echo '发送好友请求失败！';
	}
	else {
		$friend_list->D->query('commit');
		echo '发送好友请求成功！';
	}
}

/*
 * create on 2012-07-11
 * by wall
 * 删除指定目标好友
 * */
elseif ($detail == 'delete') {
	$friend_list = $this->S->dao('friend_list');
	$friend_list->D->query('begin');
	$errornum = 0;
	$upid1 = $friend_list->D->update_by_field(array('fl_uid'=>$_SESSION['uid'],'fl_friendid'=>$friendid),array('fl_status'=>'0', 'fl_msgstatus'=>'0', 'fl_ctime'=>date('Y-m-d H:i:s', time())));
	$upid2 = $friend_list->D->update_by_field(array('fl_uid'=>$friendid,'fl_friendid'=>$_SESSION['uid']),array('fl_status'=>'0', 'fl_msgstatus'=>'0', 'fl_ctime'=>date('Y-m-d H:i:s', time())));
	if (!$upid1 || !$upid2) {
		$errornum = 1;
	}
	if ($errornum) {
		$friend_list->D->query('rollback');
		$status = '0';
	}
	else {
		$friend_list->D->query('commit');
		$status = '1';
	}
	echo $status;
}

/*
 * create on 2012-07-10
 * by wall
 * 好友验证消息列表
 * */
elseif ($detail == 'check_message_list') {
	$msg_arr = array(
		'1' => '好友请求',
		'2' => '验证同意',
		'3' => '验证拒绝'
	);
	$friend_list = $this->S->dao('friend_list');
	$InitPHP_conf['pageval'] = 50;
	$datalist = $friend_list->get_msglist_by_uid($_SESSION['uid']);
	
	foreach ($datalist as &$val) {
		$val['name'] = '<span class="friend_name" a="'.$val['fl_friendid'].'" b="'.$val['fl_msgstatus'].'">'.$val['eng_name'].'_'.$val['chi_name'].'</span>';
		$val['message'] = '<span a="'.$val['fl_friendid'].'" b="'.$val['fl_msgstatus'].'">'.$msg_arr[$val['fl_msgstatus']].'</span>';
		$val['time'] = '<span a="'.$val['fl_friendid'].'" b="'.$val['fl_msgstatus'].'">'.date('Y-m-d', strtotime($val['fl_ctime'])).'</span>';
		if ($val['fl_msgstatus'] == '1') {
			$val['operation'] = '<span a="'.$val['fl_friendid'].'" b="1"><img class="friend_agree float_image" src="./staticment/images/sendsuccess.gif" title="同意"/>&nbsp;&nbsp;<img class="friend_refuse float_image" src="./staticment/images/sendfailed.gif" title="拒绝"/>&nbsp;&nbsp;<img class="friend_lgnore float_image" src="./staticment/images/marker.png" title="忽略"/></span>';
		}
		else {
			$val['operation'] = '<span a="'.$val['fl_friendid'].'" b="'.$val['fl_msgstatus'].'"></span>';
		}
	}
	
	$this->V->mark(array('title'=>'<a href="index.php?action=msg&detail=list">消息列表</a>&raquo;验证消息列表'));
	$tablewidth = '1100';
	$displayarr = array();	
	$displayarr['name'] 	    		= array('showname'=>'联系人', 'width'=>'160');
	$displayarr['message'] 	    		= array('showname'=>'验证信息', 'width'=>'150');
	$displayarr['time'] 		    	= array('showname'=>'时间', 'width'=>'150');
	$displayarr['operation'] 	    	= array('showname'=>'操作', 'width'=>'150');
	
	$jslink = "<style>img{width:16px;height:16px;}</style>\n";	
	$jslink .= '<link rel="stylesheet" type="text/css" href="./staticment/css/friend_list.css">';
	$jslink .= "<script type='text/javascript' src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/friend_list.js'></script>\n";
	
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}
/*
 * create on 2012-07-10
 * by wall
 * 处理好友请求（oper == 1：同意添加好友、2：拒绝添加好友）
 * */
elseif ($detail == 'oper_message') {
	$errornum = 0;
	$friend_list = $this->S->dao('friend_list');
	$friend_list->D->query('begin');
	if ($oper == '1') {
		$msg = '添加好友';
		$upid1 = $friend_list->D->update_by_field(array('fl_uid'=>$_SESSION['uid'],'fl_friendid'=>$friendid),array('fl_status'=>'1','fl_msgstatus'=>'0'));
		$upid2 = $friend_list->D->update_by_field(array('fl_uid'=>$friendid,'fl_friendid'=>$_SESSION['uid']),array('fl_status'=>'1','fl_msgstatus'=>'2', 'fl_ctime'=>date('Y-m-d H:i:s', time()))); 
		if (!$upid1 || !$upid2) {
			$errornum = 1;
		}
		
	}
	elseif ($oper == '2') {
		$msg = '拒绝好友';
		$upid1 = $friend_list->D->update_by_field(array('fl_uid'=>$_SESSION['uid'],'fl_friendid'=>$friendid),array('fl_status'=>'0','fl_msgstatus'=>'0'));
		$upid2 = $friend_list->D->update_by_field(array('fl_uid'=>$friendid,'fl_friendid'=>$_SESSION['uid']),array('fl_status'=>'0','fl_msgstatus'=>'3', 'fl_ctime'=>date('Y-m-d H:i:s', time()))); 
		if (!$upid1 || !$upid2) {
			$errornum = 1;
		}
		
	}
	elseif ($oper == '3') {
		$upid2 = $friend_list->D->update_by_field(array('fl_uid'=>$_SESSION['uid'],'fl_friendid'=>$friendid),array('fl_status'=>'0','fl_msgstatus'=>'0'));
		if (!$upid2) {
			$errornum = 1;
		}
	}
	if ($errornum) {
		$friend_list->D->query('rollback');
		$msg .= '失败';
		$status = '0';
	}
	else {
		$friend_list->D->query('commit');
		$msg .= '成功';
		$status = '1';
		$file = './data/file/friendadd'.$friendid.'.msg';
		write_file($file, time());
	}
	echo json_encode(array('msg'=>$msg, 'status'=>$status));
}

/*
 * create on 2012-07-10
 * by wall
 * 好友提示消息清除
 * */
elseif ($detail == 'clear_friend_msgstatus') {
	$friend_list = $this->S->dao('friend_list');
	$friend_list->D->update_by_field(array('fl_uid'=>$_SESSION['uid'],'fl_friendid'=>$friendid),array('fl_msgstatus'=>'0'));
	$res = $friend_list->get_id_by_uid_and_friendid($_SESSION['uid'], $friendid);
	if ($res['fl_msgstatus'] == '0') {
		echo '1';
	}
	else {
		echo '0';
	}
		
}

/*
 * create on 2012-07-12
 * by wall
 * 目标 好友留言列表
 * */ 
elseif ($detail == 'leave_message_list') {
	$friend_message = $this->S->dao('friend_message');
	$now = date('Y-m-d H:i:s', time());	
	$count = $friend_message->get_all_firstmessage_count_by_uid($uid);
	$InitPHP_conf['pageval'] = 10;
	$unreadcount = 0;
	// 查看自己留言簿
	if ($uid == $_SESSION['uid']) {
		$reply = '1';
		// 获取未查看的本人留言数量
		$unreadres = $friend_message->get_host_unread_count_by_uid($uid);
		if ($unreadres) {
			$unreadcount = $unreadres['num'];
		}
		// 所有自己留言标记自己已读
		$friend_message->update_usertime_by_uid($uid, $now);
	}
	// 查看好友留言簿
	else {
		// 好友留言簿中所有查看者留言标记已读
		$fres = $friend_message->get_firstmessage_list_by_friendid_in_uid($uid, $_SESSION['uid']);
		foreach ($fres as $val) {
			$unreadres = $friend_message->get_friend_unread_count_by_id($val['id']);
			if ($unreadres) {
				$unreadcount += $unreadres['num'];
			}
			$friend_message->update_friendtime_by_id($val['id'], $now);
		}		
	}
	$datalist = $friend_message->get_all_firstmessage_list_by_uid($uid);
	unset($InitPHP_conf['pageval']);
	foreach ($datalist as &$val) {
		$val['child'] = $friend_message->get_all_message_list_by_previd($val['id']);
		foreach ($val['child'] as &$child) {
			if ($child['fm_friendid'] == $_SESSION['uid']) {
				$child['fm_friendname'] = '我';
			}
			if ($val['fm_friendid'] == $_SESSION['uid'] || $child['fm_friendid'] == $_SESSION['uid']) {
				$val['reply'] = '1';
			}
		}		
	}
	$pageshow = array('uid'=>$uid);
	
	$jslink = "<script type='text/javascript'>var uid=".$uid."; var unreadcount=".$unreadcount.";</script>\n";
	
//	echo '<pre style="text-align:left;">'.print_r($datalist,1).'</pre>';
	$InitPHP_conf['pageval'] = 10;
	$friend_message->get_all_firstmessage_list_by_uid($uid);
	$this->V->mark(array('datalist'=>$datalist, 'reply'=>$reply, 'count'=>$count['count'], 'jslink'=>$jslink));
	$this->V->set_tpl('adminweb/friend_message');

	display();
}
/*
 * create on 2012-07-17
 * by wall
 * 好友留言
 * */
elseif ($detail == 'mod_leave_message') {
	$friend_message = $this->S->dao('friend_message');
	if (!isset($previd)) {
		$previd = 0;
		$friendtime = date('Y-m-d H:i:s', time());
	}
	else {
		if ($uid == $_SESSION['uid']) {
			$usertime = date('Y-m-d H:i:s', time());
		}
		else {
			$friendtime = date('Y-m-d H:i:s', time());
		}
	}
	$iid = $friend_message->insert_one_leave_message($uid, $_SESSION['uid'], $_SESSION['eng_name'].'_'.$_SESSION['chi_name'], $content, $previd, date('Y-m-d H:i:s', time()), $usertime, $friendtime);
	if ($iid) {
		echo '1';
		if ($uid != $_SESSION['uid']) {
			$file = './data/file/friendmsg'.$uid.'.msg';
			write_file($file, time());
		}
		elseif ($uid == $_SESSION['uid'] && $previd != 0) {
			$res = $friend_message->get_other_friendid_by_previd($uid, $previd);
			if ($res) {
				$file = './data/file/friendmsg'.$res['fm_friendid'].'.msg';
				write_file($file, time());
			}
		}
	}
	else {
		echo '0';
	}
}
/*
 * create on 2012-07-17
 * by wall
 * 个人留言信息列表（好友新留言，好友对自己留言回复）
 * */
elseif ($detail == 'new_leave_message_list') {
	$friend_message = $this->S->dao('friend_message');
	$InitPHP_conf['pageval'] = 50;
	$datalist = $friend_message->get_msglist_by_uid($_SESSION['uid']);
	
	foreach ($datalist as &$val) {
		$val['name'] = '<span class="friend_name" a="'.$val['id'].'" >'.$val['fm_friendname'].'</span>';
		$val['content'] = '<span a="'.$val['id'].'" >'.$val['fm_content'].'</span>';
		$val['time'] = '<span a="'.$val['id'].'" >'.date('Y-m-d', strtotime($val['fm_ctime'])).'</span>';
		if ($val['fm_previd'] == '0') {
			$val['type'] = '<span a="'.$val['id'].'" >好友留言</span>';
		}
		else {
			$val['type'] = '<span a="'.$val['id'].'" >好友回复</span>';
		}
	}
	
	$this->V->mark(array('title'=>'<a href="index.php?action=msg&detail=list">消息列表</a>&raquo;好友留言列表'));
	$tablewidth = '1100';
	$displayarr = array();	
	$displayarr['id']			 		= array('showname'=>'checkbox','width'=>'45','title'=>'全选');//选择框
	$displayarr['type'] 	    		= array('showname'=>'留言类型', 'width'=>'160');
	$displayarr['name'] 	    		= array('showname'=>'好友姓名', 'width'=>'150');
	$displayarr['content'] 		    	= array('showname'=>'内容', 'width'=>'150');
	$displayarr['time'] 	    		= array('showname'=>'时间', 'width'=>'150');
	
	$bannerstr = '<button onclick="onreaded()">标记已读</button>';
	
	$jslink = "<style>img{width:16px;height:16px;}</style>\n";	
	$jslink .= '<link rel="stylesheet" type="text/css" href="./staticment/css/friend_list.css">';
	$jslink .= "<script type='text/javascript'>var uid=0; var unreadcount=0;</script>\n";
	$jslink .= "<script type='text/javascript' src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/friend_message.js?2012-07-30'></script>\n";	
	
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}
elseif ($detail == 'msg_readed') {
	$msgid = implode(',',$id);
	$num = count($id);
	$time = date('Y-m-d H:i:s', time());
	$friend_message = $this->S->dao('friend_message');	
	$friend_message->update_time_in_id($msgid, $time);
	echo $num;
}
/*
 * create on 2012-07-17
 * by wall
 * 查看指定id相关留言
 * */
elseif ($detail == 'single_leave_message') {
	$friend_message = $this->S->dao('friend_message');
	$now = date('Y-m-d H:i:s', time());		
	$unreadcount = 0;
	$datalist = $friend_message->get_firstmessage_by_id($id);	
	foreach ($datalist as &$val) {
		$val['child'] = $friend_message->get_all_message_list_by_previd($val['id']);
		foreach ($val['child'] as &$child) {
			if ($child['fm_friendid'] == $_SESSION['uid']) {
				$child['fm_friendname'] = '我';
			}
			if ($val['fm_friendid'] == $_SESSION['uid'] || $child['fm_friendid'] == $_SESSION['uid']) {
				$val['reply'] = '1';
			}
		}		
	}
	if ($datalist[0]['fm_uid'] == $_SESSION['uid']) {
		$reply = '1';
		$unreadres = $friend_message->get_host_unread_count_by_id($datalist[0]['id']);
		$unreadcount = $unreadres['num'];
		// 查看好友的留言或好友在自己留言簿的回复
		$friend_message->update_usertime_by_id($datalist[0]['id'], $now);
	}
	else {
		$unreadres = $friend_message->get_friend_unread_count_by_id($datalist[0]['id']);
		$unreadcount = $unreadres['num'];
		// 本人查看好友留言簿的好友回复
		$friend_message->update_friendtime_by_id($datalist[0]['id'], $now);
	}
	$jslink = "<script type='text/javascript'>var uid=".$datalist[0]['fm_uid']."; var unreadcount=".$unreadcount.";</script>\n";
	
//	echo '<pre style="text-align:left;">'.print_r($datalist,1).'</pre>';
//	exit();
	
	$this->V->mark(array('datalist'=>$datalist, 'reply'=>$reply, 'count'=>$count['count'], 'jslink'=>$jslink));
	$this->V->set_tpl('adminweb/friend_message');

	display();
}
function write_file($filename, $msg) {
	$fp = fopen($filename, 'wb+'); 
	$result = '';
	if (flock($fp, LOCK_EX)) {
		fwrite($fp, $msg);
		flock(LOCK_UN);
		$result = true;
	}
	else {
		$result = false;
	}
	fclose($fp);
	return $result;
}
?>

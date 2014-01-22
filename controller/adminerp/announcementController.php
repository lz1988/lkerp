<?php
/*
 * create on 2012-05-15
 * by wall
 * 公告控制器
 * */

if ($detail == 'list') {
	/*搜索选项*/
	$stypemu = array(
		'title-s-l'=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;标题：',
		'atid-a-e'=>'&nbsp; &nbsp;公告类型：',
		'istop-a-e'=>'&nbsp; &nbsp; &nbsp;置顶：',
		'cuser-s-l'=>'<br> &nbsp; &nbsp; &nbsp;发布者：',
	);

	$istoparr = array(''=>'=请选择=','1'=>'是','0'=>'否');
	$atidarr = array(''=>'=请选择=');
	$atidlist = $this->S->dao('announcementtype')->get_all_list('');
	foreach ($atidlist as $at) {
		$atidarr[$at['id']] = $at['name'];
	}

	$sqlstr = str_replace('cuser', 'a.cuser', $sqlstr);

	//设置分页数为10
	$InitPHP_conf['pageval'] = 10;
	$datalist = $this->S->dao('announcement')->get_all_list($sqlstr);

	foreach ($datalist as &$val) {
		$val['name'] = '<font color="'.$val['color'].'">'.$val['name'].'</font>';
		$val['content'] = strip_tags($val['content']);
		if ($val['istop']) {
			$val['istop'] = '<a href=javascript:void(0);delitem("index.php?action=announcement&detail=untop&id='.$val['id'].'")  title="取消置顶">取消</a>';
			$val['title'] = '<font color=red>[顶]</font>&nbsp;'.$val['title'];
		}
		else {
			$val['istop'] = '<a href=javascript:void(0);delitem("index.php?action=announcement&detail=entop&id='.$val['id'].'")  title="置顶">置顶</a>';
		}
		$val['dele'] = '<a href=javascript:void(0);delitem("index.php?action=announcement&detail=delete&id='.$val['id'].'")  title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
	}

	$this->V->mark(array('title'=>'公告管理'));
	$tablewidth = '1100';
	$displayarr = array();
	$displayarr['title'] 	    = array('showname'=>'公告标题', 'width'=>'160');
	$displayarr['name'] 	    = array('showname'=>'公告类型', 'width'=>'150');
    $displayarr['content']    	= array('showname'=>'公告内容', 'width'=>'300');
	$displayarr['cuser'] 	    = array('showname'=>'发布者', 'width'=>'100');
	$displayarr['cdate'] 	    = array('showname'=>'发布时间', 'width'=>'150');
	$displayarr['istop'] 	    = array('showname'=>'是否置顶', 'width'=>'100');
	$displayarr['dele'] 		= array('showname'=>'删除', 'width'=>'50');

	$bannerstr = '<button onclick="window.location=\'index.php?action=announcement_type&detail=list\'">公告类型</button>';
	$bannerstr .= '<button onclick="window.location=\'index.php?action=announcement&detail=add\'">添加公告</button>';

	$jslink = "<style>img{width:16px;height:16px;}</style>\n";

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}
elseif ($detail == 'delete') {
	if (!$this->C->service('admin_access')->checkResRight('announcement_del')) {
		$this->C->ajaxmsg(0);
	}
	$announcement = $this->S->dao('announcement');
	$announcement->D->query('begin');
	if ($announcement->D->delete_by_field(array('id'=>$id)) && $this->S->dao('msg_list')->D->delete_by_field(array('ml_msgid'=>$id, 'ml_msgtype'=>'0'))) {
		$announcement->D->query('commit');
		$this->C->ajaxmsg(1);
	}
	else {
		$announcement->D->query('rollback');
		$this->C->ajaxmsg(0,'删除失败！');
	}
}
elseif ($detail == 'add') {
	if (!$this->C->service('admin_access')->checkResRight('announcement_add')) {
		$this->C->sendmsg();
	}

	$datalist = $this->S->dao('announcementtype')->get_all_list('');
	$atidoption = '';
	foreach ($datalist as $val) {
		$atidoption .= '<option value="'.$val['id'].'" style="color:'.$val['color'].';">'.$val['name'].'</option>';
	}

    $title = '增加公告-公告管理(list)';
    $conform = array('method'=>'post','action'=>'index.php?action=announcement&detail=addmod','width'=>'900');

    $this->V->mark(array('conform'=>$conform, 'atid'=>$atidoption, 'title'=>$title));

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/announcement');
	display();
}

elseif ($detail == 'addmod') {
	if (!$this->C->service('admin_access')->checkResRight('announcement_add')) {
		$this->C->ajaxmsg(0);
	}
	$announcement = $this->S->dao('announcement');
	$announcement->D->query('begin');
	$is_error = 0;
	if ($istop) {
		$istop = 1;
	}
	else {
		$istop = 0;
	}
	$aid = $announcement->D->insert(array('atid'=>$atid,'title'=>$title,'content'=>$content,'istop'=>$istop,'cdate'=>date('Y-m-d h-i-s',time()), 'cuser'=>$_SESSION['eng_name']));
	if (!$aid) {
		$is_error = 1;
	}
	$iid = $this->S->dao('msg_list')->insert_msg_by_all($aid,'0');
	if (!$iid) {
		$is_error = 1;
	}
	if ($is_error) {
		$announcement->D->query('rollback');
		echo '<script>alert("添加失败！重新添加！");history.go(-1);</script>';
	}
	else {
		$announcement->D->query('commit');
		write_file('./data/file/announcement.msg', time());
		$this->C->success('添加成功!','index.php?action=announcement&detail=list');
	}
}

elseif ($detail == 'untop') {
	if (!$this->C->service('admin_access')->checkResRight('announcement_edit')) {
		$this->C->ajaxmsg(0);
	}
	if($this->S->dao('announcement')->D->update_by_field(array('id'=>$id),array('istop'=>0))) {
		$this->C->ajaxmsg(1, '取消置顶成功', 1);
	}
}

elseif ($detail == 'entop') {
	if (!$this->C->service('admin_access')->checkResRight('announcement_edit')) {
		$this->C->ajaxmsg(0);
	}
	if($this->S->dao('announcement')->D->update_by_field(array('id'=>$id),array('istop'=>1))) {
		$this->C->ajaxmsg(1, '置顶成功', 1);
	}
}

elseif ($detail == 'person_center_list') {
	//设置显示总数为6
	$InitPHP_conf['pageval'] = 6;
	$reslist = $this->S->dao('announcement')->get_all_list_on_unread($_SESSION['uid'],$sqlstr);
	$datalist = array();
	$now = strtotime(date('Y-m-d', time()));
	foreach ($reslist as $val) {
		$temp = '['.$val['name'].']&nbsp;&nbsp;';
		$temp .= '<a href="javascript:void(0);parent.addMenutab(1002,\'公告中心\',\'index.php?action=announcement&detail=show&id='.$val['id'].'&rid='.$val['rid'].'\')" style="color:'.$val['color'].';">';
		$temp .= $val['title'].'</a>';
		//提示最新
		$temptime = strtotime($val['cdate']);
		$style = 'float:left;';
		if ($now - $temptime < 86400) {
			$style .= 'background:url(./staticment/images/newannouncement.gif) right center no-repeat; padding-right: 29px;';
		}
		$datalist[] = array(
			'title'=>array('style'=>$style,'text'=>$temp),
			'date'=>array('style'=>'float:right;color:#666666;','text'=>date('Y-m-d', strtotime($val['cdate'])))
		);
	}
	if ($InitPHP_conf['pageval'] == count($datalist)) {
		$more = '<a href="javascript:void(0);parent.addMenutab(1002,\'公告中心\',\'index.php?action=announcement&detail=show_list\');">更多>></a>';
	}

	$displaykey = array('title','date');
	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'more'=>$more));
	$this->V->set_tpl('adminweb/single_model_li');
	display();
}

elseif ($detail == 'show_list') {
	/*搜索选项*/
	$stypemu = array(
		'title-s-l'=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;标题：',
		'atid-a-e'=>'&nbsp; &nbsp;公告类型：',
		'ml_msgid-a-e'=>'&nbsp; &nbsp;浏览状况：'
	);

	$atidarr = array(''=>'=请选择=');
	$atidlist = $this->S->dao('announcementtype')->get_all_list('');
	foreach ($atidlist as $at) {
		$atidarr[$at['id']] = $at['name'];
	}
	$ml_msgidarr = array('1'=>'所有','2'=>'未读','3'=>'已读');
	$sqlstr = str_replace('and ml_msgid="1"','',$sqlstr);
	$sqlstr = str_replace('and ml_msgid="2"','and ml_msgid<>""',$sqlstr);
	$sqlstr = str_replace('and ml_msgid="3"','and ml_msgid is null',$sqlstr);
	//设置分页数为10
	$InitPHP_conf['pageval'] = 15;
	$datalist = $this->S->dao('announcement')->get_all_list_on_unread($_SESSION['uid'],$sqlstr);
	foreach ($datalist as &$val) {
		$val['title'] = '<a title="点击查看" href="index.php?action=announcement&detail=show&id='.$val['id'].'&a='.$title.'&b='.$atid.'&c='.$ml_msgid.'&rid='.$val['rid'].'">'.$val['title'].'</a>';
		$val['content'] = strip_tags($val['content']);
		$val['name'] = '<font color="'.$val['color'].'">'.$val['name'].'</font>';
		if ($val['istop']) {
			$val['title'] = '<font color=red>[顶]</font>&nbsp;'.$val['title'];
		}
		if($val['rid'] != '') {
			$val['title'] = '<img src="./staticment/images/mail_close.gif" style="width:14px;height:11px" />&nbsp;&nbsp;<b>'.$val['title'].'</b>';
			$val['name'] = '<b>'.$val['name'].'</b>';
			$val['content'] = '<b>'.$val['content'].'</b>';
			$val['istop'] = '<b>'.$val['istop'].'</b>';
			$val['cuser'] = '<b>'.$val['cuser'].'</b>';
			$val['cdate'] = '<b>'.$val['cdate'].'</b>';
		}
		else {
			$val['title'] = '<img src="./staticment/images/mail_open.gif" style="width:14px;height:11px" />&nbsp;&nbsp;'.$val['title'];
		}
	}

	$this->V->mark(array('title'=>'<a href="index.php?action=msg&detail=list">消息列表</a>&raquo;公告列表'));
	$tablewidth = '1075';
	$displayarr = array();
	$displayarr['id']			 = array('showname'=>'checkbox','width'=>'45','title'=>'全选');//选择框
	$displayarr['title'] 	    = array('showname'=>'公告标题', 'width'=>'200');
	$displayarr['name'] 	    = array('showname'=>'公告类型', 'width'=>'80');
    $displayarr['content']    	= array('showname'=>'公告内容', 'width'=>'500');
	$displayarr['cuser'] 	    = array('showname'=>'发布者', 'width'=>'100');
	$displayarr['cdate'] 	    = array('showname'=>'发布时间', 'width'=>'150');

	$bannerstr = '<button onclick="onreaded()">标记已读</button>';

	$jslink = "<style>img{width:16px;height:16px;}</style>\n";
	$jslink .= "<script type='text/javascript' src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/announcement.js'></script>\n";
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}
elseif ($detail == 'readed') {
	$msgid = implode(',',$id);
	$uid = $_SESSION['uid'];
	$msgtype = 0;
	$msg_list = $this->S->dao('msg_list');
	$res = $msg_list->get_count_in_msgid($uid, $msgid, $msgtype);
	$msg_list->delete_in_msgid($uid, $msgid, $msgtype);
	echo $res['num'];
}
elseif ($detail == 'show') {
	$announcement = $this->S->dao('announcement');
	$msg_list = $this->S->dao('msg_list');
	$sqlstr = '';
	if (!empty($a)) {
		$sqlstr .= ' and title like "%'.$a.'%" ';
	}
	if (!empty($b)) {
		$sqlstr .= ' and atid='.$b.' ';
	}
	if ($c == 2) {
		$sqlstr .= ' and ml_msgid<>"" ';
	}
	else if ($c == 3) {
		$sqlstr .= ' and ml_msgid is null ';
	}
	$res = $announcement->get_one_by_id($id);
	unset($InitPHP_conf['pageval']);
	$datalist = $announcement->get_all_list_on_unread($_SESSION['uid'],$sqlstr);
	$length = count($datalist);
	$prev = '<a href="#">已经是第一条</a>';
	$next = '<a href="#">已经是最后一条</a>';
	for ($i = 0; $i < $length; $i++) {
		if ($datalist[$i]['id'] == $id) {
			//$prev = $i==0?$prev:'<a href="index.php?action=announcement&detail=show&id='.$datalist[$i-1]['id'].'&a='.$a.'&b='.$b.'" title="'.$datalist[$i-1]['title'].'">'.strlen($datalist[$i-1]['title'])>10?substr($datalist[$i-1]['title'], 0, 10):$datalist[$i-1]['title'].'</a>';
			//$next = $i==$length-1?$next:'<a href="index.php?action=announcement&detail=show&id='.$datalist[$i+1]['id'].'&a='.$a.'&b='.$b.'" title="'.$datalist[$i+1]['title'].'">'.strlen($datalist[$i+1]['title'])>10?substr($datalist[$i+1]['title'], 0, 10):$datalist[$i+1]['title'].'</a>';
			if ($i < $length - 1) {
				$next = '<a href="index.php?action=announcement&detail=show&id='.$datalist[$i+1]['id'].'&a='.$a.'&b='.$b.'&c='.$c.'&rid='.$datalist[$i+1]['rid'].'" title="'.$datalist[$i+1]['title'].'">';
				$next .= '下一条:'.cn_substr($datalist[$i+1]['title'], 10);
				$next .= '</a>';
			}
			if ($i > 0) {
				$prev = '<a href="index.php?action=announcement&detail=show&id='.$datalist[$i-1]['id'].'&a='.$a.'&b='.$b.'&c='.$c.'&rid='.$datalist[$i-1]['rid'].'" title="'.$datalist[$i-1]['title'].'">';
				$prev .= '上一条:'.cn_substr($datalist[$i-1]['title'], 10);
				$prev .= '</a>';
			}
		}
	}

	$return = '<a href="index.php?action=announcement&detail=show_list&title='.$a.'&atid='.$b.'">返回公告列表</a>';
	$rs = $msg_list->get_one_by_id($rid);
	$msg_list->D->delete_by_field(array('id'=>$rid));

	if ($rid && $rs) {
		$del = '1';
	}
	else {
		$del = '0';
	}

	$this->V->mark(array('announcement'=>$res, 'prev'=>$prev, 'next'=>$next, 'return'=>$return, 'del' => $del));

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/show_announcement');
	display();
}

//中文截取2，单字节截取模式
function cn_substr($Str,$Length)
{
	global $s;
	$i = 0;
	$l = 0;
	$ll = strlen($Str);
	$s = $Str;
	$f = true;

	while ($i <= $ll) {
		if (ord($Str{$i}) < 0x80) {
			$l++; $i++;
		} else if (ord($Str{$i}) < 0xe0) {
			$l++; $i += 2;
		} else if (ord($Str{$i}) < 0xf0) {
			$l += 2; $i += 3;
		} else if (ord($Str{$i}) < 0xf8) {
			$l += 1; $i += 4;
		} else if (ord($Str{$i}) < 0xfc) {
			$l += 1; $i += 5;
		} else if (ord($Str{$i}) < 0xfe) {
			$l += 1; $i += 6;
		}

		if (($l >= $Length - 1) && $f) {
			$s = substr($Str, 0, $i);
			$f = false;
		}

		if (($l > $Length) && ($i < $ll)) {
			$s = $s . '...'; break; //如果进行了截取，字符串末尾加省略符号“...”
		}
	}
	return $s;
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

<?php
/*
 * Created on 2012-06-27
 *
 * by wall
 * @title 部门留言
 */
if ($detail == 'mail' || $detail == 'mailall') {
	date_default_timezone_set('Asia/Hong_Kong');
	$smtpserver = "ssl://smtp.gmail.com";//SMTP服务器
    $smtpserverport = 465;//SMTP服务器端口
    $smtpusermail = $_SESSION['eng_name'];//SMTP服务器的用户邮箱    
    $smtpuser = "loftkmail@gmail.com";//SMTP服务器的用户帐号
    $smtppass = "loftk123456";//SMTP服务器的用户密码
    $mailsubject = "=?UTF-8?B?" . base64_encode('部门留言通知') . "?=";//邮件主题       
    $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件    
    $smtp = $this->C->service('smtp');
    $smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
    $smtp->debug = false;//是否显示发送的调试信息
}

if ($detail == 'list') {
	$message_send = $this->S->dao('message_send');
	//$sqlstr = ' and ms_uid='.$_SESSION['uid'].' ';
	$InitPHP_conf['pageval'] = 10;
	//$res = $message_send->D->get_list($sqlstr,'','id desc', 'id,ms_receives,ms_content,ms_ctime,ms_failednum');
	$res = $message_send->get_list_by_userid($_SESSION['uid']);
	
	$datalist = array();
	$length = count($res);
	for ($i = 0; $i < $length; $i++) {
    	$datalist[$i]['receives'] = preg_replace('/<[^>]*>/', '', $res[$i]['ms_receives']).'<span>'.$res[$i]['id'].'</span>';
    	if ($res[$i]['ms_failednum'] == 0) {
    		$datalist[$i]['receives'] = '<img src="./staticment/images/sendany.png" style="width:13px;height:9px;" title="留言投递成功" />&nbsp;'.$datalist[$i]['receives'];
    	}
    	else {
    		$datalist[$i]['receives'] = '<img src="./staticment/images/sendall.png" style="width:13px;height:9px;" title="留言投递部分失败" />&nbsp;'.$datalist[$i]['receives'];
    	}
		$datalist[$i]['content'] = strip_tags($res[$i]['ms_content']).'<span>'.$res[$i]['id'].'</span>';
		$datalist[$i]['ctime'] = date('Y-m-d', strtotime($res[$i]['ms_ctime'])).'<span>'.$res[$i]['id'].'</span>';
	}
	
	$this->V->mark(array('title'=>'已发送'));
	$tablewidth = '1100';
	$displayarr['receives'] 		= array('showname'=>'收件人', 'width'=>'200');
	$displayarr['content'] 	    	= array('showname'=>'主题', 'width'=>'800');
	$displayarr['ctime'] 	    	= array('showname'=>'时间', 'width'=>'100');
	
	$jslink = "<style>img{width:16px;height:16px;}span{display:none;}</style>\n";	
	$jslink .= "<script type='text/javascript' src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/department_message.js'></script>\n";
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}
elseif ($detail == 'sendpage') {
	$message_send = $this->S->dao('message_send');
	$send_list = $this->S->dao('send_list');
	$messageres = $message_send->get_row_by_id($id);
	//echo '<pre>'.print_r($res,1).'</pre>';
	$sendres = $send_list->get_list_by_slid($id);
	//echo '<pre>'.print_r($res,1).'</pre>';
	
	unset($InitPHP_conf['pageval']);
	$datalist = $message_send->get_list_by_userid($_SESSION['uid']);
	$length = count($datalist);
	$prev = '上一封';
	$next = '下一封';
	for ($i = 0; $i < $length; $i++) {
		if ($datalist[$i]['id'] == $id) {			
			if ($i < $length - 1) {
				$next = '<a href="index.php?action=department_message&detail=sendpage&id='.$datalist[$i+1]['id'].'">下一条</a>';	
			}
			if ($i > 0) {
				$prev = '<a href="index.php?action=department_message&detail=sendpage&id='.$datalist[$i-1]['id'].'">上一条</a>';
			}
		}
	}
	$receives = $messageres['ms_receives'];
	$receives = str_replace(';',';  ', $receives);
	$receives = str_replace('<','&lt;', $receives);
	$receives = str_replace('>','&gt;', $receives);
	$receives = str_replace('&lt;','<b class="tcolor">&lt;', $receives);
	$receives = str_replace('&gt;','&gt;</b>', $receives);
	
	if ($messageres['ms_failednum'] == 0) {
		$mailstatus = '投递成功';
	}
	else {
		$mailstatus = '投递部分失败';
	}
	
	$maillist = array();
	$resend = array();
	$i = 0;
	foreach ($sendres as $val) {
		$mail = $val['sl_mail'];
		preg_match_all('/<([^>]*)>/', $mail, $matches_pattern);
		$maillist[$i]['mail'] = $matches_pattern[1][0];
		if ($maillist[$i]['mail'] == '') {
			$maillist[$i]['mail'] = '错误邮箱';
		}
		if ($val['sl_status'] == 0) {
			$maillist[$i]['status'] = '<font color="red">投递失败</font>';
			if ($maillist[$i]['mail'] != '错误邮箱') {
				$maillist[$i]['status'] .= '&nbsp;&nbsp;<a class="sendagain" href="javascript:void(0)">[重新发送]</a>';
				$resend[] = $val['id'];
			}
		}
		else {
			$maillist[$i]['status'] = '已投递到对方邮箱';
		}
		$maillist[$i]['time'] = $val['sl_time'];
		$i++;
	}
	
	//echo $prev.$next;
	$js = "<script>\n var arr=[".implode(',', $resend)."];\n var failednum=".$messageres['ms_failednum'].";\n var ms_id=".$id.";\n</script>\n";
	$this->V->mark(array('next'=>$next,'prev'=>$prev,'content'=>$messageres['ms_content'], 'receives'=>$receives, 'sendname'=>$messageres['ms_uname'], 'sendtime'=>$messageres['ms_ctime'], 'maillist'=>$maillist, 'mailstatus'=>$mailstatus, 'js'=>$js));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/sendpage');
	display();
}
elseif ($detail == 'mail') {	
	$message_send = $this->S->dao('message_send');
	$send_list = $this->S->dao('send_list');
    $smtpemailto = $mail;//发送给谁    
    $mailbody = $content;
    $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
    $smtp = $this->C->service('smtp');
    $smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
    $smtp->debug = false;//是否显示发送的调试信息
    $send = $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
    if($send==1){
    	$message_send->D->update_by_field(array('id'=>$ms_id),array('ms_failednum'=>$failednum));
    	$send_list->D->update_by_field(array('id'=>$sl_id),array('sl_status'=>'1', 'sl_time'=>date('Y-m-d H:i:s', time())));
    	echo "0";
    }else{
    	$send_list->D->update_by_field(array('id'=>$sl_id),array('sl_time'=>date('Y-m-d H:i:s', time())));
        echo "1";
    }
}
elseif ($detail == 'mailall') {
	$message_send = $this->S->dao('message_send');
	$send_list = $this->S->dao('send_list');
	$receives = array();
	$length = count($mail);
	for ($i = 0; $i < $length; $i++) {
		$receives[] = $rename[$i].'<'.$mail[$i].'>';
	}
	$ms_id = $message_send->D->insert(array('ms_content'=>$content, 'ms_ctime'=>date('Y-m-d H:i:s', time()), 'ms_uid'=>$_SESSION['uid'], 'ms_uname'=>$_SESSION['eng_name'], 'ms_receives'=>implode(';', $receives)));	
    $smtpemailto = implode(';',$mail);//发送给谁
    $mailbody = $content;
    // 发送失败数量
    $failednum = $length;
    for ($i = 0; $i < $length; $i++) {
   	 	$send = $smtp->sendmail($mail[$i], $smtpusermail, $mailsubject, $mailbody, $mailtype);
	    if($send==1){
	    	$send_list->D->insert(array('sl_id'=>$ms_id,'sl_mail'=>$receives[$i],'sl_status'=>1,'sl_time'=>date('Y-m-d H:i:s', time())));
	    	$failednum--;
	    	echo $receives[$i]."发送成功！";
	    }else{
	    	$send_list->D->insert(array('sl_id'=>$ms_id,'sl_mail'=>$receives[$i],'sl_status'=>0,'sl_time'=>date('Y-m-d H:i:s', time())));
	        echo $receives[$i]."发送失败！";
	    }
    }
    $message_send->D->update_by_field(array('id'=>$ms_id), array('ms_failednum'=>$failednum));
    echo '有'.$failednum.'封邮件发送失败';
}
elseif ($detail == 'test') {
	echo $_SESSION['eng_name'].'_'.$_SESSION['chi_name'].'::'.$mail.'::'.$content;
}
?>
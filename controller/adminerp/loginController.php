<?php


/*登陆*/
if($detail == 'login'){

	$user = $this->S->dao('user');
    $user_log = $this->S->dao('user_log');
	$check_timeout 	= $this->S->dao('timeout');
	$basefun = $this->getLibrary('basefuns');
    $user_ip = $basefun->getIp();
	if(($basefun->getsession('loginsess') == $loginsess_p) and ($loginsess_p != '')){
		$sesok = 1;
	}

	if($sesok){
	            if(!$username || empty($username)){
                    $basefun->Js_msg('登陆错误，用户不能为空！');
	            }

                if(!$password || empty($password)){
                    $basefun->Js_msg('登陆错误，密码不能为空！');
                }

                if(!$scode || empty($scode)){
                    $basefun->Js_msg('登陆错误，验证码不能为空！');
                }

				$loginok = $user->D->get_one_by_field(array('username'=>$username,'isuse'=>1));

                if(!$loginok){
                    $basefun->Js_msg('您的账号不存在，请联系管理员！');
                }

                if(md5($password) != $loginok['password']){
                    $basefun->Js_msg('登陆错误，输入的密码错误！');
                }

               	if($basefun->getsession('vscode') != $scode){
          	    	$basefun->Js_msg('认证码有误，请重新输入！');
               	}

				if(md5($password) == $loginok['password']){
                    //统计登陆次数
                    $count = $user_log->D->get_count(array('uid'=>$loginok['uid']));
                    $user_info = $user->D->get_one_by_field(array('uid'=>$loginok['uid']));
                    if($user_info['fstcreate'] == $user_info['lastmodify'] && $count == 0){
                        //第一次登陆
                        $tag_erp_projectURL = $basefun->check_local();
    					$basefun->setsession('logined', $tag_erp_projectURL);
    					$basefun->setsession('uid', $loginok['uid']);
    					$basefun->setsession('username', $loginok['username']);
    					$basefun->setsession('eng_name', $loginok['eng_name']);
    					$basefun->setsession('chi_name', $loginok['chi_name']);
    					$basefun->setsession('admin_level', $loginok['level']);
    					$basefun->setsession('groupid', $loginok['groupid']);
    					$basefun->setsession('rid', $loginok['roleid']);

                        //插入登陆日志
                        $user_log->D->insert(array('uid'=>$loginok['uid'],'ip_address'=>$user_ip,'login_date'=>date('Y-m-d H:i:s')));
                        /*更新统计表*/
    				    $check_timeout->update_online();
                        echo "<script>alert('欢迎第一次来到ERP系统，为了您的账号安全，请修改密码!');location.href='index.php?action=login&detail=change_psw';</script>";
                    //新用户非第一次登陆，但是未修改密码。则锁定
                    }elseif(($user_info['fstcreate'] == $user_info['lastmodify']) && $count >0){
                        $r = $user->D->update_by_field(array('uid'=>$loginok['uid']),array('isuse'=>'0'));
                        if ($r){
                            $basefun->Js_msg('抱歉，您的账号已被冻结，请联系管理员开启！');
                            die();
                        }
                    }else{
                        //判断用户两个月内是否修改密码
                        date_default_timezone_set('PRC'); //设置中国时区
                        $user_info = $user->D->get_one_by_field(array('uid'=>$loginok['uid']),'lastmodify');
                        $time = strtotime(date('Y-m-d H:i:s')) - strtotime($user_info['lastmodify']);
                        //die($time);
                        //如果大于60天，锁定用户
                        if($time > 90*24*60*60){
                            $r = $user->D->update_by_field(array('uid'=>$loginok['uid']),array('isuse'=>'0'));
                            if ($r){
                                $basefun->Js_msg('您的账号已被冻结，请联系管理员开启！');
                                die();
                            }
                        //前10天给修改密码消提示
                        }elseif($time > 4320000 && $time < 5184000){
                            if($time > 4320000 && $time < 4406400){
                                $days = 10;
                            }elseif($time > 4406400 && $time < 4492800){
                                $days = 9;
                            }elseif($time > 4492800 && $time < 4579200){
                                $days = 8;
                            }elseif($time > 4579200 && $time < 4665600){
                                $days = 7;
                            }elseif($time > 4665600 && $time < 4752000){
                                $days = 6;
                            }elseif($time > 4752000 && $time < 4838400){
                                $days = 5;
                            }elseif($time > 4838400 && $time < 4924800){
                                $days = 4;
                            }elseif($time > 4924800 && $time < 5011200){
                                $days = 3;
                            }elseif($time > 5011200 && $time < 5097600){
                                $days = 2;
                            }else{
                                $days = 1;
                            }
                            $friend_message  = $this->S->dao('friend_message');//实例化通知表
                            $friend_message->insert_one_leave_message($loginok['uid'], '', 'System', '请您尽快修改密码，否则您的账号将于(<span style="color:red;">'.$days.'天后</span>)锁定！谢谢您的配合。', 0, date('Y-m-d H:i:s'), '', date('Y-m-d H:i:s'));
                            
                            $basefun->setsession('days', $days);
                        }

    					$tag_erp_projectURL = $basefun->check_local();
    					$basefun->setsession('logined', $tag_erp_projectURL);
    					$basefun->setsession('uid', $loginok['uid']);
    					$basefun->setsession('username', $loginok['username']);
    					$basefun->setsession('eng_name', $loginok['eng_name']);
    					$basefun->setsession('chi_name', $loginok['chi_name']);
    					$basefun->setsession('admin_level', $loginok['level']);
    					$basefun->setsession('groupid', $loginok['groupid']);
    					$basefun->setsession('rid', $loginok['roleid']);

                        //插入登陆日志
                        $user_log->D->insert(array('uid'=>$loginok['uid'],'ip_address'=>$user_ip,'login_date'=>date('Y-m-d H:i:s')));
                        /*更新统计表*/
    				    $check_timeout->update_online();
    				    $basefun->redirect('index.php');
                    }
				}
		}
	}


/*退出*/
elseif($detail == 'logout'){

	$this->S->dao('timeout')->D->delete_by_field(array('uid'=>$_SESSION['uid']));
	$basefun = $this->getLibrary('basefuns');
	$k = array_keys($_SESSION);
	for($i = 0; $i < count($k); $i++){
		$basefun->unsetsession($k[$i]);
	}
	$this->C->redirect('login.php');
}



/*修改密码页面*/
elseif($detail == 'change_psw'){
        
        $detail =  isset($frame)? 'change_psw&frame='.$frame:'change_psw';
	$this->V->mark(array('title'=>'修改密码','detail'=>$detail,'frame'=>$frame));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/changepsw');
	display();
}

/*检测旧密码-用于提示*/
elseif ($detail == 'checkold'){
	$basefun = $this->getLibrary('basefuns');
	$data = $this->S->dao('user')->D->get_one_by_field(array('uid'=>$basefun->getsession('uid')),'password');
	if(md5($val) == $data['password']) {
		echo "<img src='./staticment/images/onCorrect.gif' border='0'>正确<input type='hidden' name='ismit' value='1'>";
	}else{
		echo "<img src='./staticment/images/onError.gif' border='0'>原始密码错误";
	}
}

/*正式修改*/
elseif($detail == 'changemod'){
	$basefun = $this->getLibrary('basefuns');
	/*
    if(!$ismit){exit('原始密码错误！');}
	elseif($newpsw!=$conpsw){exit('两次密码不一致！');}
	else{
        date_default_timezone_set('PRC'); //设置中国时区
		$sid = $this->S->dao('user')->D->update_by_field(array('uid'=>$basefun->getsession('uid')),array('password'=>md5($conpsw),'lastmodify'=>date('Y-m-d H:i:s')));
		if($sid){exit('修改成功，可重新输入原始密码验证！');}
	}
    */

    $newpsw = $_GET['newpsw'];//新密码
    $conpsw = $_GET['conpsw'];//确认密码
    $oldpsw = $_GET['oldpsw'];//原始密码
    $frame = $_GET['frame'];//区分登录时修改密码 和登录之后修改密码功能
    if($newpsw!=$conpsw){
        echo "<script>alert('两次密码输入不一致');history.go(-1);</script>";
    }else{
        if (($oldpsw == $conpsw) || ($oldpsw == $newpsw)){$basefun->Js_msg('原始密码与新密码不能相同！');}
        date_default_timezone_set('PRC'); //设置中国时区
		$sid = $this->S->dao('user')->D->update_by_field(array('uid'=>$basefun->getsession('uid')),array('password'=>md5($conpsw),'lastmodify'=>date('Y-m-d H:i:s')));
        if($sid){
            echo "<script>alert('密码修改成功');</script>";
            $basefun->unsetsession('days');
            if($frame){
                $detail = 'change_psw&frame='.$frame;
                $this->V->mark(array('title'=>'修改密码','detail'=>$detail,'frame'=>$frame));
                $this->V->set_tpl('admintag/tag_header','F');
                $this->V->set_tpl('admintag/tag_footer','L');
                $this->V->set_tpl('adminweb/changepsw');
                display();
            }else{
                $basefun->redirect('index.php');
            } 
        }
    }

}

/*总站顶部*/
elseif($detail == 'maintop'){

	$backcount = $this->S->dao('timeout')->D->get_count();

	$markarr = array(
		'chi_name'=>$_SESSION['chi_name'],
		'eng_name'=>$_SESSION['eng_name'],
		'countonline'=>$backcount,

	);

	$this->V->mark($markarr);
	$this->V->set_tpl('adminweb/maintop');
	display();
}

/*总站实体内容*/
elseif($detail == 'mainframe'){
	$this->V->set_tpl('adminweb/mainframe');
	display();
}

/*刷新在线人数*/
elseif($detail == 'refresh_online'){

	$this->S->dao('timeout')->update_online();
	echo $this->S->dao('timeout')->D->get_count();
}

?>

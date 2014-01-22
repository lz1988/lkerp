<?php
/**
 * @title 用户提醒信息转接
 * @author Jerry
 * @create on 2013-03-22
 */ 
 
 if ($detail == 'list') {
    $stypemu = array(
        'eng_name-s-l'=>'英文名：',
        'id-a-e'=>'部门：',
        
    );
 
    /*生成按部门搜索*/
	$backdatagroup			= $this->S->dao('admin_group')->D->get_allstr('','','','id,groupname');
   	$idarr	= array(''=>'=请选择=');
	for($i = 0; $i < count($backdatagroup); $i++){
		$idarr[$backdatagroup[$i]['id']] = $backdatagroup[$i]['groupname'];
	}
    
    $user_send  = $this->S->dao('user_send');
    $sqlstr     = str_replace('id','admin_group.id',$sqlstr);
    $sqlstr     = str_replace('eng_name','u.eng_name',$sqlstr);
    $datalist   = $user_send->get_user_send($sqlstr);
    
    $dispalyarr = array();
    $tablewidth = '800';
    
    $displayarr['eng_name']     = array('showname'=>'英文名','width'=>'80');
    $displayarr['groupname']    = array('showname'=>'部门','width'=>'80');
    $displayarr['send_name']    = array('showname'=>'转接人','width'=>'80');
    $displayarr['delete']       = array('showname'=>'操作','url'=>'index.php?action=user_send&detail=del&id={id}','ajax'=>'1');
    $bannerstr = '<button onclick=window.location="index.php?action=user_send&detail=add">添加转接</button>';
    $this->V->mark(array('title'=>'转接设置'));
    $temp = 'pub_list';
    
 }

 elseif ($detail == 'del') {
    if(!$this->C->service('admin_access')->checkResRight('d_user_send')){$this->C->ajaxmsg(0);}
    $user_send  = $this->S->dao('user_send');
    $result     = $user_send->D->delete_by_field(array('id'=>$id));
    if ($result){$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg(0,'操作失败');}   
 }
 
 /*获取用户列表*/
 elseif ($detail == 'getuser'){

    $eng_name = trim($_GET["q"]);
    $user = $this->S->dao('user');
    $data = $user->D->get_allstr(' and eng_name like "%'.$eng_name.'%"','','uid','uid,eng_name');
    $result = array();
   	foreach ($data as $val) {
        $result[] = array(
            'uid'       => $val['uid'],
            'eng_name'  => $val['eng_name']
        );
    }

    echo json_encode($result);
 }
 
 /*提醒转接设置*/
 elseif ($detail == 'add'){
    
    $this->V->view['title'] = '新增转接人-转接设置(list)';
    $jump = "index.php?action=user_send&detail=addmod";
    $conform    = array('method'=>'post','action'=>$jump,'width'=>'490');
    $colwidth   = array('1'=>'100','2'=>'220','3'=>'80');
    
    $disinputarr = array();
    $disinputarr['uid']         = array('用户uid','datatype'=>'h');
    $disinputarr['sendid']      = array('转接人uid','datatype'=>'h');
    $disinputarr['eng_name']    = array('showname'=>'用户名','extra'=>'*');
    $disinputarr['send_name']   = array('showname'=>'转接人','extra'=>'*');
    $temp = 'pub_edit';
    
    $jslink = "<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />\n";
    $jslink .= "<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
    $jslink .= "<script src='./staticment/js/user_send.js?version=".time()."'></script>\n";
    
 }
 
 elseif ($detail == 'addmod') {
    if(!$this->C->service('admin_access')->checkResRight('add_user_send')){$this->C->sendmsg();}
    
    if (empty($uid)){$this->C->sendmsg("用户不可为空！");}
    if (empty($sendid)){$this->C->sendmsg("转接人不可为空！");}
    if ($sendid == $uid){$this->C->sendmsg('相同用户不可操作！');}
    
    $user_send  = $this->S->dao('user_send');
    $_rid       = $user_send->D->get_one_by_field(array('uid'=>$uid,'senduid'=>$sendid));
    if ($_rid) {$this->C->sendmsg("此用户已设置转接人！");}
    $result     = $user_send->D->insert(array('uid'=>$uid,'senduid'=>$sendid));
    if ($result) {$this->C->success('添加转接人成功！','index.php?action=user_send&detail=list');}else{$this->C->success('操作失败！','index.php?action=user_send&detail=add');}
 }
 
 /*备货提醒[计划任务执行]*/
 elseif ($detail == 'call'){
    $global = $this->C->service('global');
    $global->buytimenotice();

 }
 
 /*到货提醒备货，采购[计划任务执行]*/
 elseif ($detail == 'come'){
    $global = $this->C->service('global');
    $global->comeproductnotice();
 }
 
 if ($detail == 'list' || $detail == 'add') {
    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>
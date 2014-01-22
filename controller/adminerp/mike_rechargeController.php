<?php 
/**
* Create on 2013-05-09
* by color
* @title ...
*/

/*数据列表*/
if($detail == 'list'){
    $InitPHP_conf['pageval'] = 20;
    $datalist = $this->S->dao('mike_recharge')->D->get_list($sqlstr);
    //获取货贷充值金额总和
    $rechargelist = $this->S->dao('mike_recharge')->D->select('sum(price) as price','1'); 
    
    $displayarr = array();
    $tablewidth = '900';
    //$displayarr['id'] = array('showname'=>'编号','width'=>'100');
    $displayarr['both'] = array('showname'=>'操作','width'=>'60','url_e'=>'index.php?action=mike_recharge&detail=edit&id={id}','url_d'=>'index.php?action=mike_recharge&detail=dele&id={id}','ajax'=>'1');
    $displayarr['price'] = array('showname'=>'金额','width'=>'100');
    $displayarr['cdate'] = array('showname'=>'创建时间','width'=>'100');
    $displayarr['comment'] = array('showname'=>'备注','width'=>'100');
    $displayarr['uname'] = array('showname'=>'操作人','width'=>'100');
    

    $bannerstr = '<button onclick="window.location=\'index.php?action=mike_recharge&detail=add\'">添加记录</button>';
    $bannerstr .='&nbsp;&nbsp;<font color="red">金额总计：'.(double)$rechargelist['price'].'</font>';
    $this->V->mark(array('title'=>'货代充值列表'));

    $temp = 'pub_list';

}

/*新增或编辑页面*/
elseif($detail == 'add' || $detail == 'edit'){
	if($detail == 'edit'){
        if(!$this->C->service('admin_access')->checkResRight('mike_recharge_edit')){$this->C->sendmsg();}//权限判断
		if(empty($id))exit('缺少标识参数！');
		$backdata = $this->S->dao('mike_recharge')->D->get_one(array('id'=>$id),'*');
		$showtitle = '编辑';
		$modurl = 'modedit';
	}elseif($detail == 'add'){
        if(!$this->C->service('admin_access')->checkResRight('mike_recharge_add')){$this->C->sendmsg();}//权限判断
		$showtitle = '新增';
		$modurl = 'modadd';
	}
	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=mike_recharge&detail='.$modurl,'width'=>'500');
	$colwidth= array('1'=>'100','2'=>'300','3'=>'100');
	
	$disinputarr = array();
	$disinputarr['id'] = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['price'] = array('showname'=>'金额','value'=>$backdata['price'],'inextra'=>'class="check_notnull Check_isnum Check_isnum_dda"');
        $disinputarr['comment'] = array('showname'=>'备注','value'=>$backdata['comment']);
	$this->V->view['title'] = $showtitle.'-货代充值列表(list)';
	$temp = 'pub_edit';
}
/*保存添加*/
elseif($detail == 'modadd'){
    
	$sid = $this->S->dao('mike_recharge')->D->insert(array('cdate'=>date('Y-m-d H:i:s', time()),'price'=>$price,'uname'=>$_SESSION['eng_name'],'comment'=>$comment));
	if($sid){
 		$this->C->success('保存成功','index.php?action=mike_recharge&detail=list');
	}else{
		$this->C->success('保存失败','index.php?action=mike_recharge&detail=add');
	}
}

/*保存编辑*/
elseif($detail == 'modedit'){
	$sid = $this->S->dao('mike_recharge')->D->update(array('id'=>$id),array('cdate'=>date('Y-m-d H:i:s', time()),'price'=>$price,'uname'=>$_SESSION['eng_name'],'comment'=>$comment));
	if($sid){
 		$this->C->success('保存成功','index.php?action=mike_recharge&detail=list');
	}else{
		$this->C->success('保存失败','index.php?action=mike_recharge&detail=edit&id='.$id);
	}
}

/*AJAX删除*/
elseif($detail == 'dele'){
	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('mike_recharge_del')) $this->C->ajaxmsg(0);
	if($this->S->dao('mike_recharge')->D->delete(array('id'=>$id))) {$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg('删除失败！');}
}


/*头尾模板包含*/
if($detail == 'list' || $detail == 'edit' || $detail == 'add' ){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>
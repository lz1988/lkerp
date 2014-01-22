<?
if($detail == 'list'){

	$datalist = $this->S->dao('exchange_rate')->D->get_all(array('isnew'=>'1'),'code','asc');

	foreach ($datalist as &$val){
		$val['c_name'] = '<a href=index.php?action=exchange_rate&detail=check_history&code='.$val['code'].' title=查看历史汇率>'.$val['c_name'].'</a>';
		$val['rate'] = '<span class="wall_click" title="点击编辑" style="display: inline;">'.$val['rate'].'</span><input class="wall_edit" type="text" style="width: 100px; display: none;" rate_id="'.$val['id'].'"  detail="editrate" />';
		$val['rate_cny'] = '<span class="wall_click" title="点击编辑" style="display: inline;">'.$val['rate_cny'].'</span><input class="wall_edit" type="text" style="width: 100px; display: none;" rate_id="'.$val['id'].'"  detail="editrate_cny" />';
	}

	$displayarr = array();
	$displayarr['c_name'] 		= array('showname'=>'币别名称');
	$displayarr['code'] 		= array('showname'=>'币别代号');
	$displayarr['rate'] 		= array('showname'=>'100美金对其汇率');
	$displayarr['rate_cny'] 	= array('showname'=>'100人民币对其汇率');
	$displayarr['stage_rate']	= array('showname'=>'期号');
	$displayarr['uptime'] 		= array('showname'=>'更新日期');
	//$displayarr['delete']  		= array('showname'=>'操作','url'=>'index.php?action=exchange_rate&detail=delete&id={id}','ajax'=>1,'width'=>'50');

	$bannerstr = "<input type='button' value='添加币种' onclick=window.location='index.php?action=exchange_rate&amp;detail=new'>";
	$this->V->mark(array('title'=>'汇率调整'));

	$jslink = "<script src='./staticment/js/exchangeRate.js'></script>\n";
	$temp = 'pub_list';
}

/*查看历史汇率*/
elseif($detail =='check_history'){

	$datalist = $this->S->dao('exchange_rate')->D->get_list(' and code="'.$code.'"','','isnew desc,id desc','code,rate,rate_cny,stage_rate,isnew,uptime');
	$displayarr = array();
	$displayarr['code'] 		= array('showname'=>'币别代号');
	$displayarr['rate'] 		= array('showname'=>'100美金对其汇率');
	$displayarr['rate_cny']		= array('showname'=>'100人民币对其汇率');
	$displayarr['stage_rate']	= array('showname'=>'期号');
	$displayarr['isnew']		= array('showname'=>'当前使用','title'=>'1为当前使用的,如果有两个为1，表示不正常');
	$displayarr['uptime'] 		= array('showname'=>'更新日期');

	$bannerstr = '<button onclick=history.go(-1)>返回</button>';
	$this->V->mark(array('title'=>$code.'历史汇率-汇率调整(list)'));
	$temp = 'pub_list';
}

/*新增币种页面*/
elseif($detail == 'new'){
	if(!$this->C->service('admin_access')->checkResRight('exchangerate_add')){$this->C->sendmsg();}

	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=exchange_rate&detail=newmod','width'=>'600','extra'=>'onsubmit=\'return&nbsp;countmod()\' id=countform');
	$colwidth = array('1'=>'30%','2'=>'50%','3'=>'20%');

	/*生成表单内容*/
	$disinputarr = array();
	$disinputarr['c_name'] = array('showname'=>'币种名称');
	$disinputarr['code'] = array('showname'=>'币种代号');
	$disinputarr['rate'] = array('showname'=>'100美金对其汇率','showtips'=>'<span class=tips>*最多4位小数</span>');
	$disinputarr['rate_cny'] = array('showname'=>'100人民币对其汇率','showtips'=>'<span class=tips>*最多4位小数</span>');

	$this->V->view['title'] = '添加币种-汇率调整(list)';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_edit';
}


/*新增币种*/
elseif($detail == 'newmod'){
	if(!$this->C->service('admin_access')->checkResRight('exchangerate_add')){$this->C->sendmsg();}
	$uptime = date('Y-m-d');
	$sid = $this->S->dao('exchange_rate')->D->insert(array('c_name'=>$c_name,'code'=>$code,'rate'=>$rate,'rate_cny'=>$rate_cny,'stage_rate'=>date('Y.m',time()),'uptime'=>$uptime));
	if($sid){$this->C->success('添加成功','index.php?action=exchange_rate&detail=list');}
}


/*更改美金汇率*/
elseif($detail == 'editrate'){
	if(!$this->C->service('admin_access')->checkResRight('exchangerate_edit')){exit('对不起，你没有该权限');}

	$uptime 	= date('Y-m-d',time());
	$stage_rate = date('Y.m',time());

	$exchange 	= $this->S->dao('exchange_rate');

	/*开始事务，将原期isnew置0，重新插入一条新汇率*/
	$exchange->D->query('begin');
	$res = $exchange->get_by_id($id);
	if ($res['stage_rate'] == date('Y.m',time())) {
		$uid = $exchange->D->update_by_field(array('id'=>$id),array('rate'=>$rate));
		$sid = 1;
	}
	else {
		$uid = $exchange->D->update_by_field(array('id'=>$id),array('isnew'=>'0'));
		$backdata = $exchange->D->get_one_by_field(array('id'=>$id),'c_name,code,rate_cny');
		$sid = $exchange->D->insert(array('c_name'=>$backdata['c_name'],'code'=>$backdata['code'],'rate'=>$rate,'rate_cny'=>$backdata['rate_cny'],'stage_rate'=>$stage_rate,'uptime'=>$uptime,));
	}
	if($sid && $uid){$exchange->D->query('commit');echo '1';}else{$exchange->D->query('rollback');echo '更新失败';}

}
/*更改人民币汇率*/
elseif($detail == 'editrate_cny'){
	if(!$this->C->service('admin_access')->checkResRight('exchangerate_edit')){exit('对不起，你没有该权限');}

	$uptime 	= date('Y-m-d',time());
	$stage_rate = date('Y.m',time());

	$exchange 	= $this->S->dao('exchange_rate');

	/*开始事务，将原期isnew置0，重新插入一条新汇率*/
	$exchange->D->query('begin');
	$res = $exchange->get_by_id($id);
	if ($res['stage_rate'] == date('Y.m',time())) {
		$uid = $exchange->D->update_by_field(array('id'=>$id),array('rate_cny'=>$rate));
		$sid = 1;
	}
	else {
		$uid = $exchange->D->update_by_field(array('id'=>$id),array('isnew'=>'0'));
		$backdata = $exchange->D->get_one_by_field(array('id'=>$id),'c_name,code,rate');
		$sid = $exchange->D->insert(array('c_name'=>$backdata['c_name'],'code'=>$backdata['code'],'rate'=>$backdata['rate'],'rate_cny'=>$rate,'stage_rate'=>$stage_rate,'uptime'=>$uptime,));
	}
	if($sid && $uid){$exchange->D->query('commit');echo '1';}else{$exchange->D->query('rollback');echo '更新失败';}

}

/*根据币别获得汇率*/
elseif($detail == 'getrate'){
	$backdata = $this->S->dao('exchange_rate')->D->get_one_by_field(array('code'=>$code,'isnew'=>1),$type);
	echo $backdata["$type"];
}

if($detail == 'list' || $detail == 'check_history'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>
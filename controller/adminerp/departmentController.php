<?php
/*
 * create by wall on 2012-03-27 
 * */
$type_name = array(	4 => '部门','职员');

/* create by wall on 2012-03-27
 * 部门，职员列表模块*/
if ($detail == 'list') {
	$stypemu = array(
		'typeid-h-e'=>'',
 		'esseid-s-l'=>'代码：',
 		'name-s-l'=>'名称：',
 	);
	if (empty($sqlstr)) {
		$sqlstr = ' and type='.$typeid.' ';
	} 
	$sqlstr = str_replace('typeid', 'type', $sqlstr);
	$datalist = $this->S->dao('esse')->D->get_list($sqlstr,'','esseid desc','id,esseid,name');	
	$tablewidth = '500';
	$displayarr = array();
	$displayarr['esseid'] = array('showname'=>$type_name[$typeid].'代码','width'=>'100');
	$displayarr['name'] = array('showname'=>$type_name[$typeid].'名称');
	$displayarr['both'] = array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=department&detail=delete&id={id}','url_e'=>'index.php?action=department&detail=edit&typeid='.$typeid.'&id={id}');

	$bannerstr = '<button onclick=window.location="index.php?action=department&detail=add&typeid='.$typeid.'">添加'.$type_name[$typeid].'</button>';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
	$this->V->mark(array('title'=>$type_name[$typeid].'管理'));
}
/*删除*/
elseif($detail == 'delete'){	
	if($id){if($this->S->dao('esse')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}
elseif ($detail == 'add' || $detail == 'edit') {	
	if($detail == 'edit'){
		if(empty($id))exit('没有ID!');
		$esse  = $this->S->dao('esse');
		$data = $esse->D->select('name,comment','id='.$id);		
		$this->V->mark(array('title'=>'编辑'.$type_name[$typeid]));
		$jump = 'index.php?action=department&detail=editmod&typeid='.$typeid;
	}elseif($detail == 'add'){
		$this->V->mark(array('title'=>'增加'.$type_name[$typeid]));
		$jump = 'index.php?action=department&detail=addmod&typeid='.$typeid;
	}
	
	/*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'80');
	
	$disinputarr = array();
	$disinputarr['id']		= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['name'] 	= array('showname'=>$type_name[$typeid].'名称', 'value'=>$data['name']);
	$disinputarr['comment'] = array('showname'=>'备注', 'value'=>$data['comment']);

	
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_edit';
}
elseif ($detail == 'addmod') {
	date_default_timezone_set('Etc/GMT-8');//北京时间

	//取得最大ID+1，实体编码递增
	$esse  = $this->S->dao('esse');
	$maxid = $esse->D->select('max(esseid) as max','type='.$typeid);
	$esseid = $maxid['max']+1;

	$sid = $esse->D->insert(array('name'=>$name,'cuser'=>$_SESSION['eng_name'],'type'=>$typeid,'esseid'=>$esseid,'extends'=>$extends,'comment'=>$comment,'cdate'=>date('Y-m-d',time())));
	if($sid) $this->C->success('添加成功','index.php?action=department&detail=list&typeid='.$typeid);
}
elseif ($detail == 'editmod') {
	$sid = $this->S->dao('esse')->D->update_by_field(array('id'=>$id),array('name'=>$name,'comment'=>$comment));
	if($sid) $this->C->success('修改成功','index.php?action=department&detail=list&typeid='.$typeid);
}
?>
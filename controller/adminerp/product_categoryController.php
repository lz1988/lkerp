<?php
$category = $this->S->dao('category');

/*类别列表*/
if($detail == 'list'){

	$stypemu = array(
		'cat_name-s-l'=>'类别名称：',
		'keywords-s-l'=>'关键字：',
	);

	$InitPHP_conf['pageval'] = 20;

	if($sqlstr){
		$sqlstr = strtr($sqlstr,array('cat_name'=>'c.cat_name','keywords'=>'c.keywords'));
	}
	$datalist = $category->category_list($sqlstr);

	foreach($datalist as &$val){
		$val['fname'] = empty($val['fname'])?'<font color=#c6a8c6>- -</font>':$val['fname'];

		$val['moddo'] = '<a href=index.php?action=product_category&detail=editcat&cat_id='.$val['cat_id'].'><img src="./staticment/images/editbody.gif" border=0"></a> ';
		$val['moddo'].= '<a href=javascript:void(0);delitem("index.php?action=product_category&detail=delcat&cat_id='.$val['cat_id'].'","确定删除类别?如果该类别下存在子类别，子类别将一并被删除!")><img src="./staticment/images/deletebody.gif" border=0"></a>';
	}

	/*定义输出数组*/
	$displayarr = array();
	$tablewidth = '100%';

	$displayarr['cat_id'] 	= array('showname'=>'类别ID','width'=>'45');
	$displayarr['fname'] 	= array('showname'=>'父类别','width'=>'100');
	$displayarr['cat_name'] = array('showname'=>'类别名称','width'=>'100');
	$displayarr['keywords'] = array('showname'=>'关键字','width'=>'120');
	$displayarr['cat_desc'] = array('showname'=>'描述','width'=>'100');
	$displayarr['moddo']	= array('showname'=>'操作','width'=>'50');

	$bannerstr = '<button onclick="window.location=\'index.php?action=product_category&detail=add\'">添加类别</button>';
	$this->V->mark(array('title'=>'类别列表'));
	$temp = 'pub_list';
}


/*保存新增的类别*/
elseif($detail == 'addmod'){
	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_c_add')){$this->C->sendmsg();}
	$sid = $category->D->insert(array('cat_name'=>$cat_name,'keywords'=>$keywords,'cat_desc'=>$cat_desc,'parent_id'=>$parent_id));

	if($parent_id){
		$category->D->update_by_field(array('cat_id'=>$sid),array('sort_order'=>$parent_id.'01'));
	}else{
		$category->D->update_by_field(array('cat_id'=>$sid),array('sort_order'=>$sid.'00'));
	}

	if($sid) $this->C->success('添加成功','index.php?action=product_category&detail=list');
}

/*保存编辑*/
elseif($detail == 'editcatmod'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_c_edit')){$this->C->sendmsg();}
	if($parent_id){
		$sid = $category->D->update_by_field(array('cat_id'=>$cat_id),array('cat_name'=>$cat_name,'keywords'=>$keywords,'cat_desc'=>$cat_desc,'parent_id'=>$parent_id,'sort_order'=>$parent_id.'01'));
	}else{
		$sid = $category->D->update_by_field(array('cat_id'=>$cat_id),array('cat_name'=>$cat_name,'keywords'=>$keywords,'cat_desc'=>$cat_desc,'parent_id'=>$parent_id,'sort_order'=>$cat_id.'00'));
	}
	if($sid) $this->C->success('保存成功','index.php?action=product_category&detail=list');
}

/*删除类别--需要权限*/
elseif($detail == 'delcat'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_c_del')){$this->C->ajaxmsg(0);}
	$sid = $category->D->delete_sql('where cat_id='.$cat_id.' or parent_id='.$cat_id);

	if($sid) {$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg(0,'删除失败');};

}

/*编辑类别*/
elseif($detail == 'editcat' || $detail == 'add'){

	if($detail == 'editcat'){
	   
		$backdata = $category->D->get_one_by_field(array('cat_id'=>$cat_id),'cat_name,keywords,cat_desc,parent_id');
        if(!$this->C->service('admin_access')->checkResRight('r_c_edit')){$this->C->sendmsg();}
        
	}else{
	   if(!$this->C->service('admin_access')->checkResRight('r_c_add')){$this->C->sendmsg();}
	}

	/*用于选择类别归属的上一级类别，所以这里取有效的根类别*/
	$categorylist = $category->D->get_all(array('is_active'=>1,'parent_id'=>0),'','','cat_id,cat_name');
	$parent_id_datastr = '<select name=parent_id><option value=>== 选择父类别 ==</option>';

	/*编辑顶级类别不可再选择上级类别*/
	if($detail == 'add' || $detail == 'editcat' && !empty($backdata['parent_id'])){
		foreach ($categorylist as $val){
			$cat_selected = $backdata['parent_id'] == $val['cat_id']?'selected':'';
			$parent_id_datastr.= '<option value='.$val['cat_id'].' '.$cat_selected.'>'.$val['cat_name'].'</option>';
		}
	}
	$parent_id_datastr.= '</select>';


	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=product_category&detail='.$detail.'mod','width'=>'700');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'300');

	$disinputarr = array();
	$disinputarr['cat_id'] = array('showname'=>'类别ID','value'=>$cat_id,'datatype'=>'h');
	$disinputarr['cat_name']	= array('showname'=>'类别名称','value'=>$backdata['cat_name']);
	$disinputarr['keywords']	= array('showname'=>'关键字','value'=>$backdata['keywords']);
	$disinputarr['cat_desc']	= array('showname'=>'描述','value'=>$backdata['cat_desc']);
	$disinputarr['parent_id']	= array('showname'=>'父类别','datastr'=>$parent_id_datastr,'datatype'=>'se','showtips'=>' 不选则表示将此类别设为一级类别');

	$this->V->mark(array('title'=>'类别管理-类别列表(list)'));
	$temp = 'pub_edit';
}

/*批更新类别的排序*/
elseif($detail == 'checkmod'){

	$datalist = $category->category_list($sqlstr);
	foreach($datalist as $val){
		if($val['fname'] == ''){
			$category->D->update_by_field(array('cat_id'=>$val['cat_id']),array('sort_order'=>$val['cat_id'].'00'));
		}
		else{
			$category->D->update_by_field(array('cat_id'=>$val['cat_id']),array('sort_order'=>$val['parent_id'].'01'));
		}
	}

	echo 'success';
}

if($detail == 'editcat' || $detail == 'list' || $detail == 'add'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

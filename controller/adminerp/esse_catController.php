<?php
/*
 * Created on 2012-3-23
 *
 * @title 项目类别管理
 * @author by hanson
 */

 /*项目类别列表*/
 if($detail == 'list'){

	$datalist = $this->S->dao('esse_cat')->D->get_allstr('','','cat_code asc');

	$cat_jump_title = 'title=点击进入查看';
	/*跳转处理*/
	foreach($datalist as &$val){

		switch ($val['id']){
			case 1 :$val['cat_name'] = '<a href=index.php?action=customer&detail=list '.$cat_jump_title.'>+'.$val['cat_name'].'</a>';break;/*客户*/
			case 2 :$val['cat_name'] = '<a href=index.php?action=warehouse&detail=list '.$cat_jump_title.'>+'.$val['cat_name'].'</a>';break;/*仓库*/
			case 3 :$val['cat_name'] = '<a href=index.php?action=supplier&detail=list '.$cat_jump_title.'>+'.$val['cat_name'].'</a>';break;/*供应商*/
			case 4 :$val['cat_name'] = '<a href=index.php?action=department&detail=list&typeid=4 '.$cat_jump_title.'>+'.$val['cat_name'].'</a>';break;/*部门*/
			case 5 :$val['cat_name'] = '<a href=index.php?action=department&detail=list&typeid=5 '.$cat_jump_title.'>+'.$val['cat_name'].'</a>';break;/*职员*/
			case 6 :$val['cat_name'] = '<a href=index.php?action=product_list&detail=list '.$cat_jump_title.'>+'.$val['cat_name'].'</a>';break;/*物料*/
		}
	}

	$tablewidth = '500';
	$displayarr = array();
	$displayarr['cat_code'] = array('showname'=>'项目代码','width'=>'100');
	$displayarr['cat_name'] = array('showname'=>'项目名称');
	$displayarr['edit'] 	= array('showname'=>'操作','url'=>'index.php?action=esse_cat&detail=edit_esse&id={id}','width'=>'50');

	$bannerstr = '<button onclick=window.location="index.php?action=esse_cat&detail=add_esse">添加项目</button>';
	$temp = 'pub_list';
	$this->V->mark(array('title'=>'项目类别'));
 }

/*编辑项目*/
elseif($detail == 'edit_esse' || $detail =='add_esse'){

	if($id) {
		$backdata = $this->S->dao('esse_cat')->D->get_one_by_field(array('id'=>$id),'cat_code,cat_name');
		$jump_detail = 'mod_edit_esse';
		$show_title	 = '编辑项目类别';
	}else{
		$jump_detail = 'mod_add_esse';
		$show_title	 = '添加项目类别';
	}
	$conform = array('method'=>'post','action'=>'index.php?action=esse_cat&detail='.$jump_detail,'width'=>'500px');

	$disinputarr = array();
	$disinputarr['id'] = array('showname'=>'id','datatype'=>'h','value'=>$id);
	$disinputarr['cat_code'] = array('showname'=>'类别代码','value'=>$backdata['cat_code']);
	$disinputarr['cat_name'] = array('showname'=>'类别名称','value'=>$backdata['cat_name']);

	$temp = 'pub_edit';
	$this->V->mark(array('title'=>$show_title));

}

/*保存-添加*/
elseif($detail == 'mod_add_esse'){
	$sid = $this->S->dao('esse_cat')->D->insert(array('cat_code'=>$cat_code,'cat_name'=>$cat_name));
	if($sid) $this->C->success('保存成功','index.php?action=esse_cat&detail=list');
}

/*保存-编辑*/
elseif($detail == 'mod_edit_esse'){
	if($id){
		$sid = $this->S->dao('esse_cat')->D->update_by_field(array('id'=>$id),array('cat_code'=>$cat_code,'cat_name'=>$cat_name));
		if($sid) $this->C->success('保存成功','index.php?action=esse_cat&detail=list');
	}
}

 if($detail == 'list' || $detail =='edit_esse' || $detail =='add_esse'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>

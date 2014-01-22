<?php
/*
 * Created on 2012-2-7
 *
 * @title-目录管理
 * @author by hanson
 */

if($detail == 'list'){

	$InitPHP_conf['pageval'] = 30;
 	$datalist = $this->S->dao('menu')->D->get_list('','','sort_id asc','id,parent_id,name,url,cuser,sort_id');
 	foreach($datalist as &$val){
 		$val['name'] = empty($val['parent_id'])?$val['name']:'--'.$val['name'];
 	}

	$displayarr = array();
	$tablewidth = '1100';
	$displayarr['name']		 = array('showname'=>'菜单名称','width'=>'100');
	$displayarr['url'] 		 = array('showname'=>'菜单地址','width'=>'250');
	$displayarr['cuser']	 = array('showname'=>'建立者','width'=>'100');
	$displayarr['sort_id']	 = array('showname'=>'排序','width'=>'100','clickedit'=>'id','detail'=>'editsort');
	$displayarr['both']		 = array('showname'=>'操作','url_d'=>'index.php?action=menu&detail=delmenu&id={id}','url_e'=>'index.php?action=menu&detail=add&id={id}','width'=>'60','ajax'=>'1');

	$bannerstr = '<button onclick=window.location="index.php?action=menu&detail=add">添加菜单</button>';
	$bannerstr.= '<button onclick=window.location="index.php?action=menu&detail=rights_list">权限关联</button>';
 	$this->V->mark(array('title'=>'菜单管理'));
 	$temp = 'pub_list';
}

/*权限菜单关联列表*/
elseif($detail == 'rights_list'){

	$stypemu	= array('name-s-l'=>'菜单名称：');
	$sqlstr	   .= 'and r.b_cat="menu" ';
	$sqlstr		= strtr($sqlstr,array('name'=>'m.name'));
	$datalist	= $this->S->dao('rights')->get_MenuRgihts($sqlstr);

	$displayarr = array();
	$tablewidth = '1000';
	$displayarr['name']		= array('showname'=>'归属菜单','width'=>'120');
	$displayarr['code'] 	= array('showname'=>'权限代码','width'=>'100');
	$displayarr['desc']		= array('showname'=>'描述','width'=>'250');
	$displayarr['comment']	= array('showname'=>'备注','width'=>'250');
	$displayarr['edit']		= array('showname'=>'编辑','width'=>'50','url'=>'index.php?action=menu&detail=add_rightsconx&id={id}');

	$bannerstr = '<button onclick=window.location="index.php?action=menu&detail=add_rightsconx">添加关联</button>';
	$this->V->mark(array('title'=>'权限关联-菜单管理(list)'));
	$temp = 'pub_list';
}

/*保存添加权限关联*/
elseif($detail == 'rigs_modadd'){

	$sid = $this->S->dao('rights')->D->insert(array('code'=>$code,'b_id'=>$b_id,'b_cat'=>'menu','desc'=>$desc,'comment'=>$comment));
	if($sid){
		$this->C->success('添加成功','index.php?action=menu&detail=rights_list');
	}else{
		$this->C->success('添加失败','index.php?action=menu&detail=add_rightsconx');
	}
}

/*更新权限关联*/
elseif($detail == 'rigs_modedit'){

	$sid = $this->S->dao('rights')->D->update(array('id'=>$id),array('code'=>$code,'b_id'=>$b_id,'b_cat'=>'menu','desc'=>$desc,'comment'=>$comment));
	if($sid){
		$this->C->success('保存成功','index.php?action=menu&detail=rights_list');
	}else{
		$this->C->success('保存失败','index.php?action=menu&detail=add_rightsconx&id='.$id);
	}
}

/*添加或编辑菜单权限关联*/
elseif($detail == 'add_rightsconx'){

	if($id){$moddeital = 'rigs_modedit';$back_id = $this->S->dao('rights')->D->get_one(array('id'=>$id),'*');}else{$moddeital = 'rigs_modadd';}

	$bmenustr				= '<select name=b_id>';
	$bmenustrArr			= $this->S->dao('menu')->D->get_allstr(' and parent_id!="" ','','sort_id asc','id,name');
	foreach($bmenustrArr as $val){
		$isselected	 = ($val['id'] == $back_id['b_id'])?'selected':'';
		$bmenustr	.= '<option value="'.$val['id'].'" '.$isselected.'>'.$val['name'].'</option>';
	}
	$bmenustr			   .= '</select>';

	$conform 				= array('method'=>'post','action'=>'index.php?action=menu&detail='.$moddeital);
	$disinputarr 			= array();
	$disinputarr['id'] 		= array('showname'=>'修改的ID','datatype'=>'h','value'=>$id);
	$disinputarr['code'] 	= array('showname'=>'权限代码','value'=>$back_id['code']);
	$disinputarr['desc'] 	= array('showname'=>'描述','value'=>$back_id['desc']);
	$disinputarr['b_menu']	= array('showname'=>'归属菜单','datatype'=>'se','datastr'=>$bmenustr);
	$disinputarr['comment']	= array('showname'=>'备注','value'=>$back_id['comment']);

	$this->V->mark(array('title'=>'添加权限-权限关联(rights_list)-菜单管理(list)'));
	$temp = 'pub_edit';
}


 /*修改排序*/
elseif($detail == 'editsort'){
	if($this->S->dao('menu')->D->update_by_field(array('id'=>$id),array('sort_id'=>$sort_id))){echo '1';}else{echo '更新失败！';}
}

/*AJAX删除*/
elseif($detail == 'delmenu'){
	if($this->S->dao('menu')->D->delete_by_field(array('id'=>$id))) {$this->C->ajaxmsg(1);}else{echo '删除失败！';}
}


/*添加菜单与编辑填写页面*/
elseif($detail == 'add'){


	if($id){$moddeital = 'modedit';$back_id = $this->S->dao('menu')->D->get_one_by_field(array('id'=>$id),'id,name,url,parent_id,sort_id');}else{$moddeital = 'modadd';}
	$conform = array('method'=>'post','action'=>'index.php?action=menu&detail='.$moddeital);

	/*生成上级菜单*/
	$back_list = $this->S->dao('menu')->D->get_list(' and parent_id="" ','','sort_id asc','id,name');
	$parent_id_datastr = '<select name=parent_id>';
	$parent_id_datastr.= '<option value=>=选择上级菜单=</option>';
	foreach($back_list as $val){
		$selected_b 	   = $val['id'] == $back_id['parent_id']?'selected':'';
		$parent_id_datastr.= '<option value='.$val['id'].' '.$selected_b.'>'.$val['name'].'</option>';
	}
	$parent_id_datastr.= '</select>';

	$disinputarr = array();
	$disinputarr['id'] = array('showname'=>'修改的ID','datatype'=>'h','value'=>$id);
	$disinputarr['parent_id'] = array('showname'=>'上级菜单','datatype'=>'se','datastr'=>$parent_id_datastr);
	$disinputarr['name'] = array('showname'=>'菜单名称','value'=>$back_id['name']);
	$disinputarr['url'] = array('showname'=>'菜单地址','value'=>$back_id['url']);
	$disinputarr['sort_id'] = array('showname'=>'排序ID','value'=>$back_id['sort_id']);

	if($id){
		$this->V->mark(array('title'=>'编辑菜单-菜单管理(list)'));
	}else{
		$this->V->mark(array('title'=>'添加菜单-菜单管理(list)'));
	}
	$temp = 'pub_edit';
}

/*保存编辑操作*/
elseif($detail == 'modedit'){
	$sid = $this->S->dao('menu')->D->update_by_field(array('id'=>$id),array('parent_id'=>$parent_id,'name'=>$name,'url'=>$url,'sort_id'=>$sort_id));
	if($sid) $this->C->success('修改成功','index.php?action=menu&detail=list');
}


/*保存添加菜单*/
elseif($detail == 'modadd'){
	$sid = $this->S->dao('menu')->D->insert(array('parent_id'=>$parent_id,'name'=>$name,'url'=>$url,'sort_id'=>$sort_id,'cuser'=>$_SESSION['eng_name']));
	if($sid) $this->C->success('添加成功','index.php?action=menu&detail=list');
}

/*模板定义*/
if($detail =='list' || $detail == 'add' || $detail == 'rights_list' || $detail == 'add_rightsconx'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>

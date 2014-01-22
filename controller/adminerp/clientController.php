<?php 
/**
* Create on 2013-04-11
* by color
* @title mac地址
*/

/*数据列表*/
if($detail == 'list'){
//	$stypemu = array(
//		'id-s-l'=>'搜索1：',
//		'mac-s-l'=>'搜索2：',
//	);
	$datalist = $this->S->dao('client')->D->get_list($sqlstr);
        
        foreach($datalist as &$val){
            $val['type'] = $val['type'] == '0'?'仓储':'允许外部登录';
        }
       
	$displayarr = array();
	$tablewidth = '500';
	//$displayarr['id'] = array('showname'=>'id','width'=>'100');
	$displayarr['mac'] = array('showname'=>'mac地址','width'=>'100');
	//$displayarr['token'] = array('showname'=>'md5mac地址','width'=>'100');
        $displayarr['type'] = array('showname'=>'类型','width'=>'100');
	$displayarr['both'] = array('showname'=>'操作','width'=>'60','url_e'=>'index.php?action=client&detail=edit&id={id}','url_d'=>'index.php?action=client&detail=dele&id={id}','ajax'=>'1');

        
	$bannerstr = '<button onclick="window.location=\'index.php?action=client&detail=add\'">添加记录</button>';
	//$bannerstr.= '<button>buttonTwo</button>';
	$this->V->mark(array('title'=>'列表'));

	$temp = 'pub_list';

}

/*新增或编辑页面*/
elseif($detail == 'add' || $detail == 'edit'){
	//if(!$this->C->service('admin_access')->checkResRight('rights_code...')){$this->C->sendmsg();}//权限判断 
    
	if($detail == 'edit'){
		if(empty($id))exit('缺少标识参数！');
		$backdata = $this->S->dao('client')->D->get_one(array('id'=>$id),'*');
		$showtitle = '编辑';
		$modurl = 'modedit';
	}elseif($detail == 'add'){
		$showtitle = '新增';
		$modurl = 'modadd';
	} 
	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=client&detail='.$modurl,'width'=>'500');
	$colwidth= array('1'=>'100','2'=>'300','3'=>'100'); 
        $setype = array(0=>' selected = "true"',1=>' selected = "true"');
	$typestr = '<select name="type">';
        $typestr .= '<option value="0" >仓储</option>';
        $typestr .= $backdata['type']==1?'<option value="1" selected = "true" >允许外部登录</option>':'<option value="1">允许外部登录</option>';
        $typestr.= '</select>'; 
	$disinputarr = array();
	$disinputarr['id'] = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['mac'] = array('showname'=>'mac地址','value'=>$backdata['mac']);
        $disinputarr['type'] = array('showname'=>'类型','datastr'=>$typestr,'datatype'=>'se');  
	//$disinputarr['token'] = array('showname'=>'编辑项2','value'=>$backdata['token']); 
	$this->V->view['title'] = $showtitle.'-列表(list)';
	$temp = 'pub_edit';
}

/*保存添加*/
elseif($detail == 'modadd'){
	$sid = $this->S->dao('client')->D->insert(array('mac'=>$mac,'token'=> md5($mac),'type'=>$type));
	if($sid){
 		$this->C->success('保存成功','index.php?action=client&detail=list');
	}else{
		$this->C->success('保存失败','index.php?action=client&detail=add');
	}
}

/*保存编辑*/
elseif($detail == 'modedit'){
	$sid = $this->S->dao('client')->D->update(array('id'=>$id),array('mac'=>$mac,'token'=>md5($mac),'type'=>$type));
	if($sid){
 		$this->C->success('保存成功','index.php?action=client&detail=list');
	}else{
		$this->C->success('保存失败','index.php?action=client&detail=edit&id='.$id);
	}
}

/*AJAX删除*/
elseif($detail == 'dele'){
	/*权限判断*/
	//if(!$this->C->service('admin_access')->checkResRight('rights_code...')) $this->C->ajaxmsg(0);
	if($this->S->dao('client')->D->delete(array('id'=>$id))) {$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg('删除失败！');}
}


/*头尾模板包含*/
if($detail == 'list' || $detail == 'edit' || $detail == 'add' ){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>
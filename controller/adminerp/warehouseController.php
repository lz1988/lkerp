<?php
/*by hanson
 *
 *@title仓库管理
 *
 * */
$esse = $this->S->dao('esse');

if($detail == 'list'){


	$datalist = $esse->D->get_all(array('type'=>2),'esseid','asc');
    foreach ($datalist as &$val) {
        $extend = json_decode($val['extends'], true);
        $val['e_address'] = $extend['e_address'];
    }
	$this->V->mark(array('title'=>'仓库管理'));

	$displayarr = array();
	$displayarr['esseid'] 	    = array('showname'=>'编码');
	$displayarr['name'] 	    = array('showname'=>'仓库名称');
    $displayarr['e_address']    = array('showname'=>'地址');
	$displayarr['cuser'] 	    = array('showname'=>'建立者');
	$displayarr['comment']      = array('showname'=>'备注');
	$displayarr['cdate'] 	    = array('showname'=>'创建日期');
    $displayarr['both']         = array('showname'=>'操作','ajax'=>1,'url_d'=>'index.php?action=warehouse&detail=delete&id={id}','url_e'=>'index.php?action=warehouse&detail=edit&id={id}');

	$bannerstr = '<button onclick="window.location=\'index.php?action=warehouse&detail=add\'">添加仓库</button>';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}

elseif($detail == 'add' || $detail == 'edit'){
	
    if ($detail == 'add') {
        if(!$this->C->service('admin_access')->checkResRight('warehouse_add')){$this->C->sendmsg();}
        $title = '增加仓库-仓库管理(list)';
        $conform = array('method'=>'post','action'=>'index.php?action=warehouse&detail=addmod','width'=>'500px');
    }
    elseif ($detail == 'edit') {
        if(!$this->C->service('admin_access')->checkResRight('warehouse_edit')){$this->C->sendmsg();}
        $title = '修改仓库-仓库管理(list)';
        $conform = array('method'=>'post','action'=>'index.php?action=warehouse&detail=editmod&id='.$id,'width'=>'500px');
        $data = $this->S->dao('esse')->D->get_one_sql('select name,comment,extends from esse where id='.$id);
        $extends = json_decode($data['extends'], true);
    }

	$disinputarr = array();
	$disinputarr['name'] 	    = array('showname'=>'仓库名称','value'=>$data['name']);
	$disinputarr['comment']     = array('showname'=>'备注','value'=>$data['comment']);
    $disinputarr['e_address']   = array('showname'=>'地址','value'=>$extends['e_address']);

	$this->V->mark(array('title'=>$title));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_edit';
}

elseif($detail == 'addmod'){
	if(!$this->C->service('admin_access')->checkResRight('warehouse_add')){$this->C->sendmsg();}
	if($name){
		$esseid = $this->C->service('global')->get_max_esseid(2);
        //扩展内容处理，如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
        $extends = array('e_address'=>$e_address);
        $extends = function_exists('get_magic_quotes_gpc')?addslashes(json_encode($extends)):json_encode($extends);
		if($esse->D->insert(array('name'=>$name,'cuser'=>$_SESSION['eng_name'],'type'=>'2','esseid'=>$esseid,'comment'=>$comment,'extends'=>$extends,'cdate'=>date('Y-m-d',time()))))
		$this->C->success('添加成功','index.php?action=warehouse&detail=list');
	}
}

elseif ($detail == 'editmod') {
    if(!$this->C->service('admin_access')->checkResRight('warehouse_edit')){$this->C->sendmsg();}
    if($id && $name){
        //扩展内容处理，如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
        $extends = array('e_address'=>$e_address);
        $extends = function_exists('get_magic_quotes_gpc')?addslashes(json_encode($extends)):json_encode($extends);
		if($esse->D->update_by_field(array('id'=>$id),array('name'=>$name,'comment'=>$comment,'extends'=>$extends)))
		$this->C->success('修改成功','index.php?action=warehouse&detail=list');
	}
}

elseif($detail == 'delete'){
	if(!$this->C->service('admin_access')->checkResRight('warehouse_del')){$this->C->ajaxmsg(0);}
	if($id){
        $eid = $this->S->dao('process')->D->get_one_sql('select * from process where provider_id='.$id.' or receiver_id='.$id);
        if ($eid) {
            $this->C->ajaxmsg(0,'仓库已使用，不能删除');
        }
        if($esse->D->delete_by_field(array('id'=>$id))) {
            $this->C->ajaxmsg(1);
        }
    }
}

elseif($detail == 'getsupplier'){
	if($name){
		//处理供应商的联想显示
		echo $this->C->service('warehouse')->show_supplier_select($name, $show_hidden_supplier_id, $showdiv);
	}
}


?>

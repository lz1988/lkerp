<?php
/*
 * create on 2012-05-15
 * by wall
 * 公告类型控制器
 * */

if ($detail == 'list') {
	//设置分页数为10
	$InitPHP_conf['pageval'] = 10;
	$datalist = $this->S->dao('announcementtype')->get_all_list('');

	foreach ($datalist as &$val) {
		$val['color'] = '<div style="background-color:'.$val['color'].';width:15px;height:15px;float:left;"></div>';
	}

	$this->V->mark(array('title'=>'公告类型管理'));

	$displayarr = array();
	$displayarr['name'] 	    = array('showname'=>'公告类型');
	$displayarr['color'] 	    = array('showname'=>'颜色');
    $displayarr['cuser']    	= array('showname'=>'建立者');
	$displayarr['cdate'] 	    = array('showname'=>'建立时间');
	$displayarr['both']         = array('showname'=>'操作','ajax'=>1,'url_d'=>'index.php?action=announcement_type&detail=delete&id={id}','url_e'=>'index.php?action=announcement_type&detail=edit&id={id}');

	$bannerstr = '<button onclick="window.location=\'index.php?action=announcement&detail=list\'">公告列表</button>';
	$bannerstr .= '<button onclick="window.location=\'index.php?action=announcement_type&detail=add\'">添加类型</button>';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}
elseif ($detail == 'delete') {
	if (!$this->C->service('admin_access')->checkResRight('r_t_annoucement')) {
		$this->C->ajaxmsg(0);
	}

	if ($this->S->dao('announcement')->get_count_by_atid($id)) {
		$this->C->ajaxmsg(0, '不能删除，有该类型公告！请先删除该类型公告！');
	}

	if ($this->S->dao('announcementtype')->D->delete_by_field(array('id'=>$id))) {
		$this->C->ajaxmsg(1);
	}
}
elseif ($detail == 'add' || $detail == 'edit') {
	if (!$this->C->service('admin_access')->checkResRight('r_t_annoucement')) {
		$this->C->ajaxmsg(0);
	}
    if ($detail == 'add') {
        $title = '增加公告类别-公告类型管理(list)';
        $conform = array('method'=>'post','action'=>'index.php?action=announcement_type&detail=addmod','width'=>'500px');
        $data['color'] = '#000000';
    }
    elseif ($detail == 'edit') {
        $title = '修改公告类别-公告类型管理(list)';
        $conform = array('method'=>'post','action'=>'index.php?action=announcement_type&detail=editmod&id='.$id,'width'=>'500px');
        $data = $this->S->dao('announcementtype')->get_one_by_id($id);
    }

	$disinputarr = array();

	$disinputarr['name'] 	    = array('showname'=>'类别名称','value'=>$data['name']);
	$disinputarr['color']     	= array('showname'=>'颜色','value' => $data['color'] ,'inextra'=>'id="color" style="background-color:'.$data['color'].'"');

	$colwidth = array('1'=>'100','2'=>'300','3'=>'150');
	$jslink = '<link href="./staticment/css/Farbtastic.css" rel="stylesheet" type="text/css" />';
	$jslink .= '<script src="./staticment/js/Farbtastic.js" type="text/javascript" ></script>';
	$jslink .= '<script src="./staticment/js/announcement_type.js" type="text/javascript" ></script>';

    $this->V->mark(array('title'=>$title));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_edit';
}

elseif ($detail == 'addmod') {
	if (!$this->C->service('admin_access')->checkResRight('r_t_annoucement')) {
		$this->C->ajaxmsg(0);
	}
	if($name){
		if (strlen($color) < 2 || strpos($color, '#') !== 0 ) {
			$color = '#000000';
		}
		if($this->S->dao('announcementtype')->D->insert(array('name'=>$name,'color'=>$color,'cdate'=>date('Y-m-d h-i-s',time()), 'cuser'=>$_SESSION['eng_name']))){
			$this->C->success('添加成功','index.php?action=announcement_type&detail=list');
		}
	}
}

elseif ($detail == 'editmod') {
	if (!$this->C->service('admin_access')->checkResRight('r_t_annoucement')) {
		$this->C->ajaxmsg(0);
	}
	if($id && $name){
		if (strlen($color) < 2 || strpos($color, '#') !== 0 ) {
			$color = '#000000';
		}
		if($this->S->dao('announcementtype')->D->update_by_field(array('id'=>$id),array('name'=>$name,'color'=>$color)))
		$this->C->success('修改成功','index.php?action=announcement_type&detail=list');
	}
}


?>

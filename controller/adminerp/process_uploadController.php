<?php


/**
 * @title选择界面 create by Hanson 2013-14
 *
 */
 if($detail == 'list'){

    $bannerstr = '<button class="eight" onclick=window.location="index.php?action=process_upload&detail=fnskulist">FN-SKU条码</button><br><font color=#bdbdbd size=-1> &nbsp; FN-SKU条形码的上传，审核，删除等。</font><br><br>';
    $bannerstr.= '<button class="eight" onclick=window.location="index.php?action=process_upload&detail=skulist">ERP-SKU条码</button><br><font color=#bdbdbd size=-1> &nbsp; ERP-SKU条形码的上传，审核，删除等。</font>';

	$this->V->mark(array('title'=>'条形码管理'));
	$temp = 'pub_list';
 }


/**
 * @title SKU条码管理 create by hanson 2013-1-14
 */
elseif($detail == 'skulist'){

	$stypemu = array(
    	'barcodeurl-s-l'   =>'文件名称：',
    	'creater-s-l'      =>'创建者：',
     );

	$datalist	= $this->S->dao('barcode')->D->get_list($sqlstr.' and type="1"','','fstcreate desc');

	for($i = 0 ;$i<count($datalist);$i++){
		$datalist[$i]['delurl']		= '<a href=javascript:void(0);delitem("index.php?action=process_upload&detail=dele&id='.$datalist[$i]['id'].'&filename='.urlencode($datalist[$i]['barcodeurl']).'","") title="删除"><img src="./staticment/images/deletebody.gif" border="0"></a>';
		$datalist[$i]['barcodeurl']	= '<a href="data/fnsku/'.$datalist[$i]['barcodeurl'].'">'.$datalist[$i]['barcodeurl'].'</a>';
    }
	$displayarr = array();
	$tablewidth = '750';

    $displayarr['delurl']	   = array('showname'=>'删除','width'=>'50');
	$displayarr['barcodeurl']  = array('showname'=>'文件名称','width'=>'200');
	$displayarr['creater']     = array('showname'=>'创建者','width'=>'150');
	$displayarr['fstcreate']   = array('showname'=>'创建时间','width'=>'150');

	$bannerstr = '<button class="six" onclick="window.location=\'index.php?action=process_upload&detail=add&type=1\'">上传条形码</button>';
	$this->V->mark(array('title'=>'ErpSku条形码列表-条形码管理(list)'));
	$temp = 'pub_list';
}



/**
 * @title 条形码文件上传
 * @author Jerry
 * @create on 2013-1-3
 *
 */
 elseif ($detail == 'fnskulist') {

	$stypemu = array(
        'ischeck-h-e'      =>'是否审核',
    	'barcodeurl-s-l'   =>'文件名称：',
    	'creater-s-l'      =>'创建者：',

     );
    /*标签导航选项*/
   	$tab_menu_stypemu = array(
		'ischeck-1'=>'未审核',
		'ischeck-2'=>'已通过',
        'ischeck-3'=>'不通过',
	);
    if(empty($sqlstr) && !isset($ischeck)){$ischeck = 1;$sqlstr=" and ischeck=1";}

	$datalist = $this->S->dao('barcode')->D->get_list($sqlstr.' and type="0"','','fstcreate desc');

    for($i = 0 ;$i<count($datalist);$i++){
        $datalist[$i]['aduturl'] = '<a href=javascript:void(0);delitem("index.php?action=process_upload&detail=editmod&id='.$datalist[$i]['id'].'","") title="通过">通过</a>&nbsp;&nbsp;<a href="index.php?action=process_upload&detail=edit&id='.$datalist[$i]['id'].'" title="不通过">不通过</a>';
        $datalist[$i]['bothurl'] = '<a href=javascript:void(0);delitem("index.php?action=process_upload&detail=dele&id='.$datalist[$i]['id'].'&filename='.urlencode($datalist[$i]['barcodeurl']).'","") title="删除"><img src="./staticment/images/deletebody.gif" border="0"></a>';
        $datalist[$i]['barcodeurl'] = '<a href="data/fnsku/'.$datalist[$i]['barcodeurl'].'" title="点击下载" >'.$datalist[$i]['barcodeurl'].'</a>';
    }

	$displayarr = array();
	$tablewidth = '750';

    $displayarr['bothurl']     = array('showname'=>'删除','width'=>'50');
	$displayarr['barcodeurl']  = array('showname'=>'文件名称','width'=>'200');
	$displayarr['creater']     = array('showname'=>'创建者','width'=>'150');
	$displayarr['fstcreate']   = array('showname'=>'创建时间','width'=>'150');

    if ($ischeck == 3){
        $displayarr['remark']  = array('showname'=>'备注','width'=>'150');
    }

    if ($ischeck == 1){
    	$displayarr['aduturl'] = array('showname'=>'操作','width'=>'100');
    }

	$bannerstr = '<button class="six" onclick="window.location=\'index.php?action=process_upload&detail=add\'">上传条形码</button>';
	$this->V->mark(array('title'=>'FnSku条形码列表-条形码管理(list)'));

	$temp = 'pub_list';
 }

/**
 * @title 条形码上传  Jerry  create on 2013-1-7
 */
elseif ($detail == 'add') {

    if(!$this->C->service('admin_access')->checkResRight('processupload_add')) $this->C->sendmsg();

    if($type == '1'){
    	$this->V->mark(array('title'=>'条形码上传-ErpSku条形码列表(skulist)-条形码管理(list)','type'=>$type));
    }else{
	    $this->V->mark(array('title'=>'条形码上传-FnSku条形码列表(fnskulist)-条形码管理(list)','type'=>$type));
    }
    $this->V->set_tpl('adminweb/process_upload');
    display();
}

/*AJAX删除*/
elseif($detail == 'dele'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('processupload_dele')) {$this->C->ajaxmsg(0);}

    if (!$filename) {exit('参数错误！');}
    $save_path = 'data/fnsku/';//文件上传目录
    $file_path = $save_path.$filename;//文件路径

	if($this->S->dao('barcode')->D->delete(array('id'=>$id))) {
	   if(file_exists($file_path)){unlink($file_path);}
	   $this->C->ajaxmsg(1);
    }else{
       $this->C->ajaxmsg('删除失败！');
    }
}

/*不通过添加备注*/
elseif ($detail == 'edit') {
    $data = $this->S->dao('barcode')->D->get_one(array('id'=>$id),'id,barcodeurl,remark');

    $jump = 'index.php?action=process_upload&detail=editmodno';

    $conform = Array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = Array('1'=>'100','2'=>'220','3'=>'80');

    $disinputarr = Array();
	$disinputarr['id'] 	           = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['barcodeurl'] 	   = array('showname'=>'文件名称','value'=>$data['barcodeurl'],'inextra'=>'readonly');
	$disinputarr['remark'] 	       = array('showname'=>'备注','value'=>$data['remark']);
    $disinputarr['ischeck'] 	   = array('showname'=>'审核状态','value'=>'不通过','inextra'=>'readonly');

    $temp = 'pub_edit';

    $this->V->view['title'] = '不通过-FnSku条形码列表(fnskulist)-条形码管理(list)';
}


elseif ($detail == 'editmodno') {

    if(!$this->C->service('admin_access')->checkResRight('processupload_editmod')) $this->C->sendmsg();

    $result = $this->S->dao('barcode')->D->update_by_field(array('id'=>$id),array('ischeck'=>3,'remark'=>$remark));
    if (!$result)
        $this->C->success('审核失败','index.php?action=process_upload&detail=list');
    else
        $this->C->success('审核成功','index.php?action=process_upload&detail=list');
}


/*审核操作*/
elseif ($detail == 'editmod') {
   	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('processupload_editmod')) $this->C->ajaxmsg(0);
    if($this->S->dao('barcode')->D->update_by_field(array('id'=>$id),array('ischeck'=>2))){$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg('操作失败');}

}

/*模板定义*/
if($detail =='list' || $detail == 'add' || $detail == 'edit' || $detail == 'fnskulist' || $detail == 'skulist'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}


?>
<?php
/*
 * 别名使用组选择列表
 * */
$sold_wayarray = array('金碟','Amazon组','ebay组', 'newegg组');

$sold_waystr = '<select name="sold_way">';
$sold_waystr .= '<option value="">请选择</option>';
foreach ($sold_wayarray as $val) {
	$sold_waystr .= '<option value="'.$val.'">'.$val.'</option>';
}
$sold_waystr .= '</select>';

if ($detail == 'list') {
	$stypemu = array(
		'pro_sku-s-e'=>'&nbsp; &nbsp; SKU：',
		'sku_code-s-l'=>'&nbsp; &nbsp;SKU别名：',
		'sold_way-a-e'=>'&nbsp; &nbsp;别名使用组：'
	);
	$sold_wayarr = array(''=>'请选择');
	foreach($sold_wayarray as $val){
        $sold_wayarr[$val] = $val;
	}

	$bannerstr = '<button onclick=window.location="index.php?action=sku_alias&detail=new">添加别名</button>';
	$bannerstr .= '<button onclick=window.location="index.php?action=sku_alias&detail=import">导入别名</button>';
    //$bannerstr .= '<button onclick=window.location="index.php?action=sku_alias&detail=export&pro_sku='.$pro_sku.'&sku_code='.$sku_code.'">导出别名</button>';
    $bannerstrarr[] = array('url'=>'index.php?action=sku_alias&detail=export','value'=>'导出别名');


	$sku_alias = $this->S->dao(sku_alias);
	$sqlstr .= ' and sku_code<>pro_sku ';
	$datalist = $sku_alias->D->get_list($sqlstr,'','id asc','id,pro_sku,sku_code,sold_way,isnew');

	$tablewidth = '800';
	$displayarr['pro_sku'] 	= array('showname'=>'产品SKU', 'width'=>'200');
	$displayarr['sku_code']	= array('showname'=>'SKU别名', 'width'=>'200');
	$displayarr['sold_way']	= array('showname'=>'销售使用组', 'width'=>'200');
	$displayarr['both'] 		= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=sku_alias&detail=delete&id={id}','url_e'=>'index.php?action=sku_alias&detail=edit&id={id}');

	$this->V->view['title'] = 'SKU别名表';
	$temp = 'pub_list';

}
elseif ($detail == 'new') {
    
    	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_alias_add')){$this->C->sendmsg();}
    
	$jslink .= "<script src='./staticment/js/jquery.js'></script>\n";
	$jumpurl = "index.php?action=sku_alias&detail=newmethod";

	$this->V->mark(array('jslink'=>$jslink, 'jumpurl'=>$jumpurl,'title'=>'添加SKU别名-SKU别名表(list)', 'sold_wayselect'=>$sold_waystr));
	$this->V->set_tpl('adminweb/newskualias');

	display();
}
/*向数据库中插入SKU别名映射*/
elseif ($detail == 'newmethod') {
	$pro_sku = $_REQUEST['pro_sku'];
	$sku_code = $_REQUEST['sku_code'];
	$sold_way = $_REQUEST['sold_way'];
	//$backdata = $this->S->dao('admin_group')->D->get_one_by_field(array('id'=>$_SESSION['groupid']),'groupname');
	for ($i=0; $i<count($pro_sku); $i++) {
		/*空值不处理*/
		if ($pro_sku[$i] != '') {
			$temp = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$pro_sku[$i]),'pid');
			$pid = $temp['pid'];
			for ($j=0; $j<count($sku_code[$i]); $j++) {
				/*空值不处理*/
				if ($sku_code[$i][$j] != '') {
				 	$sid = $this->S->dao('sku_alias')->insert_sku_alias($pid, $pro_sku[$i], $sku_code[$i][$j], $sold_way);
				}

			}
		}
	}
	if ($sid) {
		$this->C->success('添加成功','index.php?action=sku_alias&detail=list');
	}
}
elseif ($detail == 'delete') {
    
    /*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_alias_del')){$this->C->ajaxmsg(0);}
    
	if($id){
		if($this->S->dao('sku_alias')->D->delete_by_field(array('id'=>$id))) {
			$this->C->ajaxmsg(1);
		}
	}
}
elseif ($detail == 'edit') {
    /*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_alias_edit')){$this->C->sendmsg();}
    
	if(empty($id)){
		exit('没有ID!');
	}
	$data = $this->S->dao('sku_alias')->D->select('pro_sku, sku_code, sold_way','id='.$id);
	$jump = 'index.php?action=sku_alias&detail=editmethod';
	/*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'80');

	$sold_way = '<select name="sold_way">';
	$sold_way.= '<option value="">请选择</option>';
	foreach ($sold_wayarray as $val) {
		$sold_way .= '<option value="'.$val.'" '.($val==$data['sold_way']?"selected":'').'>'.$val.'</option>';
	}
	$sold_way .= '</select>';

	$disinputarr = array();
	$disinputarr['id'] 	 			= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['pro_sku'] 	 	= array('showname'=>'产品SKU','value'=>$data['pro_sku'],'extra'=>'*');
	$disinputarr['sku_code']  	 	= array('showname'=>'SKU别名','value'=>$data['sku_code'],'extra'=>'*');
	$disinputarr['sold_way']  	 	= array('showname'=>'别名使用组','datatype'=>'se','datastr'=>$sold_way,'extra'=>'*');
	$jslink = "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/skualias.js'></script>\n";

	$this->V->view['title'] = 'SKU别名修改-SKU别名表(list)';

	$temp = 'pub_edit';
}
/*修改ID为$id的别名映射*/
elseif ($detail == 'editmethod') {
    
   	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_alias_edit')){$this->C->sendmsg();}
    
	$temp = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$pro_sku),'pid');
	$sid = $this->S->dao('sku_alias')->update_sku_skucode($id, $temp['pid'], $pro_sku, $sku_code, $sold_way);
	if ($sid) {
		$this->C->success('修改成功','index.php?action=sku_alias&detail=list');
	}

}
elseif ($detail == 'checkallsku') {
	$pro_sku = $_REQUEST['pro_sku'];
	$sku_code = $_REQUEST['sku_code'];
	$return_array = array();
	/*判断添加sku映射中sku是否存在产品*/
	for ($i=0; $i<count($pro_sku); $i++) {
		$temp = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$pro_sku[$i]),'pid');
		if (!$temp) {
			$return_array[] = $pro_sku[$i];
		}
	}
	/*判断添加sku映射中sku别名是否重复*/
	for ($i=0; $i<count($sku_code); $i++) {
		$temp = $this->S->dao('sku_alias')->D->get_one_by_field(array('sku_code'=>$sku_code[$i]),'id');
		if ($temp) {
			$return_array[] = $sku_code[$i];
		}
	}
	$data = json_encode($return_array);
	echo  $data;
}
elseif ($detail == 'checksku') {
	$return_array = array();
	$temp = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$pro_sku),'pid');
	if (!$temp) {
		$return_array[] = $pro_sku;
	}
	$temp = $this->S->dao('sku_alias')->D->select('id',' sku_code="'.$sku_code.'" and id<>'.$id);
	if ($temp) {
		$return_array[] = $sku_code;
	}
	$data = json_encode($return_array);
	echo $data;
}
elseif ($detail == 'import') {
    
   	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_alias_add')){$this->C->sendmsg();}
	$product = $this->S->dao('product');
    
	$sku_alias = $this->S->dao('sku_alias');
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/sku_alias/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head = 1;//以第一行为表头

	$tablelist = '';

	/*读取已经上传的文件*/
	if($filepath){
		$all_arr =  $this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);

		$iserror = 0;
		$sku_alias->D->query('begin');
		for($i=1; $i < count($all_arr); $i++){
			//查看别名是否已经存在
			$rs2 = $sku_alias->get_sku_by_code($all_arr[$i]['sku_code']);
			//若存在，说明sku别名已经存在，提示
			if ($rs2) {
				continue;
			}

			$rs1 = $product->get_product_by_sku($all_arr[$i]['sku']);
			$sid = $sku_alias->insert_sku_alias($rs1['pid'], $all_arr[$i]['sku'], $all_arr[$i]['sku_code'], $sold_way);
			if (!$sid) {
				$iserror = 1;
			}
		}

		if ($iserror) {
			$sku_alias->D->query('rollback');
			$sucss_msg = '导入失败！';
			$this->C->success($sucss_msg,'index.php?action=sku_alias&detail=list');
		}
		else {
			$sku_alias->D->query('commit');
			$sucss_msg = '导入成功！';
			$this->C->success($sucss_msg,'index.php?action=sku_alias&detail=list');
		}

	}
	else {
		$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath = $this->getLibrary('basefuns')->getsession('filepath');
		//有错误的记录数量
		$data_error = 0;

		$bodylist = '';
		$tablelist .= '<table id="mytable">';
		//表头
		$bodylist .= '<tr><th class="list">sku</th><th class="list">sku别名</th><th class="list">产品名称</th></tr>';
		for($i=1; $i < count($all_arr); $i++){
			// 别名中是否有特殊字符
			$symbol = 0;
			for ($j=0; $j<strlen($all_arr[$i]['sku_code']); $j++) {
				//判断地址列是否有ASC=26 63 以及乱码
				if (ord($all_arr[$i]['sku_code'][$j])>128) {
					$symbol = 1;
				}
			}

			//本条记录是否有错误
			$iserror = 0;
			//查看sku是否存在产品
			$rs1 = $product->get_product_by_sku($all_arr[$i]['sku']);
			if ($rs1) {
				$bodylist .= '<td>'.$all_arr[$i]['sku'].'</td>';
			}
			//若不存在，说明输入sku有误，提示
			else {
				$bodylist .= '<td bgcolor="red" title="SKU不存在">'.$all_arr[$i]['sku'].'</td>';
				$iserror = 1;
			}
			if ($symbol) {
				$bodylist .= '<td bgcolor="red" title="SKU别名有特殊符号，请在excel中替换:'.$rs2.'">'.$all_arr[$i]['sku_code'].'</td>';
				$iserror = 1;
			}
			else {
				//查看别名是否已经存在
				$rs2 = $sku_alias->get_sku_by_code($all_arr[$i]['sku_code']);
				//若存在，说明sku别名已经存在，提示
				if ($rs2) {
					$bodylist .= '<td bgcolor="green" title="SKU别名已经存在:'.$rs2.'">'.$all_arr[$i]['sku_code'].'</td>';
				}
				else {
					$bodylist .= '<td>'.$all_arr[$i]['sku_code'].'</td>';
				}
			}
			$bodylist .= '<td>'.$rs1['product_name'].'</td>';
			$bodylist .= '</tr>';
			if ($iserror) {
				$data_error++;
			}
		}

		if (isset($all_arr)){
			$tablelist .= $bodylist;
		}
		$tablelist .= '</table>';
		/*错误判断*/
		if(!$data_error && isset($all_arr)){
            $tablelist .= '<script type="text/javascript" src="./staticment/js/jquery.js"></script>';
			$tablelist .= '<script type="text/javascript" src="./staticment/js/skualias.js"></script>';
			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />'.$not_enougth_arryrow;
			$tablelist .= $sold_waystr;
			$tablelist .= '<font color="#577dc6" size="1">&nbsp;&nbsp;选择别名使用组后提交！</font>';
			$tablelist .= '<font color="#577dc6" size="1">'.$not_enougth_skutips.'</font><br><input type="submit" value="确认并提交" name="submit" id=submit_once style="display:none;"><input type="reset" value="取消" onclick=window.location="index.php?action=sku_alias&detail=list" style="display:none;">';
		}elseif($data_error){
			$tablelist .= '<font color="#577dc6" size="1">总共有'.$data_error.'条记录错误，请修正后重新上传。</font>';
			unlink($filepath);//有错的文件删除掉
		}
	}
	//echo $bodylist;
	$submit_action = 'index.php?action=sku_alias&detail=import';
	$temlate_exlurl = 'data/uploadexl/sample/sku_alias.xls';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'导入别名-SKU别名表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}
elseif ($detail == 'export') {
    $sku_alias = $this->S->dao(sku_alias);
	$sqlstr .= ' and sku_code<>pro_sku ';
	$datalist = $sku_alias->D->get_allstr($sqlstr,'','id asc','pro_sku,sku_code');
    $filename = 'sku_alias_'.date('Y-m-d-h-i-s',time());
	$head_array = array('pro_sku'=>'sku','sku_code'=>'sku_alias');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}
if($detail == 'list' || $detail == 'edit' || $detail == 'new'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}

?>

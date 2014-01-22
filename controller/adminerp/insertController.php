<?php

if($detail == 'sku_alias'){
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/order_transfer/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head = 1;//以第一行为表头

	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	//$filepath = $this->getLibrary('basefuns')->getsession('filepath');

	if ($all_arr) {
		/*成功统计量*/
		$tempserver  = $this->S->dao('sku_alias');
		$successcout = 0;
		$tempserver->D->query('begin');

		for($i=1; $i < count($all_arr); $i++){
			$sid = $tempserver->insert_sku_alias($all_arr[$i]['pid'], $all_arr[$i]['new'], $all_arr[$i]['old']);
			if (!$sid) {
				$successcout = 1;
			}
			if ($all_arr[$i]['old'] != $all_arr[$i]['new']) {
				$sid = $tempserver->insert_sku_alias($all_arr[$i]['pid'], $all_arr[$i]['new'], $all_arr[$i]['new']);
			}
			if (!$sid) {
				$successcout = 1;
			}
		}
		$jumpurl = 'index.php?action=sku_alias&detail=list';
		if (!$successcout) {
			$tempserver->D->query('commit');
			$this->C->success('添加成功',$jumpurl);
		}
		else{
			$tempserver->D->query('rollback');
			$this->C->success('添加失败',$jumpurl);
		}
	}
	$submit_action = 'index.php?action=insert&detail=sku_alias';
}
elseif($detail == 'product'){
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/order_transfer/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head = 1;//以第一行为表头

	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	//$filepath = $this->getLibrary('basefuns')->getsession('filepath');

	if ($all_arr) {
		/*成功统计量*/
		$tempserver  = $this->S->dao('product');
		$successcout = 0;
		$tempserver->D->query('begin');

		for($i=1; $i < count($all_arr); $i++){
			$sid = $tempserver->D->update_by_field(array('pid' => $all_arr[$i]['pid']),array('sku'=>$all_arr[$i]['new']));
			if ($sid) {
				$successcout ++;
			}
		}
		$jumpurl = 'index.php?action=product_list&detail=list';
		if ($successcout == count($all_arr)-1) {
			$tempserver->D->query('commit');
			$this->C->success('添加成功',$jumpurl);
		}
		else{
			$tempserver->D->query('rollback');
			$this->C->success('添加失败',$jumpurl);
		}
		echo $successcout;
	}
	$submit_action = 'index.php?action=insert&detail=product';
}
elseif($detail == 'process'){
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/order_transfer/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head = 1;//以第一行为表头

	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	//$filepath = $this->getLibrary('basefuns')->getsession('filepath');

	if ($all_arr) {
		/*成功统计量*/
		$tempserver  = $this->S->dao('process');
		$successcout = 0;
		$tempserver->D->query('begin');

		for($i=1; $i < count($all_arr); $i++){
			$sid = $tempserver->D->update_by_field(array('pid' => $all_arr[$i]['pid']),array('sku'=>$all_arr[$i]['new']));
			if (!$sid) {
				$successcout = 1;
			}
		}
		$jumpurl = 'index.php?action=product_list&detail=list';
		if (!$successcout) {
			$tempserver->D->query('commit');
			$this->C->success('添加成功',$jumpurl);
		}
		else{
			$tempserver->D->query('rollback');
			$this->C->success('添加失败',$jumpurl);
		}
	}
	$submit_action = 'index.php?action=insert&detail=process';
}
$this->V->set_tpl('admintag/tag_header','F');
$this->V->set_tpl('admintag/tag_footer','L');
$this->V->mark(array('title'=>'导入订单','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
$this->V->set_tpl('adminweb/commom_excel_import');
display();
?>
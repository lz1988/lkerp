<?php
/**
 * @title 作SKU替换用
 * @author by wall
 */
if($detail=='sql') {
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/order_transfer/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head = 1;//以第一行为表头
	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	//$filepath = $this->getLibrary('basefuns')->getsession('filepath');

	if ($all_arr) {
		/*成功统计量*/
		$tempserver  = $this->S->dao('product');

		for($i=1; $i < count($all_arr); $i++){
			$temp = $tempserver->D->select('pid','sku="'.$all_arr[$i]['old'].'"');
			$all_arr[$i]['pid'] = $temp['pid'];
		}
		$filename = 'sku_code';
		$head_array = array('pid' => 'pid', 'old' => 'old', 'new' => 'new', 'product_name'=>'product_name');
		$this->C->service('upload_excel')->download_excel($filename,$head_array,$all_arr);
	}
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'导入订单','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}

elseif ($detail == 'change') {
	$process = $this->S->dao('process');
	$sqlstr = 'SELECT id,sku FROM process WHERE 1=1 and (property="出仓单" or property="转仓单") and isover="Y" ';
	$all_arr = $process->D->query_array($sqlstr);
	for ($i=0; $i<count($all_arr); $i++) {
		$temp = $this->S->dao('sku_alias')->D->select('pro_sku', 'sku_code="'.$all_arr[$i]['sku'].'" and pro_sku<>sku_code');
		$all_arr[$i]['pro_sku'] = $temp['pro_sku'];
		if ($temp['pro_sku']!='') {
			$process->D->update_by_field(array('id' => $all_arr[$i]['id']),array('sku'=>$temp['pro_sku']));
			$all_arr[$i]['last_sku'] = $temp['pro_sku'];
		}
	}
}
?>
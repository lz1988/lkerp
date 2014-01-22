<?php
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
elseif ($detail=='report') {
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/order_transfer/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head = 1;//以第一行为表头
	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	//$filepath = $this->getLibrary('basefuns')->getsession('filepath');

	//echo '<pre>'.print_r($all_arr, 1).'</pre>';
	if ($all_arr) {
		for($i=1; $i < count($all_arr); $i++){
			$mail = split('@',$all_arr[$i]['toemail']);
			$mailname = $mail[0];
			$all_arr[$i]['fid'] = $mailname.'_'.date('Y_m_d', time()).'_'.$i;

		}
		$filename = 'new_report';
		$head_array = array('Name' => 'product_name', 'Currency' => 'coin_code', 'Gross' => 'price', 'Fee'=>'fee','toemail'=>'email', 'fid'=>fid);
		$this->C->service('upload_excel')->download_excel($filename,$head_array,$all_arr);
	}
	//echo '<pre>'.print_r($all_arr, 1).'</pre>';
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
	for ($i=1; $i<count($all_arr); $i++) {
		$temp = $this->S->dao('sku_alias')->D->select('pro_sku', 'sku_code="'.$all_arr[$i]['sku'].'" and pro_sku<>sku_code');
		$all_arr[$i]['pro_sku'] = $temp['pro_sku'];
		if ($temp['pro_sku']!='') {
			$process->D->update_by_field(array('id' => $all_arr[$i]['id']),array('sku'=>$temp['pro_sku']));
			$all_arr[$i]['last_sku'] = $temp['pro_sku'];
		}
	}

	echo '<pre>'.print_r($all_arr, 1).'</pre>';
}
elseif ($detail=='test') {
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/order_transfer/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head = 1;//以第一行为表头
	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	//$filepath = $this->getLibrary('basefuns')->getsession('filepath');

	echo '<pre>'.print_r($all_arr, 1).'</pre>';
	if ($all_arr) {
		for($i=1; $i < count($all_arr); $i++){
			$mail = split('@',$all_arr[$i]['toemail']);
			$mailname = $mail[0];
			$all_arr[$i]['fid'] = $mailname.'_'.date('Y_m_d', time()).'_'.$i;
			for ($j=0; $j<strlen($all_arr[$i]['Shipping Address']); $j++) {
				if (ord($all_arr[$i]['Shipping Address'][$j])>128) {
					$all_arr[$i]['error'] = 1;
				}
			}
		}
		$filename = 'new_report';
		//$head_array = array('Name' => 'product_name', 'Currency' => 'coin_code', 'Gross' => 'price', 'Fee'=>'fee','toemail'=>'email', 'fid'=>fid);
		//$this->C->service('upload_excel')->download_excel($filename,$head_array,$all_arr);
	}
	echo '<pre>'.print_r($all_arr, 1).'</pre>';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

	$this->V->mark(array('title'=>'导入订单','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}

elseif($detail == 'dao'){

	$locktab = $this->S->dao('locktab');
	$locktab->D->query('lock table locktab write');

	$process = $this->S->dao('process');
	$process->D->query('begin');

	$maxx =  $process->D->get_one_by_field(array('property'=>'出仓单'),'max( SUBSTRING( order_id, 2, 7 ) ) as order_id');
	 var_dump($maxx);exit();


}

elseif($detail == 'hao'){

	for($i=0; $i< 30;$i++){

		$backdata = $this->S->dao('sku_alias')->D->get_one_by_field(array('sku_code'=>'800235-6967FC01LK-561560'),'pro_sku');
		if($backdata['pro_sku']){
			echo $i.'.aaaa<br>';
		}else{
			echo $i.'.bbbb<br>';
		}
	}
	echo rand(000,999);
}


elseif($detail == 'fm'){

	$friend_message = $this->S->dao('friend_message');

	$sid = $friend_message->insert_one_leave_message(22, 65, 'WANG', '测试的', 0, date('Y-m-d H:i:s', time()), '', date('Y-m-d H:i:s', time()));

	if($sid) {echo 'aaa';}else{echo 'bbb';}

}

elseif($detail == 'coin'){

	echo  $this->C->service('global')->get_system_defaultcoin();
}


?>
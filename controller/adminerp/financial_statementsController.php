<?php
/*
 * Created on 2012-7-31
 * update on 2012-08-16
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

// 报表包含选择搜索项
$financial_select = array(
		1=>array('select'=>array('supplier-s-l'=>'<br />供应商：',	 'sku-s-l'=>'SKU：', 'mdate-t-t'=>'订单日期：'), 'name'=>'采购订单序时簿', 'function'=>'financial_get_purchase_order', 'time'=>'mdate', 'filename'=>'purchase_order'),
		array('select'=>array('receiver_id-a-e'=>'<br />收货仓库：', 'sku-s-l'=>'SKU：', 'border_id-s-e'=>'采购订单号：', 'mdate-t-t'=>'<br />订单日期：'), 'name'=>'采购入库单序时簿', 'function'=>'financial_get_purchase_storage', 'time'=>'mdate', 'filename'=>'purchase_storage'),
		array('select'=>array('provider_id-a-e'=>'<br />发货仓库：', 'shipping-a-s'=>'&nbsp; 发货方式：', 'sku-s-l'=>'SKU：', 'aorder_id-s-e'=>'销售订单号：','mdate-t-t'=>'<br />日期：'), 'name'=>'销售出库单序时簿', 'function'=>'financial_get_sales_of_library', 'time'=>'mdate', 'filename'=>'sales_of_library'),
		array('select'=>array('provider_id-a-e'=>'<br />发货仓库：', 'shipping-a-s'=>'&nbsp; 发货方式：', 'sku-s-l'=>'SKU：', 'cdate-t-t'=>'<br />订单日期：'), 'name'=>'销售订单序时薄', 'function'=>'financial_get_sales_order', 'time'=>'cdate', 'filename'=>'sales_order'),
		array('select'=>array('sku-s-l'=>'<br />SKU：', 'provider_id-a-e'=>'调出仓库：', 'receiver_id-a-e'=>'调入仓库：', 'comment2-s-e'=>'shipmentID：', 'mdate-t-t'=>'<br />日期：'), 'name'=>'调仓单序时簿', 'function'=>'financial_get_material_allocation', 'time'=>'mdate', 'filename'=>'material_allocation'),
		array('select'=>array('sku-s-l'=>'<br />SKU：', 'provider_id-a-e'=>'发货仓库：', 'comment-s-l'=>'备注：', 'mdate-t-t'=>'<br />日期：'), 'name'=>'其他出库序时簿', 'function'=>'financial_get_other_libraries', 'time'=>'mdate', 'filename'=>'other_libraries'),
		array('select'=>array('sku-s-l'=>'<br />SKU：', 'receiver_id-a-e'=>'收货仓库：', 'comment-s-l'=>'备注：', 'mdate-t-t'=>'<br />日期：'), 'name'=>'其他入库序时簿', 'function'=>'financial_get_other_storage', 'time'=>'mdate', 'filename'=>'other_storage')
	);
// 报表显示单项
// coin_code 取得的都是订单币种、coin_code1取得的都是成本的币种
$financial_list = array(
		1=>array('mdate'=>'时间','orderNO'=>'单据编号','name'=>'供应商','sku'=>'产品SKU','product_name'=>'产品名称','quantity'=>'数量','coin_code'=>'币别','price'=>'不含税单价','e_iprice'=>'含税单价','sumprice'=>'不含税金额','e_siprice'=>'含税金额','price_rmb'=>'不含税单价(本位币RMB)','e_iprice_rmb'=>'含税单价(本位币RMB)','sumprice_rmb'=>'不含税金额(本位币RMB)','e_siprice_rmb'=>'含税金额(本位币RMB)','comment'=>'备注','e_recdate'=>'预计交货日期'),
		array('mdate'=>'日期','orderNO'=>'单据编号','sku'=>'产品SKU','product_name'=>'产品名称','name'=>'收货仓库','real_quantity'=>'应收数量','quantity'=>'实收数量','coin_code'=>'币别','price'=>'单价','sumprice'=>'金额','price_rmb'=>'单价(本位币RMB)','sumprice_rmb'=>'金额(本位币RMB)','order_id'=>'采购订单号'),
		array('mdate'=>'日期','orderNO'=>'单据编号','sold_way'=>'销售平台','sku'=>'产品SKU','product_name'=>'产品名称','name'=>'发货仓库','e_shipping'=>'发货方式','quantity'=>'数量','cost1'=>'成本单价(RMB)','sumcost1'=>'成本金额(RMB)','coin_code'=>'销售币别','price'=>'销售单价','sumprice'=>'销售金额','price_rmb'=>'销售单价(本位币RMB)','sumprice_rmb'=>'销售金额(本位币RMB)','order_id'=>'销售订单号'),
		array('cdate'=>'日期','orderNO'=>'单据编号','sold_way'=>'销售平台','sku'=>'产品SKU','product_name'=>'产品名称','name'=>'发货仓库','e_shipping'=>'发货方式','quantity'=>'数量','coin_code'=>'币别','price'=>'销售单价','sumprice'=>'销售金额','price_rmb'=>'销售单价(本位币RMB)','sumprice_rmb'=>'销售金额(本位币RMB)'),
		array('mdate'=>'日期','orderNO'=>'单据编号','sku'=>'产品SKU','product_name'=>'产品名称','outname'=>'调出仓库','inname'=>'调入仓库','quantity'=>'数量','cost1'=>'单价(RMB)','sumcost1'=>'金额(RMB)','comment2'=>'shipmentID'),
		array('mdate'=>'日期','orderNO'=>'单据编号','sku'=>'产品SKU','product_name'=>'产品名称','name'=>'发货仓库','quantity'=>'数量','cost1'=>'单价(RMB)','sumcost1'=>'金额(RMB)','comment'=>'备注'),
		array('mdate'=>'日期','orderNO'=>'单据编号','sku'=>'产品SKU','product_name'=>'产品名称','name'=>'收货仓库','quantity'=>'数量','cost1'=>'单价(RMB)','sumcost1'=>'金额(RMB)','comment'=>'备注')
	);

if($detail == 'list'){
	/*搜索项*/
	$stypemu = array(
		'statements-a-s'=>'报表：'
	);
	
	$statementsarr = array(''=>'=请选择=');
	foreach($financial_select as $key=>$val){
		$statementsarr[$key] = $val['name'];
	}
	
	foreach ($financial_select[$statements]['select'] as $key=>$val) {
		$stypemu[$key] = $val;
	}
	
	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
	$provider_idarr = array(''=>'=请选择=');
	for($i = 0; $i < count($wdata); $i++){
		$provider_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}	
	$receiver_idarr = array(''=>'=请选择=');
	for($i = 0; $i < count($wdata); $i++){
		$receiver_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}
	
	/*取得发货方式下拉*/
	$shipping_mu = $this->S->dao('shipping')->D->get_allstr('','','','s_name');
	$shippingarr = array(''=>'=请选择=');
	for($i = 0; $i < count($shipping_mu); $i++){
		$shippingarr[$shipping_mu[$i]['s_name']] = $shipping_mu[$i]['s_name'];
	}
	
	/*发货方式保存于扩展中，搜索条件另做处理*/
	if($shipping){
		$shipping_e	 = json_encode($shipping);
		$shipping_e	 = addslashes($shipping_e);
		$sqlstr		.= ' and locate("'.$shipping_e.'",a.extends,1)>0 ';
	}
	
	if ($sqlstr != '') {
	
		$sqlstr = str_replace('supplier', 'name', $sqlstr);
		$sqlstr = str_replace('receiver_id', 'a.receiver_id', $sqlstr);
		$sqlstr = str_replace('sku like "%', 'a.sku like "', $sqlstr);
		$sqlstr = str_replace('border_id', 'b.order_id', $sqlstr);
		$sqlstr = str_replace('aorder_id', 'a.order_id', $sqlstr);
		$sqlstr = str_replace('mdate', 'a.mdate', $sqlstr);
		$sqlstr = str_replace('warehouse', 'name', $sqlstr);	
		$sqlstr = str_replace('cdate', 'a.cdate', $sqlstr);
		$sqlstr = str_replace('comment', 'a.comment', $sqlstr);		
			
		$process = $this->S->dao('process');
		$InitPHP_conf['pageval'] = 15;
		
		$datalist = $process->$financial_select[$statements]['function']($sqlstr);
		$length = count($datalist);
		
		// 计算时间点目标产品的平均成本
		if (isset($datalist[0]['cost1'])) {
			$cost1arr = array();
			for ($i = 0; $i <$length; $i++) {
				$cost1arr[$datalist[$i]['sku']]['quantity'] += $datalist[$i]['quantity'];
				$cost1arr[$datalist[$i]['sku']]['sumcost1'] += $datalist[$i]['quantity'] * $datalist[$i]['cost1']; 
			}
			foreach ($cost1arr as &$val) {
				$val['cost1'] = $val['sumcost1'] / $val['quantity'];
			}
		}
//		echo '<pre>'.print_r($cost1arr, 1).'</pre>';
//		exit();
		$webcoin_code = $this->C->service('global')->get_system_defaultcoin();
		$datalist[$length]['cdate'] = $datalist[$length]['mdate'] = '合计';
		$datalist[$length]['real_quantity'] = 0;		// 应收数量合计
		$datalist[$length]['quantity'] = 0;				// 实收数量合计
		$datalist[$length]['sumcost1'] = 0;				// 成本金额合计
		$datalist[$length]['sumprice'] = 0;				// 不含税金额合计		
		$datalist[$length]['sumprice_rmb'] = 0;			// 本位币不含税金额合计
		$datalist[$length]['e_siprice'] = 0;			// 含税金额合计		
		$datalist[$length]['e_siprice_rmb'] = 0;		// 本位币含税金额合计
		for ($i = 0; $i <$length; $i++) {
			/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
			if($datalist[$i]['order_no'] == $datalist[$i-1]['order_no']){
				$datalist[$i]['orderNO'] = '';
			}else{
				$datalist[$i]['orderNO'] = $datalist[$i]['order_no'];
			}
			
			// 时间点平均成本
			$datalist[$i]['cost1'] = $cost1arr[$datalist[$i]['sku']]['cost1'];
			
			$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);/*解压压缩内容*/		
			if (isset($datalist[$i]['sumprice'])) {
				$datalist[$i]['price'] 	= $datalist[$i]['sumprice']/$datalist[$i]['quantity'];
				$datalist[$i]['price'] = number_format($datalist[$i]['price'],2,'.','');
			}
			else {	
				$datalist[$i]['sumprice'] 	= $datalist[$i]['quantity']*$datalist[$i]['price'];
			}	
			$datalist[$i]['sumcost1'] 	= $datalist[$i]['quantity']*$datalist[$i]['cost1'];
			
			$datalist[$i]['price_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['price'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);
			$datalist[$i]['sumprice_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['sumprice'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);
			$datalist[$i]['e_iprice_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['e_iprice'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);
			$datalist[$i]['e_siprice_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['e_siprice'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);			
			
			//$datalist[$i]['sumprice']	= number_format($datalist[$i]['sumprice'],2,'.','');
			//$datalist[$i]['sumcost1']	= number_format($datalist[$i]['sumcost1'],2,'.','');
			
			// 合计统计
			$datalist[$length]['quantity'] += $datalist[$i]['quantity'];
			$datalist[$length]['real_quantity'] += $datalist[$i]['real_quantity'];
			$datalist[$length]['sumprice'] += $datalist[$i]['sumprice'];
			$datalist[$length]['sumcost1'] += $datalist[$i]['sumcost1'];
			$datalist[$length]['sumprice_rmb'] += $datalist[$i]['sumprice_rmb'];
			$datalist[$length]['e_siprice'] += $datalist[$i]['e_siprice'];
			$datalist[$length]['e_siprice_rmb'] += $datalist[$i]['e_siprice_rmb'];
			
			$datalist[$i]['cost1'] = number_format($datalist[$i]['cost1'],2,'.','');
			$datalist[$i]['sumcost1'] = number_format($datalist[$i]['sumcost1'],2,'.','');
			$datalist[$i]['sumprice'] = number_format($datalist[$i]['sumprice'],2,'.','');
			$datalist[$i]['cdate'] = date('Y-m-d', strtotime($datalist[$i]['cdate']));
			$datalist[$i]['mdate'] = date('Y-m-d', strtotime($datalist[$i]['mdate']));
			$datalist[$i]['e_iprice']	= number_format($datalist[$i]['e_iprice'],2,'.','');
			$datalist[$i]['e_siprice']	= number_format($datalist[$i]['e_siprice'],2,'.','');		
			$datalist[$i]['price_rmb']	= number_format($datalist[$i]['price_rmb'],2,'.','');
			$datalist[$i]['sumprice_rmb']	= number_format($datalist[$i]['sumprice_rmb'],2,'.','');
			$datalist[$i]['e_iprice_rmb']	= number_format($datalist[$i]['e_iprice_rmb'],2,'.','');
			$datalist[$i]['e_siprice_rmb']	= number_format($datalist[$i]['e_siprice_rmb'],2,'.','');				
		} 

		$datalist[$length]['sumprice'] = number_format($datalist[$length]['sumprice'],2,'.','');
		$datalist[$length]['sumcost1'] = number_format($datalist[$length]['sumcost1'],2,'.','');
		$datalist[$length]['sumprice_rmb'] = number_format($datalist[$length]['sumprice_rmb'],2,'.','');
		$datalist[$length]['e_siprice'] = number_format($datalist[$length]['e_siprice'],2,'.','');
		$datalist[$length]['e_siprice_rmb'] = number_format($datalist[$length]['e_siprice_rmb'],2,'.','');
		
//		echo '<pre>'.print_r($datalist, 1).'</pre>';
		$bannerstrarr[] = array('url'=>'index.php?action=financial_statements&detail=output','value'=>'导出数据');
			
		$tablewidth = '1900';
		$displayarr = array();	
		foreach ($financial_list[$statements] as $key => $val) {
			$displayarr[$key]['showname'] = $val;
		}
	}	
	$jslink = "<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />\n";
	$jslink .= "<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>\n";
	$jslink .= "<script src='./staticment/js/financial_statements.js?2012-08-16'></script>\n";
	
	$this->V->mark(array('title'=>$financial_select[$statements]['name']));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';	
}

elseif ($detail == 'output') {
	//查询条件
    $sqlstr = '';
    $sqlstr.=empty($supplier)?'':" and name like '%".$supplier."%' ";
	$sqlstr.=empty($sku)?'':" and a.sku like '".$sku."%' ";
	$sqlstr.=empty($receiver_id)?'':' and a.receiver_id="'.$receiver_id.'"';
	$sqlstr.=empty($provider_id)?'':" and a.provider_id='".$provider_id."' ";
	$sqlstr.=empty($border_id)?'':" and b.order_id='".$order_id."' ";
	$sqlstr.=empty($aorder_id)?'':" and a.order_id='".$order_id."' ";
	$sqlstr.=empty($comment2)?'':" and comment2='".$comment2."' ";
	$sqlstr.=empty($comment)?'':" and a.comment like '%".$comment."%' ";
	$sqlstr.=empty($startTime)?'':' and a.'.$financial_select[$statements]['time'].'>="'.$startTime.' 00:00:00"';
	$sqlstr.=empty($endTime)?'':' and a.'.$financial_select[$statements]['time'].'<="'.$endTime.' 23:59:59"';
    
    //导出字段
    $head_array = array();
	foreach ($financial_list[$statements] as $key => $val) {
		$head_array[$key] = $val;
	}
	unset($InitPHP_conf['pageval']);
	$process = $this->S->dao('process');
	$datalist = $process->$financial_select[$statements]['function']($sqlstr);
    
	
	// 计算时间点目标产品的平均成本
	if (isset($datalist[0]['cost1'])) {
		$cost1arr = array();
		for ($i = 0; $i <$length; $i++) {
			$cost1arr[$datalist[$i]['sku']]['quantity'] += $datalist[$i]['quantity'];
			$cost1arr[$datalist[$i]['sku']]['sumcost1'] += $datalist[$i]['quantity'] * $datalist[$i]['cost1']; 
		}
		foreach ($cost1arr as &$val) {
			$val['cost1'] = $val['sumcost1'] / $val['quantity'];
		}
	}
	
	$length = count($datalist);
	
	$webcoin_code = $this->C->service('global')->get_system_defaultcoin();
	$datalist[$length]['cdate'] = $datalist[$length]['mdate'] = '合计';
	$datalist[$length]['real_quantity'] = 0;		// 应收数量合计
	$datalist[$length]['quantity'] = 0;				// 实收数量合计
	$datalist[$length]['sumcost1'] = 0;				// 成本金额合计
	$datalist[$length]['sumprice'] = 0;				// 不含税金额合计		
	$datalist[$length]['sumprice_rmb'] = 0;			// 本位币不含税金额合计
	$datalist[$length]['e_siprice'] = 0;			// 含税金额合计		
	$datalist[$length]['e_siprice_rmb'] = 0;		// 本位币含税金额合计
	
	for ($i = 0; $i <$length; $i++) {
		/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_no'] == $datalist[$i-1]['order_no']){
			$datalist[$i]['orderNO'] = '';
		}else{
			$datalist[$i]['orderNO'] = $datalist[$i]['order_no'];
		}
		
		// 时间点平均成本
		$datalist[$i]['cost1'] = $cost1arr[$datalist[$i]['sku']]['cost1'];
		
		$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);/*解压压缩内容*/			
		$datalist[$i]['sumprice'] 	= $datalist[$i]['quantity']*$datalist[$i]['price'];	
		$datalist[$i]['sumcost1'] 	= $datalist[$i]['quantity']*$datalist[$i]['cost1'];
		
		$datalist[$i]['price_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['price'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);
		$datalist[$i]['sumprice_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['sumprice'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);
		$datalist[$i]['e_iprice_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['e_iprice'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);
		$datalist[$i]['e_siprice_rmb'] = $this->C->service('exchange_rate')->change_rate_by_stage($datalist[$i]['e_siprice'], $datalist[$i]['coin_code'], $webcoin_code, $datalist[$i]['stage_rate']);			
		
		//$datalist[$i]['sumprice']	= number_format($datalist[$i]['sumprice'],2,'.','');
		//$datalist[$i]['sumcost1']	= number_format($datalist[$i]['sumcost1'],2,'.','');
		
		// 合计统计
		$datalist[$length]['quantity'] += $datalist[$i]['quantity'];
		$datalist[$length]['real_quantity'] += $datalist[$i]['real_quantity'];
		$datalist[$length]['sumprice'] += $datalist[$i]['sumprice'];
		$datalist[$length]['sumcost1'] += $datalist[$i]['sumcost1'];
		$datalist[$length]['sumprice_rmb'] += $datalist[$i]['sumprice_rmb'];
		$datalist[$length]['e_siprice'] += $datalist[$i]['e_siprice'];
		$datalist[$length]['e_siprice_rmb'] += $datalist[$i]['e_siprice_rmb'];
		
		$datalist[$i]['cost1'] = number_format($datalist[$i]['cost1'],2,'.','');
		$datalist[$i]['sumcost1'] = number_format($datalist[$i]['sumcost1'],2,'.','');
		$datalist[$i]['sumprice'] = number_format($datalist[$i]['sumprice'],2,'.','');
		$datalist[$i]['cdate'] = date('Y-m-d', strtotime($datalist[$i]['cdate']));
		$datalist[$i]['mdate'] = date('Y-m-d', strtotime($datalist[$i]['mdate']));
		$datalist[$i]['e_iprice']	= number_format($datalist[$i]['e_iprice'],2,'.','');
		$datalist[$i]['e_siprice']	= number_format($datalist[$i]['e_siprice'],2,'.','');		
		$datalist[$i]['price_rmb']	= number_format($datalist[$i]['price_rmb'],2,'.','');
		$datalist[$i]['sumprice_rmb']	= number_format($datalist[$i]['sumprice_rmb'],2,'.','');
		$datalist[$i]['e_iprice_rmb']	= number_format($datalist[$i]['e_iprice_rmb'],2,'.','');
		$datalist[$i]['e_siprice_rmb']	= number_format($datalist[$i]['e_siprice_rmb'],2,'.','');			
	} 
	$datalist[$length]['sumprice'] = number_format($datalist[$length]['sumprice'],2,'.','');
	$datalist[$length]['sumcost1'] = number_format($datalist[$length]['sumcost1'],2,'.','');
	$datalist[$length]['sumprice_rmb'] = number_format($datalist[$length]['sumprice_rmb'],2,'.','');
	$datalist[$length]['e_siprice'] = number_format($datalist[$length]['e_siprice'],2,'.','');
	$datalist[$length]['e_siprice_rmb'] = number_format($datalist[$length]['e_siprice_rmb'],2,'.','');
	
    $filename = $financial_select[$statements]['filename'].date('Y_m_d_h_i_s', time());
    $this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

// 获取供应商
elseif ($detail == 'getsupplier') {
    $q = strtolower($_GET["q"]);
	if (!$q) return;
    $result = array();
	$esseres = $this->S->dao('esse')->D->get_list(' and type=3 and (esseid like "%'.$q.'%" or name like "%'.$q.'%")','','','distinct id,name,esseid');
	foreach ($esseres as $val) {
        $result[] = array(
            'id' => $val['id'],
            'name' => $val['name'],
        	'val' => $val['esseid'].' '.$val['name'],
        );
    }

    echo json_encode($result);
}
?>


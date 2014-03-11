<?php

    
if ($detail == 'list') {
	$stypemu = array(
		'sku-s-l'     =>'&nbsp; &nbsp; SKU：',
		'sold_id-a-e' =>'&nbsp; &nbsp; 销售代码：',
		'cdate-t-t'   =>'&nbsp; &nbsp;日期：',
        'soldflag-b-' =>'&nbsp;&nbsp;渠道分组明细：'
	);

     /*取得销售账号下拉*/
	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','id, account_code');
	$sold_idarr		= array(''=>'=请选择=');
	for($i = 0; $i < count($soldaccount); $i++){
	   $sold_idarr[$soldaccount[$i]['id']] = $soldaccount[$i]['account_code'];
	}
   
    $tablewidth = '1370';
    $getaccount_code = $this->C->service('order')->get_account_code();//获取销售代码数组
    
    if (!empty($soldflag))
        $soldflagstr = '<input type="checkbox" name="soldflag" style="width:16px;height:16px;" value="1" checked >';
    else
        $soldflagstr = '<input type="checkbox" name="soldflag" style="width:16px;height:16px;" value="1" >';
        
	if ($sqlstr || $order) {
	   
        $sqlstr = str_replace("sold_id","p1.sold_id",$sqlstr);
		$bannerstrarr[] = array('url'=>'index.php?action=report_sold_bysku&detail=output&soldflag='.$soldflag.'','value'=>'导出数据');
		$asc_img 	= 'both_asc';
		$desc_img 	= 'both_desc';
		$none_img	= 'both_nonorder';
		$totalquantity_img 		= $none_img;
		$totalprice_img 		= $none_img;
		$totalprocess_img 		= $none_img;

		$totalquantity_desc 	= 'totalquantity_desc';
		$totalquantity_asc 		= 'totalquantity_asc';
		$totalprice_desc 		= 'totalprice_desc';
		$totalprice_asc 		= 'totalprice_asc';
		$totalprocess_desc 		= 'totalprocess_desc';
		$totalprocess_asc 		= 'totalprocess_asc';


		$orders = str_replace("_"," ", $order);
		if ($order) {
			$orders .= ',';
		}
		switch ($order) {
			case "totalquantity_asc":
				$totalquantity_img 		= $asc_img;
				break;
			case "totalprice_asc":
				$totalprice_img 		= $asc_img;
				break;
			case "totalprocess_asc":
				$totalprocess_img 		= $asc_img;
				break;
			case "totalquantity_desc":
				$totalquantity_img 		= $desc_img;
				break;
			case "totalprice_desc":
				$totalprice_img 		= $desc_img;
				break;
			case "totalprocess_desc":
				$totalprocess_img 		= $desc_img;
				break;
		}

		$InitPHP_conf['pageval'] = 20;
		//$sqlstr   = str_replace("sold_way","p1.sold_way",$sqlstr);
		$sqlstr   = str_replace('sku like "%','p1.sku like "',$sqlstr);
		$sqlstr   = str_replace("cdate","p1.cdate",$sqlstr);
		$process  = $this->S->dao('process');
        if ($soldflag)
            $datalist = $process->select_sku_sold_list_slod_id($sqlstr, $orders);
        else
            $datalist = $process->select_sku_sold_list($sqlstr, $orders);
        
        
		$length = count($datalist);
		$datalist[$length]['sku'] = '合计：';
		$datalist[$length]['totalquantity'] = 0;
		$datalist[$length]['totalprice'] = 0;
		$datalist[$length]['totalprocess'] = 0;
		$datalist[$length]['returnsum'] = 0;
		$datalist[$length]['returnprice'] = 0;
		$datalist[$length]['cost'] = 0;

		for ($i = 0; $i < $length; $i++) {
           
			if ($datalist[$i]['totalquantity']!=0) {
				$datalist[$i]['RMA'] = $datalist[$i]['returnsum']/$datalist[$i]['totalquantity'] *100;
				$datalist[$i]['RMA'] = number_format($datalist[$i]['RMA'], 2);
			}
            
			$datalist[$i]['RMA'] .= '%';
            
			if ($datalist[$i]['totalprice']!=0) {
				$datalist[$i]['RPA'] = $datalist[$i]['returnprice']/$datalist[$i]['totalprice'] *100;
				$datalist[$i]['RPA'] = number_format($datalist[$i]['RPA'], 2);
			}else{
                $datalist[$i]['RPA'] = '0.00';   //退货率 
			}
            
			$datalist[$i]['RPA'] .= '%';
			$datalist[$i]['totalprice'] = round($datalist[$i]['totalprice'], 2);
			$datalist[$i]['returnprice'] = number_format($datalist[$i]['returnprice'], 2);
			$datalist[$i]['coin_code'] = "USD";
            $datalist[$i]['account_code'] = $getaccount_code[$datalist[$i]['sold_id']];//销售代码

			$datalist[$length]['totalquantity'] += $datalist[$i]['totalquantity'];
			$datalist[$length]['totalprice'] += $datalist[$i]['totalprice'];
			$datalist[$length]['totalprocess'] += $datalist[$i]['totalprocess'];
			$datalist[$length]['returnsum'] += $datalist[$i]['returnsum'];
			$datalist[$length]['returnprice'] += $datalist[$i]['returnprice'];
			$datalist[$length]['cost'] += $datalist[$i]['cost'];
		}
        
		if ($datalist[$length]['totalquantity']!=0) {
            //echo $datalist[$length]['returnsum']."<br/>";
            //echo $datalist[$length]['totalquantity'];
			$datalist[$length]['RMA'] = $datalist[$length]['returnsum']/$datalist[$length]['totalquantity'] *100;
			$datalist[$length]['RMA'] = number_format($datalist[$length]['RMA'], 2);
		}
		$datalist[$length]['RMA'] .= '%';
        
		if ($datalist[$length]['totalprice']!=0) {
			$datalist[$length]['RPA'] = $datalist[$length]['returnprice']/$datalist[$length]['totalprice'] *100;
			$datalist[$length]['RPA'] = number_format($datalist[$length]['RPA'], 2);
		}
        if (empty($datalist[$length]['RPA']))
            $datalist[$length]['RPA'] = '0.00%';
        else
            $datalist[$length]['RPA'] .= '%';

		$t_orderslink .= '&order=';

		$pageshow = array('order'=>$order);
		
		$displayarr['sku'] = array('showname'=>'sku','width'=>'150');
		$displayarr['coin_code'] = array('showname'=>'币别','width'=>'60');
		$displayarr['totalquantity'] = array('showname'=>'卖出产品总数','width'=>'150', 'orderlink_desc' => $t_orderslink.$totalquantity_desc, 'orderlink_asc'=>$t_orderslink.$totalquantity_asc, 'order_type'=>$totalquantity_img);
		$displayarr['totalprice'] = array('showname'=>'订单销售总额','width'=>'150',  'orderlink_desc' => $t_orderslink.$totalprice_desc, 'orderlink_asc'=>$t_orderslink.$totalprice_asc, 'order_type'=>$totalprice_img);
		$displayarr['totalprocess'] = array('showname'=>'订单总数','width'=>'100', 'orderlink_desc' => $t_orderslink.$totalprocess_desc, 'orderlink_asc'=>$t_orderslink.$totalprocess_asc, 'order_type'=>$totalprocess_img);
        if (empty($soldflag)){
    		$displayarr['returnsum'] = array('showname'=>'退货总数','width'=>'100');
    		$displayarr['RMA'] = array('showname'=>'退货率','width'=>'80');
    		$displayarr['returnprice'] = array('showname'=>'退款金额','width'=>'150');
    		$displayarr['RPA'] = array('showname'=>'退款率','width'=>'80');
        }else{
            $displayarr['account_code'] = array('showname'=>'销售代码','width'=>'200');
        }
		$displayarr['cost'] = array('showname'=>'产品成本(RMB)','width'=>'150');
		
	}
    
    $this->V->mark(array('title'=>'SKU销售报表'));
	$temp = 'pub_list';
}
elseif ($detail == 'output') {
	$orders = str_replace("_"," ", $order);
	if ($order) {
		$orders .= ',';
	}

	$filename = 'Report';
	if ($startTime) {
		$filename .= '_'.$startTime;
	}
	else {
		$filename .= '_0000-00-00';
	}
	if ($endTime) {
		$filename .= '_'.$endTime;
	}
	else {
		$filename .= '_'.date('Y-m-d', time());
	}

	$sqlstr = str_replace("sold_id","p1.sold_id",$sqlstr);
	$sqlstr = str_replace('sku like "%','p1.sku like "',$sqlstr);
	$sqlstr = str_replace("cdate","p1.cdate",$sqlstr);
	$process = $this->S->dao('process');
	//$datalist = $process->select_sku_sold_list($sqlstr, $orders);
   if ($soldflag)
        $datalist = $process->select_sku_sold_list_slod_id($sqlstr, $orders);
   else
        $datalist = $process->select_sku_sold_list($sqlstr, $orders);
        
    $getaccount_code = $this->C->service('order')->get_account_code();//获取销售代码数组

	$length = count($datalist);
	$datalist[$length]['sku'] = '合计：';
	$datalist[$length]['totalquantity'] = 0;
	$datalist[$length]['totalprice'] = 0;
	$datalist[$length]['totalprocess'] = 0;
	$datalist[$length]['returnsum'] = 0;
	$datalist[$length]['returnprice'] = 0;
	$datalist[$length]['cost'] = 0;

	for ($i = 0; $i < $length; $i++) {
		$datalist[$length]['totalquantity'] += $datalist[$i]['totalquantity'];
		$datalist[$length]['totalprice'] += $datalist[$i]['totalprice'];
		$datalist[$length]['totalprocess'] += $datalist[$i]['totalprocess'];
		$datalist[$length]['returnsum'] += $datalist[$i]['returnsum'];
		$datalist[$length]['returnprice'] += $datalist[$i]['returnprice'];
		$datalist[$length]['cost'] += $datalist[$i]['cost'];

		if ($datalist[$i]['totalquantity']!=0) {
			$datalist[$i]['RMA'] = $datalist[$i]['returnsum']/$datalist[$i]['totalquantity'] *100;
			$datalist[$i]['RMA'] = number_format($datalist[$i]['RMA'], 2);
		}
		$datalist[$i]['RMA'] .= '%';
		if ($datalist[$i]['totalprice']!=0) {
			$datalist[$i]['RPA'] = $datalist[$i]['returnprice']/$datalist[$i]['totalprice'] *100;
			$datalist[$i]['RPA'] = number_format($datalist[$i]['RPA'], 2);
		}
		$datalist[$i]['RPA'] .= '%';
		$datalist[$i]['totalprice'] = round($datalist[$i]['totalprice'], 2);
		$datalist[$i]['returnprice'] = number_format($datalist[$i]['returnprice'], 2);
		$datalist[$i]['coin_code'] = "USD";

        $datalist[$i]['account_code']   = $getaccount_code[$datalist[$i]['sold_id']];

	}

	if ($datalist[$length]['totalquantity']!=0) {
		$datalist[$length]['RMA'] = $datalist[$length]['returnsum']/$datalist[$length]['totalquantity'] *100;
		$datalist[$length]['RMA'] = number_format($datalist[$length]['RMA'], 2);
	}
	$datalist[$length]['RMA'] .= '%';
	if ($datalist[$length]['totalprice']!=0) {
		$datalist[$length]['RPA'] = $datalist[$length]['returnprice']/$datalist[$length]['totalprice'] *100;
		$datalist[$length]['RPA'] = number_format($datalist[$length]['RPA'], 2);
	}
	$datalist[$length]['RPA'] .= '%';
	$datalist[$length]['totalprice'] = number_format($datalist[$length]['totalprice'], 2);
	$datalist[$length]['returnprice'] = number_format($datalist[$length]['returnprice'], 2);
    if (!empty($soldflag))
        $head_array = array('sku' => 'SKU', 'coin_code' => '币别', 'totalquantity' => '卖出产品总数', 'totalprice' => '订单销售总额', 'cost' => '产品成本(RMB)','account_code'=>'销售渠道');
    else
        $head_array = array('sku' => 'SKU', 'coin_code' => '币别', 'totalquantity' => '卖出产品总数', 'totalprice' => '订单销售总额', 'totalprocess' => '出仓订单总数', 'returnsum' => '退货总数', 'RMA' => '退货率', 'returnprice' => '退款金额', 'RPA' => '退款率', 'cost' => '产品成本(RMB)');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}
elseif ($detail == 'oldlist') {
	if ($sqlstr || $order) {

		$bannerstrarr[] = array('url'=>'index.php?action=report_sold_bysku&detail=output&order='.$order,'value'=>'导出数据');

		$asc_img 	= 'both_asc';
		$desc_img 	= 'both_desc';
		$none_img	= 'both_nonorder';
		$totalquantity_img 		= $none_img;
		$totalprice_img 		= $none_img;
		$totalprocess_img 		= $none_img;

		$totalquantity_desc 	= 'totalquantity_desc';
		$totalquantity_asc 		= 'totalquantity_asc';
		$totalprice_desc 		= 'totalprice_desc';
		$totalprice_asc 		= 'totalprice_asc';
		$totalprocess_desc 		= 'totalprocess_desc';
		$totalprocess_asc 		= 'totalprocess_asc';

		$t_orderslink = 'index.php?action=report_sold_bysku&detail=list';
		if($sku) {
			$t_orderslink .= '&sku='.$sku;
		}
		$orders = str_replace("_"," ", $order);
		if ($order) {
			$orders .= ',';
		}
		switch ($order) {
			case "totalquantity_asc":
				$totalquantity_img 		= $asc_img;
				break;
			case "totalprice_asc":
				$totalprice_img 		= $asc_img;
				break;
			case "totalprocess_asc":
				$totalprocess_img 		= $asc_img;
				break;
			case "totalquantity_desc":
				$totalquantity_img 		= $desc_img;
				break;
			case "totalprice_desc":
				$totalprice_img 		= $desc_img;
				break;
			case "totalprocess_desc":
				$totalprocess_img 		= $desc_img;
				break;

		}

		$InitPHP_conf['pageval'] = 20;
		$sqlstr1 = str_replace("cdate","p.cdate",$sqlstr);
		$process = $this->S->dao('process');
		$datalist = $process->get_total_process($sqlstr1, 'sku', $orders);

		/*if ($sold_way) {
			$sqlstr2 .= ' and sold_way like "%'.$sold_way.'%" ';
			$t_orderslink .= '&sold_way='.$sold_way;
		}*/
		if ($startTime) {
			$sqlstr2 .= ' and cdate>="'.$startTime.'"';
			$t_orderslink .= '&startTime='.$startTime;
		}
		if ($endTime) {
			$sqlstr2 .= ' and cdate<="'.$endTime.'" ';
			$t_orderslink .= '&endTime='.$endTime;
		}
		unset($InitPHP_conf['pageval']);
		foreach($datalist as &$val) {
			if ($val['sku']) {
				$sqlstr3 = $sqlstr2.' and sku="'.$val['sku'].'" ';
			}
			$temp = $process->get_sum_product_by_return_process($sqlstr3, 'sku');
			$val['RMA'] = $temp[0]['total'];
			if (!$val['RMA']) {
				$val['RMA'] = 0;
			}

			if ($val['totalquantity']!=0) {
				$val['RMA'] = $val['RMA']/$val['totalquantity'] *100;
				$val['RMA'] = number_format($val['RMA'], 2);
			}
			$val['RMA'] .= '%';

			$val['cost'] = $val['cost']*$val['totalquantity'];
			if ($val['coin_code'] != 'USD') {
				$val['cost'] = $this->C->service('exchange_rate')->change_usd($val['coin_code'],$val['cost']);
			}
			//$val['cost'] = number_format($val['cost'], 2);

			$val['coin_code'] = 'USD';

			$temp = $process->get_sum_price_by_return_process($sqlstr3, 'sku');
			$val['returnprice'] = $temp[0]['returnprice'];
			if (!$val['returnprice']) {
				$val['returnprice'] = '-';
			}

		}
		$InitPHP_conf['pageval'] = 20;
		$process->get_total_process($sqlstr1, 'sku', $orders);

		$t_orderslink .= '&order=';
	}


	$pageshow = array('order'=>$order);

	$this->V->mark(array('title'=>'SKU销售报表'));
	$tablewidth = '1370';
	$displayarr['sku'] = array('showname'=>'sku','width'=>'150');
	$displayarr['totalquantity'] = array('showname'=>'卖出产品总数','width'=>'150', 'orderlink_desc' => $t_orderslink.$totalquantity_desc, 'orderlink_asc'=>$t_orderslink.$totalquantity_asc, 'order_type'=>$totalquantity_img);
	$displayarr['totalprice'] = array('showname'=>'订单销售总额','width'=>'150',  'orderlink_desc' => $t_orderslink.$totalprice_desc, 'orderlink_asc'=>$t_orderslink.$totalprice_asc, 'order_type'=>$totalprice_img);
	$displayarr['totalprocess'] = array('showname'=>'订单总数','width'=>'100', 'orderlink_desc' => $t_orderslink.$totalprocess_desc, 'orderlink_asc'=>$t_orderslink.$totalprocess_asc, 'order_type'=>$totalprocess_img);
	$displayarr['returnsum'] = array('showname'=>'退货总数','width'=>'100');
	$displayarr['RMA'] = array('showname'=>'退货率','width'=>'80');
	$displayarr['returnprice'] = array('showname'=>'退款金额','width'=>'150');
	$displayarr['RPA'] = array('showname'=>'退款率','width'=>'80');
	$displayarr['cost'] = array('showname'=>'产品成本','width'=>'150');
	$displayarr['coin_code'] = array('showname'=>'币别','width'=>'60');
	$displayarr['sold_way'] = array('showname'=>'渠道','width'=>'200');
	$temp = 'pub_list';
}
elseif ($detail == 'oldoutput') {
	$orders = str_replace("_"," ", $order);
	if ($order) {
		$orders .= ',';
	}

	$filename = 'Report';
	if ($sold_way != '') {
		$sqlstr2 .= ' and sold_way like "%'.$sold_way.'%" ';
		$filename .= '_'.$sold_way;
	}
	if ($startTime) {
		$sqlstr2 .= ' and cdate>="'.$startTime.'"';
		$filename .= '_'.$startTime;
	}
	else {
		$filename .= '_0000-00-00';
	}
	if ($endTime) {
		$sqlstr2 .= ' and cdate<="'.$endTime.'" ';
		$filename .= '_'.$endTime;
	}
	else {
		$filename .= '_'.date('Y-m-d', time());
	}
	$sqlstr = str_replace("cdate","p.cdate",$sqlstr);
	$process = $this->S->dao('process');
	$datalist = $process->get_total_process($sqlstr, 'sku', $orders);
	unset($InitPHP_conf['pageval']);
	foreach($datalist as &$val) {
		if ($val['sku']) {
			$sqlstr3 = $sqlstr2.' and sku="'.$val['sku'].'" ';
		}
		$temp = $process->get_sum_product_by_return_process($sqlstr3, 'sku');
		$val['RMA'] = $temp[0]['total'];
		if (!$val['RMA']) {
			$val['RMA'] = 0;
		}

		if ($val['totalquantity']!=0) {
			$val['RMA'] = $val['RMA']/$val['totalquantity'] *100;
			$val['RMA'] = number_format($val['RMA'], 2);
		}
		$val['RMA'] .= '%';

		$val['cost'] = $val['cost']*$val['totalquantity'];
		if ($val['coin_code'] != 'USD') {
			$val['cost'] = $this->C->service('exchange_rate')->change_usd($val['coin_code'],$val['cost']);
		}
		$val['cost'] = number_format($val['cost'], 2);

		$val['coin_code'] = 'USD';

		$temp = $process->get_sum_price_by_return_process($sqlstr3, 'sku');
		$val['returnprice'] = $temp[0]['returnprice'];
		if (!$val['returnprice']) {
			$val['returnprice'] = '-';
		}

		/*if (!$sold_way) {
			$val['sold_way'] = 'ALL';
		}*/
	}

	$head_array = array('sku' => 'SKU', 'totalquantity' => '卖出产品总数', 'totalprice' => '订单销售总额', 'totalprocess' => '出仓订单总数', 'RMA' => '退货率', 'returnprice' => '退款金额', 'cost' => '产品成本', 'coin_code' => '币别', 'sold_way' => '渠道');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

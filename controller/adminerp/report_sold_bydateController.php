<?php
if ($detail == 'list') {
	$stypemu = array(
		'sold_way-a-'=>'&nbsp; &nbsp; 销售渠道：',
		'cdate-t-t'=>'&nbsp; &nbsp;日期：',
	);

    /*取得销售渠道下拉*/
    $soldway = $this->S->dao('sold_way')->getSoldWayList();
    $sold_wayarr	 = array(''=>'=请选择=');
    for($i = 0; $i < count($soldway); $i++){
        $sold_wayarr[$soldway[$i]['id']] = $soldway[$i]['wayname'];
    }
    
	/*导出报表，需要时删除注释*/
	//$bannerstr = '<button onclick=window.location="index.php?action=report_sold_bydate&detail=output&order='.$order.'&sold_way='.$sold_way.'&startTime='.$startTime.'&endTime='.$endTime.'">导出数据</button>';

	/*排序代码，需要是取消注释*/
	/*
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
	$t_orderslink = 'index.php?action=report_sold_bydate&detail=list';

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
	*/
	//$bannerstr = '<button onclick=window.location="index.php?action=report_sold_bydate&detail=output&order='.$order.'&sold_way='.$sold_way.'&startTime='.$startTime.'&endTime='.$endTime.'">导出数据</button>';
	/*若存在时间段查询-统计每页分页20条*/
	if ($startTime || $endTime) {
		$InitPHP_conf['pageval'] = 20;
	}
	/*若不存在时间段查询-统计最近30天*/
	else {
		$nowdate = date('Y-m-d', time());
		$lastdate = date('Y-m-d', time()-3600*24*30);
		$InitPHP_conf['pageval'] = 30;
		$sqlstr .= ' and cdate>="'.date('Y-m-d 00:00:00',strtotime($lastdate)).'" and cdate<="'.date('Y-m-d 23:59:59',strtotime($nowdate)).'" ';
	}
	if (!empty($sold_way)) {
		$soid_waysql = ' and sold_way like "%'.$sold_way.'%" ';
	}
	$process = $this->S->dao('process');
	$datalist = $process->get_total_process_by_date($soid_waysql,$sqlstr);

	/*统计数据
	 * $sumquantity		总计销售产品数
	 * $sumprice		总计销售额
	 * $sumprocess		总计订单数
	 * $sumreturn		总计退款数
	 * */
	$arrlenght = count($datalist);
	$sumquantity = 0;
	$sumprice = 0.00;
	$sumprocess = 0;
	$sumreturn = 0.00;


	/*搜索条件存在销售途径*/
	if ($sold_way) {		
		$t_orderslink .= '&sold_way='.$sold_way;
	}
	/*搜索条件存在开始日期*/
	if ($startTime) {
		$t_orderslink .= '&startTime='.$startTime;
	}
	/*搜索条件存在结束日期*/
	if ($endTime) {
		$t_orderslink .= '&endTime='.$endTime;
	}	

	foreach($datalist as &$val) {	

		$val['cdate'] = date('Y-m-d',strtotime($val['cdate']));

		/*币种*/
		$val['coin_code'] = 'USD';
		
		if ($val['returnprice'] == 0) {  
			$val['returnprice'] = '-';
		}

		/*订单平均销售额*/
		$val['pricebyprocess'] = $val['totalprice']/$val['totalprocess'];
		$val['pricebyprocess'] = number_format($val['pricebyprocess'], 2);

		/*订单平均销售数*/
		$val['quantitybyprocess'] = $val['totalquantity']/$val['totalprocess'];
		$val['quantitybyprocess'] = number_format($val['quantitybyprocess'], 2);

		/*产品平均金额*/
		$val['pricebyquantity'] = $val['totalprice']/$val['totalquantity'];
		$val['pricebyquantity'] = number_format($val['pricebyquantity'], 2);

		/*销售途径*/
		if (!$sold_way) {
			$val['sold_way'] = 'ALL';
		}

		/*统计销售产品数，销售总额，订单数*/
		$sumquantity += $val['totalquantity'];
		$sumprice += $val['totalprice'];
		$sumprocess += $val['totalprocess'];
		if ($val['returnprice']!= '-'){
			$sumreturn += $val['returnprice'];
		}
	}

	/*统计求订单平均销售额，订单平均销售数，产品平均金额*/
	$sumpricebyprocess = $sumprice/$sumprocess;
	$sumpricebyprocess = number_format($sumpricebyprocess, 2);
	$sumquantitybyprocess = $sumquantity/$sumprocess;
	$sumquantitybyprocess = number_format($sumquantitybyprocess, 2);
	$sumpricebyquantity = $sumprice/$sumquantity;
	$sumpricebyquantity = number_format($sumpricebyquantity, 2);
	$datalist[$arrlenght] = array(
		'cdate'	 			=> '总计',
		'totalquantity'		=> $sumquantity,
		'totalprice'			=> $sumprice,
		'totalprocess'		=> $sumprocess,
		'pricebyprocess'		=> $sumpricebyprocess,
		'quantitybyprocess'	=> $sumquantitybyprocess,
		'pricebyquantity'		=> $sumpricebyquantity,
		'returnprice'			=> $sumreturn,
		'coin_code'			=> 'USD',
		'sold_way'				=> $datalist[0]['sold_way']
	);
	
	$t_orderslink .= '&order=';

	$pageshow = array('order'=>$order);

	$this->V->mark(array('title'=>'时间段销售报表'));
	$tablewidth = '1300';
	$displayarr['cdate'] = array('showname'=>'日期','width'=>'80');
	$displayarr['totalprice'] = array('showname'=>'订单销售总额','width'=>'150'/*,  'orderlink_desc' => $t_orderslink.$totalprice_desc, 'orderlink_asc'=>$t_orderslink.$totalprice_asc, 'order_type'=>$totalprice_img*/);
	$displayarr['totalquantity'] = array('showname'=>'卖出产品总数','width'=>'120'/*, 'orderlink_desc' => $t_orderslink.$totalquantity_desc, 'orderlink_asc'=>$t_orderslink.$totalquantity_asc, 'order_type'=>$totalquantity_img*/);
	$displayarr['totalprocess'] = array('showname'=>'订单总数','width'=>'100'/*, 'orderlink_desc' => $t_orderslink.$totalprocess_desc, 'orderlink_asc'=>$t_orderslink.$totalprocess_asc, 'order_type'=>$totalprocess_img*/);
	$displayarr['pricebyprocess'] = array('showname'=>'订单平均销售额','width'=>'150');
	$displayarr['quantitybyprocess'] = array('showname'=>'订单平均销售数','width'=>'120');
	$displayarr['pricebyquantity'] = array('showname'=>'产品平均销售额','width'=>'120');
	$displayarr['returnprice'] = array('showname'=>'退款金额','width'=>'150');
	$displayarr['sold_way'] = array('showname'=>'渠道','width'=>'150');
	$displayarr['coin_code'] = array('showname'=>'币种','width'=>'60');
	$temp = 'pub_list';
}
elseif ($detail == 'output') {
	$nowdate = date('Y-m-d', time());
	$lastdate = date('Y-m-d', time()-3600*24*30);
	$sqlstr = '';
	$sqlstr .= ($sold_way == '') ? '' : ' and sold_way like "%'.$sold_way.'%" ';
	if ($startTime || $endTime) {
		$sqlstr .= empty($startTime)?'':' and p.cdate>="'.date('Y-m-d 00:00:00',strtotime($startTime)).'" ';
		$sqlstr .= empty($endTime)?'':' and p.cdate<="'.date('Y-m-d 23:59:59',strtotime($endTime)).'" ';
	}
	else {
		$sqlstr .= ' and p.cdate>="'.date('Y-m-d 00:00:00',strtotime($lastdate)).'" ';
		$sqlstr .= ' and p.cdate<="'.date('Y-m-d 23:59:59',strtotime($nowdate)).'" ';
	}

	$orders = str_replace("_"," ", $order);
	if ($order) {
		$orders .= ',';
	}

	/*报表文件名称*/
	$filename = 'Report';
	/*报表文件名称-存在渠道查找，显示统计渠道*/
	if ($sold_way != '') {
		$sqlstr2 .= ' and sold_way like "%'.$sold_way.'%" ';
		$filename .= '_'.$sold_way;
	}
	/*报表文件名称-存在时间查找，显示统计时间段*/
	if ($startTime || $endTime) {
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
	}
	else {
		$filename .= '_'.$lastdate;
		$filename .= '_'.$nowdate;
	}

	$process = $this->S->dao('process');
	$datalist = $process->get_total_process($sqlstr, 'TO_DAYS(p.cdate)', $orders);

	/*统计数据*/
	$arrlenght = count($datalist);
	$sumquantity = 0;
	$sumprice = 0.00;
	$sumprocess = 0;
	$sumreturn = 0.00;

	unset($InitPHP_conf['pageval']);
	foreach($datalist as &$val) {
		/*判定当前时间*/
		if ($val['cdate']) {
			$sqlstr3 = $sqlstr2.' and cdate>="'.date('Y-m-d 00:00:00',strtotime($val['cdate'])).'" and cdate<="'.date('Y-m-d 23:59:59',strtotime($val['cdate'])).'" ';
		}

		$val['cdate'] = date('Y-m-d',strtotime($val['cdate']));

		/*币种*/
		$val['coin_code'] = 'USD';

		/*退款金额*/
		$temp = $process->get_sum_price_by_return_process($sqlstr3, 'TO_DAYS(cdate)');
		$val['returnprice'] = $temp[0]['returnprice'];
		if (!$val['returnprice']) {
			$val['returnprice'] = '-';
		}

		/*订单平均销售额*/
		$val['pricebyprocess'] = $val['totalprice']/$val['totalprocess'];
		$val['pricebyprocess'] = number_format($val['pricebyprocess'], 2);

		/*订单平均销售数*/
		$val['quantitybyprocess'] = $val['totalquantity']/$val['totalprocess'];
		$val['quantitybyprocess'] = number_format($val['quantitybyprocess'], 2);

		/*产品平均金额*/
		$val['pricebyquantity'] = $val['totalprice']/$val['totalquantity'];
		$val['pricebyquantity'] = number_format($val['pricebyquantity'], 2);

		/*销售途径*/
		if (!$sold_way) {
			$val['sold_way'] = 'ALL';
		}

		/*统计销售产品数，销售总额，订单数*/
		$sumquantity += $val['totalquantity'];
		$sumprice += $val['totalprice'];
		$sumprocess += $val['totalprocess'];
		if ($val['returnprice']!= '-'){
			$sumreturn += $val['returnprice'];
		}
	}

	/*统计求订单平均销售额，订单平均销售数，产品平均金额*/
	$sumpricebyprocess = $sumprice/$sumprocess;
	$sumpricebyprocess = number_format($sumpricebyprocess, 2);
	$sumquantitybyprocess = $sumquantity/$sumprocess;
	$sumquantitybyprocess = number_format($sumquantitybyprocess, 2);
	$sumpricebyquantity = $sumprice/$sumquantity;
	$sumpricebyquantity = number_format($sumpricebyquantity, 2);
	$datalist[$arrlenght] = array(
		'cdate'	 			=> '总计',
		'totalquantity'		=> $sumquantity,
		'totalprice'			=> $sumprice,
		'totalprocess'		=> $sumprocess,
		'pricebyprocess'		=> $sumpricebyprocess,
		'quantitybyprocess'	=> $sumquantitybyprocess,
		'pricebyquantity'		=> $sumpricebyquantity,
		'returnprice'			=> $sumreturn,
		'coin_code'			=> 'USD',
		'sold_way'				=> $datalist[0]['sold_way']
	);

	$head_array = array('cdate'=>'日期', 'totalprice' => '订单销售总额', 'totalquantity' => '卖出产品总数', 'totalprocess' => '出仓订单总数', 'pricebyprocess' => '订单平均销售额', 'quantitybyprocess' => '订单平均销售数', 'pricebyquantity' => '产品平均销售额', 'returnprice' => '退款金额', 'sold_way' => '渠道', 'coin_code' => '币种');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

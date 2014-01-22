<?php
/**
 * @author Jerry
 * @title 销售售出报表，除去重发订单
 * @creat on 2013-10-22
 * 
 */



if ($detail == 'list') { 
    
	/*搜索选项*/
	$stypemu = array(
		'order_id-s-e'	=>'订单号：',
		'sku-s-l'	=>'&nbsp;&nbsp;&nbsp;&nbsp;SKU：',
		'sold_way-a-e'	=>'&nbsp;&nbsp;渠&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;道：',
        'sold_id-a-e'	=>'&nbsp;&nbsp;销售账号：',
		'cuser-s-l'	=>'<br>制单人：',
		'buyer_id-s-l'	=>'搜索号：',
		'cdate-t-t'	=>'订单日期：',
		'rdate-t-t'	=>'&nbsp;&nbsp;发货日期：',
        'provider_id-a-e'=>'<br>发出仓库：',
        'orders-b-'=>'',//订单和产品统计
	);
    
	$InitPHP_conf['pageval'] = 20; 
        
        /*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	$provider_idarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$provider_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}
    

	/*生成渠道搜索*/
	$sold_wayarr = $this->C->service('global')->get_sold_way(0,'sold_way','wayname');

   	/*取得销售账号下拉*/
	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','id,account_name');
	$sold_idarr		= array(''=>'=请选择=');
	for($i = 0; $i < count($soldaccount); $i++){
            $sold_idarr[$soldaccount[$i]['id']] = $soldaccount[$i]['account_name'];
	} 
        
	if ($sqlstr) {
	   
       //订单和产品统计
	   $datacount = $this->S->dao('process')->get_countsold($sqlstr); 
	   $ordersstr = "<font color=red>订单总数：".$datacount[0][0]."个&nbsp&nbsp;&nbsp;产品总数：".$datacount[0][1]."个</font>";
       
		/*导出数据按钮*/
		$bannerstrarr[] = array('url'=>'index.php?action=report_sold&detail=output','value'=>'导出数据');
		$datalist = $this->S->dao('process')->get_soldlist($sqlstr); 
		for($i=0;$i<count($datalist);$i++){
                    $datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);//将数组中的压缩内容解压并作为字段增加入数据中

                    $datalist[$i]['cdate'] 	= date('Y-m-d', strtotime($datalist[$i]['cdate']));
                    $datalist[$i]['rdate'] 	= date('Y-m-d', strtotime($datalist[$i]['rdate']));
                    $datalist[$i]['address'] = $datalist[$i]['e_address1'].'-'.$datalist[$i]['e_address2'];
                    $temparr = $this->S->dao('product')->D->select('shipping_weight','sku='.$datalist[$i]['sku']);
                    $datalist[$i]['weight'] = $temparr['shipping_weight'] * $datalist[$i]['quantity'];

                    $temparr = $this->S->dao('product_cost')->D->select('cost2,coin_code', 'pid='.$datalist[$i]['pid']);
                    $datalist[$i]['cost'] = $temparr['cost2']; 

                    if ($temparr['coin_code'] != 'USD') {
                            $datalist[$i]['cost'] = $this->C->service('exchange_rate')->change_usd($temparr['coin_code'],$datalist[$i]['cost']);
                    }

                    $datalist[$i]['total_cost'] = $datalist[$i]['cost'] * $datalist[$i]['quantity'];
                    $datalist[$i]['total_cost'] = number_format($datalist[$i]['total_cost'], 2);

                    $temparr = $this->S->dao('esse')->D->select('name', 'id='.$datalist[$i]['provider_id']);
                    $datalist[$i]['provider_id']= $temparr['name'];

                    if (!$datalist[$i]['e_shipping_price']) {
                            $datalist[$i]['e_shipping_price'] = '0.00';
                    }
                    if (!$datalist[$i]['e_performance_fee']) {
                            $datalist[$i]['e_performance_fee'] = '0.00';
                    }
                    if (!$datalist[$i]['e_shipping_fee']) {
                            $datalist[$i]['e_shipping_fee'] = '0.00';
                    }
                    /*应king要求改的*/
                    //产品收入=单价*数量
                    //$datalist[$i]['price']              = $datalist[$i]['price'];
                    //总收入=产品收入+运费
                    $datalist[$i]['total_price']        = $datalist[$i]['price'] + $datalist[$i]['e_shipping_price']; 
                    
                    $datalist[$i]['e_shipping_price']   = number_format($datalist[$i]['e_shipping_price'], 2);
                    $datalist[$i]['e_performance_fee']  = number_format($datalist[$i]['e_performance_fee'], 2);
                    $datalist[$i]['e_shipping_fee']     = number_format($datalist[$i]['e_shipping_fee'], 2);
                                      
                    
                    $datalist[$i]['sold_id'] = $datalist[$i]['sold_id']?$sold_idarr[$datalist[$i]['sold_id']]:'';
                    //同一个订单有多个产品的处理
                    $datalist[$i]['order_id'] = ($datalist[$i]['order_id'] == $datalist[$i-1]['order_id'])?'':$datalist[$i]['order_id'];
		}
	}

	$this->V->mark(array('title'=>'销售售出列表'));
	$tablewidth = '1900';
	$displayarr['cdate']                = array('showname'=>'订单日期','width'=>'100');
	$displayarr['rdate']                = array('showname'=>'发货日期','width'=>'120');
	$displayarr['order_id']             = array('showname'=>'订单号','width'=>'80');
	$displayarr['deal_id']              = array('showname'=>'平台单号','width'=>'100');
	$displayarr['fid']                  = array('showname'=>'第三方单号','width'=>'150');
	$displayarr['sku']                  = array('showname'=>'产品SKU','width'=>'100');
	$displayarr['product_name']         = array('showname'=>'产品名称','width'=>'250');
	$displayarr['quantity']             = array('showname'=>'数量','width'=>'50');
	$displayarr['price']                = array('showname'=>'产品收入','width'=>'120');
	$displayarr['e_shipping_price']     = array('showname'=>'运费收入','width'=>'150');
    $displayarr['total_price']          = array('showname'=>'总收入','width'=>'120');
	$displayarr['e_performance_fee']    = array('showname'=>'平台支出','width'=>'150');
	$displayarr['e_shipping_fee']       = array('showname'=>'终端运费支出','width'=>'150');
	$displayarr['cost']                 = array('showname'=>'单位产品成本','width'=>'150');
	$displayarr['total_cost']           = array('showname'=>'产品总成本','width'=>'150');
	$displayarr['e_shipping']           = array('showname'=>'发货方式','width'=>'120');
	$displayarr['sold_way']             = array('showname'=>'销售渠道','width'=>'120');
    $displayarr['sold_id']              = array('showname'=>'销售账号','width'=>'120');
	$displayarr['provider_id']          = array('showname'=>'发货仓库','width'=>'120');
	$displayarr['comment2']             = array('showname'=>'物流跟踪号','width'=>'120');
	$displayarr['weight']               = array('showname'=>'重量','width'=>'80');
	$displayarr['e_country']            = array('showname'=>'国家','width'=>'100');
	$displayarr['address']              = array('showname'=>'地址','width'=>'200');
	$displayarr['e_receperson']         = array('showname'=>'收件人','width'=>'100');
	$displayarr['e_tel']                = array('showname'=>'电话','width'=>'100');
	$displayarr['buyer_id']             = array('showname'=>'搜索号','width'=>'100');
	$displayarr['cuser']                = array('showname'=>'制单人','width'=>'80');
	$temp = 'pub_list';
}
elseif ($detail == 'output') { 
	$datalist = $this->S->dao('process')->get_soldlist($sqlstr); 
        $soldaccount = $this->S->dao('sold_account')->D->get_allstr('','','','id,account_name');
	for($i = 0; $i < count($soldaccount); $i++){
		$sold_idarr[$soldaccount[$i]['id']] = $soldaccount[$i]['account_name'];
	}

	for($i=0;$i<count($datalist);$i++){
		$datalist[$i] 								= $this->C->service('warehouse')->decodejson($datalist,$i);//将数组中的压缩内容解压并作为字段增加入数据中
		$datalist[$i]['cdate'] 						= date('Y-m-d', strtotime($datalist[$i]['cdate']));
		$datalist[$i]['rdate'] 						= date('Y-m-d', strtotime($datalist[$i]['rdate']));
		$datalist[$i]['address']					= $datalist[$i]['e_address1'].'-'.$datalist[$i]['e_address2'];
		$temparr 									= $this->S->dao('product')->D->select('shipping_weight','sku='.$datalist[$i]['sku']);
		$datalist[$i]['weight'] 					= $temparr['shipping_weight'] * $datalist[$i]['quantity'];

		$temparr 									= $this->S->dao('product_cost')->D->select('cost2,coin_code', 'pid='.$datalist[$i]['pid']);
		$datalist[$i]['cost'] 						= $temparr['cost2'];

		if ($temparr['coin_code']  != 'USD') {
			$datalist[$i]['cost'] 					= $this->C->service('exchange_rate')->change_usd($temparr['coin_code'],$datalist[$i]['cost']);
		}

		$datalist[$i]['total_cost'] 				= $datalist[$i]['cost'] * $datalist[$i]['quantity'];
		$datalist[$i]['total_cost'] 				= number_format($datalist[$i]['total_cost'], 2);

		$temparr 									= $this->S->dao('esse')->D->select('name', 'id='.$datalist[$i]['provider_id']);
		$datalist[$i]['provider_id']				= $temparr['name'];



		if (!$datalist[$i]['e_shipping_price']) {
			$datalist[$i]['e_shipping_price'] 	= '0.00';
		}
		if (!$datalist[$i]['e_performance_fee']) {
			$datalist[$i]['e_performance_fee'] 	= '0.00';
		}
		if (!$datalist[$i]['e_shipping_fee']) {
			$datalist[$i]['e_shipping_fee'] 	= '0.00';
		}

        /*应king要求改的*/
        //产品收入=单价*数量
        $datalist[$i]['price']              = $datalist[$i]['price']*$datalist[$i]['quantity'];
        //总收入=产品收入+运费
        $datalist[$i]['total_price']        = $datalist[$i]['price'] + $datalist[$i]['e_shipping_price']; 
                    

		$datalist[$i]['e_shipping_price'] 			= number_format($datalist[$i]['e_shipping_price'], 2);
		$datalist[$i]['e_performance_fee'] 			= number_format($datalist[$i]['e_performance_fee'], 2);
		$datalist[$i]['e_shipping_fee'] 			= number_format($datalist[$i]['e_shipping_fee'], 2);
        $datalist[$i]['sold_id']                    = $datalist[$i]['sold_id']?$sold_idarr[$datalist[$i]['sold_id']]:'';
	}

	$filename = 'Report_Sold_Detailed';
	$filename .= ($sold_way == '') ? '' : '_'.$datalist['0']['sold_way'];
	$filename .= ($cuser == '') ? '' : '_'.$cuser;

	$head_array = array(
					'cdate'				=> '订单日期',
					'rdate'				=> '发货日期',
					'order_id' 			=> '订单号',
					'deal_id'			=> '平台单号',
					'fid'			 	=> '第三方单号',
					'sku' 				=> '产品SKU',
					'product_name' 		=> '产品名称',
					'quantity' 			=> '数量',
					'price' 			=> '产品收入',
                    'total_price'       => '总收入',
					'e_shipping_price' 	=> '运费收入',
					'e_performance_fee' => 'paypal费',
					'e_shipping_fee' 	=> '终端运费支出',
					'cost' 				=> '单位产品成本',
					'total_cost' 		=> '产品总成本',
					'e_shipping' 		=> '发货方式',
					'sold_way' 			=> '销售渠道',
                    'sold_id'           => '销售账号',
					'provider_id' 		=> '发货仓库',
					'comment2' 			=> '物流跟踪号',
					'weight' 			=> '重量',
					'e_country' 		=> '国家',
					'address' 			=> '地址',
					'e_receperson'		=> '收件人',
					'e_tel'				=> '电话',
					'buyer_id' 			=> '搜索号',
					'cuser' 			=> '制单人'
				);
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);

}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

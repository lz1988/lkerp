<?php

/**
 * @title 订单信息处理报表
 * @atuohr Jerry
 * @create on 2013-1-22
 *
 */
 ini_set("memory_limit","500M");
 if ($detail == 'list') {

   	/*搜索选项*/
	$stypemu = array(
		'deal_sku-s-l'	=>'平台SKU：',
		'erp_sku-s-l'	=>'ERP &nbsp;SKU：',
		'deal_id-s-l'	=>'平台单号：',
		'fid-s-l'		=>'第三方单号：',
		'erp_pname-s-l'	=>'<br>中文描述：',
		'shipway-a-e'	=>'发&nbsp;货&nbsp;方&nbsp;式：',
		'address-a-e'	=>'账号代码：',
		'buyer_id-a-e'	=>'收&nbsp;款&nbsp;账&nbsp;号&nbsp;：',
		'date-t-t'		=>'日&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;期&nbsp;：',
		'cuser-s-e'		=>'&nbsp;&nbsp;&nbsp;制&nbsp;单&nbsp;人&nbsp;&nbsp;：',
	);

    /*获取统计的结果*/
    if($sqlstr){
	    $datalists = $this->S->dao('orders_detail')->report_sold_detaillist($sqlstr);
    }

  	/*取得发货方式下拉*/
	$shipping_mu	= $this->S->dao('shipping')->D->get_allstr('','','','s_name');
	$shipwayarr 	= array(''=>'=请选择=');
	for($i = 0; $i < count($shipping_mu); $i++){
		$shipwayarr[$shipping_mu[$i]['s_name']] = $shipping_mu[$i]['s_name'];
	}

	/*取得销售账号下拉*/
	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','account_name, account_code');
	$addressarr		= array(''=>'=请选择=');
	for($i = 0; $i < count($soldaccount); $i++){
		$addressarr[$soldaccount[$i]['account_name']] = $soldaccount[$i]['account_code'];
	}

	/*取得收款账号下拉*/
	$recaccountarr	= $this->S->dao('finance_payrec_account')->D->get_allstr('','','','payrec_account');
	$buyer_idarr	= array(''=>'=请选择=');
	for($i = 0; $i < count($recaccountarr); $i++){
		$buyer_idarr[$recaccountarr[$i]['payrec_account']] = $recaccountarr[$i]['payrec_account'];
	}

	$exservice	= $this->C->service('exchange_rate');
    $basefun	= $this->getLibrary('basefuns');

	/*有筛选条件才查结果*/
    if ($sqlstr){

	    $InitPHP_conf['pageval'] = 20;

		/*数据查询与处理*/
		$datalist = $this->S->dao('orders_detail')->report_sold_detaillist($sqlstr);


	    if ($datalist){
			foreach($datalist as &$val){
				$val['allsums']		=   $val['price'] + $val['shipprice'] + $val['shipfee'] + $val['amazonfee'] + $val['otherfee'] + $val['paypalfee'] + $val['ebayfee'];
                $val['cost']        =   !empty($val['cost1'])?$val['cost1']:'0.00';
                $val['cost']        =   number_format($val['cost'],2);
	            $val['price']       =   number_format($val['price']/$val['usd_rate']*100,2);
                $val['shipprice']   =   number_format($val['shipprice']/$val['usd_rate']*100,2);
                $val['shipfee']     =   number_format($val['shipfee']/$val['usd_rate']*100,2);
                $val['amazonfee']   =   number_format($val['amazonfee']/$val['usd_rate']*100,2);
                $val['otherfee']    =   number_format($val['otherfee']/$val['usd_rate']*100,2);
                $val['paypalfee']   =   number_format($val['paypalfee']/$val['usd_rate']*100,2);
                $val['ebayfee']     =   number_format($val['ebayfee']/$val['usd_rate']*100,2);
                $val['allsums']     =   number_format($val['allsums']/$val['usd_rate']*100,2);
                $val['account']     =   number_format($val['account']/$val['usd_rate']*100,2);

	            //单位产品成本
	            $val['total_cost'] = $val['quantity']*$val['cost'];
	            $val['total_cost'] = number_format($val['total_cost'],2);

	            //毛利润
	            $val['total_price'] =   $val['allsums'] - $val['total_cost'];
	            $val['total_price'] =   number_format($val['total_price'],2);

	            $val['currency']    =   'USD';

			}

	    	//统计
		    if (empty($page)){
		    	foreach($datalists as &$val){
					$val['allsums']		=   $val['price'] + $val['shipprice'] + $val['shipfee'] + $val['amazonfee'] + $val['otherfee'] + $val['paypalfee'] + $val['ebayfee'];
                    $val['cost']        =   !empty($val['cost1'])?$val['cost1']:'0.00';
                    $val['price']       =   $val['price']/$val['usd_rate']*100;
                    $val['shipprice']   =   $val['shipprice']/$val['usd_rate']*100;
                    $val['shipfee']     =   $val['shipfee']/$val['usd_rate']*100;
                    $val['amazonfee']   =   $val['amazonfee']/$val['usd_rate']*100;
                    $val['otherfee']    =   $val['otherfee']/$val['usd_rate']*100;
                    $val['paypalfee']   =   $val['paypalfee']/$val['usd_rate']*100;
                    $val['ebayfee']     =   $val['ebayfee']/$val['usd_rate']*100;
                    $val['allsums']     =   $val['allsums']/$val['usd_rate']*100;
                    $val['account']     =   $val['account']/$val['usd_rate']*100;
		            //单位产品成本
		            $val['total_cost']	= $val['quantity']*$val['cost1'];

		            //毛利润
		            $val['total_price'] =   $val['allsums'] - $val['total_cost'];


		            $val['currency']    =   'USD';

			        $sum_price      += $val['price'];
			        $sum_shipprice  += $val['shipprice'];
			        $sum_shipfee    += $val['shipfee'];
			        $sum_amazonfee  += $val['amazonfee'];
			        $sum_otherfee   += $val['otherfee'];
			        $sum_paypalfee  += $val['paypalfee'];
			        $sum_ebayfee    += $val['ebayfee'];
			        $sum_account    += $val['account'];
			        $sum_cost       += $val['cost'];
			        $sum_total_cost += $val['total_cost'];
			        $sum_allsums    += $val['allsums'];
			        $sum_total_price    += $val['total_price'];
			        $sum_count      += $val['quantity'];
			        $sum_currency   = 'USD';
		        }
                    $sum_price      = number_format($sum_price,2);
			        $sum_shipprice  = number_format($sum_shipprice,2);
			        $sum_shipfee    = number_format($sum_shipfee,2);
			        $sum_amazonfee  = number_format($sum_amazonfee,2);
			        $sum_otherfee   = number_format($sum_otherfee,2);
			        $sum_paypalfee  = number_format($sum_paypalfee,2);
			        $sum_ebayfee    = number_format($sum_ebayfee,2);
			        $sum_account    = number_format($sum_account,2);
			        $sum_cost       = number_format($sum_cost,2);
			        $sum_total_cost = number_format($sum_total_cost,2);
			        $sum_allsums    = number_format($sum_allsums,2);
			        $sum_total_price= number_format($sum_total_price,2);

			}

            //根据条件查询到的结果集，统计总数的时候，统计所有查询结果的总数，而不是统计当前页的总数。
		    if (isset($page)){
		        $datalist[] = array(
		            'erp_pname'=>'合计',
		            'quantity'=>'<font color="red">'.$_SESSION['sum_count'].'</font>',
		            'price'=>'<font color="red">'.$_SESSION['sum_price'].'</font>',
		            'shipprice'=>'<font color="red">'.$_SESSION['sum_shipprice'].'</font>',
		            'shipfee'=>'<font color="red">'.$_SESSION['sum_shipfee'].'</font>',
		            'amazonfee'=>'<font color="red">'.$_SESSION['sum_amazonfee'].'</font>',
		            'otherfee'=>'<font color="red">'.$_SESSION['sum_otherfee'].'</font>',
		            'paypalfee'=>'<font color="red">'.$_SESSION['sum_paypalfee'].'</font>',
		            'ebayfee'=>'<font color="red">'.$_SESSION['sum_ebayfee'].'</font>',
		            'account'=>'<font color="red">'.$_SESSION['sum_account'].'</font>',
		            'cost'=>'<font color="red">'.$_SESSION['sum_cost'].'</font>',
		            'total_cost'=>'<font color="red">'.$_SESSION['sum_total_cost'].'</font>',
		            'allsums'=>'<font color="red">'.$_SESSION['sum_allsums'].'</font>',
		            'total_price'=>'<font color="red">'.$_SESSION['sum_total_price'].'</font>',
                    'currency'=>'<font color="red">'.$_SESSION['sum_currency'].'</font>',
		        );
		    }else{
		        $basefun->setsession('sum_count',$sum_count);
		        $basefun->setsession('sum_price', $sum_price);
		        $basefun->setsession('sum_shipprice', $sum_shipprice);
		        $basefun->setsession('sum_shipfee', $sum_shipfee);
		        $basefun->setsession('sum_amazonfee', $sum_amazonfee);
		        $basefun->setsession('sum_otherfee', $sum_otherfee);
		        $basefun->setsession('sum_paypalfee', $sum_paypalfee);
		        $basefun->setsession('sum_ebayfee', $sum_ebayfee);
		        $basefun->setsession('sum_account', $sum_account);
		        $basefun->setsession('sum_cost', $sum_cost);
		        $basefun->setsession('sum_total_cost', $sum_total_cost);
		        $basefun->setsession('sum_allsums', $sum_allsums);
		        $basefun->setsession('sum_total_price', $sum_total_price);
		        $basefun->setsession('sum_currency', $sum_currency);
		        $datalist[] = array(
		            'erp_pname'=>'合计',
		            'quantity'=>'<font color="red">'.$sum_count.'</font>',
		            'price'=>'<font color="red">'.$sum_price.'</font>',
		            'shipprice'=>'<font color="red">'.$sum_shipprice.'</font>',
		            'shipfee'=>'<font color="red">'.$sum_shipfee.'</font>',
		            'amazonfee'=>'<font color="red">'.$sum_amazonfee.'</font>',
		            'otherfee'=>'<font color="red">'.$sum_otherfee.'</font>',
		            'paypalfee'=>'<font color="red">'.$sum_paypalfee.'</font>',
		            'ebayfee'=>'<font color="red">'.$sum_ebayfee.'</font>',
		            'account'=>'<font color="red">'.$sum_account.'</font>',
		            'cost'=>'<font color="red">'.$sum_cost.'</font>',
		            'total_cost'=>'<font color="red">'.$sum_total_cost.'</font>',
		            'allsums'=>'<font color="red">'.$sum_allsums.'</font>',
		            'total_price'=>'<font color="red">'.$sum_total_price.'</font>',
		            'currency'=>'<font color="red">'.$sum_currency.'</font>',
		        );
		    }
	    }
    }

	$displayarr 				= array();
	$displayarr['id'] 	 		= array('showname'=>'checkbox',	'width'=>'40','title'=>'反选');
	$displayarr['date']  		= array('showname'=>'日期',	'width'=>'100');
	$displayarr['deal_id'] 	 	= array('showname'=>'平台单号',	'width'=>'100');
	$displayarr['3rd_part_id']  = array('showname'=>'第三方单号','width'=>'110');
	$displayarr['deal_sku']  	= array('showname'=>'平台SKU',	'width'=>'80');
	$displayarr['erp_sku']  	= array('showname'=>'ERP SKU',	'width'=>'80');
	$displayarr['jin_sku']  	= array('showname'=>'金碟SKU',	'width'=>'80');
	$displayarr['deal_pname']  	= array('showname'=>'产品名称',	'width'=>'100');
	$displayarr['erp_pname']  	= array('showname'=>'中文描述','width'=>'80');
	$displayarr['quantity']  	= array('showname'=>'数量',	'width'=>'80');
	$displayarr['price']	  	= array('showname'=>'收入', 'width'=>'80');
	$displayarr['shipprice']  	= array('showname'=>'运费收入', 'width'=>'80');
	$displayarr['shipfee']  	= array('showname'=>'amazon代收运费', 'width'=>'140');
	$displayarr['amazonfee']  	= array('showname'=>'amazon fee', 'width'=>'140');
	$displayarr['otherfee']  	= array('showname'=>'其它平台费用', 'width'=>'140');
	$displayarr['paypalfee']  	= array('showname'=>'paypal费', 'width'=>'110');
	$displayarr['ebayfee']  	= array('showname'=>'ebay fee', 'width'=>'110');
    $displayarr['account']      = array('showname'=>'终端运费支出', 'width'=>'110');
    $displayarr['allsums']		= array('showname'=>'合计(USD)','width'=>'80');
    $displayarr['cost']         = array('showname'=>'单位产品成本', 'width'=>'110');
    $displayarr['total_cost']   = array('showname'=>'产品总成本', 'width'=>'110');
    $displayarr['total_price']  = array('showname'=>'毛利润','width'=>'80');
    $displayarr['currency']  	= array('showname'=>'币别',	'width'=>'80');
	$displayarr['shipway']  	= array('showname'=>'发货方式', 'width'=>'90');
	$displayarr['shiphouse']  	= array('showname'=>'发货仓库', 'width'=>'90');
	$displayarr['shipment_date']= array('showname'=>'发货时间', 'width'=>'90','clickedit'=>'id','detail'=>'editshipmentdata','width'=>'120');
	$displayarr['ship_country'] = array('showname'=>'国家', 'width'=>'80');
	$displayarr['account_code']	= array('showname'=>'账号代码', 'width'=>'80');
	$displayarr['buyer_id']  	= array('showname'=>'收款账号', 'width'=>'80');
	$displayarr['cuser']  		= array('showname'=>'制单人', 'width'=>'80');
	$displayarr['comment']  	= array('showname'=>'备注', 'width'=>'80');

	if($sqlstr){
	    $bannerstrarr[]	= array('url'=>'index.php?action=report_order_soldmod&detail=outport','value'=>'导出数据');
	}

	$jslink			= "<script src='./staticment/js/order_soldmod.js?version=".time()."'></script>\n";
	$this->V->mark(array('title'=>'销售分析明细表'));
	$temp 			= 'pub_list';
 }

  /*导出数据*/
 elseif($detail == 'outport'){

    $datalist	= $this->S->dao('orders_detail')->report_sold_detaillist($sqlstr);
	$exservice	= $this->C->service('exchange_rate');

	foreach($datalist as &$val){

		/*算出合计，并且转换USD*/
		$val['allsums']	= $val['price'] + $val['shipprice'] + $val['shipfee'] + $val['amazonfee'] + $val['otherfee'] + $val['paypalfee'] + $val['ebayfee'];
        $val['cost']        =   !empty($val['cost1'])?$val['cost1']:'0.00';
        $val['price']       =   $val['price']/$val['usd_rate']*100;
        $val['shipprice']   =   $val['shipprice']/$val['usd_rate']*100;
        $val['shipfee']     =   $val['shipfee']/$val['usd_rate']*100;
        $val['amazonfee']   =   $val['amazonfee']/$val['usd_rate']*100;
        $val['otherfee']    =   $val['otherfee']/$val['usd_rate']*100;
        $val['paypalfee']   =   $val['paypalfee']/$val['usd_rate']*100;
        $val['ebayfee']     =   $val['ebayfee']/$val['usd_rate']*100;
        $val['allsums']     =   $val['allsums']/$val['usd_rate']*100;
        $val['account']     =   $val['account']/$val['usd_rate']*100;

        $val['currency']    =   'USD';

        //单位产品成本
        $val['total_cost'] = $val['quantity']*$val['cost'];
        

        //毛利润
        $val['total_price'] =   $val['allsums'] - $val['total_cost'];

        $sum_price      += $val['price'];
        $sum_shipprice  += $val['shipprice'];
        $sum_shipfee    += $val['shipfee'];
        $sum_amazonfee  += $val['amazonfee'];
        $sum_otherfee   += $val['otherfee'];
        $sum_paypalfee  += $val['paypalfee'];
        $sum_ebayfee    += $val['ebayfee'];
        $sum_account    += $val['account'];
        $sum_cost       += $val['cost'];
        $sum_total_cost += $val['total_cost'];
        $sum_allsums    += $val['allsums'];
        $sum_total_price += $val['total_price'];
        $sum_count  += $val['quantity'];
        $sum_currency = 'USD';
        
        
        $val['cost']        =   number_format($val['cost'],2);
        $val['price']       =   number_format($val['price'],2);
        $val['shipprice']   =   number_format($val['shipprice'],2);
        $val['shipfee']     =   number_format($val['shipfee'],2);
        $val['amazonfee']   =   number_format($val['amazonfee'],2);
        $val['otherfee']    =   number_format($val['otherfee'],2);
        $val['paypalfee']   =   number_format($val['paypalfee'],2);
        $val['ebayfee']     =   number_format($val['ebayfee'],2);
        $val['allsums']     =   number_format($val['allsums'],2);
        $val['account']     =   number_format($val['account'],2);

        //单位产品成本
        $val['total_cost'] = number_format($val['total_cost'],2);
        //毛利润
        $val['total_price'] =   number_format($val['total_price'],2);

	}
 
        


    $datalist[] = array(
        'erp_pname'=>'合计',
        'quantity'		=>'<font color="red">'.$sum_count.'</font>',
        'price'			=>'<font color="red">'.number_format($sum_price,2).'</font>',
        'shipprice'		=>'<font color="red">'.number_format($sum_shipprice,2).'</font>',
        'shipfee'		=>'<font color="red">'.number_format($sum_shipfee,2).'</font>',
        'amazonfee'		=>'<font color="red">'.number_format($sum_amazonfee,2).'</font>',
        'otherfee'		=>'<font color="red">'.number_format($sum_otherfee,2).'</font>',
        'paypalfee'		=>'<font color="red">'.number_format($sum_paypalfee,2).'</font>',
        'ebayfee'		=>'<font color="red">'.number_format($sum_ebayfee,2).'</font>',
        'account'		=>'<font color="red">'.number_format($sum_account,2).'</font>',
        'cost'			=>'<font color="red">'.number_format($sum_cost,2).'</font>',
        'total_cost'	=>'<font color="red">'.number_format($sum_total_cost,2).'</font>',
        'allsums'		=>'<font color="red">'.number_format($sum_allsums,2).'</font>',
        'total_price'	=>'<font color="red">'.number_format($sum_total_price,2).'</font>',
        'currency'		=>'<font color="red">'.$sum_currency.'</font>',
    );

	$filename = 'report_order_details_'.date('Y-m-d H:i:s',time());
	$head_array = array(//与导入时表格一样
		'date'			=> 'date',
		'deal_id'		=> '平台订单号',
		'3rd_part_id'	=> '第三方单号',
		'deal_sku'		=> '平台SKU',
		'erp_sku'		=> 'ERP SKU',
		'jin_sku'		=> '金碟SKU',
		'deal_pname'	=> '产品名称',
		'erp_pname'		=> '中文描述',
		'quantity'		=> '数量',
		'price'			=> '收入',
		'shipprice'		=> '运费收入',
		'shipfee'		=> 'amazon代收运费',
		'amazonfee'		=> 'amazon fee',
		'otherfee'		=> '其它平台费用',
		'paypalfee'		=> 'paypal费',
		'ebayfee'		=> 'ebay fee',
        'account'       => '终端运费支出',
		'allsums'		=> '合计(USD)',
        'cost'          => '单位产品成本',
        'total_cost'    => '产品总成本',
        'total_price'   => '毛利润',
	    'currency'		=> '币别',
		'shipway'		=> '发货方式',
		'shiphouse'		=> '发货仓库',
		'shipment_date'	=> 'shipdate',
		'ship_country'	=> '国家',
		'account_code'	=> '账号代码',
		'buyer_id'		=> '收款账号',
		'cuser'			=> '制单人',
		'comment'		=> '备注',
	);
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
 }

if ($detail == 'list') {
    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>
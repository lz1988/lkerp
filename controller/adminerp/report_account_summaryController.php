<?php
/*
 * Created on 2013-1-29 by hanson
 * @title 销售帐号汇总表
 *
 */

 /*列表页*/
 if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'date-t-t' =>'日&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;期&nbsp;：',
        'checktime-b-'=>'审核期间：',
        'address-a-' =>'销售账号：'
	);
    
    /*取得销售账号下拉*/
    $soldaccount = $this->S->dao('sold_account')->D->get_allstr('','','','account_name,account_code');
    $addressarr	 = array(''=>'=请选择=');
    for($i = 0; $i < count($soldaccount); $i++){
        $addressarr[$soldaccount[$i]['account_name']] = $soldaccount[$i]['account_code'];
    }
    
    //审核日期文本搜索
    $checktimestr = "<input type=text name=checktime  class='find-T' onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$checktime." >";
    
	/*查出所有销售账号作为快捷栏*/
	$backaccount	= $this->S->dao('sold_account')->D->get_allstr();
    $accountstr     = '';
    foreach($backaccount as $val){
    	$accountstr.= '<span><a href=javascript:void(0);self.parent.addMenutab('.mt_rand(2000,3000).',"统计图表","index.php?action=report_account_summary&detail=chart&account_id='.$val['id'].'")   title="点击查看'.$val['account_code'].'账号近12月销售统计图表">'.$val['account_code'].'</a></span>';
    }

	$bannerstdiv	= '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:780px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
	$bannerstr		= '<style type="text/css">.qick span{padding:2px 5px 2px 5px;background:#ececec;margin-right:5px;cursor:pointer;color:#000;}</style><div style="background:#FFFFE5;border:1px solid #ffc674;font-size:12px;line-height:22px;color:#ff2a00;margin:10px 0;" class="qick">&nbsp;'.$accountstr.'</div>';

	/*日期无选择给予提示*/
	/*if(empty($startTime) || empty($endTime)){

		if(empty($startTime) && empty($endTime)){
			$bannerstr.= $bannerstdiv.'1、若查某时间段所有账号的订单信息汇总，请选择开始日期与截止日期。2、若查单个账号近12个月的销售额统计图，直接点击上面账号。';
		}elseif(empty($startTime) && !empty($endTime)){
			$bannerstr.= $bannerstdiv.'请选择开始日期！';
		}else{
			$bannerstr.= $bannerstdiv.'请选择截止日期！';
		}
		$bannerstr.= '</div>';*/
    
    if (empty($sqlstr) && empty($checktime) && empty($address)){
        $bannerstr.=$bannerstdiv.'请输入查询条件！';
        $bannerstr.= '</div>';
	}

	else{
	    $product    = $this->S->dao('product');
        $esse       = $this->S->dao('esse');
        $warehouseshipping = $this->S->dao('warehouseshipping');
        $orders_detail = $this->S->dao('orders_detail');
         
        if ($checktime){$sqlstr.=' and checktime="'.$checktime.'"';}
		/*汇总明细表数据*/
		$obj_orders_detail			= $this->S->dao('orders_detail');
        if (empty($address)){
		  $datalist					= $obj_orders_detail->get_report_sumary($sqlstr.' and statu="1" ');//订单信息汇总
        }else{
            //通过销售账号查询，列表金额显示原始币别
            $sqlstr .= ' and address="'.$address.'"';
            $datalist                = $obj_orders_detail->get_report_sumary_currency($sqlstr.' and statu="1"');   
        }
        
		$sum_price					= 0;//收入合计
	    $sum_shipprice  			= 0;//运费收入合计
	    $sum_shipfee    			= 0;//amazon代收的运费支出合计
	    $sum_amazonfee  			= 0;//amzon fee合计
	    $sum_otherfee   			= 0;//其它平台费用合计
	    $sum_paypalfee  			= 0;//paypal费合计
	    $sum_ebayfee    			= 0;//ebay fee合计
	    $sum_cost1					= 0;//成本合计
	    $sum_refund					= 0;//退款合计


		/*查退款汇总与组装数据*/
		$refundArr					= array();
		$R_sqlstr					= strtr($sqlstr,array('date'=>'cdate'));
        
        //列表退款显示原币别未转换金额
        if ($address)
            $datalist_refund        = $this->S->dao('orders_refund')->get_sumbyAccount_currency($R_sqlstr);
        else
		  $datalist_refund			= $this->S->dao('orders_refund')->get_sumbyAccount($R_sqlstr);
          
		if($datalist_refund){
			foreach($datalist_refund as $val){
				$refundArr[$val['account_name']]= $val['refund_total'];
				$refundArr['arr'][]				= $val['account_name'];
				$sum_refund					   += $val['refund_total'];//计算合计
			}
		}
        
        
        
		/*数据处理*/
		foreach($datalist as &$val){
		  
		   /************start计算仓库分摊运费***********************/
		   if (!empty($address)) $sumsqlstr  = $sqlstr;else $sumsqlstr = $sqlstr.' and address = "'.$val['address'].'"';
           $sumbyaccount_sku = $this->S->dao('orders_refund')->get_sumbyaccount_sku($sumsqlstr);
           $_sum_warehouse_shipping = 0;

           foreach($sumbyaccount_sku as $v)
           {
                //单个sku重量
                $sku_box_shipping_weight = $product->D->get_one_by_field(array('pid'=>$v['pid']),'box_shipping_weight');
              
                //获取当前sku对应的发送仓库id
                $_eid = $esse->D->get_one_by_field(array('type'=>'2','name'=>$v['shiphouse']),'id'); 
            
                //获取当前sku对应仓库的运费
                $_shipping = $warehouseshipping->D->get_one_by_field(array('warehouse'=>$_eid['id'],'checktime'=>$v['checktime']));
              
                //sku对应仓库和审核日期的总重
                $sql = 'shiphouse="'.$v['shiphouse'].'" and checktime ="'.$v['checktime'].'"';
                $sum_box_shipping_weight = $orders_detail->get_warehouse_shipping($sql); //sku总重量
                $box_shipping_weight = $sum_box_shipping_weight[0]['box_shipping_weight'];
               
                if ($box_shipping_weight > 0 )
                    $_sum_warehouse_shipping += ($v['quantity'] * $sku_box_shipping_weight['box_shipping_weight'] * $_shipping['shipping'])/$box_shipping_weight;
                      
            }
    
            $sum_shippfare += $_sum_warehouse_shipping;
            /************end****************************************/

			/*计算退款*/
			if(in_array($val['address'],$refundArr['arr'])){
				$val['refund']		= $refundArr[$val['address']];
			}

			/*计算利润*/
			$val['profit'] 			= $val['price'] + $val['shipprice'] + $val['shipfee'] + $val['amazonfee'] + $val['otherfee'] + $val['paypalfee'] + $val['ebayfee'] - $val['cost1'] + $val['refund'];
			$sum_price			   += $val['price'];
			$sum_shipprice		   += $val['shipprice'];
			$sum_shipfee		   += $val['shipfee'];
			$sum_amazonfee		   += $val['amazonfee'];
			$sum_otherfee		   += $val['otherfee'];
			$sum_paypalfee		   += $val['paypalfee'];
			$sum_ebayfee		   += $val['ebayfee'];
			$sum_cost1			   += $val['cost1'];

			/*毛利率*/
			$val['profit_rate']		= (number_format($val['profit']/$val['price'],2)*100).'%';

			/*减少误差，先算出利润，再将数据二位小数化*/
			$val['price']			= number_format($val['price'],2);
			$val['shipprice']		= number_format($val['shipprice'],2);
			$val['shipfee']			= number_format($val['shipfee'],2);
			$val['amazonfee']		= number_format($val['amazonfee'],2);
			$val['otherfee']		= number_format($val['otherfee'],2);
			$val['paypalfee']		= number_format($val['paypalfee'],2);
			$val['ebayfee']			= number_format($val['ebayfee'],2);
			$val['refund']			= number_format($val['refund'],2);
			$val['profit'] 			= number_format($val['profit'],2);
            $val['shippfare']       = number_format($_sum_warehouse_shipping,2);
            if (empty($address)) { $val['coin_code']		= 'USD';}else{ $val['coin_code'] = $val['currency'];}
            //因为导出不需要出现链接，特重写一个值
            $val['accountid']       = $val['account_code'];
			$val['account_code']	= '<a href=javascript:void(0);self.parent.addMenutab('.mt_rand(2000,3000).',"统计图表","index.php?action=report_account_summary&detail=chart&account_id='.$val['account_id'].'")   title="点击查看'.$val['account_code'].'账号近12月销售统计图表">'.$val['account_code'].'</a>';
		}

		/*利润与毛利率合计*/
		$sum_profit					= $sum_price + $sum_shipprice + $sum_shipfee + $sum_amazonfee + $sum_otherfee + $sum_paypalfee + $sum_ebayfee - $sum_cost1 + $sum_refund;
		$sum_profit_rate			= (number_format($sum_profit/$sum_price,2)*100).'%';

		/*无数据则提示*/
		if(empty($datalist)) {
			$bannerstr.= $bannerstdiv.'无数据！</div>';
		}else{
            //销售账号为空，才统计列表总计数据
            if (empty($address)){
    			$datalist[] = array(
    				'account_code'			=>'<font color="red">合计</font>',
                    'accountid'			    =>'<font color="red">合计</font>',
    				'price'					=>'<font color="red">'.number_format($sum_price,2).'</font>',
    				'shipprice'				=>'<font color="red">'.number_format($sum_shipprice,2).'</font>',
    				'shipfee'				=>'<font color="red">'.number_format($sum_shipfee,2).'</font>',
    				'amazonfee'				=>'<font color="red">'.number_format($sum_amazonfee,2).'</font>',
    				'otherfee'				=>'<font color="red">'.number_format($sum_otherfee,2).'</font>',
    				'paypalfee'				=>'<font color="red">'.number_format($sum_paypalfee,2).'</font>',
    				'ebayfee'				=>'<font color="red">'.number_format($sum_ebayfee,2).'</font>',
    				'cost1'					=>'<font color="red">'.number_format($sum_cost1,2).'</font>',
    				'shippfare' 			=>'<font color="red">'.number_format($sum_shippfare,2).'</font>',
    				'refund'				=>'<font color="red">'.number_format($sum_refund,2).'</font>',
    				'profit'				=>'<font color="red">'.number_format($sum_profit,2).'</font>',
    				'profit_rate'			=>'<font color="red">'.$sum_profit_rate.'</font>',
    				'coin_code'				=>'<font color="red">USD</font>',
    			);
            }
            $_SESSION['list_sums']	= $datalist;
			$bannerstr 			   .= '<button onclick="window.location=\'index.php?action=report_account_summary&detail=outport\'">导出数据</button>';
		}
	}

	$displayarr 				= array();
	$displayarr['account_code']	= array('showname'=>'账号','width'=>'100');
	$displayarr['price']		= array('showname'=>'收入','width'=>'80');
	$displayarr['shipprice']	= array('showname'=>'运费收入','width'=>'80');
	$displayarr['shipfee']		= array('showname'=>'amazon代收的运费支出','width'=>'180');
	$displayarr['amazonfee']	= array('showname'=>'amzon fee','width'=>'90');
	$displayarr['otherfee']		= array('showname'=>'其它平台费用','width'=>'100');
	$displayarr['paypalfee']	= array('showname'=>'paypal费','width'=>'80');
	$displayarr['ebayfee']		= array('showname'=>'ebay fee','width'=>'80');
	$displayarr['cost1']		= array('showname'=>'成本','width'=>'70');
	$displayarr['shippfare']	= array('showname'=>'运费','width'=>'60');
	$displayarr['refund']		= array('showname'=>'退款','width'=>'60');
	$displayarr['profit']		= array('showname'=>'利润','width'=>'80');
	$displayarr['profit_rate']	= array('showname'=>'毛利率','width'=>'90');
	$displayarr['coin_code']	= array('showname'=>'币别','width'=>'80');

 	$this->V->mark(array('title'=>'销售账号汇总表'));
	$temp = 'pub_list';

 }

 /*导出表格*/
 elseif($detail == 'outport'){

	$filename	= 'report_summary_'.date('Y-m-d',time());
	$head_array	= array('accountid'=>'账号','price'=>'收入','shipprice'=>'运费收入','shipfee'=>'amazon代收的运费支出','amazonfee'=>'amzon fee','otherfee'=>'其它平台费用','paypalfee'=>'paypal费','ebayfee'=>'ebay fee','cost1'=>'成本','shippfare'=>'运费','refund'=>'退款','profit'=>'利润','profit_rate'=>'毛利率','coin_code'=>'币别');
    
	$this->C->service('upload_excel')->download_excel($filename, $head_array, $_SESSION['list_sums']);

 }

 /*统计图表*/
 elseif($detail == 'chart'){

	/*读取该账号近12月的销售数据*/
	$backaccount	= $this->S->dao('sold_account')->D->get_one(array('id'=>$account_id),'account_name,account_code');

	/*查近12个月数据*/
	$limitMonth		= " AND date >= DATE_SUB(DATE_SUB(  DATE_FORMAT(NOW(),'%y-%m-%d'),INTERVAL(EXTRACT(DAY FROM NOW())-1)DAY  ) , INTERVAL 11 MONTH )";
	$backdata		= $this->S->dao('orders_detail')->get_near_summarysales(' and address ="'.$backaccount['account_name'].'" '.$limitMonth);
	$xmlcont		= "<graph caption='".$backaccount['account_code']."' subcaption='Monthly Sales Summary For the near 12 month' xAxisName='Year-Month' yAxisName='Sales' decimalPrecision='0' formatNumberScale='0' numberPrefix='$' showNames='1' showValues='1'  showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5' >";
	foreach($backdata as $val){
		$xmlcont.= "<set name='".$val['dates']."' value='".$val['prices']."' hoverText='' color='7E9382' />";
	}
	$xmlcont	   .= "</graph>";


	/*生成XML格式数据*/
	$xmlFileName = './data/xml/'.$account_id.'.xml';
	$file_handle = fopen($xmlFileName,'w+');
	fwrite($file_handle,$xmlcont);
	fclose($file_handle);

	/*输出页面*/
	$this->V->mark(array('title'=>'统计表','xmlfile'=>$account_id.'.xml','swType'=>'FCF_Column2D.swf','Type'=>'FCF_Line.swf','swAction'=>'report_account_summary','swDetail'=>'switch'));
	$this->V->set_tpl('adminweb/report_chart');
	display();

 }

 /*切换图表*/
 elseif($detail == 'switch'){

	$this->V->mark(array('title'=>'统计表','xmlfile'=>$xmlfile,'swType'=>$Type,'Type'=>$swType,'swAction'=>'report_account_summary','swDetail'=>'switch'));
	$this->V->set_tpl('adminweb/report_chart');
	display();

 }


 /*头尾部包含*/
 if ($detail == 'list' || $detail == 'chart' || $detail == 'switch'){
    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }

?>

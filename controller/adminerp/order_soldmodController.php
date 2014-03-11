<?php
/*
 * Created on 2012-9-14
 *
 * @title	生成财务所用最终销售表
 * @author	by hanson
 */

/*上传配置数组*/
$fieldarray = array(
    'detail'	=>array('A','B','C','D','E','F','G','H','I','J','K','L','M'),
    'detailnew'	=>array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'),
    'outbound'	=>array('A','I','N'),
    'allorder'	=>array('A','B','L','AB'),
    'final'		=>array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y'),
    'upload_dir'=>"./data/uploadexl/orders/",
    'reckoning'	=>array('A','B','C','D')//结帐明细
);
$upload_exl_service 	= $this->C->Service('upload_excel');
ini_set('memory_limit','1000M');

if($detail == 'list'){ 
    /*搜索选项，注意增加条件需要手动在导出按钮处加条件*/
    $stypemu = array(
        'statu-h-e'		=>'状态',
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
        'currency-a-'   =>'&nbsp;&nbsp;&nbsp;币&nbsp;&nbsp;别&nbsp;&nbsp;&nbsp;：',
        'checktime-b-'  =>'审&nbsp;核&nbsp;期&nbsp;间&nbsp;：',
        'fstcreate-t-t' =>'录入时间：',
    );
    
    //审核日期文本搜索
    $checktimestr = "<input type=text name=checktime  class='find-T' onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$checktime." >";
    
    /*用于导出按钮的传参处理*/
    $stykeys = array_keys($stypemu);
    $outputparam = '';
    foreach($stykeys as $val){
       $tik = substr($val, 0, strpos($val,'-'));
       if ($$tik !="") $outputparam.= '&'.$tik.'='.urlencode($$tik);
    }
    $outputparam.='&startTime='.$startTime.'&endTime='.$endTime.'&fstcreatestartTime='.$fstcreatestartTime.'&fstcreateendTime='.$fstcreateendTime;//时间特别处理
    
    /*取得发货方式下拉*/
    $shipping_mu	= $this->S->dao('shipping')->D->get_allstr('','','','s_name');
    $shipwayarr 	= array(''=>'=请选择=');
    for($i = 0; $i < count($shipping_mu); $i++){
        $shipwayarr[$shipping_mu[$i]['s_name']] = $shipping_mu[$i]['s_name'];
    }

    /*取得销售账号下拉*/
    $soldaccount = $this->S->dao('sold_account')->D->get_allstr('','','','account_name,account_code');
    $addressarr	 = array(''=>'=请选择=');
    for($i = 0; $i < count($soldaccount); $i++){
        $addressarr[$soldaccount[$i]['account_name']] = $soldaccount[$i]['account_code'];
    }

    /*取得收款账号下拉*/
    $recaccountarr	= $this->S->dao('finance_payrec_account')->D->get_allstr('','','','payrec_account');
    $buyer_idarr	= array(''=>'=请选择=');
    for($i = 0; $i < count($recaccountarr); $i++){
        $buyer_idarr[$recaccountarr[$i]['payrec_account']] = $recaccountarr[$i]['payrec_account'];
    } 
    
    //币别
    /*财务要求加上jerry*/
    $currencycount = array(array('id'=>'USD','code'=>'USD'));
    $currencyarr	 = array(''=>'=请选择=');
    for($i = 0; $i < count($currencycount); $i++){
        $currencyarr[$currencycount[$i]['id']] = $currencycount[$i]['code'];
    }
    
    
    
    /*初始打开默认显示未审核的*/
    if(empty($sqlstr) && !isset($statu)){$sqlstr.= ' and statu="0" '; $statu='0' ;}
 
    /*标签导航选项*/
    $tab_menu_stypemu = array(
            'statu-0'=>'未审核',
            'statu-1'=>'已审核',
    );
    //$sqlstr.= ' and statu="1" ';
    if ($checktime) $sqlstr.= ' and checktime="'.$checktime.'"';
    
    $sqlstr = strtr($sqlstr, array('fid'=>'3rd_part_id'));

    /*分页参数,默认15,注意放在statu处理之后,查表之前*/
    $showperhtml= $this->C->service('warehouse')->perpage_show_html(array('0'=>'15','1'=>'50','2'=>'200','3'=>'1000','4'=>'6000'),$selfval_set,$statu);

    /*本位币*/
    if (!empty($currency)) $de_coin = $currency; else $de_coin	= $this->C->service('global')->get_system_defaultcoin();
    $exservice	= $this->C->service('exchange_rate');
    $product    = $this->S->dao('product');
    $esse       = $this->S->dao('esse');
    $warehouseshipping = $this->S->dao('warehouseshipping');
    $orders_detail = $this->S->dao('orders_detail');

    /*数据查询与处理*/
    $datalist = $orders_detail->D->get_list($sqlstr);
    foreach($datalist as &$val){
         /*echo $sku_box_shipping_weight['box_shipping_weight']."eeee";
            echo $val['quantity']."wwww<br/>";
            echo $_shipping['shipping']."eeeee";
            echo $box_shipping_weight;*/
        
        /*仓库运费分摊，依据：对应订单的单个SKU重*该订单SKU数量*月份总运费/月份总重*/
         if ($statu == 1)
        {
            //单个sku重量
            $sku_box_shipping_weight = $product->D->get_one_by_field(array('sku'=>$val['erp_sku']),'shipping_weight');
            
            if($sku_box_shipping_weight['shipping_weight'] > 0){
                
                //获取当前sku对应的发送仓库id
                $_eid = $esse->D->get_one_by_field(array('type'=>'2','name'=>$val['shiphouse']),'id'); 
            
                //获取当前sku对应仓库的运费
                $_shipping = $warehouseshipping->D->get_one_by_field(array('warehouse'=>$_eid['id'],'checktime'=>$val['checktime']));
                //echo '<pre>';print_r($_shipping);
                //sku对应仓库和审核日期的总重
                $sql = 'shiphouse="'.$val['shiphouse'].'" and checktime ="'.$val['checktime'].'"';
                //echo $sql;
                $sum_box_shipping_weight = $orders_detail->get_warehouse_shipping($sql); //sku总重量
                //echo '<pre>';print_r($sum_box_shipping_weight);
                $box_shipping_weight = $sum_box_shipping_weight[0]['shipping_weight'];
              
                $val['warehouse_shipping'] = ($val['quantity'] * $sku_box_shipping_weight['shipping_weight'] * $_shipping['shipping'])/$box_shipping_weight;
            
                //仓库分摊运费，统一转化当前订单币别
                $val['warehouse_shipping'] = round($exservice->change_rate("USD", $val['currency'], $val['warehouse_shipping']),2);
            }else{
                $val['warehouse_shipping'] = '0.00';
            }
        }
        
        
         //成本
        $val['cost1'] = $val['cost1'] * $val['quantity'];
        
        //成本原币别为USD,为了统计合计，统一转化为该订单原币别
        $val['cost1']  = round($exservice->change_rate("USD", $val['currency'], $val['cost1']),2);
                
        //小计
        $val['xiaoji']	= $val['price'] + $val['shipprice'] + $val['shipfee'] + $val['amazonfee'] + $val['otherfee'] + $val['paypalfee'] + $val['ebayfee'];
        
         //合计
        $val['allsums'] = $val['xiaoji'] - $val['cost1'] - $val['warehouse_shipping'];
        
        if ($val['currency'] != $de_coin){
            $val['allsumscoin'] = number_format($exservice->change_rate($val['currency'], $de_coin, $val['allsums']), 2);
        }else{
            $val['allsumscoin'] = number_format($val['allsums'], 2);
        }
        
        if (!empty($currency) && $val['currency'] != $de_coin){ 
            //币别统一转化
            $val['price']       = number_format($exservice->change_rate($val['currency'], $de_coin, $val['price']), 2);
            $val['shipprice']   = number_format($exservice->change_rate($val['currency'], $de_coin, $val['shipprice']), 2);
            $val['shipfee']     = number_format($exservice->change_rate($val['currency'], $de_coin, $val['shipfee']), 2);
            $val['amazonfee']   = number_format($exservice->change_rate($val['currency'], $de_coin, $val['amazonfee']), 2);
            $val['otherfee']    = number_format($exservice->change_rate($val['currency'], $de_coin, $val['otherfee']), 2);
            $val['paypalfee']   = number_format($exservice->change_rate($val['currency'], $de_coin, $val['paypalfee']), 2);
            $val['ebayfee']     = number_format($exservice->change_rate($val['currency'], $de_coin, $val['ebayfee']), 2);
            $val['cost1']       = number_format($exservice->change_rate($val['currency'], $de_coin, $val['cost1']), 2);
            $val['allsums']     = number_format($exservice->change_rate($val['currency'], $de_coin, $val['allsums']),2);
            $val['xiaoji']      = number_format($exservice->change_rate($val['currency'], $de_coin, $val['xiaoji']),2);
            $val['warehouse_shipping'] = round($exservice->change_rate($val['currency'],$de_coin,$val['warehouse_shipping']),2);
		}
        
        $val['address'] = $addressarr[$val['address']];//替换成账号代码    
    }

    $displayarr = array();
    $displayarr['id'] 	 	= array('showname'=>'checkbox',	'width'=>'40','title'=>'反选');
    if($statu==0){
        $displayarr['delete']   = array('showname'=>'操作','width'=>'40','url'=>'index.php?action=order_soldmod&detail=delete&id={id}','ajax'=>1);
    }
    $displayarr['date']  	= array('showname'=>'日期',	'width'=>'100');
    $displayarr['deal_id'] 	= array('showname'=>'平台单号',	'width'=>'100');
    $displayarr['3rd_part_id']  = array('showname'=>'第三方单号','width'=>'110');
    $displayarr['deal_sku']  	= array('showname'=>'平台SKU',	'width'=>'80');
    $displayarr['erp_sku']  	= array('showname'=>'ERP SKU',	'width'=>'80');
    $displayarr['jin_sku']  	= array('showname'=>'金碟SKU',	'width'=>'80');
    $displayarr['deal_pname']  	= array('showname'=>'产品名称',	'width'=>'100');
    $displayarr['erp_pname']  	= array('showname'=>'中文描述','width'=>'80');
    $displayarr['quantity']  	= array('showname'=>'数量',	'width'=>'80');
    $displayarr['currency']  	= array('showname'=>'币别',	'width'=>'80');
    $displayarr['price']	    = array('showname'=>'收入', 'width'=>'80');
    $displayarr['shipprice']  	= array('showname'=>'运费收入', 'width'=>'80');
    $displayarr['shipfee']  	= array('showname'=>'amazon代收运费', 'width'=>'140');
    $displayarr['amazonfee']  	= array('showname'=>'amazon fee', 'width'=>'140');
    $displayarr['otherfee']  	= array('showname'=>'其它平台费用', 'width'=>'140');
    $displayarr['paypalfee']  	= array('showname'=>'paypal费', 'width'=>'110');
    $displayarr['ebayfee']  	= array('showname'=>'ebay fee', 'width'=>'110');
    if ($statu ==1){
        $displayarr['xiaoji']               = array('showname'=>'小计','width'=>'110'); 
        $displayarr['warehouse_shipping']   = array('showname'=>'分摊仓库运费','width'=>'110');
    }
    $displayarr['cost1']  	     = array('showname'=>'成本', 'width'=>'110');
    $displayarr['allsums']	= array('showname'=>'合计','width'=>'80');
    $displayarr['allsumscoin']	= array('showname'=>'合计('.$de_coin.')','width'=>'100');
    $displayarr['shipway']  	= array('showname'=>'发货方式', 'width'=>'90');
    $displayarr['shiphouse']  	= array('showname'=>'发货仓库', 'width'=>'90');
    $displayarr['shipment_date']= array('showname'=>'发货时间', 'width'=>'90','clickedit'=>'id','detail'=>'editshipmentdata','width'=>'120');
    $displayarr['ship_country'] = array('showname'=>'国家', 'width'=>'80');
    $displayarr['address']  	= array('showname'=>'账号代码', 'width'=>'80');
    $displayarr['buyer_id']  	= array('showname'=>'收款账号', 'width'=>'80');
    $displayarr['cuser']  	= array('showname'=>'制单人', 'width'=>'80');
    $displayarr['comment']  	= array('showname'=>'备注', 'width'=>'80'); 
    $displayarr['checktime']    = array('showname'=>'审核期间','width'=>'80');
    $displayarr['fstcreate']    = array('showname'=>'录入时间','width'=>'100');
    /*数据流操作按钮*/
    $this->C->service('global')->disconnect_modbutton(array('0'=>&$mod_disabled_0,'1'=>&$mod_disabled_1),$statu);
    $bannerstr	= '<button onclick=window.location="index.php?action=order_soldmod&detail=import">导入表格</button>';
    $bannerstr .= '<button onclick=window.location="index.php?action=order_soldmod&detail=outport'.$outputparam.'">导出数据</button>';
    $bannerstr .= '<button onclick=audit("1") '.$mod_disabled_0.' >审核选中</button>';
    $bannerstr .= '<button onclick=audit("0") '.$mod_disabled_1.' >反审选中</button>';
    $bannerstr .= $showperhtml;
    $jslink = "<script src='./staticment/js/order_soldmod.js?version=".time()."'></script>\n";
    $this->V->mark(array('title'=>'订单数据列表'));
    $temp = 'pub_list';
 }
 
/*删除*/
else if($detail == 'delete'){
    if(!$this->C->service('admin_access')->checkResRight('order_soldmod_del')){$this->C->ajaxmsg(0);}
    $detaildata = $this->S->dao('orders_detail')->D->get_one(array('id'=>$id),'cuser');
    if($detaildata == $_SESSION['eng_name']){ 
        if($this->S->dao('orders_detail')->D->delete(array('id'=>$id))){$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0,'删除失败！');}
    }else{
        $this->C->ajaxmsg(0,'只能删除自己做的单！');
    } 
}
 /*修改发货时间*/
 elseif($detail == 'editshipmentdata'){
	if($this->S->dao('orders_detail')->D->update_by_field(array('id'=>$id),array('shipment_date'=>$shipment_date))){echo '1';}
 }

 /*导出数据*/
 elseif($detail == 'outport'){

 	/*初始打开默认显示未审核的*/
	if(empty($sqlstr) && !isset($statu)){ $sqlstr.= ' and statu="0" '; $statu='0' ;}
    //$sqlstr.= ' and statu="1" ';
    if ($checktime) $sqlstr.= ' and checktime="'.$checktime.'"';
	$sqlstr = strtr($sqlstr, array('fid'=>'3rd_part_id'));
	$datalist = $this->S->dao('orders_detail')->D->get_allstr($sqlstr);

	/*本位币*/
    if (!empty($currency)) $de_coin = $currency;else $de_coin	= $this->C->service('global')->get_system_defaultcoin();
	$exservice	= $this->C->service('exchange_rate');
    
    $product    = $this->S->dao('product');
    $esse       = $this->S->dao('esse');
    $warehouseshipping = $this->S->dao('warehouseshipping');
    $orders_detail = $this->S->dao('orders_detail');

	/*取得销售账号下拉*/
	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','account_name,account_code');
	$addressarr		= array();
	for($i = 0; $i < count($soldaccount); $i++){
		$addressarr[$soldaccount[$i]['account_name']]		= $soldaccount[$i]['account_code'];
	}

	foreach($datalist as &$val){
	   
       /*仓库运费分摊，依据：对应订单的单个SKU重*该订单SKU数量*月份总运费/月份总重*/
         if ($statu == 1)
        {
            //单个sku重量
            $sku_box_shipping_weight = $product->D->get_one_by_field(array('sku'=>$val['erp_sku']),'shipping_weight');
            
            if($sku_box_shipping_weight['shipping_weight'] > 0){
            
                //获取当前sku对应的发送仓库id
                $_eid = $esse->D->get_one_by_field(array('type'=>'2','name'=>$val['shiphouse']),'id'); 
            
                //获取当前sku对应仓库的运费
                $_shipping = $warehouseshipping->D->get_one_by_field(array('warehouse'=>$_eid['id'],'checktime'=>$val['checktime']));
              
                //sku对应仓库和审核日期的总重
                $sql = 'shiphouse="'.$val['shiphouse'].'" and checktime ="'.$val['checktime'].'"';
                $sum_box_shipping_weight = $orders_detail->get_warehouse_shipping($sql); 
                $box_shipping_weight = $sum_box_shipping_weight[0]['shipping_weight'];
              
                $val['warehouse_shipping'] = ($val['quantity'] * $sku_box_shipping_weight['shipping_weight'] * $_shipping['shipping'])/$box_shipping_weight;
                
                //仓库分摊运费，统一转化当前订单币别
                $val['warehouse_shipping'] = round($exservice->change_rate("USD", $val['currency'], $val['warehouse_shipping']),2);
            }else{
                $val['warehouse_shipping'] = '0.00';
            }
        }
	          
        //成本
        $val['cost1'] = $val['cost1'] * $val['quantity'];
        
        //成本原币别为USD,为了统计合计，统一转化为该订单原币别
        $val['cost1']  = round($exservice->change_rate("USD", $val['currency'], $val['cost1']),2);
                
        //小计
        $val['xiaoji']	= $val['price'] + $val['shipprice'] + $val['shipfee'] + $val['amazonfee'] + $val['otherfee'] + $val['paypalfee'] + $val['ebayfee'];
        
         //合计
        $val['allsums'] = $val['xiaoji'] - $val['cost1'] - $val['warehouse_shipping'];
        
        if ($val['currency'] != $de_coin){
            $val['allsumscoin'] = number_format($exservice->change_rate($val['currency'], $de_coin, $val['allsums']), 2);
        }else{
            $val['allsumscoin'] = number_format($val['allsums'], 2);
        }
         
        if (!empty($currency) && $val['currency'] != $de_coin){ 
            //币别统一转化
            $val['price']       = number_format($exservice->change_rate($val['currency'], $de_coin, $val['price']), 2);
            $val['shipprice']   = number_format($exservice->change_rate($val['currency'], $de_coin, $val['shipprice']), 2);
            $val['shipfee']     = number_format($exservice->change_rate($val['currency'], $de_coin, $val['shipfee']), 2);
            $val['amazonfee']   = number_format($exservice->change_rate($val['currency'], $de_coin, $val['amazonfee']), 2);
            $val['otherfee']    = number_format($exservice->change_rate($val['currency'], $de_coin, $val['otherfee']), 2);
            $val['paypalfee']   = number_format($exservice->change_rate($val['currency'], $de_coin, $val['paypalfee']), 2);
            $val['ebayfee']     = number_format($exservice->change_rate($val['currency'], $de_coin, $val['ebayfee']), 2);
            $val['cost1']       = number_format($exservice->change_rate($val['currency'], $de_coin, $val['cost1']), 2);
            $val['allsums']     = number_format($exservice->change_rate($val['currency'], $de_coin, $val['allsums']),2);
            $val['xiaoji']      = number_format($exservice->change_rate($val['currency'], $de_coin, $val['xiaoji']),2);
            $val['warehouse_shipping'] = round($exservice->change_rate($val['currency'],$de_coin,$val['warehouse_shipping']),2);
		}
		$val['address']	= $addressarr[$val['address']];//替换成账号代码
	}

	$filename = 'order_details_'.date('Y-m-d H:i:s',time());
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
		'currency'		=> '币别',
		'price'			=> '收入',
		'shipprice'		=> '运费收入',
		'shipfee'		=> 'amazon代收运费',
		'amazonfee'		=> 'amazon fee',
		'otherfee'		=> '其它平台费用',
		'paypalfee'		=> 'paypal费',
		'ebayfee'		=> 'ebay fee',
        'xiaoji'        =>'小计',
        'warehouse_shipping'=>'分摊仓库运费',
        'cost1'         =>'成本',
		'allsums'		=> '合计',
		'allsumscoin'	=> '合计('.$de_coin.')',
		'shipway'		=> '发货方式',
		'shiphouse'		=> '发货仓库',
		'shipment_date'	=> 'shipdate',
		'ship_country'	=> '国家',
		'address'		=> '账号代码',
		'buyer_id'		=> '收款账号',
		'cuser'			=> '制单人',
		'comment'		=> '备注',
	);
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

/*审核与反审*/
elseif($detail == 'audit'){
  if($statu == 1){
	  if(!$this->C->service('admin_access')->checkResRight('r_t_soldmod')){$this->C->ajaxmsg(0);}//审核权限判断
  }elseif($statu == 0){
  	  if(!$this->C->service('admin_access')->checkResRight('r_t_backsoldmod')){$this->C->ajaxmsg(0);}//审核权限判断
  }

  $sid = $this->S->dao('orders_detail')->D->update(' and id in('.stripslashes($strid).')',array('statu'=>$statu,'checktime'=>$checktime));
  if($sid) {
	echo '1';
  }else{
  	echo '操作失败';
  }

}

/*选择类型界面*/
elseif($detail == 'import'){
    if(!$this->C->service('admin_access')->checkResRight('r_p_add_detailorders')){$this->C->sendmsg();}//权限判断
        $bannerstr  = '<div style="color: #FF2A00;font-size:12px;background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;width:650px; line-height:22px;padding-left:25px;margin-top:10px; margin-bottom:10px">';
        $bannerstr .= '提醒：该页面的表格操作已经大大优化，可导入更多内容的表格不提示溢出错误(约2W行)，生成的表格也无需另存为表格格式，可直接用于重复导入。</div>';
        $bannerstr .= '<br><button class="eight" onclick=window.location="index.php?action=order_soldmod&detail=import_detail">原始表格导入</button><br><font color=#bdbdbd size=-1> &nbsp;导入三个原始表格，系统将原明细表中的退款订单筛选出来，再结合另外两表，生成新的明细表与退款表供下载。</font>';
        $bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=order_soldmod&detail=import_detail_new">新明细表导入</button><br><font color=#bdbdbd size=-1>  &nbsp;导入上一步生成的新明细表，系统处理后将生成最终的销售明细表。</font>';
        $bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=order_soldmod&detail=import_detail_final">最终明细表导入</button><br><font color=#bdbdbd size=-1>  &nbsp;导入经过整理生成的最终销售明细表，保存到系统。</font>';
        $bannerstr .= '<br><br><br><br><br><br><br><br><button class="eight" onclick=window.location="index.php?action=order_soldmod&detail=import_details">新三表导入</button><br><font color=#bdbdbd size=-1>  &nbsp;销售明细表(新格式)、出库表、allorder导入。</font>';
        $bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=order_soldmod&detail=import_detail_news">明细表导入</button><br><font color=#bdbdbd size=-1>  &nbsp;导入经过上一步处理过，补充了第三方单号、发货时间、国家的明细表，系统处理后将生成最终的销售明细表。</font>';
        $bannerstr .= '<br><br><button class="eight" style=" text-align:left; width:117px;" onclick=window.location="index.php?action=order_soldmod&detail=import_detail_new_final">米悠新明细表导入</button><br><font color=#bdbdbd size=-1>  &nbsp;导入两个原始表，结帐明细表和出库表进行合并生成最终明细表供下载。</font>';
        $this->V->mark(array('title'=>'导入表格-订单数据列表(list)'));
        $temp = 'pub_list';
}
 
 /*导入明细表(新格式的)、all order表、出库表*/
 elseif($detail == 'import_details'){

	$data_error				= 0;
	$show_data				= '';
	$tablelist 		    	= '<table id="mytable">';

	/*获得明细表*/
	if($_FILES["upload_file3"]["name"]){

		/*上传并获得数据*/
		$all_arr_detail		=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['detailnew'], 1,3);
		$all_arr_outbound	=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['outbound'], 1,2);
		$all_arr_allorder	=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['allorder'], 1);


		/*表头特殊显示处理*/
		$tablelist_h	   .= $upload_exl_service->checkmod_head(&$all_arr_detail,&$data_error,'order_detailnew');
		$tablelist_h	   .= $upload_exl_service->checkmod_head(&$all_arr_outbound,&$data_error,'order_outbound');
		$tablelist_h	   .= $upload_exl_service->checkmod_head(&$all_arr_allorder,&$data_error,'order_allorder');

	}

	$message_upload 		= ' &nbsp;<font size=-1>明&nbsp; 细&nbsp; 表：</font> <input name="upload_file3" type="file" /><br><br>';
	$message_upload	       .= ' &nbsp;<font size=-1>出&nbsp; 库&nbsp; 表：</font> <input name="upload_file2" type="file" /><br><br>';
	$message_upload	       .= ' &nbsp;<font size=-1>allorder表：</font>';

	if(!$data_error && $all_arr_detail){
		$show_data		    = '<div style="color: #FF2A00;font-size:12px;background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;width:450px; line-height:22px;padding-left:25px;margin-top:10px; margin-bottom:10px">';
		$show_data		   .= '上传成功，请点击下面的链接下载需要的表格。<br>新生成的明细表（①处下载），需要另存为表格格式才能用于再次导入。</div>';
		$show_data 	       .= '<span style="font-size:12px;text-decoration:underline;"><a href="index.php?action=order_soldmod&detail=download_newdetail_new" target="_blank">①下载新整理的明细表</a></span>&nbsp; &nbsp; ';
		$show_data 	       .= '<span style="font-size:12px;text-decoration:underline;"><a href="index.php?action=order_soldmod&detail=download_refound_new" target="_blank">②下载筛选的退款表</a></span>';

	}

	/*如果有错误表头，则显示*/
	elseif($data_error){
		$tablelist		   .= $tablelist_h;
		$exl_error_msg		= '有错误表头，请查看红色提示修改！';
	}

	$tablelist			   .= $show_data.'<tr></tr></table>';

	unset($all_arr_detail,$all_arr_outbound,$all_arr_allorder);
	$this->V->set_tpl('adminweb/commom_excel_import');

	$this->V->mark(array('title'=>'新三表导入-导入表格(import)-订单数据列表(list)','message_upload'=>$message_upload,'tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
 	display();

 }

 /*新格式明细表*/
 elseif($detail == 'download_newdetail_new' || $detail == 'download_refound_new'){

 	$all_arr_detail			= $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath3'], $fieldarray['detailnew'],   1, 3);
	$all_arr_outbound		= $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath2'], $fieldarray['outbound'], 1, 2);
	$all_arr_allorder		= $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath'] , $fieldarray['allorder'], 1);

	unset($all_arr_detail['0'],$all_arr_outbound['0'],$all_arr_allorder['0']);//除掉表头

 	/*开始数据处理，补全明细表的国家，发货时间，第三方单号*/
	if($all_arr_detail){

		$order_maps = $this->S->dao('order_mapp');
		$errornum	= 0;

		/*循环明细主表--Start*/
		foreach($all_arr_detail as $key=>&$vald){

			/*导新明细表需要做的处理*/
			if($detail == 'download_newdetail_new'){

				if($vald['type'] != 'Order') {
					unset($all_arr_detail[$key]);//除掉非Order类型的
				}elseif(!empty($vald['order id'])){

					/*第三方单号、国家、发货日期有一项为空都继续匹配*/
					if(empty($vald['3rd_part_id']) || empty($vald['ship country']) || empty($vald['shipment date'])){

						/*如果订单号和SKU已有存在的，则直接赋值，避免重复查找*/
						if($this_same[$vald['order id']][$vald['sku']]){

							$vald['3rd_part_id'] 	= $this_same[$vald['order id']][$vald['sku']]['3rd_part_id']?$this_same[$vald['order id']][$vald['sku']]['3rd_part_id']:$vald['3rd_part_id'];
							$vald['ship country'] 	= $this_same[$vald['order id']][$vald['sku']]['ship country']?$this_same[$vald['order id']][$vald['sku']]['ship country']:$vald['ship country'];
							$vald['shipment date'] 	= $this_same[$vald['order id']][$vald['sku']]['shipment date']?$this_same[$vald['order id']][$vald['sku']]['shipment date']:$vald['shipment date'];
						}

						elseif($this_same_emsku[$vald['order id']]){
							$vald['sku']			= $this_same_emsku[$vald['order id']]['sku']?$this_same_emsku[$vald['order id']]['sku']:$vald['sku'];
							$vald['3rd_part_id'] 	= $this_same_emsku[$vald['order id']]['3rd_part_id']?$this_same_emsku[$vald['order id']]['3rd_part_id']:$vald['3rd_part_id'];
							$vald['ship country'] 	= $this_same_emsku[$vald['order id']]['ship country']?$this_same_emsku[$vald['order id']]['ship country']:$vald['ship country'];
							$vald['shipment date'] 	= $this_same_emsku[$vald['order id']]['shipment date']?$this_same_emsku[$vald['order id']]['shipment date']:$vald['shipment date'];
						}

						else{

							if($all_arr_allorder){//有导all_order表

								/*循环表ALL ORDER--取家家地区，取第三方单号*/
								foreach($all_arr_allorder as $vala){

									/*匹配条件，(订单号与SKU组合)或(订单号与空SKU-手动发货导出的表SKU为空)*/
									if( ($vald['order id'] == $vala['amazon-order-id'] && $vald['sku'] == $vala['sku']) || ($vald['order id'] == $vala['amazon-order-id'] && empty($vald['sku'])) ){

										$vald['ship country'] = $vala['ship-country'];//取国家地区

										/*allorder表第三方单号不为空则取allorder的第三方单号*/
										if(!empty($vala['merchant-order-id'])){
											$vald['3rd_part_id'] = $vala['merchant-order-id'];
										}

										/*allorder表第三方单号为空则匹配ERP的映射表中的第三方单号*/
										else{
											$backfid = $order_maps->D->get_one_by_field(array('order_default'=>$vald['Order ID']),'order_trd');
											$vald['3rd_part_id'] = $backfid['order_trd'];
										}

										/*防止重复查找，并且假若SKU为空补全SKU*/
										$sameArr	 = array('3rd_part_id'=>$vald['3rd_part_id'],'ship country'=>$vald['ship country'],'order id'=>$vald['order id']);
										if(empty($vald['sku'])){$vald['sku'] = $sameArr['sku'] = $vala['sku'];$this_same_emsku[$vald['order id']] = $sameArr;	}else{	$this_same[$vald['order id']][$vald['sku']] = $sameArr;}

									}
								}
								/*循环结束*/
							}

							if($all_arr_outbound){//有导出库表

								/*循环出库表outbound--取发货时间*/
								foreach($all_arr_outbound as $valo){
									if( ($vald['order id'] == $valo['amazon-order-id'] && $vald['sku'] == $valo['sku']) || ($vald['order id'] == $valo['amazon-order-id'] && empty($vald['sku'])) ){
										$vald['shipment date'] = date('Y-m-d',strtotime($valo['shipment-date'])-8*3600);//获取时间，消除时差，直接显示上面日期

										if(empty($vald['sku'])){$this_same_emsku[$vald['order id']]['shipment date'] = $vald['shipment date'];}else{$this_same[$vald['order id']][$vald['sku']]['shipment date'] = $vald['shipment date'];}

									}
								}
							}

							/*(1)只导明细表补全3rd_part_id，(2)all_order表循环结束后3rd_part_id依然为空，则继续到系统匹配映射表第三方单号*/
							if(empty($vald['3rd_part_id'])){
									$vald['3rd_part_id'] = $order_maps->D->get_one(array('order_default'=>$vald['order id']),'order_trd');
							}
						}
					}
					/*匹配处理--End*/
				}
			}

			/*导退款表需要做的处理*/
			elseif($detail == 'download_refound_new'){
				if($vald['type'] != 'Refund'){
					unset($all_arr_detail[$key]);//除掉非Refund类型的
				}
			}
		}
		/*循环明细主表--End*/

		/*导出处理*/
		unset($all_arr_outbound,$all_arr_allorder,$this_same_emsku,$this_same);

		/*下载新明细操作*/
		if($detail == 'download_newdetail_new'){
			$filename		= 'news_detail_'.date('Y-m-d',time());
			$all_arr_detail	= $this->getLibrary('array')->array_sort_each($all_arr_detail,'3rd_part_id');//按第三方单号升序排序
		}

		/*下载退款操作*/
		elseif($detail == 'download_refound_new'){
			$filename		= 'refound_'.date('Y-m-d',time());
		}

		$head_array = $upload_exl_service->output_mkhead('order_detailnew');
		$this->C->service('upload_excel')->download_xls($filename, $head_array, $all_arr_detail);
	}
}


/*导入新格式明细表，生成最终表*/
elseif($detail == 'import_detail_news'){

	/*读取已经上传的文件*/
	if($filepath){

		$temp_sells		= $this->S->dao('temp_sells');//实例化临时表
		$thismark		= time().mt_rand(00,99);//本次导入标记，清空与查询时用
		$errornum		= 0;

		$all_arr_detail =  $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray['detailnew'], 1);
		unset($all_arr_detail['0']);//删除表头
        
        $global = $this->C->service('global');

		$temp_sells->D->query('begin');//采用事务

		/*遍历插入数据*/
		foreach($all_arr_detail as $vald){

			/*插入临时表*/
			$insertArr = array(
				//'datetime'				=>date('Y-m-d H:i:s',strtotime($vald['date/time'])-16*3600),//减去16个时差，保持数字一致
                'datetime'              =>$global->changetime_ymdhis($vald['date/time']),
				'order_id'				=>$vald['order id'],
				'3rd_part_id'			=>$vald['3rd_part_id'],
				'sku'					=>$vald['sku'],
				'quantity'				=>(substr($vald['order id'] , 0, 1) == 'S') ?'0':$vald['quantity'],//S开头的单不取数量
				'product_sales'			=>$vald['product sales'],
				'shipping_credits'		=>$vald['shipping credits'],
				'promotional_rebates'	=>$vald['promotional rebates'],
				'gift_wrap_credits'		=>$vald['gift wrap credits'],
				'sales_tax_collected'	=>$vald['sales tax collected'],
				'selling_fees'			=>$vald['selling fees'],
				'fba_fees'				=>$vald['fba fees'],
				'other_transaction_fees'=>$vald['other transaction fees'],
				'other'					=>$vald['other'],
				'currency'				=>$vald['currency'],
				'ship_country'			=>$vald['ship country'],
				'shipment_date'			=>$vald['shipment date'],
				'mark'					=>$thismark,
			);
			$sid = $temp_sells->D->insert($insertArr);
			if(!$sid) $errornum++;
		}

       
		if(empty($errornum)){
			$temp_sells->D->query('commit');
		}else{
			$temp_sells->D->query('rollback');
			echo "<script>alert('处理失败，请重新导入！');</script>";
			exit;
		}
        

		/*查询数据组合并弹出下载*/
		$datalist	= $temp_sells->get_moddatalist($thismark);
        
		foreach($datalist as &$valn){
		      //echo $valn['datetime'];die();
            //$valn['datetime'] = $global->changetime_ymdhis($valn['datetime']);//时间转换
			/*$datatimeNum		= date('Y-m-d',strtotime($valn['datetime']));
			$declare	 		= (strtotime($datatimeNum)-strtotime('2012-01-01'))/3600/24;//求天数差
			$valn['datetime']	= $declare + 40909;*/
            
            //日期统一按照时间格式处理

			$valn['deal_pname'] = $valn['erp_pname'];
		}

		/*生成表格后删除此次导入的数据*/
		$temp_sells->D->delete(array('mark'=>$thismark));

		$head_array = array(
			'datetime' 		=> '日期',
			'deal_id' 		=> '平台订单号',
			'3rd_part_id'	=> '第三方单号',
			'deal_sku' 		=> '平台SKU',
			'erp_sku' 		=> 'ERP SKU',
			'jin_sku' 		=> '金碟SKU',
			'deal_pname'	=> '产品名称',
			'erp_pname' 	=> '中文描述',
			'quantity'	 	=> '数量',
			'currency'		=> '币别',
			'price' 		=> '收入',
			'shipprice' 	=> '运费收入',
			'shipfee'	 	=> 'amazon代收运费',
			'amazonfee' 	=> 'amazon fee',
			'otherfee' 		=> '其它平台费用',
			'paypalfee' 	=> 'paypal费',
			'ebayfee' 		=> 'ebay fee',
			'shipway' 		=> '发货方式',
			'shiphouse' 	=> '发货仓库',
			'shipment_date'	=> '发货时间',
			'ship_country' 	=> '国家',
			'address' 		=> '销售账号',
			'buyer_id'	 	=> '收款账号',
			'cuser' 		=> '制单人',
			'comment' 		=> '备注',
		);

		/*弹出下载*/
		$filename	= 'final_detail_'.date('Y-m-d',time());
		$upload_exl_service->download_xls($filename,$head_array,$datalist);
	}

	/*导入表格*/
	else{

		$data_error = 0;
		$tablelist 		    = '<table id="mytable">';

		/*上传并获得数据*/
		$all_arr_detail		=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['detailnew']  , 1);
		$filepath			=  $_SESSION['filepath'];

		/*表头检测，若有错，显示表头*/
		$tablelist		   .= $upload_exl_service->checkmod_head(&$all_arr_detail,&$data_error,'order_detailnew');

		foreach($all_arr_detail as $k=>$val){

			$tablelist .= '<tr>';
			foreach( $val as $j=>$value) {

				$error_style = '';
				if($j == 'order id' && empty($value)){

					$error_style = ' bgcolor="red" title="单号不能为空!"';
					$data_error++;
				}

				if($j == '3rd_part_id' && empty($value)){

					$error_style = ' bgcolor="red" title="第三方单号不能为空!"';
					$data_error++;
				}

				if($j == 'sku' && empty($value)){

					$error_style = ' bgcolor="red" title="SKU不能为空!"';
					$data_error++;
				}

				if($j == 'date/time' && empty($value)){
					$error_style = ' bgcolor="red" title="时间不能为空!"';
					$data_error++;
				}

				$tablelist .= '<td '.$error_style.'>&nbsp;'.$value.'</td>';

			}
			$tablelist .= '</tr>';
		}


		$tablelist	   .= '</table>';
		if(!$data_error && isset($all_arr_detail)){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="submit" value="确认并提交">';
		}elseif($data_error){

			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}

		$this->V->set_tpl('adminweb/commom_excel_import');
		$this->V->mark(array('title'=>'明细表导入-导入表格(import)-订单数据列表(list)','tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
		display();

	}

}

 /*导入明细表，ALL ORDER表、出库表*/
 elseif($detail == 'import_detail'){

	$data_error				= 0;
	$show_data				= '';
	$tablelist 		    	= '<table id="mytable">';

	/*获得明细表*/
	if($_FILES["upload_file3"]["name"]){

		/*上传并获得数据*/
		$all_arr_detail		=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['detail']  , 1,3);
		$all_arr_outbound	=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['outbound'], 1,2);
		$all_arr_allorder	=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['allorder'], 1);


		/*表头特殊显示处理*/
		$tablelist_h	   .= $upload_exl_service->checkmod_head(&$all_arr_detail,&$data_error,'order_detail');
		$tablelist_h	   .= $upload_exl_service->checkmod_head(&$all_arr_outbound,&$data_error,'order_outbound');
		$tablelist_h	   .= $upload_exl_service->checkmod_head(&$all_arr_allorder,&$data_error,'order_allorder');

	}

	$message_upload 		= ' &nbsp;<font size=-1>明&nbsp; 细&nbsp; 表：</font> <input name="upload_file3" type="file" /><br><br>';
	$message_upload	       .= ' &nbsp;<font size=-1>出&nbsp; 库&nbsp; 表：</font> <input name="upload_file2" type="file" /><br><br>';
	$message_upload	       .= ' &nbsp;<font size=-1>allorder表：</font>';

	if(!$data_error && $all_arr_detail){
		$show_data		    = '<div style="color: #FF2A00;font-size:12px;background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;width:450px; line-height:22px;padding-left:25px;margin-top:10px; margin-bottom:10px">';
		$show_data		   .= '上传成功，请点击下面的链接下载需要的表格。<br>新生成的明细表（①处下载），需要另存为表格格式才能用于再次导入。</div>';
		$show_data 	       .= '<span style="font-size:12px;text-decoration:underline;"><a href="index.php?action=order_soldmod&detail=download_newdetail" target="_blank">①下载新整理的明细表</a></span>&nbsp; &nbsp; ';
		$show_data 	       .= '<span style="font-size:12px;text-decoration:underline;"><a href="index.php?action=order_soldmod&detail=download_refound" target="_blank">②下载筛选的退款表</a></span>';

	}

	/*如果有错误表头，则显示*/
	elseif($data_error){
		$tablelist		   .= $tablelist_h;
		$exl_error_msg		= '有错误表头，请查看红色提示修改！';
	}

	$tablelist			   .= $show_data.'<tr></tr></table>';

	unset($all_arr_detail,$all_arr_outbound,$all_arr_allorder);
	$this->V->set_tpl('adminweb/commom_excel_import');
	$this->V->mark(array('title'=>'原始表格导入-导入表格(import)-订单数据列表(list)','message_upload'=>$message_upload,'tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
	display();

 } 
 /*下载新的明细表或退款表*/
 elseif($detail == 'download_newdetail' || $detail == 'download_refound'){

	$this_same 				= array();
	$this_same_emsku		= array();

	$all_arr_detail			= $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath3'], $fieldarray['detail'],   1, 3);
	$all_arr_outbound		= $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath2'], $fieldarray['outbound'], 1, 2);
	$all_arr_allorder		= $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath'] , $fieldarray['allorder'], 1);

	unset($all_arr_detail['0'],$all_arr_outbound['0'],$all_arr_allorder['0']);//除掉表头

 	/*开始数据处理，补全明细表的国家，发货时间，第三方单号*/
	if($all_arr_detail){

		$order_maps = $this->S->dao('order_mapp');

		/*循环明细主表--Start*/
		foreach($all_arr_detail as $key=>&$vald){

			/******非退款的单*******/
			if($vald['Transaction type'] != '退款' && $vald['Transaction type'] != '其他' && $vald['Transaction type'] != '服务费' && $vald['Transaction type'] != 'Remboursement' && substr($vald['Payment Detail'],0,13) != 'Remboursement' && $vald['Transaction type'] != 'Refund' && substr($vald['Payment Detail'],0,6) != 'Refund' && substr($vald['Payment Detail'],0,7) != 'Balance' && !empty($vald['Order ID'])){


				/*退款的*/
				if($detail == 'download_refound'){
					unset($all_arr_detail[$key]);
				}

				/*明细的，第三方单号、国家、发货日期有一项为空都继续匹配；并且过虑退款的*/
				elseif($detail == 'download_newdetail' && (empty($vald['3rd_part_id']) || empty($vald['Ship Country']) || empty($vald['Shipment Date'])) ){


					/*如果订单号和SKU已有存在的，则直接赋值，避免重复查找*/
					if($this_same[$vald['Order ID']][$vald['SKU']]){

						$vald['3rd_part_id'] 	= $this_same[$vald['Order ID']][$vald['SKU']]['3rd_part_id']?$this_same[$vald['Order ID']][$vald['SKU']]['3rd_part_id']:$vald['3rd_part_id'];
						$vald['Ship Country'] 	= $this_same[$vald['Order ID']][$vald['SKU']]['Ship Country']?$this_same[$vald['Order ID']][$vald['SKU']]['Ship Country']:$vald['Ship Country'];
						$vald['Shipment Date'] 	= $this_same[$vald['Order ID']][$vald['SKU']]['Shipment Date']?$this_same[$vald['Order ID']][$vald['SKU']]['Shipment Date']:$vald['Shipment Date'];
					}

					elseif($this_same_emsku[$vald['Order ID']]){
						$vald['SKU']			= $this_same_emsku[$vald['Order ID']]['SKU']?$this_same_emsku[$vald['Order ID']]['SKU']:$vald['SKU'];
						$vald['3rd_part_id'] 	= $this_same_emsku[$vald['Order ID']]['3rd_part_id']?$this_same_emsku[$vald['Order ID']]['3rd_part_id']:$vald['3rd_part_id'];
						$vald['Ship Country'] 	= $this_same_emsku[$vald['Order ID']]['Ship Country']?$this_same_emsku[$vald['Order ID']]['Ship Country']:$vald['Ship Country'];
						$vald['Shipment Date'] 	= $this_same_emsku[$vald['Order ID']]['Shipment Date']?$this_same_emsku[$vald['Order ID']]['Shipment Date']:$vald['Shipment Date'];
					}

					else{

						if($all_arr_allorder){//有导all_order表

							/*循环表ALL ORDER--取家家地区，取第三方单号*/
							foreach($all_arr_allorder as $vala){

								/*匹配条件，(订单号与SKU组合)或(订单号与空SKU-手动发货导出的表SKU为空)*/
								if( ($vald['Order ID'] == $vala['amazon-order-id'] && $vald['SKU'] == $vala['sku']) || ($vald['Order ID'] == $vala['amazon-order-id'] && empty($vald['SKU'])) ){

									$vald['Ship Country'] = $vala['ship-country'];//取国家地区

									/*allorder表第三方单号不为空则取allorder的第三方单号*/
									if(!empty($vala['merchant-order-id'])){
										$vald['3rd_part_id'] = $vala['merchant-order-id'];
									}

									/*allorder表第三方单号为空则匹配ERP的映射表中的第三方单号*/
									else{
										$backfid = $order_maps->D->get_one_by_field(array('order_default'=>$vald['Order ID']),'order_trd');
										$vald['3rd_part_id'] = $backfid['order_trd'];
									}

									/*防止重复查找，并且假若SKU为空补全SKU*/
									$sameArr	 = array('3rd_part_id'=>$vald['3rd_part_id'],'Ship Country'=>$vald['Ship Country'],'Order ID'=>$vald['Order ID']);

									if(empty($vald['SKU'])){$vald['SKU'] = $sameArr['SKU'] = $vala['sku'];$this_same_emsku[$vald['Order ID']] = $sameArr;	}else{	$this_same[$vald['Order ID']][$vald['SKU']] = $sameArr;}

								}
							}
						}

						/*有导出库表*/
						if($all_arr_outbound){

							/*循环出库表outbound--取发货时间*/
							foreach($all_arr_outbound as $valo){
								if( ($vald['Order ID'] == $valo['amazon-order-id'] && $vald['SKU'] == $valo['sku']) || ($vald['Order ID'] == $valo['amazon-order-id'] && empty($vald['SKU'])) ){
									$vald['Shipment Date'] = date('Y-m-d',strtotime($valo['shipment-date'])-8*3600);//获取时间，消除时差，直接显示上面日期

									if(empty($vald['SKU'])){$this_same_emsku[$vald['Order ID']]['Shipment Date'] = $vald['Shipment Date'];}else{$this_same[$vald['Order ID']][$vald['SKU']]['Shipment Date'] = $vald['Shipment Date'];}

								}
							}
						}

						/*只导明细表或匹配完后再检查明细表第三方单号是否依然为空，到系统匹配*/
						if(empty($vald['3rd_part_id'])){
							$vald['3rd_part_id'] = $order_maps->D->get_one(array('order_default'=>$vald['Order ID']),'order_trd');
						}
					}
				}
			}

			/******是退款的单-导明细时将退款的删掉*******/
			elseif($detail == 'download_newdetail'){
				unset($all_arr_detail[$key]);
			}
		}
		/*循环明细主表--End*/

	}

	unset($all_arr_outbound,$all_arr_allorder,$this_same_emsku,$this_same);

	/*弹出下载*/
	if($detail == 'download_newdetail'){
		$filename		= 'new_detail_'.date('Y-m-d',time());
		$all_arr_detail	= $this->getLibrary('array')->array_sort_each($all_arr_detail,'3rd_part_id');//按第三方单号升序排序
	}elseif($detail == 'download_refound'){
		$filename		= 'refound_'.date('Y-m-d',time());
	}

	$head_array = $upload_exl_service->output_mkhead('order_detail');
	$this->C->service('upload_excel')->download_xls($filename,$head_array,$all_arr_detail);

 }

 /*新明细表导入(已筛选掉退款的)生成最终明细表*/
 elseif($detail == 'import_detail_new'){


	/*读取已经上传的文件*/
	if($filepath){

		$temp_soldmod	= $this->S->dao('temp_soldmod');
		$thismark		= time().mt_rand(00,99);//本次导入标记，清空与查询时用
		$errornum		= 0;

		$all_arr_detail =  $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray['detail'], 1);
		unset($all_arr_detail['0']);//删除表头

		$temp_soldmod->D->query('begin');//采用事务
        $bool = false;
		foreach($all_arr_detail as $val){
            //判断是否为中文utf-8
            if  (preg_match("/^[".chr(0x81)."-".chr(0xfe)."]/", $val['Transaction type']) || preg_match("/^[".chr(0x81)."-".chr(0xfe)."]/", $val['Payment Type']) || preg_match("/^[".chr(0x81)."-".chr(0xfe)."]/", $val['Payment Detail'])) {
                 $bool = true;
            } 
			$insertarr					= array(
				'Date'					=> $val['Date'],
				'Order_ID'				=> $val['Order ID'],
				'3rd_part_id'			=> $val['3rd_part_id'],
				'SKU'					=> $val['SKU'],
				'Transaction_type'		=> $val['Transaction type'],
				'Payment_Type'			=> $val['Payment Type'],
				'Payment_Detail'		=> $val['Payment Detail'],
				'Currency'				=> $val['Currency'],
				'Amount'				=> $val['Amount'],
				'Quantity'				=> $val['Quantity'],
				'Product_Title'			=> $val['Product Title'],
				'Ship_Country'			=> $val['Ship Country'],
				'Shipment_Date'			=> $val['Shipment Date'],
				'mark'					=> $thismark,
			);

			if(!$temp_soldmod->D->insert($insertarr)) $errornum++;

		}

		if(empty($errornum)) {
			$temp_soldmod->D->query('commit');
		}else{
			$temp_soldmod->D->query('rollback');
			$this->C->success('操作失败，请重试','index.php?action=order_soldmod&detail=import_detail_new');exit();
		}

		/*查询数据组合并弹出下载*/
        //米悠导入明细表 @jerry @2013-05-23
        if($bool){
            //miu操作方法
            $datalist	= $temp_soldmod->miuget_moddatalist($thismark);
        }else{
            //loftk操作方法
            $datalist	= $temp_soldmod->get_moddatalist($thismark);
        }
        
		$temp_soldmod->D->delete_by_field(array('mark'=>$thismark));//删除此次导入的数据


		$head_array = array(
			'date' 			=> '日期',
			'deal_id' 		=> '平台订单号',
			'3rd_part_id'	=> '第三方单号',
			'deal_sku' 		=> '平台SKU',
			'erp_sku' 		=> 'ERP SKU',
			'jin_sku' 		=> '金碟SKU',
			'deal_pname'	=> '产品名称',
			'erp_pname' 	=> '中文描述',
			'quantity'	 	=> '数量',
			'currency'		=> '币别',
			'price' 		=> '收入',
			'shipprice' 	=> '运费收入',
			'shipfee'	 	=> 'amazon代收运费',
			'amazonfee' 	=> 'amazon fee',
			'otherfee' 		=> '其它平台费用',
			'paypalfee' 	=> 'paypal费',
			'ebayfee' 		=> 'ebay fee',
			'shipway' 		=> '发货方式',
			'shiphouse' 	=> '发货仓库',
			'shipment_date'	=> '发货时间',
			'ship_country' 	=> '国家',
			'address' 		=> '销售账号',
			'buyer_id'	 	=> '收款账号',
			'cuser' 		=> '制单人',
			'comment' 		=> '备注',
		);

		$filename	= 'final_detail_'.date('Y-m-d',time()).mt_rand(00,99);
		$this->C->service('upload_excel')->download_xls($filename,$head_array,$datalist);

	}


	else{

		$data_error = 0;
		$tablelist 		    = '<table id="mytable">';

		/*上传并获得数据*/
		$all_arr_detail		=  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['detail']  , 1);
		$filepath			=  $_SESSION['filepath'];

		/*表头检测，若有错，显示表头*/
		$tablelist		   .= $upload_exl_service->checkmod_head(&$all_arr_detail,&$data_error,'order_detail');

		foreach($all_arr_detail as $k=>$val){

			$tablelist .= '<tr>';
			foreach( $val as $j=>$value) {

				$error_style = '';
				if($j == 'Order ID' && empty($value)){

					$error_style = ' bgcolor="red" title="单号不能为空!"';
					$data_error++;
				}

				if($j == '3rd_part_id' && empty($value)){

					$error_style = ' bgcolor="red" title="第三方单号不能为空!"';
					$data_error++;
				}

				if($j == 'SKU' && empty($value)){

					$error_style = ' bgcolor="red" title="SKU不能为空!"';
					$data_error++;
				}

				if($j == 'Amount'){
					if($value && !preg_match('/^-?[\d]+(\.?[\d]{1,2})?$/',$value)){
						$error_style = ' bgcolor="red" title="请检查Amount是否纯数字！"';
						$data_error++;
					}
				}

				$tablelist .= '<td '.$error_style.'>&nbsp;'.$value.'</td>';

			}
			$tablelist .= '</tr>';
		}


		$tablelist	   .= '</table>';
		if(!$data_error && isset($all_arr_detail)){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="submit" value="确认并提交">';
		}elseif($data_error){

			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}

		$this->V->set_tpl('adminweb/commom_excel_import');
		$this->V->mark(array('title'=>'新明细表导入-导入表格(import)-订单数据列表(list)','tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
		display();

	}


 }

 /*导入最终的销售明细表*/
 elseif($detail == 'import_detail_final'){

	/*读取上传的文件内容并写入数据库保存*/
	if($filepath){
		$errornum	= 0;
		$orders 	= $this->S->dao('orders_detail');
		$product	= $this->S->dao('product');
		$productcost= $this->S->dao('product_cost');
		$exservice	= $this->C->service('exchange_rate');
        $global     = $this->C->service('global');
		$all_arr 	=  $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray['final'], 1);
		unlink($filepath);//取得内容后删除表格
		unset($all_arr['0']);//删除表头

		/*取得销售号与收款号关系*/
		$rexlistArr = $this->C->service('order')->getRecaccountBysoldaccount();

		/*取得币别*/
		$rateArr	= array();
		$backexChgeRate = $this->S->dao('exchange_rate')->D->get_allstr(' and isnew="1" ','','','code,stage_rate,rate');
		foreach($backexChgeRate as $val){
			$rateArr[$val['code']] = array('rate'=>$val['rate'],'stage_rate'=>$val['stage_rate']);
		}


		$orders->D->query('BEGIN');
		foreach($all_arr as $val){
			$val['pid']			= $product->D->get_one(array('sku'=>$val['ERP SKU']),'pid');
			$val['date']		= $global->changetime_ymdhis($val['日期']);//时间转换          
			$val['shipdate']	= $global->changetime_ymdhis($val['日期']);//时间转换

			/*取得转换为USD的产品成本*/
			$backcostArr		= $productcost->D->get_one(array('pid'=>$val['pid']));
			$val['cost1']		= $exservice->change_rate($backcostArr['coin_code'],'USD',$backcostArr['cost1']);
            
            /*财务因为date原因，导入数据被覆盖，加上一个date，销售账号来验证是否覆盖原有数据---jerry*/
			/*检测重复*/
			$backid 			= $orders->D->get_one(array('deal_id'=>$val['平台订单号'],'date'=>$val['date'],'3rd_part_id'=>$val['第三方单号'],'address'=>$val['销售账号'],'deal_sku'=>$val['平台SKU']),'id');
            
			/*收款账号没填系统自动带出保存*/
			if(empty($val['收款账号'])){
				$val['收款账号']= $rexlistArr[$val['销售账号']];
			}

		    $insertarr			= array(
				'date'			=> $val['date'],
				'deal_id'		=> $val['平台订单号'],
				'3rd_part_id'	=> $val['第三方单号'],
				'deal_sku'		=> $val['平台SKU'],
				'erp_sku'		=> $val['ERP SKU'],
				'jin_sku'		=> $val['金碟SKU'],
				'deal_pname'	=> $val['产品名称'],
				'erp_pname'		=> $val['中文描述'],
				'quantity'		=> $val['数量'],
				'currency'		=> $val['币别'],
				'cost1'			=> $val['cost1'],
				'price'			=> $val['收入'],
				'shipprice'		=> $val['运费收入'],
				'shipfee'		=> $val['amazon代收运费'],
				'amazonfee'		=> $val['amazon fee'],
				'otherfee'		=> $val['其它平台费用'],
				'paypalfee'		=> $val['paypal费'],
				'ebayfee'		=> $val['ebay fee'],
				'stage_rate'	=> $rateArr[$val['币别']]['stage_rate'],
				'usd_rate'		=> $rateArr[$val['币别']]['rate'],
				'shipway'		=> $val['发货方式'],
				'shiphouse'		=> $val['发货仓库'],
				'shipment_date'	=> $val['shipdate'],
				'ship_country'	=> $val['国家'],
				'address' 		=> $val['销售账号'],
				'buyer_id'	 	=> $val['收款账号'],
				'cuser'			=> $_SESSION['eng_name'],
				'comment'		=> $val['备注'],
				'pid'			=> $val['pid'],
			);

			/*重复的覆盖*/
			if($backid){
				unset($insertarr['stage_rate'],$insertarr['usd_rate'],$insertarr['cost1']);
				$sid = $orders->D->update(array('id'=>$backid),$insertarr);
			}else{
				$sid = $orders->D->insert($insertarr);
			}
			if(!$sid) $errornum++;
		}

		$jumpUrl = 'index.php?action=order_soldmod&detail='.(empty($errornum)?'list':'import_detail_final');
		if(empty($errornum)) {$orders->D->query('commit');$this->C->success('保存成功',$jumpUrl);}else{$orders->D->query('rollback');$this->C->success('保存失败，请重试',$jumpUrl);}


	}else{

		$data_error 		= 0;
		$tablelist 		    = '<table id="mytable">';
		$product			= $this->S->dao('product');
        $esse               = $this->S->dao('esse');

		/*检测发货方式用*/
		$backshipArr		= array();
		$backship			= $this->S->dao('shipping')->D->get_allstr();
		foreach($backship as $val){
			$backshipArr[] = $val['s_name'];
		}

		/*检测销售账号用*/
		$backsoacArr		= array();
		$backsoldaccount	= $this->S->dao('sold_account')->D->get_allstr();
		foreach($backsoldaccount as $val){
			$backsoacArr[]	= $val['account_name'];
		}

		/*检测收款账号用*/
		$backrecArrq		= array();
		$backpayrecacount	= $this->S->dao('finance_payrec_account')->D->get_allstr();
		foreach($backpayrecacount as $val){
			$backrecArr[]	= $val['payrec_account'];
		}

		/*检测币别用*/
		$backcoincodeArr	= $this->S->dao('exchange_rate')->get_sys_coincode();

		/*上传并获得数据*/
		$all_arr 			= $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'] , $fieldarray['final'], 1);
		$filepath			=  $_SESSION['filepath'];

		/*表头检测，若有错，显示表头*/
		$tablelist		   .= $upload_exl_service->checkmod_head(&$all_arr,&$data_error,'order_final');

		foreach($all_arr as $k=>$val){
			$tablelist .= '<tr>';
			foreach( $val as $j=>$value) {

				$error_style = '';
				if($j == '平台订单号' && empty($value)){

					$error_style = ' bgcolor="red" title="平台订单号不能为空!"';
					$data_error++;
				}

				if($j == '第三方单号' && empty($value)){

					$error_style = ' bgcolor="red" title="第三方单号不能为空!"';
					$data_error++;
				}

				if($j == '平台SKU' && empty($value)){

					$error_style = ' bgcolor="red" title="平台SKU不能为空!"';
					$data_error++;
				}

				if($j == 'ERP SKU'){
					if(empty($value)){
						$error_style = ' bgcolor="red" title="ERP SKU不能为空!"';
						$data_error++;
					}elseif(!$product->D->get_one(array('sku'=>$value),'pid')){
						$error_style = ' bgcolor="red" title="系统不存在的SKU!"';
						$data_error++;
					}
				}
                
                if($j == '发货仓库'){
					if(empty($value)){
						$error_style = ' bgcolor="red" title="发货仓库不能为空!"';
						$data_error++;
					}elseif(!$esse->D->get_count(array('type'=>'2','name'=>$value))){
						$error_style = ' bgcolor="red" title="系统不存在的发货仓库!"';
						$data_error++;
					}
				}
                
				if($j == '币别'){
					if(empty($value)){
						$error_style = ' bgcolor="red" title="币别不能为空!"';
						$data_error++;
					}elseif(!in_array($value,$backcoincodeArr)){
						$error_style = ' bgcolor="red" title="系统不存在的币别!"';
						$data_error++;
					}
				}

				if($j == '发货方式'){
					if(empty($value)){
						$error_style = ' bgcolor="red" title="发货方式不能为空!"';
						$data_error++;
					}elseif(!in_array($value,$backshipArr)){
						$error_style = ' bgcolor="red" title="系统不存在的发货方式!"';
						$data_error++;
					}
				}

				if($j == '销售账号'){
					if(empty($value)){
						$error_style = ' bgcolor="red" title="销售账号不能为空!"';
						$data_error++;
					}
                    elseif(!in_array($value,$backsoacArr)){
						$error_style = ' bgcolor="red" title="系统不存在的销售账号!"';
						$data_error++;
					}
				}

				/*收款账号若不填写，则保存时由关联的销售账号带出，若填写，则检测是否系统录入*/
				if($j == '收款账号' && !empty($value)){
					if(!in_array($value,$backrecArr)){
						$error_style = ' bgcolor="red" title="系统不存在的收款账号!"';
						$data_error++;
					}
				}

				if($j == '日期' && (!preg_match('/^[0-9]*$/',$value) && !date('Y-m-d',strtotime($value)))){
					$error_style = ' bgcolor="red" title="时间格式错误!"';
					$data_error++;
				}

				$tablelist  .= '<td '.$error_style.' >&nbsp;'.$value.'</td>';
			}
			$tablelist .= '</tr>';
		}
		$tablelist	   .= '</table>';

		if(!$data_error && isset($all_arr)){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="submit" value="确认并提交">';
		}elseif($data_error){

			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}

	 	$this->V->set_tpl('adminweb/commom_excel_import');
	 	$this->V->mark(array('title'=>'最终明细表导入-导入表格(import)-订单数据列表(list)','tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
	 	display();
	}


 } 
 //米悠结帐明细表和出库表导入
 //查找出库表平台订单号跟结帐明细表订单号对应
 //并把结帐明细表交易时间 覆盖掉 出库表日期
 //结帐明细表销售金额 覆盖掉 出库表收入
 //结帐明细表佣金 覆盖掉 出库表其他平台费用
 elseif($detail == 'import_detail_new_final'){
    if(!$this->C->service('admin_access')->checkResRight('r_p_add_new_final')){$this->C->sendmsg();}
        $data_error = 0;
        $show_data = '';
        $tablelist = '<table id="mytable">';
    /*获得明细表*/
    if($_FILES["upload_file2"]["name"]){  
        /*上传并获得数据*/ 
        //库存表
        $all_arr_final       =  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['final'], 1,2);
        //结帐明细reckoning
        $all_arr_reckoning   =  $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'], $fieldarray['reckoning'], 1);  
        /*表头特殊显示处理*/ 
        $tablelist_h = $upload_exl_service->checkmod_head(&$all_arr_final,&$data_error,'order_final');    
        $tablelist_reckoning_h = $upload_exl_service->checkmod_head(&$all_arr_reckoning,&$data_error,'order_reckoning'); 
    }
    
    $checkorder = array();
    if($all_arr_reckoning){
        foreach ($all_arr_reckoning as $key => $value) {
           if(isset($orderarr[$value['平台订单号']])){
               $checkorder[$value['平台订单号']] = $orderarr[$value['平台订单号']];
               $checkorder[] = $key;
           }else{
               $orderarr[$value['平台订单号']] = $key; 
           }
        }
        unset($orderarr); 
      if($checkorder){
          $error_style = ' bgcolor="red" title="重复的订单号!"';
          foreach ($checkorder as $key => $value) { 
              $tabletrlist  .= '<tr>'; 
              $tabletrlist  .= '<td  >&nbsp;'.$all_arr_reckoning[$value]['日期'].'</td>';
              $tabletrlist  .= '<td '.$error_style.' >&nbsp;'.$all_arr_reckoning[$value]['平台订单号'].'</td>';
              $tabletrlist  .= '<td >&nbsp;'.$all_arr_reckoning[$value]['收入'].'</td>';
              $tabletrlist  .= '<td >&nbsp;'.$all_arr_reckoning[$value]['其它平台费用'].'</td>';
              $tabletrlist  .= '</tr>'; 
          }
      } 
    } 
    
    //生成file控件
    $message_upload = ' &nbsp;<font size=-1>&nbsp;出&nbsp; 库&nbsp; 表&nbsp;：</font> <input name="upload_file2" type="file" /><br><br>';
    $message_upload .= ' &nbsp;<font size=-1>收款明细表：</font>';
        
    if(!$data_error && $all_arr_reckoning && !$checkorder){
        $show_data  = '<div style="color: #FF2A00;font-size:12px;background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;width:450px; line-height:22px;padding-left:25px;margin-top:10px; margin-bottom:10px">';
        $show_data .= '上传成功，请点击下面的链接下载需要的表格。<br>新生成的销售明细表（①处下载），需要另存为表格格式才能用于再次导入。</div>';
        $show_data .= '<span style="font-size:12px;text-decoration:underline;"><a href="index.php?action=order_soldmod&detail=download_new_final" target="_blank">①下载新整理的销售明细表</a></span>&nbsp; &nbsp; ';
    }
    /*如果有错误表头，则显示*/
    elseif($data_error){
        $tablelist .=$tablelist_h.$tablelist_reckoning_h; 
        $exl_error_msg = '有错误表头，请查看红色提示修改！';
    }elseif($checkorder){
        $tablelist .=$tablelist_reckoning_h.$tabletrlist; 
        $exl_error_msg = '有'.count($checkorder).'处重复平台订单号，平台订单号必须是唯一的，请查看红色提示修改！';
    }  
   $tablelist .= $show_data.'<tr></tr></table>';
    unset($all_arr_reckoning,$all_arr_final,$orderarr,$checkorder);
    $this->V->set_tpl('adminweb/commom_excel_import');
    $this->V->mark(array('title'=>'米悠新明细表格导入-导入表格(import)-订单数据列表(list)','message_upload'=>$message_upload,'tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
    display();  
 } 
 
 //下载米悠结帐明细表和出库表合并成新的明细表
 elseif ($detail == 'download_new_final'){ 
        //读取出库表
        $all_arr_final =  $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath2'], $fieldarray['final'], 1,2); 
        //读取结帐明细表
        $all_arr_reckoning =  $upload_exl_service->get_excel_datas_withkey($_SESSION['filepath'], $fieldarray['reckoning'], 1);     
        //除掉表头
	    unset($all_arr_final['0'],$all_arr_reckoning['0']);   
        /*有导出库表*/
        if($all_arr_final){ 
            $finalarr       = array();
            $finalarrcount  = array();
            $finalcostarr   = array();
            $product	    = $this->S->dao('product');
            $productcost    = $this->S->dao('product_cost');
            $global         = $this->C->service('global');
            //设同个订单号为键 唯一sku码为值
            foreach ($all_arr_final as $key => $value) {  
                $finalarr[$value['平台订单号']][] =$value['ERP SKU']; 
            }
           
            //获取一个订单号里面的sku值大于1的市场价cost3 和总的市场价
            //过滤掉不是拆装的订单号
            if($finalarr){
                foreach ($finalarr as $keys => $values) {
                    if(count($values)>1){
                        $costcount = 0;//市场总价 
                        //循环sku
                        foreach ($values as $ks => $vs) {
                            //获取pid
                            $pid = $product->D->get_one(array('sku'=>$vs),'pid'); 
                            //获取cost3市场价
                            $backcostArr = $productcost->D->get_one(array('pid'=>$pid)); 
                            $costcount += $backcostArr['cost3'];
                            $finalcostarr[$keys][$vs] = $backcostArr['cost3'];
                        }
                        $finalcostarr[$keys]['cnum'] = $costcount;
                    }
                } 
            }
           
            $reckarr = array(); 
            /*循环出库表*/
            foreach($all_arr_final as $key => $val){  
                 $bool = true;
                 //循环结帐明细表
                 foreach ($all_arr_reckoning as $k => $v) {
                    $v['日期'] = $global->changetime_ymdhis($v['日期']);
                    /*if(!preg_match('/^[0-9]*$/',$v['日期'])){
                        $temptimec = (strtotime($v['日期'])-strtotime('2012-01-01'))/3600/24; 
                        $v['日期'] = $temptimec + 40909; 
                    }else{
                        $v['日期'] = date('Y-m-d H:i:s',strtotime($v['日期']));
                    }*/
                    //$v['日期'] = date('Y-m-d H:i:s',strtotime($v['日期']));
                    //获取结帐明细表不对应出库表的数据
                    if(!$finalarr[$v['平台订单号']]){
                        $reckarr[] = array('平台订单号'=>$v['平台订单号'],'日期'=>$v['日期'],'收入'=>$v['收入'],'其它平台费用'=>$v['其它平台费用']); 
                        unset($all_arr_reckoning[$k]);
                        continue;
                    } 
                   if($val['平台订单号'] == $v['平台订单号'] && $bool){
                        //是否为拆装单
                        if($finalcostarr[$val['平台订单号']]){  
                            $all_arr_final[$key]['日期'] = $v['日期'];
                            $cost3 = $finalcostarr[$val['平台订单号']][$val['ERP SKU']];  //市场价 
                            $cost3count = $finalcostarr[$val['平台订单号']]['cnum'];  //市场总价
                            $all_arr_final[$key]['收入'] = ($cost3/$cost3count)*$v['收入'];
                            $all_arr_final[$key]['其它平台费用'] =($cost3/$cost3count)*$v['其它平台费用']; 
                        }else{
                            //单个货单
                            $all_arr_final[$key]['日期'] = $v['日期'];
                            $all_arr_final[$key]['收入'] = $v['收入'];
                            $all_arr_final[$key]['其它平台费用'] = $v['其它平台费用']; 
                            unset($all_arr_reckoning[$k]);
                        }  
                        $bool = false;
                    } 
                 }
                 if($bool){unset($all_arr_final[$key]);}
            }
            /*把条件相同的 费用累加起来*/
            if($all_arr_final){
                foreach ($all_arr_final as $ks => $vs) { 
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['平台订单号'] = $vs['平台订单号'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['第三方单号'] = $vs['第三方单号'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['ERP SKU'] = $vs['ERP SKU'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['发货仓库'] = $vs['发货仓库'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['销售账号'] = $vs['销售账号'];
                    
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['日期'] = $vs['日期'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['平台SKU'] = $vs['平台SKU'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['金碟SKU'] = $vs['金碟SKU'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['产品名称'] = $vs['产品名称'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['中文描述'] = $vs['中文描述'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['币别'] = $vs['币别'];
                    
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['数量'] += $vs['数量'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['收入'] += $vs['收入'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['运费收入'] += $vs['运费收入'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['amazon代收运费'] += $vs['amazon代收运费'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['amazon fee'] += $vs['amazon fee'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['其它平台费用'] += $vs['其它平台费用'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['paypal费'] += $vs['paypal费'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['ebay fee'] += $vs['ebay fee'];
                    
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['发货方式'] = $vs['发货方式'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['发货时间'] = $vs['发货时间'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['国家'] = $vs['国家'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['收款账号'] = $vs['收款账号'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['制单人'] = $vs['制单人'];
                    $finalarrcount[$vs['平台订单号'].'_'.$vs['第三方单号'].'_'.$vs['ERP SKU'].'_'.$vs['发货仓库'].'_'.$vs['销售账号']]['备注'] = $vs['备注']; 
                 
                }
                sort($finalarrcount);
            }
             
            //结帐明细表不对应出库表的数据 增加到出库表中
            foreach ($reckarr as $keys => $values) {
                $finalarrcount[]=array('平台订单号' => $values['平台订单号'],'日期'=>$values['日期'],'收入'=>$values['收入'],'其它平台费用'=>$values['其它平台费用']);
            }     
        }
       
    /*循环明细主表--End*/  
    unset($all_arr_reckoning,$finalarr,$finalcostarr,$reckarr); 
    /*弹出下载*/
    $filename = 'new_finalorder_'.date('Y-m-d',time());  
    $head_array = $upload_exl_service->output_mkhead('order_final');
    $this->C->service('upload_excel')->download_xls($filename,$head_array,$finalarrcount);  
 }  
 
 /*调用头部尾部*/
 if($detail == 'list' || $detail == 'import_detail' || $detail == 'import_detail_new' || $detail == 'import_detail_new_final' || $detail == 'import_detail_final'  || $detail == 'import' || $detail == 'import_details' || $detail == 'import_detail_news'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>


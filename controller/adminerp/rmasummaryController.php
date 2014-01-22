<?php

/**
 +-----------------------------------------------
 * @title RMA汇总
 * @author Jerry
 * @create on 2012-11-30
 +-----------------------------------------------
 */

/*
 * @title RMA汇总列表
 *
 */
if ($detail == 'list') {

    /*搜索选项*/
    $stypemu = array(
        'order_id-s-e'  =>'RMA&nbsp;&nbsp;&nbsp;单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号：',
        'sold_way-a-e'  =>'渠&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;道：',
        'sold_id-a-e'   =>'销售账号：',
        'cdate-t-t'     =>' <br> RAM创单时间：',
        'fid-s-e'       =>'第三方单号：',
        'tempstr-h-e'   =>'ddd',
        'rdate-t-t'=>'出库时间：',
        'orders-b-'=>'',
    );

    if(isset($order_id)) {
        $sqlstr = str_replace('R','x',$sqlstr);
    }


    /*取得销售账号下拉*/
	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','id,account_name');
	$sold_idarr		= array(''=>'=请选择=');
	for($i = 0; $i < count($soldaccount); $i++){
		$sold_idarr[$soldaccount[$i]['id']] = $soldaccount[$i]['account_name'];
	}

    /*生成渠道搜索*/
	$sold_wayarr    = $this->C->service('global')->get_sold_way(0,'sold_way','wayname');
    $exchange_rate  = $this->C->service('exchange_rate');
    $rmasummary     = $this->S->dao('rmasummary');
    
     //数据输出
    $displayarr = array();
    $tablewidth = '1300';
    

    //存在条件就查询
    /*如果搜索的时候是以order_id,出库时间，就查询出库订单退货退款数据，否则就查询具体的退货退款明细*/
    if (isset($tempstr)  && ($order_id ||$rdatestartTime || $rdateendTime)) {
        
        $ret = array();
        $t = 0;
        $sqlstr 		= strtr($sqlstr,array('order_id'=>'p.order_id','cdate'=>'tmp.cdate','rdate'=>'tmp.rdate'));
        $data 		= $rmasummary->rmasummarylistcount($sqlstr);
        for($j = 0; $j<count($data);$j++){
            if (!in_array($data[$j]['order_id'],$ret)){
                $ret[] = $data[$j]['order_id'];
                $t++;
            }
        }
        
        $ordersstr = "<font color=red>RMA订单数：".$t."个</font>";
        
        $InitPHP_conf['pageval'] = 20;
        $datalist 		= $rmasummary->rmasummarylist($sqlstr);
        
        for($i = 0; $i<count($datalist); $i++){
    
            $datalist[$i]['order_idd']  = ($datalist[$i]['order_id'] == $datalist[$i-1]['order_id'])?'':'<a href="index.php?action=rmasummary&detail=details&order_id='.$datalist[$i]['order_id'].'")  title="点击查看明细">R'.substr($datalist[$i]['order_id'],1).'</a>';
            $datalist[$i]['comment3']   = str_replace('_',' ',$datalist[$i]['comment3']);
            if (($datalist[$i]['price']) && ($datalist[$i]['coin_code'] && $datalist[$i]['stage_rate'])){
                $returnprice = $exchange_rate->change_rate_by_stage($datalist[$i]['price'],'USD',$datalist[$i]['coin_code'],$datalist[$i]['stage_rate']);
                $datalist[$i]['price']      = number_format($returnprice,2);
            }
        }
    
        $displayarr['order_idd']     = array('showname'=>'RMA单号','width'=>'50');
        $displayarr['sku']          = array('showname'=>'产品sku','width'=>'50');
        $displayarr['product_name'] = array('showname'=>'产品名称','width'=>'100');
        $displayarr['ruser']        = array('showname'=>'处理人','width'=>'50');
        $displayarr['cdate']        = array('showname'=>'创建时间','width'=>'80');
        $displayarr['comment3']     = array('showname'=>'原因','width'=>'100');
        $displayarr['returns']      = array('showname'=>'退货数量','width'=>'50');
        $displayarr['replaces']     = array('showname'=>'重发数量','width'=>'50');
        $displayarr['price']        = array('showname'=>'退款金额','width'=>'50');
        
    }else{
        $InitPHP_conf['pageval'] = 20;
        $sqlstr     = strtr($sqlstr,array('cdate'=>'p.cdate'));
        $datalist   = $rmasummary->rmadetailslist($sqlstr);
        
        for($i = 0;$i < count($datalist); $i++){
            $datalist[$i]['price']      = $datalist[$i]['price']."&nbsp;(".$datalist[$i]['coin_code'].')';
            $datalist[$i]['comment3']   = str_replace('_',' ',$datalist[$i]['comment3']);
    
            if ($datalist[$i]['protype'] == '退货'){
                $datalist[$i]['desc']    = $datalist[$i]['protype'].'单号：'.$datalist[$i]['order_id'].",";
                $datalist[$i]['desc']   .= $datalist[$i]['input'] == 1?'已入库':'未入库';
            }
            if ($datalist[$i]['property'] == '退款单'){
                $datalist[$i]['desc']   .= $datalist[$i]['ispay'] == 1?'已退款':'未退款';
            }
    
        }
    
        $displayarr['sku']      = array('showname'=>'产品sku','width'=>'80');
        $displayarr['quantity'] = array('showname'=>'数量','width'=>'50');
        $displayarr['price']    = array('showname'=>'退款金额(币别)','width'=>'120');
        $displayarr['fid']    = array('showname'=>'第三方单号','width'=>'120');
        $displayarr['comment3'] = array('showname'=>'原因','width'=>'100');
        $displayarr['cuser']    = array('showname'=>'制单人','width'=>'80');
        $displayarr['cdate']    = array('showname'=>'制单时间','width'=>'100');
        $displayarr['name']     = array('showname'=>'接收仓库','width'=>'100');
        $displayarr['comment']  = array('showname'=>'备注','width'=>'100');
        $displayarr['ruser']    = array('showname'=>'处理人','width'=>'80');
        $displayarr['rdate']    = array('showname'=>'处理时间','width'=>'100');
        $displayarr['desc']     = array('showname'=>'系统描述','width'=>'150');
    }
    
    $bannerstrarr[] = array('url'=>'index.php?action=rmasummary&detail=output','value'=>'导出数据');
    $this->V->mark(array('title'=>'RMA汇总'));
    $temp = 'pub_list';
}

/**
 * @title RMA出库明细
 * @author Jerry
 * @create on 2012-11-30
 */

elseif ($detail == 'details') {

    $rmasummary = $this->S->dao('rmasummary');
    $process    = $this->S->dao('process');

    $_id        = $process->D->get_one(array('order_id'=>$order_id),'id');
    $sql        = '  and p.detail_id='.$_id;
    $datalist   = $rmasummary->rmadetailslist($sql);

    for($i = 0;$i < count($datalist); $i++){
        $datalist[$i]['price']      = $datalist[$i]['price']."&nbsp;(".$datalist[$i]['coin_code'].')';
        $datalist[$i]['comment3']   = str_replace('_',' ',$datalist[$i]['comment3']);

        if ($datalist[$i]['protype'] == '退货'){
            $datalist[$i]['desc']    = $datalist[$i]['protype'].'单号：'.$datalist[$i]['order_id'].",";
            $datalist[$i]['desc']   .= $datalist[$i]['input'] == 1?'已入库':'未入库';
        }
        if ($datalist[$i]['property'] == '退款单'){
            $datalist[$i]['desc']   .= $datalist[$i]['ispay'] == 1?'已退款':'未退款';
        }

    }
    $displayarr = array();

    $displayarr['sku']      = array('showname'=>'产品sku','width'=>'80');
    $displayarr['quantity'] = array('showname'=>'数量','width'=>'50');
    $displayarr['price']    = array('showname'=>'退款金额(币别)','width'=>'120');
    $displayarr['comment3'] = array('showname'=>'原因','width'=>'100');
    $displayarr['cuser']    = array('showname'=>'制单人','width'=>'80');
    $displayarr['cdate']    = array('showname'=>'制单时间','width'=>'100');
    $displayarr['name']     = array('showname'=>'接收仓库','width'=>'100');
    $displayarr['comment']  = array('showname'=>'备注','width'=>'100');
    $displayarr['ruser']    = array('showname'=>'处理人','width'=>'80');
    $displayarr['rdate']    = array('showname'=>'处理时间','width'=>'100');
    $displayarr['desc']     = array('showname'=>'系统描述','width'=>'150');

    $this->V->mark(array('title'=>'RMA明细-RAM汇总(list)'));
    $temp = 'pub_list';

}

/**
 * @title 汇总导出
 * @author Jerry
 * @create on 2012-12-03
 */
elseif ($detail == 'output') {
    
    $rmasummary = $this->S->dao('rmasummary');
    $exchange_rate = $this->C->service('exchange_rate');
    
    /*订单号存在，就查询出库订单，其他条件，就查询退货退款明细*/
    if (isset($tempstr) && ($order_id ||$rdatestartTime || $rdateendTime)){
        
        $sqlstr 		= strtr($sqlstr,array('order_id'=>'p.order_id','cdate'=>'tmp.cdate','rdate'=>'tmp.rdate'));     
        $sqlstr = str_replace('R','x',$sqlstr);
        $datalist = $rmasummary->rmasummarylist($sqlstr);
    
        for ($i = 0; $i<count($datalist); $i++) {
            $datalist[$i]['order_id'] = "R".substr($datalist[$i]['order_id'],1);
    
            $datalist[$i]['order_idd']  = ($datalist[$i]['order_id'] == $datalist[$i-1]['order_id'])?'':$datalist[$i]['order_id'];
    		$datalist[$i]['comment3']   = str_replace('_',' ',$datalist[$i]['comment3']);
    
            if ($datalist[$i]['coin_code'] && $datalist[$i]['price'] && $datalist[$i]['stage_rate']){
                $returnprice = $exchange_rate->change_rate_by_stage($datalist[$i]['price'],'USD',$datalist[$i]['coin_code'],$datalist[$i]['stage_rate']);
                $datalist[$i]['price']      = number_format($returnprice,2);
            }
        }
        
        $head_array = array('order_idd'=>'RMA单号','sku'=>'sku','product_name'=>'产品名称','ruser'=>'处理人','cdate'=>'创建时间','comment3'=>'原因','returns'=>'退货数量','replaces'=>'重发数量','price'=>'退款金额');
        
    }else{
        $sqlstr     = strtr($sqlstr,array('cdate'=>'p.cdate'));
        $datalist   = $rmasummary->rmadetailslist($sqlstr);
        
        for($i = 0;$i < count($datalist); $i++){
        $datalist[$i]['price']      = $datalist[$i]['price']."&nbsp;(".$datalist[$i]['coin_code'].')';
        $datalist[$i]['comment3']   = str_replace('_',' ',$datalist[$i]['comment3']);

        if ($datalist[$i]['protype'] == '退货'){
            $datalist[$i]['desc']    = $datalist[$i]['protype'].'单号：'.$datalist[$i]['order_id'].",";
            $datalist[$i]['desc']   .= $datalist[$i]['input'] == 1?'已入库':'未入库';
        }
        if ($datalist[$i]['property'] == '退款单'){
            $datalist[$i]['desc']   .= $datalist[$i]['ispay'] == 1?'已退款':'未退款';
        }
        }
        
        $head_array = array('sku'=>'sku','quantity'=>'数量','price'=>'退款(币别)','fid'=>'第三方单号','comment3'=>'原因','cuser'=>'制单人','cdate'=>'制单时间','name'=>'接收仓库','comment'=>'备注','ruser'=>'处理人','rdate'=>'处理时间','desc'=>'系统描述');
    }

    $filename   = 'RMA_List'.date('Y-m-d');
    $this->C->service('upload_excel')->download_excel($filename, $head_array, $datalist);
}

if ($detail =='list' || $detail =='details') {

    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L');
}

?>
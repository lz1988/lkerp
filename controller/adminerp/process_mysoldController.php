<?php
/*
 * Created on 2012-3-22
 *
 * @title查看个人的销售历史
 * @author by hanson
 */

/*列表显示*/
 if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'sku-s-l'=>'&nbsp; &nbsp; SKU：',
		'order_id-s-l'=>'&nbsp; &nbsp; &nbsp; 订单号：',
		'fid-s-l'=>'&nbsp; &nbsp; &nbsp; 第三方单号：',
		'statu-a-e'=>'状态：',
		'comment2-s-l'=>'&nbsp; &nbsp; &nbsp; 跟踪号：',
		'buyer_id-s-l'=>'&nbsp; Buyer_ID：',
		'deal_id-s-l'=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 平台单号：',
        'stars-a-e'=> '标记：',
        'cuser-s-e' => '&nbsp; &nbsp; &nbsp; &nbsp; 制单人：',
	);
	$statuarr = array(''=>'=按状态=','0'=>'预出库','1'=>'已接收','2'=>'待发货','3'=>'已出库');
	$starsarr = array(''=>'=全部=','1'=>'未标记','2'=>'已标记');

    if (empty($cuser)) {
        $sqlstr.=' and (protype="售出" or protype="重发") and p.cuser="'.$_SESSION['eng_name'].'"';
        $cuser = $_SESSION['eng_name'];
    }
    else {
        $sqlstr.=' and (protype="售出" or protype="重发") ';
        $sqlstr = str_replace('cuser', 'p.cuser', $sqlstr);
    }

    /*取得销售账号*/
    $accountarr		= array();
	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','account_name,account_code');
	for($i = 0; $i < count($soldaccount); $i++){
		$accountarr[$soldaccount[$i]['account_name']] = $soldaccount[$i]['account_code'];
	}

	/*清除框架自动生成的SQL语句*/
    if($stars){
    	$sqlstr = strtr($sqlstr,array('and stars="'.$stars.'"'=>''));
    }

	$InitPHP_conf['pageval'] = 20;

	/*查出数据*/
	$datalist = $this->S->dao('process')->get_shipment_orders($sqlstr,'mysold',$stars);

	/*数据解压等处理*/
 	for($i=0;$i<count($datalist);$i++){

		/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd'] = '';
		}else{
			$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
		}

		$datalist[$i]['comment2'] = empty($datalist[$i]['comment2'])?'--':$datalist[$i]['comment2'];
 		$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);//将数组中的压缩内容解压并作为字段增加入数据中

 		switch ($datalist[$i]['statu']){
			case '0':$datalist[$i]['status'] = '预出库';break;
			case '1':$datalist[$i]['status'] = '已接收';break;
			case '2':$datalist[$i]['status'] = '待发货';break;
			case '3':$datalist[$i]['status'] = '已出库';break;
 		}

 		$datalist[$i]['isnormal'] = ($datalist[$i]['isover'] == 'Y')?'异常':'正常';
 		$datalist[$i]['comment3'] = $this->C->service('warehouse')->get_canceltype(1,$datalist[$i]['comment3']);

 		$datalist[$i]['e_p_fee']  = number_format($datalist[$i]['e_performance_fee'],2);
 		$datalist[$i]['e_s_fee']  = number_format($datalist[$i]['e_shipping_fee'],2);

		if($datalist[$i]['os_id']){
 			$datalist[$i]['stars']	  = '<span id=stars_'.$i.' ><a href="javascript:void(0);"  class="movetips"  id='.$datalist[$i]['id'].' name='.$i.'><img src="./staticment/images/star_t.gif" border="0"></a></span>';
		}else{
			$datalist[$i]['stars']	  = '<span id=stars_'.$i.'><a href="javascript:void(0);setline('.$i.')" class="settips" title="标为星标" name="'.$datalist[$i]['order_id'].'" id='.$datalist[$i]['id'].'><img src="./staticment/images/star_o.gif" border="0"></a></span>';
		}

		$datalist[$i]['soldid']   = $accountarr[$datalist[$i]['soldid']];

 	}

 	$bannerstr = '<style type="text/css">';//提醒框CSS
 	$bannerstr.= '#tooltip h2,#settips h2{ border-bottom:#cccccc dotted 1px; font-size:12px; color:#333333; font-weight:normal; padding-bottom:5px;}';
 	$bannerstr.= '#tooltip h2 span,#settips h2 span{float:right;}';
 	$bannerstr.= '#tooltip p,#settips p{ margin:0; padding:0;}';
 	$bannerstr.= '#d_p {height:90px}';
 	$bannerstr.= '</style>';

	/*数据输出*/
	$displayarr = array();
	$tablewidth = '1850';
	$displayarr['stars']	 	 = array('showname'=>'标记','width'=>'50');
	$displayarr['order_idd'] 	 = array('showname'=>'订单号','width'=>'80');
	$displayarr['fid'] 			 = array('showname'=>'第三方单号','width'=>'120');
	$displayarr['sku'] 			 = array('showname'=>'产品SKU','width'=>'100');
	$displayarr['product_name']  = array('showname'=>'产品名称','width'=>'100');
	$displayarr['quantity'] 	 = array('showname'=>'数量','width'=>'50');
	$displayarr['price'] 	 	 = array('showname'=>'金额','width'=>'100');
	$displayarr['e_p_fee'] 		 = array('showname'=>'交易费','width'=>'110');
	$displayarr['e_s_fee']		 = array('showname'=>'运费','width'=>'110');
	$displayarr['coin_code']	 = array('showname'=>'币别','width'=>'50');
	$displayarr['cuser'] 		 = array('showname'=>'制单人','width'=>'80');
	$displayarr['status'] 		 = array('showname'=>'状态','width'=>'70');
	$displayarr['isnormal'] 	 = array('showname'=>'是否异常','width'=>'80');
	$displayarr['comment3'] 	 = array('showname'=>'取消原因','width'=>'100');
	$displayarr['e_shipping'] 	 = array('showname'=>'发货方式','width'=>'80');
	$displayarr['warename'] 	 = array('showname'=>'发货仓库','width'=>'80');
	$displayarr['comment2'] 	 = array('showname'=>'物流跟踪号','width'=>'100');
	$displayarr['buyer_id'] 	 = array('showname'=>'buyer_ID','width'=>'80');
	$displayarr['soldway'] 	 	 = array('showname'=>'渠道','width'=>'80');
	$displayarr['soldid'] 	 	 = array('showname'=>'帐号/平台','width'=>'100');
	$displayarr['payrecid']	 	 = array('showname'=>'收款','width'=>'100');

	$displayarr['comment']	 	 = array('showname'=>'备注','width'=>'100');
	$displayarr['cdate'] 		 = array('showname'=>'制单日期','width'=>'100');
	$displayarr['deal_id'] 		 = array('showname'=>'平台单号','width'=>'120');

	$jslink = "<script src='./staticment/js/process_mysold.js?version=".rand(00,99)."'></script>\n";
	$this->V->mark(array('title'=>'个人销售历史'));
	$temp = 'pub_list';
}

/*保存标记*/
elseif($detail == 'setsign'){

	$sid		= $this->S->dao('order_signed')->D->insert(array('os_id'=>$os_id,'os_desc'=>$os_desc,'os_cuser'=>$_SESSION['eng_name'],'os_cdate'=>date('Y-m-d H:i:s',time())));
	$backdata	= $this->S->dao('process')->D->get_one_by_field(array('id'=>$os_id),'order_id');

	echo json_encode(array('order_id'=>$backdata['order_id'],'os_desc'=>$os_desc,'nowtime'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name']));

}

/*取消标记*/
elseif($detail == 'clesign'){
	$sid = $this->S->dao('order_signed')->D->delete_by_field(array('os_id'=>$os_id));

	if($sid) {
		$backdata	= $this->S->dao('process')->D->get_one_by_field(array('id'=>$os_id),'order_id');
		echo $backdata['order_id'];
	}else{
		echo '0';
	}

}

/*显示标记*/
elseif($detail == 'show'){
	$backdata = $this->S->dao('order_signed')->D->get_one_by_field(array('os_id'=>$os_id),'os_desc,os_cuser,os_cdate');
	echo json_encode($backdata);
}

if($detail == 'list'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>

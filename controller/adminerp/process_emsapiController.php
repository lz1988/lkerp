<?php
/*
 * Created on 2012-6-27
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */




if($detail == 'list'){

	$stypemu = array(
		'statu-a-e'=>'订单状态：',
		'order_id-s-l'=>'订单号：',
	);

	$statuarr = array('1'=>'已接收','2'=>'待发货','3'=>'已出库');
	if(empty($statu)) { $sqlstr = ' and statu="1" ';$statu = 1;}
	$InitPHP_conf['pageval'] = 15;

	$sqlstr 	.= ' and p.provider_id="10" and (p.protype="售出" or p.protype="重发") and p.isover="N"  and locate("'.addslashes(json_encode('e邮宝')).'",p.extends,1)>0 ';
	$datalist 	= $this->S->dao('process')->get_emsorder($sqlstr);

	/*数据处理*/
	for($i = 0; $i < count($datalist); $i++){

		$extends = json_decode($datalist[$i]['extends'],true);
		$datalist[$i]['shipping'] = $extends['e_shipping'];

		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd'] = '';
		}else{
			$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
		}
	}

	/*数据显示*/
	$displayarr = array();
	$tablewidth = '1100';

	$displayarr['id'] 	 			= array('showname'=>'checkbox','width'=>'50','title'=>'反选');
	$displayarr['order_idd']		= array('showname'=>'订单号','width'=>'80');
	$displayarr['fid']				= array('showname'=>'第三方单号','width'=>'150');
	$displayarr['sku']				= array('showname'=>'SKU','width'=>'80');
	$displayarr['product_name']		= array('showname'=>'产品名称','width'=>'250');
	$displayarr['quantity']			= array('showname'=>'数量','width'=>'50');
	$displayarr['cuser']			= array('showname'=>'制单人','width'=>'60');
	$displayarr['shipping']			= array('showname'=>'发货方式','width'=>'80');
	$displayarr['warehouse']		= array('showname'=>'发货仓库','width'=>'100');

	$bannerstr = '<button onclick=get_ems() class="six">获取运单编号</button>';
	$jslink = "<script src='./staticment/js/process_shipment.js'></script>\n";

	$this->V->mark(array('title'=>'EMS'));
	$temp = 'pub_list';
}


/*取得运单编号*/
elseif($detail == 'get_ems'){
	$strid 		= stripslashes($strid);
	$productobj = $this->S->dao('product');
	$datalist 	= $this->S->dao('process')->D->get_allstr(' and id in('.$strid.')','','id asc','order_id,pid,product_name,quantity,extends');
	$productstr = '';

    //判断国家US|AU(美国或者澳大利亚)
    $arrCountry  = array(
            'us'            =>'UNITED STATES OF AMERICA',
            'united states' =>'UNITED STATES OF AMERICA',
            'australia'     =>'AUSTRALIA',
            'au'            =>'AUSTRALIA'
    );
        
	for($i=0; $i<count($datalist); $i++){
	   
	   	/*将扩展数据解压*/
		$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);
        $e_country = strtolower($datalist[$i]['e_country']);

        if (!in_array($e_country,array_keys($arrCountry))){
            
            exit("目的国家错误，只能是United States或简码US ，Australia或简码AU! ");
        }

		$backdata_weight = $productobj->D->get_one_by_field(array('pid'=>$datalist[$i]['pid']),'shipping_weight');

		/*商品信息*/
		$productstr.= '<item>';
		$productstr.= '<enname><![CDATA['.$datalist[$i]['product_name'].']]></enname>';
		$productstr.= '<count>'.$datalist[$i]['quantity'].'</count>';
		$productstr.= '<weight>'.$backdata_weight['shipping_weight'].'</weight>';
		$productstr.= '<delcarevalue>0</delcarevalue>';
		$productstr.= '<origin>CN</origin>';
		$productstr.= '</item>';
	}
 
	$xmldata = '<?xml version="1.0" encoding="UTF-8"?>';
	$xmldata.= '<orders xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
	$xmldata.= '<order>';
	$xmldata.= '<orderid>'.$datalist['0']['order_id'].'</orderid>'; //订单号
	$xmldata.= '<clcttype>0</clcttype>';
	$xmldata.= '<pod>false</pod>';
	$xmldata.= '<untread>Returned</untread>';
	$xmldata.= '<printcode>1</printcode>';

	/*寄件人信息*/
	$xmldata.= '<sender>';
	$xmldata.= 		'<name>Mr.WU</name>';
	$xmldata.= 		'<postcode>518033</postcode>';
	$xmldata.= 		'<country>CN</country>';
	$xmldata.= 		'<province>440000</province>';//省份代码(广东)
	$xmldata.= 		'<city>440300</city>';//城市代码
	$xmldata.= 		'<county>440305</county>';//区县代码(南山)
	$xmldata.= 		'<street>NAHHAI E COOLBUILDING 1#204，NO.6 XINGHUA ROAD,SHEKOU NANSHAN DISTRICT</street>';
	$xmldata.= '</sender>';

	/*接收人信息*/
	$xmldata.= '<receiver>';
	$xmldata.= 		'<name>'.$datalist['0']['e_receperson'].'</name>';
	$xmldata.=		'<postcode>'.$datalist['0']['e_post_code'].'</postcode>';
	$xmldata.=		'<phone>'.$datalist['0']['e_tel'].'</phone>';
	$xmldata.=		'<mobile>'.$datalist['0']['e_tel'].'</mobile>';
    
    //获取目的国家
    $e_country = strtolower($datalist[0]['e_country']);
    $country = $arrCountry[$e_country];
        
	$xmldata.=		'<country>'.$country.'</country>';
	$xmldata.=		'<province>'.$datalist['0']['e_state'].'</province>';
	$xmldata.=		'<city>'.$datalist['0']['e_city'].'</city>';
	$xmldata.=		'<street>'.$datalist['0']['e_address1'].$datalist['0']['e_address2'].'</street>';
	$xmldata.= '</receiver>';

	/*揽件人信息*/
	$xmldata.= '<collect>';
	$xmldata.= 		'<name>巫建才</name>';
	$xmldata.= 		'<postcode>518033</postcode>';
	$xmldata.= 		'<country>CN</country>';
	$xmldata.= 		'<province>440000</province>';//省份代码(广东)
	$xmldata.= 		'<city>440300</city>';//城市代码
	$xmldata.= 		'<county>440305</county>';//区县代码(南山)
	$xmldata.= 		'<street>NAHHAI E COOLBUILDING 1#204，NO.6 XINGHUA ROAD,SHEKOU NANSHAN DISTRICT</street>';
	$xmldata.= '</collect>';

	/*商品信息*/
	$xmldata.= '<items>';
	$xmldata.= 		$productstr;
	$xmldata.= '</items>';
	$xmldata.= '</order>';
	$xmldata.= '</orders>';

	//echo $xmldata;exit();
    //lktown_cbbd1430746236309682a45daa5ee4e3
	$url 		= 'http://www.ems.com.cn/partner/api/public/p/order/';
	$authention = 'lktown_cbbd1430746236309682a45daa5ee4e3';
	$header		= array("Content-type: text/xml\nversion: international_eub_us_1.1\nauthenticate: $authention");//授权码和版本号
	$ch			= curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
	$response = curl_exec($ch);

	//echo $response;exit();
	if(curl_errno($ch))
	{
	    print curl_error($ch);
	}

	curl_close($ch);
	unset($ch);

	/*匹配出运单编号，生成标签文件*/
	preg_match('/<mailnum>[\w]+<\/mailnum>/',$response,$mail_numarr);

	if($mail_numarr) {//成功则截取运单编号
		$mail_num = $mail_numarr['0'];
		$mail_num = strtr($mail_num,array('<mailnum>'=>'','</mailnum>'=>''));
	}else{//否则输出错误信息
		echo $response;exit();
	}

	$tab_url = 'http://labels.ems.com.cn/partner/api/public/p/static/label/download/';
	$tab_url.= md5($authention.$mail_num).'/'.$mail_num.'.pdf';

	header("refresh:1" . $time . ";url=" .$tab_url. "");
}


/*检查所选的订单ID是否属于不同订单，如果是则弹出提示*/
elseif($detail == 'checkemsid'){

	$strid 		= stripslashes($strid);
	$datalist 	= $this->S->dao('process')->D->get_allstr(' and id in('.$strid.')','','','order_id');
	for($i=1; $i<count($datalist); $i++){
		if($datalist[$i]['order_id'] != $datalist[$i-1]['order_id']){
			echo '1';exit();
		}
	}

}


if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

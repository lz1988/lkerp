<?php
/*
 * Created on 2012-03-09
 *
 * by wall
 * @title 生成订单
 */

/*
 * create on 2012-05-21
 * by wall
 * 优化发货方式映射，现在从erp系统中获取
 * */
$temp_list = $this->S->dao('shipping_map')->get_all_list('');

$amazon_shipping = array();
$ebay_shipping = array();

foreach ($temp_list as $val) {
	if ($val['sm_type'] == 'ebay') {
		$ebay_shipping[$val['sm_name']] = $val['s_name'];
	}
	elseif ($val['sm_type'] == 'amazon') {
		$amazon_shipping[$val['sm_name']] = $val['s_name'];
	}
}

$ebay_warehouse = array(
	'CN'  => '中国仓库-蛇口',
	'CNR' => '中国仓库-蛇口',
	'UK'  => 'KM-Amazon-UK仓库',
	'US'  => 'AM-Amazon-US仓库',
	'USW' => 'WI-加州william仓'
);

$amazon_warehouse = array(
	'CN'  => '中国仓库-蛇口',
	'GB'  => 'KM-Amazon-UK仓库',
	'US'  => 'AM-Amazon-US仓库'
);

$ebay_status = array(
	'Uncleared' => '1',
	'Completed' => '1',
	'Cleared' => '1',
	'Unclaimed' => '1',
	'Completed - Funds not yet available' => '1'
);

$ebay_type = array(
	'Express Checkout Payment Received' => '1',
	'Mobile Payment Received' => '1',
	'Mobile Express Checkout Payment Received' => '1',
	'Update to eCheck Received' => '1',
	'Update to Payment Received' => '1',
	'Express Checkout Payment Received' => '1'
);

// ebay销售帐号映射
$ebay_sold_arr = array(
	'pingpeonpay@gmail.com' => 'pingpeon',
	'loso83pay@gmail.com' => 'loso',
	'ataripay@gmail.com' => 'atari',
	'liyinwen2011@hotmail.com' => 'eclub',
	'liyinwen02@hotmail.com' => 'anydeal'
);

// amazon销售帐号映射
$amazon_sold_arr = array(
	'US' => 'Amazon us',
	'PR' => 'Amazon us',
	'UK' => 'Amazon uk',
	'FR' => 'Amazon fr'
);

// ebay收付款帐号映射
$ebay_finance_arr = array(
	'pingpeonpay@gmail.com' => 'loftkey.sales06@gmail.com',
	'loso83pay@gmail.com' => 'loftkey.sales06@gmail.com',
	'ataripay@gmail.com' => 'loftkey.sales06@gmail.com',
	'liyinwen2011@hotmail.com' => 'liyingwen@hotmail.com',
	'liyinwen02@hotmail.com' => 'liyingwen@hotmail.com'
);

//取上传的文件的数组
$upload_dir = "./data/uploadexl/order_combine/";//上传文件保存路径的目录
$fieldarray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
					'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');//有效的excel列表值
$head = 1;//以第一行为表头

//ebay双excel表生成订单
if ($detail=='ebay') {
	//第一上传文件数据
	$all_arr1 =  $this->C->Service('upload_excel')->get_upload_excel_datas_one($upload_dir, $fieldarray, $head);
	//第二上传文件数据
	$all_arr2 =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);

	if ($all_arr1 && $all_arr2) {
		$buyerid_array = array();
		$sum = count($all_arr1);
		$sum1 = count($all_arr2);
		for($i=1; $i < $sum; $i++){
			//筛选订单
			if ($ebay_status[$all_arr1[$i]['Status']] == '1' && $ebay_type[$all_arr1[$i]['Type']] == '1' ) {
				//映射两excel表数据，生成有效数据，合并到数组1
				for($j=1; $j < $sum1; $j++) {
					if ($all_arr2[$j]['User Id'] == $all_arr1[$i]['Buyer ID']) {
						$all_arr1[$i]['qty'] = $all_arr2[$j]['Quantity'];
						$all_arr1[$i]['sku'] = $all_arr2[$j]['Custom Label'];
						$all_arr1[$i]['shipping'] = $all_arr2[$j]['Shipping Service'];
						$all_arr1[$i]['phone'] = $all_arr2[$j]['Buyer Phone Number'];
						unset($all_arr2[$j]);
						break;
					}
				}

				$mail = split('@',$all_arr1[$i]['To Email Address']);
				$mailname = $mail[0];
				//$all_arr1[$i]['fid'] = $mailname.'-'.date('Ymd', time()).'-'.$i;
				$all_arr1[$i]['fid'] = 'ebay-'.$ebay_sold_arr[$all_arr1[$i]['To Email Address']];
				$addresswaring = 0;

				$all_arr1[$i]['sold_account'] = $ebay_sold_arr[$all_arr1[$i]['To Email Address']];
				$all_arr1[$i]['payrec_account'] = $ebay_finance_arr[$all_arr1[$i]['To Email Address']];

				for ($j=0; $j<strlen($all_arr1[$i]['Shipping Address']); $j++) {
					//判断地址列是否有ASC=26 63 以及乱码
					if (ord($all_arr1[$i]['Shipping Address'][$j])>128 || ord($all_arr1[$i]['Shipping Address'][$j])==26 || ord($all_arr1[$i]['Shipping Address'][$j])==63) {
						$addresswaring++;
					}
				}
				if ($addresswaring) {
					$all_arr1[$i]['warning'] .= '<font color=red>address</font>&nbsp;';
				}
				$namewaring = 0;
				for ($j=0; $j<strlen($all_arr1[$i]['Name']); $j++) {
					//判断收件人列是否有ASC=26 63 以及乱码
					if (ord($all_arr1[$i]['Name'][$j])>128 || ord($all_arr1[$i]['Name'][$j])==26 || ord($all_arr1[$i]['Name'][$j])==63) {
						$namewaring++;
					}
				}
				if ($namewaring) {
					$all_arr1[$i]['warning'] .= '<font color=red>name</font>&nbsp;';
				}
				//映射产生erp中shipping
				$all_arr1[$i]['shipping'] = $ebay_shipping[$all_arr1[$i]['shipping']];

				//截取sku，生成erp中sku，以及某些shipping后缀
				$temp = split('-',$all_arr1[$i]['sku']);
				$length = count($temp);
				//发货仓库
				$lang = $temp[$length - 1];
				$all_arr1[$i]['warehouse'] = $ebay_warehouse[$lang];

				//组成新的SKU
				if ($length == 4 && strlen($temp[0]) == 2 && strlen($temp[1]) == 4 && strlen($temp[2]) == 3) {
					$all_arr1[$i]['sku'] = $temp[0].'-'.$temp[1].'-'.$temp[2];
				}
				else {
					$all_arr1[$i]['warning'] .= '<font color=red>sku<font>&nbsp;';
				}
				//香港小包加发货方式
				if ('香港小包' == $all_arr1[$i]['shipping']) {
					if ($lang == 'CNR' ) {
						$all_arr1[$i]['shipping'] .= '挂号';
					}
				}
				$buyerid_array[$all_arr1[$i]['Buyer ID']]++;

			}
			else {
				unset($all_arr1[$i]);
			}
		}
		for($i=1; $i < $sum; $i++){
			if ($buyerid_array[$all_arr1[$i]['Buyer ID']]>1) {
				$all_arr1[$i]['warning'] .= '<font color=red>'.$all_arr1[$i]['Buyer ID'].'</font> ';
			}
		}

		/*删除空白一行*/
		unset($all_arr1['0']);
		$filename = 'ebay_order_'.time();
		$head_array = array(
			'warning' => '<font color=red>warning(此列用于提醒，修改后请删除此列再上传)</font>'
			, 'Transaction ID'=>'deal_id'
			, 'fid' => '3rd_part_id'
			, 'sku' => 'sku'
			, 'listing' => 'listing'
			, 'qty'=>'quantity'
			, 'Gross'=>'item_price'
			, 'item_tax'=>'item_tax'
			, 'shipping_price' => 'shipping_price'
			, 'shipping_tax' => 'shipping_tax'
			, 'Fee'=>'performance_fee'
			, 'shipping_fee'=>'shipping_fee'
			, 'Currency'=>'currency'
			, 'sold_account' => 'sold_account'
			, 'payrec_account' => 'payrec_account'
			, 'warehouse' => 'warehouse'
			, 'shipping' => 'shipping'
			, 'Name'=>'receive_person'
			, 'Buyer ID'=>'buyer_id'
			, 'phone'=>'tel'
			, 'From Email Address' => 'email'
			, 'Address Line 1' => 'address1'
			, 'Address Line 2/District/Neighborhood'=>'address2'
			, 'Town/City'=>'city'
			, 'State/Province/Region/County/Territory/Prefecture/Republic'=>'state'
			, 'Country' => 'country'
			, 'Zip/Postal Code' => 'post_code'
			, 'Note' => 'comment'
			, 'Status' => 'status'

		);

		$this->C->service('upload_excel')->download_xls($filename,$head_array,$all_arr1);
	}


	$message_upload = '<font size=-1>订单表</font>： <input name="upload_file1" type="file" /><br><font size=-1>映射表</font>：';

	/*定义一个隐藏table，防止调用了拖动列宽的公共JS报错*/
	$tablelist = '<table id="mytable" style="display:none"><tr></tr></table>';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'<a href="index.php?action=process_shipment&detail=list">出货列表</a> &raquo; <a href="index.php?action=process_shipment&detail=import_exl">选择导入类型</a> &raquo; 生成ebay订单','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl,'message_upload'=>$message_upload));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}
elseif ($detail == 'amazon') {

    if(!$this->C->service('admin_access')->checkResRight('r_w_addmod')){$this->C->sendmsg();}
	$sku_alias = $this->S->dao('sku_alias');
	//上传文件数据
	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);

	if ($all_arr) {
		$num = count($all_arr);
		for($i=1; $i < $num; $i++){
			$all_arr[$i]['shipping'] = $amazon_shipping[$all_arr[$i]['ship-service-level']];
			//使用FBA自动发货那张表生成导入订单时
			//第三方单号为merchant-order-id
			//buyer_id为amazon-order-id
			//产品数量为quantity-shipped
			if ($all_arr[$i]['merchant-order-id'] != '') {
				$all_arr[$i]['deal_id'] = $all_arr[$i]['merchant-order-id'];
			}
			if ($all_arr[$i]['amazon-order-id'] != '') {
				$all_arr[$i]['order-id'] = $all_arr[$i]['amazon-order-id'];
			}
			if ($all_arr[$i]['quantity-shipped'] != '') {
				$all_arr[$i]['quantity-purchased'] = $all_arr[$i]['quantity-shipped'];
			}
			//amazon的sku放在listing中
			$all_arr[$i]['listing'] = $all_arr[$i]['sku'];
			//FBA表生成导入订单时，Status定义为Outputed，以为直接出库
			$all_arr[$i]['Status'] = 'Completed';
			if ($all_arr[$i]['sales-channel'] != '') {
				$all_arr[$i]['Status'] = 'Outputed';
			}

			//sku别名替换成ERP sku
			$rs = $sku_alias->get_sku_by_code($all_arr[$i]['sku']);
			if ($rs) {
				$all_arr[$i]['sku'] = $rs;
			}
			else {
				$all_arr[$i]['sku'] = '<font color="red">'.$all_arr[$i]['sku'].'</font>';
			}
			$isasc = 0;
			for ($j=0; $j<strlen($all_arr[$i]['recipient-name']); $j++) {
				//判断收件人列是否有乱码
				if (ord($all_arr[$i]['recipient-name'][$j])>128){
					$isasc = 1;
				}
			}
			if ($isasc) {
				$all_arr[$i]['recipient-name'] = '<font color="red">'.$all_arr[$i]['recipient-name'].'</font>';
			}

			//非FBA自动生成订单的仓库，通过第三方单号获取
			if (strstr(strtolower($all_arr[$i]['deal_id']),'amazon') === false ) {
				$all_arr[$i]['warehouse'] = $amazon_warehouse['CN'];
			}
			elseif (strstr(strtolower($all_arr[$i]['deal_id']), 'uk') === false) {
				$all_arr[$i]['warehouse'] = $amazon_warehouse['US'];
			}
			else {
				$all_arr[$i]['warehouse'] = $amazon_warehouse['GB'];
			}

			//FBA自动的，根据收获仓库获取
			if ($all_arr[$i]['amazon-order-id'] != '') {
				if ($all_arr[$i]['ship-country']=='GB') {
					$all_arr[$i]['warehouse'] = $amazon_warehouse['GB'];
				}
				else {
					$all_arr[$i]['warehouse'] = $amazon_warehouse['US'];
				}
			}

			//第三方单号不存在的，将order_id赋给第三方单号
			if ($all_arr[$i]['deal_id'] == '') {
				$all_arr[$i]['deal_id'] = '<font color="blue">'.$all_arr[$i]['order-id'].'</font>';
			}
			else {
				if ($all_arr[$i]['Status'] == 'Outputed') {
					$all_arr[$i]['Status'] = 'Compared';
					$thirdid[$all_arr[$i]['deal_id']] ++;
					if ($thirdid[$all_arr[$i]['deal_id']] > 1) {
						unset($all_arr[$i]);
					}
				}
			}
			$all_arr[$i]['sold_account'] = $amazon_sold_arr[$all_arr[$i]['ship-country']];
			$all_arr[$i]['payrec_account'] = 'USD-美国银行60845';
		}

		/*删除空白一行*/
		unset($all_arr['0']);
		$filename = 'amazon_order_'.time();
		$head_array = array(
			  'deal_id'				=> 'deal_id'//平台订单号，第三方单号让销售手动编辑
			, 'empty' 				=> '3rd_part_id'
			, 'sku' 				=> 'sku'
			, 'listing' 			=> 'listing'
			, 'quantity-purchased'	=> 'quantity'
			, 'item-price'			=> 'item_price'
			, 'item-tax'			=> 'item_tax'
			, 'shipping-price' 		=> 'shipping_price'
			, 'shipping-tax' 		=> 'shipping_tax'
			, 'performance-fee'		=> 'performance_fee'
			, 'shipping-fee'		=> 'shipping_fee'
			, 'currency'			=> 'currency'
			, 'sold_account' 		=> 'sold_account'
			, 'payrec_account' 		=> 'payrec_account'
			, 'warehouse' 			=> 'warehouse'
			, 'shipping' 			=> 'shipping'
			, 'recipient-name'		=> 'receive_person'
			, 'order-id'			=> 'buyer_id'
			, 'ship-phone-number'	=> 'tel'
			, 'buyer-email' 		=> 'email'
			, 'ship-address-1' 		=> 'address1'
			, 'ship-address-2'		=> 'address2'
			, 'ship-city'			=> 'city'
			, 'ship-state'			=> 'state'
			, 'ship-country' 		=> 'country'
			, 'ship-postal-code' 	=> 'post_code'
			, 'comment'				=> 'comment'
			, 'Status' 				=> 'status'

		);

		$this->C->service('upload_excel')->download_xls($filename,$head_array,$all_arr);
	}

	/*定义一个隐藏table，防止调用了拖动列宽的公共JS报错*/
	$tablelist = '<table id="mytable" style="display:none"><tr></tr></table>';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'<a href="index.php?action=process_shipment&detail=list">出货列表</a> &raquo; <a href="index.php?action=process_shipment&detail=import_exl">选择导入类型</a> &raquo; 生成amazon订单','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}

/*ebay生成最终表*/
elseif($detail == 'ebayfinal'){

    if(!$this->C->service('admin_access')->checkResRight('r_w_addmod')){$this->C->sendmsg();}
	//取得上传表格
	if($_FILES["upload_file"]["name"]){

		$data_error			= 0;
		$upload_dir 		= "./data/uploadexl/orders/";//上传文件保存路径的目录
		$upload_exl_service	=$this->C->Service('upload_excel');
		$fieldarray 		= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB');//有效的excel列表值
		$datalist			=  $upload_exl_service->get_upload_excel_datas($upload_dir, $fieldarray, 1);

		$objskua_lias		= $this->S->dao('sku_alias');
		$objproduct			= $this->S->dao('product');
		$rexsold			= $this->S->dao('sold_relation_conf');
		$upload_exl_service->checkmod_head(&$datalist,&$data_error,'order_shipment');//表头检测


		/*执行生成最终销售明细表*/
		if(!$data_error && $datalist){

			foreach($datalist as &$val){

				$val['erp_sku'] = $val['sku'];

				/*查金碟SKU*/
				$val['jin_sku']		= $objskua_lias->D->get_one(array('pro_sku'=>$val['sku'],'sold_way'=>'金碟'),'sku_code');
				/*查品名*/
				$val['deal_pname']	= $val['erp_pname'] = $objproduct->D->get_one(array('sku'=>$val['sku']),'product_name');

				$val['paypalfee']	= $val['performance_fee'];//费用

				/*如果没填写收款帐号*/
				if(empty($val['payrec_account']))	$val['payrec_account'] = $rexsold->get_default_payrecByname($val['sold_account']);

			}

			$head_array = array(
				'date' 			=> '日期',
				'deal_id' 		=> '平台订单号',
				'3rd_part_id'	=> '第三方单号',
				'sku'	 		=> '平台SKU',
				'erp_sku' 		=> 'ERP SKU',
				'jin_sku' 		=> '金碟SKU',
				'deal_pname'	=> '产品名称',
				'erp_pname' 	=> '中文描述',
				'quantity'	 	=> '数量',
				'currency'		=> '币别',
				'item_price'	=> '收入',
				'shipprice' 	=> '运费收入',
				'shipfee'	 	=> 'amazon代收运费',
				'amazonfee' 	=> 'amazon fee',
				'otherfee' 		=> '其它平台费用',
				'paypalfee' 	=> 'paypal费',
				'ebayfee' 		=> 'ebay fee',
				'shipping' 		=> '发货方式',
				'warehouse' 	=> '发货仓库',
				'shipment_date'	=> '发货时间',
				'country'	 	=> '国家',
				'sold_account'	=> '销售账号',
				'payrec_account'=> '收款账号',
				'cuser' 		=> '制单人',
				'comment' 		=> '备注',
			);

			/*弹出下载*/
			$filename	= 'ebayfinal_detail_'.date('Y-m-d',time()).'_'.mt_rand(00,99);
			$this->C->service('upload_excel')->download_xls($filename,$head_array,$datalist);
		}elseif($data_error){
			$exl_error_msg = '表头有错，请检查！';
		}

		unset($datalist);
		unlink($_SESSION['filepath']);//删除原文件

	}

	/*定义一个隐藏table，防止调用了拖动列宽的公共JS报错*/
	$tablelist = '<table id="mytable" style="display:none"><tr></tr></table>';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'<a href="index.php?action=process_shipment&detail=list">出货列表</a> &raquo; <a href="index.php?action=process_shipment&detail=import_exl">选择导入类型</a> &raquo; ebay生成最终表','exl_error_msg'=>$exl_error_msg,'exl_error_width'=>600,'tablelist'=>$tablelist));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}

/*导入amazon出库表，直接出库*/
elseif($detail == 'outamazon'){
    if(!$this->C->service('admin_access')->checkResRight('r_w_addmod')){$this->C->sendmsg();}

	/*实例化表格操作服务*/
	$upload_exl_service 	= $this->C->Service('upload_excel');
	$upload_exl_orders		= $this->C->Service('order');

	/*上传配置*/
	$upload_dir 		= './data/uploadexl/temp/';//上传文件保存路径的目录
	$fieldarray 		= array('A','B','C','D','E','G','J','L','M','N','O','Q','S','T','U','V','W','X','AC','AD','AF','AG','AH','AI');//有效的excel列值
	$head 				= 1;//以第一行为表头

	/*读取上传的文件内容并写入数据库保存*/
	if($filepath){

		/*锁上标记表*/
		$backurl	= 'index.php?action=process_order&detail=outamazon';
		if(!$upload_exl_orders->checklock('begin', $backurl)) $this->C->sendmsg($backurl);
		$all_arr	= $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray, $head);
		unlink($filepath);

		$hid		= 0;//接收隐藏表单传过来的值
		$oid		= 0;//用于加订单号
		$dataerror	= 0;
		$repeatrow	= 0;//重复或以CN-MY开头的记录
		$process	= $this->S->dao('process');
		$product	= $this->S->dao('product');
		$exchrate	= $this->S->dao('exchange_rate');
        $product_cost = $this->S->dao('product_cost');

		/*用于根据帐号来取得渠道ID与收款帐号ID*/
		$backArr	= $upload_exl_orders->getIdBysoldaccount();

		/*检测发货仓库无误后取得ID*/
		$backhoidArr= $upload_exl_orders->getEsseidByname('esse',' and type="2"','name');

		/*取最大单号*/
		$max 		= $this->C->service('warehouse')->get_maxorder_manay('出仓单','x',$process);


		/*实例化自动包含文件用于保存即时成本与币别*/
		$this->C->service('exchange_rate');
		$finansvice 	= $this->C->service('finance');
        
        //日期转化公共函数
        $global = $this->C->service('global');

		$process->D->query('begin');

		for($i=1;$i<count($all_arr);$i++){

			/*若由于数据太多超过POST限值导致传过来的PID有漏，则跳出循环*/
			if(empty($pid[$hid])) {$dataerror++;$msg='数据过多，请分批上传！';break;}

			$fid		= empty($all_arr[$i]['merchant-order-id'])?$all_arr[$i]['amazon-order-id']:$all_arr[$i]['merchant-order-id'];//第三方单号没填写则自动取出库单号
			//$backcheckid= $process->D->get_one(array('fid'=>$fid,'pid'=>$pid[$hid],'deal_id'=>$all_arr[$i]['amazon-order-id'],'comment2'=>$all_arr[$i]['shipment-item-id']),'id');


			/*若不存在重复并且第三方单号不以CN和MY开头，则插入*/
			$preTwo	= substr($all_arr[$i]['merchant-order-id'],0,2);
			//if(!$backcheckid && $preTwo != 'CN' && $preTwo != 'MY') {
            if($preTwo != 'CN' && $preTwo != 'MY') {
				$inesrt_arr	= array();
				/*取最大单号*/
				$order_id 	= 'x'.sprintf("%07d",substr($max,1) + $oid);$oid++;

				/*扩展内容处理*/
				$extends	= array('e_item_tax'=>$all_arr[$i]['item-tax'],'e_shipping_price'=>$all_arr[$i]['shipping-price'],'e_shipping_tax'=>$all_arr[$i]['shipping-tax'],'e_performance_fee'=>'','e_shipping_fee'=>'','e_shipping'=>$all_arr[$i]['shipping'],'e_receperson'=>$all_arr[$i]['buyer-name'],'e_tel'=>'','e_email'=>$all_arr[$i]['buyer-email'],'e_address1'=>str_replace('"','',$all_arr[$i]['ship-address-1']),'e_address2'=>str_replace('"','',$all_arr[$i]['ship-address-2']),'e_city'=>$all_arr[$i]['ship-city'],'e_state'=>$all_arr[$i]['ship-state'],'e_country'=>$all_arr[$i]['ship-country'],'e_post_code'=>$all_arr[$i]['ship-postal-code']);

				/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
				$extends 	= get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

				/*非amazon订单无订单创建时间，取amazon代发货的出库时间*/
				$cdate		= empty($all_arr[$i]['purchase-date'])?$all_arr[$i]['shipment-date']:$all_arr[$i]['purchase-date'];

				/*时间转换，取消时差*/
				//$cdate		= date('Y-m-d H:i:s',strtotime($cdate)-8*3600);
				//$rdate		= date('Y-m-d H:i:s',strtotime($all_arr[$i]['reporting-date'])-8*3600);
                
                $cdate = $global->changetime_ymdhis($cdate);
                $rdate = $global->changetime_ymdhis($all_arr[$i]['reporting-date']);

				/*保存即时成本，币别统一转换本位币(CNY)*/
				$price2 	= $finansvice->get_productcost($pid[$hid]);

                $child_pid  = $process->output_child_sku(' and s.pid='.$pid[$hid]);
                if (count($child_pid)>1){
                    foreach($child_pid as $val){
                        $arprice += $val['cost3'];
                    }
                    
                    foreach($child_pid as $v){
                        //组合sku的子sku订单检测，数据表是否有数据，有就跳过执行
                        $backcheckid= $process->D->get_one(array('fid'=>$fid,'pid'=>$v['pid'],'deal_id'=>$all_arr[$i]['amazon-order-id'],'comment2'=>$all_arr[$i]['shipment-item-id']),'id');
                        
                        if (!$backcheckid){
                            $skuquantity = $v['quantity'] * $all_arr[$i]['quantity-shipped'];
                            $iprice = $all_arr[$i]['item-price'];
                            //组合的sku，子sku的比例售价
                            $price = $v['cost3'] * $iprice/$arprice;
                            $insert_arr 	= array(
                                'market_price'  =>$v['cost3'],
            					'provider_id'	=>$backhoidArr[$all_arr[$i]['warehouse']],
            					'sku'			=>$v['sku'],
            					'product_name'	=>$v['product_name'],
            					'fid'			=>$fid,
            					'deal_id'		=>$all_arr[$i]['amazon-order-id'],
            					'pid'			=>$v['pid'],
            					'price'			=>$price,
            					'price2'		=>$v['cost1'],
            					'stage_rate'	=>$exchrate->D->get_one(array('code'=>$all_arr[$i]['currency'],'isnew'=>1),'stage_rate'),
            					'coin_code'		=>$all_arr[$i]['currency'],
            					'quantity'		=>$skuquantity,
            					'cdate'			=>$cdate,
            					'mdate'			=>$rdate,
            					'rdate'			=>$rdate,
            					'cuser'			=>$_SESSION['eng_name'],
            					'muser'			=>$_SESSION['eng_name'],
            					'ruser'			=>$_SESSION['eng_name'],
            					'order_id'		=>$order_id,
            					'buyer_id'		=>$all_arr[$i]['buyer-name'],
            					'active'		=>1,
            					'output'		=>1,
            					'property'		=>'出仓单',
            					'protype'		=>'售出',
            					'statu'			=>3,
            					'extends'		=>$extends,
            					'comment2'		=>$all_arr[$i]['shipment-item-id'],//将此当作游流跟踪号
            					'sold_way'		=>$backArr[$all_arr[$i]['sold_account']]['swid'],
            					'sold_id'		=>$backArr[$all_arr[$i]['sold_account']]['said'],
            					'payrec_id'		=>$backArr[$all_arr[$i]['sold_account']]['fpaid'],
    				        );
                            $sid = $process->D->insert($insert_arr);
                            if(!$sid) $dataerror++;
                        }else{
                            $repeatrow++;
                        }
                    }
                }else{
                    
                    //sku检测是否订单存在，有就跳过执行
                    $backcheckid= $process->D->get_one(array('fid'=>$fid,'pid'=>$pid[$hid],'deal_id'=>$all_arr[$i]['amazon-order-id'],'comment2'=>$all_arr[$i]['shipment-item-id']),'id');
                    if (!$backcheckid){
                        /*取得产品名称与SKU*/
    				    $backproduc	= $product->D->get_one(array('pid'=>$pid[$hid]),'sku,product_name');
                        
                        /*市场指导价参考*/
                        $market_price = $product_cost->D->get_one_by_field(array('pid'=>$pid[$hid]),'cost3');
                        
        				$insert_arr	= array(
                            'market_price'  =>$market_price['cost3'],
        					'provider_id'	=>$backhoidArr[$all_arr[$i]['warehouse']],
        					'sku'			=>$backproduc['sku'],
        					'product_name'	=>$backproduc['product_name'],
        					'fid'			=>$fid,
        					'deal_id'		=>$all_arr[$i]['amazon-order-id'],
        					'pid'			=>$pid[$hid],
        					'price'			=>$all_arr[$i]['item-price'],
        					'price2'		=>$price2,
        					'stage_rate'	=>$exchrate->D->get_one(array('code'=>$all_arr[$i]['currency'],'isnew'=>1),'stage_rate'),
        					'coin_code'		=>$all_arr[$i]['currency'],
        					'quantity'		=>$all_arr[$i]['quantity-shipped'],
        					'cdate'			=>$cdate,
        					'mdate'			=>$rdate,
        					'rdate'			=>$rdate,
        					'cuser'			=>$_SESSION['eng_name'],
        					'muser'			=>$_SESSION['eng_name'],
        					'ruser'			=>$_SESSION['eng_name'],
        					'order_id'		=>$order_id,
        					'buyer_id'		=>$all_arr[$i]['buyer-name'],
        					'active'		=>1,
        					'output'		=>1,
        					'property'		=>'出仓单',
        					'protype'		=>'售出',
        					'statu'			=>3,
        					'extends'		=>$extends,
        					'comment2'		=>$all_arr[$i]['shipment-item-id'],//将此当作游流跟踪号
        					'sold_way'		=>$backArr[$all_arr[$i]['sold_account']]['swid'],
        					'sold_id'		=>$backArr[$all_arr[$i]['sold_account']]['said'],
        					'payrec_id'		=>$backArr[$all_arr[$i]['sold_account']]['fpaid'],
        				);
    
                        $sid = $process->D->insert($insert_arr);
                        if(!$sid) $dataerror++;
                    }else{
                        $repeatrow++;
                    }
                }
                
			}//else{
			//	$repeatrow++;//记录重复或以CN、MY开头的记录数。
			//}
			$hid++;

		}

		if(empty($dataerror)) {
			$process->D->query('commit');
			$msg.= '执行完毕，添加记录数 '.$oid.' 条；';
			$msg.= $repeatrow?'\n已录过的或以MY、CN开头的记录数 '.$repeatrow.' 条自动跳过。':'';
		}else{
			$process->D->query('rollback');
			$msg.= '保存失败，请重试';
		}

		/*解锁表*/
		$did = $upload_exl_orders->checklock('end');
		if($did){$this->C->success($msg,$backurl);}else{$upload_exl_orders->checklock('end');$this->C->success($msg,$backurl);}


	}else{
		$data_error 		= 0;
		$tablelist 		    = '<table id="mytable">';


		/*上传并获得数据*/
		$all_arr 			= $upload_exl_service->get_upload_excel_datas($upload_dir , $fieldarray, $head);
		$filepath			= $_SESSION['filepath'];
		$obj_skualia		= $this->S->dao('sku_alias');
		$fbden_arr			= $this->C->service('global')->sys_settings('check_fbden_whouse','sys','json');//禁止直接出库的仓库，防止中国发货被直接导出

		/*检测发货仓库无误后取得ID*/
		$backhoidArr		= $upload_exl_orders->getEsseidByname('esse',' and type="2"','name');

		/*表头检测，若有错，显示表头*/
		$tablelist		   .= $upload_exl_service->checkmod_head(&$all_arr,&$data_error,'amazon_outbond');

		/*检测销售账号用*/
		$backsoacArr		= $upload_exl_service->upcel_check('sold_account','account_name');

		/*检测发货仓库用*/
		$backhousArr		= $upload_exl_service->upcel_check('esse','name',' and type="2"');

		/*检测发货方式用*/
		$backshiArr			= $upload_exl_service->upcel_check('shipping','s_name');

		/*检测币别用*/
		$backconArr			= $upload_exl_service->upcel_check('exchange_rate','code',' and isnew="1"');


		/*数据循环检测*/
		foreach($all_arr as $k=>$val){
			$exl_row++;
			$tablelist .= '<tr>';
			foreach($val as $j=>$value){
				$error_style = '';

				/*检测销售账号*/
				if($j == 'sold_account' && !in_array($value, $backsoacArr)){
					$error_style = ' bgcolor="red" title="系统不存在的销售账号!"';
					$data_error++;
				}

				/*检测仓库*/
				if($j == 'warehouse'){
					if(!in_array($value,$backhousArr)){
						$error_style = ' bgcolor="red" title="系统不存在的仓库!"';
						$data_error++;
					}elseif(in_array($backhoidArr[$value],$fbden_arr)){
						$error_style = ' bgcolor="red" title="该仓库已被设置禁止导入直接出库的订单，可联系管理员开启!"';
						$data_error++;
					}
				}

				/*检测发货方式*/
				if($j == 'shipping' && !in_array($value,$backshiArr)){
					$error_style = ' bgcolor="red" title="系统不存在的发货方式!"';
					$data_error++;
				}

				/*平台单号、itemid、quantity-shipped、*/
				if(($j == 'amazon-order-id' || $j == 'shipment-item-id' || $j == 'quantity-shipped') && empty($value)){
					$error_style = ' bgcolor="red" title="不能为空!"';
					$data_error++;
				}

				/*检测SKU别名是否录入*/
				if($j == 'sku'){
					if(empty($value)){
						$error_style = ' bgcolor="red" title="不能为空!"';$data_error++;
					}else{
						$backskupid  = $obj_skualia->D->get_one(array('sku_code'=>$value),'pid,pro_sku');
						if(!$backskupid){
							$error_style= ' bgcolor="red" title="SKU未录入别名!"';$data_error++;
						}else{$skupid  .= '<input type=hidden name=pid[] value="'.$backskupid['pid'].'" >';}
					}
				}

				/*币别*/
				if($j == 'currency' && !in_array($value, $backconArr)){
					$error_style = ' bgcolor="red" title="系统不存在的币别代码!"';
					$data_error++;
				}
                /*出库日期*/
                if ($j == 'reporting-date'){
                    if (empty($value)){$error_style = ' bgcolor="red" title="不能为空!"';$data_error++;}
                }
                
                if ($j == 'shipment-date'){
                    if (empty($value)){$error_style = ' bgcolor="red" title="不能为空!"';$data_error++;}
                }
                
				$tablelist.='<td '.$error_style.'>'.$value.'</td>';
			}
			$tablelist .= '</tr>';
		}
		$tablelist .= '</table>';

		/*错误判断*/
		if(!$data_error && isset($all_arr)){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />'.$skupid;
			$tablelist .= '<input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=process_order&detail=outamazon">';
		}elseif($data_error){
			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}elseif(empty($all_arr)){
			$tablelist.= '<div style="color: #FF2A00;font-size:12px;background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;width:500px; line-height:22px;padding-left:25px;margin-top:10px; margin-bottom:10px">';
			$tablelist.= '温馨提醒：根据保存需要，系统只提取上传表格的部分数据，并非表格所有列的数据都提取，所以上传后看到预览数据的列数将比原表格少。</div>';
		}
	}
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'<a href="index.php?action=process_shipment&detail=list">出货列表</a> &raquo; <a href="index.php?action=process_shipment&detail=import_exl">选择导入类型</a> &raquo; 亚马逊出库单','exl_error_msg'=>$exl_error_msg,'exl_error_width'=>600,'tablelist'=>$tablelist));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();

}

?>
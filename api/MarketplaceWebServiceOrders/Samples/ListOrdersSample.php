<?php
/**
 *  PHP Version 5
 *
 *  @version     2011-01-01
 */
	/*调用数据层类与配置*/
	define('IS_INITPHP','isset');
	require ('../../../../initphp/core/dao/db.init.php');
	require ('../../../../initphp/initphp.php');
	require ('../../../conf.inc.php');
	include_once ('.config.inc.php');

	$process 		= new dbInit('process');
	$locktab 		= new dbInit('locktab');
	$exchange_rate 	= new dbInit('exchange_rate');
	$sku_alias		= new dbInit('sku_alias');
	$user			= new dbInit('info_amazon');
	$product		= new dbInit('product');
	$esse			= new dbInit('esse');
	$sku_alias_api	= new dbInit('sku_alias_api');
	$sys_setting	= new dbInit('sys_setting');
	$product_cost   = new dbInit('product_cost');

	$configTimeApi	= 33;		//如果取不到上次抓取时间，则取33分钟前(一般用于第一次)。
	$laterTime		= 3;		//延迟时间3分钟

	/*因服务器时间不准确，取网上的时间*/
	$standTimeUrl = 'http://ntp.news.sohu.com/mtime.php';

	$strTime = file_get_contents($standTimeUrl);

	$strTime = substr($strTime,5,strlen($strTime));

	$timearr = explode(',',$strTime);

	$nowTime = strtotime($timearr['0'].'-'.$timearr['1'].'-'.$timearr['2'].' '.$timearr['3'].':'.$timearr['4'].':'.$timearr['5']);

	/*取不到时间生成错误日志且不执行读取订单*/
	if(!$nowTime){
		set_failed_log('../log/log.txt','can not get the time',time());
		exit();
	}

	$user->init($InitPHP_G['db'],$InitPHP_G['db_type']);//调用框架方法链接数据库

	/*只有填写了关联销售渠道与仓库的才读取订单信息*/
	$info_sql = 'select * from info_amazon where ia_houseid!="" and ia_sold_way!="" ';
	$backdata = $user->query_array($info_sql);

	/*取不到别名的SKU*/
	$sku_empty_code = '';
	$error_skuname	= '../log/error_skuname_'.date('Y_m_d',$nowTime).'.txt';

	if(is_readable($error_skuname) == false){
		$fp = fopen($error_skuname,'w+');//如果不存在该文件，则创建
		fclose($fp);
	}


/*循环每个帐户读取订单信息--Start*/
for($mi = 0; $mi < count($backdata); $mi++){


		/*记录时间的文件*/
		$fileTime 		= '../log/'.$backdata[$mi]['id'].'_time.txt';
		$error_filename = '../log/'.$backdata[$mi]['id'].'_log.txt';

		if(is_readable($fileTime) == false){
			$fp = fopen($fileTime,'w+');//如果不存在该文件，则创建
			fclose($fp);
		}

		if(is_readable($error_filename) == false){
			$fp = fopen($error_filename,'w+');//如果不存在该文件，则创建
			fclose($fp);
		}


		/*接口配置参数*/
		$MERCHANT_ID					=	$backdata[$mi]['ia_merchant_id'];
		$MARKETPLACE_ID					=   $backdata[$mi]['ia_marketplace_id'];
		$AWS_ACCESS_KEY_ID				= 	$backdata[$mi]['ia_aws_access_key_id'];
		$AWS_SECRET_ACCESS_KEY			=	$backdata[$mi]['ia_aws_secret_access_key'];



		/*端点*/
		$serviceUrl = $backdata[$mi]['ia_port']."/Orders/2011-01-01";

		$config = array ('ServiceURL' => $serviceUrl, 'ProxyHost' => null,'ProxyPort' => -1,'MaxErrorRetry' => 3,);

	 	$service = new MarketplaceWebServiceOrders_Client(
	        $AWS_ACCESS_KEY_ID,
	        $AWS_SECRET_ACCESS_KEY,
	        APPLICATION_NAME,
	        APPLICATION_VERSION,
	        $config);


		$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
		$request->setSellerId($MERCHANT_ID);

		//文件保存北京时间，读取出来转换成世界标准时间
		$timearr = file($fileTime);

		$setCAtime_d= $timearr['0'];
		$setCAtime_d= empty($setCAtime_d)?date('Y-m-d H:i:s',($nowTime-$configTimeApi*60)):$setCAtime_d;

		$setCBtime_d= date('Y-m-d H:i:s',$nowTime-$laterTime*60);

		$setCAtime	= date('Y-m-d H:i:s',strtotime($setCAtime_d)-8*3600); //转换成世界标准时间
		$setCBtime	= date('Y-m-d H:i:s',strtotime($setCBtime_d)-8*3600); //转换成世界标准时间



		/*按修改时间取订单--30分钟执行一次*/
		if($_GET['apitype'] == 'update'){

		 	$request->setLastUpdatedAfter(new DateTime($setCAtime, new DateTimeZone('UTC')));
		 	$request->setLastUpdatedBefore(new DateTime($setCBtime, new DateTimeZone('UTC')));

			$timearr['0']	= $setCBtime_d;

		}else{exit();}

		$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
		$marketplaceIdList->setId(array($MARKETPLACE_ID));
		$request->setMarketplaceId($marketplaceIdList);



		 /*for item*/
		 $request_item = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
		 $request_item->setSellerId($MERCHANT_ID);
		 /*for item end*/

		 $datalist 	=	array();
		 $count_num = 	0;

		 /*读取接口订单内容*/
		 $datalist	=	invokeListOrders($service, $request,$request_item,&$count_num);
		 echo '<pre>'.print_r($datalist,1).'</pre>';

		 $count_list= count($datalist);
		 $error_num = 0;
		 $order_num = 1;


		 /*update的时候需要插入订单，所以需要查看锁表标记*/
		 if($_GET['apitype'] == 'update'){

//			 $back_checklock = $locktab->get_one_by_field(array('type'=>0),'onoff');

			 /*如果当前有人导表，则退出，放弃此次读取订单*/
//			 if($back_checklock['onoff'] == '1'){ set_failed_log($error_filename,'locked',$nowTime); exit(); }else{ $locktab->update_by_field(array('type'=>0),array('onoff'=>1)); }//标记锁表
		 }


		 /*插入事务表*/
//		 $process->query('begin');

		 /*取得最大单号*/
//		 $maxx =  $process->get_one_by_field(array('property'=>'出仓单'),'max( SUBSTRING( order_id, 2, 7 ) ) as order_id');

		 for($i = 0 ; $i < $count_list ; $i++){

			$back_checkfid = array();

			/*插入FBA发货订单shipped状态*/
			if(strtolower($datalist[$i]['orderstatus']) == 'shipped'){

				/*将带有listing的SKU转换为系统的SKU*/
				$back_skualias = array();
				$back_skualias = $sku_alias->get_one_by_field(array('sku_code'=>$datalist[$i]['sku']),'pro_sku');

				/*只对在ERP上能取到SKU的记录进行操作*/
				if($back_skualias['pro_sku']){

//					/*检测是否已录过中国发货*/
//					$back_checkfid = $process->get_one_by_field(array('fid'=>$datalist[$i]['fid'],'sku'=>$back_skualias['pro_sku'],'provider_id'=>10),'id');
//
//
//					/*如果已经录进过ERP，代表是中国发货的，则跳过；只抓取没有录进ERP系统的*/
//					if(!$back_checkfid['id']){
//
//						/*如果是同一个订单多个SKU的，注意有些订单一个SKU在中国发，一个SKU在amazon发*/
//						if($datalist[$i]['fid'] == $datalist[$i-1]['fid']) {
//							$datalist[$i]['order_id'] = $pre_order_id;
//						}else{
//							$datalist[$i]['order_id'] = 'x'.sprintf("%07d",$maxx['order_id']+$order_num);
//							$order_num++;
//						}
//
//						$pre_order_id = $datalist[$i]['order_id'];//重置上一个单的订单号，为下次循环准备
//
//
//						/*世界标准时间转换成北京时间*/
//						$datalist[$i]['cdate'] = date('Y-m-d H:i:s',strtotime($datalist[$i]['cdate']));
//						$datalist[$i]['mdate'] = date('Y-m-d H:i:s',strtotime($datalist[$i]['mdate']));
//
//
//						/*获取PID与产品名称*/
//						$backdata_pid_name = $product->get_one_by_field(array('sku'=>$back_skualias['pro_sku']),'pid,product_name');
//
//
//						/*获取产品成本*/
//						$backpcost = $product_cost->get_one_by_field(array('pid'=>$backdata_pid_name['pid']),'cost1,coin_code');
//						if($backpcost['cost1']){
//							$bafault_coin 	= $sys_setting->get_one_by_field(array('remer'=>'system_defaultcoin','bid'=>'sys'),'value');
//
//							if($backpcost['coin_code'] != $bafault_coin['value']){
//								$backpcost['cost1'] = change_rate($backpcost['coin_code'],$bafault_coin['value'],$backpcost['cost1']);
//							}
//						}else{
//							$backpcost['cost1'] = 0;
//						}
//						$price2 = $backpcost['cost1'];
//
//
//
//						/*扩展内容*/
//						$datalist[$i]['extends'] = array('e_listing'=>$datalist[$i]['listing'],'e_address1'=>$datalist[$i]['address1'],'e_address2'=>$datalist[$i]['address2'],'e_city'=>$datalist[$i]['city'],'e_state'=>$datalist[$i]['state'],'e_post_code'=>$datalist[$i]['post_code'],'e_country'=>$datalist[$i]['country'],'e_tel'=>$datalist[$i]['tel'],'e_email'=>$datalist[$i]['email'],'e_receperson'=>$datalist[$i]['receperson']);
//
//						/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
//						$datalist[$i]['extends'] = get_magic_quotes_gpc()?addslashes(json_encode($datalist[$i]['extends'])):json_encode($datalist[$i]['extends']);
//
//
//						/*2012-08-14后整改，无需统一转换成美元，只需要获得期号*/
//						$backrate = $exchange_rate->get_one_by_field(array('isnew'=>1,'code'=>$datalist[$i]['currency']),'stage_rate');
//
//
//						/*插入ERP*/
//						$insert_arr = array(
//							'provider_id'	=>$backdata[$mi]['ia_houseid'],
//							'order_id'		=>$datalist[$i]['order_id'],
//							'pid'			=>$backdata_pid_name['pid'],
//							'product_name'	=>$backdata_pid_name['product_name'],
//							'sku'			=>$back_skualias['pro_sku'],
//							'sold_way'		=>$backdata[$mi]['ia_sold_way'],
//							'fid'			=>$datalist[$i]['fid'],
//							'cdate'			=>$datalist[$i]['cdate'],
//							'mdate'			=>$datalist[$i]['mdate'],
//							'rdate'			=>$datalist[$i]['mdate'],
//							'cuser'			=>'SystemApi',
//							'muser'			=>'SystemApi',
//							'ruser'			=>'SystemApi',
//							'property'		=>'出仓单',
//							'protype'		=>'售出',
//							'active'		=>1,
//							'output'		=>1,
//							'statu'			=>3,
//							'coin_code'		=>$datalist[$i]['currency'],
//							//'s_coin_code'	=>$datalist[$i]['currency'],
//							//'coin_rate'	=>$backrate['rate'],
//							'stage_rate'	=>$backrate['stage_rate'],
//							'quantity'		=>$datalist[$i]['quantity'],
//							'price'			=>$datalist[$i]['item_price'],
//							'price2'		=>$price2,
//							//'buyer_id'	=>$datalist[$i]['receperson'],
//							'extends'		=>$datalist[$i]['extends'],
//						);
//						$sid = $process->insert($insert_arr);
//						if(!$sid) $error_num++;
//					}
				}else{/*取不到SKU的生成报告，转发给销售*/
					$sku_empty_code.= $datalist[$i]['sku'].'('.$backdata[$mi]['ia_seller_id'].'--'.date('Y-m-d H:i:s',$nowTime).')'."\r\n";
					$sku_alias_api->insert(array('sku_code'=>$datalist[$i]['sku'],'cdate'=>date('Y-m-d H:i:s',$nowTime)));//插入表记录
				}

			 }
	 }/*API读取订单信息--End*/

	/*标记解锁表，执行两次，防止一次失败*/
//	$ccid = $locktab->update_by_field(array('type'=>0),array('onoff'=>'0'));
//	if(!$ccid) $locktab->update_by_field(array('type'=>0),array('onoff'=>'0'));


	if($error_num){

		/*事务回滚*/
//		$process->query('rollback');

		/*写入错误日志*/
		set_failed_log($error_filename,$error_num,$nowTime);

	}else{

		/*提交事务*/
//		$process->query('commit');

		if(!empty($datalist)){
			/*更新读取时间*/
			$file_handle = fopen($fileTime,'w+');
			fwrite($file_handle,$timearr['0']);
			fclose($file_handle);
		}
	}

}
/*循环每个帐户读取订单信息--End*/


/*如果存在无法获取SKU别名的产品，保存在文档*/
if($sku_empty_code){

	$file_handle = fopen($error_skuname,'r');
	$error_data  = fread($file_handle,filesize($error_skuname));
	$error_data	.= $sku_empty_code."\r\n\r\n";
	fclose($file_handle);

	$file_handle = fopen($error_skuname,'w+');
	fwrite($file_handle,$error_data);
	fclose($file_handle);
}





 /*失败生成日志*/
 function set_failed_log($error_filename,$failmsg,$tim){

 		$file_handle = fopen($error_filename,'r');
		$error_data  = fread($file_handle,filesize($error_filename));
		$error_data	.= 'failed--'.$failmsg.'--'.date('Y-m-d H:i:s',$tim)."\r\n";
		fclose($file_handle);

	 	$file_handle = fopen($error_filename,'w+');
	 	fwrite($file_handle,$error_data);
	 	fclose($file_handle);
 }


 /*币别转换(暂只用于成本保存时)*/
  function change_rate($source,$tobe,$val){

		$obj = new dbInit('exchange_rate');

		if($source == $tobe && $tobe == 'USD'){return $val;}//美元兑换美元，直接返回。

		elseif($source == 'USD' && $tobe != 'USD'){//美元兑换其他。
			$rate = $obj->get_one_by_field(array('code'=>$tobe,'isnew'=>1),'rate');
			return $val*$rate['rate']/100;
		}
		elseif($source != 'USD' && $tobe == 'USD'){//其它兑换美元。
			$rate = $obj->get_one_by_field(array('code'=>$source,'isnew'=>1),'rate');
			return $val/$rate['rate']*100;
		}
		elseif($source != 'USD' && $tobe != 'USD'){//其它兑换其他
			$rate = $obj->get_one_by_field(array('code'=>$source,'isnew'=>1),'rate');
			$val = $val/$rate['rate']*100;//先兑换成美元
			$rate = $obj->get_one_by_field(array('code'=>$tobe,'isnew'=>1),'rate');
			return  $val*$rate['rate']/100;//美元兑换成其他
		}
  }


 /*读取订单信息的方法*/
 function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request, $request_item, $count_num)
 {
 	global $datalist;
      try {
              $response = $service->listOrders($request);

                if ($response->isSetListOrdersResult()) {

                    $listOrdersResult = $response->getListOrdersResult();

                    if ($listOrdersResult->isSetCreatedBefore())
                    {
                        echo("                CreatedBefore<br>");
                        echo("                    " . $listOrdersResult->getCreatedBefore() . "<br>");
                    }
                    if ($listOrdersResult->isSetLastUpdatedBefore())
                    {
                        echo("                LastUpdatedBefore<br>");
                        echo("                    " . $listOrdersResult->getLastUpdatedBefore() . "<br>");
                    }
                    if ($listOrdersResult->isSetOrders()) {
                        //echo("                Orders<br>");
                        $orders = $listOrdersResult->getOrders();
                        $orderList = $orders->getOrder();
                        foreach ($orderList as $order) {

                           // echo("                    Order<br>");
                           // if ($order->isSetAmazonOrderId())
                           // {
                           //     echo("                        AmazonOrderId<br>");
                           //     echo("                            " . $order->getAmazonOrderId() . "<br>");
                           // }


							/*拍下时间*/
                            if ($order->isSetPurchaseDate())
                            {
                                //echo("                        PurchaseDate<br>");
                                //echo("                            " . $order->getPurchaseDate() . "<br>");
                                $datalist[$count_num]['cdate'] = $order->getPurchaseDate();

                            }

                            /*更新时间*/
                            if ($order->isSetLastUpdateDate())
                            {
                                //echo("                        LastUpdateDate<br>");
                                //echo("                            " . $order->getLastUpdateDate() . "<br>");
                                $datalist[$count_num]['mdate'] = $order->getLastUpdateDate();

                            }

                            /*pending,unshipped,shipped,canceled*/
                            if ($order->isSetOrderStatus())
                            {
                                //echo("                        OrderStatus<br>");
                                //echo("                            " . $order->getOrderStatus() . "<br>");
                                $datalist[$count_num]['orderstatus'] = $order->getOrderStatus();

                            }

                            /*MFN,AFN*/
                            if ($order->isSetFulfillmentChannel())
                            {
                                //echo("                        FulfillmentChannel<br>");
                                //echo("                            " . $order->getFulfillmentChannel() . "<br>");
                                $datalist[$count_num]['fillmentchannel'] = $order->getFulfillmentChannel();
                            }

                            /*Amazon.com*/
                            if ($order->isSetSalesChannel())
                            {
                                //echo("                        SalesChannel<br>");
                                //echo("                            " . $order->getSalesChannel() . "<br>");
                                $datalist[$count_num]['SalesChannel'] = $order->getSalesChannel();

                            }

							/*发货相关*/
                            if ($order->isSetShippingAddress()) {
                                //echo("                        ShippingAddress<br>");
                                $shippingAddress = $order->getShippingAddress();
                                if ($shippingAddress->isSetName())
                                {
                                   // echo("                            Name<br>");
                                   // echo("                                " . $shippingAddress->getName() . "<br>");
                                    $datalist[$count_num]['receperson'] = $shippingAddress->getName();
                                }
                                if ($shippingAddress->isSetAddressLine1())
                                {
                                    //echo("                            AddressLine1<br>");
                                    //echo("                                " . $shippingAddress->getAddressLine1() . "<br>");
                                    $datalist[$count_num]['address1'] = $shippingAddress->getAddressLine1();
                                }
                                if ($shippingAddress->isSetAddressLine2())
                                {
                                    //echo("                            AddressLine2<br>");
                                    //echo("                                " . $shippingAddress->getAddressLine2() . "<br>");
                                    $datalist[$count_num]['address2'] = $shippingAddress->getAddressLine2();
                                }

                                if ($shippingAddress->isSetCity())
                                {
                                    //echo("                            City<br>");
                                    //echo("                                " . $shippingAddress->getCity() . "<br>");
                                    $datalist[$count_num]['city'] = $shippingAddress->getCity();
                                }


                                if ($shippingAddress->isSetStateOrRegion())
                                {
                                    //echo("                            StateOrRegion<br>");
                                    //echo("                                " . $shippingAddress->getStateOrRegion() . "<br>");
                                    $datalist[$count_num]['state'] = $shippingAddress->getStateOrRegion();
                                }

                                if ($shippingAddress->isSetPostalCode())
                                {
                                    //echo("                            PostalCode<br>");
                                    //echo("                                " . $shippingAddress->getPostalCode() . "<br>");
                                    $datalist[$count_num]['post_code'] = $shippingAddress->getPostalCode();
                                }

                                if ($shippingAddress->isSetCountryCode())
                                {
                                    //echo("                            CountryCode<br>");
                                    //echo("                                " . $shippingAddress->getCountryCode() . "<br>");
                                    $datalist[$count_num]['country'] = $shippingAddress->getCountryCode();

                                }
                                if ($shippingAddress->isSetPhone())
                                {
                                    //echo("                            Phone<br>");
                                    //echo("                                " . $shippingAddress->getPhone() . "<br>");
                                    $datalist[$count_num]['tel'] = $shippingAddress->getPhone();
                                }
                            }


							/*买家邮箱*/
                            if ($order->isSetBuyerEmail())
                            {
                                //echo("                        BuyerEmail<br>");
                                //echo("                            " . $order->getBuyerEmail() . "<br>");
                                $datalist[$count_num]['email'] = $order->getBuyerEmail();
                            }

                            if ($order->isSetBuyerName())
                            {
                                //echo("                        BuyerName<br>");
                                //echo("                            " . $order->getBuyerName() . "<br>");
                                $datalist[$count_num]['buyer_id'] = $order->getBuyerName();
                            }


						    /*for item-start*/

					         $request_item->setAmazonOrderId($order->getAmazonOrderId());
							 $orderItemList =$service->listOrderItems($request_item)->getListOrderItemsResult()->getOrderItems()->getOrderItem();

							 foreach($orderItemList as $orderItem){
								 //echo 'SKU： &nbsp; '.$orderItem->getSellerSKU().'<br>';
								  $datalist[$count_num]['sku'] = $orderItem->getSellerSKU();
								  $datalist[$count_num]['listing'] = $orderItem->getASIN();
								  $datalist[$count_num]['fid'] = $order->getAmazonOrderId();

								  if ($orderItem->isSetItemPrice()){
								  	$itemPrice = $orderItem->getItemPrice();
								  	if ($itemPrice->isSetCurrencyCode()){
								  		$datalist[$count_num]['currency'] = $itemPrice->getCurrencyCode();//币别
								  	}

								  	if ($itemPrice->isSetAmount()){
								  		$datalist[$count_num]['item_price'] = $itemPrice->getAmount();//售额
								  	}

									if ($orderItem->isSetQuantityShipped())
		                            {
										$datalist[$count_num]['quantity'] = $orderItem->getQuantityShipped();//取已发的数量。
		                            }


								  }
							  	$count_num++;
							 }

							/*for item-end*/

                        }
                    }

                    return $datalist;
                }
                if ($response->isSetResponseMetadata()) {
                    echo("            ResponseMetadata<br>");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId())
                    {
                        echo("                RequestId<br>");
                        echo("                    " . $responseMetadata->getRequestId() . "<br>");
                    }
                }

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "<br>");
         echo("Response Status Code: " . $ex->getStatusCode() . "<br>");
         echo("Error Code: " . $ex->getErrorCode() . "<br>");
         echo("Error Type: " . $ex->getErrorType() . "<br>");
         echo("Request ID: " . $ex->getRequestId() . "<br>");
         echo("XML: " . $ex->getXML() . "<br>");
     }
 }


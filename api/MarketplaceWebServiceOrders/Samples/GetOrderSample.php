<?php
/**
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebServiceOrders
 *  @copyright   Copyright 2008-2009 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2011-01-01
 */
/*******************************************************************************
 *  Marketplace Web Service Orders PHP5 Library
 *  Generated: Fri Jan 21 18:53:17 UTC 2011
 *
 */

/**
 * Get Order  Sample
 */

include_once ('.config.inc.php');

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// United States:
$serviceUrl = "https://mws.amazonservices.com/Orders/2012-05-01";
// United Kingdom
//$serviceUrl = "https://mws.amazonservices.co.uk/Orders/2011-01-01";
// Germany
//$serviceUrl = "https://mws.amazonservices.de/Orders/2011-01-01";
// France
//$serviceUrl = "https://mws.amazonservices.fr/Orders/2011-01-01";
// Italy
//$serviceUrl = "https://mws.amazonservices.it/Orders/2011-01-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Orders/2011-01-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2011-01-01";
// Canada
//$serviceUrl = "https://mws.amazonservices.ca/Orders/2011-01-01";

 $config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'MaxErrorRetry' => 3,
 );

 $service = new MarketplaceWebServiceOrders_Client(
        'AKIAISEVUTCXNC76CLXA',
        'q0NnoLnN6WmiqncmX5rpf5vv39YzEZ0ZBGB1nB9x',
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);

/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebServiceOrders
 * responses without calling MarketplaceWebServiceOrders service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebServiceOrders/Mock tree
 *
 ***********************************************************************/
 $service = new MarketplaceWebServiceOrders_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for Get Order Action
 ***********************************************************************/
 $request = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
 $request->setSellerId('A7XHMWMCZ9EID');
 // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_GetOrderRequest
 // object or array of parameters

 // Set the list of AmazonOrderIds
 $orderIds = new MarketplaceWebServiceOrders_Model_OrderIdList();
 $orderIds->setId(array('102-2843002-9986658'));
 $request->setAmazonOrderId($orderIds);

 /*item order*/

 $request_item = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
 $request_item->setSellerId('A7XHMWMCZ9EID');
 //$request_item->setAmazonOrderId("102-2240540-7214639");

 /*end*/



 invokeGetOrder($service, $request, $request_item);


/**
  * Get Order Action Sample
  * This operation takes up to 50 order ids and returns the corresponding orders.
  *
  * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
  * @param mixed $request MarketplaceWebServiceOrders_Model_GetOrder or array of parameters
  */
  function invokeGetOrder(MarketplaceWebServiceOrders_Interface $service, $request, $request_item)
  {
      try {
              $response = $service->getOrder($request);

              /*item*/

              	$request_item->setAmazonOrderId("102-2843002-9986658");


//			  	$response_item = $service->listOrderItems($request_item);
//              $listOrderItemsResult = $response_item->getListOrderItemsResult();
//              $orderItems = $listOrderItemsResult->getOrderItems();
//              $orderItemList = $orderItems->getOrderItem();
//
//				echo '<pre>'.print_r($orderItemList,1).'</pre>';
//
//				echo $orderItemList['0']->getSellerSKU();
//				exit();

				//$request_item->setAmazonOrderId("002-6565335-7985002");
				$orderItemList =$service->listOrderItems($request_item)->getListOrderItemsResult()->getOrderItems()->getOrderItem();

				echo count($orderItemList).'<br>';
				foreach($orderItemList as $val){

					echo 'sku：'.$val->getSellerSKU();
					 $itemPrice = $val->getItemPrice();
					echo 'price：'.$itemPrice->getAmount();
				}
			//	exit();


//              foreach ($orderItemList as $orderItem) {
//              	   if ($orderItem->isSetSellerSKU())
//                            {
//                                echo(" SellerSKU��<br>");
//                                echo(" " . $orderItem->getSellerSKU() . "��<br>");
//                            }
//              }
              /*item*/

                echo ("Service Response��<br>");
                echo ("=============================================================================��<br>");

                echo("        GetOrderResponse��<br>");
                if ($response->isSetGetOrderResult()) {
                    echo("            GetOrderResult��<br>");
                    $getOrderResult = $response->getGetOrderResult();
                    if ($getOrderResult->isSetOrders()) {
                        echo("                Orders��<br>");
                        $orders = $getOrderResult->getOrders();
                        $orderList = $orders->getOrder();
                        foreach ($orderList as $order) {
                            echo("                    Order��<br>");
                            if ($order->isSetAmazonOrderId())
                            {
                                echo("                        AmazonOrderId��<br>");
                                echo("                            " . $order->getAmazonOrderId() . "��<br>");
                            }
                            if ($order->isSetSellerOrderId())
                            {
                                echo("                        SellerOrderId��<br>");
                                echo("                            " . $order->getSellerOrderId() . "��<br>");
                            }

                            //echo "SKU��<br>";
                           	//echo $order->getSellerSKU().'��<br>';


                            if ($order->isSetPurchaseDate())
                            {
                                echo("                        PurchaseDate��<br>");
                                echo("                            " . $order->getPurchaseDate() . "��<br>");
                            }
                            if ($order->isSetLastUpdateDate())
                            {
                                echo("                        LastUpdateDate��<br>");
                                echo("                            " . $order->getLastUpdateDate() . "��<br>");
                            }
                            if ($order->isSetOrderStatus())
                            {
                                echo("                        OrderStatus��<br>");
                                echo("                            " . $order->getOrderStatus() . "��<br>");
                            }
                            if ($order->isSetFulfillmentChannel())
                            {
                                echo("                        FulfillmentChannel��<br>");
                                echo("                            " . $order->getFulfillmentChannel() . "��<br>");
                            }
                            if ($order->isSetSalesChannel())
                            {
                                echo("                        SalesChannel��<br>");
                                echo("                            " . $order->getSalesChannel() . "��<br>");
                            }
                            if ($order->isSetOrderChannel())
                            {
                                echo("                        OrderChannel��<br>");
                                echo("                            " . $order->getOrderChannel() . "��<br>");
                            }
                            if ($order->isSetShipServiceLevel())
                            {
                                echo("                        ShipServiceLevel��<br>");
                                echo("                            " . $order->getShipServiceLevel() . "��<br>");
                            }
                            if ($order->isSetShippingAddress()) {
                                echo("                        ShippingAddress��<br>");
                                $shippingAddress = $order->getShippingAddress();
                                if ($shippingAddress->isSetName())
                                {
                                    echo("                            Name��<br>");
                                    echo("                                " . $shippingAddress->getName() . "��<br>");
                                }
                                if ($shippingAddress->isSetAddressLine1())
                                {
                                    echo("                            AddressLine1��<br>");
                                    echo("                                " . $shippingAddress->getAddressLine1() . "��<br>");
                                }
                                if ($shippingAddress->isSetAddressLine2())
                                {
                                    echo("                            AddressLine2��<br>");
                                    echo("                                " . $shippingAddress->getAddressLine2() . "��<br>");
                                }
                                if ($shippingAddress->isSetAddressLine3())
                                {
                                    echo("                            AddressLine3��<br>");
                                    echo("                                " . $shippingAddress->getAddressLine3() . "��<br>");
                                }
                                if ($shippingAddress->isSetCity())
                                {
                                    echo("                            City��<br>");
                                    echo("                                " . $shippingAddress->getCity() . "��<br>");
                                }
                                if ($shippingAddress->isSetCounty())
                                {
                                    echo("                            County��<br>");
                                    echo("                                " . $shippingAddress->getCounty() . "��<br>");
                                }
                                if ($shippingAddress->isSetDistrict())
                                {
                                    echo("                            District��<br>");
                                    echo("                                " . $shippingAddress->getDistrict() . "��<br>");
                                }
                                if ($shippingAddress->isSetStateOrRegion())
                                {
                                    echo("                            StateOrRegion��<br>");
                                    echo("                                " . $shippingAddress->getStateOrRegion() . "��<br>");
                                }
                                if ($shippingAddress->isSetPostalCode())
                                {
                                    echo("                            PostalCode��<br>");
                                    echo("                                " . $shippingAddress->getPostalCode() . "��<br>");
                                }
                                if ($shippingAddress->isSetCountryCode())
                                {
                                    echo("                            CountryCode��<br>");
                                    echo("                                " . $shippingAddress->getCountryCode() . "��<br>");
                                }
                                if ($shippingAddress->isSetPhone())
                                {
                                    echo("                            Phone��<br>");
                                    echo("                                " . $shippingAddress->getPhone() . "��<br>");
                                }
                            }
                            if ($order->isSetOrderTotal()) {
                                echo("                        OrderTotal��<br>");
                                $orderTotal = $order->getOrderTotal();
                                if ($orderTotal->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $orderTotal->getCurrencyCode() . "��<br>");
                                }
                                if ($orderTotal->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $orderTotal->getAmount() . "��<br>");
                                }
                            }
                            if ($order->isSetNumberOfItemsShipped())
                            {
                                echo("                        NumberOfItemsShipped��<br>");
                                echo("                            " . $order->getNumberOfItemsShipped() . "��<br>");
                            }
                            if ($order->isSetNumberOfItemsUnshipped())
                            {
                                echo("                        NumberOfItemsUnshipped��<br>");
                                echo("                            " . $order->getNumberOfItemsUnshipped() . "��<br>");
                            }
                            if ($order->isSetPaymentExecutionDetail()) {
                                echo("                        PaymentExecutionDetail��<br>");
                                $paymentExecutionDetail = $order->getPaymentExecutionDetail();
                                $paymentExecutionDetailItemList = $paymentExecutionDetail->getPaymentExecutionDetailItem();
                                foreach ($paymentExecutionDetailItemList as $paymentExecutionDetailItem) {
                                    echo("                            PaymentExecutionDetailItem��<br>");
                                    if ($paymentExecutionDetailItem->isSetPayment()) {
                                        echo("                                Payment��<br>");
                                        $payment = $paymentExecutionDetailItem->getPayment();
                                        if ($payment->isSetCurrencyCode())
                                        {
                                            echo("                                    CurrencyCode��<br>");
                                            echo("                                        " . $payment->getCurrencyCode() . "��<br>");
                                        }
                                        if ($payment->isSetAmount())
                                        {
                                            echo("                                    Amount��<br>");
                                            echo("                                        " . $payment->getAmount() . "��<br>");
                                        }
                                    }
                                    if ($paymentExecutionDetailItem->isSetSubPaymentMethod())
                                    {
                                        echo("                                SubPaymentMethod��<br>");
                                        echo("                                    " . $paymentExecutionDetailItem->getSubPaymentMethod() . "��<br>");
                                    }
                                }
                            }
                            if ($order->isSetPaymentMethod())
                            {
                                echo("                        PaymentMethod��<br>");
                                echo("                            " . $order->getPaymentMethod() . "��<br>");
                            }
                            if ($order->isSetMarketplaceId())
                            {
                                echo("                        MarketplaceId��<br>");
                                echo("                            " . $order->getMarketplaceId() . "��<br>");
                            }
                            if ($order->isSetBuyerEmail())
                            {
                                echo("                        BuyerEmail��<br>");
                                echo("                            " . $order->getBuyerEmail() . "��<br>");
                            }
                            if ($order->isSetBuyerName())
                            {
                                echo("                        BuyerName��<br>");
                                echo("                            " . $order->getBuyerName() . "��<br>");
                            }
                            if ($order->isSetShipmentServiceLevelCategory())
                            {
                                echo("                        ShipmentServiceLevelCategory��<br>");
                                echo("                            " . $order->getShipmentServiceLevelCategory() . "��<br>");
                            }
                        }
                    }
                }
                if ($response->isSetResponseMetadata()) {
                    echo("            ResponseMetadata��<br>");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId())
                    {
                        echo("                RequestId��<br>");
                        echo("                    " . $responseMetadata->getRequestId() . "��<br>");
                    }
                }

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "��<br>");
         echo("Response Status Code: " . $ex->getStatusCode() . "��<br>");
         echo("Error Code: " . $ex->getErrorCode() . "��<br>");
         echo("Error Type: " . $ex->getErrorType() . "��<br>");
         echo("Request ID: " . $ex->getRequestId() . "��<br>");
         echo("XML: " . $ex->getXML() . "��<br>");
     }
 }


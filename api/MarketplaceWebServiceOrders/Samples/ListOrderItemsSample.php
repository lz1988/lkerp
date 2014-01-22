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
 * List Order Items  Sample
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
$serviceUrl = "https://mws.amazonservices.com/Orders/2011-01-01";
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
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
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
 // $service = new MarketplaceWebServiceOrders_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for List Order Items Action
 ***********************************************************************/
 $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
 $request->setSellerId(MERCHANT_ID);
 $request->setAmazonOrderId("102-2240540-7214639");

 // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_ListOrderItemsRequest
 // object or array of parameters

 invokeListOrderItems($service, $request);


/**
  * List Order Items Action Sample
  * This operation can be used to list the items of the order indicated by the
  * given order id (only a single Amazon order id is allowed).
  *
  * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
  * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrderItems or array of parameters
  */
  function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
              $response = $service->listOrderItems($request);

                echo ("Service Response��<br>");
                echo ("=============================================================================��<br>");

                echo("        ListOrderItemsResponse��<br>");
                if ($response->isSetListOrderItemsResult()) {
                    echo("            ListOrderItemsResult��<br>");
                    $listOrderItemsResult = $response->getListOrderItemsResult();
                    if ($listOrderItemsResult->isSetNextToken())
                    {
                        echo("                NextToken��<br>");
                        echo("                    " . $listOrderItemsResult->getNextToken() . "��<br>");
                    }
                    if ($listOrderItemsResult->isSetAmazonOrderId())
                    {
                        echo("                AmazonOrderId��<br>");
                        echo("                    " . $listOrderItemsResult->getAmazonOrderId() . "��<br>");
                    }
                    if ($listOrderItemsResult->isSetOrderItems()) {
                        echo("                OrderItems��<br>");
                        $orderItems = $listOrderItemsResult->getOrderItems();
                        $orderItemList = $orderItems->getOrderItem();
                        foreach ($orderItemList as $orderItem) {
                            echo("                    OrderItem��<br>");
                            if ($orderItem->isSetASIN())
                            {
                                echo("                        ASIN��<br>");
                                echo("                            " . $orderItem->getASIN() . "��<br>");
                            }
                            if ($orderItem->isSetSellerSKU())
                            {
                                echo("                        SellerSKU��<br>");
                                echo("                            " . $orderItem->getSellerSKU() . "��<br>");
                            }
                            if ($orderItem->isSetOrderItemId())
                            {
                                echo("                        OrderItemId��<br>");
                                echo("                            " . $orderItem->getOrderItemId() . "��<br>");
                            }
                            if ($orderItem->isSetTitle())
                            {
                                echo("                        Title��<br>");
                                echo("                            " . $orderItem->getTitle() . "��<br>");
                            }
                            if ($orderItem->isSetQuantityOrdered())
                            {
                                echo("                        QuantityOrdered��<br>");
                                echo("                            " . $orderItem->getQuantityOrdered() . "��<br>");
                            }
                            if ($orderItem->isSetQuantityShipped())
                            {
                                echo("                        QuantityShipped��<br>");
                                echo("                            " . $orderItem->getQuantityShipped() . "��<br>");
                            }
                            if ($orderItem->isSetItemPrice()) {
                                echo("                        ItemPrice��<br>");
                                $itemPrice = $orderItem->getItemPrice();
                                if ($itemPrice->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $itemPrice->getCurrencyCode() . "��<br>");
                                }
                                if ($itemPrice->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $itemPrice->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetShippingPrice()) {
                                echo("                        ShippingPrice��<br>");
                                $shippingPrice = $orderItem->getShippingPrice();
                                if ($shippingPrice->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $shippingPrice->getCurrencyCode() . "��<br>");
                                }
                                if ($shippingPrice->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $shippingPrice->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetGiftWrapPrice()) {
                                echo("                        GiftWrapPrice��<br>");
                                $giftWrapPrice = $orderItem->getGiftWrapPrice();
                                if ($giftWrapPrice->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $giftWrapPrice->getCurrencyCode() . "��<br>");
                                }
                                if ($giftWrapPrice->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $giftWrapPrice->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetItemTax()) {
                                echo("                        ItemTax��<br>");
                                $itemTax = $orderItem->getItemTax();
                                if ($itemTax->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $itemTax->getCurrencyCode() . "��<br>");
                                }
                                if ($itemTax->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $itemTax->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetShippingTax()) {
                                echo("                        ShippingTax��<br>");
                                $shippingTax = $orderItem->getShippingTax();
                                if ($shippingTax->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $shippingTax->getCurrencyCode() . "��<br>");
                                }
                                if ($shippingTax->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $shippingTax->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetGiftWrapTax()) {
                                echo("                        GiftWrapTax��<br>");
                                $giftWrapTax = $orderItem->getGiftWrapTax();
                                if ($giftWrapTax->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $giftWrapTax->getCurrencyCode() . "��<br>");
                                }
                                if ($giftWrapTax->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $giftWrapTax->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetShippingDiscount()) {
                                echo("                        ShippingDiscount��<br>");
                                $shippingDiscount = $orderItem->getShippingDiscount();
                                if ($shippingDiscount->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $shippingDiscount->getCurrencyCode() . "��<br>");
                                }
                                if ($shippingDiscount->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $shippingDiscount->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetPromotionDiscount()) {
                                echo("                        PromotionDiscount��<br>");
                                $promotionDiscount = $orderItem->getPromotionDiscount();
                                if ($promotionDiscount->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $promotionDiscount->getCurrencyCode() . "��<br>");
                                }
                                if ($promotionDiscount->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $promotionDiscount->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetPromotionIds()) {
                                echo("                        PromotionIds��<br>");
                                $promotionIds = $orderItem->getPromotionIds();
                                $promotionIdList  =  $promotionIds->getPromotionId();
                                foreach ($promotionIdList as $promotionId) {
                                    echo("                            PromotionId��<br>");
                                    echo("                                " . $promotionId);
                                }
                            }
                            if ($orderItem->isSetCODFee()) {
                                echo("                        CODFee��<br>");
                                $CODFee = $orderItem->getCODFee();
                                if ($CODFee->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $CODFee->getCurrencyCode() . "��<br>");
                                }
                                if ($CODFee->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $CODFee->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetCODFeeDiscount()) {
                                echo("                        CODFeeDiscount��<br>");
                                $CODFeeDiscount = $orderItem->getCODFeeDiscount();
                                if ($CODFeeDiscount->isSetCurrencyCode())
                                {
                                    echo("                            CurrencyCode��<br>");
                                    echo("                                " . $CODFeeDiscount->getCurrencyCode() . "��<br>");
                                }
                                if ($CODFeeDiscount->isSetAmount())
                                {
                                    echo("                            Amount��<br>");
                                    echo("                                " . $CODFeeDiscount->getAmount() . "��<br>");
                                }
                            }
                            if ($orderItem->isSetGiftMessageText())
                            {
                                echo("                        GiftMessageText��<br>");
                                echo("                            " . $orderItem->getGiftMessageText() . "��<br>");
                            }
                            if ($orderItem->isSetGiftWrapLevel())
                            {
                                echo("                        GiftWrapLevel��<br>");
                                echo("                            " . $orderItem->getGiftWrapLevel() . "��<br>");
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


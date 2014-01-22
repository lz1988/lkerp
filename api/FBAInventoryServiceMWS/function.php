<?php
	
function get_amazon_warehouse($amazon_info, $code_arr) {
	//amazon接口帐号参数
	$merchant_id = $amazon_info['ia_merchant_id'];
	$marketplace_id = $amazon_info['ia_marketplace_id'];
	$aws_access_key_id = $amazon_info['ia_aws_access_key_id'];
	$aws_secret_access_key = $amazon_info['ia_aws_secret_access_key'];
	$amazon_url = $amazon_info['ia_port'].'/FulfillmentInventory/2010-10-01/';
			
	set_include_path(get_include_path() . PATH_SEPARATOR . './api');
	
	$config = array (
		'ServiceURL' => $amazon_url,
 		'ProxyHost' => null,
 		'ProxyPort' => -1,
 		'MaxErrorRetry' => 3
	);
		
	$service = new FBAInventoryServiceMWS_Client(
		$aws_access_key_id, 
		$aws_secret_access_key, 
	    $config,
	    'hanson',
	    '2010-10-01');
	     	
   	$request = new FBAInventoryServiceMWS_Model_ListInventorySupplyRequest();
	$request->setSellerId($merchant_id);
	$request->setMarketplace($marketplace_id);
		
	$skulen = 0;
	$skulen = (int)($length / 50);
	$i = 0;
	$amazon_res_arr = array();
	while ($i <= $skulen) {
	
		$sku = new FBAInventoryServiceMWS_Model_SellerSkuList();
 		$sku->setmember(array_slice($code_arr, $i*50, 50));
 		$request->setSellerSkus($sku);		 		
 		try {
 			$response = $service->listInventorySupply($request);
 			if ($response->isSetListInventorySupplyResult()) { 
 				$listInventorySupplyResult = $response->getListInventorySupplyResult();
 				if ($listInventorySupplyResult->isSetInventorySupplyList()) { 
 					$inventorySupplyList = $listInventorySupplyResult->getInventorySupplyList();
                    $memberList = $inventorySupplyList->getmember();
                    foreach ($memberList as $member) {
                    	$amazon_res_arr[$member->getSellerSKU()] = array($member->getTotalSupplyQuantity(), $member->getInStockSupplyQuantity());                        
                    }
 				}
 			}
 		}
		catch (FBAInventoryServiceMWS_Exception $ex) {
         	return false;
     	}
     	$i++;
	}
	return $amazon_res_arr;
} 
 
function __autoload($className){
	$filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	$includePaths = explode(PATH_SEPARATOR, get_include_path());
	foreach($includePaths as $includePath){
		if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
			require_once $filePath;
            return;
		}
    }
}
?>
<?php

/**
 *@title 条码生成 
 *@author Jerry
 *@create on 2013-03-18 
 */
 
/*产品条码生成*/
if ($detail == 'skubarcode'){
    $codebar= 'BCGcode128';
    $barcodes = $this->C->service('barcode');
    return $barcodes->barcode($codebar,$sku);
    
}
/*采购订单生成条码*/
elseif ($detail == 'orderidbarcode'){
    $codebar= 'BCGcode39';
    $barcodes = $this->C->service('barcode');
    return $barcodes->barcode($codebar,$order_id);
}

?>
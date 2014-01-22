<?php

/*调用数据层类与配置*/
require('../../../initphp/initphp.php');
require('../../conf.inc.php');
require('wmsconf.php');
require('../../lib/nusoap/nusoap.php');

/*将调拨单转置待发货状态*/
function StockTransferDetailSave($order_id,$username,$token) {
    $C			= new C();//实例化控制层
    if(!$C->C->service('global')->checktoken($token)) return false;
    $objprocess	= $C->S->dao('process');//实例化表
    $username	= $C->S->dao('user')->D->get_one(array('username'=>$username),'eng_name');

    $sid		= $objprocess->D->update(array('order_id'=>$order_id),array('statu'=>'2','muser'=>$username,'mdate'=>date('Y-m-d H:i:s',time()),'ruser'=>$username,'rdate'=>date('Y-m-d H:i:s',time())));

    if($sid){
        return '1';
    }else{
        return '0';
    }
}

//LK
$namespace = "http://erp.loftk.com.cn/erp/api/wms";
//MIU
//$namespace = "http://erp.miucolor.com/miu/api/wms";
$server = new soap_server();
$server->configureWSDL("StockTransferDetailSaveService");
$server->wsdl->schemaTargetNamespace = $namespace;
$server->register(
    'StockTransferDetailSave',
    array('order_id'=>'xsd:string','username'=>'xsd:string','token'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    '转仓单转置待发货状态'
);

$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

$server->service($POST_DATA);
exit();
?>
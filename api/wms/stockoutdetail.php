<?php
/*调用数据层类与配置*/
require('../../../initphp/initphp.php');
require('../../conf.inc.php');
require('wmsconf.php');
require('../../lib/nusoap/nusoap.php');

/*显示单条调拨单的详情*/
function StockOutDetail($order_id,$username,$token) {
    $C			= new C();//实例化控制层
    $objprocess	= $C->S->dao('process');//实例化表

    if(!$C->C->service('global')->checktoken($token)) return false;

    $datalist	= $objprocess->D->get_allstr(' and order_id="'.$order_id.'" and statu="1" ','','','sku,product_name,quantity');

    /*数据封装*/
    if(count($datalist) > 0){
        $data_array = array();
        header("Content-type: text/xml");

        foreach($datalist as $val){
            $data_array[] = array('SKU'=>$val['sku'],'产品名称'=>$val['product_name'],'数量'=>$val['quantity']);
        }

        //创建一个XML文档并设置XML版本和编码
        $dom=new DomDocument('1.0', 'utf-8');

        //创建根节点
        $article = $dom->createElement('article');
        $dom->appendchild($article);

        foreach ($data_array as $data) {
            $item = $dom->createElement('item');
            $article->appendchild($item);

            create_item($dom, $item, $data);
        }

        return $dom->saveXML();
    } else {
        return false;
    }
}

function create_item($dom, $item, $data, $attribute='') {
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            //创建元素
            $$key = $dom->createElement($key);
            $item->appendchild($$key);

            //创建元素值
            $text = $dom->createTextNode($val);
            $$key->appendchild($text);

            if (isset($attribute[$key])) {//如果此字段存在相关属性需要设置
                foreach ($attribute[$key] as $akey => $row) {
                    //创建属性节点
                    $$akey = $dom->createAttribute($akey);
                    $$key->appendchild($$akey);

                    //创建属性值节点
                    $aval = $dom->createTextNode($row);
                    $$akey->appendChild($aval);
                }
            }
        }
    }
}

//LK
$namespace = "http://erp.loftk.com.cn/erp/api/wms";
//MIU
//$namespace = "http://erp.miucolor.com/miu/api/wms";
$server = new soap_server();
$server->configureWSDL("StockOutDetailService");
$server->wsdl->schemaTargetNamespace = $namespace;
$server->register(
    'StockOutDetail',
    array('order_id'=>'xsd:string','username'=>'xsd:string','token'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    '出库单条详情'
);

$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

$server->service($POST_DATA);
exit();
?>
<?php
/*调用数据层类与配置*/
require('../../../initphp/initphp.php');
require('../../conf.inc.php');
require('wmsconf.php');
require('../../lib/nusoap/nusoap.php');

function StockOut($stockin_no = '',$username,$token) {
    $C			= new C();//实例化控制层
    $objprocess	= $C->S->dao('process');//实例化表
    $sevice		= $C->C->service('warehouse');

    if(!$C->C->service('global')->checktoken($token)) return false;

    $datalist	= $objprocess->D->get_allstr(' and statu="1" and provider_id="10" and (protype="售出" or protype="重发") and isover="N"','','','order_id,sku,product_name,quantity');

    if(count($datalist) > 0){
        $data_array = array();
        header("Content-type: text/xml");

        for($i=0;$i<count($datalist);$i++){

            $order_id		= ($datalist[$i]['order_id'] == $datalist[$i-1]['order_id'])?'':$datalist[$i]['order_id'];
            $data_array[]	= array('order_id'=>$datalist[$i]['order_id'],'订单号'=>$order_id,'SKU'=>$datalist[$i]['sku'],'产品名称'=>$datalist[$i]['product_name'],'数量'=>$datalist[$i]['quantity']);

        }

        //创建一个XML文档并设置XML版本和编码。。
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
$server->configureWSDL("StockOutService");
$server->wsdl->schemaTargetNamespace = $namespace;
$server->register(
    'StockOut',
    array('list'=>'xsd:string','username'=>'xsd:string','token'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    '已接收出库订单'
);

$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

$server->service($POST_DATA);
exit();
?>
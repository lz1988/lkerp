<?php
/*调用数据层类与配置*/
require('../../../initphp/initphp.php');
require('../../../initphp/core/dao/db.init.php');
require('../../conf.inc.php');
require('../../lib/dao/processDao.php');
require('../../lib/nusoap/nusoap.php');

function StockIn($stockin_no,$username,$token) {
    if(!empty($stockin_no)){
        global $InitPHP_G;
        $process	= new dbInit('process');
        $process->init($InitPHP_G['db'],$InitPHP_G['db_type']);//调用框架方法链接数据库
        $backcheck	= $process->query_array('select * from client where token="'.$token.'"');


        if(!$backcheck) return false;


		$objprocess	= new processDao();
		$backdata	= $objprocess->gorecstockcigou($stockin_no);

        if(count($backdata) > 0){
            $data_array = array();
            header("Content-type: text/xml");

            foreach($backdata as $val){
                $data_array[] = array('detail_id'=>$val['id'],'产品SKU'=>$val['sku'],'产品名称'=>$val['product_name'],'入库仓库'=>$val['name'],'订单数量'=>$val['quantity'],'入库数量'=>$val['quantity']-$val['countnum'],'备注'=>'');
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
$server->configureWSDL("StockInService");
$server->wsdl->schemaTargetNamespace = $namespace;
$server->register(
    'StockIn',
    array('stockin_no'=>'xsd:string','username'=>'xsd:string','token'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    '采购单查询'
);

$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

$server->service($POST_DATA);
exit();
?>
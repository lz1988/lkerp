<?php

$InitPHP_G = array();
/* 数据库配置 */
$InitPHP_G['db_type']                        = 0; //0-单个服务器，1-读写分离，3-随机
$InitPHP_G['db'][0]['localhost']             = 'localhost'; //主机  118.123.20.188
$InitPHP_G['db'][0]['username']              = 'root'; //数据库用户名 admin_vps   (hanson)
$InitPHP_G['db'][0]['password']              = '111'; //数据库密码 vps123457         (123123)
$InitPHP_G['db'][0]['database']              = 'loftkerp'; //数据库 vps_erp         (vps_erp)
$InitPHP_G['db'][0]['charset']               = 'utf8'; //数据库编码
$InitPHP_G['db'][0]['pconnect']              = 0; //是否持久链接

/* memcache配置 */
//$InitPHP_G['memcache'][0]   = array('127.0.0.1', '11211');

/* 控制器目录结构，支持2级目录 */
$InitPHP_G['controller'] = array(
    'controller/adminerp',
);
/* Service目录结构，支持2级目录 */
$InitPHP_G['service'] = array(
    'lib/service/',
);
/* Dao目录结构，支持2级目录 */
$InitPHP_G['dao'] = array(
    'lib/dao/',
);
?>

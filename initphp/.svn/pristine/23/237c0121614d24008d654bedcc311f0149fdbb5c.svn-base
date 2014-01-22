<?php
/* 框架全局配置常量 */
error_reporting(E_ERROR | E_PARSE);
define('INITPHP_PATH', dirname(__FILE__));
define('IS_INITPHP', 1);
define('TRANSFER_HOUSE',10);//物料调拨默认蛇口仓库检查库存
date_default_timezone_set('PRC'); //设置中国时区 
ini_set('memory_limit','400M');
/* 框架全局配置变量 */
$InitPHP_conf = array();
/* 模板设置 */
$InitPHP_conf['template']['template_path']      = 'template'; //模板路劲
$InitPHP_conf['template']['template_c_path']    = 'template_c'; //模板编译路劲
$InitPHP_conf['template']['template_type']      = 'tpl'; //模板文件类型
$InitPHP_conf['template']['template_c_type']    = 'tpl.php';//模板编译文件类型
$InitPHP_conf['template']['template_tag_left']  = '<!--{';//模板左标签
$InitPHP_conf['template']['template_tag_right'] = '}-->';//模板右标签
$InitPHP_conf['template']['is_compile']         = true;//模板每次编译-系统上线后可以关闭此功能
$InitPHP_conf['template']['driver']             = 'simple'; //不同的模板驱动编译
/* 控制器参数设置 */
$InitPHP_conf['controller']['controller_postfix']    = 'Controller'; //控制器文件后缀名
$InitPHP_conf['controller']['action_postfix']        = ''; //Action函数名称后缀
$InitPHP_conf['controller']['default_controller']    = 'common'; //默认执行的控制器名称
$InitPHP_conf['controller']['default_action']        = 'index'; //默认执行的Action函数
$InitPHP_conf['controller']['default_before_action'] = 'before'; //默认前置的ACTION名称
$InitPHP_conf['controller']['default_after_action']  = 'after'; //默认后置ACTION名称
/* Service类名后缀 */
$InitPHP_conf['service']['service_postfix']  = 'Service'; //后缀
/* Dao类名后缀 */
$InitPHP_conf['dao']['dao_postfix']  = 'Dao'; //后缀
/* 缓存设置 */
$InitPHP_conf['cache']['filepath'] = 'data/filecache';   //文件缓存目录
$InitPHP_conf['cache']['maxtime']  = 10;   //缓存时间
/* 是否 开启URI访问方式 */
$InitPHP_conf['isuri'] = true;
/* 是否开启输出函数进行javascript过来，建议开启 */
$InitPHP_conf['isviewfilter'] = true;
/* cookie参数 */
$InitPHP_conf['cookie']['prefix'] = "init_"; //cookie前缀
$InitPHP_conf['cookie']['expire'] = 3600 * 24 * 30; //cookie实效
$InitPHP_conf['cookie']['path']   = '/'; //作用域
$InitPHP_conf['cookie']['domain'] = ''; //作用的主机
/* hook插件机制 - 插件文件目录 */
$InitPHP_conf['hook']['path']          = 'lib/hook'; //插件文件夹目录， 不需要加'/'
$InitPHP_conf['hook']['class_postfix'] = 'Hook'; //默认插件类名后缀
$InitPHP_conf['hook']['file_postfix']  = '.hook.php'; //默认插件文件名称
$InitPHP_conf['hook']['config']        = 'hook.conf.php'; //配置文件
/* 单元测试文件后缀名称 */
$InitPHP_conf['unittesting']['test_postfix'] = $InitPHP_conf['service']['service_postfix'] . 'Test';
$InitPHP_conf['unittesting']['path'] = 'lib/test/';
/* Error机制模板文件 */
$InitPHP_conf['error']['template'] = 'lib/helper/error.tpl.php';
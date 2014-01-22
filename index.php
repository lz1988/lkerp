<?php
SESSION_START();
define("APP_PATH",dirname(__FILE__));
require_once('conf.inc.php'); //加载配置文件
require_once('../initphp/initphp.php'); //加载入口文件
header("Content-Type:text/html; charset=utf-8");
ini_set("max_execution_time","180000");
$InitPHP = new InitPHP();
$InitPHP->init();
?>
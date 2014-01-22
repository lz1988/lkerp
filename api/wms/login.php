<?php
/*调用数据层类与配置*/
define('IS_INITPHP','isset');
require('../../../initphp/core/dao/db.init.php');
require('../../../initphp/initphp.php');
require('../../conf.inc.php');
require('../../lib/nusoap/nusoap.php');

function UserLogin($username,$password) {
    global $InitPHP_G;
	$user = new dbInit('user');
	$user->init($InitPHP_G['db'],$InitPHP_G['db_type']);//调用框架方法链接数据库
    
    $sql = "SELECT user.*,admin_group.groupname FROM user LEFT JOIN admin_group ON admin_group.id=user.groupid WHERE groupname LIKE '%物流%' AND username='".$username."' AND password='".md5($password)."' ";
    $user_data = $user->get_all_sql($sql);
    
    if(!empty($user_data[0]['uid']))
    {
        return 1;
    } else {
        return 0;
    }
}

//LK
$namespace = "http://erp.loftk.com.cn/erp/api/wms";
//MIU
//$namespace = "http://erp.miucolor.com/miu/api/wms";
$server = new soap_server();
$server->configureWSDL("MemberLoginService");
$server->wsdl->schemaTargetNamespace = $namespace;
$server->register(
    'UserLogin', 		 
    array('member_name'=>'xsd:string','password'=>'xsd:string'), 
    array('return'=>'xsd:string'),
    $namespace,
    false,
    'rpc',
    'encoded',
    '用户登陆'
);

$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

$server->service($POST_DATA);                
exit();
?>  
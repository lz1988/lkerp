<?php
require_once("lib/nusoap/nusoap.php");

function UserLogin($username,$password) {
	if($username == 'leon' && $password == '123456')
    {
        return '1';
    } else {
        return '0';
    }
}

$namespace = "http://192.168.18.42/mvclkerp";

$server = new soap_server();

$server->configureWSDL("MemberLoginService");

$server->wsdl->schemaTargetNamespace = $namespace;

$server->register(
    'UserLogin',
    array('username'=>'xsd:string','password'=>'xsd:string'),
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
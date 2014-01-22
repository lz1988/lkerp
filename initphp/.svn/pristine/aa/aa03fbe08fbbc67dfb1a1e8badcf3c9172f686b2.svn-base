<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 request核心文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class requestInit {
	
	/**
	 * Request-获取POST信息
	 * 
	 * @param  string $name POST的键值名称
	 * @return string
	 */
	public function get_post($name = '') {
		if (empty($name)) return $_POST;
		return (isset($_POST[$name])) ? $_POST[$name] : '';
	}
	
	/**
	 * Request-获取GET方法的值
	 * 
	 * @param  string $name GET的键值名称
	 * @return string
	 */
	public function get_get($name = '') {
		if (empty($name)) return $_GET;
		return (isset($_GET[$name])) ? $_GET[$name] : '';
	}
	
	/**
	 * Request-获取COOKIE信息
	 * 
	 * @param  string $name COOKIE的键值名称
	 * @return string
	 */
	public function get_cookie($name = '') {
		if ($name == '') return $_COOKIE;
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : '';
	}
	
	/**
	 * Request-获取SESSION信息
	 * 
	 * @param  string $name SESSION的键值名称
	 * @return string
	 */
	public function get_session($name = '') {
		if ($name == '') return $_SESSION;
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : '';
	}
	
	/**
	 * Request-获取ENV信息
	 * 
	 * @param  string $name ENV的键值名称
	 * @return string
	 */
	public function get_env($name = '') {
		if ($name == '') return $_ENV;
		return (isset($_ENV[$name])) ? $_ENV[$name] : '';
	}
	
	/**
	 * Request-获取SERVICE信息
	 * 
	 * @param  string $name SERVER的键值名称
	 * @return string
	 */
	public function get_service($name = '') {
		if ($name == '') return $_SERVER;
		return (isset($_SERVER[$name])) ? $_SERVER[$name] : '';
	}
	
	/**
	 *	Request-获取当前正在执行脚本的文件名
	 *
	 *  @return string
	 */
	public function get_php_self() {
		return $this->get_service('PHP_SELF');
	}
	
	/**
	 *	Request-获取当前正在执行脚本的文件名
	 *
	 *  @return string
	 */
	public function get_service_name() {
		return $this->get_service('SERVER_NAME');
	}
	
	/**
	 *	Request-获取请求时间
	 *
	 *  @return int
	 */
	public function get_request_time() {
		return $this->get_service('REQUEST_TIME');
	}
	
	/**
	 * Request-获取useragent信息
	 * 
	 * @return string
	 */
	public function get_useragent() {
		return $this->get_service('HTTP_USER_AGENT');	
	}	
	
	/**
	 * Request-获取URI信息
	 * 
	 * @return string
	 */
	public function get_uri() {
		return $this->get_service('REQUEST_URI');
	}
	
	/**
	 * Request-判断是否为POST方法提交
	 * 
	 * @return bool
	 */
	public function is_post() {
		return (strtolower($this->get_service('REQUEST_METHOD')) == 'post') ? true : false;
	}
	
	/**
	 * Request-判断是否为GET方法提交
	 * 
	 * @return bool
	 */
	public function is_get() {
		return (strtolower($this->get_service('REQUEST_METHOD')) == 'get') ? true : false;
	}
	
	/**
	 * Request-判断是否为AJAX方式提交
	 * 
	 * @return bool
	 */
	public function is_ajax() {
		if ($this->get_service('HTTP_X_REQUESTED_WITH') && strtolower($this->get_service('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') return true;
		if ($this->get_post('initphp_ajax') || $this->get_get('initphp_ajax')) return true; //程序中自定义AJAX标识
		return false;
	}

	/**
	 *	Request-用户用户IP 
	 *
	 *  @return string
	 */
	public function get_ip() {
		static $realip;
    	if (isset($_SERVER)){
        	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
           		$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        	} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            	$realip = $_SERVER["HTTP_CLIENT_IP"];
        	} else {
            	$realip = $_SERVER["REMOTE_ADDR"];
        	}
    	} else {
        	if (getenv("HTTP_X_FORWARDED_FOR")){
           		$realip = getenv("HTTP_X_FORWARDED_FOR");
        	} else if (getenv("HTTP_CLIENT_IP")) {
            	$realip = getenv("HTTP_CLIENT_IP");
        	} else {
            	$realip = getenv("REMOTE_ADDR");
        	}
    	}
    	return $realip;
	}
	
	/**
	 * Request-URI方式访问
	 * 
	 * @return bool
	 */
	protected function uri() {
		$uri = $this->get_uri();
		$file = dirname($_SERVER['SCRIPT_NAME']);
		$request = str_replace($file,'',$uri);
		$request = explode('/', trim($request, '/'));
		if (isset($request[0]) && strpos($request[0], '.php')) {
			if (isset($request[1])) {
				$_GET['c'] = $request[1];
				unset($request[1]);
			}
			if (isset($request[2])) {
				$_GET['a'] = $request[2];
				unset($request[2]);
			}
			unset($request[0]);		
		} else {
			if (isset($request[0])) {
				$_GET['c'] = $request[0];
				unset($request[0]);
			}
			if (isset($request[1])) {
				$_GET['a'] = $request[1];
				unset($request[1]);
			}
		}
		if (count($request) > 1) {
			$mark = 0;
			$val = $key = array();
			foreach($request as $value){
				$mark++;
				if ($mark % 2 == 0) {
					$val[] = $value;
				} else {
					$key[] = $value;
				}
			}
			if(count($key) !== count($val)) $val[] = NULL;
			$get = array_combine($key,$val);
			foreach($get as $key=>$value) $_GET[$key] = $value;
		}
		return true;
	}
	
}
?>

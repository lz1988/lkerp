<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 Cookie操作工具
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class cookieInit {
	
	private $prefix = "init_"; //cookie前缀
	private $expire = 2592000; //cookie时间
	private $path   = '/'; //cookie路劲
	private $domain = '';
	
	/**
 	 * 设置cookie的值
	 * 
 	 * @param  string $name    cookie的名称
	 * @param  string $val     cookie值
	 * @param  string $expire  cookie失效时间
	 * @param  string $path    cookie路劲
	 * @param  string $domain  cookie作用的主机
 	 * @return string   
	 */
	public function set($name, $val, $expire = '', $path = '', $domain = '') {
		$expire = (empty($expire)) ? time() + $this->expire : $expire; //cookie时间
		$path   = (empty($path)) ? $this->path : $path; //cookie路劲
		$domain = (empty($domain)) ? $this->domain : $domain; //主机名称
		if (empty($domain)) {
			setcookie($this->prefix.$name, $val, $expire, $path);
		} else {
			setcookie($this->prefix.$name, $val, $expire, $path, $domain);
		}
		$_COOKIE[$this->prefix.$name] = $val;
	}
	
	/**
 	 * 获取cookie的值
	 * 
 	 * @param  string $name    cookie的名称
 	 * @return string   
	 */
	public function get($name) {
		return $_COOKIE[$this->prefix.$name];
	}
	
	/**
 	 * 删除cookie值
	 * 
 	 * @param  string $name    cookie的名称
 	 * @return string   
	 */
	public function del($name) {
		$this->set($name, '', time() - 3600);
		$_COOKIE[$this->prefix.$name] = '';
		unset($_COOKIE[$this->prefix.$name]);
	}
	
	/**
 	 * 检查cookie是否存在
	 * 
 	 * @param  string $name    cookie的名称
 	 * @return string   
	 */
	public function is_set($name) {
		return isset($_COOKIE[$this->prefix.$name]);
	}
}
?>
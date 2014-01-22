<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');   
/*********************************************************************************
 * InitPHP 1.0 SESSION操作
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/  
class sessionInit {

	/**
 	 * Session-session_start()
	 * 
 	 * @return string   
	 */
	public function start() {
		session_start();
	}
	
	/**
 	 * Session-设置session值
	 * 
	 * @param  string $key    key值，可以为单个key值，也可以为数组
	 * @param  string $value  value值
 	 * @return string   
	 */
	public function set($key, $value='') {
		if (!is_array($key)) {
			$_SESSION[$key] = $value;
		} else {
			foreach ($key as $k => $v) $_SESSION[$k] = $v;
		}
		return true;
	}
	
	/**
 	 * Session-获取session值
	 * 
	 * @param  string $key    key值
 	 * @return string   
	 */
	public function get($key) {
		return (isset($_SESSION[$key])) ? $_SESSION[$key] : NULL;
	}
	
	/**
 	 * Session-删除session值
	 * 
	 * @param  string $key    key值
 	 * @return string   
	 */
	public function del($key) {
		if (is_array($key)) {
			foreach ($key as $k){
				if (isset($_SESSION[$k])) unset($_SESSION[$k]);
			}
		} else {
			if (isset($_SESSION[$key])) unset($_SESSION[$key]);
		}
		return true;
	}
	
	/**
 	 * Session-清空session
	 * 
 	 * @return   
	 */
	public function clear() {
		session_destroy();
	}
	
}
?>
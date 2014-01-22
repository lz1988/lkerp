<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 队列操作
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class queueInit {
	//存放队列数据
	private static $queue = array();
	
	/**
 	 * 队列-设置值
	 * 
 	 * @return string   
	 */
	public function set($val) {
		array_unshift(self::$queue, $val);
		return true;
	}
	
	/**
 	 * 队列-从队列中获取一个最早放进队列的值
	 * 
 	 * @return string   
	 */
	public function get() {
		return array_pop(self::$queue);
	}
	
	/**
 	 * 队列-队列中总共有多少值
	 * 
 	 * @return string   
	 */
	public function count() {
		return count(self::$queue);
	}
	
	/**
 	 * 队列-清空队列数据
	 * 
 	 * @return string   
	 */
	public function clear() {
		return self::$queue = array();
	}
}
?>
<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 DAO-memcache内存缓存类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class memcachedInit {
	
	private $memcache;
	private $max_cache_time;
		
	/**
	 * Memcache缓存-设置缓存
	 *
	 * @param  string $key   KEY值
	 * @param  string $value 值
	 */
	public function set_cache($key, $value) {
		return $this->memcache->set($key, $value, 0, $this->max_cache_time);
	}
	
	/**
	 * Memcache缓存-获取缓存
	 *
	 * @param  string $key   KEY值
	 */
	public function get_cache($key) {
		return $this->memcache->get($key);
	}
	
	/**
	 * Memcache缓存-清除一个缓存
	 *
	 * @param  string $key   KEY值
	 */
	public function clear($key) {
		return $this->memcache->delete($key);
	}
	
	/**
	 * Memcache缓存-清空所有缓存
	 *
	 * @return
	 */
	public function clear_all() {
		return $this->memcache->flush();
	}
	
	/**
	 * Memcache缓存-设置链接服务器
	 * 支持多MEMCACHE服务器
	 *
	 * @param  array $servers 服务器数组-array(array('127.0.0.1', '11211'))
	 */
	public function add_server($servers) {
		$this->memcache = new Memcache;
		if (!is_array($servers) || empty($servers)) exit('memcache server is null!');
		foreach ($servers as $val) {
			$this->memcache->addServer($val[0], $val[1]);
		}	
	}
	
	/**
	 * MYSQL缓存-设置缓存时间
	 * 
	 * @param  int $time 缓存时间
	 * @return int
	 */
	public function set_cache_time($time) {
		return $this->max_cache_time = (int) $time;
	}
}
?>

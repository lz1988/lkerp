<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 cache核心类文件-NOSQL解决方案
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
require_once("db.init.php");
class cacheInit extends dbInit{

	//缓存类型 FILE-文件缓存类型 MEM-内存缓存类型 MYSQL-数据库缓存类型
	private $cache_type = array('FILE','MEM', 'MYSQL');
	private static $instance = array();  //单例模式获取缓存类
	
	/**
	 * NOSQL解决方案-缓存接口
	 * 
	 * @param  string $key   缓存键值
	 * @param  string $value 缓存数据
	 * @param  string $type  缓存类型
	 * @return value
	 */
	public function cache($key, $value, $type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		$iscache = $cache->get_cache($key);
		if ($iscache == false) {
			if ($cache->set_cache($key, $value)) return $value;
			return false;
		} else {
			return $iscache;
		}
	}
	
	/**
	 * NOSQL解决方案-缓存设置
	 * 
	 * @param  string $key   缓存键值
	 * @param  string $value 缓存数据
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function set_cache($key, $value, $type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		return $cache->set_cache($key, $value);
	}
	
	/**
	 * NOSQL解决方案-获取缓存值
	 * 
	 * @param  string $key   缓存键值
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function get_cache($key, $type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		return $cache->get_cache($key);
	}
	
	/**
	 * NOSQL解决方案-清除缓存
	 * 
	 * @param  string $key   缓存键值
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function clear($key, $type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		return $cache->clear($key);
	}
	
	/**
	 * NOSQL解决方案-清除全部缓存
	 * 
	 * @param  string $type  缓存类型
	 * @return 
	 */
	public function clear_all($type = 'FILE') {
		$cache = $this->get_cache_handle($type);
		return $cache->clear_all();
	}
	
	/**
	 * NOSQL解决方案-获取不同缓存类型的对象句柄
	 * 
	 * @param  string $type  缓存类型
	 * @return obj
	 */
	private function get_cache_handle($type) {
		global $InitPHP_conf; //需要设置文件缓存目录
		$type = strtoupper($type);
		$type = (in_array($type, $this->cache_type)) ? $type : 'FILE';
		if ($type == 'FILE') {
			$filecache = $this->load_cache('filecache.init.php', 'filecacheInit');
			$filecache->set_cache_path($InitPHP_conf['cache']['filepath']);
			$filecache->set_cache_time($InitPHP_conf['cache']['maxtime']);
			return $filecache;
		} elseif ($type == 'MEM') {
			global $InitPHP_G;
			$mem = $this->load_cache('memcached.init.php', 'memcachedInit');
			$mem->add_server($InitPHP_G['memcache']); //添加服务器
			$mem->set_cache_time($InitPHP_conf['cache']['maxtime']);
			return $mem;
		} elseif ($type == 'MYSQL') {
			$mysqlcache = $this->load_cache('mysqlcache.init.php', 'mysqlcacheInit');
			$mysqlcache->set_cache_time($InitPHP_conf['cache']['maxtime']);
			$mysqlcache->set_sql_handler($this);
			return $mysqlcache;
		}
	}
	
	/**
	 * NOSQL解决方案-加载不同缓存类文件
	 * 
	 * @param  string $file  缓存文件名
	 * @param  string $class 缓存类名
	 * @return obj
	 */
	private function load_cache($file, $class) {
		if (cacheInit::$instance['require']['cache/'.$file] !== TRUE) {
			require('cache/'.$file);
			cacheInit::$instance['require']['cache/'.$file] = TRUE;
		}
		if (cacheInit::$instance['class'][$class] !== TRUE) {
			return new $class;
			cacheInit::$instance['class'][$class] = TRUE;
		} else {
			return cacheInit::$instance['class'][$class];
		}
	}
}
?>

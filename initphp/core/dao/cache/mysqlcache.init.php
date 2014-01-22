<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 DAO-内存缓存核心文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
 *************************************************************************
 *	CREATE TABLE `initphp_mysqlcache` (
 *	 `id` int(10) NOT NULL auto_increment,
 *	 `k` varchar(255) NOT NULL default '',
 *   `v` text NOT NULL default '',
 *   `dtime` int(10) NOT NULL default '0',
 *   PRIMARY KEY  (`id`),
 *   KEY `k` (`k`)
 *  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
***********************************************************************************/
class mysqlcacheInit {

	private $sql;
	private $max_cache_time;

	/**
	 * MYSQL缓存-设置缓存
	 * 
	 * @param string $key    缓存名
	 * @param array  $value  缓存数据
	 * @return
	 */
	public function set_cache($key, $value) {
		$key = $this->get_cache_key($key);
		$time = time();
		/* 缓存不存在的情况 */
		$sql = sprintf("INSERT INTO initphp_mysqlcache (k, v, dtime) VALUES (%s, %s, %d)", 
			$this->sql->build_escape($key), 
			$this->sql->build_escape(base64_encode(@serialize($value))),
			$time
		);
		return $this->sql->query($sql);
	}
	
	/**
	 * MYSQL缓存-获取缓存
	 * 
	 * @param string $key   缓存名
	 * @param array  $value 缓存数据
	 * @return
	 */
	public function get_cache($key) {
		$key = $this->get_cache_key($key);
		/* 缓存是否过期 */
		$select_sql = sprintf("SELECT * FROM initphp_mysqlcache WHERE k = %s LIMIT 1", $this->sql->build_escape($key));
		$query = $this->sql->query($select_sql);
		$result = $this->sql->fetch_assoc($query);
		if (!$result) return false;
		if ((time() > ($result['dtime'] + $this->max_cache_time))) {
			$delete_sql = sprintf("DELETE FROM initphp_mysqlcache WHERE k = %s ", $this->sql->build_escape($key));
			$this->sql->query($delete_sql); //缓存过期，则删除缓存
			return false; //过期
		}
		return @unserialize(base64_decode($result['v']));
	}
	
	/**
	 * MYSQL缓存-清除缓存
	 * 
	 * @param string $key   缓存名
	 * @return
	 */
	public function clear($key) {
		$key = $this->get_cache_key($key);
		$sql = sprintf("DELETE FROM initphp_mysqlcache WHERE k = %s", $this->sql->build_escape($key));
		return $this->sql->query($sql);
	}
	
	/**
	 * MYSQL缓存-清除所有缓存
	 * 
	 * @return
	 */
	public function clear_all() {
		$sql = "TRUNCATE TABLE initphp_mysqlcache";
		return $this->sql->query($sql);
	}
	
	/**
	 * MYSQL缓存-获取DB handler
	 * 
	 * @return
	 */
	public function set_sql_handler($obj) {
		$this->sql = $obj;
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
	
	/**
	 * MYSQL缓存-获取缓存KEY值
	 * 
	 * @param  string $key   缓存名
	 * @return string
	 */
	private function get_cache_key($key) {
		return md5(trim($key));
	}
}
?>

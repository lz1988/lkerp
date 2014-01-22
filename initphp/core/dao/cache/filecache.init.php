<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 文件缓存基础类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class filecacheInit {
	
	private $max_cache_time = 3600; //缓存最大时间
	private $cache_path = '.'; //缓存路劲
	
	/**
	 * 文件缓存-设置缓存
	 * 
	 * @param string $filename 缓存名
	 * @param array  $data     缓存数据
	 */
	public function set_cache($filename, $data) {
		 $filename = $this->get_cache_filename($filename);
		 @file_put_contents($filename, '<?php exit;?>' . time() . serialize($data));
		 clearstatcache();
		 return true;
	}
	
	/**
	 * 文件缓存-获取缓存
	 * 
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function get_cache($filename) {
		$filename = $this->get_cache_filename($filename);
		/* 缓存不存在的情况 */
		if (!file_exists($filename)) return false; 
		$data = file_get_contents($filename); //获取缓存
		/* 缓存过期的情况 */
		$filetime = substr($data, 13, 10);
		if (time() > ($filetime + $this->max_cache_time)) return false; //缓存过期
		/* 未过期，获取缓存 */
        $data  = substr($data, 23);
        return @unserialize($data);
	}
	
	/**
	 * 文件缓存-清除缓存
	 * 
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function clear($filename) {
		$filename = $this->get_cache_filename($filename);
		if (!file_exists($filename)) return true;
		@unlink($filename);
		return true;
	}
	
	/**
	 * 文件缓存-清除全部缓存
	 * 
	 * @param  string $filename 缓存名
	 * @return array
	 */
	public function clear_all() {
		@set_time_limit(3600);
		$path = opendir($this->cache_path);		
		while (false !== ($filename = readdir($path))) {
			if ($filename !== '.' && $filename !== '..') {
   				@unlink($this->cache_path . '/' .$filename);
			}
		}
		closedir($path);
		return true;
	}
	
	/**
	 * 设置缓存时间
	 * 
	 * @param  int $time 缓存时间
	 * @return int
	 */
	public function set_cache_time($time) {
		return $this->max_cache_time = (int) $time;
	}
	
	/**
	 * 设置文件缓存路劲
	 * 
	 * @param  string $path 路劲
	 * @return string
	 */
	public function set_cache_path($path) {
		return $this->cache_path = $path;
	}
	
	/**
	 * 获取缓存文件名
	 * 
	 * @param  string $filename 缓存名
	 * @return string
	 */
	private function get_cache_filename($filename) {
		$filename = md5($filename); //文件名MD5加密
		$filename = $this->cache_path .'/'. $filename . '.php';
		return $filename;
	}
}
?>

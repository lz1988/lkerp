<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 initphp核心包文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class coreInit{

	private static $instance = array();
	private $init_path = array(
					'd' => '/core/dao/', //DAO层
					's' => '/core/service/', //service层
					'c' => '/core/controller/', //controller层
					'v' => '/core/view/', //view层
					'u' => '/core/util/', //核心工具
					'l' => '/library/', //扩展类库
					);

	/**
	 *	系统自动加载InitPHP类库
	 *
	 *  @param  string  $class_name  类名称
	 *  @param  string  $type        类所属类型
	 *  @return object
	 */
	public function load($class_name, $type ,$tableName='') {
		$class_path = $this->get_class_path($class_name, $type);
		$class_name = $this->get_class_name($class_name);
		$class_path = INITPHP_PATH . $class_path;
		if (!file_exists($class_path)) exit('file '. $class_name . '.php is not exist!');
		/* 重写为开启强制重新实例化 */
		//if (!isset(self::$instance[$class_name])) {
			require_once($class_path);
			if (!class_exists($class_name)) exit('class' . $class_name . ' is not exist!');
			if($class_name != 'daoInit') {
				$init_class = new $class_name;
			}else{
				$init_class = new $class_name("$tableName");
			}
			self::$instance[$class_name] = $init_class;
		//}

		return self::$instance[$class_name];
	}

	/**
	 *	系统获取library下面的类
	 *
	 *  @param  string  $class_name  类名称
	 *  @return object
	 */
	public function getLibrary($class) {
		return $this->load($class, 'l');
	}

	/**
	 *	系统获取Util类函数
	 *
	 *  @param  string  $class_name  类名称
	 *  @return object
	 */
	public function getUtil($class) {
		return $this->load($class, 'u');
	}

	/**
	 *	获取系统类文件路劲
	 *
	 *  @param  string  $class_name  类名称
	 *  @param  string  $type        类所属类型
	 *  @return string
	 */
	private function get_class_path($class_name, $type) {
		return $this->init_path[$type] . $class_name . '.init.php';
	}

	/**
	 *	获取系统类完整名称
	 *
	 *  @param  string  $class_name  类名称
	 *  @return string
	 */
	private function get_class_name($class_name) {
		return $class_name . 'Init';
	}

}
?>

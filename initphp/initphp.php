<?php
/*********************************************************************************
 * InitPHP 1.0 框架入口文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
require_once('initphp.conf.php'); //导入框架类
require_once('core/core.init.php'); //导入核心类文件
class InitPHP extends coreInit{

	private $controller_postfix     = 'Controller'; //控制器后缀
	private $action_postfix         = ''; //动作后缀
	private $default_controller     = 'common'; //默认执行的控制器名称
	private $default_action         = 'index'; //默认执行动作名称
	//private $default_before_action  = 'before';//默认的前置Action
	//private $default_after_action   = 'after'; //默认的后置Action
	private static $instance        = array();


	/**
	 *	InitPHP装载器
	 *
	 *  @return object
	 */
	public function init() {
		global $InitPHP_conf; //全局配置
		$this->set_params($InitPHP_conf['controller']);
		$controller = $this->run_controller();
		$this->run_before_action($controller);//前置Action
		$this->run_action($controller); //正常流程Action
		$this->run_after_action($controller); //后置Action
	}

	/**
	 *	控制器参数设置
	 *
	 *  @param  $params array(
	 *					'controller_postfix'    => '控制器后缀',
	 *                  'action_postfix'        => '动作后缀',
	 *                  'default_controller'    => '默认执行的控制器名称',
	 *                  'default_action'        => '默认执行动作名称',
	 *                  'default_before_action' => '默认的前置Action',
	 *                  'default_after_action'  => '默认的后置Action',
	 *						)
	 *  @return object
	 */
	public function set_params($params = array()) {
		if (isset($params['controller_postfix'])) $this->controller_postfix       = $params['controller_postfix'];
		if (isset($params['action_postfix'])) $this->action_postfix               = $params['action_postfix'];
		if (isset($params['default_controller'])) $this->default_controller       = $params['default_controller'];
		if (isset($params['default_action'])) $this->default_action               = $params['default_action'];
		if (isset($params['default_before_action'])) $this->default_before_action = $params['default_before_action'];
		if (isset($params['default_after_action'])) $this->default_after_action   = $params['default_after_action'];
	}

	/**
	 *	全局URL
	 *
	 *  @param  string $url URL 正常模式 index.php?c=test&a=test
	 *  @return object
	 */
	public static function url($url) {
		global $InitPHP_conf, $InitPHP_G;
		//path模式
		if ($InitPHP_conf['isuri']) {
			$param  = $paramArr = array();
			$urlArr = explode('?', $url);
			$file = $urlArr[0];
			$string = $InitPHP_G['url'] . $file;
			if (isset($urlArr[1])) {
				$string = $string . '/';
				$param = explode('&', $urlArr[1]);
				foreach ($param as $v) {
					$temp = explode('=', $v);
					if ($temp[0] == 'c' || $temp[0] == 'a') {
						$string .=  $temp[1] . '/';
					} else {
						$string .=  $temp[0] . '/' . $temp[1] . '/';
					}
				}
			}
			return $string;
		//正常模式
		} else {
			return $url;
		}
	}

	/**
	 *	文件加载
	 *
	 *  @param  string $filename 导入的文件名/路径
	 *  @param  array  $pathArr  文件夹路劲，数组
	 *  @return object
	 */
	public static function import($filename, array $pathArr = array()) {
		if (isset($GLOBALS['InitPHP_G']['importfile'][md5($filename)]) && empty($pathArr)) return true; //已经加载该文件，则不重复加载
		if (@is_readable($filename) == true && empty($pathArr)) {
			require($filename);
			$GLOBALS['InitPHP_G']['importfile'][md5($filename)] = true; //设置已加载
			return true;
		} else {
			/* 自动搜索文件夹 */
			foreach ($pathArr as $val) {
				$new_filename = $val . '/' . $filename;
				if (isset($GLOBALS['InitPHP_G']['importfile'][md5($new_filename)])) return true;
				if (@is_readable($new_filename)) {
					require($new_filename);// 载入文件
					$GLOBALS['InitPHP_G']['importfile'][md5($new_filename)] = true;
					return true;
				}
			}
		}
		return false;
	}

	/**
	 *	类的实例化 单例模式
	 *
	 *  @param  string $classname 类名
	 *  @param  string $force     是否强制重新实例化
	 *  @return object
	 */
	public static function loadclass($classname, $tableName='',$force = false) {
		if (preg_match('/[^a-z0-9\-_.]/i', $classname)) exit('invalid classname');
		if ($force == true) unset(self::$instance[$classname]);
		if (!isset(self::$instance[$classname])) {
			if (!class_exists($classname)) exit($classname . ' is not exist!');
			if(empty($tableName)){
				$Init_class = new $classname;
			}else{
				$Init_class = new $classname($tableName);
			}
			self::$instance[$classname] = $Init_class;
		}
		return self::$instance[$classname];
	}

	/**
	 *	InitPHP Hook钩子函数
	 *
	 *  @param  string $class  钩子名称
	 *  @param  array  $function
	 *  @return object
	 */
	public static function hook($hookname, $data = '') {
		global $InitPHP_conf;
		//配置文件
		$hookconfig = $InitPHP_conf['hook']['path'] . '/' . $InitPHP_conf['hook']['config'];
		if (!isset(self::$instance['inithookconfig']) && file_exists($hookconfig)) {
			self::$instance['inithookconfig'] = require_once($hookconfig);
		}
		if (!isset(self::$instance['inithookconfig'][$hookname])) return false;
		if (!is_array(self::$instance['inithookconfig'][$hookname])) {
			self::_hook(self::$instance['inithookconfig'][$hookname][0], self::$instance['inithookconfig'][$hookname][1], $data);
		} else {
			foreach (self::$instance['inithookconfig'][$hookname] as $v) {
				self::_hook($v[0], $v[1], $data);
			}
		}

	}

	/**
	 *	InitPHP Hook钩子具体处理函数
	 *
	 *  @param  string $class  钩子的类名
	 *  @param  array  $function  钩子方法名称
	 *  @return object
	 */
	private static function _hook($class, $function, $data = '') {
		global $InitPHP_conf;
		//类处理
		if (preg_match('/[^a-z0-9\-_.]/i', $class)) return false;
		$file_name  = $InitPHP_conf['hook']['path'] . '/' . $class . $InitPHP_conf['hook']['file_postfix'];
		$class_name = $class . $InitPHP_conf['hook']['class_postfix']; //类名
		if (!file_exists($file_name)) return false;
		if (!isset(self::$instance['inithook'][$class_name])) {
			require_once($file_name);
			if (!class_exists($class_name)) return false;
			$init_class = new $class_name;
			self::$instance['inithook'][$class_name] = $init_class;
		}
		if (!method_exists($class_name, $function)) return false;
		return self::$instance['inithook'][$class_name]->$function($data);
	}

	/**
	 *	InitPHP 输出处理函数
	 *
	 *  @param  array  $value  需要过滤的变量
	 *  @return object
	 */
	public static function output($value, $type = 'encode') {
		if ($type == 'encode') {
			if (function_exists('htmlspecialchars')) return htmlspecialchars($value);
			return str_replace(array("&", '"', "'", "<", ">"), array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;"), $value);
		} else {
			if (function_exists('htmlspecialchars_decode')) return htmlspecialchars_decode($value);
			return str_replace(array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;"), array("&", '"', "'", "<", ">"), $value);
		}
	}

	/**
	 *	InitPHP 获取Service类
	 *
	 *  @param  array  $class  Service类名称
	 *  @return object
	 */
	public static function getService($class) {
		global $InitPHP_conf, $InitPHP_G;
		$class = $class . $InitPHP_conf['service']['service_postfix'];
		if (!InitPHP::import($class . '.php', $InitPHP_G['service'])) return false;
		return InitPHP::loadclass($class);
	}

	/**
	 *	InitPHP 获取Dao类
	 *
	 *  @param  array  $class  Dao类名称
	 *  @return object
	 */
	public static function getDao($class) {
		global $InitPHP_conf, $InitPHP_G;
		$class = $class . $InitPHP_conf['dao']['dao_postfix'];
		if (!InitPHP::import($class . '.php', $InitPHP_G['dao'])) return false;
		return InitPHP::loadclass($class);
	}

	/**
	 *	控制器 启动一个Controller
	 *
	 *  @param  object  $controller Controller对象
	 *  @return
	 */
	private function run_controller() {
		$controller  = '';
		$controller  = (empty($controller)) ? $this->default_controller : trim($controller);
		$controller  = $controller . $this->controller_postfix;
		/* 如果加载失败 - 加载默认的controller */
		if (!InitPHP::import($controller . '.php', $GLOBALS['InitPHP_G']['controller'])) {
			$controller = $this->default_controller . $this->controller_postfix;
		 	InitPHP::import($controller . '.php', $GLOBALS['InitPHP_G']['controller']);
		}
		return InitPHP::loadclass($controller);
	}

	/**
	 *	控制器 启动一个Controller中的Action
	 *
	 *  @param  object  $controller Controller对象
	 *  @return
	 */
	private function run_action($controller) {
		$action_mod = trim($_GET['action']);
		if ($action_mod == 'common') {
		    exit('error:this is the failed action');
		}elseif($action_mod == ''){
			$action_mod = 'index';
		}
		$controller_file  = $GLOBALS['InitPHP_G']['controller'][0].'/'.$action_mod.$this->controller_postfix.'.php';  //需要包含的controller
		$action_name = trim($_GET['func']);//已改为公共controller的函数，此无效

		if (!in_array($action_name, $controller->InitPHP_WhiteList)) $action_name = $this->default_action; //白名单
		$action_name = $action_name . $this->action_postfix;
		if (!method_exists($controller, $action_name)) {
			$action_name = $this->default_action . $this->action_postfix;
		}

		$controller->$action_name($controller_file);
	}

	/**
	 *	控制器 启动一个在正常Action加载前加载的Action
	 *
	 *  @param  object  $controller Controller对象
	 *  @return
	 */
	private function run_before_action($controller) {
		$before_action = $this->default_before_action . $this->action_postfix;
		if (!method_exists($controller, $before_action)) return false;
		$controller->$before_action();
	}

	/**
	 *	控制器 启动一个在正常Action加载后加载的Action
	 *
	 *  @param  object  $controller Controller对象
	 *  @return
	 */
	private function run_after_action($controller) {
		$after_action = $this->default_after_action . $this->action_postfix;
		if (!method_exists($controller, $after_action)) return false;
		$controller->$after_action();
	}

}

//控制层继承的父类
class C extends coreInit{

	public function __construct() {
		global $InitPHP_conf; //全局配置
		global $InitPHP_G; //全局变量
		$this->C = $this->load('controller', 'c'); //
		$this->V = $this->load('view', 'v'); //导入view
		$this->S = $this->load('service', 's'); //导入处理类
		$this->V->set_template_config($InitPHP_conf['template']); //设置模板
		$this->V->assign('init_token', $this->C->get_token()); //全局输出init_token标记
	}

}

//Service层继承的父类
class S extends coreInit{

	public function __construct() {
		$this->S= $this->load('service', 's'); //导入处理类
		$this->C = $this->load('controller', 'c');
	}

}

//DAO层继承的父类
class D extends coreInit{
	public function __construct($tableName) {
		global $InitPHP_G; //全局变量
		$this->D = $this->load('dao' , 'd',$tableName);
		$this->D->init($InitPHP_G['db'],$InitPHP_G['db_type']);
	}
}

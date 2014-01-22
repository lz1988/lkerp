<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 模板文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class templateInit {

	private $template_path      = 'template'; //模板目录
	private $template_c_path    = 'template_c'; //编译目录
	private $template_type      = 'htm'; //模板文件类型
	private $template_c_type    = 'tpl.php'; //模板编译文件类型
	private $template_tag_left  = '<!--{'; //左标签
	private $template_tag_right = '}-->'; //右标签
	private $is_compile 		= true; //是否需要每次编译
	private $driver_config;
	private static $driver      = NULL; //定义默认的一个模板编译驱动模型
	
	/**
	 * 模板编译-设置模板信息
	 * 
	 * @param  array $config 设置参数
	 * @return bool
	 */
	public function set_template_config($config) {
		global $InitPHP_G;
		if (!is_array($config)) return false;
		//模板主题实现
		if (isset($InitPHP_G['theme']) && !empty($InitPHP_G['theme'])) {
			$config['template_path'] = $config['template_path'] . '/' . $InitPHP_G['theme'];
			$config['template_c_path'] = $config['template_c_path'] . '/' . $InitPHP_G['theme'];
			if ($config['is_compile'] == true) $this->create_dir($config['template_c_path']); //创建主题文件夹
		}
		if (isset($config['template_path'])) $this->template_path = $config['template_path'];
		if (isset($config['template_c_path'])) $this->template_c_path = $config['template_c_path'];
		if (isset($config['template_type'])) $this->template_type = $config['template_type'];
		if (isset($config['template_c_type'])) $this->template_c_type = $config['template_c_type'];
		if (isset($config['template_tag_left'])) $this->template_tag_left = $config['template_tag_left'];
		if (isset($config['template_tag_right'])) $this->template_tag_right = $config['template_tag_right'];
		if (isset($config['is_compile'])) $this->is_compile = $config['is_compile'];
		$this->driver_config = $config['driver'];
		return true;
	}
	
	/**
	 * 模板编译-模板类入口函数
	 * 
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @return string
	 */
	public function template_run($file_name) {
		$this->check_path(); //检测模板目录和编译目录
		list($template_file_name, $compile_file_name) = $this->get_file_name($file_name);
		if ($this->is_compile == true) { //是否强制编译
			$str = $this->read_template($template_file_name);
			$str = $this->layout($str); //layout模板页面中加载模板页
			$str = $this->replace_tag($str);
			$str = $this->compile_version($str, $template_file_name);
			$this->compile_template($compile_file_name, $str);
		}
		return $compile_file_name;
	}
	
	/**
	 * 模板编译-读取静态模板
	 * 
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @return 
	 */
	private function read_template($template_file_name) {
		if (!file_exists($template_file_name)) exit($template_file_name. ' is not exist!');
		return @file_get_contents($template_file_name);
	}
	
	/**
	 * 模板编译-编译模板
	 * 
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @param  string $str 写入编译文件的数据
	 * @return 
	 */
	private function compile_template($compile_file_name, $str) {
		if (($path = dirname($compile_file_name)) !== $this->template_c_path) { //自动创建文件夹
			$this->create_dir($path); 
		}
		@file_put_contents($compile_file_name, $str);
	}
	
	/**
	 * 模板编译-通过传入的filename，获取要编译的静态页面和生成编译文件的文件名
	 * 
	 * @param  string $filename 文件名称，例如：test，不带文件.htm类型
	 * @return 
	 */
	private function get_file_name($file_name) {
		return array(
			$this->template_path .'/'. $file_name . '.' . $this->template_type, //组装模板文件路劲
			$this->template_c_path .'/'. $file_name . '.' . $this->template_c_type //模板编译路劲
		);
	}
	
	/**
	 * 模板编译-检测模板目录和编译目录是否可写
	 * 
	 * @return 
	 */
	private function check_path() {
		if (!is_dir($this->template_path) || !is_readable($this->template_path)) exit('template path is unread!');
		if (!is_dir($this->template_c_path) || !is_readable($this->template_c_path)) exit('compiled path is unread!');
		return true;
	}
	
	/**
	 * 模板编译-编译文件-头部版本信息
	 * 
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	private function compile_version($str, $template_file_name) {
		$version_str = '<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on ' .date('Y-m-d H:i:s');
		$version_str .= ', compiled from '. $template_file_name . ' */ ?>' . "\r\n";
		return $version_str . $str;
	}
	
	/**
	 * 模板编译-标签正则替换
	 * 
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	private function replace_tag($str) {
		$this->get_driver($this->driver_config);
		return self::$driver->init($str, $this->template_tag_left, $this->template_tag_right); //编译
	}
	
	/**
	 * 模板编译-layout在HTML模板中直接使用<!--{layout:user/version}-->就可以调用模板
	 * 
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	private function layout($str) {
		preg_match_all("/(".$this->template_tag_left."layout:)(.*)(".$this->template_tag_right.")/", $str, $matches);
		$matches[2] = array_unique($matches[2]); //重复值移除
		$matches[0] = array_unique($matches[0]);
		foreach ($matches[2] as $val) $this->template_run($val);
		foreach ($matches[0] as $k => $v) {
			$str = str_replace($v, $this->layout_path($matches[2][$k]), $str);
		}
		return $str; 
	}
	
	/**
	 * 模板编译-layout路径
	 * 
	 * @param  string $template_name 模板名称
	 * @return string
	 */
	private function layout_path($template_name) {
		return "<?php include('".$this->template_c_path.'/'.$template_name.'.'.$this->template_c_type."'); ?>";
	}
	
	/**
	 * 模板编译-获取不同
	 * 
	 * @param  string $template_name 模板名称
	 * @return string
	 */
	private function get_driver($driver) {
		$diver_path = 'driver/' . $driver . '.init.php';
		if (self::$driver === NULL) {
			require_once($diver_path);
			$class = $driver . 'Init';
			if (!class_exists($class)) exit('class' . $class . ' is not exist!');
			$init_class = new $class;
			self::$driver = $init_class;
		}
		return self::$driver;
	}
	
	/**
	 *	创建目录
	 *
	 * 	@param  string  $path   目录
	 *  @return 
	 */
	private function create_dir($path) {
		if (is_dir($path)) return false;
		$this->create_dir(dirname($path));
		@mkdir($path);
		@chmod($path, 0777);
		return true;
	}

}
?>

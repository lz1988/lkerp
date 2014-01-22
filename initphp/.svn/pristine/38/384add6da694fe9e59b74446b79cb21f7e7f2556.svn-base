<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 VIEW视图层核心文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
require_once("template.init.php");
class viewInit extends templateInit {

	public  $view = array(); //视图变量
	private $template_arr = array(); //视图存放器

	/**
	 * 模板-显示视图
	 *
	 * @return array
	 */
	public function display() {
		global $InitPHP_conf;
		if (is_array($this->view)) {
			if ($InitPHP_conf['isviewfilter']) $this->out_put($this->view);
			foreach ($this->view as $key => $val) {
				$$key = $val;
			}
		}
		$this->template_arr = $this->parse_template_arr($this->template_arr); //模板设置
		foreach ($this->template_arr as $file_name) {
			$complie_file_name = $this->template_run($file_name); //模板编译
			if (!file_exists($complie_file_name)) exit($complie_file_name. ' is not exist!');
			include_once($complie_file_name);
		}
	}

	/**
	 * 模板-设置模板变量
	 *
	 * @param  string  $key   KEY值
	 * @param  string  $value value值
	 * @return array
	 */
	public function assign($key, $value) {
		$this->view[$key] = $value;
	}

	public function mark($key_array){
		if(!is_array($key_array) || empty($key_array)) return 0;
		foreach($key_array as $k=>$v){
			$this->view[$k] = $v;
		}
	}

	/**
	 * 模板-设置模板
	 *
	 * @param  string  $template_name 模板名称
	 * @param  string  $type 类型，F-头模板，L-脚步模板
	 * @return array
	 */
	public function set_tpl($template_name, $type = '') {
		if ($type == 'F') {
			$this->template_arr['F'] = $template_name;
		} elseif ($type == 'L') {
			$this->template_arr['L'] = $template_name;
		} else {
			$this->template_arr[] = $template_name;
		}
	}

	/**
	 * 模板-处理视图存放器数组，分离头模板和脚模板顺序
	 *
	 * @param  array  $arr 视图存放器数组
	 * @return array
	 */
	private function parse_template_arr(array $arr) {
		$temp = $arr;
		unset($temp['F'], $temp['L']);
		if (isset($this->template_arr['F'])) { //头模板
			array_unshift($temp, $this->template_arr['F']);
		}
		if (isset($this->template_arr['L'])) {
			array_push($temp, $this->template_arr['L']);
		}
		return $temp;
	}

	/**
	 * 模板-模板变量输出过滤
	 *
	 * @param  array  $arr 视图存放器数组
	 * @return array
	 */
	public function out_put(&$value) {
		$value = (array) $value;
		foreach ($value as $key => $val) {
			if (is_array($val)) {
				self::out_put($value[$key]);
			} elseif (is_object($val)) {
				$value[$key] = $val;
			} else {
				if (function_exists('htmlspecialchars')) {
					//$value[$key] =  htmlspecialchars($val);
				} else {
					$value[$key] =  str_replace(array("&", '"', "'"), array("&amp;", "&quot;", "&#039;"), $val);
				}
			}
		}
	}

}
?>

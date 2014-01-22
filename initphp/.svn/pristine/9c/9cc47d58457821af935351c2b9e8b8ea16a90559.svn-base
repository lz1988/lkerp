<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 安全过滤类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class fliterInit extends validateInit {
	
	/**
	 * 安全过滤类-获取GET或者POST的参数值，经过过滤
	 * 
	 * @param  string|array $value 参数
	 * @param  bool         $isfliter 变量是否过滤
	 * @return string|array
	 */
	public function get_gp($value, $isfliter = true) {
		if (!is_array($value)) {
			if (isset($_GET[$value])) $temp = $_GET[$value];
			if (isset($_POST[$value])) $temp = $_POST[$value];
			$temp = ($isfliter === true) ? $this->fliter_escape($temp) : $temp;
			return $temp;
		} else {
			$temp = array();
			foreach ($value as $val) {
				if (isset($_GET[$val])) $temp[$val] = $_GET[$val];
				if (isset($_POST[$val])) $temp[$val] = $_POST[$val];
				$temp[$val] = ($isfliter === true) ? $this->fliter_escape($temp[$val]) : $temp[$val];
			}
			return $temp;
		}
	}
	
	/**
	 * 安全过滤类-全局变量过滤
	 * 
	 * @return
	 */
	public function fliter() {
		if (is_array($_SERVER)) {
			foreach ($_SERVER as $k => $v) {
				if (isset($_SERVER[$k])) {
					$_SERVER[$k] = str_replace(array('<','>','"',"'",'%3C','%3E','%22','%27','%3c','%3e'), '', $v);
				}
			}
		}
		unset($_ENV, $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS, $HTTP_ENV_VARS);
		self::fliter_slashes($_GET);
		self::fliter_slashes($_POST);
		self::fliter_slashes($_COOKIE);
		self::fliter_slashes($_FILES);
		self::fliter_slashes($_REQUEST);
		if ($GLOBALS['InitPHP_conf']['isuri']) $this->uri(); //是否开启URI访问模式
	}
	
	/**
	 * 安全过滤类-加反斜杠，放置SQL注入
	 * 
	 * @param  string $value 需要过滤的值
	 * @return string
	 */
	public static function fliter_slashes(&$value) {
		if (get_magic_quotes_gpc()) return false; //开启魔术变量
		$value = (array) $value;
		foreach ($value as $key => $val) {
			if (is_array($val)) {
				self::fliter_slashes($value[$key]);
			} else {
				$value[$key] = addslashes($val);
			}
		}
	}
	
	/**
	 * 安全过滤类-过滤javascript,css,iframes,object等不安全参数 过滤级别高
	 * 
	 * @param  string $value 需要过滤的值
	 * @return string
	 */
	public function fliter_script($value) {
		$value = preg_replace("/(javascript:)?on(click|load|key|mouse|error|abort|move|unload|change|dblclick|move|reset|resize|submit)/i","&111n\\2",$value);
		$value = preg_replace("/<script(.*?)>(.*?)<\/script>/si","",$value);
		$value = preg_replace("/<iframe(.*?)>(.*?)<\/iframe>/si","",$value);
		$value = preg_replace ("/<object.+<\/object>/iesU", '', $value);
		return $value;
	}
	
	/**
	 * 安全过滤类-过滤HTML标签
	 * 
	 * @param  string $value 需要过滤的值
	 * @return string
	 */
	public function fliter_html($value) {
		if (function_exists('htmlspecialchars')) return htmlspecialchars($value);
		return str_replace(array("&", '"', "'", "<", ">"), array("&amp;", "&quot;", "&#039;", "&lt;", "&gt;"), $value);
	}
	
	/**
	 * 安全过滤类-对进入的数据加下划线 防止SQL注入
	 * 
	 * @param  string $value 需要过滤的值
	 * @return string
	 */
	public function fliter_sql($value) {
		$sql = array("select", 'insert', "update", "delete", "\'", "\/\*", 
						"\.\.\/", "\.\/", "union", "into", "load_file", "outfile");
		$sql_re = array("","","","","","","","","","","","");
		return str_replace($sql, $sql_re, $value);
	}
	
	/**
	 * 安全过滤类-通用数据过滤
	 * 
	 * @param string $value 需要过滤的变量
	 * @return string|array
	 */
	public function fliter_escape($value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = self::fliter_str($v);
			}
		} else {
			$value = self::fliter_str($value);
		}
		return $value;
	}
	
	/**
	 * 安全过滤类-字符串过滤 过滤特殊有危害字符
	 * 
	 * @param  string $value 需要过滤的值
	 * @return string
	 */
	public function fliter_str($value) {
		$badstr = array("\0", "%00", "\r", '&', ' ', '"', "'", "<", ">", "   ", "%3C", "%3E");
		$newstr = array('', '', '', '&amp;', '&nbsp;', '&quot;', '&#39;', "&lt;", "&gt;", " &nbsp; ", "&lt;", "&gt;");
		$value  = str_replace($badstr, $newstr, $value);
		$value  = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $value);
		return $value;
	}
	
	/**
	 * 安全过滤类-返回函数
	 * 
	 * @param  string $value 需要过滤的值
	 * @return string
	 */
	public function str_out($value) {
		$badstr = array("<", ">", "%3C", "%3E");
		$newstr = array("&lt;", "&gt;", "&lt;", "&gt;");
		$value  = str_replace($newstr, $badstr, $value);
		return stripslashes($value); //下划线
	}
	
	/**
	 * 安全过滤类-检测请求来源，防止小偷和下载
	 * 
	 * @return string
	 */
	public function fliter_url() {
		if (preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) !== preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST']))
			return false;
		return true;
	}
}
?>

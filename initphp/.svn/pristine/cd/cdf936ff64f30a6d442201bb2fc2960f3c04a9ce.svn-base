<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 数据基础验证类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class validateInit extends requestInit {

	/**
	 *	数据基础验证-检测字符串长度 
	 *
	 * 	@param  string $value 需要验证的值
	 * 	@param  int    $min   字符串最小长度
	 * 	@param  int    $max   字符串最大长度
	 *  @return bool
	 */
	public function is_length($value, $min = 0, $max= 0) {
		$value = trim($value);
		if ($min != 0 && strlen($value) < $min) return false;
		if ($max != 0 && strlen($value) > $max) return false;
		return true; 
	}
	
	/**
	 *	数据基础验证-是否必须填写的参数
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_require($value) {
		return preg_match('/.+/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是空字符串
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_empty($value) {
		return (empty($value) || $value=="");
	}
	
	/**
	 *	数据基础验证-检测数组，数组为空时候也返回FALSH
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_arr($value) {
		if (!is_array($value) || empty($value)) return false;
		return true;
	}
	
	/**
	 *	数据基础验证-是否是Email 验证：xxx@qq.com
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_email($value) {
		return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是IP
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_ip($value) {
		return preg_match('/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是数字类型 
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_number($value) {
		return preg_match('/\d+$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是身份证
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_card($value){
		return preg_match("/^(\d{15}|\d{17}[\dx])$/i", $value);
	}
	
	/**
	 *	数据基础验证-是否是电话 验证：0571-xxxxxxxx
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_mobile($value) {
		return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是移动电话 验证：1385810XXXX
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_phone($value) {
		return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(13|15)\d{9}$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是URL 验证：http://www.easyphp.cc
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_url($value) {
		return preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是邮政编码 验证：311100
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_zip($value) {
		return preg_match('/^[1-9]\d{5}$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是QQ 
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_qq($value) {
		return preg_match('/^[1-9]\d{4,12}$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是英文字母
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_english($value) {
		return preg_match('/^[A-Za-z]+$/', trim($value));
	}
	
	/**
	 *	数据基础验证-是否是中文
	 *
	 * 	@param  string $value 需要验证的值
	 *  @return bool
	 */
	public function is_chinese($value) {
		return preg_match("/^([\xE4-\xE9][\x80-\xBF][\x80-\xBF])+$/", trim($value));
	}
	
}
?>

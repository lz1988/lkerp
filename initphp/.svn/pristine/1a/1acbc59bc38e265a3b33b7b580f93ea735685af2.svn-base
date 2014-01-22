<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 模板驱动-默认驱动
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class defaultInit {

	/**
	 * 模板驱动-默认的驱动
	 * 
	 * @param  string $str 模板文件数据
	 * @return string
	 */
	 public function init($str, $left, $right) {
	 	$pattern = array('/'.$left.'/', '/'.$right.'/');
		$replacement = array('<?php ', ' ?>');
		return preg_replace($pattern, $replacement, $str);
	 }
}

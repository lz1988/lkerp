<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 SERVICE核心文件
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:Dabing
 * $Dtime:2010-3-6
***********************************************************************************/
class serviceInit {

	/**
	 *	字段校验-用于进入数据库的字段映射
	 *
	 * 	@param  array   $field   可信任字段 array(array('field', 'int'))
	 * 	@param  array   $data    传入的参数
	 *  @return object
	 */
	public function parse_data($field, $data) {
		$field = (array) $field;
		$temp = array();
		foreach ($field as $val) {
			if (isset($data[$val[0]])) {
				if ($val[1] == 'int') {
					$data[$val[0]] = (int) $data[$val[0]];
				} elseif ($val[1] == 'obj') {
					$data[$val[0]] = serialize($data[$val[0]]);
				}
				$temp[$val[0]] = $data[$val[0]];
			}
		}
		return $temp;
	}

	/**
	 *	service特殊情况-数据返回组装器
	 *
	 * 	@param  int    $status   返回参数状态
	 * 	@param  string $msg      提示信息
	 * 	@param  string $data     传递的参数
	 *  @return object
	 */
	public function return_msg($status, $msg, $data = '') {
		return array($status, $msg, $data);
	}

	/**
	 *	类加载-获取Dao
	 *
	 *  @param  object  $class Dao类名称
	 *  @return
	 */
	public function dao($class) {
		global $InitPHP_conf;
		$tableName = $class;
		$class = $class . $InitPHP_conf['dao']['dao_postfix'];
		if (!InitPHP::import($class . '.php', $GLOBALS['InitPHP_G']['dao'])) return false;
		return InitPHP::loadclass($class,$tableName);
	}
}
?>

<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 数据库语句监控器
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class sqlcontrolInit {

	/**
	 * 数据库监控
	 */
	public function sqlstrain() {
		global $InitPHP_conf;
		echo "<br>sql->：";
		if (isset($InitPHP_conf['sqlcontrolarr']) && is_array($InitPHP_conf['sqlcontrolarr'])) {
			$i = 1;
			foreach ($InitPHP_conf['sqlcontrolarr'] as $k => $v) {
				echo $v."<br>";
				$i++;
			}
		}
		echo "<br>";
	}


}
?>
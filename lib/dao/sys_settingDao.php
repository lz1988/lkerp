<?php
/*
 * Created on 2012-5-7
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class sys_settingDao extends D{

	/*查出已有的配置*/
	public function get_allsettings($is_conallset){

		if($is_conallset)	$sqlstr = 'or bid="sys"';
		$sql = 'select remer,value from sys_setting where bid="'.$_SESSION['uid'].'" '.$sqlstr;
		return $this->D->query_array($sql);
	}
 }
?>

<?php
/*
 * Created on 2012-7-23
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


 if($detail == 'list'){

	$stypemu = array(
		'country-s-l'=>'国家名称：',
		'code2-s-l'=>'国家代码：',
	);

	$InitPHP_conf['pageval'] = 20;
	$datalist 	= $this->S->dao('shipping_code')->D->get_list($sqlstr,'','country','country,code2');
	$tablewidth	= '800';

	$displayarr 				= array();
	$displayarr['country']		= array('showname'=>'国家名称','width'=>'100');
	$displayarr['code2']		= array('showname'=>'国家代码','width'=>'80');



	$temp = 'pub_list';

 }
?>

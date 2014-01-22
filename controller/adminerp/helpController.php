<?php

/**
 * Created on 2012-12-6 by Hanson
 * @title 系统帮助文档
 *
 */

/*** 帮助文档首页 ***/
if($detail == 'index'){

	$this->V->set_tpl('help/index');
	$this->V->mark(array('title'=>'系统帮助'));
	display();
}

/*** 帮助文档顶部 ***/
elseif($detail == 'top'){
	$cho = '<div style="border-right:1px solid #bdbdbd;border-bottom:1px solid #bdbdbd;margin:10px 10px 0 10px;height:50px;line-height:50px;padding-left:10px;background:#ececec"><h1>系统帮助文档</h1></div>';
	$cho.= '<div style="margin:10px 10px 0 10px;"><hr color="#eeeeee"></div>';
	echo $cho;
}

/*** 左导航 ***/
elseif($detail == 'left'){

	$this->V->set_tpl('help/left');
	display();
}


?>

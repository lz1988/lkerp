<?php
/*
 * Created on 2013-4-2
 *
 * xml相关处理类
 */

 class xmlmodInit {

	/**
	 * @title	加载XML内容
	 * @type 	0为字符串，1为文件
	 * @str 	XML字符串或文件路径
	 *
	 */
	public function readxml($str,$type){
    	if($type==1){
     		$xmlstr = simplexml_load_file($str);//simplexml_load_file()作用是：将一个XML文档装载入一个对象中。
    	}else{
     		$xmlstr = simplexml_load_string($str);
    	}

    	return $xmlstr;
  	}

	/**
	 * @title	将XML内容转换为数组
	 *
	 */
  	public function xmltoarray($str,$type = '0'){
    	$xmlstr = $this->readxml($str,$type);
    	$arrstr = array();

    	$str = serialize($xmlstr); //serialize()  产生一个可存储的值的表示
    	$str = str_replace('O:16:"SimpleXMLElement"', 'a', $str);
    	$arrstr = unserialize($str); //unserialize()  从已存储的表示中创建 PHP 的值
    	return $arrstr;
   }



 }

?>

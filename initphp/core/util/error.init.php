<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 Error机制，错误友好输出机制
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
class errorInit {
	
	private $error_data = array(); //error容器
	private $error_type = array('html', 'text', 'json', 'xml', 'array'); //error类型数组
	
	/**
	 *	Error机制 添加一个error
	 *
	 * 	@param  string   $error_message  错误信息
	 *  @return object
	 */
	public function add_error($error_message) {
		$this->error_data[] = $error_message;
	}
	
	/**
	 *	Error机制 输出一个error
	 *
	 * 	@param  string   $error_message  错误信息
	 * 	@param  string   $error_type     错误类型
	 *  @return object
	 */
	public function send_error($error_message, $error_type = 'json') {
		$this->error_data[] = $error_message;
		$error_type = strtolower($error_type);
		if (!in_array($error_type, $this->error_type)) $error_type = 'json';
		$this->display($error_type);
	}
	
	/**
	 *	Error机制 私有函数，error输出
	 *
	 * 	@param  string   $error_type     错误类型
	 *  @return object
	 */
	private function display($error_type) {
		if ($error_type == 'text') {
			$error = implode("\r\t", $this->error_data);
			exit($error);
		} elseif ($error_type == 'json') {
			exit(json_encode($this->error_data));
		} elseif ($error_type == 'xml') {
			$xml = '<?xml version="1.0" encoding="utf-8"?>';
			$xml .= '<return>';
				foreach ($this->error_data as $v) {
					$xml .= '<error>' .$v. '</error>';
				}
			$xml .= '</return>';
		 	exit($xml);
		} elseif ($error_type == 'array') {
			exit(var_export($this->error_data));
		} elseif ($error_type == 'html') {
			global $InitPHP_conf;
			$error = $this->error_data;
			if ($InitPHP_conf['error']['template']) {
				if (!file_exists($InitPHP_conf['error']['template'])) exit ('error template is not exist');
				@include $InitPHP_conf['error']['template'];
			} else {
				echo 'please set error template in initphp.conf.php';
			}
			//扩展HTML错误输出
			exit;
		}
	}
}
?>
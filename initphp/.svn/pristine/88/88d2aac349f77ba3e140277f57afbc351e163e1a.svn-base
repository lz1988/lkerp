<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 控制器模块
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
require_once("request.init.php");
require_once("validate.init.php");
require_once("fliter.init.php");
class controllerInit extends fliterInit{

	public $v; //视图模型对象

	/**
	 * 初始化控制器，
	 */
	public function __construct() {
		$this->fliter();
		$this->set_token(); //生成全局TOKEN值，防止CRsf攻击
	}

	/**
	 *	控制器 AJAX脚本输出
	 *
	 * 	@param  int     $status  0:错误信息|1:正确信息
	 * 	@param  string  $status  显示的信息
	 * 	@param  array   $data    传输的信息
	 * 	@param  array   $type    返回数据类型，json|xml|eval
	 *  @return object
	 */
	public function ajax_return($status, $message = '', $data = array(), $type = 'json') {
		$return_data = array($status, $message, $data);
		$type = strtolower($type);
		if ($type == 'json') {
			exit(json_encode($return_data));
		} elseif ($type == 'xml') {
			$xml = '<?xml version="1.0" encoding="utf-8"?>';
			$xml .= '<return>';
				$xml .= '<status>' .$status. '</status>';
				$xml .= '<message>' .$message. '</message>';
				$xml .= '<data>' .serialize($data). '</data>';
			$xml .= '</return>';
		 	exit($xml);
		} elseif ($type == 'eval') {
			exit($return_data);
		} else {

		}
	}

	/**
	 *	控制器 重定向
	 *
	 * 	@param  string  $url   跳转的URL路劲
	 * 	@param  int     $time  多少秒后跳转
	 *  @return
	 */
	public function redirect($url, $time = 0) {
		if (!headers_sent()) {
			if ($time === 0) header("Location: ".$url);
			header("refresh:" . $time . ";url=" .$url. "");
		} else {
			exit("<meta http-equiv='Refresh' content='" . $time . ";URL=" .$url. "'>");
		}
	}

	/**
	 * 对接收的参数数组过滤前后空字符
	 * 可过滤二维数组
	 */
	public function trim_value(&$value){
		if(is_array($value)){
			array_walk($value,array(controllerInit,'trim_value_arr'));
		}else{
			$value = trim($value);
		}
	}

	public function trim_value_arr(&$value){
		$value = trim($value);
	}

	/**
	 *	类加载 获取SERVICE
	 *
	 *  @param  object  $class Service类名称
	 *  @return
	 */
	public function service($class) {
		global $InitPHP_conf;
		$class = $class . $InitPHP_conf['service']['service_postfix'];
		if (!InitPHP::import($class . '.php', $GLOBALS['InitPHP_G']['service'])) return false;
		return InitPHP::loadclass($class);
	}

	/**
	 *	类加载-获取Dao
	 *
	 *  @param  object  $class Dao类名称
	 *  @return
	 */
	public function dao($class) {
		global $InitPHP_conf;
		$class = $class . $InitPHP_conf['dao']['dao_postfix'];
		if (!InitPHP::import($class . '.php', $GLOBALS['InitPHP_G']['dao'])) return false;
		return InitPHP::loadclass($class);
	}

	/**
	 *	类加载-获取全局TOKEN，防止CSRF攻击
	 *  @return
	 */
	public function get_token() {
		return $_COOKIE['init_token'];
	}

	/**
	 *	类加载-检测token值
	 *  @return
	 */
	public function check_token($ispost = true) {
		if ($ispost && !$this->is_post()) return false;
		if ($this->get_gp('init_token') !== $this->get_token()) return false;
		return true;
	}

	/**
	 *	类加载-设置全局TOKEN，防止CSRF攻击
	 *
	 *  @return
	 */
	private function set_token() {
		if (!$_COOKIE['init_token']) {
			$str = substr(md5(time(). $this->get_useragent()), 5, 8);
			setcookie("init_token", $str);
			$_COOKIE['init_token'] = $str;
		}
	}

	/**
	 * 网站跳转提醒页面
	 * @param string $msg--默认的提示语
	 */
	public function sendmsg($msg='对不起，你没有该权限！',$back){
		$this->redirect('index.php?action=msg&detail=pronounce&content='.urlencode($msg).'&back='.urlencode($back));
		exit();
	}

	/**
	 * 添加成功跳转回列表页
	 */
	 public function success($msg,$url){
	 	echo "<script>alert('".$msg."');window.location='".$url."';</script>";
	 }

	 /**
	  *AJAX弹出窗提示,多用于对删除操作。
	  */
	 public function ajaxmsg($mod,$msg = '',$doerror){
	 	if(empty($doerror)){
		 	if($mod==0){$msg = empty($msg)?'对不起，你没有该权限！':$msg;exit($msg);}
		 	elseif($mod==1){$msg = empty($msg)?'删除成功':$msg;echo $msg;}
	 	}elseif($doerror == 1){
	 		echo $doerror;
	 	}
	 }

	/**
	 * 二维数组排序，冒泡排序法
	 *
	 * @param array $a 需要排序的数组
	 * @param string $sort 作为排序参考的键名
	 * @param string $d 默认空即是升序排序，加参数即为倒序
	 */
	 public function array_sort($a,$sort,$d=''){

		$num=count($a);
	    if(!$d){
	        for($i=0;$i<$num;$i++){
	            for($j=0;$j<$num-1;$j++){
	                if($a[$j][$sort] > $a[$j+1][$sort]){
	                    foreach ($a[$j] as $key=>$temp){
	                        $t=$a[$j+1][$key];
	                        $a[$j+1][$key]=$a[$j][$key];
	                        $a[$j][$key]=$t;
	                    }
	                }
	            }
	        }
	    }
	    else{
	        for($i=0;$i<$num;$i++){
	            for($j=0;$j<$num-1;$j++){
	                if($a[$j][$sort] < $a[$j+1][$sort]){
	                    foreach ($a[$j] as $key=>$temp){
	                        $t=$a[$j+1][$key];
	                        $a[$j+1][$key]=$a[$j][$key];
	                        $a[$j][$key]=$t;
	                    }
	                }
	            }
	        }
	    }
	    return $a;
	 }
}
?>

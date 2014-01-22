<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 MYSQL基础类
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
require_once("dbbase.init.php");
class mysqlInit extends dbbaseInit{


	private static $link_id   = NULL; //数据连接ID
	private static $link_temp = NULL;//主从分离时候存放SQL链接
	private $_linkid = NULL; //当前操作的linkid


	/**
	 * MYSQL连接-初始化
	 *
	 * @param  array $db_config 数据库参数
	 * @return
	 */
	public function init($db_config, $db_type) {
		if ($db_type == 0) { //单数据库服务器
			if (self::$link_id !== NULL) return false;
			self::$link_id = $this->connect(
				$db_config[0]['localhost'],
				$db_config[0]['username'],
				$db_config[0]['password'],
				$db_config[0]['database'],
				$db_config[0]['charset'],
				$db_config[0]['pconnect']
			);
			self::$link_temp 	= self::$link_id;
		} elseif($db_type == 1) { //读写分离-2台服务器状态
			//从服务器-随机
			if (self::$link_temp !== NULL) return false;
			$key = floor(mt_rand(1,(count($db_config) - 1)));
			self::$link_temp = $this->connect(
				$db_config[$key]['localhost'],
				$db_config[$key]['username'],
				$db_config[$key]['password'],
				$db_config[$key]['database'],
				$db_config[$key]['charset'],
				$db_config[$key]['pconnect']
			);

			//主服务器
			if (self::$link_id !== NULL) return false;
			self::$link_id = $this->connect(
				$db_config[0]['localhost'],
				$db_config[0]['username'],
				$db_config[0]['password'],
				$db_config[0]['database'],
				$db_config[0]['charset'],
				$db_config[0]['pconnect']
			);
		} elseif ($db_type == 2) { //随机
			$key = floor(mt_rand(0,(count($db_config) - 1)));
			if (self::$link_id !== NULL) return false;
			self::$link_id = $this->connect(
				$db_config[$key]['localhost'],
				$db_config[$key]['username'],
				$db_config[$key]['password'],
				$db_config[$key]['database'],
				$db_config[$key]['charset'],
				$db_config[$key]['pconnect']
			);
			self::$link_temp = self::$link_id;
		} else {
			//其他的SQL均衡方案
		}
	}

	/**
	 * MYSQL连接器
	 *
	 * @param  string $host sql服务器
	 * @param  string $user 数据库用户名
	 * @param  string $password 数据库登录密码
	 * @param  string $database 数据库
	 * @param  string $charset 编码
	 * @param  string $pconnect 是否持久链接
	 * @return obj
	 */
	public function connect($host, $user, $password, $database, $charset = 'utf8', $pconnect = 0) {
		$link_id = ($pconnect == 0) ? mysql_connect($host, $user, $password) : mysql_pconnect($host, $user, $password);
		if (!$link_id) exit('mysql connect error!');
		mysql_query('SET NAMES ' . $charset, $link_id);
		if (!mysql_select_db($database, $link_id)) exit('database is not exist!');
		return $link_id;
	}

	/**
	 * SQL执行器
	 *
	 * @param  string $sql SQL语句
	 * @return obj
	 */
	public function query($sql) {
		$this->_linkid = ($this->is_insert($sql)) ? self::$link_temp : self::$link_id; //读写分离方案
		//数据库语句监控
		if($_SESSION['issqlcontrol'] ==1){
			echo 'sql->'.$sql.'<br>';
		}
		return mysql_query($sql, $this->_linkid);
	}

	/**
	 * SQL执行器,直接返回数组,带分页
	 *
	 * @param  $back 执行方式
	 * @return array
	 */
	public function query_array($sql,$back='fetch_array') {
		global $InitPHP_conf;

		$this->_linkid = ($this->is_insert($sql)) ? self::$link_temp : self::$link_id; //读写分离方案

		//效率低下，考虑将来达千万级数据量，先屏掉。
		//$query = mysql_query($sql, $this->_linkid);
		//$InitPHP_conf['sums'] = mysql_num_rows($query);

       
		if($InitPHP_conf['pageval']!=''){//有分页参数

			$num_sums = $this->fetch_assoc(mysql_query("select count(*) as sums from ($sql) as sumsql",$this->_linkid));
			$InitPHP_conf['sums'] = $num_sums['sums'];
			$offest = $InitPHP_conf['pageval']*($_GET['page']-1);
			$limit = $this->build_limit($offest, $InitPHP_conf['pageval']);
			$sql = $sql.' '.$limit;

			//数据库语句监控
			if($_SESSION['issqlcontrol'] ==1){
				echo 'sql->'.$sql.'<br>';
			}
			$query = mysql_query($sql, $this->_linkid);
		}else{
			if($_SESSION['issqlcontrol'] ==1){echo 'sql->'.$sql.'<br>';}
			$query = mysql_query($sql, $this->_linkid);
		}
		while($row = $this->$back($query)){
				$temp[] = $row;
		}
		return $temp;
	}

	/**
	 * SQL分析器
	 *
	 * @param  string $sql SQL语句
	 * @return bool
	 */
	private function is_insert($sql) {
		$sql = trim($sql);
		$sql_temp = strtoupper(substr($sql, 0, 6));
		if ($sql_temp == 'SELECT') return true;
		return false;
	}

	/**
	 * 结果集中的行数
	 *
	 * @param $result 结果集
	 * @return array
	 */
	public function result($result, $num=1) {
		return mysql_result($result, $num);
	}

	/**
	 * 从结果集中取得一行作为关联数组
	 *
	 * @param $result 结果集
	 * @return array
	 */
	public function fetch_assoc($result) {
		return mysql_fetch_assoc($result);
	}

	public function fetch_array($result) {
		return mysql_fetch_array($result);
	}

	/**
	 * 从结果集中取得列信息并作为对象返回
	 *
	 * @param  $result 结果集
	 * @return array
	 */
	public function fetch_fields($result) {
		return mysql_fetch_field($result);
	}

	/**
	 * 结果集中的行数
	 *
	 * @param $result 结果集
	 * @return int
	 */
	public function num_rows($result) {
		return mysql_num_rows($result);
	}

	/**
	 * 结果集中的字段数量
	 *
	 * @param $result 结果集
	 * @return int
	 */
	public function num_fields($result) {
		return mysql_num_fields($result);
	}

	/**
	 * 释放结果内存
	 *
	 * @param obj $result 需要释放的对象
	 */
	public function free_result($result) {
		return mysql_free_result($result);
	}

	/**
	 * 获取上一INSERT的ID值
	 *
	 * @return Int
	 */
	public function insert_id() {
		return mysql_insert_id($this->_linkid);
	}

	/**
	 * 前一次操作影响的记录数
	 *
	 * @return int
	 */
	public function affected_rows() {
		return mysql_affected_rows($this->_linkid);
	}

	/**
	 * 关闭连接
	 */
	public function close() {
		if (self::$link_id !== NULL) @mysql_close(self::$link_id);
		if (self::$link_temp !== NULL) @mysql_close(self::$link_temp);
		self::$link_id = self::$link_temp = NULL;
		return true;
	}

	/**
	 * 错误信息
	 *
	 * @return string
	 */
	public function error() {
		return mysql_error($this->_linkid);
	}
}
?>

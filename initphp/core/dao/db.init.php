<?php
if (!defined('IS_INITPHP')) exit('Access Denied!');
/*********************************************************************************
 * InitPHP 1.0 SQL-操作-常用SQL操作
 *-------------------------------------------------------------------------------
 * 版权所有: CopyRight By InitPHP Team
 * 您可以自由使用该源码，但是在使用过程中，请保留作者信息。尊重他人劳动成果就是尊重自己
 *-------------------------------------------------------------------------------
 * $Author:DaBing
 * $Dtime:2010-3-6
***********************************************************************************/
require_once("db/mysql.init.php");
class dbInit extends mysqlInit {
	public $table_name;
	public function __construct($tableName){

		$this->table_name = strtolower($tableName);
	}

	/**
	 * SQL操作-插入一条数据
	 *
	 * @param array  $data array('key值'=>'值')
	 * @param string $table_name 表名
	 * @return id
	 */
	public function insert($data) {
		if (!is_array($data) || empty($data)) return 0;
		$data = $this->build_insert($data);
		$sql = sprintf("INSERT INTO %s %s", $this->table_name, $data);
        //die($sql);
        $result = $this->query($sql);
		if (!$result) return 0;
		return $this->insert_id();
	}

	/**
	 * SQL操作-插入多条数据
	 *
	 * @param array $field 字段
	 * @param array $data  对应的值，array(array('test1'),array('test2'))
	 * @param string $table_name 表名
	 * @return id
	 */
	public function insert_more($field, $data) {
		if (!is_array($data) || empty($data)) return false;
		if (!is_array($field) || empty($field)) return false;
		$sql = $this->build_insertmore($field, $data);
		$sql = sprintf("INSERT INTO %s %s", $this->table_name, $sql);
		return $this->query($sql);
	}

	/**
	 *重写过的，用SQL更新
	 */
	 public function update_sql($sqlstr,$data){
	 	if (!is_array($data) || empty($data)) return false;
	 	$data = $this->build_update($data);
	 	$sql = sprintf("UPDATE %s %s %s", $this->table_name, $data, $sqlstr);
        //die($sql);
		return $this->query($sql);
	 }

	/**
	 * SQL操作-根据字段更新数据
	 *
	 * @param  array  $data 参数
	 * @param  array  $field 字段参数
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function update_by_field($field,$data) {
		if (!is_array($data) || empty($data)) return false;
		if (!is_array($field) || empty($field)) return false;
		$data = $this->build_update($data);
		$field = $this->build_where($field);
		$sql = sprintf("UPDATE %s %s %s", $this->table_name, $data, $field);
        //die($sql);
		return $this->query($sql);
	}

	/**
	 * 全能的更新函数
	 * $sqlstr_field 支持数组和SQL语句
	 * $data_arr	支持直接写更新语句，数组，数量与语句混写三种方式。
	 *
	 */
	public function update($sqlstr_arr,$data_arr){
		if(empty($sqlstr_arr) || empty($data_arr)) return false;
		if(is_array($sqlstr_arr)){
			$where = $this->build_where($sqlstr_arr);
		}else{
			$where = ' where 1 '.$sqlstr_arr;
		}

		if(is_array($data_arr)){
			if($data_arr['extra_sql']){
				if(count($data_arr) == 1){
					$data = 'set '.$data_arr['extra_sql'];
				}else{
					$extra_sql = ','.$data_arr['extra_sql'];
					unset($data_arr['extra_sql']);
					$data  = $this->build_update($data_arr);
					$data .= $extra_sql;
				}
			}else{
				$data  = $this->build_update($data_arr);
			}
		}else{
			$data  = 'set '.$data_arr;
		}

		$sql = sprintf("UPDATE %s %s %s", $this->table_name, $data, $where);
        //die($sql);
		return $this->query($sql);

	}

	/**
	 * SQL操作-删除数据
	 *
	 * @param  $sqlstr_arr 删除条件，可以是and条件或数组
	 * @return bool
	 */
	public function delete($sqlstr_arr) {
		if (is_array($sqlstr_arr)){
			$sqlstr_arr	= $this->build_where($sqlstr_arr);
		}else{
			$sqlstr_arr = ' where 1 '.$sqlstr_arr;
		}
        //die($sqlstr_arr);
		$sql = sprintf("DELETE FROM %s %s", $this->table_name, $sqlstr_arr);
		return $this->query($sql);
	}

	/**
	 * SQL操作-通过条件语句删除数据
	 *
	 * @param  array  $field 条件数组
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function delete_by_field($field) {
		if (!is_array($field) || empty($field)) return false;
		$where = $this->build_where($field);
		$sql = sprintf("DELETE FROM %s %s", $this->table_name, $where);
		return $this->query($sql);
	}


	/**
	 * SQL操作-通过条件语句获取一条信息
	 *
	 * @param  array  $field 条件数组 array('username' => 'username')
	 * @param  string $table_name 表名
	 * @return bool
	 */
	public function get_one_by_field($field,$cloumn='*') {
		if (!is_array($field) || empty($field)) return array();
		$where = $this->build_where($field);
		$sql = sprintf("SELECT %s FROM %s %s LIMIT 1",$cloumn, $this->table_name, $where);
        //echo '<pre>';echo $sql;
		$result = $this->query($sql);
		if (!$result) return false;
		return $this->fetch_assoc($result);
	}

	/**
	 * SQL操作-获取单条信息-sql语句方式
	 *
	 * @param  string $sql 数据库语句
	 * @return array
	 */
	public function get_one_sql($sql) {
		$sql = trim($sql . ' ' .$this->build_limit(1));
		$result = $this->query($sql);
		if (!$result) return false;
		return $this->fetch_assoc($result);
	}

	/**
	 * 重写-select-读一条记录
	 * @param array $result 返回记录
	 * @param string $column 需要查询的字段
	 */
	public function select($column,$sqlstr){
		if($column == 'all'){$column = '*';}
		$sql = sprintf("SELECT %s FROM %s WHERE %s LIMIT 1",$column, $this->table_name, $sqlstr);
		$result = $this->query($sql);
        //echo $sql;
		if (!$result) return false;
		return $this->fetch_array($result);
	}

	/**
	 * 查找一条-第一参数支持自写条件与数组
	 * @param arr || string $arr_str
	 * @param string $colum
	 *
	 */
	public function get_one($sqlstr_arr,$cloumn = '*'){

		if(empty($sqlstr_arr) || empty($cloumn)) return false;
		if(is_array($sqlstr_arr)){
			$where = $this->build_where($sqlstr_arr);
		}else{
			$where = ' where 1 '.$sqlstr_arr;
		}

		$sql = sprintf("SELECT %s FROM %s %s LIMIT 1",$cloumn, $this->table_name, $where);
        //print_r($sql);echo '<br>';die();
		$result = $this->query($sql);
		if (!$result) return false;
		$backdata =  $this->fetch_assoc($result);

		if(count($backdata) == 1) {
			return $backdata[$cloumn];
		}else{
			return $backdata;
		}
	}


	/**
	 * SQL操作-获取全部数据
	 *
	 * @param string $table_name 表名
	 * @param array  $field 条件语句
	 * @param int    $key_id KEY值
	 * @param string $sort 排序键
	 */
	public function get_all($field = array(), $id_key = 'id', $sort = 'DESC',$cloumn = '*') {

		$orders = '';
		if(!empty($id_key) && !empty($sort)){
			$orders = "ORDER BY {$id_key} {$sort}";
		}
		$where = $this->build_where($field);
		$sql = sprintf("SELECT %s FROM %s %s %s",$cloumn, $this->table_name, $where, $orders);
        //die($sql);
		$result = $this->query($sql);
		if (!$result) return false;
		$temp = array();
		while ($row = $this->fetch_array($result)) {
			$temp[] = $row;
		}
		return $temp;
	}

	/**
	 * SQL操作-获取所有数据
	 *
	 * @param string $sql SQL语句
	 * @return array
	 */
	public function get_all_sql($sql) {
		$sql = trim($sql);
		$result = $this->query($sql);
		if (!$result) return false;
		while ($row = $this->fetch_assoc($result)) {
			$temp[] = $row;
		}
		return $temp;
	}


	/**
	 * SQL操作-获取数据总数
	 *
	 * @param  string $table_name 表名
	 * @param  array  $field 条件语句
	 * @return int
	 */
	public function get_count($field = array()) {
		$where = $this->build_where($field);
		$sql = sprintf("SELECT COUNT(*) as count FROM %s %s LIMIT 1", $this->table_name, $where);
        //echo($sql);
		$result = $this->query($sql);
		$result =  $this->fetch_assoc($result);
		return $result['count'];
	}


	/**
	 * 重写-删除，通过SQL语句
	 */
	public function delete_sql($sqlstr){
		$sql = sprintf('delete from %s %s',$this->table_name, $sqlstr);
		return $this->query($sql);
	}


	/**
	 * 取得当前条件执行后返回的条数
	 */
	public function get_count_sql($sqlstr){
		$sql = sprintf("SELECT COUNT(*) as count FROM %s where 1=1 %s LIMIT 1", $this->table_name, $sqlstr);
		$result = $this->query($sql);
		$result =  $this->fetch_assoc($result);
		return $result['count'];
	}

	/**
	 * 取得列表数据，适用于单表。
	 */
	public function get_list($sqlstr='', $groups='',$orders='',$column='*') {

		global $InitPHP_conf;
		if($InitPHP_conf['pageval']){$num = $InitPHP_conf['pageval'];}else{$num=15;$InitPHP_conf['pageval'] = $num;};
		$offest = $num*($_GET['page']-1);
		$limit = $this->build_limit($offest, $num);

		$groups = empty($groups)?'':"GROUP BY {$groups}";
		$orders = empty($orders)?'ORDER BY ID DESC':"ORDER BY {$orders}";



		$sql = sprintf("SELECT %s FROM %s WHERE 1=1 %s %s %s %s",$column, $this->table_name, $sqlstr, $groups,$orders,$limit);
        //print_r($sql);
		$result = $this->query($sql);
		if (!$result) return false;
		$temp = array();
		while ($row = $this->fetch_array($result)) {
			$temp[] = $row;
		}
		$InitPHP_conf['sums'] = $this->get_count_sql($sqlstr);
		return $temp;
	}

	/*重写新增的，不会自动分页。取得所有数据*/
	public function get_allstr($sqlstr='', $groups='',$orders='',$column='*'){

		$groups = empty($groups)?'':"GROUP BY {$groups}";
		$orders = empty($orders)?'ORDER BY ID DESC':"ORDER BY {$orders}";

		$sql = sprintf("SELECT %s FROM %s WHERE 1=1 %s %s %s ",$column, $this->table_name, $sqlstr, $groups,$orders);
        //echo $sql;
		$result = $this->query($sql);
		if (!$result) return false;
		$temp = array();
		while ($row = $this->fetch_array($result)) {
			$temp[] = $row;
		}
		return $temp;
	}

}

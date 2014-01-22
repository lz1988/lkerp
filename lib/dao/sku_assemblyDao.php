<?php
/*
 * Created on 2012-3-19
 * by wall
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class sku_assemblyDao extends D{
	/* create by wall
	 * $pid 组装产品id
	 * select 组装此类产品最后方案类型
	 * */
 	public function get_max_type_by_pid($pid) {
 		$sql = 'select max(type) as type from sku_assembly where pid='.$pid.' group by pid';
 		return $this->D->get_one_sql($sql);
 	}

 	/* create by wall
	 * $pid 组装产品id
	 * select 组装此类产品所有方案类型
	 * */
 	public function get_all_type_by_pid($pid) {
 		$sql = 'select distinct type as type from sku_assembly where pid='.$pid;
 		return $this->D->get_one_sql($sql);
 	}

 	/* create by wall
 	 * $pid 组装产品id
 	 * $type 组装方案
	 * select 组装此类产品的所有子产品数量和ID
 	 * */
 	public function select_by_pid($pid, $type) {
 		$sql = 'select child_pid,quantity from sku_assembly where pid='.$pid.' and type='.$type;
 		return $this->D->query_array($sql);
 	}

	/* create by wall
 	 * $child_pid 组装子产品id
 	 * select: 组装方案编号
 	 * */
 	public function select_assembleid_by_childpid($child_pid) {
 		$sql = 'select assembleid from sku_assembly where child_pid='.$child_pid;
 	 	return $this->D->query_array($sql);
 	}

 	/* create by wall
 	 * $child_pid 组装产品id
 	 * select: 组装方案编号
 	 * */
 	public function select_assembleid_by_pid($pid) {
 		$sql = 'select assembleid from sku_assembly where pid='.$pid;
 	 	return $this->D->query_array($sql);
 	}

 	/* create by wall
 	 * $pid 组装产品id
 	 * $child_pid 组装子产品id
 	 * quantity 组装子产品数量
 	 * select: 组装方案类型
 	 * */
 	public function get_type_by_one($pid, $child_pid, $quantity) {
 		$sql = 'select type from sku_assembly where pid='.$pid.' and child_pid='.$child_pid.' and quantity='.$quantity;
 	 	return $this->D->query_array($sql);
 	}

	/* create by wall
 	 * $assembleid 组装产品方案编号
	 * select 组装此类产品的所有子产品数量和ID
 	 * */
 	public function select_by_assembleid($assembleid) {
 		$sql = 'select pid,child_pid,quantity from sku_assembly where assembleid="'.$assembleid.'"';
 		return $this->D->query_array($sql);
 	}

	/* create by wall
	 * $assembleid 组装产品方案编号
	 * select 此方案的组装产品id
	 * */
 	public function get_pid_by_assembleid($assembleid) {
 		$sql = 'select distinct pid from sku_assembly where assembleid="'.$assembleid.'"';
 		return $this->D->query_array($sql);
 	}

 	/* create by wall
	 * $pid  此方案的组装产品id
	 * $type 产品方案
	 * select 组装产品方案编号
	 * */
 	public function get_assembleid_by_pid($pid, $type) {
 		$sql = 'select assembleid from sku_assembly where pid='.$pid.' and type='.$type;
 		return $this->D->get_one_sql($sql);
 	}

 	/* create by wall
 	 * $pid  组装产品id
 	 * $type 组装方案
 	 * select 该产品下该组装方案包含子产品数
 	 * */
 	public function get_count_type_by_one($pid, $type) {
 		$sql = 'select count(*) as sums from sku_assembly where pid='.$pid.' and type='.$type;
 		return $this->D->get_one_sql($sql);
 	}

 	/* create by wall on 2012-3-26
 	 * $sqlstr 查询条件
 	 * return  组装编号（不重复）
 	 * */
 	public function get_list_in_assembleid($sqlstr) {
 		$sql = ' select distinct assembleid,sa.cuser as cuser from sku_assembly as sa ';
 		$sql .= ' left join product as p on sa.pid=p.pid ';
 		$sql .= ' left join product as cp on sa.child_pid=cp.pid ';
 		$sql .= ' where 1=1 '.$sqlstr.' order by sa.id asc ';
 		return $this->D->query_array($sql);
 	}

 	/**
 	 * create by hanson on 2012-09-07
 	 * @title 用于物料调自动将组装的SKU的原SKU提取出来
 	 */
 	public function get_sonlist($sqlstr){
 		$sql = 'select p.sku,p.product_name,s.quantity,s.assembleid,s.pid,s.child_pid,s.quantity from sku_assembly s left join product p on p.pid=s.child_pid where 1 '.$sqlstr;
 		return $this->D->query_array($sql);
 	}

 }
?>

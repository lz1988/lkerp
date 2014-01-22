<?php
/*
 * Created on 2011-10-11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class sku_aliasDao extends D{

 	/*通过修改sku别名
	 * $id 			sku别名表ID
	 * $pid			修改后的产品ID
	 * $pro_sku		修改后的产品sku
	 * $sku_code	修改后的SKU别名
	 * $sold_way	修改后的别名使用组
	 * */
	public function update_sku_skucode($id, $pid, $pro_sku, $sku_code, $sold_way) {
		return $this->D->update_by_field(array('id' => $id),array('pid'=>$pid,'pro_sku'=>$pro_sku,'sku_code'=>$sku_code, 'sold_way'=>$sold_way));
	}

	/*增加一条sku别名
	 * $pid 			产品的pid
	 * $pro_sku 		产品的sku
	 * $sku_code 		产品的sku别名
	 * $sold_way		销售渠道
	 * */
	public function insert_sku_alias($pid, $pro_sku, $sku_code, $sold_way = 'ERP') {
		return $this->D->insert(array('pid' => $pid, 'pro_sku' => $pro_sku, 'sku_code' => $sku_code, 'sold_way' => $sold_way));
	}

	/*增加一条sku别名为自己的记录
	 * $pid 			产品的pid
	 * $pro_sku 		产品的sku
	 * $sold_way		销售渠道
	 * */
	public function insert_sku_alias_own($pid, $pro_sku) {
		return $this->insert_sku_alias($pid, $pro_sku, $pro_sku);
	}

	/*修改pid为$pid的所有SKU
	 * $pid 		产品的pid
	 * $pro_sku 	产品的sku
	 * */
	public function update_all_sku($pid, $pro_sku) {
		return $this->D->update_by_field(array('pid' => $pid),array('pro_sku'=>$pro_sku));
	}

	/*删除$pid的所有sku*/
	public function delete_by_pid($pid) {
		return $this->D->delete_by_field(array('pid'=>$pid));
	}

	/*通过sku别名精确获取SKU的原名
	 * $sku_code sku别名
	 * return 返回SKU字符串，不存在则返回''
	 * */
	 public function get_sku_by_code($sku_code) {
	 	$temp = $this->D->get_one_by_field(array('sku_code'=>$sku_code),'pro_sku');
	 	return $temp['pro_sku'];
	 }

	/*
	 * 查找指定条件所有SKU别名
	 * $sqlstr	查询条件
	 * RETURN 	返回查询结果
	 * */
	 public function select_sku_code($sqlstr) {
		$sql = 'select a.pid,pro_sku,sku_code,product_name from sku_alias as a ';
		$sql .= ' left join product as b on a.pid=b.pid ';
		$sql .= ' where pro_sku<>sku_code '.$sqlstr.' order by pro_sku desc';
		return $this->D->query_array($sql);
	 }
	 
	 /*
	  * 查找条件下产品id
	  * */
	 public function select_pid_group_by_pid($sqlstr) {
	 	$sql = 'select pid from sku_alias where 1 '.$sqlstr.' group by pid order by pid desc';
	 	return $this->D->query_array($sql); 
	 } 	
 }
?>

<?php
/*
 * Created on 2011-10-11
 * Create by wall
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class product_customDao extends D{

	/* create by wall
	 * 根据pid,uid查找表中是否存在*/
	public function get_one_by_uid_pid($uid, $pid) {
		$sql = 'select id from product_custom where uid='.$uid.' and pid='.$pid;
		return $this->D->get_one_sql($sql);
	}

	/* create by wall
	 * 根据用户ID查找所有定制产品id
	 * 并根据产品id获取所有有操作订单的产品
	 * 按创建时间倒序
	 * $uid 用户id
	 * */
	public function get_pid_by_uid($uid) {
		$sql = 'select pc.pid from product_custom as pc left join process as p on pc.pid=p.pid where uid='.$uid.' and pc.pid=p.pid group by pc.pid order by pc.id desc';
		return $this->D->query_array($sql);
	}

	/* create by wall
	 * 根据查询条件，查找所有定制产品名称和sku
	 * $sqlstr 查询条件
	 * */
	public function get_product_by_join($sqlstr) {
		$sql = 'select pc.id,pc.pid from product_custom as pc left join product as p on pc.pid=p.pid where 1 '.$sqlstr.' order by pc.id desc';
		return $this->D->query_array($sql);
	}
 }
?>

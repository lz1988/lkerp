<?php

class product_costDao extends D{

	/*加强平均更新costp(cost1),cost2，只用于组装时才用，其它情况已更新到financeServcie方法*/
	public function updatecost($pid,$cost1,$cost2,$mdate){
		$sql = 'replace into product_cost (pid,cost1,cost2,coin_code,mdate)values("'.$pid.'","'.$cost1.'","'.$cost2.'","CNY","'.$mdate.'")';
		return $this->D->query($sql);
	}

	/*查上一次采购价格*/
	public function get_precost($pid){
		$sql		= 'select price from process where property="采购单" and pid='.$pid.' order by id desc limit 2';
		$back		= $this->D->query_array($sql);

		$preprice	= $back['0']['price']?$back['0']['price']:0;
		return number_format($preprice,2);
	}

	/*查上一次进仓价格*/
	public function get_last_info_by_pid($sqlstr){
		$sql = 	' select p.pid,p.sku,p.price,p.coin_code,p.stage_rate from process p inner join (' .
				' select max(id) id from process where property="进仓单" and protype="采购" and active="1" and input="1" '.$sqlstr.' group by pid ' .
				' ) t on t.id=p.id order by p.pid desc';

		$result = $this->D->query($sql);
		if(!$result) return false;
		$temp	= array();
		while($row = $this->D->fetch_array($result)){
			$temp[] = $row;
		}
		return $temp;

	}

}
?>

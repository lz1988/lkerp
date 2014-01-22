<?php
/**
 * Create on 2013-01-28
 * by Hanson
 */

class orders_refundDao extends D{

	public function get_list($sqlstr){

		$sql = 'select f.*,p.sku,p.product_name,s.account_name from orders_refund f left join product p on p.pid=f.pid left join sold_account s on s.id=f.account where 1 '.$sqlstr;
		return $this->D->query_array($sql);

	}

	/**
	 * create on 2013-02-05 by hanson
	 * @title 查找退款的汇总用嵌入销售账号汇总表
	 *
	 */
	public function get_sumbyAccount($sqlstr){

		$sql = 'SELECT SUM( total / usd_rate *100 )as refund_total,t.account_name FROM  `orders_refund` o  left join sold_account t on t.id=o.account left join orders_detail on orders_detail.address = t.account_name where 1 '.$sqlstr.' GROUP BY account';
        //echo $sql;
		return $this->D->query_array($sql);
	}
    
    /**
	 * create on 2013-07-22 by Jerry
	 * @title 查找退款的汇总用嵌入销售账号汇总表
	 *
	 */
	public function get_sumbyAccount_currency($sqlstr){

		$sql = 'SELECT SUM( total)as refund_total,t.account_name FROM  `orders_refund` o  left join sold_account t on t.id=o.account  left join orders_detail on orders_detail.address = t.account_name where 1 '.$sqlstr.' GROUP BY account';
		return $this->D->query_array($sql);
	}
    
    /**
     *@title 获取销售账号sku
     *@author Jerry 
     *@create on 2014-1-8 
     */ 
    public function get_sumbyaccount_sku($sqlstr){
        $sql = 'SELECT address,pid,shiphouse,checktime,quantity FROM orders_detail o LEFT JOIN sold_account s ON o.address=s.account_name WHERE 1 AND statu="1"  '.$sqlstr.'';
        //echo $sql."<br/><br/>";
        return $this->D->get_all_sql($sql); 
    }
}
?>
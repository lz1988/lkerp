<?php
/*
 * Created on 2012-10-8
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class orders_detailDao extends D{

    /*明细报表用*/
    public function report_sold_detaillist($sqlstr){
        $sql = 'SELECT orders_detail.*,product_cost.coin_code,product_cost.cost1 as cost,sold_account.account_code FROM orders_detail LEFT JOIN product_cost ON product_cost.pid=orders_detail.pid left join sold_account on sold_account.account_name=orders_detail.address WHERE 1=1 and statu = "1" '.$sqlstr;
        $sql.= 'order by 3rd_part_id desc';
        return $this->D->query_array($sql);
    }

    /*销售账号汇总表用*/
	public function get_report_sumary($sqlstr){
		$sql = 'SELECT s.id as account_id,s.account_code,address,sum(price/usd_rate*100) as price,sum(shipprice/usd_rate*100) as shipprice,sum(shipfee/usd_rate*100) as shipfee,sum(amazonfee/usd_rate*100) as amazonfee,sum(otherfee/usd_rate*100) as otherfee,sum(paypalfee/usd_rate*100) as paypalfee,sum(ebayfee/usd_rate*100) as ebayfee,sum(cost1*quantity) as cost1 from orders_detail o left join sold_account s on o.address=s.account_name where 1 '.$sqlstr.' ';
		$sql.= 'group by address';
		return $this->D->query_array($sql);
	}
    
    /*销售账号汇总表用,显示原币别*/
	public function get_report_sumary_currency($sqlstr){
		$sql = 'SELECT s.id as account_id,s.account_code,address,sum(price) as price,sum(shipprice) as shipprice,sum(shipfee) as shipfee,sum(amazonfee) as amazonfee,sum(otherfee) as otherfee,sum(paypalfee) as paypalfee,sum(ebayfee) as ebayfee,sum(cost1*quantity) as cost1,o.currency from orders_detail o left join sold_account s on o.address=s.account_name where 1 '.$sqlstr.' ';
		$sql.= 'group by address';
		return $this->D->query_array($sql);
	}

	/*最近12个月的销售数据*/
	public function get_near_summarysales($sqlstr){
		$sql = 'select sum(price/usd_rate*100) as prices,concat(year(date),"-",month(date)) as dates from orders_detail where 1 '.$sqlstr.'  GROUP BY YEAR( DATE ) , MONTH( DATE )';
		return $this->D->query_array($sql);
	}
    
    /*统计订单sku总重量，根据相同仓库，审核期间*/
    public function get_warehouse_shipping($sqlstr){
        $sql = 'SELECT SUM(product.shipping_weight*orders_detail.quantity) as shipping_weight,shiphouse,checktime FROM orders_detail LEFT JOIN product ON product.pid=orders_detail.pid ';
        $sql .= ' WHERE orders_detail.statu ="1" AND checktime<>"" AND shiphouse<>"" and '.$sqlstr.' GROUP BY shiphouse,checktime';
    //echo $sql;
        return $this->D->get_all_sql($sql);
    }


 }
?>

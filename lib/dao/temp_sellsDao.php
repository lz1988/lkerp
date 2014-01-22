<?php
/*
 * Created on 2012-12-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class temp_sellsDao extends D{

	public function get_moddatalist($thismark){

		$sql = 'select p.product_name as erp_pname,s2.sku_code as jin_sku,s.pro_sku as erp_sku, temp.datetime,temp.order_id as deal_id,temp.3rd_part_id,temp.sku as deal_sku,sum(temp.quantity) as quantity,sum(product_sales) as price,sum(shipping_credits) as shipprice,sum(promotional_rebates) as shipfee,(sum(gift_wrap_credits)+sum(sales_tax_collected)+sum(selling_fees)+sum(fba_fees)+sum(other_transaction_fees)+sum(other)) as amazonfee,temp.currency,temp.ship_country,temp.shipment_date from temp_sells temp ';
		$sql.= ' LEFT JOIN sku_alias as s on s.sku_code=temp.sku ';//匹配出ERP-SKU
		$sql.= ' LEFT JOIN sku_alias as s2 on s2.pro_sku=s.pro_sku and s2.sold_way="金碟" ';//匹配金碟SKU
		$sql.= ' LEFT JOIN product as p on p.sku=s.pro_sku ';//匹配中文名称
		$sql.= ' where temp.mark="'.$thismark.'"';
		$sql.= ' group by temp.datetime,temp.order_id,temp.sku ';
		return $this->D->query_array($sql);
	}

 }
?>

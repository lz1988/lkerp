<?php

	/**
	 * by hanson 2012-09-24
	 *
	 */
	class temp_soldmodDao extends D{

		/*取得整理后的明细表*/
		public function get_moddatalist($mark){

			$commom_where = '(payment_type="Amazon fees" OR payment_type="Other" OR payment_type="Promo rebates" or payment_type="Commissions Amazon" or payment_type="Autres" or payment_type="Rabais promotionnels")';
			$commom_select= 'mark,date, order_id AS deal_id, 3rd_part_id, sku AS deal_sku, product_title, quantity, currency,';

			$sql	.= 'SELECT p.product_name as erp_pname,s2.sku_code as jin_sku,s.pro_sku as erp_sku,temp.mark,temp.date,temp.deal_id,temp.3rd_part_id,temp.deal_sku,temp.product_title as deal_pname,temp.quantity,temp.currency,sum(price) as price,sum(shipprice) as shipprice,sum(shipfee) as shipfee, sum(amazonfee) as amazonfee,ship_country,shipment_date FROM(';

			$sql	.= ' SELECT '.$commom_select.' amount as price, 0 as shipprice, 0 as shipfee, 0 as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE payment_type="product charges" or payment_type="Frais produit" ';/*查产品收入*/
			$sql	.= ' UNION ALL';
			$sql	.= ' SELECT '.$commom_select.' 0 as price, amount as shipprice, 0 as shipfee, 0 as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE '.$commom_where.' AND (payment_detail like "Shipping%" or payment_detail like "%Expédition%" or payment_detail like "%Expédié%") AND amount > 0 ';/*查amazon运费收入*/
			$sql	.= ' UNION ALL';
			$sql	.= ' SELECT '.$commom_select.' 0 as price, 0 as shipprice, amount as shipfee, 0 as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE '.$commom_where.' AND (payment_detail like "Shipping%" or payment_detail like "%Expédition%" or payment_detail like "%Expédié%") AND amount < 0 ';/*查amazon运费支出*/
			$sql	.= ' UNION ALL';
			$sql	.= ' SELECT '.$commom_select.' 0 as price, 0 as shipprice, 0 as shipfee, amount as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE '.$commom_where.' AND payment_detail not like "Shipping%" and payment_detail not like "%Expédition%" and payment_detail not like "%Expédié%" ';/*查amazon其它费用，允许正负相抵冲*/

			$sql	.= ')temp ';
			$sql	.= 'LEFT JOIN sku_alias as s on s.sku_code=temp.deal_sku ';//匹配出ERP-SKU
			$sql	.= 'LEFT JOIN sku_alias as s2 on s2.pro_sku=s.pro_sku and s2.sold_way="金碟" ';//匹配金碟SKU
			$sql	.= 'LEFT JOIN product as p on p.sku=s.pro_sku ';//匹配中文名称
			$sql	.= 'where temp.mark="'.$mark.'" group by temp.deal_id,temp.deal_sku';//以平台单号，SKU合并
			return $this->D->query_array($sql);

		}
        
        /*米悠取得整理后的明细表*/
		public function miuget_moddatalist($mark){

			$commom_where = '(payment_type="亚马逊所收费用" OR payment_type="其他" OR payment_type="促销返点") ';
			$commom_select= 'mark,date, order_id AS deal_id, 3rd_part_id, sku AS deal_sku, product_title, quantity, currency,';

			$sql	.= 'SELECT p.product_name as erp_pname,s2.sku_code as jin_sku,s.pro_sku as erp_sku,temp.mark,temp.date,temp.deal_id,temp.3rd_part_id,temp.deal_sku,temp.product_title as deal_pname,temp.quantity,temp.currency,sum(price) as price,sum(shipprice) as shipprice,sum(shipfee) as shipfee, sum(amazonfee) as amazonfee,ship_country,shipment_date FROM(';

			$sql	.= ' SELECT '.$commom_select.' amount as price, 0 as shipprice, 0 as shipfee, 0 as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE payment_type="商品价格"';/*查产品收入*/
			$sql	.= ' UNION ALL';
			$sql	.= ' SELECT '.$commom_select.' 0 as price, amount as shipprice, 0 as shipfee, 0 as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE '.$commom_where.' AND payment_detail like "运费%" AND amount > 0 ';/*查amazon运费收入*/
			$sql	.= ' UNION ALL';
			$sql	.= ' SELECT '.$commom_select.' 0 as price, 0 as shipprice, amount as shipfee, 0 as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE '.$commom_where.' AND payment_detail like "运费%" AND amount < 0 ';/*查amazon运费支出*/
			$sql	.= ' UNION ALL';
			$sql	.= ' SELECT '.$commom_select.' 0 as price, 0 as shipprice, 0 as shipfee, amount as amazonfee, ship_country, shipment_date FROM temp_soldmod WHERE '.$commom_where.' AND payment_detail not like "运费%" ';/*查amazon其它费用，允许正负相抵冲*/

			$sql	.= ')temp ';
			$sql	.= 'LEFT JOIN sku_alias as s on s.sku_code=temp.deal_sku ';//匹配出ERP-SKU
			$sql	.= 'LEFT JOIN sku_alias as s2 on s2.pro_sku=s.pro_sku and s2.sold_way="金碟" ';//匹配金碟SKU
			$sql	.= 'LEFT JOIN product as p on p.sku=s.pro_sku ';//匹配中文名称
			$sql	.= 'where temp.mark="'.$mark.'" group by temp.deal_id,temp.deal_sku';//以平台单号，SKU合并
			return $this->D->query_array($sql);

		}





	}
?>

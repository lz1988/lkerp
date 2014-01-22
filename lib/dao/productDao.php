<?php
class productDao extends D{
	/*增加图片。取得所有产品列表，匹配条件，需要有类别，排列按问题数从高到低。*/
	public function getProductList($sqlstr,$orders){
		$sql = 'select o.cost1,o.cost2,o.coin_code,c.cat_name,p.pid,p.sku,p.product_name,p.conditionerp,p.create_user,p.cdate,p.shipping_weight,m.images,l.label_id,sku_assembly.assembleid,sku_shipping.shipping,sku_shipping.eid,GROUP_CONCAT(CONCAT(esse.name,":",sku_shipping.shipping)) as shipping from product as p
                left join product_images as m on (p.pid=m.pid)
                left join product_label as l on (p.pid=l.product_id and l.create_user=\''.$_SESSION['eng_name'].'\')
                left join category  as c on (p.cat_id=c.cat_id)
                left join product_cost as o on p.pid=o.pid
                left join sku_assembly on sku_assembly.pid=p.pid
                left join sku_shipping on sku_shipping.pid=p.pid
                left join esse on esse.id=sku_shipping.eid
                where 1=1 '.$sqlstr.' group by sku '.$orders;
		return $this->D->query_array($sql,'fetch_assoc');
	}

	/*取得所有产品列表，匹配条件，需要有类别，排列按问题数从高到低。*/
	public function getProductList2($sqlstr,$orders){
		$sql = 'select count(g.id) as num,c.cat_name,p.* from product as p
                left join product_guestbook as g on (p.pid=g.pid)
                left join category  as c on (p.cat_id=c.cat_id)
                where 1=1 '.$sqlstr.'
                group by p.pid '.$orders;
		return $this->D->query_array($sql,'fetch_assoc');
	}

	/*根据传过来的ID取得一条产品信息*/
	public function getProductByID($pid){
		$sql = 'select * from product p
                left join category c on p.cat_id=c.cat_id
                where p.pid='.$pid.' limit 1';
		return $this->D->query_array($sql,'fetch_assoc');
	}

	/*导出产品带成本价*/
	public function getout_produtctlist(){
		$sql = 'select c.cost2,c.coin_code,sku,shipping_weight,box_shipping_weight,product_dimensions,box_product_dimensions,product_name from product p
                left join product_cost c on p.pid=c.pid';
		return $this->D->query_array($sql,'fetch_assoc');
	}

	/* create by wall
	 * $pid 需要查找的产品id
	 * return 产品名称和产品sku
     */
	public function get_product_by_id($pid) {
		$sql = 'select pid,sku,product_name from product where pid='.$pid;
		return $this->D->get_one_sql($sql);
	}

	/* create by wall
	 * $sku 需要查找的产品的sku
	 * return 产品id,产品名称
	 */
	public function get_product_by_sku($sku) {
		$sql = 'select pid,product_name,shipping_weight from product where sku="'.$sku.'"';
		return $this->D->get_one_sql($sql);
	}

	/* create on 2012-04-26
	 * by wall
	 * @param $selectattr 查找项目集
	 * @param $sqlstr 查找条件集
	 */
	public function select_by_attr_str($selectattr, $sqlstr) {
		$sql = 'select m.images,'.$selectattr.' from product as p
                left join product_images as m on (p.pid=m.pid)
                left join product_cost as pc on p.pid=pc.pid
                left join esse as e on  SUBSTRING(SUBSTRING_INDEX(p.manufacturer,",",1),2)=e.id
                left join sku_shipping on sku_shipping.pid=p.pid
                left join esse on esse.id=sku_shipping.eid
                where 1 '.$sqlstr.' group by sku';
                //die($sql);
		return $this->D->query_array($sql);
	}
}
?>

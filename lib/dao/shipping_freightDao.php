
<?php
/**
 * Create on 2013-03-05
 * by Hanson
 */

class shipping_freightDao extends D{

	/*查看现在运费管理*/
	public function get_datalist($sqlstr){

		$sql = 'select s.*,e.s_name,a.nation,a.province,a.city,a2.nation as nation2,a2.province as province2,a2.city as city2 from shipping_freight as s left join shipping as e on e.id=s.shipping_id left join shipping_area a on a.id=s.from left join shipping_area a2 on a2.id=s.to '.$sqlstr;
		return $this->D->query_array($sql);
	}
    
    
    public function get_cost($sqlstr,$field){
        $sql    = 'SELECT '.$field.' FROM shipping_freight AS s LEFT JOIN shipping AS e ON e.id=s.shipping_id JOIN shipping_cost ON shipping_cost.shipping_id=s.shipping_id';
        $sql    .= $sqlstr;
        return $this->D->query_array($sql);
    }
}
?>
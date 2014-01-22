<?php
/**
 *@title 代码与国家映射表
 *
 */
class shipping_encodeDao extends D{

    //获取代码与国家列表
    public function getchipping_encode($sqlstr = ''){
        $sql = 'SELECT shipping_encode.id as id,shipping.s_name,shipping_encode.code,shipping_area.nation,shipping_area.province,shipping_area.city,shipping_encode.area_id,shipping_encode.shipping_id FROM shipping  JOIN shipping_encode ON shipping.id=shipping_encode.shipping_id LEFT JOIN shipping_area ON shipping_area.id=shipping_encode.area_id where 1=1 ';
        $sql .= $sqlstr;
        return $this->D->query_array($sql);
    }

	/** 查某个发货方式，某个代码下的所有国家 **/
	public function getAreabyCode($code,$shipping_id){
		$sql = 'select s.nation,s.province,s.city from shipping_encode c left join shipping_area s on c.area_id=s.id where c.code="'.$code.'" and c.shipping_id='.$shipping_id;
		return $this->D->query_array($sql);
	}
}
?>
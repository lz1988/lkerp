<?php

class shipping_fareDao extends D{

	/*中国发货到其他国家的运费数据读取*/
	public function farelist($weight,$a_code1,$a_code2,$a_code3){
		$sql = 'select shipping_com,type,price,cal_type from shipping_fare where min_weight<'.$weight.' and max_weight>='.$weight.' and (area_code="'.$a_code1.'" or area_code="'.$a_code2.'" or area_code="'.$a_code3.'")';
		return $this->D->get_all_sql($sql);
	}

	public function faresignle($weight,$code,$type = ''){

		if(empty($type)){
			$sqlstr = ' and area_code="'.$code.'"';
		}else{
			$sqlstr = ' and area_code="'.$code.'" and type="'.$type.'"';
		}

		$sql = 'select shipping_com,type,price,cal_type from shipping_fare where min_weight<'.$weight.' and max_weight>='.$weight.$sqlstr;
		return $this->D->get_one_sql($sql);
	}


	/*美国Amazon_us发货到美国*/
	public function amazon_us($weight,$country,$stockware){
		$sql = 'select * from shipping_fare where min_weight<'.$weight.' and max_weight>='.$weight.' and shipping_com="'.$stockware.'" and area_code="'.$country.'"';
		return $this->D->get_all_sql($sql);
	}
}
?>
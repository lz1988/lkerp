<?php

class exchange_rateDao extends D{
	/*
	 * create on 2012-05-11
	 * by wall
	 * param &code 查询币别代码
	 * */
	public function get_by_code($code) {
		$sql = 'select id from exchange_rate where isnew="1" and code="'.$code.'"';
		return $this->D->get_one_sql($sql);
	}

	/**
	 * create on 2012-08-15
	 * by wall
	 * param $code 币别
	 * parame $stage_rate 期号
	 * return 目标币种目标期号的最新一次记录
	 * */
	public function get_by_code_and_stage_rate($code, $stage_rate) {
		$sql = 'select id,rate from exchange_rate where code="'.$code.'" and stage_rate="'.$stage_rate.'" order by id desc';
		return $this->D->get_one_sql($sql);
	}

	/**
	 * create on 2012-08-24
	 * by wall
	 * @param $id 汇率id
	 * @return 目标id汇率信息
	 * */
	public function get_by_id($id) {
		$sql = 'select * from exchange_rate where id='.$id;
		return $this->D->get_one_sql($sql);
	}


	/**
	 * create on 2013-01-28
	 * by hanson
	 * @return array 系统币别一维数组
	 */
	public function get_sys_coincode(){
		$sql	= 'select * from exchange_rate group by code ';
		$back	= $this->D->query_array($sql);
		$coinArr= array();

		foreach($back as $val){
			$coinArr[] = $val['code'];
		}

		return $coinArr;
	}

}


?>
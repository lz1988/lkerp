<?
class shipping_codeDao extends D{
	/*
	 * create on 2012-05-22
	 * by wall
	 * 通过国家名称获取国家编码
	 * */
	public function get_code_by_country($country) {
		$sql = 'select code2 from shipping_code where country="'.$country.'"';
		return $this->D->get_one_sql($sql);
	}
}

?>

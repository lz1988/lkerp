<?php
class sold_wayDao extends D{
	/*取得所有销售渠道列表*/
	public function getSoldWayList(){
		$sql = 'SELECT * FROM sold_way';
		return $this->D->query_array($sql,'fetch_assoc');
	}
	
	/**
	 * create on 2012-08-24
	 * by wall
	 * @param $id 销售渠道id
	 * @return 销售渠道名称
	 * */
	public function get_wayname_by_id($id) {
		$sql = 'select wayname from sold_way where id='.$id;
		return $this->D->get_one_sql($sql);
	}
}
?>

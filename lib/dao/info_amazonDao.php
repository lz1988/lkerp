<?php
/*
 * Created on 2012-06-04
 * by wall
 * 卖家注册MWS的信息表
 */
 class info_amazonDao extends D{
	/*
	 * create on 2012-06-04
	 * by wall
	 * 根据id获取注册的MWS信息
	 * */
 	public function get_one_by_id($id) {
 		$sql = 'select * from info_amazon where id='.$id;
 		return $this->D->get_one_sql($sql);
 	}

 	/*
 	 * create on 2012-06-04
 	 * by wall
 	 * 根据条件查询注册信息列表
 	 * */
 	public function get_all_list($sqlstr) {
 		$sql = 'select a.*,e.name as housename,s.wayname as ia_sold_way from info_amazon as  a left join esse e on e.id=a.ia_houseid left join sold_way as s on s.id=a.ia_sold_way where 1 '.$sqlstr;
 		return $this->D->query_array($sql);
 	}

 	/*
 	 * create on 2012-06-12
 	 * by wall
 	 * @param $id 帐号id
 	 * 获取指定id帐号信息的销售渠道
 	 * */
 	public function get_soldway_by_id($id) {
 		$sql = 'select ia_sold_way from info_amazon where id='.$id;
 		return $this->D->get_one_sql($sql);
 	}
 }
?>

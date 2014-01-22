<?
class shipping_mapDao extends D{
	/*
	 * create on 2012-05-21
	 * by wall
	 * 发货方式映射列表
	 * */
	public function get_all_list($sqlstr) {
		$sql = 'select sm.id,sm_name,sm_user,sm_date,s_name,sm_type from shipping_map as sm ';
		$sql .= ' left join shipping as s on sm.sid=s.id ';
		$sql .= ' where 1 '.$sqlstr;
		return $this->D->query_array($sql);
	}
	
	/*
	 * create on 2012-05-21
	 * by wall
	 * 获取指定id发货方式映射信息
	 * */
	public function get_one_by_id($id) {
		$sql = 'select * from shipping_map where id='.$id;
		return $this->D->get_one_sql($sql);
	}
	
	/*
	 * create on 2012-05-22
	 * by wall
	 * 获取已经存在的发货方式类型
	 * */
	public function get_type_list($type) {
		$sql = 'select sm_type from shipping_map where sm_type like "%'.$type.'%" group by sm_type';
		return $this->D->query_array($sql);
	}
}

?>

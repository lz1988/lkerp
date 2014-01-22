<?php


class categoryDao extends D{


	/*取得产品类别，用于类别管理*/
	public function category_list($sqlstr){

		$sql = 'select c.parent_id,c.cat_id,c.cat_name,c.keywords,c.cat_desc,f.cat_name as fname from category c left join category f on c.parent_id=f.cat_id where 1 '.$sqlstr.' order by c.sort_order asc,c.cat_id desc';
		return $this->D->query_array($sql);
	}


	/*取得有效的所有类别列表，用于产品编辑与新增的类别选择*/
	public function category_treelist(){

		return $this->D->get_all(array('is_active'=>1),'sort_order','ASC');
	}

	/*取得根类别*/
	public function categorylist(){
		$sql = 'select * from category where parent_id=0 and is_active=1 order by sort_order';
		return $this->D->query_array($sql,'fetch_assoc');
	}

	/*取得子类别*/
	public function category_childrenlist($rcat_id){
		$sql = 'select cat_id, cat_name from category where parent_id='.$rcat_id.' and is_active=1 order by sort_order';
		return $this->D->query_array($sql,'fetch_assoc');
	}

	/* create on 2012-04-26
	 * by wall
	 * $name 需要查找的类型名称
	 * return 类型id
	 * */
	public function get_category_by_name($name) {
		$sql = 'select cat_id from category where cat_name="'.$name.'"';
		return $this->D->get_one_sql($sql);
	}
}
?>

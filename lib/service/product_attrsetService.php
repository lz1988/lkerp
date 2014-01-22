<?php

class product_attrsetService extends S{


	/**
	 * 取得属性集列表
	 * @return arrray
	 */
	public function get_attrset(){

		return	$this->S->dao('product_attribute_set')->D->get_all('','','','attr_set_id,attr_set_name');

	}

	/**
	 * 根据某属性集，将集里的ID转换为Name
	 * @reutrn input+name;
	 */
	public function get_everyattr($set_array=''){

		$attr = $this->S->dao('product_attribute');
		$attrlist = $attr->D->get_all('','attr_code','asc','attr_id,attr_name');

		$attr_checkbox = '';
		foreach($attrlist as $r){
			if($set_array) {$checked = in_array($r['attr_id'],$set_array)?' checked':'';}
			$attr_checkbox.="<input type='checkbox' name='attr_id[]' value =".$r['attr_id'].$checked.">".$r['attr_name']."&nbsp;";
		}
		return $attr_checkbox;
	}
}
?>

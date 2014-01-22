<?php
/*
 * Created on 2011-10-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class product_attributeService extends S{
	
	/*处理属性的新增和修改*/
	/* 
	public function product_attributeProcess($attr_code, $attr_name, $attr_input_type, $attr_values){
		$pa = $this->S->dao('product_attribute');
		if($pa->D->get_one_by_field(array('attr_code'=>$attr_code))){
			 return $pa->D->update_by_field(array('attr_code'=>$attr_code), array('attr_name'=>$attr_name, 'attr_input_type'=>$attr_input_type, 'attr_values'=>$attr_values));
		}else{
			return $pa->D->insert(array('attr_code'=>$attr_code, 'attr_name'=>$attr_name, 'attr_input_type'=>$attr_input_type, 'attr_values'=>$attr_values));
		}
	}
	*/
 }
?>

<?php
/*
 * Created on 2013-3-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class shipping_costDao extends D{

	/** 将导入的表格数据还原 **/
 	public function showcosttable($codetoArr, $shipping_id){

		$TempSumstr = '';
		$WhenColstr = '';


 		/*循环内容处理*/
 		for($i=0; $i<count($codetoArr); $i++){
			$code		= $codetoArr[$i];
 			$TempSumstr.= ',sum('.$code.') as '.$code;
 			$WhenColstr.= ',case code when "'.$code.'" then cost else 0 end as '.$code;
 		}

 		$sql = 'select ctype,min_weight,max_weight '.$TempSumstr.' from(
					select ctype,min_weight,max_weight '.$WhenColstr.' from
					(
						SELECT c.ctype, c.cost, c.min_weight, c.max_weight, s.code
						FROM shipping_cost c
						LEFT JOIN shipping_encode s ON c.to = s.area_id  where c.shipping_id='.$shipping_id.' group by min_weight,max_weight,s.code
					)tmp
				)temp group by min_weight,max_weight';

		return $this->D->query_array($sql);
 	}

 }
?>

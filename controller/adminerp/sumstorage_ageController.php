<?php
/**
 * @title 账龄分析汇总表
 * @author Jerry  
 * @create on 2013-03-20 
 */ 
 
 if ($detail == 'list') {
    
    
    $coin_code	=  'CNY';//金额默认转换为USD
   	/*查询条件处理*/
	
	$whouse			= $this->C->service('warehouse')->get_whouse('houseid','name','id','id',$houseid);/*获得仓库*/

	/*点击搜索才出内容*/


		//$InitPHP_conf['pageval']	= 15;
		$process 					= $this->S->dao('process');
		$exchange					= $this->C->service('exchange_rate');
        if($houseid){
            $sqlstr.= ' and temp.wid='.$houseid;
            $datalist = $process->get_storge_age_bystocks($sqlstr,2);
            $bannerstr 	= '<button onclick=window.location="index.php?action=sumstorage_age&detail=outport_bydays&houseid='.$houseid.'">导出数据</button>';
        }
		/*数据处理*/
		for($i = 0; $i <count($datalist); $i++){

			$_three = $datalist[$i]['three'] + $datalist[$i]['four'];
			$_two	= $_three + $datalist[$i]['two'];
			$_one	= $_two + $datalist[$i]['one'];

			$datalist[$i]['stock'] = $datalist[$i]['one'] + $datalist[$i]['two'] + $datalist[$i]['three'] + $datalist[$i]['four'] - $datalist[$i]['outs'];

			/*数量显示*/
			if(($datalist[$i]['four'] - $datalist[$i]['outs']) < 0){
				$datalist[$i]['four'] = 0;
				if(($_three - $datalist[$i]['outs']) < 0){
					$datalist[$i]['three'] = 0;
					if(($_two - $datalist[$i]['outs']) < 0){
						$datalist[$i]['two'] = 0;
						$datalist[$i]['one'] = $_one - $datalist[$i]['outs'];
					}else{
						$datalist[$i]['two'] = $_two - $datalist[$i]['outs'];
					}
				}else{
					$datalist[$i]['three'] = $_three - $datalist[$i]['outs'];
				}
			}else{
				$datalist[$i]['four'] -= $datalist[$i]['outs'];
			}
            //echo '<pre>';
            //echo $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']);
			/*金额处理*/
            $data['one_price'] 	+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']);
			$data['two_price'] 	+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['two']*$datalist[$i]['cost2']);
			$data['three_price']+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['three']*$datalist[$i]['cost2']);
			$data['four_price']	+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['four']*$datalist[$i]['cost2']);
			/*$datalist[$i]['one_price'] 	= $datalist[$i]['one'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
			$datalist[$i]['two_price'] 	= $datalist[$i]['two'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['two']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
			$datalist[$i]['three_price']= $datalist[$i]['three']? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['three']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
			$datalist[$i]['four_price']	= $datalist[$i]['four'] ? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['four']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
            */
            //$datalist[$i]['one_price']  += $datalist[$i]['one_price'];
            $data['one'] += $datalist[$i]['one'];
            //$datalist[$i]['two_price']  += $datalist[$i]['two_price'];
            $data['two'] += $datalist[$i]['two'];
            //$datalist[$i]['three_price'] += $datalist[$i]['three_price'];
            $data['three'] += $datalist[$i]['three'];
            //$datalist[$i]['four_price'] += $datalist[$i]['four_price'];
            $data['four'] += $datalist[$i]['four'];
            
            $data['stock'] += $datalist[$i]['stock'];
            $data['warehouse'] = $datalist[$i]['warehouse'];
		}
        //echo '<pre>';print_R($data['one_price']);
         //$bannerstrarr[] = array('url'=>'index.php?action=sumstorage_age&detail=outport_bydays','value'=>'导出数据');
	//$bannerstr 	= '<button onclick=window.location="index.php?action=sumstorage_age&detail=outport_bydays">导出数据</button>';
	

	$this->V->mark(array(title=>'库存帐龄汇总','data'=>$data,'tablewidth'=>'99%','whouse'=>$whouse,'bannerstr'=>$bannerstr));
    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/sumstorage_age');
	display();
 }
 /*导出数据*/
elseif($detail == 'outport_bydays'){
    
	$process 					= $this->S->dao('process');
	$exchange					= $this->C->service('exchange_rate');
    if($houseid){
        $sqlstr.= ' and temp.wid='.$houseid;
        $datalist = $process->get_storge_age_bystocks($sqlstr,2);
    }

	/*数据处理*/
	for($i = 0; $i <count($datalist); $i++){

		$_three = $datalist[$i]['three'] + $datalist[$i]['four'];
		$_two	= $_three + $datalist[$i]['two'];
		$_one	= $_two + $datalist[$i]['one'];
		$datalist[$i]['stock'] = $datalist[$i]['one'] + $datalist[$i]['two'] + $datalist[$i]['three'] + $datalist[$i]['four'] - $datalist[$i]['outs'];

		/*数量显示*/
		if(($datalist[$i]['four'] - $datalist[$i]['outs']) < 0){
			$datalist[$i]['four'] = 0;
			if(($_three - $datalist[$i]['outs']) < 0){
				$datalist[$i]['three'] = 0;
				if(($_two - $datalist[$i]['outs']) < 0){
					$datalist[$i]['two'] = 0;
					$datalist[$i]['one'] = $_one - $datalist[$i]['outs'];
				}else{
					$datalist[$i]['two'] = $_two - $datalist[$i]['outs'];
				}
			}else{
				$datalist[$i]['three'] = $_three - $datalist[$i]['outs'];
			}
		}else{
			$datalist[$i]['four'] -= $datalist[$i]['outs'];
		}
        
        $coin_code = 'CNY';
		/*金额处理*/
		/*$datalist[$i]['one_price'] 	= $datalist[$i]['one'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
		$datalist[$i]['two_price'] 	= $datalist[$i]['two'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['two']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
		$datalist[$i]['three_price']= $datalist[$i]['three']? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['three']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
		$datalist[$i]['four_price']	= $datalist[$i]['four'] ? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['four']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
        */
        //echo '<pre>';
            //echo $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']);
			/*金额处理*/
            $data['one_price'] 	+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']);
			$data['two_price'] 	+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['two']*$datalist[$i]['cost2']);
			$data['three_price']+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['three']*$datalist[$i]['cost2']);
			$data['four_price']	+= $exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['four']*$datalist[$i]['cost2']);
			
            
            $data['one'] += $datalist[$i]['one'];
            $data['two'] += $datalist[$i]['two'];
            $data['three'] += $datalist[$i]['three'];
            $data['four'] += $datalist[$i]['four'];
            $data['stock'] += $datalist[$i]['stock'];
            $data['warehouse'] = $datalist[$i]['warehouse'];

	}
    $datas[] = $data;
	$filename = 'storage_age_'.date('Y-m-d',time());
	$head_array = array('stock'=>'现存','warehouse'=>'仓库','one'=>'数量(0-30天)','one_price'=>"金额($coin_code)",'two'=>'数量(30-60天)','two_price'=>"金额($coin_code)",'three'=>'数量(60-90天)','three_price'=>"金额($coin_code)",'four'=>'数量(90天以上)','four_price'=>"金额($coin_code)");

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datas);

}
 
?>
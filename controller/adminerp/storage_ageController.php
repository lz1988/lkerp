<?php
/*
 * Created on 2012-10-10
 *
 * by hanson -库龄查询
 */

if($detail == 'list'){


	$coin_code		= $coin_code ? $coin_code : 'USD';//金额默认转换为USD

	$combine_html	= "<select name='combine' style='width:80px'><option value='0' ".(empty($combine)?'selected':'').") >=是=</option><option value='1' ".($combine=='1'?'selected':'')." >=否=</option></select>";

	/*查询条件处理*/
	if($houseid && $combine){$sqlstr.= ' and temp.wid='.$houseid;}else{unset($houseid);}
	$sqlstr.= $sku?' and temp.sku="'.$sku.'"':'';

	$whouse			= $this->C->service('warehouse')->get_whouse('houseid','name','id','id',$houseid);/*获得仓库*/
	$coin_code_html	= $this->C->service('warehouse')->get_coincode_html('coin_code','style="width:80px"',$coin_code); /*取得币种列表*/


	/*点击搜索才出内容*/
	if($searchmod){

		$InitPHP_conf['pageval']	= 15;
		$process 					= $this->S->dao('process');
		$exchange					= $this->C->service('exchange_rate');
		$datalist					= $process->get_storge_age_bystocks($sqlstr,$combine);

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

			/*金额处理*/
			$datalist[$i]['one_price'] 	= $datalist[$i]['one'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
			$datalist[$i]['two_price'] 	= $datalist[$i]['two'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['two']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
			$datalist[$i]['three_price']= $datalist[$i]['three']? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['three']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
			$datalist[$i]['four_price']	= $datalist[$i]['four'] ? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['four']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';

			/*标签随机ID*/
	    	$tag_id						= mt_rand(2000,3000);
			$datalist[$i]['skushow']	= empty($combine) ?$datalist[$i]['sku']:'<a href=javascript:void(0);self.parent.addMenutab('.$tag_id.',"'.$datalist[$i]['sku'].'进出库详细","index.php?action=storage_age&detail=process_detail&wid='.$datalist[$i]['wid'].'&sku='.$datalist[$i]['sku'].'")  title="点击查看进出库明细">'.$datalist[$i]['sku'].'</a>';
			$datalist[$i]['warehouse']	= empty($combine) ? '<font color=#bdbdbd>--</font>':$datalist[$i]['warehouse'];
		}

		$bannerstr 	= '<button onclick=window.location="index.php?action=storage_age&detail=outport_bydays&sku='.$sku.'&houseid='.$houseid.'&coin_code='.$coin_code.'&combine='.$combine.'">导出数据</button>';
		$pageshow 	= array('sku'=>$sku,'houseid'=>$houseid,'coin_code'=>$coin_code,'combine'=>$combine,'searchmod'=>$searchmod);
	}

	$this->V->mark(array(title=>'库存帐龄','datalist'=>$datalist,'whouse'=>$whouse,'sku'=>$sku,'coin_code_html'=>$coin_code_html,'combine_html'=>$combine_html,'tablewidth'=>'99%','bannerstr'=>$bannerstr));
	$this->V->set_tpl('adminweb/storage_age_byhouse');
	display();
}

/*导出数据*/
elseif($detail == 'outport_bydays'){

	/*查询条件处理*/
	if($houseid && $combine){$sqlstr.= ' and temp.wid='.$houseid;}
	$sqlstr.= $sku?' and temp.sku="'.$sku.'"':'';

	$process 					= $this->S->dao('process');
	$exchange					= $this->C->service('exchange_rate');
	$datalist					= $process->get_storge_age_bystocks($sqlstr,$combine);

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

		/*金额处理*/
		$datalist[$i]['one_price'] 	= $datalist[$i]['one'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['one']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
		$datalist[$i]['two_price'] 	= $datalist[$i]['two'] 	? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['two']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
		$datalist[$i]['three_price']= $datalist[$i]['three']? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['three']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';
		$datalist[$i]['four_price']	= $datalist[$i]['four'] ? '<font color=#bdbdbd>'.number_format($exchange->change_rate($datalist[$i]['coin_code'], $coin_code, $datalist[$i]['four']*$datalist[$i]['cost2']),2).'</font>':'<font color=#bdbdbd>0.00</font>';

		$datalist[$i]['warehouse']	= empty($combine) ? '<font color=#bdbdbd>--</font>':$datalist[$i]['warehouse'];
	}

	$filename = 'storage_age_'.date('Y-m-d',time());
	$head_array = array('sku'=>'su','stock'=>'现存','warehouse'=>'仓库','one'=>'数量(0-30天)','one_price'=>"金额($coin_code)",'two'=>'数量(30-60天)','two_price'=>"金额($coin_code)",'three'=>'数量(60-90天)','three_price'=>"金额($coin_code)",'four'=>'数量(90天以上)','four_price'=>"金额($coin_code)");

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);

}

/*查看某SKU进出库详细*/
elseif($detail == 'process_detail'){

	$sku 							= strtr($sku,array('x'=>'-'));
	$pageshow 						= array('sku'=>$sku,'wid'=>$wid);
	$InitPHP_conf['pageval']		= 30;
	$sqlstr							= ' and sku="'.$sku.'" and active="1" and (provider_id='.$wid.' or receiver_id='.$wid.')';
	$datalist 						= $this->S->dao('process')->get_process_detail($sqlstr);
	$thisnum						= 0;

	/*数据显示处理*/
	foreach($datalist as &$val){

		if($val['protype'] 			==	'采购'){
			$val['from'] 			 = 	$val['pname'];
			$val['modtype'] 		 = 	'采购进仓';
		}elseif($val['protype'] 	== 	'其它'){
			if($val['provider_id'] 	== 	$wid) 	{$val['to'] = $val['modtype'] = '其它出库';}
			if($val['receiver_id'] 	== 	$wid) 	{$val['from'] = $val['modtype'] = '其它入库';}
		}elseif($val['protype'] 	== 	'售出'){
			$val['to'] 				 = 	'客户';
			$val['modtype'] 		 = 	'正常售出';
		}elseif($val['protype'] 	== 	'重发'){
			$val['to'] 				 = 	'客户';
			$val['modtype'] 		 = 	'重发货';
		}elseif($val['protype'] 	== 	'退货'){
			$val['from'] 			 = 	'客户';
			$val['modtype'] 		 = 	'退货入库';
		}elseif($val['property'] 	== 	'转仓单'){
			$val['modtype'] 		 = 	'转仓';
			if($val['provider_id'] 	== $wid) {
				$val['to'] 			 = $val['rname'];
				if(empty($val['receiver_id'])) {$val['to'] = '不良品仓';$val['modtype'] = '不良品调拨';}
			}
			if($val['receiver_id'] 	== $wid) $val['from'] 		= $val['pname'];
		}

		if($val['output'] 	== '1' && $val['provider_id'] == $wid){$val['thisnum'] = $thisnum = $thisnum - $val['quantity']; $val['to']	 = '- '.$val['quantity'].'pcs &nbsp; '.$val['to'];}
		if($val['input'] 	== '1' && $val['receiver_id'] == $wid){$val['thisnum'] = $thisnum = $thisnum + $val['quantity']; $val['from']= '+'.$val['quantity'].'pcs &nbsp; '.$val['from']; }

	}

	$displayarr = array();
	$displayarr['sku'] 		= array('showname'=>'SKU');
	$displayarr['order_id'] = array('showname'=>'单号');
	$displayarr['from'] 	= array('showname'=>'来源');
	$displayarr['to'] 		= array('showname'=>'去向');
	$displayarr['thisnum']	= array('showname'=>'库存');
	$displayarr['modtype']	= array('showname'=>'类型');
	$displayarr['ruser']	= array('showname'=>'操作');
	$displayarr['rdate']	= array('showname'=>'时间');

	$sku = strtr($sku,array('-'=>'x'));//因为-在框架内部会被作处理，所以需要替换成x
	$this->V->mark(array('title'=>'<a href="index.php?action=storage_age&detail=process_detail&wid='.$wid.'&sku='.$sku.'">进出库明细</a>  &raquo; '));
	$temp = 'pub_list';
}

/*模板定义*/
if($detail =='list' || $detail == 'process_detail'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>

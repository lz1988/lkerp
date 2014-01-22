<?php
/*
 * Created on 2012-3-1
 *
 * To change the template for this generated file go to
 * by hanson
 * @title 查实际库存，可按时间段，一般供仓管与财务看。
 */

/*搜索选项*/
$stypemu = array(
	'sku-s-l'		=>'SKU：',
	'houseid-a-e'	=>'仓库：',
	'mdate-t-r'		=>'截止日期：'
);


if($detail == 'list'){

	/*取得仓库下拉-用于生成搜索条件*/
	$wdata		= $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
	$houseidarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$houseidarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

	/*初始打开默认中国仓库*/
	if(empty($sqlstr)){$houseid = $this->C->service('global')->sys_settings('listshipment_house','sys');}
	$InitPHP_conf['pageval']	= 15;

	/*SQL语句替换处理,前置临时表名,否则查询出错,有输入条件才显示库存*/
	if($sqlstr){
		$sqlstr = str_replace('sku like "%','temp.sku like "',$sqlstr);
		$sqlstr = str_replace('houseid','temp.wid',$sqlstr);
		$sqlstr = ereg_replace('and mdate(.){23}','',$sqlstr);//过滤掉时间否则不准

		if($startTime) {$timesqlstr.=' and rdate>="'.$startTime.' 00:0:00"';}
		if($endTime)   {$timesqlstr.=' and rdate<="'.$endTime.' 23:59:59"';}

		$exchange	= $this->C->service('exchange_rate');//币别转换用
		$datalist	= $this->S->dao('process')->get_allw_allsku_withp_time($sqlstr,$timesqlstr);
		$sums		= 0;//库存总数
		$sumcost	= 0;//库存成本总价

		foreach ($datalist as &$val) {
			$val['avecost'] = $exchange->change_rate($val['coin_code'],'CNY',$val['cost2']);
			$val['sumcost'] = $val['avecost'] * $val['sums'];

			$sums 		   += $val['sums'];
			$sumcost 	   += $val['sumcost'];

			$val['avecost'] = number_format($val['avecost'],2);
			$val['sumcost'] = number_format($val['sumcost'],2);
		}

		$length							= count($datalist);
		$datalist[$length]['warename']	= '合计：';
		$datalist[$length]['sums']		= $sums;
		$datalist[$length]['sumcost']	= $sumcost;
		$bannerstrarr[]					= array('url'=>'index.php?action=true_stock&detail=output','value'=>'导出库存');
	}

	$tablewidth 						= 1000;
	$displayarr 						= array();
	$displayarr['warename'] 			= array('showname'=>'仓库','width'=>'100');
	$displayarr['sku'] 					= array('showname'=>'SKU','width'=>'80');
	$displayarr['jin_sku'] 				= array('showname'=>'金碟SKU','width'=>'80');
	$displayarr['product_name'] 		= array('showname'=>'产品名称','width'=>'250');
	$displayarr['sums'] 				= array('showname'=>'库存数','width'=>'80');
	$displayarr['avecost']				= array('showname'=>'库存成本(CNY)', 'width'=>'110');
	$displayarr['sumcost']				= array('showname'=>'库存总成本(CNY)', 'width'=>'130');

	$this->V->mark(array('title'=>'历史库存查询'));
	$temp = 'pub_list';
}

/*导出库存*/
elseif($detail == 'output'){

	/*SQL语句替换处理,前置临时表名,否则查询出错*/
	if($sqlstr){
		$sqlstr = str_replace('sku like "%','temp.sku like "',$sqlstr);
		$sqlstr = str_replace('houseid','temp.wid',$sqlstr);
		$sqlstr = ereg_replace('and mdate(.){23}','',$sqlstr);//过滤掉时间否则不准
	}

	if($startTime) {$timesqlstr.=' and rdate>="'.$startTime.' 00:0:00"';}
	if($endTime)   {$timesqlstr.=' and rdate<="'.$endTime.' 23:59:59"';}

	$exchange	= $this->C->service('exchange_rate');//币别转换用
	$datalist	= $this->S->dao('process')->get_allw_allsku_withp_time($sqlstr,$timesqlstr);

	$sums		= 0;			// 库存总数
	$sumcost	= 0;		// 库存成本总价

	foreach ($datalist as &$val) {
		$val['avecost'] = $exchange->change_rate($val['coin_code'],'CNY',$val['cost2']);
		$val['sumcost'] = $val['avecost'] * $val['sums'];

		$sums		   += $val['sums'];
		$sumcost 	   += $val['sumcost'];

		$val['avecost'] = number_format($val['avecost'],2);
		$val['sumcost'] = number_format($val['sumcost'],2);

	}

	$length 						= count($datalist);
	$datalist[$length]['warename']	= '合计：';
	$datalist[$length]['sums']		= $sums;
	$datalist[$length]['sumcost']	= number_format($sumcost,2);

	$filename = 'truestock_'.date('Y-m-d',time());
	$head_array = array('warename'=>'仓库','sku'=>'sku','jin_sku'=>'金碟SKU','sums'=>'库存数','product_name'=>'产品名称','avecost'=>'库存成本(CNY)','sumcost'=>'库存总成本(CNY)');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}

?>

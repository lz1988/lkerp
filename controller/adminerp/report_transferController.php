<?php
/*搜索选项*/
$stypemu = array(
	'sku-s-l'=>'&nbsp;&nbsp;&nbsp;&nbsp;SKU：',
	'order_id-s-l'=>'&nbsp;&nbsp;&nbsp;&nbsp;订单号：',
	'fid-s-l'=>'&nbsp; &nbsp; &nbsp;&nbsp;运单编号：',
	'cuser-s-l'=>'&nbsp; &nbsp;制单人：',
	'comment2-s-l'=>'<br>追踪号：',
	'mdate-t-t'=>'出库日期：',
);
if ($detail == 'list') {

	if ($sqlstr) {
		/*导出数据按钮*/
		$bannerstrarr[] = array('url'=>'index.php?action=report_transfer&detail=output','value'=>'导出数据');

		$InitPHP_conf['pageval'] = 15;
		$sqlstr = str_replace('cuser','p.cuser',$sqlstr);
        $sqlstr = str_replace('mdate','p.mdate',$sqlstr);

		$sqlstr .= ' and isover="N" and output="1" and property="转仓单" ';
		$datalist = $this->S->dao('process')->showtransfer($sqlstr,$orders);

		for($i=0;$i<count($datalist);$i++){
			/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
			if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
				$datalist[$i]['order_idd'] = '';
			}else{
				$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
			}

			$datalist[$i]['comment2'] = empty($datalist[$i]['comment2'])?'--':$datalist[$i]['comment2'];
			$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);
		}
	}

	$this->V->mark(array('title'=>'物料调拨明细'));
	$tablewidth = '1500';

	$displayarr['order_idd'] 		= array('showname'=>'订单号','width'=>'80');
	$displayarr['sku'] 			 	= array('showname'=>'sku','width'=>'100');
	$displayarr['product_name']  	= array('showname'=>'产品名称','width'=>'220');
	$displayarr['prohouse'] 	 	= array('showname'=>'发货仓库','width'=>'100');
	$displayarr['rechouse'] 	 	= array('showname'=>'目的仓库','width'=>'100');
	$displayarr['e_unit_box'] 	 	= array('showname'=>'单箱数量','width'=>'80');
	$displayarr['e_box'] 		 	= array('showname'=>'箱数','width'=>'60');
	$displayarr['quantity'] 	 	= array('showname'=>'总数','width'=>'60');
	$displayarr['comment2'] 	 	= array('showname'=>'物流跟踪号','width'=>'100');
	$displayarr['e_shipping'] 	 	= array('showname'=>'发货方式','width'=>'100');
	$displayarr['e_shipping_fee']	= array('showname'=>'运费支出','width'=>'80');
	$displayarr['fid']		 		= array('showname'=>'运单编号','width'=>'100');
	$displayarr['e_remeber_id']	= array('showname'=>'助记码','width'=>'100');
	$displayarr['cuser'] 		 	= array('showname'=>'制单人','width'=>'80');
	$displayarr['comment'] 		= array('showname'=>'备注','width'=>'100');
	$displayarr['cdate'] 		 	= array('showname'=>'制单日期','width'=>'95');
	$displayarr['muser'] 		 	= array('showname'=>'接单人','width'=>'100');

	$temp = 'pub_list';
}
elseif ($detail == 'output') {

	$sqlstr = str_replace('cuser','p.cuser',$sqlstr);
    $sqlstr = str_replace('mdate','p.mdate',$sqlstr);

	$sqlstr .= ' and isover="N" and output="1" and property="转仓单" ';
	$datalist = $this->S->dao('process')->showtransfer($sqlstr,$orders);

	for($i=0;$i<count($datalist);$i++){
		/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd'] = '';
		}else{
			$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
		}

		$datalist[$i]['comment2'] = empty($datalist[$i]['comment2'])?'--':$datalist[$i]['comment2'];
		$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);
	}

	$filename = 'Report_Transfer';

	$displayarr['order_idd'] 		= array('showname'=>'订单号','width'=>'80');
	$displayarr['sku'] 			 	= array('showname'=>'sku','width'=>'100');
	$displayarr['product_name']  	= array('showname'=>'产品名称','width'=>'220');
	$displayarr['prohouse'] 	 	= array('showname'=>'发货仓库','width'=>'100');
	$displayarr['rechouse'] 	 	= array('showname'=>'目的仓库','width'=>'100');
	$displayarr['e_unit_box'] 	 	= array('showname'=>'单箱数量','width'=>'80');
	$displayarr['e_box'] 		 	= array('showname'=>'箱数','width'=>'60');
	$displayarr['quantity'] 	 	= array('showname'=>'总数','width'=>'60');
	$displayarr['comment2'] 	 	= array('showname'=>'物流跟踪号','width'=>'100');
	$displayarr['e_shipping'] 	 	= array('showname'=>'发货方式','width'=>'100');
	$displayarr['e_shipping_fee']	= array('showname'=>'运费支出','width'=>'80');
	$displayarr['fid']		 		= array('showname'=>'运单编号','width'=>'100');
	$displayarr['e_remeber_id']	= array('showname'=>'助记码','width'=>'100');
	$displayarr['cuser'] 		 	= array('showname'=>'制单人','width'=>'80');
	$displayarr['comment'] 		= array('showname'=>'备注','width'=>'100');
	$displayarr['mdate'] 		 	= array('showname'=>'制单日期','width'=>'95');
	$displayarr['muser'] 		 	= array('showname'=>'接单人','width'=>'100');
	$head_array = array(
				'order_idd'				=> '订单号',
				'sku' 						=> 'sku',
				'product_name'			=> '产品名称',
				'prohouse' 				=> '发货仓库',
				'rechouse' 				=> '目的仓库',
				'e_unit_box' 				=> '单箱数量',
				'e_box' 					=> '箱数',
				'quantity' 				=> '总数',
				'comment2' 				=> '物流跟踪号',
				'e_shipping' 				=> '发货方式',
				'e_shipping_fee' 			=> '运费支出',
				'fid' 						=> '运单编号',
				'e_remeber_id' 			=> '助记码',
				'cuser' 					=> '制单人',
				'comment' 					=> '备注',
				'mdate'					=> '制单日期',
				'muser' 					=> '接单人'
				);
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);

}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

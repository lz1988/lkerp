<?php
/*
 * Created on 2013-1-28 by hanson
 * @title 订单退款明细表
 *
 */


 /*列表*/
 if($detail == 'list'){

	$stypemu = array(
			'deal_id-s-l'		=>'&nbsp;&nbsp;&nbsp;平台单号：',
			'deal_sku-s-l'		=>'&nbsp;&nbsp;&nbsp;&nbsp;平台SKU：',
			'sku-s-l'			=>'&nbsp;&nbsp;&nbsp;&nbsp;SKU：',
			'product_name-s-l'	=>'&nbsp;&nbsp;&nbsp;&nbsp;品名：',
			'cuser-s-l'			=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;创建人：',
			'account-a-e'		=>'&nbsp;&nbsp;&nbsp;&nbsp;销售账号：',
			'cdate-t-t'			=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;时间：',
	);

	/*取得销售账号下拉*/
	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','id,account_code');
	$accountarr		= array(''=>'=请选择=');
	for($i = 0; $i < count($soldaccount); $i++){
		$accountarr[$soldaccount[$i]['id']] = $soldaccount[$i]['account_code'];
	}

	$InitPHP_conf['pageval']	= 20;
	$sqlstr						= strtr($sqlstr,array('cdate'=>'f.cdate'));//时间转换

	/*数据查询与处理*/
	$datalist					= $this->S->dao('orders_refund')->get_list($sqlstr);
	foreach($datalist as &$val){
		$val['account_code']	= $accountarr[$val['account']];
	}


	$displayarr					= array();
	$displayarr['cdate']		= array('showname'=>'时间','width'=>'100');
	$displayarr['deal_id']		= array('showname'=>'平台单号','width'=>'120');
	$displayarr['deal_sku'] 	= array('showname'=>'平台SKU','width'=>'150');
	$displayarr['sku']			= array('showname'=>'SKU','width'=>'80');
	$displayarr['product_name']	= array('showname'=>'品名','width'=>'200');
	$displayarr['quantity'] 	= array('showname'=>'数量','width'=>'50');
	$displayarr['total']		= array('showname'=>'总额','width'=>'50');
	$displayarr['currency']		= array('showname'=>'币别','width'=>'60');
	$displayarr['account_code']	= array('showname'=>'销售账号','width'=>'100');
	$displayarr['cuser']		= array('showname'=>'建单人','width'=>'100');

	$bannerstr		= '<button onclick="window.location=\'index.php?action=order_refund&detail=import\'">导入表格</button>';
	$bannerstrarr[]	= array('url'=>'index.php?action=order_refund&detail=output','value'=>'导出数据');

	$this->V->mark(array('title'=>'订单退款明细表'));
	$temp = 'pub_list';
 }


 /*导出退款*/
 elseif($detail == 'output'){

 	$sqlstr		= strtr($sqlstr,array('cdate'=>'f.cdate'));//时间转换
	$datalist	= $this->S->dao('orders_refund')->get_list($sqlstr);

	$filename	= 'order_refund_'.date('Y-m-d',time());
	$head_array	= array('cdate'=>'时间','deal_id'=>'平台单号','deal_sku'=>'平台SKU','sku'=>'SKU','product_name'=>'品名','quantity'=>'数量','total'=>'总额','currency'=>'币别','account_name'=>'销售账号','cuser'=>'建单人');

	$this->C->service('upload_excel')->download_excel($filename, $head_array, $datalist);

 }

 /*导入退款*/
 elseif($detail == 'import'){

	/*实例化上传表格类*/
	$upload_exl_service 	= $this->C->Service('upload_excel');

	/*上传配置数组*/
	$fieldarray				= array(
								'order_refund'	=>array('A','B','C','D','E','F','G','H'),
								'upload_dir'	=>"./data/uploadexl/orders/"
							);

	/*读取上传的文件内容并写入数据库保存*/
	if($filepath){

		$orders				= $this->S->dao('orders_refund');
		$objsku_alias		= $this->S->dao('sku_alias');
		$product			= $this->S->dao('product');

		$errornum			= 0;
		$all_arr			=  $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray['order_refund'], 1);
		unlink($filepath);//取得内容后删除表格
		unset($all_arr['0']);//删除表头

		/*取得币别*/
		$rateArr	= array();
		$backexChgeRate = $this->S->dao('exchange_rate')->D->get_allstr(' and isnew="1" ','','','rate,code,stage_rate');
		foreach($backexChgeRate as $val){
			$rateArr[$val['code']] = array('rate'=>$val['rate'],'stage_rate'=>$val['stage_rate']);
		}

		/*根据销售账号取ID用*/
		$backsoacArr		= array();
		$backsoldaccount	= $this->S->dao('sold_account')->D->get_allstr();
		foreach($backsoldaccount as $val){
			$backsoacArr[$val['account_name']]	= $val['id'];
		}

		$orders->D->query('BEGIN');
		foreach($all_arr as $val){

			if(strpos($val['date/time'],'PST')){
				$cdate = strtotime($val['date/time'])-16*3600;//US账号减去16个时差，保持数字一致
			}elseif(strpos($val['date/time'],'GMT')){
				$cdate = strtotime($val['date/time'])-8*3600;//UK账号减去8个时差，保持数字一致
			}elseif(strpos($val['date/time'],'PDT')){
				$cdate = strtotime($val['date/time'])-15*3600;//US账号存在PDT减去15个时差，保持数字一致
			}else{
				$cdate = strtotime("+".($val['date/time']-40909)." days",strtotime('2012-01-01'));//时间转换
			}

			/*取产品PID，先到产品表查，若无法匹配，再将SKU作为别名到别名表查*/
			$pid 			= $product->D->get_one(array('sku'=>$val['sku']),'pid');
			if(!$pid) $pid	= $objsku_alias->D->get_one(array('sku_code'=>$val['sku']),'pid');

			$insertarr	= array(
				'cdate'		=>date('Y-m-d H:i:s',$cdate),
				'deal_id'	=>$val['order id'],
				'deal_sku'	=>$val['sku'],
				'pid'		=>$pid,
				'quantity'	=>$val['quantity'],
				'total'		=>$val['total'],
				'currency'	=>$val['currency'],
				'stage_rate'=>$rateArr[$val['currency']]['stage_rate'],
				'usd_rate'	=>$rateArr[$val['currency']]['rate'],
				'account'	=>$backsoacArr[$val['sold_account']],
				'cuser'		=>$_SESSION['eng_name'],
			);

			$isexists_id= $orders->D->get_one(array('cdate'=>date('Y-m-d H:i:s',$cdate),'deal_id'=>$val['order id'],'deal_sku'=>$val['sku']),'id');

			if($isexists_id){
				unset($insertarr['stage_rate'],$insertarr['usd_rate']);//更新的话，期号与当期对美元汇率不能更新，保留原来的
				$sid = $orders->D->update(array('id'=>$isexists_id),$insertarr);//以date/time、order id、sku为判断重复标准，重复则覆盖
			}else{
				$sid = $orders->D->insert($insertarr);
			}
			if(!$sid) $errornum++;
		}

		$jumpUrl = 'index.php?action=order_refund&detail='.(empty($errornum)?'list':'import');
		if(empty($errornum)) {$orders->D->query('commit');$this->C->success('保存成功',$jumpUrl);}else{$orders->D->query('rollback');$this->C->success('保存失败，请重试',$jumpUrl);}

	}else{

		$data_error 		= 0;
		$tablelist 		    = '<table id="mytable">';

		/*检测销售账号用*/
		$backsoacArr		= array();
		$backsoldaccount	= $this->S->dao('sold_account')->D->get_allstr();
		foreach($backsoldaccount as $val){
			$backsoacArr[]	= $val['account_name'];
		}

		/*检测币别用*/
		$backcoincodeArr	= $this->S->dao('exchange_rate')->get_sys_coincode();

		/*检测SKU别名用*/
		$objsku_alias		= $this->S->dao('sku_alias');

		/*检测产品*/
		$product			= $this->S->dao('product');


		/*上传并获得数据*/
		$all_arr 			= $upload_exl_service->get_upload_excel_datas($fieldarray['upload_dir'] , $fieldarray['order_refund'], 1);
		$filepath			= $_SESSION['filepath'];

		/*表头检测，若有错，显示表头*/
		$tablelist		   .= $upload_exl_service->checkmod_head(&$all_arr,&$data_error,'order_refund');

		/*循环检测数据*/
		foreach($all_arr as $k=>$val){


			$tablelist .= '<tr>';
			foreach($val as $j=>$value) {

				$error_style = '';
				/*检测销售账号*/
				if($j == 'sold_account'){
					if(empty($value)){
						$error_style = ' bgcolor="red" title="销售账号不能为空!"';
						$data_error++;
					}elseif(!in_array($value,$backsoacArr)){
						$error_style = ' bgcolor="red" title="系统不存在的销售账号!"';
						$data_error++;
					}
				}

				/*检测币别*/
				if($j == 'currency'){

					if(empty($value)){
						$error_style = ' bgcolor="red" title="币别不能为空!"';
						$data_error++;
					}elseif(!in_array($value,$backcoincodeArr)){
						$error_style = ' bgcolor="red" title="系统不存在的币别!"';
						$data_error++;
					}
				}

				/*检测SKU别名*/
				if($j == 'sku'){

					if(empty($value)){
						$error_style = ' bgcolor="red" title="SKU不能为空!"';
						$data_error++;
					}else{
						if(!$product->D->get_one(array('sku'=>$value),'pid')){
							if(!$objsku_alias->D->get_one(array('sku_code'=>$value),'id')){
								$error_style = ' bgcolor="red" title="该SKU未录入系统或未录入此别名，请检查!"';
								$data_error++;
							}
						}
					}
				}

				/*非退款类型提示错误*/
				if($j == 'type' && $value != 'Refund' && $value != 'Remboursement'){
					$error_style = ' bgcolor="red" title="type值只能是Refund或Remboursement!" ';
					$data_error++;
				}

				/*检测时间*/
				if($j == 'date/time'){
					if(!strpos($value,'PST') && !strpos($value,'GMT') && !strpos($value,'PDT') && !preg_match('/^[0-9]*$/',$value) ){
						$error_style = ' bgcolor="red" title="时间格式错误!"';
						$data_error++;
					}
				}

				/*检测金额*/
				if($j == 'total'){
					if(!preg_match('/^-[\d]+(\.?[\d]+)?$/',$value)){
						$error_style = ' bgcolor="red" title="金额错误，只能是整数或最多两位小数并且是负数!"';
						$data_error++;
					}
				}

				$tablelist.= '<td '.$error_style.' >&nbsp;'.$value.'</td>';
			}
			$tablelist.= '</tr>';
		}
		$tablelist.= '</table>';

		if(!$data_error && isset($all_arr)){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="submit" value="确认并提交">';
		}elseif($data_error){

			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}

		/*标记变量与模板输出*/
		$this->V->set_tpl('adminweb/commom_excel_import');
	 	$this->V->mark(array('title'=>'导入退款明细表-订单退款明细表(list)','tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
	 	display();
	}

 }

 /*头尾模板包含*/
 if($detail == 'list' || $detail == 'import'){

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }

?>

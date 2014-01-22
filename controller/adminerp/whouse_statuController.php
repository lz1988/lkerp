<?php
/*
 * Created on 2012-1-9
 *
 * @title 查看库存状态
 * @author by hanson
 */

include_once('./api/FBAInventoryServiceMWS/function.php');

if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'sku-s-e'=>'SKU：',
		'houseid-a-e'=>'仓库：',
		'order_id-h-e'=>'隐藏订单号',//做跳转用
		'strreplace-h-e'=>'5'//用于查询直接单击查询
	);

	/*用于跳转至采购页面提前生成SESSION用*/
	$_SESSION['process_modstock_stypemu'] = json_encode(array('order_id-s-e'=>'采购订单号'));

//	if(isset($strreplace)) {
//		$sqlstr .= ' and 1=1 and e.extends not like \'%"e_id":"%\'';
//	}

	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
    //echo '<pre>';print_r($wdata);
    //临时账号，查询库存，不显示仓库名称，只显示代码
    if ($_SESSION['username'] == 'vendor1'){
        $houseidarr = array(''=>'=请选择=');
    	for($i=0;$i<count($wdata);$i++){
    		$houseidarr[$wdata[$i]['id']] = substr($wdata[$i]['name'],0,strpos($wdata[$i]['name'],'-')); 
    	}
    }else{
    	$houseidarr = array(''=>'=请选择=');
    	for($i=0;$i<count($wdata);$i++){
    		$houseidarr[$wdata[$i]['id']] = $wdata[$i]['name'];
    	}
    }
	if ($this->C->service('admin_access')->checkResRight('r_t_amazon')) {
		$bannerstr 	= '<p style="color:#c6a8c6; font-size:12px">需要查看Amazon同步库存， <a href="index.php?action=whouse_statu&detail=amazon">点此进入</a></p>';
	}
	$process  = $this->S->dao('process');

	/*SQL语句替换处理,前置临时表名,否则查询出错*/
	if($sqlstr){
		$sqlstr = str_replace('sku','temp.sku',$sqlstr);
		$sqlstr = str_replace('houseid','temp.wid',$sqlstr);
		//$sqlstr = str_replace();

		$sku_alias = $this->S->dao('sku_alias');
		$esse = $this->S->dao('esse');
		$info_amazon = $this->S->dao('info_amazon');
		$datalist = array();
		$code_arr = array();

		if (empty($houseid)) {
			$warehouse = $esse->get_all_other_warehouse();
			foreach($warehouse as $val) {
				$in .= $val['id'].',';
			}
			$in = substr($in, 0, strlen($in)-1);
			$sqlstr .= ' and e.id not in ('.$in.')';
//echo '<pre>'.print_r($warehouse,1).'</pre>';
			/*查库存数(实实在在库存可发数量不包括损益的)*/
			$datalist = $process->get_allw_allsku($sqlstr,2);
			// 目标sku别名

			$sql = ' and sold_way="Amazon组" ';
			$sql .= ' and pro_sku ="'.$sku.'" ';
			$sku_code = $sku_alias->select_sku_code($sql);
//echo '<pre>'.print_r($sku_code,1).'</pre>';
			if ($sku_code) {
				foreach ($sku_code as $val) {
					$code_arr[] = $val['sku_code'];
				}
				$length = count($code_arr);
				set_include_path(get_include_path() . PATH_SEPARATOR . './api');
//echo '<pre>'.print_r($warehouse,1).'</pre>';
				foreach ($warehouse as $val) {
					$amazon_info = $info_amazon->get_one_by_id($val['a_id']);
					$amazon_res_arr = get_amazon_warehouse($amazon_info, $code_arr);
					if (!$amazon_res_arr) {
			 			echo '<script>alert("输入正确sku！确认存在amazon别名！");history.go(-1)</script>';
			 			exit();
			 		}
					$datanum = count($datalist);
			     	// 统计某项sku的总在途库存和总可发库存
			     	$loadquantity = 0;
			     	$instockquantity = 0;
			     	for ($i = 0; $i < $length; $i++) {
			     		// 在途库存
			     		$temp = $amazon_res_arr[$sku_code[$i]['sku_code']][0] - $amazon_res_arr[$sku_code[$i]['sku_code']][1];
			     		$loadquantity += $temp;
			     		// 可发库存
			     		$temp = $amazon_res_arr[$sku_code[$i]['sku_code']][1];
			     		$instockquantity += $temp;
			     	}
			     	$datalist[$datanum]['wid'] = $val['id'];
                    //echo $_SESSION['username'];
                    //if ($_SESSION['username'] == 'vendor1')
                        //$datalist[$datanum]['warename'] = substr($val['name'],0,strpos($val['name'],'-')); 
                    //else
			     	   $datalist[$datanum]['warename'] = $val['name'];
			     	$datalist[$datanum]['sku'] = $sku_code[0]['pro_sku'];
			     	$datalist[$datanum]['pid'] = $sku_code[0]['pid'];
			     	$datalist[$datanum]['product_name'] = $sku_code[0]['product_name'];
			     	$datalist[$datanum]['sums'] = $instockquantity;
			     	$datalist[$datanum]['in_waresums'] = $loadquantity;
			     	$datalist[$datanum]['in_soldsums'] = 0;
			     	$datalist[$datanum]['tensoldsums'] = 0;
				}
			}
		}
		else {
			if ($this->C->service('global')->is_other_warehouse($houseid)) {
				/*查库存数(实实在在库存可发数量不包括损益的)*/
				$sku_alias = $this->S->dao('sku_alias');
				$sql = ' and sold_way="Amazon组" ';
				if (!empty($sku)) {
					$sql .= ' and pro_sku ="'.$sku.'" ';
					$sku_code = $sku_alias->select_sku_code($sql);
					foreach ($sku_code as $val) {
						$code_arr[] = $val['sku_code'];
					}
				}
				else {
					$InitPHP_conf['pageval'] = 15;
					$sku_pid = $sku_alias->select_pid_group_by_pid($sql);
					unset($InitPHP_conf['pageval']);
					foreach( $sku_pid as $val ) {
						$tempsql = ' and a.pid ='.$val['pid'].' and sold_way="Amazon组"  ';
						$temp_code = $sku_alias->select_sku_code($tempsql);
						foreach ($temp_code as $val1) {
							$sku_code[] = $val1;
							$code_arr[] = $val1['sku_code'];
						}
					}
				}
//echo '<pre>'.print_r($sku_code,1).'</pre>';
//echo '<pre>'.print_r($code_arr,1).'</pre>';
				$length = count($code_arr);
//echo $length;
				if ($length > 0) {
					$extends = $this->S->dao('esse')->get_extends_by_id($houseid);
//echo '<pre>'.print_r($extends,1).'</pre>';
//echo '<pre>'.print_r($e_id,1).'</pre>';
					$amazon_info = $this->S->dao('info_amazon')->get_one_by_id($extends['a_id']);
					$amazon_res_arr = get_amazon_warehouse($amazon_info, $code_arr);

					if (!$amazon_res_arr) {
	 					echo '<script>alert("输入正确sku！确认存在amazon别名！");history.go(-1)</script>';
	 					exit();
	 				}
			     	//$length = count($code_arr);
			     	$datanum = 0;
			     	// 统计某sku总数所在位置
			     	$tempnum = 0;
			     	// 统计某项sku的总在途库存和总可发库存
			     	$loadquantity = 0;
			     	$instockquantity = 0;
			     	for ($i = 0; $i < $length; $i++) {
			     		if ($i == 0 || $sku_code[$i]['pro_sku'] != $sku_code[$i - 1]['pro_sku']) {
			     			$datalist[$datanum]['sku'] = $sku_code[$i]['pro_sku'];
			     			$datalist[$datanum]['pid'] = $sku_code[$i]['pid'];
			     			$datalist[$datanum]['product_name'] = $sku_code[$i]['product_name'];
			     		}
			     		$datalist[$datanum]['wid'] = $houseid;
                        
                       
			     		$datalist[$datanum]['warename'] = $extends['name'];

			     		// 在途库存
			     		$temp = $amazon_res_arr[$sku_code[$i]['sku_code']][0] - $amazon_res_arr[$sku_code[$i]['sku_code']][1];
			     		$loadquantity += $temp;

			     		// 可发库存
			     		$temp = $amazon_res_arr[$sku_code[$i]['sku_code']][1];
			     		$instockquantity += $temp;

			     		if ($i == $length-1 || $sku_code[$i]['pro_sku'] != $sku_code[$i + 1]['pro_sku']) {
			     			$datalist[$datanum]['sums'] = $instockquantity;
			     			$datalist[$datanum]['in_waresums'] = $loadquantity;
			     			$datalist[$datanum]['in_soldsums'] = 0;
			     			$datalist[$datanum]['tensoldsums'] = 0;
			     			$loadquantity = 0;
			     			$instockquantity = 0;
			     			$datanum++;
			     		}
			     	}

			     	if (empty($sku)) {
						$InitPHP_conf['pageval'] = 15;
						$sku_alias->select_pid_group_by_pid($sql);
			     	}
				}
				else {
					echo '<script>alert("输入正确sku！确认存在amazon别名！");history.go(-1)</script>';
				}
			}
			else {
				/*查库存数(实实在在库存可发数量不包括损益的)*/
				$InitPHP_conf['pageval'] = 15;
				$datalist = $process->get_allw_allsku($sqlstr,2);
				$bannerstrarr[] = array('url'=>'index.php?action=whouse_statu&detail=output','value'=>'导出数据');
				$bannerstrarr[] = array('url'=>'index.php?action=whouse_statu&detail=outputwidthp','value'=>'导出数据(成本)','class'=>'eight');
			}
		}



//echo '<pre>'.print_r($datalist,1).'</pre>';
//echo count($datalist);
		/*以存在库存的SKU为基础搜索采购在途与转仓在途*/
		for($i=0;$i<count($datalist);$i++){

				/*库存系数显示处理*/
				$show_error = empty($datalist[$i]['tensoldsums'])?'9999':number_format($datalist[$i]['sums']/$datalist[$i]['tensoldsums'],2);
				if($show_error <= 1){
					$datalist[$i]['tensoldsums'] = '<font color=red><b>'.$show_error.'</b></font>';
				}elseif(1 < $show_error && $show_error<= 7){
					$datalist[$i]['tensoldsums'] = '<font color=blue><b>'.$show_error.'</b></font>';
				}else{
					$datalist[$i]['tensoldsums'] = '<font color=black size=3>+<b>∞</b></font>';
				}


				$detaid = mt_rand(00000,99999);
                 if ($_SESSION['username'] == 'vendor1')
                    $datalist[$i]['warename'] = substr($datalist[$i]['warename'],0,strpos($datalist[$i]['warename'],'-')); 
                // else
                   // $datalist[$i]['warename'] = $extends['name'];
				/*在途为存，包括退货，采购，调拨*/
				$datalist[$i]['in_ware'] = '<a href=javascript::void(0) title=点击滑动 onclick=slide(this.id,this.nextSibling.id,"check_allin",'.$datalist[$i]['wid'].',"'.trim($datalist[$i]['sku']).'") id='.$detaid.' class="no"><span id=s'.$detaid.' style="background:url(./staticment/images/open_no.gif) no-repeat;width:84px;height:21px;color:#fff;display:block;cursor:pointer">&nbsp;&nbsp;'.$datalist[$i]['in_waresums'].'</span></a>';
				$datalist[$i]['in_ware'].= '<span id="no" class=c'.$detaid.'></span><div id="d'.$detaid.'" style="display:none;padding-left:8px"><img src="./staticment/images/loading.gif" border=0></div>';

				/*查销售记录*/
				$detaid 	 = mt_rand(00000,99999);
				$datalist[$i]['in_sold'] = '<a href=javascript::void(0) title=点击查看近三个月记录 onclick=slide(this.id,this.nextSibling.id,"check_allsold",'.$datalist[$i]['wid'].',"'.trim($datalist[$i]['sku']).'") id='.$detaid.' class="no"><span id=s'.$detaid.' style="background:url(./staticment/images/open_no.gif) no-repeat;width:84px;height:21px;color:#fff;display:block;cursor:pointer">&nbsp;&nbsp;'.$datalist[$i]['in_soldsums'].'</span></a>';
				$datalist[$i]['in_sold'].= '<span id="no" class=c'.$detaid.'></span><div id="d'.$detaid.'" style="display:none;padding-left:8px"><img src="./staticment/images/loading.gif" border=0></div>';


				/*库存数输出处理。*/
				$detaid = mt_rand(00000,99999);
				$datalist[$i]['sums'] = '<a href=javascript::void(0) title=点击滑动 onclick=slide(this.id,this.nextSibling.id,"check_sums",'.$datalist[$i]['wid'].',"'.trim($datalist[$i]['sku']).'") id='.$detaid.' class="no"><span id=s'.$detaid.' style="background:url(./staticment/images/open_no.gif) no-repeat;width:84px;height:21px;color:#fff;display:block;cursor:pointer">&nbsp;&nbsp;'.$datalist[$i]['sums'].'</span></a>';
				$datalist[$i]['sums'].= '<span id="no" class=c'.$detaid.'></span><div id="d'.$detaid.'" style="display:none;padding-left:8px"><img src="./staticment/images/loading.gif" border=0></div>';
		}
//echo '<pre>'.print_r($datalist,1).'</pre>';
		$displayarr = array();

		$displayarr['warename'] 	= array('showname'=>'仓库','width'=>'80');
		$displayarr['sku'] 			= array('showname'=>'SKU','width'=>'80');
		$displayarr['product_name'] = array('showname'=>'产品名称','width'=>'200');
		$displayarr['in_ware'] 		= array('showname'=>'在途库存','width'=>'150','title'=>'包含采购在途、转入在途、退货在途');
		$displayarr['sums'] 		= array('showname'=>'可发库存','width'=>'150','title'=>'仓库可发数');
		$displayarr['in_sold'] 		= array('showname'=>'销售历史','width'=>'150','title'=>'近三个月销售记录');
		$displayarr['tensoldsums']		= array('showname'=>'库存系数','width'=>'80','title'=>'红色不充足，蓝色正常，黑色过足');
	}
	$temp = 'pub_list';
	$jslink = "<script src='./staticment/js/whouse_statu.js'></script>\n";
	$this->V->mark(array('title'=>'库存状态'));
}

/*导出库存带成本价*/
elseif($detail == 'outputwidthp'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_w_output')){$this->C->sendmsg();}

	/*搜索选项*/
	$stypemu = array(
		'sku-s-e'=>'SKU：',
		'houseid-a-e'=>'仓库：',
	);

	/*sql替*/
	if($sqlstr) {
		$sqlstr = str_replace('sku','temp.sku',$sqlstr);
		$sqlstr = str_replace('houseid','temp.wid',$sqlstr);
	}

	$datalist = $this->S->dao('process')->get_allw_allsku_withp($sqlstr);

	/*转换货币，默认人民币不用转,导出统一用人民币显示*/
	foreach($datalist as &$val){

		if($val['coin_code']!='CNY'){
			$new_price = $this->C->service('exchange_rate')->change_rate($val['coin_code'],'CNY',$val['costp']);
			$val['costp'] = number_format($new_price,2);//全站数据保留两位小数
		}
        
        if ($_SESSION['username'] == 'vendor1')
            $val['warename'] = substr($val['warename'],0,strpos($val['warename'],'-'));
        
	}
	$filename = 'warehouse_withcostp_'.date('Y-m-d',time());
	$head_array = array('warename'=>'仓库名称','sku'=>'sku','sums'=>'库存','costp'=>'costp','product_name'=>'产品名称');

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);

}

/*导出报表*/
elseif($detail == 'output'){

	/*sql替换*/
	if($sqlstr) {
		$sqlstr = str_replace('sku','temp.sku',$sqlstr);
		$sqlstr = str_replace('houseid','temp.wid',$sqlstr);
	}

	$process= $this->S->dao('process');
	$datalist = $process->get_allw_allsku($sqlstr,2);
    //print_r($datalist);die();

	for($i=0;$i<count($datalist);$i++){
        if ($_SESSION['username'] == 'vendor1')
            $datalist[$i]['warename'] = substr($datalist[$i]['warename'],0,strpos($datalist[$i]['warename'],'-'));

		/*库存系数显示处理*/
		$show_error = empty($datalist[$i]['tensoldsums'])?'9999':number_format($datalist[$i]['sums']/$datalist[$i]['tensoldsums'],2);
		if($show_error <= 1){
			$datalist[$i]['tensoldsums'] = '<font color=red><b>'.$show_error.'</b></font>';
		}elseif(1 < $show_error && $show_error<= 7){
			$datalist[$i]['tensoldsums'] = '<font color=blue><b>'.$show_error.'</b></font>';
		}else{
			$datalist[$i]['tensoldsums'] = '<font color=black size=3>+<b>∞</b></font>';
		}
	}

	$filename = 'warehouse_'.date('Y-m-d',time());
	$head_array = array('warename'=>'仓库名称','sku'=>'sku','in_waresums'=>'在途库存','sums'=>'可发库存','in_soldsums'=>'销售历史','tensoldsums'=>'库存系数','product_name'=>'产品名称');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);

}


/*查询近三个月的销售记录*/
elseif($detail == 'check_allsold'){

	$backlist = $this->S->dao('process')->get_havesold_nearthree($sku,$houseid);
	if(empty($backlist)) exit('没有记录!');

	$return = '<table cellpadding="0"  cellspacing="0">';
	$return.= '<tr><td>年月</td><td>销量</td></tr>';

	foreach($backlist as $val){
		$return.= '<tr><td>'.substr($val['cdate'],0,7).'</td><td>'.$val['soldsums'].'</td></tr>';
	}

	$return.='</table>';
	echo  $return;

}


/*显示采购在途与转入在途详情*/
elseif($detail == 'check_allin'){

	$backlist = $this->S->dao('process')->get_allinware($sku,$houseid);
	if(empty($backlist)) exit('没有数据!');


	$return = '<table cellpadding="0"  cellspacing="0">';
	$return.= '<tr><td>批次</td><td>数量</td><td>进度</td></tr>';

	foreach ($backlist as $val){

		/*分析采购单状态*/
		if($val['property']=='采购单'){
			$jumpaction = 'process_modstock';
			switch ($val['statu']){
				case '0':$status = '未审核';break;
				case '1':$status = '已审核';break;
				case '3':$status = '已下单';break;
				case '4':$status = '抵销单';break;
			}
		}

		/*分析转仓单状态*/
		elseif($val['property'] == '转仓单' || $val['property']=='进仓单'){
			$jumpaction = 'process_transfer';
			switch ($val['statu']){
				case '0':$status = '未接收';break;
				case '1':$status = '已接收';break;
				case '3':$status = '已发货';break;
			}
		}

		$return.= '<tr><td><a title=点击查看详情 target=_blank href=index.php?action='.$jumpaction.'&detail=list&ispay=&order_id='.$val['order_id'].'&statu='.$val['statu'].'>'.$val['order_id'].'</a></td><td>'.$val['sums'].'</td><td>'.$status.'</td></tr>';
	}
	$return.='</table>';

	echo  $return;
}



/*查看库存数详情,调出不良品状况*/
elseif($detail == 'check_sums'){

	$process 	 = $this->S->dao('process');
	$backlist    = $process->get_badinhouse($sku,$houseid);
	$backlist_t  = $process->get_havebook_bysku($sku,$houseid);

	$middle_line = '';
	$return_t 	 = '';
	$return   	 = '';
	if(empty($backlist) && empty($backlist_t['quantity'])){exit('没有损益与预出库记录!');}elseif($backlist&&$backlist_t){$middle_line = '<hr>';};

	/*预出库总数*/
	if(!empty($backlist_t['quantity'])){

		$return_t.= '<table cellpadding="0"  cellspacing="0" width=120>';
		$return_t.='<tr><td colspan=2>预出库总数：'.$backlist_t['quantity'].'pcs</td></tr>';
		$return_t.='</table>';
	}

	if(is_array($backlist)){

		$return_pre = '';
		$return_nums= '';
		/*损益情况列表*/
		foreach ($backlist as $val){

			/*分析采购单状态*/
			switch ($val['comment2']){
				case 'defective_customer':$status = '客户损坏';break;
				case 'damaged_distributor':$status = '供应商损坏';break;
				case 'damaged_carrier':$status = '物流损坏';break;
				case 'damaged_warehouse':$status = '仓库损坏';break;
				case 'damaged':$status = '其他损坏';break;
			}

			$return_pre.= '<tr><td>'.$status.'</td><td>'.$val['badsums'].'</td></tr>';
			$return_nums+=$val['badsums'];
		}
		$return = '<table cellpadding="0"  cellspacing="0" width=120>';
		$return.='<tr><td colspan=2>损益总数：'.$return_nums.'pcs</td></tr>';
		$return.= '<tr><td>损益情况</td><td>数量</td></tr>';
		$return.= $return_pre;
		$return.='</table>';
	}
	echo  $return_t.$middle_line.$return;

}
/*
 * create on 2012-06-07
 * by wall
 * 亚马逊接口
 * */
elseif ($detail == 'amazon') {
	if (!$this->C->service('admin_access')->checkResRight('r_t_amazon')) {
		$this->C->ajaxmsg(0);
	}
	$stypemu = array(
		'pro_sku-s-e'=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;SKU：',
		'id-a-e'=>'&nbsp; &nbsp; &nbsp; &nbsp;帐号选择：'
	);

	$idarr = array();
	$idarr[''] = '请选择';
	$interface = $this->S->dao('info_amazon')->get_all_list('');
	foreach ($interface as $val) {
		$idarr[$val['id']] = $val['ia_seller_id'];
	}
	$this->V->mark(array('title'=>'amazon库存查询-库存状态(list)'));
	if (!empty($id)) {
		$sku_alias = $this->S->dao('sku_alias');
		$code_arr = array();
		$sql = ' and sold_way="Amazon组" ';
		if (!empty($pro_sku)) {
			$sql .= ' and pro_sku ="'.$pro_sku.'" ';
			$sku_code = $sku_alias->select_sku_code($sql);
			foreach ($sku_code as $val) {
				$code_arr[] = $val['sku_code'];
			}
		}
		else {
			$InitPHP_conf['pageval'] = 15;
			$sku_pid = $sku_alias->select_pid_group_by_pid($sql);
			unset($InitPHP_conf['pageval']);
			foreach( $sku_pid as $val ) {
				$tempsql = ' and a.pid ='.$val['pid'].' and sold_way="Amazon组"  ';
				$temp_code = $sku_alias->select_sku_code($tempsql);
				foreach ($temp_code as $val1) {
					$sku_code[] = $val1;
					$code_arr[] = $val1['sku_code'];
				}
			}
		}
		$length = count($code_arr);

		if ($length > 0) {

	 		$amazon_info = $this->S->dao('info_amazon')->get_one_by_id($id);
	 		$amazon_res_arr = get_amazon_warehouse($amazon_info, $code_arr);

	 		if (!$amazon_res_arr) {
	 			echo '<script>alert("输入正确sku！确认存在amazon别名！")</script>';
	 			exit();
	 		}

	     	$datalist = array();

	     	//$length = count($code_arr);
	     	$datanum = 0;
	     	// 统计某sku总数所在位置
	     	$tempnum = 0;
	     	// 统计某项sku的总在途库存和总可发库存
	     	$loadquantity = 0;
	     	$instockquantity = 0;
	     	for ($i = 0; $i < $length; $i++) {
	     		if ($i == 0 || $sku_code[$i]['pro_sku'] != $sku_code[$i - 1]['pro_sku']) {
	     			$datalist[$datanum]['sku'] = $sku_code[$i]['pro_sku'];
	     			//$datalist[$datanum]['sellersku'] = $sku_code[$i]['pro_sku'];
	     			$datalist[$datanum]['name'] = $sku_code[$i]['product_name'];
	     			$tempnum = $datanum;
	     			$datanum++;
	     		}
	     		$datalist[$datanum]['sellersku'] = $sku_code[$i]['sku_code'];
	     		// 在途库存
	     		$temp = $amazon_res_arr[$sku_code[$i]['sku_code']][0] - $amazon_res_arr[$sku_code[$i]['sku_code']][1];
	     		$loadquantity += $temp;
	     		$datalist[$datanum]['LoadSupplyQuantity'] = $temp;
	     		// 可发库存
	     		$temp = $amazon_res_arr[$sku_code[$i]['sku_code']][1];
	     		$instockquantity += $temp;
	     		$datalist[$datanum]['InStockSupplyQuantity'] = $temp;

	     		$datanum++;
	     		if ($i == $length-1 || $sku_code[$i]['pro_sku'] != $sku_code[$i + 1]['pro_sku']) {
	     			$datalist[$tempnum]['LoadSupplyQuantity'] = $loadquantity;
	     			$datalist[$tempnum]['InStockSupplyQuantity'] = $instockquantity;
	     			$loadquantity = 0;
	     			$instockquantity = 0;
	     		}
	     	}

			$tablewidth = '800';
			$displayarr = array();
			$displayarr['sku'] 	    					= array('showname'=>'sku', 'width'=>'100');
			$displayarr['name']    						= array('showname'=>'产品名称', 'width'=>'300');
			$displayarr['sellersku'] 	    			= array('showname'=>'亚马逊sku', 'width'=>'200');
			$displayarr['LoadSupplyQuantity'] 	    	= array('showname'=>'在途库存', 'width'=>'100');
			$displayarr['InStockSupplyQuantity'] 	    = array('showname'=>'可发库存', 'width'=>'100');

			if (empty($pro_sku)) {
				$InitPHP_conf['pageval'] = 15;
				$sku_alias->select_pid_group_by_pid($sql);
			}
		}
		else {
			echo '<script>alert("输入正确sku！确认存在amazon别名！")</script>';
		}
	}

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}

/*模板定义*/
if($detail =='list'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}

?>

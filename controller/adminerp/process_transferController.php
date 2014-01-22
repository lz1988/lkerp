<?php
/**
 * Created on 2011-12-29
 * @title 转仓
 * @author by hanson
 */

 /*转仓单列表*/
 if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'statu-h-e'			=>'状态:',
		'sku-s-l'			=>'&nbsp; &nbsp; SKU：',
		'order_id-s-e'		=>'&nbsp; &nbsp; 订单号：',
		'fid-s-l'			=>'&nbsp; &nbsp; 运单编号：',
		'cuser-s-l'			=>'&nbsp; &nbsp; 制单人：',
		'comment2-s-l'		=>'&nbsp; &nbsp; 跟踪号：',
		'receiver_id-a-e'	=>' &nbsp;目的仓库：',
	);

	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
	$receiver_idarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$receiver_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

	/*标签导航选项*/
	$tab_menu_stypemu = array(
		'statu-0'=>'预出库',
		'statu-1'=>'已接收',
        'statu-2'=>'待发货',
		'statu-3'=>'已出库',
	);

	/*回退权限显示*/
	if ($statu == 1 || $statu == 2 || $statu == 3) {
		$retui = $this->C->service('admin_access')->checkResRight('r_p_backordertransfer');
	}

	if($sqlstr) $sqlstr = str_replace('cuser','p.cuser',$sqlstr);
    if($sqlstr) $sqlstr = str_replace('sku','p.sku',$sqlstr);

	/*初始打开默认显示预出库的*/
	if(empty($sqlstr) && !isset($statu)){$sqlstr = ' and statu="0" and output="0" '; $statu='0' ;}
	$sqlstr.=' and property="转仓单" and isover="N"';
	if($statu == '0') {$sqlstr.=' and output="0" '; $orders = ' order by p.order_id asc';}else{$orders = ' order by p.order_id desc';}

	/*分页参数,默认15,注意放在statu处理之后,查表之前*/
	$showperhtml = $this->C->service('warehouse')->perpage_show_html(array('0'=>'15','1'=>'50','2'=>'200','3'=>'1000'),$selfval_set,$statu);

	/*
	 * update on 2012-05-09
	 * by wall
	 * 工作提醒传过时间查找
	 * */
	if (!empty($job_alert_time)) {
		$sqlstr .= ' and p.rdate like "%'.$job_alert_time.'%" ' ;
		$pageshow = array('job_alert_time' => $job_alert_time);
	}
    
    //预估运费
    $shipping_fare  = $this->C->service('shipping_fare');
    $product        = $this->S->dao('product');
    $shipping_area  = $this->S->dao('shipping_area');
    $shipping       = $this->S->dao('shipping');
    $process        = $this->S->dao('process');
	$datalist		= $process->showtransfer($sqlstr,$orders);
	$shippingfeeArr = array();

	/*输出数组处理*/
	for($i=0;$i<count($datalist);$i++){

	/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd']	= '';
			$datalist[$i]['returnback']	= '';
			$datalist[$i]['editship']	= '';
			//$datalist[$i]['shipping_fee']= '';
		}else{
			$datalist[$i]['order_idd']	= '<a title="点击打包下载条形码" href="index.php?action=process_transfer&detail=downloadfile&type=all&order_id='.$datalist[$i]['order_id'].'" target="_blank">'.$datalist[$i]['order_id'].'</a>';
			$datalist[$i]['returnback']	= '<a href=\'javascript:void(0);delitem("index.php?action=process_transfer&detail=delout&order_id='.$datalist[$i]['order_id'].'","确定回退至预出库？")\' title="回退"><img src="./staticment/images/sysback.gif" border="0"></a>';
			$datalist[$i]['editship']	= '<a href="index.php?action=process_transfer&amp;detail=editorderse&amp;statu='.$statu.'&amp;order_id='.$datalist[$i]['order_id'].'" title="填写跟踪号、物流"><img src="./staticment/images/editbody.gif" border="0"></a>';
		}

		/*同一个跟踪号只显示一次费用*/
		/*if(!in_array($datalist[$i]['comment2'], $shippingfeeArr)){
			$shippingfeeArr[] = $datalist[$i]['comment2'];
		}else{
			$datalist[$i]['shipping_fee']= '';
		}*/
        
		$datalist[$i]['cancel']   		= '<a href=index.php?action=process_transfer&detail=cancel_order&order_id='.$datalist[$i]['order_id'].' ><font color=#828482>取消</font></a>';
		$datalist[$i]['comment2'] 		= empty($datalist[$i]['comment2'])?'--':$datalist[$i]['comment2'];
		$datalist[$i]['sold_way'] 		= empty($datalist[$i]['sold_way'])?'--':$datalist[$i]['sold_way'];
		$datalist[$i] 			  		= $this->C->service('warehouse')->decodejson($datalist,$i);
        
         //预估运费
        if ($statu == '2' || $statu == '3'){
            $weight= $product->D->select('shipping_weight','sku="'.$datalist[$i]['sku'].'"');
            $area_id        = $shipping_area->D->get_one_by_field(array('pinyin'=>$datalist[$i]['e_country']),'id');
            $e_shipping     = $shipping->D->get_one_by_field(array('s_name'=>$datalist[$i]['e_shipping']),'id');
            $datalist[$i]['shipping_fee_plan']           = $shipping_fare->getshipping_cost($weight['shipping_weight'],'18600000000',$area_id['id'],$e_shipping['id'],'CNY',$datalist[$i]['quantity']);
        }
        //运费反写
        if ($statu == '3'){
            // 运费反写 jerry 2013-05-21
           //单个sku的实重与体积重比较 大者除以sku对应物流追踪号对应sku的总重
           //单个sku的反写运费：sku重量比例*当前sku对应物流追踪号的运费
            /*if (!empty($datalist[$i]['box_product_dimensions'])){
                $box_product_dimensions = strtr($datalist[$i]['box_product_dimensions'],'x','*');
                eval("\$V = $box_product_dimensions;");
                $_weight = $V/5000;
            }*/
            
            $skuweight = ($datalist[$i]['sum_product_size'] > $datalist[$i]['sum_product_weight'])?$datalist[$i]['sum_product_size']:$datalist[$i]['sum_product_weight'];
            
            if ($skuweight){
                $firewritecost  = $process->showtransfer_sumfarewritecost_s($datalist[$i]['comment2']);
                //echo '<pre>';print_r($firewritecost);
                $sumweight      = $firewritecost['sumweight'];
                $shipping_fee   = $datalist[$i]['shipping_fee'];
                $skucost        = $skuweight/$sumweight;
                //echo '<pre>';echo $skuweight;
                //echo '<pre>';echo $sumweight.'sss';
                //echo '<prE>';echo $shipping_fee;
                //echo $skucost;
                $datalist[$i]['tariff_costs'] = number_format($skucost*$shipping_fee,2);
            }
        }
            
		$datalist[$i]['sku']			= '<a title="点击下载Sku条形码" href="index.php?action=process_transfer&detail=downloadfile&type=onlysku&fnsname='.$datalist[$i]['order_id'].'_'.$datalist[$i]['sku'].'_'.$datalist[$i]['quantity'].'&sname='.$datalist[$i]['sku'].'" target="_blank">'.$datalist[$i]['sku'].'</a>';
		$datalist[$i]['e_remeber_id']	= '<a title="点击下载FnSku条形码" href="index.php?action=process_transfer&detail=downloadfile&type=onlyfnsku&fnsname='.$datalist[$i]['order_id'].'_'.$datalist[$i]['e_remeber_id'].'_'.$datalist[$i]['quantity'].'&sname='.$datalist[$i]['e_remeber_id'].'" target="_blank">'.$datalist[$i]['e_remeber_id'].'</a>';
       

	}


	/*配置输出表单*/
	$displayarr = array();
	$tablewidth = '1500';

	$displayarr['order_id'] 	 = array('showname'=>'checkbox','width'=>'50','title'=>'反选');
	if($statu == '0'){
		$displayarr['both'] 	 = array('showname'=>'操作','width'=>'60','ajax'=>'1','url_e'=>'index.php?action=process_transfer&detail=editorder&id={id}','url_d'=>'index.php?action=process_transfer&detail=delshipment&id={id}');
		$displayarr['cancel']	 = array('showname'=>'取消','width'=>'50','title'=>'点击取消');
	}elseif(($statu == '1'|| $statu=='2' || $statu=='3') && $retui){
		$displayarr['returnback']= array('showname'=>'回退','width'=>'50');
		$displayarr['editship']	 = array('showname'=>'编辑','width'=>'50');
	}
	$displayarr['order_idd'] 	 = array('showname'=>'订单号','width'=>'80');
	$displayarr['sku'] 			 = array('showname'=>'sku','width'=>'100');
	$displayarr['product_name']  = array('showname'=>'产品名称','width'=>'220');
	$displayarr['prohouse'] 	 = array('showname'=>'发货仓库','width'=>'100');
	$displayarr['rechouse'] 	 = array('showname'=>'目的仓库','width'=>'100');
	$displayarr['e_unit_box'] 	 = array('showname'=>'单箱数量','width'=>'80');
	$displayarr['e_box'] 		 = array('showname'=>'箱数','width'=>'60');
	$displayarr['quantity'] 	 = array('showname'=>'总数','width'=>'60');
	if($statu == '1'|| $statu=='2' || $statu=='3'){
		$displayarr['comment2'] 	 = array('showname'=>'物流跟踪号','width'=>'100');
		//$displayarr['shipping_fee']	 = array('showname'=>'运费(CNY)','width'=>'100');
		$displayarr['e_shipping'] 	 = array('showname'=>'发货方式','width'=>'100');
	}
    if ($statu == '2' || $statu == '3'){
        
        $displayarr['shipping_fee_plan']	 = array('showname'=>'预估运费(CNY)','width'=>'100');
    }
    if ($statu == '3'){
        $displayarr['shipping_farerewrite']  = array('showname'=>'反写运费(CNY)','width'=>'100');
        $displayarr['tariff_costs']          = array('showname'=>'反写关税(CNY)','width'=>'100');
    }
	$displayarr['fid'] = array('showname'=>'运单编号','width'=>'100');
	$displayarr['e_remeber_id']	 = array('showname'=>'助记码','width'=>'100');
    
    $displayarr['e_tel'] 		 = array('showname'=>'联系电话','width'=>'100');
	$displayarr['e_address1'] 	 = array('showname'=>'地址1','width'=>'150');
	$displayarr['e_address2'] 	 = array('showname'=>'地址2','width'=>'150');
	$displayarr['e_city'] 		 = array('showname'=>'城市','width'=>'80');
	$displayarr['e_state'] 		 = array('showname'=>'洲','width'=>'50');
	$displayarr['e_country'] 	 = array('showname'=>'国家','width'=>'80');
	$displayarr['e_post_code'] 	 = array('showname'=>'邮编','width'=>'80');
    $displayarr['e_receperson']  = array('showname'=>'收件人','width'=>'80');
    $displayarr['e_company'] 	 = array('showname'=>'收件公司','width'=>'80');
    
	$displayarr['cuser'] 		 = array('showname'=>'制单人','width'=>'80');
	$displayarr['comment'] 		 = array('showname'=>'备注','width'=>'100');
	$displayarr['cdate'] 		 = array('showname'=>'制单日期','width'=>'95');
	if($statu == '1'){$displayarr['muser'] 		 = array('showname'=>'接单人','width'=>'100');}

 	$this->V->mark(array('title'=>'物料调拨'));
	$temp = 'pub_list';

	/*数据流操作按钮*/
	$this->C->service('global')->disconnect_modbutton(array('0'=>&$mod_disabled_0,'1'=>&$mod_disabled_1,'1-3'=>&$mod_disabled_2,'2'=>&$mod_disabled_2_3),$statu);

	$bannerstr = '<button onclick=window.location="index.php?action=process_transfer&detail=addorder" '.$mod_disabled_0.'>添加订单</button>';
	$bannerstr.= '<button onclick=window.location="index.php?action=process_transfer&detail=showimport" '.$mod_disabled_0.'>导入订单</button>&nbsp;&nbsp;';
	$bannerstr.= '<button onclick=combine() '.$mod_disabled_0.'>合并订单</button>';					 //(此处与销售下单共用detail)
	$bannerstr.= '<button onclick=celcombine() '.$mod_disabled_0.'>取消合并</button>&nbsp;&nbsp;';//(此处与销售下单共用detail)
	$bannerstr.= $showperhtml;
    $bannerstr.= '<br><button onclick=print_table_detail("print_detail") '.$mod_disabled_0.' >打印明细</button>'; //(打印物料调配发货单明细表)
	$bannerstr.= '<button onclick=receselecttransfer() '.$mod_disabled_0.'>接收选中</button>&nbsp;&nbsp;';				 //(此处与销售下单共用detail)
    $bannerstr.= '<button onclick=sureprint(1) '.$mod_disabled_1.' >等待发货</button>';
	$bannerstr.= '<button onclick=sureoutseleave() '.$mod_disabled_2_3.' >确认出货</button>';

	$bannerstrarr[] = array('url'=>'index.php?action=process_transfer&detail=output','value'=>'导出数据','extra'=>$mod_disabled_2);

	$jslink = "<script src='./staticment/js/process_transfer.js?version=".time()."'></script>\n";
	$jslink.= "<script src='./staticment/js/process_shipment.js?version=".time()."'></script>\n";

 }

/*已接收的导出*/
elseif($detail == 'output'){

	$sqlstr		.= ' and property="转仓单" and isover="N"';
	$datalist 	 = $this->S->dao('process')->showtransfer($sqlstr,' order by p.order_id desc ');
	$whouse		 = $this->C->service('warehouse');

	/*输出数组处理*/
	for($i=0;$i<count($datalist);$i++){
		$datalist[$i] 			  		= $whouse->decodejson($datalist,$i);
	}

	$filename	 = 'fba_'.date('Y-m-d',time()).'_'.mt_rand(00,99);
	$head_array  = array('order_id'=>'单号','sku'=>'sku','product_name'=>'产品名称','rechouse'=>'目的地','e_unit_box'=>'单箱数量','e_box'=>'箱数','quantity'=>'总数','fid'=>'运单号','e_remeber_id'=>'助记码','comment2'=>'物流跟踪号','e_shipping'=>'发货方式','e_shipping_fee'=>'运费(CNY)','cuser'=>'制单','cdate'=>'日期','comment'=>'备注');

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

/*取消订单*/
elseif($detail == 'cancel_order'){

    $_cuser = $this->S->dao('process')->D->get_one(array('order_id'=>$order_id),'cuser');
    if(!$this->C->service('admin_access')->checkResRight('r_p_editorder','mod',$_cuser)){$this->C->sendmsg();}
	/*取消原因数组*/
	$reason_datastr = $this->C->service('warehouse')->get_canceltype(2,'comment3');


	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=process_transfer&detail=modcancel_order');

	$disinputarr = array();
	$disinputarr['order_idd'] = array('showname'=>'订单号','value'=>$order_id,'inextra'=>'disabled');
	$disinputarr['order_id'] = array('showname'=>'订单号','value'=>$order_id,'datatype'=>'h');
	$disinputarr['comment3'] = array('showname'=>'取消原因','datatype'=>'se','datastr'=>$reason_datastr);

	$this->V->view['title'] = '取消订单-物料调拨(list)';
	$temp = 'pub_edit';
}

/*执行取消订单*/
elseif($detail == 'modcancel_order'){

	if($comment3 == '') {$this->C->success('请选择原因','index.php?action=process_transfer&detail=cancel_order&order_id='.$order_id);exit;}

	$sid = $this->S->dao('process')->D->update_by_field(array('order_id'=>$order_id),array('isover'=>'Y','comment3'=>$comment3));
	if($sid){$this->C->success('保存成功，已经搁置异常订单','index.php?action=process_transfer&detail=list');}
}

/*导入订单表格*/
elseif($detail == 'showimport'){
    //权限控制
    if(!$this->C->service('admin_access')->checkResRight('r_p_addorder')){$this->C->sendmsg();} 
	//取上传的文件的数组
	$upload_dir 	= "./data/uploadexl/order_transfer/";//上传文件保存路径的目录
	$fieldarray 	= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R');//有效的excel列表值
	$head 			= 1;//以第一行为表头

	$sku_assembly 	= $this->S->dao('sku_assembly');

	$tablelist 		= '';

	/*读取已经上传的文件*/
	if($filepath){
		$all_arr =  $this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);

		/*根据名称获取仓库ID,检测是否发货仓与收货仓列的每行都相同*/
		$no_samew = 0;
		for($i=1;$i<count($all_arr);$i++){
			if($i>1 && ($all_arr[$i]['provider_warehouse'] != $all_arr[$i-1]['provider_warehouse'] || $all_arr[$i]['receiver_warehouse'] != $all_arr[$i-1]['receiver_warehouse'])) $no_samew++;
		}


		/*相同则只查两次表*/
		if($no_samew == '0'){
			$provider_id = $this->S->dao('esse')->D->select('id','name="'.$all_arr['1']['provider_warehouse'].'"');
			$receiver_id = $this->S->dao('esse')->D->select('id','name="'.$all_arr['1']['receiver_warehouse'].'"');
			foreach ($all_arr as &$val){
				$val['provider_warehouse'] = $provider_id['id'];
				$val['receiver_warehouse'] = $receiver_id['id'];
			}
		}

		/*不同则多少行查多少次*2次表*/
		else{
			for($i=1;$i<count($all_arr);$i++){
				$provider_id = $this->S->dao('esse')->D->select('id','name="'.$all_arr[$i]['provider_warehouse'].'"');
				$receiver_id = $this->S->dao('esse')->D->select('id','name="'.$all_arr[$i]['receiver_warehouse'].'"');
				$all_arr[$i]['provider_warehouse'] = $provider_id['id'];
				$all_arr[$i]['receiver_warehouse'] = $receiver_id['id'];
			}
		}

		$process  		= $this->S->dao('process');
		$max 			= $this->C->service('warehouse')->get_maxorder('转仓单','f',$process);/*获得最大转仓单号*/

		/*失败统计量*/
		$failedcount 	= 0;
		$setdatatime 	= date('Y-m-d H:i:s',time());

		//实例化自动包含文件
		$this->C->service('exchange_rate');
		$finansvice 	= $this->C->service('finance');

		$process->D->query('begin');

		/*以$all_arr为数组参考*/
		for($i=1;$i<count($all_arr);$i++){

			$order_id = 'f'.sprintf("%07d",substr($max,1)+$i-1);
			$extends = array('e_unit_box'=>$all_arr[$i]['unit_box'],'e_box'=>$all_arr[$i]['box'],'e_remeber_id'=>$all_arr[$i]['remeber_id'],'e_tel'=>$all_arr[$i]['tel'],'e_address1'=>str_replace('"','',$all_arr[$i]['address1']),'e_address2'=>str_replace('"','',$all_arr[$i]['address2']),'e_city'=>$all_arr[$i]['city'],'e_state'=>$all_arr[$i]['state'],'e_country'=>$all_arr[$i]['country'],'e_post_code'=>$all_arr[$i]['post_code'],'e_receperson'=>$all_arr[$i]['receperson'],'e_company'=>$all_arr[$i]['company']);

			/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
			$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

			/*取PID与产品名称*/
			$backpidpname[$i] = $process->D->get_one_sql('select pid,product_name from product where sku="'.trim($all_arr[$i]['sku']).'"');
            
			/*获得产品ID，没有获取PID的不让提交*/
			if($backpidpname[$i]['pid']){
				$all_arr[$i]['pid'] 			= $backpidpname[$i]['pid'];
				$all_arr[$i]['product_name'] 	= $backpidpname[$i]['product_name'];
			}else{
				$process->D->query('rollback');
				$this->C->success('保存失败，存在无法获取产品ID的SKU:'.$all_arr[$i]['sku'].'，请检查重试!','index.php?action=process_transfer&detail=showimport');
				exit();
			}

			$insert_arr = array('provider_id'=>$all_arr[$i]['provider_warehouse'],'receiver_id'=>$all_arr[$i]['receiver_warehouse'],'fid'=>$all_arr[$i]['shipping_id'],'cdate'=>$setdatatime,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'mdate'=>$setdatatime,'ruser'=>$_SESSION['eng_name'],'rdate'=>$setdatatime,'order_id'=>$order_id,'property'=>'转仓单','extends'=>$extends,'comment'=>$all_arr[$i]['comment']);
 
			/*检测是否组装SKU，组装SKU自动提取原SKU分别保存，并用同一个单号*/
			$backassem 	= $sku_assembly->get_sonlist(' and s.pid='.$all_arr[$i]['pid']); 
			if($backassem){
				foreach($backassem as $vals){
					$insert_arr['sku'] 			= $vals['sku'];
					$insert_arr['pid'] 			= $vals['child_pid'];
					$insert_arr['product_name'] = $vals['product_name'];
					$insert_arr['quantity'] 	= $vals['quantity']*$all_arr[$i]['quantity'];
                    $finansvice->rewrite_inorup_arr(&$insert_arr,$vals['child_pid']);
                    $sid = $process->D->insert($insert_arr);
					if(!$sid) $failedcount++;
				}
			}else{
					$insert_arr['sku'] 			= $all_arr[$i]['sku'];
					$insert_arr['pid'] 			= $all_arr[$i]['pid'];
					$insert_arr['product_name'] = $all_arr[$i]['product_name'];
					$insert_arr['quantity'] 	= $all_arr[$i]['quantity'];
                    /*增加保存即时成本，期号，币别(CNY)*/
					$finansvice->rewrite_inorup_arr(&$insert_arr,$all_arr[$i]['pid']);
                    $sid = $process->D->insert($insert_arr);
				 	if(!$sid) $failedcount++;
			}
		}
        
		$jumpurl = 'index.php?action=process_transfer&detail=list';
	 	if(empty($failedcount)) {$process->D->query('commit');$this->C->success('添加成功',$jumpurl);}else{$process->D->query('rollback');$this->C->success('添加失败',$jumpurl);}


	}else{

		$all_arr 		= $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath 		= $this->getLibrary('basefuns')->getsession('filepath');
		$product		= $this->S->dao('product');
		$process		= $this->S->dao('process');
		$data_error 	= 0;
		$tablelist 		= '';
		$cur_sku_count 	= array();
		$tablelist 	   .= '<table id="mytable">';
		/*表头特殊显示处理*/
		$tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'order_transfer');
		/*实体内容处理*/
		foreach($all_arr as $k=>$val){
            
			$error_stockhouse = '';
			$tablelist 		 .= '<tr>';
			$skutips		  = '';
			/*检查SKU是否组装*/
			$backchecksku = $product->D->get_one_by_field(array('sku'=>$val['sku']),'pid');
			if($backchecksku['pid']){
				$backassem = $sku_assembly->get_sonlist(' and s.pid='.$backchecksku['pid']);
				if($backassem){
					/*检测是否存在多种方案*/
					for($ass = 1 ; $ass < count($backassem); $ass++){
						if($backassem[$ass]['assembleid'] != $backassem[$ass-1]['assembleid']){ $skutips = 'title=该SKU存在多种组装方案，请检查，系统只允许一种！ bgcolor=red ';$data_error++;}
					}
                    /*给出提醒。*/
					$skutips .= ' title="组装的SKU，提交后会系统将自动提取原SKU替代后保存 " bgcolor=green ';
				}
			}

			/*检查是否在当前的仓库表中*/
			$whouse_id   = $this->S->dao('esse')->D->select('id','name="'.$val['receiver_warehouse'].'" ');
			$whouse_id_t = $this->S->dao('esse')->D->select('id','name="'.$val['provider_warehouse'].'" ');
            
			if(!$whouse_id[0]){$error_whouse = 1;}else{$error_whouse = 0;}
			if(!$whouse_id_t[0]){$error_whouse_t = 1;}else{$error_whouse_t = 0;}
            
            if ($whouse_id_t[0] == TRANSFER_HOUSE){
    			/*检测库存是否充足*/
    			if($whouse_id[0] && $whouse_id_t[0]){
                    /*组装的SKU，提取原SKU判断逐一判断库存*/
    				if($backassem){
    					$this_assm_skutips = '';
    					for($ass = 0 ; $ass < count($backassem); $ass++){
                            $cur_sku_count[$whouse_id_t[0]][$backassem[$ass]['sku']] += ($backassem[$ass]['quantity']*$val['quantity']);//累计已发数量
                            $back_enough = $process->get_allw_allsku(' and temp.sku="'.$backassem[$ass]['sku'].'" and temp.wid='.$whouse_id_t[0]);
                            if($back_enough['0']['sums'] < $cur_sku_count[$whouse_id_t[0]][$backassem[$ass]['sku']]){
    							$this_assm_skutips .='SKU：'.$backassem[$ass]['sku'].'库存不足，可发'.$back_enough['0']['sums'].'个。';
    						}
    					}
    					$this_assm_skutips_s = empty($this_assm_skutips)?'':'这是组装产品，提取出的'.$this_assm_skutips;
                        if($this_assm_skutips_s){
    						$error_stockhouse = ' bgcolor="red" title="'.$this_assm_skutips_s.'"';
    						$data_error++;
    					}
    				}
    
    				/*非组装SKU，单一判断*/
    				else{
                        $cur_sku_count[$whouse_id_t[0]][$val['sku']]+= $val['quantity'];//累计已发数量
                        //echo '<pre>';print_r($cur_sku_count);
                        $back_enough = $process->get_allw_allsku(' and temp.sku="'.$val['sku'].'" and temp.wid='.$whouse_id_t[0]);
                        if($back_enough['0']['sums'] < $cur_sku_count[$whouse_id_t[0]][$val['sku']]){
    						$back_enough['0']['sums'] = empty($back_enough['0']['sums'])?'0':$back_enough['0']['sums'];
    						$error_stockhouse = ' bgcolor="red" title="库存不足,库存可发数'.$back_enough['0']['sums'].'"';
    						$data_error++;
    					}
    				}
    			}
            }
			if( is_array($val) ){
				foreach( $val as $j=>$value) {
					$error_style = '';
					if($j == 'receiver_warehouse' && $error_whouse){
							$error_style = ' bgcolor="red" title="仓库名称有误或者不在仓库列表中，请核对！"';
							$data_error++;
					}

					if($j == 'provider_warehouse' && $error_whouse_t){
							$error_style = ' bgcolor="red" title="仓库名称有误或者不在仓库列表中，请核对！"';
							$data_error++;
					}

					/*检查SKU是否合法*/
					if($j == 'sku'){
						 if(!preg_match("/(^(\d)+-\d+-(\d+)$)|(^(\d)+-\d+-\d+-(\w+)$)/",$value)){
							$error_style = ' bgcolor="red" title="SKU格式不对,格式如(236-41-48或者236-41-48-CD001)"';
							$data_error++;
						 }elseif($skutips){
						 	$error_style = $skutips;
						 }
					}
                    if($j == 'quantity'){
						if(empty($value) || !preg_match('/^[\d]*$/',$value)  || $value<0){
							$error_style = ' bgcolor="red" title="请检查数量！"';
							$data_error++;
						}
					}
                    if($j == 'tel'){
                        if(empty($value) || !preg_match('/^[\d]*$/',$value)){
							$error_style = ' bgcolor="red" title="电话必须是数字！"';
							$data_error++;
						}
                    }
                    if($j == 'tel'){
                        if(empty($value) || !preg_match('/^[\d]*$/',$value)){
							$error_style = ' bgcolor="red" title="电话必须是数字！"';
							$data_error++;
						}
                    }
                    if($j == 'address1'){
                        if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
                    }
                    if($j == 'city'){
                        if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
                    }
                    if($j == 'state'){
                        if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
                    }
                    if($j == 'country'){
                        if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
                    }
                    if($j == 'post_code'){
                        if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
                    }
                    if($j == 'receperson'){
                        if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
                    }
                    if($j == 'company'){
                        if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
                    } 
                    $tablelist .= '<td '.$error_style.$error_stockhouse.'>&nbsp;'.$value.'</td>';
				}
			}
			$tablelist .= '</tr>';
		}
		$tablelist .= '</table>';

		/*错误判断*/
		if(!$data_error && isset($all_arr)){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="submit" value="确认并提交"><input type="reset" value="取消" onclick=window.location="index.php?action=process_transfer&detail=list">';
		}elseif($data_error){

			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}
	}

	$submit_action = 'index.php?action=process_transfer&detail=showimport';
	$temlate_exlurl = 'data/uploadexl/sample/order_transfer.xls';
    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('exl_error_msg'=>$exl_error_msg,'exl_error_width'=>600,'title'=>'导入订单-物料调拨(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}

/*物流填写发货方式，跟踪号，*/
elseif($detail == 'editorderse'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_receoutw')){$this->C->sendmsg();}//填写的权限判断

	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=process_transfer&detail=modeditorderse','width'=>'750');

	$backdata = $this->S->dao('process')->D->get_one(array('order_id'=>$order_id),'comment2,extends,product_dimensions,product_weight,product_size,difference_weight,sum_product_weight,sum_product_size');
	$extends  = json_decode($backdata['extends'],true);//先取出原来的扩展内容。

	/*生成发货方式下拉*/
	$e_shippingstr =  $this->C->service('global')->get_shipping('e_shipping','s_name','s_name','s_name',$extends['e_shipping']);

	$disinputarr = array();
	$colwidth	 = array(105,130,500);

	$disinputarr['order_id']		      = array('showname'=>'id','datatype'=>'h','value'=>$order_id);
	$disinputarr['order_idd']		      = array('showname'=>'单号','value'=>$order_id,'inextra'=>'disabled');
	$disinputarr['e_shipping'] 		      = array('showname'=>'发货方式','datatype'=>'se','datastr'=>$e_shippingstr);
	$disinputarr['comment2'] 		      = array('showname'=>'物流跟踪号','value'=>$backdata['comment2']);
    $disinputarr['product_dimensions']    = array('showname'=>'箱子尺寸(长×宽×高)','value'=>$backdata['product_dimensions'],'extra'=>'/cm 每输入完一个按*号自动显示“x”','inextra'=>"onkeyup=getkey('product_dimensions')");
    $disinputarr['product_weight']        = array('showname'=>'每箱实重','value'=>$backdata['product_weight'],'extra'=>'/kg');
    $disinputarr['product_size']          = array('showname'=>'每箱体积重','value'=>$backdata['product_size'],'extra'=>'/kg');
    $disinputarr['difference_weight']     = array('showname'=>'实重和体积重差异','value'=>$backdata['difference_weight'],'extra'=>'/kg');
    $disinputarr['sum_product_weight']    = array('showname'=>'总重量','value'=>$backdata['sum_product_weight'],'inextra'=>'class="check_notnull Check_isnum_dd2"','extra'=>'/kg(<font color="red">*必填</font>)');
    $disinputarr['sum_product_size']      = array('showname'=>'总体积重','value'=>$backdata['sum_product_size'],'inextra'=>'class="check_notnull Check_isnum_dd2"','extra'=>'/kg(<font color="red">*必填</font>)');
	$disinputarr['statu']	 		      = array('showname'=>'状态','value'=>$statu,'datatype'=>'h');//修改后跳转的状态

    $jslink = '<script charset="utf-8" src="./staticment/js/shippingmod.js"></script>';
    
	$this->V->view['title']='物流编辑-物料调拨(list)';
	$temp = 'pub_edit';

}


/*执行保存后期编辑出货单*/
elseif($detail == 'modeditorderse'){
	if(!$this->C->service('admin_access')->checkResRight('r_w_receoutw')){$this->C->sendmsg();}//保存的权限判断

	$process  = $this->S->dao('process');
	$backlist = $process->D->get_allstr(' and order_id="'.$order_id.'"','','','id');
	$errornum = 0;

	/*更新整个订单*/
	for($i = 0; $i<count($backlist); $i++){

		$backdata = $process->D->get_one(array('id'=>$backlist[$i]['id']),'comment2,extends');
		$extends  = json_decode($backdata['extends'],true);//先取出原来的扩展内容。

		/*扩展内容增加*/
		$extends['e_shipping'] 		= $e_shipping;


		/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
		$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);
		$sid = $process->D->update(array('id'=>$backlist[$i]['id']),array('comment2'=>$comment2,'extends'=>$extends,'product_dimensions'=>$product_dimensions,'product_weight'=>$product_weight,'product_size'=>$product_size,'difference_weight'=>$difference_weight,'sum_product_weight'=>$sum_product_weight,'sum_product_size'=>$sum_product_size));
		if(!$sid) $errornum++;
	}

	$jurl = 'index.php?action=process_transfer&detail=list&statu='.$statu;

	if(empty($errornum)) {$this->C->success('保存成功',$jurl);}else{$this->C->success('保存失败',$jurl);};
}


elseif($detail == 'addorder'){

    if(!$this->C->service('admin_access')->checkResRight('r_p_addorder')){$this->C->sendmsg();}
	/*分别生成供需仓库下拉,新增的，默认发货仓是中国仓，收货仓是amazon-US*/

	$backdata_p = $this->C->service('warehouse')->get_whouse('provider_id[]','name','id','id',$this->C->service('global')->sys_settings('maketransfer_house','sys'));
	$backdata_r = $this->C->service('warehouse')->get_whouse('receiver_id[]','name','id','id',$this->C->service('global')->sys_settings('recetransfer_house','sys'));

	$this->V->mark(array('title'=>'添加订单-物料调拨(list)','backdata_p'=>$backdata_p,'backdata_r'=>$backdata_r));
	$this->V->set_tpl('adminweb/process_transfer_addorder');
	display();
}

 /*编辑订单*/
elseif($detail == 'editorder'){


	$backdata = $this->S->dao('process')->D->get_one_by_field(array('id'=>$id),'pid,sku,fid,product_name,provider_id,receiver_id,quantity,comment,extends,cuser');

    if(!$this->C->service('admin_access')->checkResRight('r_p_editorder','mod',$backdata['cuser'])){$this->C->sendmsg();}

	$backdata = $this->C->service('warehouse')->decodejson($backdata);

	/*分别生成供需仓库下拉,编辑的，则默认取选中的*/
	//$backdata_p = $this->C->service('warehouse')->get_whouse('provider_id','name','id','id',$backdata['provider_id']);
	$backdata_r = $this->C->service('warehouse')->get_whouse('receiver_id','name','id','id',$backdata['receiver_id']);

	$backdatahouse = $this->S->dao('esse')->D->get_one_by_field(array('id'=>$backdata['provider_id']),'name');

	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=process_transfer&detail=modeditorder');

	$disinputarr['id'] 				= array('showname'=>'id','datatype'=>'h','value'=>$id);
	$disinputarr['pid'] 			= array('showname'=>'产品id','datatype'=>'h','value'=>$backdata['pid']);
	$disinputarr['sku'] 			= array('showname'=>'产品SKU','inextra'=>'onblur=get_productmsg()','value'=>$backdata['sku']);
	$disinputarr['old_sku']			= array('showname'=>'旧SKU','datatype'=>'h','value'=>$backdata['sku']);
	$disinputarr['old_quantity']	= array('showname'=>'旧数量','datatype'=>'h','value'=>$backdata['quantity']);

	$disinputarr['product_name'] 	= array('showname'=>'产品名称','value'=>$backdata['product_name']);
	//$disinputarr['provider_id'] 	= array('showname'=>'发货仓库','datatype'=>'se','datastr'=>$backdata_p);
	$disinputarr['warehouse'] 		= array('showname'=>'发货仓库','value'=>$backdatahouse['name'],'inextra'=>'disabled','showtips'=>'<span class=tips>此项只供查看</span>');
	$disinputarr['receiver_id'] 	= array('showname'=>'目的仓库','datatype'=>'se','datastr'=>$backdata_r);
	$disinputarr['e_unit_box'] 		= array('showname'=>'单箱数量','inextra'=>'onkeyup=count_sum()','value'=>$backdata['e_unit_box']);
	$disinputarr['e_box'] 			= array('showname'=>'箱数','inextra'=>'onkeyup=count_sum()','value'=>$backdata['e_box']);
	$disinputarr['quantity'] 		= array('showname'=>'总数量','value'=>$backdata['quantity']);
	$disinputarr['shipping_id']		= array('showname'=>'运单编号','value'=>$backdata['fid']);
	$disinputarr['e_remeber_id']	= array('showname'=>'助记码','value'=>$backdata['e_remeber_id']);
    
    $disinputarr['e_tel'] 			= array('showname'=>'电话','value'=>$backdata['e_tel'],'extra'=>'*','inextra'=>'class="check_notnull Check_isnum_dda"');
    $disinputarr['e_address1'] 		= array('showname'=>'地址一','value'=>$backdata['e_address1'],'extra'=>'*','inextra'=>'class=check_notnull');
	$disinputarr['e_address2'] 		= array('showname'=>'地址二','value'=>$backdata['e_address2']);
	$disinputarr['e_city'] 			= array('showname'=>'城市','value'=>$backdata['e_city'],'extra'=>'*','inextra'=>'class=check_notnull');
	$disinputarr['e_state'] 		= array('showname'=>'洲','value'=>$backdata['e_state'],'extra'=>'*','inextra'=>'class=check_notnull');
	$disinputarr['e_country'] 		= array('showname'=>'国家','value'=>$backdata['e_country'],'extra'=>'*','inextra'=>'class=check_notnull');
    $disinputarr['e_post_code'] 	= array('showname'=>'邮编','value'=>$backdata['e_post_code'],'extra'=>'*','inextra'=>'class=check_notnull');
    $disinputarr['e_receperson'] 	 = array('showname'=>'收件人','value'=>$backdata['e_receperson'],'extra'=>'*','inextra'=>'class=check_notnull');
    $disinputarr['e_company'] 	 = array('showname'=>'收件公司','value'=>$backdata['e_company'],'extra'=>'*','inextra'=>'class=check_notnull');
	
	$disinputarr['comment']			= array('showname'=>'备注','value'=>$backdata['comment']);

	$jslink = "<script src='./staticment/js/process_transfer.js'></script>\n";
	$jslink.= "<script src='./staticment/js/jquery.js'></script>\n";
	$this->V->mark(array('title'=>'编辑订单-物料调拨(list)'));
	$temp = 'pub_edit';
 }

 /*执行保存编辑订单,权限，只能修改自己的*/
 elseif($detail == 'modeditorder'){

    //权限控制
    $process  = $this->S->dao('process');
	$backdata = $process->D->get_one(array('id'=>$id),'cuser,provider_id');
    if(!$this->C->service('admin_access')->checkResRight('r_p_editorder','mod',$backdata['cuser'])){$this->C->sendmsg();}

 	if(empty($sku)){$this->C->sendmsg('无效的SKU!');}
 	$backpid  = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name');
 	if(!$backpid['pid']){$this->C->sendmsg('修改失败，系统不存在的SKU!');}


	/*修改了SKU，重新判断库存*/
	if($sku != $old_sku){
		$back_enough = $process->get_allw_allsku(' and temp.sku="'.$sku.'" and temp.wid='.$backdata['provider_id']);
		if($back_enough['0']['sums'] < $quantity){
			$this->C->success('修改失败，库存不足','index.php?action=process_transfer&detail=editorder&id='.$id);
			exit();
		}
	}

	/*只是修改了数量，重新判断库存，记住加上本单数量作可发库存*/
	elseif($old_quantity != $quantity){
		$back_enough = $process->get_allw_allsku(' and temp.sku="'.$sku.'" and temp.wid='.$backdata['provider_id']);
		if($back_enough['0']['sums']+$old_quantity < $quantity){
			$this->C->success('修改失败，库存不足','index.php?action=process_transfer&detail=editorder&id='.$id);
			exit();
		}
	}

	$this->C->service('exchange_rate');//实例化自动包含文件
	$global_service = $this->C->service('global');
	$finansvice 	= $this->C->service('finance');
	$exchange_rate	= $this->S->dao('exchange_rate');

	$extends = array('e_unit_box'=>$e_unit_box,'e_box'=>$e_box,'e_remeber_id'=>$e_remeber_id,'e_tel'=>$e_tel,'e_address1'=>$e_address1,'e_address2'=>$e_address2,'e_city'=>$e_city,'e_state'=>$e_state,'e_country'=>$e_country,'e_post_code'=>$e_post_code,'e_receperson'=>$e_receperson,'e_company'=>$e_company);

	/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
	$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

	/*更新内容数组*/
	$update_arr = array('fid'=>$shipping_id,'receiver_id'=>$receiver_id,'sku'=>$sku,'pid'=>$backpid['pid'],'product_name'=>$backpid['product_name'],'quantity'=>$quantity,'extends'=>$extends,'comment'=>$comment);


	/*保存即时成本，期号，币别*/
	$system_defaultcoin			= $global_service->get_system_defaultcoin();//本位币
	$backdata_stagerate			= $exchange_rate->D->get_one_by_field(array('code'=>$system_defaultcoin,'isnew'=>1),'stage_rate');//期号
	$update_arr['coin_code'] 	= $system_defaultcoin;
	$update_arr['stage_rate']	= $backdata_stagerate['stage_rate'];
	$update_arr['price2']		= $finansvice->get_productcost($backpid['pid']);//通过PID获取成本(CNY)


	$sid = $process->D->update_by_field(array('id'=>$id),$update_arr);
	if($sid) $this->C->success('修改成功','index.php?action=process_transfer&detail=list');

 }


 /*执行保存添加订单*/
 elseif($detail == 'modaddorder'){
	$process  = $this->S->dao('process');
	$max = $this->C->service('warehouse')->get_maxorder('转仓单','f',$process);/*获得最大转仓单号*/

	/*成功统计量*/
	$successcout = 0;

	$error_sku	 = '';
	for ($i=0;$i<count($sku);$i++){
		if(empty($pid[$i]))	$error_sku .=  $sku[$i].',\n';
	}
	if($error_sku){
		$this->C->success('保存失败，存在无法获取产品ID的SKU:\n'.$error_sku.'请检查重试!','index.php?action=process_transfer&detail=addorder');
		exit();
	}

	$setdatatime 	= date('Y-m-d H:i:s',time());
	$this->C->service('exchange_rate');//实例化自动包含文件
	$global_service = $this->C->service('global');
	$finansvice 	= $this->C->service('finance');
	$exchange_rate	= $this->S->dao('exchange_rate');
    $sku_assembly   = $this->S->dao('sku_assembly');

	$process->D->query('begin');
	/*以SKU为数组参考*/
	for($i=0;$i<count($sku);$i++){

		$order_id = 'f'.sprintf("%07d",substr($max,1)+$i);
		$extends = array('e_unit_box'=>$e_unit_box[$i],'e_box'=>$e_box[$i],'e_remeber_id'=>$e_remeber_id[$i],'e_tel'=>$e_tel[$i],'e_address1'=>$e_address1[$i],'e_address2'=>$e_address2[$i],'e_city'=>$e_city[$i],'e_state'=>$e_state[$i],'e_country'=>$e_country[$i],'e_post_code'=>$e_post_code[$i],'e_receperson'=>$e_receperson[$i],'e_company'=>$e_company[$i]);

		/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
		$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);
        /*取PID与产品名称*/
		$backpidpname[$i] = $process->D->get_one_sql('select pid,product_name from product where sku="'.trim($sku[$i]).'"');

		/*获得产品ID，没有获取PID的不让提交*/
        if($backpidpname[$i]['pid']){
			$pid[$i] 			= $backpidpname[$i]['pid'];
			$product_name[$i] 	= $backpidpname[$i]['product_name'];
		}else{
			$process->D->query('rollback');
			$this->C->success('保存失败，存在无法获取产品ID的SKU:'.$sku[$i]['sku'].'，请检查重试!','index.php?action=process_transfer&detail=addorder');
			exit();
		}

		$insert_arr = array('provider_id'=>$provider_id[$i],'receiver_id'=>$receiver_id[$i],'fid'=>$shipping_id[$i],'product_name'=>$product_name[$i],'quantity'=>$quantity[$i],'cdate'=>$setdatatime,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'mdate'=>$setdatatime,'ruser'=>$_SESSION['eng_name'],'rdate'=>$setdatatime,'order_id'=>$order_id,'property'=>'转仓单','extends'=>$extends,'comment'=>$comment[$i]);
        /********************start***********************************/
        $backassem 							= $sku_assembly->get_sonlist(' and s.pid='.$pid[$i]);
			if($backassem){

				foreach($backassem as $vals){
					$insert_arr['sku'] 			= $vals['sku'];
					$insert_arr['pid'] 			= $vals['child_pid'];
					$insert_arr['product_name'] = $vals['product_name'];
					$insert_arr['quantity'] 	= $vals['quantity']*$quantity[$i];
                    /*增加保存即时成本，期号，币别(CNY)*/
					$finansvice->rewrite_inorup_arr(&$insert_arr,$vals['child_pid']);

					$sid = $process->D->insert($insert_arr);
					if(!$sid) $failcout++;

				}

			}else{
                    $insert_arr['pid']         = $pid[$i];
                    $insert_arr['sku']         = $sku[$i];
                   	/*增加保存即时成本，期号，币别(CNY)*/
					$finansvice->rewrite_inorup_arr(&$insert_arr,$pid[$i]);

				 	$sid = $process->D->insert($insert_arr);
				 	if(!$sid) $failcout++;
			}

       /********************end*********************************/
	}

	$jumpurl = 'index.php?action=process_transfer&detail=list';
 	if(!$failcout) {$process->D->query('commit');$this->C->success('添加成功',$jumpurl);}else{$process->D->query('rollback');$this->C->success('添加失败',$jumpurl);}
 }
/*
 * create on 2012-05-04
 * by wall
 * 判断库存是否充足
 * 传入数组可能有重复SKU
 * */
elseif ($detail == 'check_quantity') {
	$length = count($sku);
	$skuquantity = array();
	$result = array();
    $arysku = array();
    $cur_sku_count = array();
    $product      = $this->S->dao('product');
    $sku_assembly = $this->S->dao('sku_assembly');
    $esse         = $this->S->dao('esse');
    $process      = $this->S->dao('process');
    $cur_sku      = array();
	if ( $length == 0 ) {
		$result['msg'] = '请输入数据！不允许提交空数据！';
	}
	else {
		for($i=0; $i<$length; $i++) {
			$tempsku = $sku[$i];
            $temhouseid = $houseid[$i];
			$rs = $this->S->dao('product')->get_product_by_sku($tempsku);

			if ($rs) {
                if ($skuquantity[$tempsku] != -2) {
                    
                    if ($temhouseid == TRANSFER_HOUSE){
                        $backchecksku = $product->D->get_one_by_field(array('sku'=>$tempsku),'pid');
                        if ($backchecksku['pid'])	{
                            $backassem = $sku_assembly->get_sonlist(' and s.pid='.$backchecksku['pid']);
                        }
                        
                        /*非组装SKU，单一判断*/
                        if(!$backassem){
            				if ($skuquantity[$tempsku] != -2) {
            					$cur_sku_count[$temhouseid][$tempsku] += $quantity[$i];
            				    $kc = $this->S->dao('process')->get_allw_allsku(' and temp.sku="'.$tempsku.'" and temp.wid='.$temhouseid);
            					if ($kc['0']['sums'] < $cur_sku_count[$temhouseid][$tempsku]) {
            					    $skip= 'SKU：'.$tempsku.'库存不足，可发：'.$kc['0']['sums'].'个';
            						$skuquantity[$tempsku] = -2;
            					}
            				}
                        }
                        /*组装的SKU，提取原SKU判断逐一判断库存*/
                        elseif($backassem && is_array($backassem)){
                            if ($temhouseid) {
                					$skipp= '';
                					for($ass = 0 ; $ass < count($backassem); $ass++){
                					   $_sku = $backassem[$ass]['sku'];//当前子SKU
        
                                       $back_enough = $process->get_allw_allsku(' and temp.sku="'.$_sku.'" and temp.wid='.$temhouseid);//原有子SKU总量
                					   $cur_sku_count[$temhouseid][$_sku] += $backassem[$ass]['quantity']*$quantity[$i];//需要的累计总量
                                       if ($cur_sku_count[$temhouseid][$_sku] > $back_enough['0']['sums'] ) {
                                            $skipp.= 'SKU：'.$_sku.'库存不足，可发：'.$back_enough['0']['sums'].'个。';
                                            $skuquantity[$tempsku] = -2;
                                       }
                					}
                                    if($skipp) $skip = '这是组装产品，提取出的'.$skipp;
                             }
                             $arysku[$tempsku] = 1;
                        }
                    }
                }
			}else {
				$skuquantity[$tempsku] = -1;
			}

			$result[] = array('sku' => $tempsku, 'quantity'=> $skuquantity[$tempsku], 'num'=>$i ,'countsku'=>$arysku[$tempsku],'skip'=>$skip);
		}
	}
		 echo json_encode($result);
}
/** 物料调拨打印明细表
 * @create on  2012.12.29
 * @author by Jerry
*/
elseif ($detail == 'print_detail'){

    $orderid = stripslashes($order_id);

    $sql.=' and property="转仓单" and isover="N"';
    $orders = ' order by p.order_id asc';

    $sql.='and order_id in('.$orderid.')';
	$backdata = $this->S->dao('process')->showtransfer($sql,$orders);

    $detail_num = 0;//明细表总数
    for($i=0;$i<count($backdata);$i++){

        $extends = json_decode($backdata[$i]['extends'],true);  //解码
        $backdata[$i]['e_remeber_id']= $extends['e_remeber_id'];//目的仓库
        $backdata[$i]['e_unit_box']  = $extends['e_unit_box'];  //单箱箱数
        $backdata[$i]['e_box']       = $extends['e_box'];       //箱数
        $detail_num                 += $backdata[$i]['quantity'];//总数
    }
    $this->V->mark(array('datalist'=>$backdata,'pageid'=>count($backdata),'detail_num'=>$detail_num));
    $this->V->set_tpl('adminweb/print_product_detail');
    display();

}

/*删除出库单,权限-1.只能删除自己的,2.出货单状态,接收过的不能更改（此处与转仓出库共用）*/
elseif($detail == 'delshipment'){

	$process  = $this->S->dao('process');
    $_cuser = $process->D->get_one(array('id'=>$id),'cuser');
    if(!$this->C->service('admin_access')->checkResRight('r_p_delshipment','mod',$_cuser)){$this->C->ajaxmsg(0);}

	$sid = $process->D->delete_by_field(array('id'=>$id));
	if($sid){$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg(0,'删除失败');}
}

//接收选中
elseif($detail == 'recemod'){

	if(!$this->C->service('admin_access')->checkResRight('r_p_surereceive')){$this->C->ajaxmsg(0);}//接收权限判断

	/*防止逆向操作*/
	$process = $this->S->dao('process');
	$strid = stripslashes($strid);
	$backdata = $process->D->get_allstr(' and order_id in('.$strid.')','','','statu');

	foreach ($backdata as $val){
		if($val['statu'] != '0') exit('不合理操作');
	}

	/*执行接收*/
	$sid = $process->D->update_sql('where order_id in('.$strid.')',array('statu'=>1,'muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time()),'ruser'=>$_SESSION['eng_name'],'rdate'=>date('Y-m-d H:i:s',time())));
	if($sid){$this->C->ajaxmsg(1,'接收成功');}else{echo '接收失败';}
}


/*确认出库,权限*/
elseif($detail == 'modoutstock'){

	/*防止逆向操作*/
	$process = $this->S->dao('process');
	$strid = stripslashes($strid);
	$backdata = $process->D->get_allstr(' and order_id in('.$strid.')','','','statu,muser');

    if(!$this->C->service('admin_access')->checkResRight('r_p_sureleave')){$this->C->ajaxmsg(0);}


	/*执行确认*/
	$sid = $process->D->update_sql('where order_id in('.$strid.')',array('statu'=>'3','active'=>'1','output'=>'1','ruser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time())));
	if($sid){echo '确认成功';}else{echo '确认失败';}

}

/**
 * 回退已接收的出仓单,权限-（物流权限)
 */
elseif($detail == 'delout'){

	if(!$this->C->service('admin_access')->checkResRight('r_p_backordertransfer')){$this->C->ajaxmsg(0);}//接收回退判断

	$sid = $this->S->dao('process')->D->update_by_field(array('order_id'=>$order_id),array('statu'=>'0','active'=>'0','output'=>'0','mdate'=>'','muser'=>''));
	if($sid) {$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0);}
}

/**
 * 下载条形码文件 create on 2013-1-4 by hanson
 *
 */
elseif($detail == 'downloadfile'){

	$aft 	= '.btw';


	/*下载SKU条码文件或下载FNSKU条码文件*/
	if($type == 'onlyfnsku' || $type == 'onlysku'){


		$zipname= 'data/fnsku/'.$sname.$aft;

		if(!file_exists($zipname)) {$this->C->sendmsg('下载失败！<br>条形码文件<u>'.$sname.$aft.'</u>不存在！');}

		/*FnSku审核不通过则不能下载*/
		if($type == 'onlyfnsku'){
			$checks = $this->S->dao('barcode')->D->get_one(array('barcodeurl'=>$sname.$aft),'ischeck');
			if($checks != '2'){
				$this->C->sendmsg('下载失败！<br>条形码文件<u>'.$sname.$aft.'</u>未审核！');
			}
		}

		$file	= fopen($zipname , 'r');
	    Header ( "Content-type: application/octet-stream" );
	    Header ( "Accept-Ranges: bytes" );
	    Header ( "Accept-Length: " . filesize ( $zipname ) );
	    Header ( "Content-Disposition: attachment; filename=" .$fnsname.$aft);

	    /** 输出文件内容，读取文件内容并直接输出到浏览器 **/
	    echo fread($file, filesize($zipname));
	    fclose($file);
	}

	/*打包下载两个条形码文件*/
	elseif($type == 'all'){
		$backdatalist	= $this->S->dao('process')->D->get_allstr(' and order_id="'.$order_id.'"','','','sku,quantity,extends');
		$zip			= $this->getLibrary('zip');

		foreach($backdatalist as $val){
			$extends	= json_decode($val['extends'], 1);
			$skufile	= 'data/fnsku/'.$val['sku'].$aft;
			$fnskufile	= 'data/fnsku/'.$extends['e_remeber_id'].$aft;
			$filedir	= 'temp/'.$_SESSION['eng_name'];
			$lostmsg	= '';

			mkdir($filedir);//创建目录

			if(file_exists($skufile)){//移动sku.btw
				copy($skufile , $filedir.'/'.$order_id.'_'.$val['sku'].'_'.$val['quantity'].$aft);
				$lostmsg.= $val['sku'].$aft."_下载成功，";
			}else{
				$lostmsg.= $val['sku'].$aft."_下载失败，";
			}

			if(file_exists($fnskufile)){//移动fnsku.btw
				copy($fnskufile , $filedir.'/'.$order_id.'_'.$extends['e_remeber_id'].'_'.$val['quantity'].$aft);
				$lostmsg.= $extends['e_remeber_id'].$aft."_下载成功！\r\n";
			}else{
				$lostmsg.= $extends['e_remeber_id'].$aft."下载失败\r\n";
			}
		}

		/*若存在有文件没上传*/
		if($lostmsg){
			$file_handle = fopen('temp/'.$_SESSION['eng_name'].'/report.txt','w+');
		 	fwrite($file_handle,$lostmsg);
		 	fclose($file_handle);
		}

		/*压缩文件--------START*/
        $file_arr = scandir($filedir);
        $zipfilearr = array();
        foreach($file_arr as $val)	{if($val != '.' && $val != '..') $zipfilearr[] = $filedir.'/'.$val;}  //删除 '.', '..' 赋值给压缩文件名数组

		$zipname = 'temp/'.$order_id.'_'.$backdatalist['0']['quantity'].'.zip';
		$zip->zip($zipname , $zipfilearr);
		/*压缩文件--------END*/

		$file = fopen($zipname,'r');
        Header ( "Content-type: application/octet-stream" );
        Header ( "Accept-Ranges: bytes" );
        Header ( "Accept-Length: " . filesize ( $zipname ) );
        Header ( "Content-Disposition: attachment; filename=" . $order_id.'_'.$backdatalist['0']['quantity'].'.zip');

        /*读取文件内容并直接输出到浏览器*/
        echo fread ( $file, filesize ( $zipname ) );
        fclose ( $file );

        /*删除临时文件目录*/
        unlink($zipname);
        $zip->delDir('temp/'.$_SESSION['eng_name']);
        exit();
	}
}


/*模板定义*/
if($detail =='list' || $detail == 'addorder' || $detail =='editorderse'||$detail == 'editorder' || $detail == 'cancel_order'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>


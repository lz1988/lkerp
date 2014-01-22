<?php
/*
 * Created on 2011-12-19
 *
 * by hanson
 * @title 销售下单
 */

/*列表显示*/
if($detail == 'list'){
	/*搜索选项*/
	$stypemu = array(
		'statu-h-e'			=>'状态:',
		'sku-s-l'			=>'&nbsp; SKU：',
		'order_id-s-l'		=>'&nbsp; &nbsp; &nbsp; 订单号：',
		'fid-s-l'			=>'&nbsp; &nbsp; &nbsp; 第三方单号：',
		'comment2-s-l'		=>'&nbsp; &nbsp; &nbsp; 跟踪号：',
		'sold_way-a-e'		=>'&nbsp;&nbsp;渠道：',
		'cuser-s-l'			=>'&nbsp; &nbsp; &nbsp;&nbsp;制单人：',
		'buyer_id-s-l'		=>' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Buyer_ID：',
		'provider_id-a-e'	=>' &nbsp; 发货仓库：',
		'shipping-a-s'		=>'&nbsp; 物流：',
		'deal_id-s-l'		=>'&nbsp; 平台单号：',
        'rdate-t-t'         =>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;出 库 时 间：',

	);
    
	/*生成渠道搜索*/
	$sold_wayarr = $this->C->service('global')->get_sold_way(0,'sold_way','wayname');

	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
	$provider_idarr = array(''=>'=请选择=');
	for($i = 0; $i < count($wdata); $i++){
		$provider_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

	/*取得发货方式下拉*/
	$shipping_mu = $this->S->dao('shipping')->D->get_allstr('','','','s_name');
	$shippingarr = array(''=>'=请选择=');
	for($i = 0; $i < count($shipping_mu); $i++){
		$shippingarr[$shipping_mu[$i]['s_name']] = $shipping_mu[$i]['s_name'];
	}


	/*标签导航选项*/
	$tab_menu_stypemu = array(
		'statu-0'=>'预出库',
		'statu-1'=>'已接收',
		'statu-2'=>'待发货',
		'statu-3'=>'已出库'
	);

	/*判断回退与运单编号权限*/
	$retui = $this->C->service('admin_access')->checkResRight('r_w_backorder');


	/*初始打开默认显示预出库的*/
	if(empty($sqlstr) && !isset($statu)){
		$provider_id = $this->C->service('global')->sys_settings('listshipment_house','sys');
		$sqlstr = ' and statu="0" '; $statu='0' ;
		if($provider_id){$sqlstr.=' and provider_id='.$provider_id;}
	}
	if($sqlstr) $sqlstr = str_replace('cuser','p.cuser',$sqlstr);
	if($statu=='0'){$orders = 'id asc';}

	$sqlstr.=' and (protype="售出" or protype="重发") and isover="N"  ';

	/*发货方式保存于扩展中，搜索条件另做处理*/
	if($shipping){
		$shipping_e	 = json_encode($shipping);
		$shipping_e	 = addslashes($shipping_e);
		$sqlstr		.= ' and locate("'.$shipping_e.'",p.extends,1)>0';
	}

	/*分页参数,默认15,注意放在statu处理之后,查表之前*/
	$showperhtml = $this->C->service('warehouse')->perpage_show_html(array('0'=>'15','1'=>'50','2'=>'200','3'=>'1000'),$selfval_set,$statu,'&provider_id='.$provider_id);

	/*
	 * update on 2012-05-09
	 * by wall
	 * 工作提醒传过时间查找
	 * */
	if (!empty($job_alert_time)) {
		$sqlstr .= ' and p.rdate like "%'.$job_alert_time.'%" ' ;
		$pageshow = array('job_alert_time' => $job_alert_time);
	}
	$datalist = $this->S->dao('process')->get_shipment_orders($sqlstr);
    //rma信息
    $rmasummary = $this->S->dao('rmasummary');
    $process    = $this->S->dao('process');

	/*数据解压等处理*/
 	for($i=0;$i<count($datalist);$i++){
        
        //出库单的退货，退款信息
        if ($datalist[$i]['statu'] == 3)
        {
            $rmacomment = '';
            $sql        = '  and p.detail_id='.$datalist[$i]['id'];
            $rmadatalist   = $rmasummary->process_shipment_rmadetailslist($sql);
            
            for($j = 0;$j < count($rmadatalist); $j++){
                if ($rmadatalist[$j]['protype'] == '退货'){
                    $rmacomment    = $rmadatalist[$j]['protype'].'单号：'.$rmadatalist[$j]['order_id'].",";
                    $rmacomment   .= $rmadatalist[$j]['input'] == 1?'已入库':'未入库';
                }
                if ($rmadatalist[$j]['property'] == '退款单'){
                    $rmacomment   .= $rmadatalist[$j]['ispay'] == 1?'已退款':'未退款';
                }
            }
        }
    
		/*需要另外定义orderidd(默认等order_id,重复才置空,多条出单一次出货只显示一个出仓单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['order_idd']  = '';
            $datalist[$i]['rmacomment'] = '';
		}else{
			$datalist[$i]['order_idd'] = $datalist[$i]['order_id'];
	        $datalist[$i]['return']   = '<a href=index.php?action=process_shipment&detail=refundmode&order_id='.$datalist[$i]['order_id'].'  title=点击建立退货或退款单><font color=#828482>RMA</font></a>';
            $datalist[$i]['rmacomment'] = $rmacomment;
		}
        
	 	$datalist[$i]['getnum']	  = "<a title=获取运单编号 target=_blank href=index.php?action=process_shipment&detail=get_emsnum&id=".$datalist[$i]['id']."><font color=#828482>EMS</font></a>";
		$datalist[$i]['reout']	  = '<a href=index.php?action=process_shipment&detail=replace&detail_id='.$datalist[$i]['id'].'  title="点击将当条记录生成重发单"><font color=#828482>重发</font></a>';
		$datalist[$i]['cancel']   = '<a href=index.php?action=process_shipment&detail=cancel_order&order_id='.$datalist[$i]['order_id'].'  title=点击取消该订单><font color=#828482>取消</font></a>';
		$datalist[$i]['comment2'] = empty($datalist[$i]['comment2'])?'--':$datalist[$i]['comment2'];

 		/*将数组中的压缩内容解压并作为字段增加入数据中*/
 		$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);
		if (($statu == '2' || $statu == '3') && $retui) {
 			$datalist[$i]['e_shipping'] = '<span class="ajax_shipping" id="'.$datalist[$i]['id'].'" >'.$datalist[$i]['e_shipping'].'</span>';
		}
 		/*增加转至备货链接*/
 		$datalist[$i]['toupstock']= "<a title=点击备货 href=index.php?action=process_upstock&detail=add&sku=".$datalist[$i]['sku']."&e_stockname=".$datalist[$i]['fid']." target='_blank'><font color=#828482>备货</font></a>";
 	}

	/*数据输出*/
	$displayarr = array();
	$tablewidth = '2600';

	$displayarr['order_id'] 	 = array('showname'=>'checkbox','width'=>'40','title'=>'反选');

	if($statu == '0'){

		$displayarr['both'] 	 = array('showname'=>'操作','width'=>'50','ajax'=>'1','url_d'=>'index.php?action=process_shipment&detail=delshipment&id={id}','url_e'=>'index.php?action=process_shipment&detail=editshipment&id={id}');
		//$displayarr['toupstock'] = array('showname'=>'备货','width'=>'45');
		$displayarr['cancel']	 = array('showname'=>'取消','width'=>'45');
	}elseif($statu == '1' || $statu == '2' || $statu == '3'){

		if($retui)
		{
			$displayarr['sysback'] 	 = array('showname'=>'操作','width'=>'50','ajax'=>'1','url'=>'index.php?action=process_shipment&detail=delout&order_id={order_id}');
		}

		if($statu == '3')
		{
			$displayarr['reout']     = array('showname'=>'重发','width'=>'50');
            $displayarr['return']    = array('showname'=>'RMA','width'=>'50');
		}
	}
	$displayarr['order_idd'] 	 = array('showname'=>'订单号','width'=>'80');
	$displayarr['deal_id'] 		 = array('showname'=>'平台单号','width'=>'120');
	$displayarr['fid'] 			 = array('showname'=>'第三方单号','width'=>'120');
	$displayarr['sku'] 			 = array('showname'=>'产品SKU','width'=>'100');
	$displayarr['product_name']  = array('showname'=>'产品名称','width'=>'250');
	$displayarr['e_listing'] 	 = array('showname'=>'listing','width'=>'70');
	$displayarr['quantity'] 	 = array('showname'=>'数量','width'=>'50');
	$displayarr['cuser'] 		 = array('showname'=>'制单人','width'=>'80');
	$displayarr['wayname']	 	 = array('showname'=>'渠道','width'=>'100');
	$displayarr['e_shipping'] 	 = array('showname'=>'发货方式','width'=>'100');
	if($statu == '2' || $statu=='3'){$displayarr['comment2'] = array('showname'=>'物流跟踪号','clickedit'=>'id','detail'=>'editcomment','width'=>'100');}
	$displayarr['e_receperson']  = array('showname'=>'收件人','width'=>'80');
	$displayarr['buyer_id'] 	 = array('showname'=>'buyer_ID','width'=>'80');
	$displayarr['e_tel'] 		 = array('showname'=>'联系电话','width'=>'100');
	$displayarr['e_address1'] 	 = array('showname'=>'地址1','width'=>'150');
	$displayarr['e_address2'] 	 = array('showname'=>'地址2','width'=>'150');
	$displayarr['e_city'] 		 = array('showname'=>'城市','width'=>'80');
	$displayarr['e_state'] 		 = array('showname'=>'洲','width'=>'50');
	$displayarr['e_country'] 	 = array('showname'=>'国家','width'=>'80');
	$displayarr['e_post_code'] 	 = array('showname'=>'邮编','width'=>'80');
	$displayarr['e_email']	 	 = array('showname'=>'邮箱','width'=>'80');
	$displayarr['cdate'] 		 = array('showname'=>'制单时间','width'=>'100');
	if($statu == '3'){
        $displayarr['rdate']        = array('showname'=>'出库时间','width'=>'100');
        $displayarr['rmacomment']   = array('showname'=>'RMA信息','width'=>'160');
    }
	$displayarr['comment'] 		 = array('showname'=>'备注','width'=>'160');

	if($statu == '1' || $statu=='2'){$displayarr['muser'] = array('showname'=>'接单人','width'=>'80');}

	/*数据流操作按钮*/
	$this->C->service('global')->disconnect_modbutton(array('0'=>&$mod_disabled_0,'1-2-3'=>&$mod_disabled_1_2,'1'=>&$mod_disabled_1,'2'=>&$mod_disabled_2,'3'=>&$mod_disabled_3),$statu);

	$bannerstr = '<button onclick=window.location="index.php?action=process_shipment&detail=neworder" '.$mod_disabled_0.' >添加订单</button>';
	$bannerstr .= '<button onclick=window.location="index.php?action=process_shipment&detail=import_exl"  '.$mod_disabled_0.' >导入表格</button>&nbsp;&nbsp;';
	$bannerstr .= '<button onclick=combine()  '.$mod_disabled_0.' >合并订单</button>';
	$bannerstr .= '<button onclick=celcombine()  '.$mod_disabled_0.' >取消合并</button>&nbsp;&nbsp;';
	$bannerstr .= '<button onclick=printorder() id="printorder" '.$mod_disabled_1_2.'>打印操作</button>&nbsp;&nbsp;';
    $bannerstr .= $showperhtml;
    //$bannerstr .= '<button onclick=window.location="index.php?action=process_shipment&detail=printfinalorder" '.$mod_disabled_3.'>导出数据</button>&nbsp;&nbsp;';
	$bannerstr .= '<br><button onclick=receselect()  '.$mod_disabled_0.' >接收选中</button>';
    $bannerstr .= '<button onclick=print_table("print_detail") '.$mod_disabled_1_2.' >打印明细</button>&nbsp;&nbsp;';
	$bannerstr .= '<button onclick=sureprint(2) '.$mod_disabled_1.' >等待发货</button>';
	$bannerstr .= '<button onclick=sureout() '.$mod_disabled_2.' >确认出货</button>&nbsp;&nbsp;';
	$bannerstr .= '<button onclick=window.location="index.php?action=process_shipment&detail=outport_dhl" '.$mod_disabled_2.'>导出DHL</button>&nbsp;&nbsp;';
    $bannerstrarr[] = array('url'=>'index.php?action=process_shipment&detail=printfinalorder', 'value'=>'导出数据','extra'=>$mod_disabled_3);

	$jslink = "<script src='./staticment/js/process_shipment.js?version=".time()."'></script>\n";
	$this->V->mark(array('title'=>'出货列表'));
	$temp = 'pub_list';
}

/*删除已出库中的订单(用于删除接口获取的)*/
elseif($detail == 'modelouted'){

    $process = $this->S->dao('process');
    $_cuser  = $process->D->get_one(array('id'=>$id),'cuser');
	if(!$this->C->service('admin_access')->checkResRight('r_w_delouted','mod',$_cuser)){$this->C->ajaxmsg(0);}
	$sid = $this->S->dao('process')->D->delete_by_field(array('id'=>$id));

	if($sid) {echo '删除成功';}else{echo '删除失败';}
}

/*导入订单跳转选择页面*/
elseif($detail == 'import_exl'){

    if(!$this->C->service('admin_access')->checkResRight('r_w_addmod')){$this->C->sendmsg();}

	$bannerstr	= '<button class="eight" onclick=isloading("body",0,"跳转中...");window.location="index.php?action=process_shipment&detail=import">普通订单导入</button><br><font color=#bdbdbd size=-1> &nbsp; 导入生成的或手动整理的订单，保存后显示在预出库或异常订单。</font>';
	$bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=process_shipment&detail=che_fbaorder">匹配出库导入</button><br><font color=#bdbdbd size=-1> &nbsp; 匹配第三方平台发货订单的第三方单号，匹配成功的订单将直接出库。</font>';
	$bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=process_order&detail=ebay">生成ebay订单</button><br><font color=#bdbdbd size=-1> &nbsp; 将ebay平台上的两个原始订单表格合并成一个可供上传到ERP的订单表格。</font>';
	$bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=process_order&detail=ebayfinal">ebay生成最终表</button><br><font color=#bdbdbd size=-1> &nbsp; 导入整理后的ebay订单表格，生成最终销售明细表。</font>';
	$bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=process_order&detail=amazon">生成亚马逊订单</button><br><font color=#bdbdbd size=-1> &nbsp; 将amazon平台上的原始订单表格生成一个可供上传到ERP的订单表格。</font>';
	$bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=process_order&detail=outamazon">亚马逊出库单</button><br><font color=#bdbdbd size=-1> &nbsp; 导入亚马逊平台下载的出库单，保存成功后将显示在系统中的已出库。</font>';
	$bannerstr .= '<br><br><button onclick=history.go(-1);>返回</button>';
	$temp 		= 'pub_list';
	$this->V->mark(array(title=>'选择导入类型-出货列表(list)'));
}

/*导入第三方单号匹配FBA发货*/
elseif($detail == 'che_fbaorder'){
    if(!$this->C->service('admin_access')->checkResRight('r_w_addmod')){$this->C->sendmsg();}

	$submit_action	= 'index.php?action=process_shipment&detail=che_fbaorder';
	$temlate_exlurl = 'data/uploadexl/sample/che_fba_order.xls';
	$upload_dir 	= "./data/uploadexl/check_fba/";	//上传文件保存路径的目录
	$fieldarray 	= array('A');						//有效的excel列表值
	$head 			= 1;								//以第一行为表头

	$warehouse_html = $this->C->service('warehouse')->get_whouse('houseid','name','id');
	$warehouse_html.= '<font color="#bdbdbd" size="-1">&nbsp;(请选择了仓库再上传文件)</font><br><br>';

	$all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	if(is_array($all_arr)){

		$fbden_arr = $this->C->service('global')->sys_settings('check_fbden_whouse','sys','json');

		if(empty($houseid) || in_array($houseid,$fbden_arr)){$this->C->sendmsg('仓库未选择或该仓库禁止匹配出库!<br>如有需要，<br>请联系管理员开放该仓库！','index.php?action=process_shipment&detail=che_fbaorder');}
		$process = $this->S->dao('process');
		unset($all_arr['0']);//删除表头.

		$process->D->query('begin');/*采用事务*/
		$sucess_fba_update_nums = 0;
		$failed_fba_update_nums = 0;
		$failed_fba_fids		= '';

		/*更新FBA发货的订单，直接出库*/
		for($i=1;$i<=count($all_arr);$i++){
			$chk_backdata_fid = $process->D->get_one_by_field(array('fid'=>trim($all_arr[$i]['3rd_part_id']),'isover'=>'N','output'=>'0','provider_id'=>$houseid),'id');
			if($chk_backdata_fid){
				$sid = $process->D->update_by_field(array('fid'=>$all_arr[$i]['3rd_part_id']),array('statu'=>'3','active'=>'1','output'=>'1','mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name']));
				$sucess_fba_update_nums++;
				if(!$sid) $error_che_fba = 1;
			}else{
				$failed_fba_fids.= $all_arr[$i]['3rd_part_id']."\r\n";
				$failed_fba_update_nums++;
				$chk_backdata_fid = '';
			}
		}

		/*事务回滚与提交*/
		if($error_che_fba){
			$process->D->query('rollback');
			echo '<script>alert("保存失败，请稍候重试！");</script>';
		}else{
			if($failed_fba_update_nums){ $failed_fba_msg = '(失败原因：系统无法匹配，不存在的第三方单号。)';}
			$sucss_msg = '匹配报告：共 '.count($all_arr).' 个第三方单号：'."\r\n".'系统已匹配并出库：'.$sucess_fba_update_nums.' 个。'."\r\n".'失败：'.$failed_fba_update_nums." 个。\r\n".$failed_fba_msg;
			if($failed_fba_update_nums) $sucss_msg.="\r\n".'失败的第三方单号如下：'."\r\n".$failed_fba_fids;
			$process->D->query('commit');

			$this->C->service('upload_excel')->download('over_report_'.time(),$sucss_msg,'txt');
			$this->C->success($sucss_msg,'index.php?action=process_shipment&detail=list&statu=3&provider_id=');
		}

	}

	/*定义一个隐藏table，防止调用了拖动列宽的公共JS报错*/
	$tablelist = '<table id="mytable" style="display:none"><tr></tr></table>';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'匹配出库-选择导入类型(import_exl)-出货列表(list)','tablelist'=>$tablelist,'message_upload'=>$warehouse_html,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}


/*重发操作填写原因*/
elseif($detail == 'replace'){

	/*权限判断*/
	//$this->C->service('warehouse')->check_thegroup_right('id',$detail_id,1);
    $_cuser = $this->S->dao('process')->D->get_one(array('id'=>$detail_id),'cuser');
    if(!$this->C->service('admin_access')->checkResRight('none','follow',$_cuser)){$this->C->ajaxmsg(0);}

	$process	= $this->S->dao('process');
	$backdata	= $process->D->get_one(array('id'=>$detail_id),'sku,product_name,quantity');//取得信息

	/*RMA原因数组*/
	$reason_datastr = $this->C->service('warehouse')->get_backreaseon_html('comment3');


	/*表单配置*/
	$conform  = array('method'=>'post','action'=>'index.php?action=process_shipment&detail=modreplace');
	$colwidth = array('1'=>'100','2'=>'250','3'=>'250');

	$disinputarr['detail_id']	= array('showname'=>'detail_id','datatype'=>'h','value'=>$detail_id);
	$disinputarr['sku'] 		= array('showname'=>'SKU','inextra'=>'disabled','value'=>$backdata['sku']);
	$disinputarr['product_name']= array('showname'=>'产品名称','value'=>$backdata['product_name'],'inextra'=>' title="'.$backdata['product_name'].'" disabled');
	$disinputarr['quantity']	= array('showname'=>'* 重发数量','value'=>$backdata['quantity'],'showtips'=>'<span class=tips>&nbsp;系统默认取出原单数量，可修改。</span>');
	$disinputarr['reason']		= array('showname'=>'* 重发原因','datatype'=>'se','datastr'=>$reason_datastr);

	$this->V->view['title'] = '重发-出货列表(list)';
	$temp = 'pub_edit';
}

/*重发保存*/
elseif($detail == 'modreplace'){

    $_cuser = $this->S->dao('process')->D->get_one(array('id'=>$detail_id),'cuser');
    if(!$this->C->service('admin_access')->checkResRight('none','follow',$_cuser)){$this->C->ajaxmsg(0);}

	if(empty($comment3)) $this->C->sendmsg('请选择原因','index.php?action=process_shipment&detail=replace&detail_id='.$detail_id);
	if(empty($quantity)) $this->C->sendmsg('请填写退货数量','index.php?action=process_shipment&detail=replace&detail_id='.$detail_id);

	$process		= $this->S->dao('process');
	$orderservice	= $this->C->service('order');


	$back_shipment 	= $process->D->get_one_by_field(array('id'=>$detail_id),'buyer_id,quantity,provider_id,sku,fid,deal_id,pid,product_name,sold_way,sold_id,payrec_id,extends,comment');
	$jumpurl		= 'index.php?action=process_shipment&detail=list&provider_id='.$back_shipment['provider_id'];


	if(!$back_shipment['provider_id'] || !$back_shipment['fid'] || !$back_shipment['pid'] ||  !$back_shipment['sold_way'] || !$back_shipment['extends']){
		$this->C->sendmsg('原单信息获取失败!生成重发单失败!');
	}


	/*判断库存*/
	$back_enough 	= $process->get_allw_allsku(' and temp.sku="'.$back_shipment['sku'].'" and temp.wid='.$back_shipment['provider_id']);
	if($back_enough['0']['sums'] < $quantity){
		$this->C->sendmsg('库存不足，库存可发数：<strong>'.$back_enough['0']['sums'].'</strong> <br>生成重发单失败。');
	}



	/*实例化自动包含文件*/
	$this->C->service('exchange_rate');
	$finansvice 	= $this->C->service('finance');
	$back_shipment['extends'] = get_magic_quotes_gpc()?addslashes($back_shipment['extends']):$back_shipment['extends'];

	/*检测锁表*/
	$orderservice->checklock('begin',$jumpurl.'&statu=3');

	$max_x 			= $this->C->service('warehouse')->get_maxorder_manay('出仓单','d',$process);
	$insert_arr 	= array('provider_id'=>$back_shipment['provider_id'],'deal_id'=>$back_shipment['deal_id'],'sku'=>$back_shipment['sku'],'fid'=>$back_shipment['fid'],'pid'=>$back_shipment['pid'],'product_name'=>$back_shipment['product_name'],'extends'=>$back_shipment['extends'],'comment'=>$back_shipment['comment'],'comment3'=>$comment3,'sold_way'=>$back_shipment['sold_way'],'sold_id'=>$back_shipment['sold_id'],'payrec_id'=>$back_shipment['payrec_id'],'buyer_id'=>$back_shipment['buyer_id'],'detail_id'=>$detail_id,'quantity'=>$quantity,'cdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'order_id'=>$max_x,'property'=>'出仓单','protype'=>'重发');

	/*增加保存即时成本，期号，币别(CNY)*/
	$finansvice->rewrite_inorup_arr(&$insert_arr,$back_shipment['pid']);

	$sid   			= $process->D->insert($insert_arr);
	if($sid){
		$this->C->success('已生成新的出货单,新订单信息复制原单，如需修改，可在预出库中编辑。\n点击"确定"跳转查看新生成的出货单',$jumpurl.'&order_id='.$max_x.'&statu=0');
	}else{
		$this->C->success('生成重发单失败，请稍后重试！', $jumpurl.'&statu=3');
	}

	if(!$orderservice->checklock('end')) $orderservice->checklock('end');//释放锁表
}

/**
 * update on 2012-05-24
 * by wall 优化合并订单退款一次性操作
 *
 */
/*退款单资料填写页面*/
elseif($detail == 'remoneymode'){

	/*权限判断*/
	//$this->C->service('warehouse')->check_thegroup_right('order_id',$order_id,1);
    $process = $this->S->dao('process');
    $_cuser  = $process->D->get_one(array('order_id'=>$order_id),'cuser');

    if(!$this->C->service('admin_access')->checkResRight('none','follow',$_cuser)){$this->C->sendmsg();}

	$backcomeway = $process->D->get_one_by_field(array('order_id'=>$order_id),'sold_way,price,fid,extends,protype,provider_id');
	$backcomeway = $this->C->service('warehouse')->decodejson($backcomeway);//解压extends

	if($backcomeway['protype'] == '重发') $this->C->sendmsg('不能对重发的订单退款<br>请操作原始订单!');

	/*退款原因数组*/
	$reason_datastr = $this->C->service('warehouse')->get_backreaseon_html('comment3');

	/*全半额退款*/
	$re_sm_datastr 	= '<input type=radio name=re_sm  value=1 style="width:12px;height:12px;border:none" >全额退款 &nbsp; <input type=radio name=re_sm value=2  style="width:12px;height:12px;border:none">部分退款';

	/*币别*/
	$coin_code_datastr 	= $this->C->service('warehouse')->get_coincode_html('coin_code');

	/*备注*/
	$comment_datastr = '<textarea rows="3" cols="25" name=comment></textarea>';

	/*求该订单ID的收入总额$all_back_p*/
	$all_back	= $process->D->get_one_by_field(array('id'=>$detail_id),'price,extends');
	$all_back 	= $this->C->service('warehouse')->decodejson($all_back);//解压extends
	$all_back_p	= $all_back['price']+$all_back['e_item_tax']+$all_back['e_shipping_price']+$all_back['e_shipping_tax'];

	/*该$detail_id已经退款的*/
	$back_haveremony= $process->D->get_one_by_field(array('detail_id'=>$detail_id,'property'=>'退款单'),' sum(price/coin_rate*100) as price ');
	$haveremony		= round($back_haveremony['price'],2);

	/*可退款额=原可退*1.5-已退*/
	$could_beback 	= round($all_back_p*1.5,2) - $haveremony;

	$temp_list = $process->get_all_price_by_order_id($order_id);

	/*订单退款详情*/
	$detail_str = '<div style="max-height:130px; overflow-y:scroll;">';
	$price_array = array();
	$list_num = 0;
	foreach ($temp_list as $val) {
		$price_array[$list_num] = array();
		$val = $this->C->service('warehouse')->decodejson($val);//解压extends
		$price_array[$list_num]['item_price'] 		= number_format($val['price'],2);
		$price_array[$list_num]['e_item_tax'] 		= number_format($val['e_item_tax'],2);
		$price_array[$list_num]['e_shipping_price'] = number_format($val['e_shipping_price'],2);
		$price_array[$list_num]['e_shipping_tax'] 	= number_format($val['e_shipping_tax'],2);
		$price_array[$list_num]['haveremony']		= number_format(($val['price']+$val['e_item_tax']+$val['e_shipping_price']+$val['e_shipping_tax'])*1.5-$val['out_price'],2);
		$price_array[$list_num]['out_price'] 		= number_format($val['out_price'],2);

		$detail_str .= '<div style="padding-top:5px;">';
		$detail_str .= '<input type="text" name="sku[]" value="'.$val['sku'].'" disabled="" style="width:100px;"/>&nbsp;&nbsp;';

		if ($price_array[$list_num]['haveremony'] >0 ) {
			$detail_str .= '<input class="wall_disabled_check wall_focus_check wall_price_change" type="text" name="price[]" disabled="" style="width:100px;" value="退款金额" onfocus=\'if ($.trim(this.value) == "退款金额") this.value = "";\'  onblur=\'if ($.trim(this.value) == "") this.value = "退款金额";\' />&nbsp;&nbsp;';
			$detail_str .= '<input class="wall_disabled_check wall_focus_check wall_shipment_order_id" type="text" name="order_id[]" disabled="" style="width:150px;" value="交易ID" onfocus="if ($.trim(this.value) == \'交易ID\') this.value = \'\';"  onblur="if ($.trim(this.value) == \'\') this.value = \'交易ID\';"/>&nbsp;&nbsp;';
			$detail_str .= '<input class="wall_returnback_price" name="id[]" type="checkbox" style="width:15px;height:15px;border:none" value="'.$val['id'].'" title="勾选退款" />';
			$detail_str .= '<span></span>';
		}
		else {
			$detail_str .= '<input class="wall_disabled_check wall_focus_check wall_price_change" type="hidden" name="price[]" disabled="" />';
			$detail_str .= '<input class="wall_disabled_check wall_focus_check wall_shipment_order_id" type="hidden" name="order_id[]" disabled="" />';
			$detail_str .= '<font color=c6a8c6>已全退</font>';
		}
		$detail_str .= '</div>';
		$list_num++;
	}
	$detail_str .= '</div>';
	$price_list = json_encode($price_array);

	$bannerstr = '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:850px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
	$bannerstr.= '1、可退金额大约等于订单收入的1.5倍。<br>2、退款金额系统会自动转换成美元，折算后不得大于可退金额。';
    $bannerstr.= '<br>3、若需要做退货，<span><a href="index.php?action=process_shipment&detail=refundmode&order_id='.$order_id.'">点击这里</a></span> 跳到退货页面。';
	$bannerstr.= '</div>';



	/*表单配置*/
	$conform  = array('method'=>'post','action'=>'index.php?action=process_shipment&detail=modremoney_order','extra'=>'id="wall_shipment_form"');
	$colwidth = array('1'=>'150','2'=>'550','3'=>'250');

	$disinputarr['order_idl'] 		= array('showname'=>'订单号','inextra'=>'disabled','value'=>$order_id);
    $disinputarr['oid']             = array('showname'=>'订单号，作跳转用','datatype'=>'h','value'=>$order_id);
    $disinputarr['nbsp_none']		= array('showname'=>'<span class=tips>_收入项目<span>','datatype'=>'se','datastr'=>'<span class=tips>-------------------------------------------------</span>');
	$disinputarr['item_price'] 		= array('showname'=>'<span class=tips>产品收入<span>','inextra'=>'disabled','showtips'=>'<span class=tips>USD(item_price)</span>','value'=>0);
	$disinputarr['e_item_tax'] 		= array('showname'=>'<span class=tips>税金收入<span>','inextra'=>'disabled','showtips'=>'<span class=tips>USD(item_tax)</span>','value'=>0);
	$disinputarr['e_shipping_price']= array('showname'=>'<span class=tips>运费收入<span>','inextra'=>'disabled','showtips'=>'<span class=tips>USD(shipping_price)</span>','value'=>0);
	$disinputarr['e_shipping_tax'] 	= array('showname'=>'<span class=tips>运费税金收入<span>','inextra'=>'disabled','showtips'=>'<span class=tips>USD(shipping_tax)</span>','value'=>0);
	$disinputarr['nbsp_none2']		= array('showname'=>'<span class=tips>_收入项目<span>','datatype'=>'se','showtips'=>'<span class="wall_shipment_total"></span>','datastr'=>'<span class=tips>-------------------------------------------------</span>');
	$disinputarr['coin_code']		= array('showname'=>'* 币种','datatype'=>'se','datastr'=>$coin_code_datastr);
	$disinputarr['detail'] 			= array('showname'=>'退款详细','datatype'=>'se','datastr'=>$detail_str,'showtips'=>'<span class=tips>&nbsp 1、在需要做退款单的SKU后面打勾。<br> &nbsp; 2、点击退款金额、交易ID填写。<br> &nbsp; 3、未打勾的SKU不参与生成退款单。</span>');
	$disinputarr['extends']			= array('showname'=>'* 退款帐号');
	$disinputarr['comment2']		= array('showname'=>'* 联系人','width'=>'80');
	$disinputarr['re_sm']	 		= array('showname'=>'* 退款类型','datatype'=>'se','datastr'=>$re_sm_datastr);
	$disinputarr['comment3']		= array('showname'=>'* 退款原因','datatype'=>'se','datastr'=>$reason_datastr);
	$disinputarr['comment']			= array('showname'=>'备注','datatype'=>'se','datastr'=>$comment_datastr);
	$disinputarr['rate']			= array('showname'=>'汇率','datatype'=>'h','value'=>'0');
	$disinputarr['price_h']			= array('showname'=>'金额，转换成美元后的','datatype'=>'h','value'=>'0');
	$disinputarr['haveremony']		= array('showname'=>'可退金额','datatype'=>'h','value'=>$could_beback);
	$disinputarr['provider_id']		= array('showname'=>'发货仓库，作跳转用','datatype'=>'h','value'=>$backcomeway['provider_id']);

	$jslink = "<script src='./staticment/js/process_shipment.js'></script>\n";
	$jslink.= "<script src='./staticment/js/new.js'></script>\n";
	$jslink.= "<script src='./staticment/js/returnback_price.js'></script>\n";
	$jslink.= "<script type='text/javascript' >var price_list='".$price_list."';</script>\n";
	$this->V->mark(array('price_array'=>$price_array));
	if($is_jump_fbad){
		$this->V->view['title'] = '<a href="index.php?action=process_badorder&detail=list">异常订单列表</a>&raquo;退款处理';
	}else{
		$this->V->view['title'] = '退款处理-出货列表(list)';
	}
	$temp = 'pub_edit';
}

/*保存生成退款单,退单statu=1全额退款,statu=2部分退款,statu=3退货,退款单无单号*/
elseif($detail == 'modremoney_order'){

    /*权限控制*/
    $_cuser = $this->S->dao('process')->D->get_one(array('order_id'=>$order_id),'cuser');
    if(!$this->C->service('admin_access')->checkResRight('none','follow',$_cuser)){$this->C->sendmsg();}

	$insert_err = 0;
	$insert_len = count($id);

	if ($insert_len == 0) {
		echo "<script>alert('请在需要做退款单的产品SKU后面打勾！');history.back(-1)</script>";
	}
	$process = $this->S->dao('process');

	/*取最新期号*/
	$backrate = $this->S->dao('exchange_rate')->D->get_one_by_field(array('code'=>$coin_code,'isnew'=>1),'stage_rate');
	$process->D->query('begin');
	for ($i=0; $i<$insert_len; $i++) {
		$back_shipment 	= $process->D->get_one_by_field(array('id'=>$id[$i]),'quantity,sku,fid,pid,product_name,sold_way,sold_id,price,extends');
		$sid = $process->D->insert(array('sku'=>$back_shipment['sku'],'detail_id'=>$id[$i],'fid'=>$back_shipment['fid'],'pid'=>$back_shipment['pid'],'product_name'=>$back_shipment['product_name'],'price'=>$price[$i],'coin_code'=>$coin_code,'stage_rate'=>$backrate['stage_rate'],'cdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'statu'=>$re_sm,'sold_way'=>$back_shipment['sold_way'],'sold_id'=>$back_shipment['sold_id'],'property'=>'退款单','extends'=>$extends,'comment'=>$comment,'order_id'=>$order_id[$i],'comment2'=>$comment2,'comment3'=>$comment3));
		if (!$sid) {
			$insert_err = 1;
		}
	}
	if ($insert_err) {
		$process->D->query('callback');
		echo "<script>alert('操作失败！请重新操作！');history.back(-1)</script>";
	}
	else {
		$process->D->query('commit');
		$this->C->success('已生成退款单,可到"退款明细"中查看！','index.php?action=process_shipment&detail=remoneymode&order_id='.$oid);
	}
}

/**
 * create on 2012-05-23
 * by wall 新退货操作，支持合并订单中一次退多个货物，使用同一个退货单号
 */
/*建立退货单资料填写页面*/
elseif($detail == 'refundmode'){

	/*权限判断，只能对个人的记录进行退货*/
    $_cuser = $this->S->dao('process')->D->get_one(array('order_id'=>$order_id),'cuser');
    if(!$this->C->service('admin_access')->checkResRight('none','follow',$_cuser)){$this->C->ajaxmsg(0);}
	//$this->C->service('warehouse')->check_thegroup_right('order_id',$order_id,1);
	/*判断结束*/

	/*退货原因数组*/
	$reason_datastr = $this->C->service('warehouse')->get_backreaseon_html('comment3');

	/*表单配置*/
	$conform  = array('method'=>'post','id'=>'base','action'=>'index.php?action=process_shipment&detail=modrefund_order','width'=>'900','extra'=>'onsubmit="return check_backproform()"');
	$colwidth = array('1'=>'110','2'=>'400','3'=>'400');

	/*退货仓库*/
	$receiver_id_datastr = $this->C->service('warehouse')->get_whouse('receiver_id','name','id');

	/*平台ID*/
	//$sold_idstr	= $this->C->service('global')->get_sold_way(1,'sold_account','account_name');

	$temp_list	= $this->S->dao('process')->get_all_list_by_order_id($order_id);

	/*订单退货详情*/
	$detail_str = '<div style="max-height:130px; overflow-y:scroll;">';
	foreach ($temp_list as $val) {
		$detail_str 	.= '<div style="padding-top:5px;">';
		$detail_str 	.= '<input type="text" name="sku[]" value="'.$val['sku'].'" disabled="" />&nbsp;&nbsp;';
		if ($val['quantity'] > 0) {
			$detail_str .= '<select class="wall_disabled_check" name="quantity[]" disabled="" style="width:60px;height:21px;">';
			for ($i=1; $i<=$val['quantity']; $i++) {
				$baclpr_selected = ($i == $val['quantity'])?'selected':'';
				$detail_str 	.= '<option value="'.$i.'" '.$baclpr_selected.'>'.$i.'</option>';
			}
			$detail_str .= '</select>';
			$detail_str .= '<input class="wall_returnback_stock" name="id[]" type="checkbox" style="width:15px;height:15px;border:none" value="'.$val['id'].'" title="勾选退货"/>';
		}
		else {
			$detail_str .= '<font color=c6a8c6>已全退</font>';
		}
		$detail_str 	.= '</div>';
	}
	$detail_str 		.= '</div>';

	$disinputarr = array();

    $bannerstr = '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:870px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
	$bannerstr.= '温馨提示：若需要做退款，<span><a href="index.php?action=process_shipment&detail=remoneymode&order_id='.$order_id.'">点击这里</a></span>跳转至退款页面。';
	$bannerstr.= '</div>';

	$disinputarr['detail_id'] 	= array('showname'=>'上一级事务ID','datatype'=>'h','value'=>$detail_id);
	$disinputarr['order_id'] 	= array('showname'=>'订单号','inextra'=>'disabled','value'=>$order_id);
	$disinputarr['detail'] 		= array('showname'=>'退货详细','datatype'=>'se','datastr'=>$detail_str,'showtips'=>'<span class=tips>&nbsp 1、在需要做退货单的SKU后面打勾。<br> &nbsp; 2、点击下拉选择退货数量。<br> &nbsp; 3、未打勾的SKU不参与生成退货单。</span>');
	$disinputarr['receiver_id'] = array('showname'=>'接收仓库','datatype'=>'se','datastr'=>$receiver_id_datastr,'showtips'=>'<span class=tips>&nbsp*</span>');
	$disinputarr['comment3']	= array('showname'=>'退货原因','datatype'=>'se','datastr'=>$reason_datastr,'showtips'=>'<span class=tips>&nbsp*</span>');
	$disinputarr['comment']		= array('showname'=>'备注');
	$disinputarr['provider_id']	= array('showname'=>'发货仓库ID，作跳转用','datatype'=>'h','value'=>$backcomeway['provider_id']);
    $disinputarr['oid']			= array('showname'=>'订单号，作跳转用','datatype'=>'h','value'=>$order_id);

	$jslink = "<script src='./staticment/js/process_shipment.js?2012-08-02'></script>\n";
	$jslink.= "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink.= "<script src='./staticment/js/new.js'></script>\n";
	$jslink.= "<script src='./staticment/js/returnback_stock.js'></script>\n";
	$this->V->mark(array('title'=>'退货处理-出货列表(list)'));
	$temp = 'pub_edit';
}



/*生成退货单*/
elseif($detail == 'modrefund_order'){
	$insert_err = 0;
	$process = $this->S->dao('process');
	$insert_len = count($id);
	if ($insert_len == 0) {
		echo "<script>alert('请在需要做退货单的产品SKU后面打勾！');history.back(-1)</script>";
	}
	$max_r 		= $this->C->service('warehouse')->get_maxorder_manay('进仓单','r',$process);
	$max_x 		= $this->C->service('warehouse')->get_maxorder_manay('出仓单','d',$process);
	$setdate	= date('Y-m-d H:i:s',time());

	/*实例化自动包含文件*/
	$this->C->service('exchange_rate');
	$finansvice = $this->C->service('finance');

	$process->D->query('begin');
	for ($i=0; $i<$insert_len; $i++) {
		$back_shipment = $process->D->get_one_by_field(array('id'=>$id[$i]),'buyer_id,quantity,provider_id,sku,fid,pid,product_name,sold_way,sold_id,payrec_id,extends,comment');
		/*插入退单,单号R开头,statu=3,price=0*/

		$r_insert_arr 	= array('receiver_id'=>$receiver_id,'sku'=>$back_shipment['sku'],'fid'=>$back_shipment['fid'],'pid'=>$back_shipment['pid'],'product_name'=>$back_shipment['product_name'],'sold_way'=>$back_shipment['sold_way'],'sold_id'=>$back_shipment['sold_id'],'buyer_id'=>$back_shipment['buyer_id'],'detail_id'=>$id[$i],'quantity'=>$quantity[$i],'cdate'=>$setdate,'mdate'=>$setdate,'rdate'=>$setdate,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'order_id'=>$max_r,'statu'=>'3','active'=>'1','property'=>'进仓单','protype'=>'退货','comment'=>$comment,'comment2'=>$comment2,'comment3'=>$comment3);

		/*增加保存即时成本，期号，币别(CNY)*/
		$finansvice->rewrite_inorup_arr(&$r_insert_arr,$back_shipment['pid']);

		$sid 			= $process->D->insert($r_insert_arr);

		if (!$sid) {
			$insert_err = 1;
		}

		/*重发，即自动生成一个新的发货单,quantity=退货数量,price=0*/
		if($re_x == 1){
			$back_shipment['extends'] = get_magic_quotes_gpc()?addslashes($back_shipment['extends']):$back_shipment['extends'];

			$d_insert_arr 	= array('provider_id'=>$back_shipment['provider_id'],'sku'=>$back_shipment['sku'],'fid'=>$back_shipment['fid'],'pid'=>$back_shipment['pid'],'product_name'=>$back_shipment['product_name'],'extends'=>$back_shipment['extends'],'comment'=>$back_shipment['comment'],'sold_way'=>$back_shipment['sold_way'],'sold_id'=>$back_shipment['sold_id'],'buyer_id'=>$back_shipment['buyer_id'],'payrec_id'=>$back_shipment['payrec_id'],'detail_id'=>$id[$i],'quantity'=>$quantity[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'order_id'=>$max_x,'property'=>'出仓单','protype'=>'重发');

			$finansvice->rewrite_inorup_arr(&$d_insert_arr,$back_shipment['pid']);
			$cid   			= $process->D->insert($d_insert_arr);

			if(!$cid){
				$insert_err = 1;
			}
		}
	}
	if ($insert_err) {
		$process->D->query('callback');
		echo "<script>alert('操作失败！请重新操作！');history.back(-1)</script>";
	}
	else {
		$process->D->query('commit');
		if (empty($re_x)) {
			$this->C->success('已生成退货单，退货单号：'.$max_r.'。','index.php?action=process_shipment&detail=refundmode&order_id='.$oid);
		}
		else {
			$this->C->success('已生成退货单并生成新的出货单,新单内容复制源单\n如需修改，可在预出库中查找修改。','index.php?action=process_shipment&detail=list&statu=3&provider_id='.$provider_id);
		}
	}
}


/*取消订单--可以取消同一组人的*/
elseif($detail == 'cancel_order'){

	/*权限判断，只能修改同组人的记录*/
	//$backsoldway	= $this->S->dao('admin_group')->D->get_one_by_field(array('id'=>$_SESSION['groupid']),'`desc`');

	$process		= $this->S->dao('process');
    $_cuser         = $process->D->get_one(array('order_id'=>$order_id),'cuser');
	//$this->C->service('warehouse')->check_thegroup_right('order_id',$order_id,1);//权限判断
    if(!$this->C->service('admin_access')->checkResRight('r_w_editmod','mod',$_cuser)){$this->C->sendmsg();}

	/*取消原因数组*/
	$reason_datastr = $this->C->service('warehouse')->get_canceltype(2,'comment3');


	/*表单配置*/
	$disinputarr 				= array();
	$conform 					= array('method'=>'post','action'=>'index.php?action=process_shipment&detail=modcancel_order');
	$disinputarr['order_idd']	= array('showname'=>'订单号','value'=>$order_id,'inextra'=>'disabled');
	$disinputarr['order_id'] 	= array('showname'=>'订单号','value'=>$order_id,'datatype'=>'h');
	$disinputarr['comment3'] 	= array('showname'=>'取消原因','datatype'=>'se','datastr'=>$reason_datastr);

	$this->V->mark(array('title'=>'取消订单-出货列表(list)'));
	$temp = 'pub_edit';

}

/*执行取消订单*/
elseif($detail == 'modcancel_order'){

    //权限控制
   	$process		= $this->S->dao('process');
    $_cuser         = $process->D->get_one(array('order_id'=>$order_id),'cuser');

	if($comment3 == '') {$this->C->success('请选择原因','index.php?action=process_shipment&detail=cancel_order&order_id='.$order_id);exit;}

	$sid = $this->S->dao('process')->D->update_by_field(array('order_id'=>$order_id),array('isover'=>'Y','comment3'=>$comment3));
	if($sid){$this->C->success('保存成功，已经搁置异常订单','index.php?action=process_shipment&detail=list');}
}



/*预览打印发货单与快递联*/
elseif($detail == 'print_outbound' || $detail == 'print_express_yunda' || $detail == 'print_express_shentong' || $detail == 'print_quantwl'){

	$order_id = stripslashes($order_id);
	$process  = $this->S->dao('process');
	$warehouse= $this->C->service('warehouse');


	$datalist = $process->D->get_allstr('and order_id in('.$order_id.')','','order_id desc','order_id,buyer_id,sku,product_name,quantity,fid,cdate,extends,comment');
	$showdata = array();
	$n_row	  = 0;

	for($i = 0; $i < count($datalist); $i++){

		$datalist[$i]	= $warehouse->decodejson($datalist,$i);

		/*需要整合的内容sku,名称,数量*/
		$detailarr		= array('sku'=>$datalist[$i]['sku'],'product_name'=>$datalist[$i]['product_name'],'quantity'=>$datalist[$i]['quantity']);

		/*信息整合*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){

			array_push($showdata[$n_row-1]['showdetail'],$detailarr);

		}

		/*插入新的记录*/
		else{
			$detail_list[] = $detailarr;
			$showdata[$n_row] = array('comment'=>$datalist[$i]['comment'],'fid'=>$datalist[$i]['fid'],'buyer_id'=>$datalist[$i]['buyer_id'],'e_receperson'=>$datalist[$i]['e_receperson'],'cdate'=>date('Y-m-d',strtotime($datalist[$i]['cdate'])),'address'=>$datalist[$i]['e_address1'].$datalist[$i]['e_address2'],'city'=>$datalist[$i]['e_city'],'post_code'=>$datalist[$i]['e_post_code'],'tel'=>$datalist[$i]['e_tel'],'showdetail'=>$detail_list);
			$n_row++;
		}

		unset($detailarr,$detail_list);
	}

	//echo '<pre>'.print_r($showdata,1).'</pre>';exit();

	$this->V->mark(array('showdata'=>$showdata));
	$this->V->set_tpl('adminweb/'.$detail);
	display();
}

/*打印明细表与汇总表*/
elseif($detail == 'print_detail'){

	$order_id = stripslashes($order_id);//由于框架强制开启魔法变量，引号自动加上了反斜杠。些处要去掉。

	$process  = $this->S->dao('process');
	$backdata = $process->D->get_allstr('and order_id in('.$order_id.')','','order_id asc','statu,order_id,sku,quantity,extends,sold_way,comment');

	$backdata_sum	= $process->get_sumsells($order_id);

	$detail_num = 0;//明细表总数
	$all_num = 0;//汇总表总数

	foreach ($backdata_sum as $val){
		$all_num+= $val['num'];
	}

	$obj_sold_way = $this->S->dao('sold_way');

	/*明细表数据处理*/
	for($i=0;$i<count($backdata);$i++){

		/*渠道临时方案*/
		if(intval($backdata[$i]['sold_way'])) {
			$backsoldway[$i] = $obj_sold_way->D->get_one_by_field(array('id'=>$backdata[$i]['sold_way']),'wayname');
			$backdata[$i]['sold_way'] = $backsoldway[$i]['wayname'];
		}

		$extends 					= json_decode($backdata[$i]['extends'],true);
		$backdata[$i]['e_shipping'] = $extends['e_shipping']; //解压发货方式
		$detail_num				   +=$backdata[$i]['quantity'];//统计总数
		$backdata[$i]['order_idd'] 	= ($backdata[$i]['order_id'] == $backdata[$i-1]['order_id'])?'':$backdata[$i]['order_id'];//同一个订单有多个产品的处理
	}

	/*按发货方式e_shipping进行排序,方便仓库包货*/
	//$backdata = $this->C->array_sort($backdata,'e_shipping');
	//考虑到仓管贴标签按订单从小到大排序方便，先去掉。

	$this->V->mark(array('datalist'=>$backdata,'pageid'=>count($backdata),'datalist_sum'=>$backdata_sum,'pageid_sum'=>count($backdata_sum),'detail_num'=>$detail_num,'all_num'=>$all_num));
	$this->V->set_tpl('adminweb/print_process_detail');
	display();

}


/*合并订单-权限(①本人下的单②未接收)*/
elseif($detail == 'combine'){


	/*去掉由框架强制开启魔法变量添加的反斜杠*/
	$strid = stripslashes($strid);
    //print_r($strid);die();
	$process = $this->S->dao('process');
	$backdata = $process->D->get_allstr(' and order_id in ('.$strid.')','','','cuser,statu,receiver_id');

	for($i = 0; $i<count($backdata); $i++){

        if(strpos($strid,'x') != false || strpos($strid,'d') != false){
            if(!$this->C->service('admin_access')->checkResRight('r_w_editmod','mod',$backdata[$i]['cuser'])){$this->C->ajaxmsg(0);}
	   }elseif(strpos($strid,'f') != false){
	        if(!$this->C->service('admin_access')->checkResRight('r_p_editorder','mod',$backdata[$i]['cuser'])){$this->C->ajaxmsg(0);}
    	}

	   if($i && $backdata[$i]['receiver_id'] != $backdata[$i-1]['receiver_id']) exit('不合理操作,不能将目的仓库不同的订单合并 !');
	}

	$maxorder = $process->maxorder(' and order_id in ('.$strid.')');
	$sid = $process->D->update_sql(' where order_id in ('.$strid.')',array('order_id'=>$maxorder));
	if($sid){echo '合并成功，总单号：'.$maxorder;}else{echo '合并失败';}

}

/*取消合并-权限(①本人下的单②未接收，并且只能对一条合并的订单进行操作)*/
elseif($detail == 'calcombine'){

	/*去掉由框架强制开启魔法变量添加的反斜杠*/
	$strid 		= stripslashes($strid);
	$new_order	= substr($strid,1,8);

	$locktab	= $this->S->dao('locktab');

	/*如果选中多条订单，提示不合理操作*/
	if(strlen($strid) > '10') exit('不合理操作,只能选择一个订单 !');

	$process = $this->S->dao('process');

	$backdata = $process->D->get_allstr(' and order_id='.$strid,'','','id,cuser,statu');/*默认倒序取出该订单数据*/

    if(!$this->C->service('admin_access')->checkResRight('r_w_editmod','mod',$backdata[0]['cuser'])){$this->C->ajaxmsg(0);}

	if(count($backdata) == 1) exit('单条记录无法再拆分 !');

	/*防止逆操作和非法操作*/
	foreach($backdata as $val){//出库订单
         if($order_pre == 'x' || $order_pre == 'd'){
            if(!$this->C->service('admin_access')->checkResRight('r_w_editmod','mod',$val['cuser'])){$this->C->ajaxmsg(0);}
	    }elseif($order_pre == 'f'){//代表物料调拨
            if(!$this->C->service('admin_access')->checkResRight('r_p_editorder','mod',$val['cuser'])){$this->C->ajaxmsg(0);}
    	}
	}

	/*查看锁表标记(为减少锁表冲突，故放在此处)*/
	$back_checklock = $locktab->D->get_one_by_field(array('type'=>0),'onoff');

	/*如果当前有人在导表，则退出*/
	if($back_checklock['onoff'] == '1'){ exit('服务器繁忙，请重试 !'); }else{ $locktab->D->update_by_field(array('type'=>0),array('onoff'=>1)); }//标记锁表

	if($order_pre == 'x' || $order_pre == 'd'){
		$max 		= $this->C->service('warehouse')->get_maxorder_manay('出仓单', $order_pre, $process);
	}elseif($order_pre == 'f'){
		$max 		= $this->C->service('warehouse')->get_maxorder('转仓单','f',$process);
	}

	$errornum	= '';

	/*开始一个事务*/
	$process->D->query('begin');
	for($i=1; $i<count($backdata); $i++){

		$order_id 	= $order_pre.sprintf("%07d",substr($max,1)+$i-1);

		$sid 		= $process->D->update_by_field(array('id'=>$backdata[$i]['id']),array('order_id'=>$order_id));
		$new_order .= '，'.$order_id;
		if(!$sid) $errornum++;
	}

	if(empty($errornum)){

		/*解锁两次，防止一次的失败*/
		$lid = $locktab->D->update_by_field(array('type'=>0),array('onoff'=>'0'));
		if(!$lid) $locktab->D->update_by_field(array('type'=>0),array('onoff'=>'0'));

		$process->D->query('commit');echo '操作成功，拆分后的新订单号分别是：'."\n".$new_order;

	}else{
		$process->D->query('rollback');echo '取消失败';
	}
}

//导入文件处理进行判断处理
elseif($detail == 'import'){

    if(!$this->C->service('admin_access')->checkResRight('r_w_addmod')){$this->C->sendmsg();}
	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/order_shipment/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC');//有效的excel列表值
	$head 		= 1;//以第一行为表头

	$tablelist 	= '';
	$status_arr = array('','completed','uncleared','cleared','outputed','compared');//表格的状态类型
    
    /*实例化b2bcorpbsl表*/
    $b2bcorpbsl = $this->S->dao('b2bcorpbsl');
    $product_cost = $this->S->dao('product_cost');

	/*读取已经上传的文件*/
	if($filepath){


		/*查看锁表标记*/
		$locktab = $this->S->dao('locktab');
		$back_checklock = $locktab->D->get_one_by_field(array('type'=>0),'onoff');
		if($back_checklock['onoff'] == '1'){$this->C->sendmsg('服务器繁忙，请重试！','index.php?action=process_shipment&detail=import');}

		/*标记锁表*/
		$locktab->D->update_by_field(array('type'=>0),array('onoff'=>1));


		$all_arr =  $this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);
		$upload_in_status = array('cleared','compared');//只是匹配的订单数组

		/*根据上传人取得渠道*/
		//$backdata = $this->S->dao('admin_group')->D->get_one_by_field(array('id'=>$_SESSION['groupid']),'`desc`');

		/*币别转换*/
		$changeobj = $this->C->service('exchange_rate');//调成本也需要用到，先实例化，则不用再包含文件
		$obj_exchange_rate = $this->S->dao('exchange_rate');

		for($i=1;$i<count($all_arr);$i++){

			if(!in_array(strtolower($all_arr[$i]['status']),$upload_in_status))
			{/*Start--需要插入的时候才需要获取以下资料*/

				$back_obj_exchange_rate[$i] = $obj_exchange_rate->D->get_one_by_field(array('code'=>$all_arr[$i]['currency'],'isnew'=>1),'stage_rate,rate');


				/*获取供应者ID*/
				$whouse_id = $this->S->dao('esse')->D->select('id','name="'.$all_arr[$i]['warehouse'].'"');
				$all_arr[$i]['warehouse'] = $whouse_id['0'];

			}/*End--需要插入的时候才需要获取以下资料*/
		}




		/*生成出仓单号,取得出仓最大单号，并取出数字，x+7位数字，不够补0*/
		$process = $this->S->dao('process');
		$max = $this->C->service('warehouse')->get_maxorder_manay('出仓单','x',$process);
		$global_service = $this->C->service('global');
		$finansvice 	= $this->C->service('finance');

		$sold_account		= $this->S->dao('sold_account');			/*销售帐号*/
		$payrec_account		= $this->S->dao('finance_payrec_account');	/*收款帐号*/
		$sold_relation_conf	= $this->S->dao('sold_relation_conf');		/*关系设置*/


		$successnum 		= 0;	/*成功统计量*/
		$countnum_complete	= 0;	/*正常订单统计量*/
		$bad_completed		= 0;	/*正常订单库存不足数量*/

		$countnum_uncleared = 0;	/*uncleare订单数量*/
		$countnum_outputed 	= 0;	/*outputed直接出库的订单数量*/
		$countnum_compared	= 0;	/*Compared订单数量-用于匹配直接出库无须判断库存*/
		$success_compared	= 0;	/*Compared成功匹配的订单量*/
		$bad_compared		= 0;	/*Compared失败的订单量*/


		$countnum_cleared	= 0;	/*cleare订单数量-用于匹配还原异常订单*/
		$success_cleared	= 0;    /*cleare成功还原数量*/
		$bad_cleared		= 0;	/*cleare还原失败数量*/
		$cleared_have_check = 0;	/*cleare系统找到匹配的数量*/

		$bad_client_email	= '';
		$bad_compared_fid	= '';


		/*开始一个事务，用事务处理，全部成功才插入。避免导入者不知哪些是成功的，保留以后优化*/
		$process->D->query('begin');

		for($i=1;$i<count($all_arr);$i++){

			/*statu=Cleared,则检测异常订单中是否存在，并判断库存是否充足自动还原*/
			if(strtolower($all_arr[$i]['status']) == 'cleared'){
				$countnum_cleared++;	//统计导入表格中cleared订单数
				$chbad_exists = $process->D->get_all_sql('select id,fid,sku,provider_id,quantity from process where isover="Y" and comment3="9" and extends like "%'.$all_arr[$i]['email'].'%" ');
				/*有返回结果则下一步判断库存*/
				if(is_array($chbad_exists)){

					/*--①对在异常订单中查出结果判断库存--*/
					for($ch_i=0;$ch_i<count($chbad_exists);$ch_i++){
						$cleared_have_check++;

						/*对原单取数量进行库存判断*/
						$check_back_enough = $process->get_allw_allsku(' and temp.sku="'.$chbad_exists[$ch_i]['sku'].'" and temp.wid='.$chbad_exists[$ch_i]['provider_id']);
						if($chbad_exists[$ch_i]['quantity'] <= $check_back_enough['0']['sums']){

							$ch_sid = $process->D->update_by_field(array('id'=>$chbad_exists[$ch_i]['id']),array('isover'=>'N','comment3'=>'','fid'=>$all_arr[$i]['3rd_part_id']));/*有库存则还原，同时更新第三方单号*/
							if($ch_sid){$success_cleared++;}else{$error_cleared_sums = 1;}/*程序还原失败错误量*/
						}else{
							$bad_cleared++; /*库存不足还原失败数统计*/
							$bad_client_email.= $all_arr[$i]['email'].',\n';/*统计还原失败的客人邮箱*/
						}
					}
					/*--①End--*/

					/*判断还原情况*/
					if(empty($error_cleared_sums)) $successnum++;
				}
				/*若找不到匹配的*/
				else{
					$successnum++;
					$bad_cleared++;
					$bad_client_email.= $all_arr[$i]['email'].',';

				}
			}

			/*status=compared的订单，匹配出库，无须判断库存*/
			elseif(strtolower($all_arr[$i]['status']) == 'compared'){
				$countnum_compared++;//统计该类型订单量

				$chk_backdata_fid = $process->D->get_one_by_field(array('fid'=>$all_arr[$i]['3rd_part_id'],'isover'=>'N','output'=>'0'),'id');
				if($chk_backdata_fid){
					$sid = $process->D->update_by_field(array('fid'=>$all_arr[$i]['3rd_part_id']),array('statu'=>'3','active'=>'1','output'=>'1','mdate'=>date('Y-m-d H:i:s',time()),'muser'=>$_SESSION['eng_name']));
					if($sid){$success_compared++;$successnum++;}
				}else{
					$bad_compared++;
					$successnum++;/*需要加1，防止影响事务成功统计量*/
					$bad_compared_fid.=$all_arr[$i]['3rd_part_id'].'\n';
				}
			}

			/*complete订单与unclear的，或者不存在statu(老表格模板)，执行如下操作*/
			else{

				$order_id = 'x'.sprintf("%07d",substr($max,1)+$i-1);


				/*分解SKU，去掉listing*/
				$skukeyarr = explode('-',$all_arr[$i]['sku']);
				if(count($skukeyarr)>3){unset($skukeyarr['3']);$all_arr[$i]['sku']=implode('-',$skukeyarr);}


				/*获得产品ID，没有获取PID的不让提交*/
				$backpidpname[$i] = $process->D->get_one_sql('select pid,product_name,shipping_weight from product where sku="'.trim($all_arr[$i]['sku']).'"');
				if($backpidpname[$i]['pid']){

					$all_arr[$i]['pid'] = $backpidpname[$i]['pid'];
					$all_arr[$i]['product_name'] = $backpidpname[$i]['product_name'];
				}else{
					$process->D->query('rollback');
					$locktab->D->update_by_field(array('type'=>0),array('onoff'=>0));//开启锁表标记
					$this->C->success('保存失败，存在无法获取产品ID的SKU:'.$all_arr[$i]['sku'].'，请检查重试!','index.php?action=process_shipment&detail=import');
					exit();
				}

				/*需要运费估算*/
				if(in_array($i,$nedd_coutshpfare)){

					if($backpidpname[$i]['shipping_weight'] && empty($all_arr[$i]['shipping_fee'])){//需要有填写重量并且运费并没手填
						$all_arr[$i]['country'] 	 = ($all_arr[$i]['country'] == 'United States')?'US':$all_arr[$i]['country'];//美国不分东西
						$all_arr[$i]['shipping_fee'] = $global_service->count_shipping_fare(trim($all_arr[$i]['shipping']),$backpidpname[$i]['shipping_weight']*$all_arr[$i]['quantity'],$all_arr[$i]['country']);

						if($all_arr[$i]['currency'] != 'CNY'){
							$all_arr[$i]['shipping_fee'] = -$changeobj->change_rate('CNY',$all_arr[$i]['currency'],$all_arr[$i]['shipping_fee']);//转的成当前币别
						}
					}
				}

				/*扩展内容处理*/
				$extends = array('e_listing'=>$all_arr[$i]['listing'],'e_item_tax'=>$all_arr[$i]['item_tax'],'e_shipping_price'=>$all_arr[$i]['shipping_price'],'e_shipping_tax'=>$all_arr[$i]['shipping_tax'],'e_performance_fee'=>$all_arr[$i]['performance_fee'],'e_shipping_fee'=>$all_arr[$i]['shipping_fee'],'e_shipping'=>$all_arr[$i]['shipping'],'e_receperson'=>$all_arr[$i]['receive_person'],'e_tel'=>$all_arr[$i]['tel'],'e_email'=>$all_arr[$i]['email'],'e_address1'=>str_replace('"','',$all_arr[$i]['address1']),'e_address2'=>str_replace('"','',$all_arr[$i]['address2']),'e_city'=>$all_arr[$i]['city'],'e_state'=>$all_arr[$i]['state'],'e_country'=>$all_arr[$i]['country'],'e_post_code'=>$all_arr[$i]['post_code']);

				/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
				$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

				/*①如果status=uncleared，即客人付款而款未到的订单，直接跳到异常订单，原因默认为未付款*/
				if(strtolower($all_arr[$i]['status']) == 'uncleared'){

					$zhi_output = 0;
					$zhi_statu  = 0;
					$zhi_active = 0;
					$isover = 'Y';$comment3 = '9';
					$countnum_uncleared++;/*统计量*/

				}

				/*②正常的订单，按老逻辑，检测库存，库存充足正常添加，库存不足搁置异常订单*/
				elseif(strtolower($all_arr[$i]['status']) == 'completed' || empty($all_arr[$i]['status'])){
					$countnum_complete++;/*统计量*/
					$zhi_output = 0;
					$zhi_statu  = 0;
					$zhi_active = 0;

					/*判断是否有库存的订单*/
					$if_exists_noteoughtfid = in_array($all_arr[$i]['3rd_part_id'],$not_enougth_fid)?1:0;
					if(in_array($i,$not_enougth_row) || $if_exists_noteoughtfid){
						$bad_completed++;/*库存不足数*/

						/*如果是由于同一第三方单号中存在一条库足不足的订单而牵带其它订单一同抛至异常订单的，订单号自动合成*/
						if($if_exists_noteoughtfid) {
							$backdata_fid 	= $process->D->get_one_by_field(array('fid'=>$all_arr[$i]['3rd_part_id'],'provider_id'=>$all_arr[$i]['warehouse'],'coin_code'=>$all_arr[$i]['currency']),'order_id');
							if($backdata_fid) $order_id		= $backdata_fid['order_id'];
						}

						$isover = 'Y';$comment3 = '1';
					}else{
						$isover = 'N';$comment3 = '';
					}
				}

				/*③直接出库的订单*/
				elseif(strtolower($all_arr[$i]['status']) == 'outputed'){
					$countnum_outputed++;
					$zhi_output = 1;
					$zhi_statu	= 3;
					$isover		= 'N';
					$zhi_active = 1;
				}
                
                //如果是组合sku,子sku进行下单
                $sku_back = $process->output_child_sku(' and s.pid='.$all_arr[$i]['pid']);
                if ($sku_back){
                    $child_cost3 = $process->get_childsku_cost3($all_arr[$i]['pid']);
                    foreach($sku_back as $v){
                        
                        $item_price = $all_arr[$i]['item_price'];
                        /*拆分sku的售价，根据每个sku的市场指导价所占比例乘以组合sku售价*/
                        $item_price = $v['cost3']*$item_price/$child_cost3[0]['cost3'];
                
                        $price2 	= $finansvice->get_productcost($v['child_pid']);
                        $insert_arr = array('provider_id'=>$all_arr[$i]['warehouse'],'sku'=>$v['sku'],'fid'=>$all_arr[$i]['3rd_part_id'],'deal_id'=>$all_arr[$i]['deal_id'],'pid'=>$v['child_pid'],'product_name'=>$v['product_name'],'price'=>$item_price,'price2'=>$price2,'coin_code'=>$all_arr[$i]['currency'],'stage_rate'=>$back_obj_exchange_rate[$i]['stage_rate'],'quantity'=>$all_arr[$i]['quantity'],'cdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time()),'ruser'=>$_SESSION['eng_name'],'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'buyer_id'=>$all_arr[$i]['buyer_id'],'isover'=>$isover,'active'=>$zhi_active,'property'=>'出仓单','protype'=>'售出','output'=>$zhi_output,'statu'=>$zhi_statu,'extends'=>$extends,'comment'=>$all_arr[$i]['comment'],'comment3'=>$comment3);
                        
                        /*渠道、销售帐号、收款帐号--START*/
    					$back_sold_account	= $sold_account->D->get_one_by_field(array('account_name'=>$all_arr[$i]['sold_account']),'id');//取得销售帐号ID
    					$back_relations		= $sold_relation_conf->get_default_payrec($back_sold_account['id']);//取得关联的渠道ID与收款ID
                        
                        
                        /*记录市场指导价，计算sku所占收入比例*/
                        $market_price = $product_cost->D->get_one_by_field(array('pid'=>$v['child_pid']),'cost3');
                        
    					/*如果填写了收款ID*/
    					if($all_arr[$i]['payrec_account']){
    						$backpayrec = $payrec_account->D->get_one_by_field(array('payrec_account'=>$all_arr[$i]['payrec_account']),'id');
    						$back_relations['payrec_id'] = $backpayrec['id'];
    					}
    
    					$insert_arr['sold_way'] = $back_relations	['way_id'];
    					$insert_arr['sold_id']	= $back_sold_account['id'];
    					$insert_arr['payrec_id']= $back_relations	['payrec_id'];
                        $insert_arr['market_price'] = $market_price['cost3'];
                        /********end*******/
                        
                        /*如果sold_account为B2B类型，那么b2b_customers必填*/
                        if (strtolower($all_arr[$i]['sold_account']) == 'b2b'){
                            $b2b_id = $b2bcorpbsl->D->get_one_by_field(array('corpname'=>$all_arr[$i]['b2b_customers']),'id');
                            $insert_arr['b2b_customers'] = $b2b_id['id'];
                        }
                            
                        $sid 		= $process->D->insert($insert_arr);
                        if(strtolower($all_arr[$i]['status']) == 'outputed'){
                            $process->D->update_by_field(array('id'=>$sid),array('mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name']));
 				        }
                        
                    }
                }else{
				    /*保存即时成本，币别统一转换本位币(CNY)*/
    				$price2 	= $finansvice->get_productcost($all_arr[$i]['pid']);
    
    				$insert_arr = array('provider_id'=>$all_arr[$i]['warehouse'],'sku'=>$all_arr[$i]['sku'],'fid'=>$all_arr[$i]['3rd_part_id'],'deal_id'=>$all_arr[$i]['deal_id'],'pid'=>$all_arr[$i]['pid'],'product_name'=>$all_arr[$i]['product_name'],'price'=>$all_arr[$i]['item_price'],'price2'=>$price2,'coin_code'=>$all_arr[$i]['currency'],'stage_rate'=>$back_obj_exchange_rate[$i]['stage_rate'],'quantity'=>$all_arr[$i]['quantity'],'cdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time()),'ruser'=>$_SESSION['eng_name'],'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'buyer_id'=>$all_arr[$i]['buyer_id'],'isover'=>$isover,'active'=>$zhi_active,'property'=>'出仓单','protype'=>'售出','output'=>$zhi_output,'statu'=>$zhi_statu,'extends'=>$extends,'comment'=>$all_arr[$i]['comment'],'comment3'=>$comment3);
    
    				/*渠道、销售帐号、收款帐号--START*/
					$back_sold_account	= $sold_account->D->get_one_by_field(array('account_name'=>$all_arr[$i]['sold_account']),'id');//取得销售帐号ID
					$back_relations		= $sold_relation_conf->get_default_payrec($back_sold_account['id']);//取得关联的渠道ID与收款ID

					/*如果填写了收款ID*/
					if($all_arr[$i]['payrec_account']){
						$backpayrec = $payrec_account->D->get_one_by_field(array('payrec_account'=>$all_arr[$i]['payrec_account']),'id');
						$back_relations['payrec_id'] = $backpayrec['id'];
					}
                    
                    /*记录市场指导价，计算sku所占收入比例*/
                    $market_price = $product_cost->D->get_one_by_field(array('pid'=>$all_arr[$i]['pid']),'cost3');
                    $insert_arr['market_price'] = $market_price['cost3'];
                    
					$insert_arr['sold_way'] = $back_relations	['way_id'];
					$insert_arr['sold_id']	= $back_sold_account['id'];
					$insert_arr['payrec_id']= $back_relations	['payrec_id'];
 			   	    /*------------------------END*/
                    
                    /*如果sold_account为B2B类型，那么b2b_customers必填*/
                    if (strtolower($all_arr[$i]['sold_account']) == 'b2b'){
                        $b2b_id = $b2bcorpbsl->D->get_one_by_field(array('corpname'=>$all_arr[$i]['b2b_customers']),'id');
                        $insert_arr['b2b_customers'] = $b2b_id['id'];
                    }
                    
    				$sid 		= $process->D->insert($insert_arr);
                    if(strtolower($all_arr[$i]['status']) == 'outputed'){
    					$process->D->update_by_field(array('id'=>$sid),array('mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name']));
 				    }
    				
                }
                if($sid){$successnum++;}//成功统计量+1;
                
				
			}

		}/*for循环结束*/

		/*提示处理*/
		if($countnum_complete)	$msg_completed  = 'Completed或普通订单 '.$countnum_complete.' 个，其中 '.$bad_completed.' 个因库存不足搁置异常订单。\n\n';
		if($countnum_uncleared) $msg_uncleared 	= 'Uncleared订单 '.$countnum_uncleared.' 个已搁置异常订单。\n\n';
		if($countnum_outputed)	$msg_outputed	= 'Outputed订单 '.$countnum_outputed.' 个直接出库。\n\n';
		if($countnum_compared)  $msg_compared	= 'Compared订单 '.$countnum_compared.' 个，系统匹配出库 '.$success_compared.'个。\n\n';
		if($bad_compared)		$msg_compared  .= '由于系统无法找到匹配的第三方单号导致失败 '.$bad_compared.' 个。\n失败的第三方单号如下：\n'.$bad_compared_fid.' \n\n';

		if($countnum_cleared) 	$msg_cleared	= 'Cleared订单 '.$countnum_cleared.' 个，系统匹配到 '.$cleared_have_check.' 个，成功还原异常订单 '.$success_cleared.' 个';
		if($bad_cleared)		$msg_cleared   .= ',由于库存不足或无法找到匹配邮箱导致失败 '.$bad_cleared.' 个。\n还原失败的客户邮箱：\n'.$bad_client_email.'\n';

		if($successnum == count($all_arr)-1){$process->D->query('commit');$msg .= '保存成功，详细信息如下：\n\n'.$msg_completed.$msg_uncleared.$msg_outputed.$msg_compared.$msg_cleared;}else{$process->D->query('rollback');$msg = '保存失败';}

		/*解锁表并跳转*/
		$bbid = $locktab->D->update_by_field(array('type'=>0),array('onoff'=>0));
		if($bbid){$this->C->success($msg,'index.php?action=process_shipment&detail=list');}else{$locktab->D->update_by_field(array('type'=>0),array('onoff'=>0));$this->C->success($msg,'index.php?action=process_shipment&detail=list');}


	}

	/*上传文件并读取*/
	else{
		//$this->S->dao('locktab')->D->update_by_field(array('type'=>0),array('onoff'=>0));//导表前再解锁一次,防止上次导完的解锁失败。

		$all_arr		=  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath		=  $this->getLibrary('basefuns')->getsession('filepath');

		/*取系统发货方式*/
		$shipping_back	=  $this->S->dao('shipping')->D->get_allstr('','','','s_name');
		$shipping_arr 	= array();
		foreach($shipping_back as $val){
			$shipping_arr[] = $val['s_name'];
		}
        
		/*取得系统币别，不存在的币种报错*/
		$back_system_rate = $this->S->dao('exchange_rate')->D->get_allstr('','','','code');
		foreach($back_system_rate as $val){
			$back_system_rate_array[] = $val['code'];//转为一维数组
		}

		//处理数组
		$exl_row 		 		= 0;
		$data_error 	 		= 0;
		$not_enougth_nums		= 0;
		$currency_ary 			= array();
		$cur_sku_count	 		= array();
		$not_enougth_arryrow 	= '';
		$process  				= $this->S->dao('process');
		$sold_account			= $this->S->dao('sold_account');
		$payrec_account			= $this->S->dao('finance_payrec_account');
		$sold_relation_conf		= $this->S->dao('sold_relation_conf');
        $product                = $this->S->dao('product');

		$tablelist 				= '';
		$tablelist 			   .= '<table id="mytable">';

		/*表头特殊显示处理*/
		$tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'order_shipment');

		/*如果需要估算运费*/
		$back_slincoushp = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'selling_countship'),'value');

		foreach($all_arr as $k=>$val){
			$exl_row++;
			$tablelist .= '<tr>';
			if( is_array($val) ){
				foreach( $val as $j=>$value) {
					$error_style = '';
					if($j=='warehouse'){//检查是否在当前的仓库表中
					 	$whouse_id = $this->S->dao('esse')->D->select('id','name=\''.$value.'\'');

					 	/*检测配置哪些仓库需要判断库存*/
					 	$backdata_check_stock_whouse = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'check_stock_whouse'),'value,id');
					 	if(empty($backdata_check_stock_whouse['id'])){
					 		$default_allcheck = 1;//未配置过，则全部检测库存
					 	}else{
						 	$backdata_check_stock_whouse = json_decode($backdata_check_stock_whouse['value'],true);
					 	}

						if(!$whouse_id[0]){
							$error_style = ' bgcolor="red" title="仓库名称有误或者不在仓库列表中，请核对！" ';
							$data_error++;
						}


						/*在配置中的仓库才判断库存，如果未配置，则默认需要检测库存*/
						elseif($default_allcheck == 1 || in_array($whouse_id[0],$backdata_check_stock_whouse)){

							$all_arr[$k][$j] = $whouse_id[0];

							/*completed或空状态的订单，则检测库存--Start*/
							$val['status'] = strtolower($val['status']);
							if($val['status'] == 'completed' || $val['status'] == ''){

								/*检测库存是否充足*/
								if(substr_count($val['sku'],'-') > 2){

									$bloder_skuarr = explode('-',$val['sku']);
									unset($bloder_skuarr['3']);
									$bloder_skunew = implode('-',$bloder_skuarr);
								}else{
									$bloder_skunew = $val['sku'];
								}
                                
                                $backchecksku = $product->D->get_one_by_field(array('sku'=>$bloder_skunew),'pid');
                                $sku_data = $process->output_child_sku(' and s.pid='.$backchecksku['pid']);
                                //是否存在子sku
                                if ($sku_data){
                                    foreach($sku_data as $v){
                                        $cur_sku_count[$whouse_id[0]][$v['sku']]+= $v['quantity']*$val['quantity'];
                                        $back_enough = $process->get_allw_allsku(' and temp.sku="'.$v['sku'].'" and temp.wid='.$whouse_id[0]);
                                        
                                        if ($cur_sku_count[$whouse_id[0]][$v['sku']] > $back_enough['0']['sums']){
                                            $back_enough['0']['sums'] = empty($back_enough['0']['sums'])?'0':$back_enough['0']['sums'];
                                            $error_style = ' bgcolor="green" title="sku为：'.$v['sku'].'库存不足,库存可发数'.$back_enough['0']['sums'].'" ';
                                            
                                            $not_enougth_nums++;
    									    $exl_error_msg		 = '存在 <b>'.$not_enougth_nums.'</b> 个库存不足的订单，此订单无法提交！<br>如果存在其它订单与库存不足订单的第三方单号一样，也将一并搁置异常订单。';
                                            
                                            $not_enougth_arryrow.= '<input type=hidden name=not_enougth_row[] value='.$exl_row.'>';//生成隐藏表单，保存库存不足行数。
    									    $not_enougth_arryrow.= '<input type=hidden name=not_enougth_fid[] value='.$val['3rd_part_id'].'>';//生成隐藏表单，保存库存不足第三方单号
                                        }
                                    }
                                    
                                }else{
                                    $back_enough = $process->get_allw_allsku(' and temp.sku="'.$bloder_skunew.'" and temp.wid='.$whouse_id[0]);
    						 		$cur_sku_count[$whouse_id[0]][$bloder_skunew]+= $val['quantity'];//累计SKU发货量
    
    								if($back_enough['0']['sums']< $cur_sku_count[$whouse_id[0]][$bloder_skunew]){
    									$back_enough['0']['sums'] = empty($back_enough['0']['sums'])?'0':$back_enough['0']['sums'];
    									$error_style = ' bgcolor="green" title="sku为：'.$bloder_skunew.'库存不足,库存可发数'.$back_enough['0']['sums'].'" ';
    
    									$not_enougth_nums++;
    									$exl_error_msg		 = '存在 <b>'.$not_enougth_nums.'</b> 个库存不足的订单，此订单无法提交！<br>如果存在其它订单与库存不足订单的第三方单号一样，也将一并搁置异常订单。';
                                        
                                        $not_enougth_arryrow.= '<input type=hidden name=not_enougth_row[] value='.$exl_row.'>';//生成隐藏表单，保存库存不足行数。
				                        $not_enougth_arryrow.= '<input type=hidden name=not_enougth_fid[] value='.$val['3rd_part_id'].'>';//生成隐藏表单，保存库存不足第三方单号
    
    								}
                                }
                               
							}
							/*--End*/

						}
					}


					if($j=='currency'){//检查币种
						if(!in_array($value,$back_system_rate_array)){

							$error_style = ' bgcolor="red" title="币别代码错误或系统未录入该币别" ';
							$data_error++;
						}
					}
					if($j=='sku'){
						 if(empty($value)){
						 	$error_style = '  bgcolor="red" title="SKU不能为空" ';
						 	$data_error++;
						 }
						 elseif(!preg_match("/(^(\d)+-\d+-(\d+)$)|(^(\d)+-\d+-\d+-(\w+)$)/",$value)){
							$error_style = ' bgcolor="red" title="SKU格式不对,格式如(236-41-48或者236-41-48-CD001)" ';
							$data_error++;
						 }
					}
                    
					if($j=='quantity'){
						if(empty($value) || !preg_match('/^[\d]*$/',$value)  || $value<0){
							$error_style = ' bgcolor="red" title="请检查数量！"';
							$data_error++;
						}
					}

					if($j == '3rd_part_id' && strtolower($all_arr[$exl_row]['status']) !='compared'){//判断第三方单号与发货仓库是否同时重复，异常订单也不能。

						if(empty($value)){

							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}else{

							$whouse_id 				= $this->S->dao('esse')->D->get_one_by_field(array('name'=>$all_arr[$exl_row]['warehouse']),'id');
							$back_check_fid_exists	= $process->D->get_one_by_field(array('fid'=>$value,'coin_code'=>$all_arr[$exl_row]['currency']),'id');
							if(is_array($back_check_fid_exists)){
								$error_style = ' bgcolor="red" title="存在与系统已有的第三方单号重复，请检查！" ';
								$data_error++;
							}
						}

					}

					if($j == 'status' && !in_array(strtolower($value),$status_arr)){
							$error_style = ' bgcolor="red" title="不规范的状态！" ';
							$data_error++;
					}

					if($j == 'shipping' && !in_array($value,$shipping_arr)){
							$error_style = ' bgcolor="red" title="系统不存的发货方式，请检查是否填写正确(注意大小写)" ';
							$data_error++;
					}

					if($j == 'country' && !empty($back_slincoushp['value'])){//配置了需要计算运费

						if($default_allcheck == 1 || in_array($whouse_id[0],$backdata_check_stock_whouse)){//需要判断库存的需要计算运费

							$back_shipconcode = $this->S->dao('shipping_code')->D->select('country',' country="'.$value.'" or code2="'.$value.'"');
							if(!$back_shipconcode['country'] && $value != 'United States'){

									$error_style = ' bgcolor="red" title="系统不存在的国家名称或代码，请查看国家列表！" ';
									$data_error++;

							}else{
								$not_enougth_arryrow.= '<input type=hidden value='.$exl_row.' name=nedd_coutshpfare[] >';
							}
						}

					}

					/*销售帐号检测*/
					if($j == 'sold_account'){
						$backsoldaccnt = $sold_account->D->get_one_by_field(array('account_name'=>$value),'id');
						if(!$backsoldaccnt) {
							$error_style = ' bgcolor="red" title="系统不在的销售帐号" ';
							$data_error++;
						}
					}
                    
                    /*b2b客户检测*/
                    if ($j == 'b2b_customers' && strtolower($val['sold_account']) == 'b2b') {
                       
                        $b2bdata = $b2bcorpbsl->D->get_one_by_field(array('corpname'=>$value),'id');
                        if (!is_array($b2bdata)){
                            $error_style = ' bgcolor="red" title="B2B客户填写有误"';
                            $data_error++;
                        }
                        
                    }
                    
					/*收款帐号检测*/
					if($j == 'payrec_account'){

						if(!empty($value)){
							$backpayrec	 = $payrec_account->D->get_one_by_field(array('payrec_account'=>$value),'id');
							if(!$backpayrec) {
								$error_style = ' bgcolor="red" title="系统不在的收款帐号" ';
								$data_error++;
							}
						}else{//取出系统默认的名称

							if($backsoldaccnt){
								$backpayrec 	= $sold_relation_conf->get_default_payrec($backsoldaccnt['id']);
								$value 			= $backpayrec['payrec_account'];
								$error_style 	= ' title="由于不填写收款帐号，系统取出了该销售帐号默认关联的收款帐号" ';
							}
						}
					}


					$tablelist .= '<td '.$error_style.'>&nbsp;'.$value.'</td>';
				}

			}
			$tablelist .= '</tr>';
		}
		$tablelist .= '</table>';

		/*错误判断*/
		if(!$data_error && isset($all_arr) && !$not_enougth_nums){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />'.$not_enougth_arryrow;

			$tablelist .= '<font color="#577dc6" size="-1">'.$not_enougth_skutips.'</font><br><input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=process_shipment&detail=list">';
		}elseif($data_error){
			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}
	}
	$submit_action = 'index.php?action=process_shipment&detail=import';
	$temlate_exlurl = 'data/uploadexl/sample/order_shipment.xls';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('exl_error_msg'=>$exl_error_msg,'exl_error_width'=>600,'title'=>'导入订单-选择导入类型(import_exl)-出货列表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();

}


/*编辑(查看)出货单,权限需要配置*/
elseif($detail == 'editshipment'){
	//同组人才能修改
	//$this->C->service('warehouse')->check_thegroup_right('id', $id, 1);

    //权限控制非本人订单不可操作
    $process    = $this->S->dao('process');
    $_cuser     = $process->D->get_one(array('id'=>$id),'cuser');

    if(!$this->C->service('admin_access')->checkResRight('r_w_editmod','mod',$_cuser)){$this->C->sendmsg();}

	$process  = $this->S->dao('process');
	$backdata = $process->D->get_one_by_field(array('id'=>$id),'b2b_customers,sold_way,sold_id,payrec_id,pid,product_name,provider_id,id,order_id,fid,deal_id,detail_id,sku,quantity,price,sold_way,buyer_id,comment,extends,cuser,statu,coin_code');

	/*将数组中的压缩内容解压并作为字段增加入数据中*/
	$backdata = $this->C->service('warehouse')->decodejson($backdata);

	/*生成仓库下拉*/
	$wdata 				= $this->C->service('warehouse')->get_whouse('houseid class="check_notnull"','name','id','id',$backdata['provider_id']);

	/*生成物流下拉*/
	$e_shippingstr 		= $this->C->service('global')->get_shipping('e_shipping class="check_notnull"','s_name','s_name','s_name',$backdata['e_shipping']);

	/*生成币别下拉*/
	$currencystr   		= $this->C->service('warehouse')->get_coincode_html('coin_code','class="check_notnull"',$backdata['coin_code']);

	/*生成销售帐号下拉*/
	$sold_accountstr 	= $this->C->service('global')->get_sold_way(1,'sold_account','account_name',$backdata['sold_id'],'class="check_notnull" onchange=autonext(this)');

	/*生成收款帐号下拉*/
	$payrec_account		= $this->C->service('global')->get_sold_way(1,'finance_payrec_account','payrec_account',$backdata['payrec_id']);
    
    /*生成b2b客户下拉*/
    $b2b_customers      = $this->C->service('global')->get_b2b_customers(1,'b2bcorpbsl','corpname',$backdata['b2b_customers']);


	/*表单配置*/
	$conform 			= array('method'=>'post','action'=>'index.php?action=process_shipment&detail=modeditshipment');


	$disinputarr 		= array();

	$disinputarr['singleorder']		= array('showname'=>'<font color=#ff2a00 size=-1>①当前记录修改项目如下</font>','datatype'=>'se','datastr'=>'<div style="height:30px"></div>');
	$disinputarr['id'] 				= array('showname'=>'id','datatype'=>'h','value'=>$id);
	$disinputarr['statu']			= array('showname'=>'statu','datatype'=>'h','value'=>$backdata['statu']);
	$disinputarr['provider_id']		= array('showname'=>'provider_id','datatype'=>'h','value'=>$backdata['provider_id']);
	$disinputarr['order_id']		= array('showname'=>'order_id','datatype'=>'h','value'=>$backdata['order_id']);
	$disinputarr['detail_id']		= array('showname'=>'detail_id','datatype'=>'h','value'=>$backdata['detail_id']);
	$disinputarr['old_quantity']	= array('showname'=>'old_quantity','datatype'=>'h','value'=>$backdata['quantity']);
	$disinputarr['old_sku'] 		= array('showname'=>'old_sku','datatype'=>'h','value'=>$backdata['sku']);

	$disinputarr['order_idd'] 		= array('showname'=>'order_id','value'=>$backdata['order_id'],'showtips'=>'<span class=tips>此项只供查看</span>','inextra'=>'disabled');
	$disinputarr['deal_id'] 		= array('showname'=>'deal_id','value'=>$backdata['deal_id']);
	$disinputarr['fid'] 			= array('showname'=>'3rd_part_id','value'=>$backdata['fid'],'extra'=>' *','inextra'=>'class="check_notnull"');
	$disinputarr['sku'] 			= array('showname'=>'sku','value'=>$backdata['sku'],'extra'=>' *','inextra'=>'class="check_notnull"');
	$disinputarr['e_listing'] 		= array('showname'=>'listing','value'=>$backdata['e_listing']);
	$disinputarr['quantity'] 		= array('showname'=>'quantity','value'=>$backdata['quantity'],'extra'=>' *','inextra'=>'class="check_notnull check_isnum"');
	$disinputarr['item_price'] 		= array('showname'=>'item_price','value'=>$backdata['price'],'showtips'=>'<span class=tips></span>');
	$disinputarr['e_item_tax'] 		= array('showname'=>'item_tax','value'=>$backdata['e_item_tax'],'showtips'=>'<span class=tips></span>');
	$disinputarr['e_shipping_price']= array('showname'=>'shipping_price','value'=>$backdata['e_shipping_price'],'showtips'=>'<span class=tips></span>');
	$disinputarr['e_shipping_tax']  = array('showname'=>'shipping_tax','value'=>$backdata['e_shipping_tax'],'showtips'=>'<span class=tips></span>');
	$disinputarr['e_performance_fee']= array('showname'=>'performance_fee','value'=>$backdata['e_performance_fee'],'showtips'=>'<span class=tips></span>');
	$disinputarr['e_shipping_fee']  = array('showname'=>'shipping_fee','value'=>$backdata['e_shipping_fee'],'showtips'=>'<span class=tips> (系统自动计算)</span>');
	$disinputarr['coin_code']		= array('showname'=>'currency','datatype'=>'se','datastr'=>$currencystr,'extra'=>' *');

	$disinputarr['comment'] 		= array('showname'=>'comment','value'=>$backdata['comment']);

	$disinputarr['wholeorder']		= array('showname'=>'<font color=#ff2a00 size=-1>②整个订单修改项目如下</font>','datatype'=>'se','datastr'=>'<div style="height:30px"></div>');
	$disinputarr['warehouse'] 		= array('showname'=>'warehouse','datatype'=>'se','datastr'=>$wdata,'extra'=>' *');
	$disinputarr['e_shipping'] 		= array('showname'=>'shipping','datatype'=>'se','datastr'=>$e_shippingstr,'extra'=>' *');
	$disinputarr['sold_account']	= array('showname'=>'sold_account','datatype'=>'se','datastr'=>$sold_accountstr,'extra'=>' *');
	$disinputarr['payrec_account']	= array('showname'=>'payrec_account','datatype'=>'se','datastr'=>$payrec_account);
    $disinputarr['b2b_customers']   = array('showname'=>'b2b_customers','datatype'=>'se','datastr'=>$b2b_customers,'extra'=>'*');

	$disinputarr['e_receperson'] 	= array('showname'=>'receive_person','value'=>$backdata['e_receperson']);
	$disinputarr['buyer_id']		= array('showname'=>'Buyer_ID','value'=>$backdata['buyer_id']);
	$disinputarr['e_address1'] 		= array('showname'=>'address1','value'=>$backdata['e_address1']);
	$disinputarr['e_address2'] 		= array('showname'=>'address2','value'=>$backdata['e_address2']);
	$disinputarr['e_city'] 			= array('showname'=>'city','value'=>$backdata['e_city']);
	$disinputarr['e_state'] 		= array('showname'=>'state','value'=>$backdata['e_state']);
	$disinputarr['e_post_code'] 	= array('showname'=>'post_code','value'=>$backdata['e_post_code']);
	$disinputarr['e_country'] 		= array('showname'=>'country','value'=>$backdata['e_country']);
	$disinputarr['e_tel'] 			= array('showname'=>'tel','value'=>$backdata['e_tel']);
	$disinputarr['e_email'] 		= array('showname'=>'email','value'=>$backdata['e_email']);

	$bannerstr = '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:630px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
	$bannerstr.= '1、当前记录修改项只修改本条记录的信息。<br>';
	$bannerstr.= '2、整个订单修改项会修改整个订单，如一个订单有两条记录，修改了shipping保存后会修改两条记录的shipping。<br>';
	$bannerstr.= '</div>';
	$jslink = "<script src='./staticment/js/process_shipment.js'></script>\n";
	$jslink.= "<script src='./staticment/js/new.js'></script>\n";

	$this->V->mark(array('title'=>'编辑出货单-出货列表(list)'));
	$temp = 'pub_edit';

}

/*保存编辑的出货单*/
elseif($detail == 'modeditshipment'){

	//同组人才能修改
	//$this->C->service('warehouse')->check_thegroup_right('id', $id, 1);

    //权限控制
    $process    = $this->S->dao('process');
    $_cuser     = $process->D->get_one(array('id'=>$id),'cuser');

    if(!$this->C->service('admin_access')->checkResRight('r_w_editmod','mod',$_cuser)){$this->C->sendmsg();}

	if(empty($sku)){$this->C->sendmsg('无效的SKU!');}
	if(empty($fid)){$this->C->sendmsg('第三方单号不能为空!');}

	$backdata = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name,shipping_weight');
	if(!$backdata) $this->C->sendmsg('修改失败，系统不存在的SKU!');

	//$process  	= $this->S->dao('process');
    

	/*不能出现不同订单号的相同的第三方单号*/
	$backfid  = $process->D->get_one_by_field(array('fid'=>$fid,'property'=>'出仓单','protype'=>'售出'),'order_id');
	if(isset($backfid['order_id']) && $backfid['order_id'] != $order_id){

		/*排除重发单*/
		$back_zfid = $process->D->get_one_by_field(array('id'=>$detail_id),'fid');
		if($back_zfid['fid'] != $fid)	$this->C->sendmsg('系统已经存在的第三方单号!');
	}

	/*检测配置哪些仓库需要判断库存*/
	//if($provider_id != $houseid || $old_quantity != $quantity || $old_sku != $sku){

		$default_allcheck 	= '';
		$skucount			= '';

		$backdata_check_stock_whouse = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'check_stock_whouse'),'value,id');
		if(empty($backdata_check_stock_whouse['id'])){
			$default_allcheck = 1;//未配置过，则全部检测库存
		}else{
		 	$backdata_check_stock_whouse = json_decode($backdata_check_stock_whouse['value'],true);
		}
	//}

	/*如果需要估算运费*/
	$back_slincoushp = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'selling_countship'),'value');
	if(!empty($back_slincoushp['value']) && ($default_allcheck == 1 || in_array($houseid,$backdata_check_stock_whouse))){

		$back_shipconcode = $this->S->dao('shipping_code')->D->select('country',' country="'.$e_country.'" or code2="'.$e_country.'"');//查找国家代码与名称
		if($back_shipconcode['country']){
		      //simon那边运费需要自己填写，不需要估算，此处去掉。
			//$e_shipping_fee = $this->C->service('global')->count_shipping_fare($e_shipping,$backdata['shipping_weight']*$quantity,$e_country);

			if($coin_code != 'CNY'){
				$e_shipping_fee = -$this->C->service('exchange_rate')->change_rate('CNY',$coin_code,$e_shipping_fee);//转的成当前币别
			}

		}else{
			$this->C->success('修改失败，国家名称不存在，请查看国家列表','index.php?action=process_shipment&detail=editshipment&id='.$id);exit();
		}
	}


	$backrecord = $process->D->get_one_by_field(array('order_id'=>$order_id),'count(*) as records');

	/*销售帐号处理*/
	$backrelaxtion 			= $this->S->dao('sold_relation_conf')->get_default_payrec($sold_account);
	$sold_way 				= $backrelaxtion['way_id'];
	$finance_payrec_account = empty($finance_payrec_account)?$backrelaxtion['payrec_id']:$finance_payrec_account;

	/*扩展内容处理*/
	$extends  = array('e_listing'=>$e_listing,'e_item_tax'=>$e_item_tax,'e_shipping_price'=>$e_shipping_price,'e_shipping_tax'=>$e_shipping_tax,'e_performance_fee'=>$e_performance_fee,'e_shipping_fee'=>$e_shipping_fee,'e_shipping'=>$e_shipping,'e_receperson'=>$e_receperson,'e_tel'=>$e_tel,'e_email'=>$e_email,'e_address1'=>$e_address1,'e_address2'=>$e_address2,'e_city'=>$e_city,'e_state'=>$e_state,'e_country'=>$e_country,'e_post_code'=>$e_post_code);

	/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
	$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);



	/*如果修改了仓库，则订单的每条记录都重新扫描一遍判断库存*/
	if($provider_id != $houseid){

		/*该仓库需要判断库存*/
		if($default_allcheck == 1 || in_array($houseid,$backdata_check_stock_whouse)){

			$backdatalist = $process->D->get_allstr(' and order_id="'.$order_id.'"','','','id,sku,quantity');
			for($i=0; $i<count($backdatalist); $i++){

				/*本条记录以新仓库新SKU发货的库存判断*/
				$compare_num = $backdatalist[$i]['id'] == $id ? $quantity	:$backdatalist[$i]['quantity'];
				$compare_sku = $backdatalist[$i]['id'] == $id ? $sku		:$backdatalist[$i]['sku'];

				$back_enough = $process->get_allw_allsku(' and temp.sku="'.$compare_sku.'" and temp.wid='.$houseid);

				if($back_enough['0']['sums'] < $compare_num){
					$skucount.= $compare_sku.',';
				}
			}

			if($skucount){$this->C->success('修改失败，以下SKU库存不足：\n'.$skucount,'index.php?action=process_shipment&detail=editshipment&id='.$id);exit();}
		}
	}

	/*如果没有修改仓库，修改了SKU或数量，只是本条记录重新判断库存*/
	elseif($old_quantity != $quantity || $old_sku != $sku){

		/*该仓库需要判断库存*/
		if($default_allcheck == 1 || in_array($houseid,$backdata_check_stock_whouse)){

			/*修改了SKU*/
			if($old_sku != $sku){

				$back_enough = $process->get_allw_allsku(' and temp.sku="'.$sku.'" and temp.wid='.$houseid);
				if($back_enough['0']['sums'] < $quantity){
					$skucount.= $sku.',';
				}
			}

			/*仅仅修改了数量*/
			elseif($old_quantity != $quantity){

				$back_enough = $process->get_allw_allsku(' and temp.sku="'.$sku.'" and temp.wid='.$houseid);//求可发量
				$new_sums	 = $back_enough['0']['sums'] + $old_quantity;//新的可发量
				if($new_sums < $quantity){
					$skucount.= $sku.',';
				}
			}

			if($skucount){$this->C->success('修改失败，以下SKU库存不足：\n'.$skucount,'index.php?action=process_shipment&detail=editshipment&id='.$id);exit();}
		}
	}


	/*只是一条记录的订单*/
	if($backrecord['records'] == 1){
 
		$update_arr = array('b2b_customers'=>$b2bcorpbsl,'sold_way'=>$sold_way,'sold_id'=>$sold_account,'payrec_id'=>$finance_payrec_account,'provider_id'=>$houseid,'sku'=>$sku,'fid'=>$fid,'deal_id'=>$deal_id,'pid'=>$backdata['pid'],'product_name'=>$backdata['product_name'],'price'=>$item_price,'quantity'=>$quantity,'buyer_id'=>$buyer_id,'coin_code'=>$coin_code,'extends'=>$extends,'comment'=>$comment);

		if($old_sku != $sku){//如果修改了SKU，即时成本同时修改
			$this->C->service('exchange_rate');//实例化自动包含文件
			$update_arr['price2']	= $this->C->service('finance')->get_productcost($backdata['pid']);
		}

		/*执行更新*/
		$sid = $process->D->update_by_field(array('id'=>$id),$update_arr);
		if($sid){$msg = '修改成功';}else{$msg = '修改失败';}
		$this->C->success($msg,'index.php?action=process_shipment&detail=editshipment&id='.$id);

	}

	/*如果一个订单有多条记录*/
	else{

		$error_num = 0;

		$backdatalist = $process->D->get_allstr(' and order_id="'.$order_id.'"','','','id,extends');
		for($i = 0; $i<count($backdatalist);$i++){

				$extends_i 					= json_decode($backdatalist[$i]['extends'],1);
				$extends_i['e_shipping']	= $e_shipping;
				$extends_i['e_receperson']	= $e_receperson;
				$extends_i['e_tel']			= $e_tel;
				$extends_i['e_email']		= $e_email;
				$extends_i['e_address1']	= $e_address1;
				$extends_i['e_address2']	= $e_address2;
				$extends_i['e_city']		= $e_city;
				$extends_i['e_state']		= $e_state;
				$extends_i['e_country']		= $e_country;
				$extends_i['e_post_code']	= $e_post_code;

				$extends_i	= get_magic_quotes_gpc()?addslashes(json_encode($extends_i)):json_encode($extends_i);
				$cid 		= $process->D->update_by_field(array('id'=>$backdatalist[$i]['id']),array('sold_way'=>$sold_way,'sold_id'=>$sold_account,'payrec_id'=>$finance_payrec_account,'provider_id'=>$houseid,'buyer_id'=>$buyer_id,'extends'=>$extends_i));
				if(!$cid) $error_num++;

		}

		$update_arr  = array('sku'=>$sku,'b2b_customers'=>$b2bcorpbsl,'fid'=>$fid,'deal_id'=>$deal_id,'pid'=>$backdata['pid'],'product_name'=>$backdata['product_name'],'price'=>$item_price,'quantity'=>$quantity,'coin_code'=>$coin_code,'extends'=>$extends,'comment'=>$comment);

		/*如果修改了SKU，即时成本同时修改*/
		if($old_sku != $sku){
			$this->C->service('exchange_rate');//实例化自动包含文件
			$update_arr['price2'] = $this->C->service('finance')->get_productcost($backdata['pid']);
		}
		$sid = $process->D->update_by_field(array('id'=>$id),$update_arr);
		if($sid && empty($error_num)){$msg = '修改成功';}else{$msg = '修改失败';}
		$this->C->success($msg,'index.php?action=process_shipment&detail=editshipment&id='.$id);

	}

}

/*删除出货单,权限-1.只能删除自己的,2.出货单状态,接收过的不能更改（此处与转仓出库共用）*/
elseif($detail == 'delshipment'){

	$process  = $this->S->dao('process');
    $_cuser = $process->D->get_one(array('id'=>$id),'cuser');
    if(!$this->C->service('admin_access')->checkResRight('r_w_delouted','mod',$_cuser)){$this->C->ajaxmsg(0);}

	$sid = $process->D->delete_by_field(array('id'=>$id));
	if($sid){$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg(0,'删除失败');}
}


/**
 * 回退已接收的出仓单,权限-（物流权限)
 */
elseif($detail == 'delout'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_backorder')){$this->C->ajaxmsg(0);}//接收回退判断

	$sid = $this->S->dao('process')->D->update_by_field(array('order_id'=>$order_id),array('statu'=>'0','active'=>'0','output'=>'0','mdate'=>'','muser'=>''));
	if($sid) {$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0);}
}


/*填写物流跟踪号*/
elseif($detail =='editcomment'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_backordernum')){$this->C->ajaxmsg(0);}//修改备注权限判断
	if($this->S->dao('process')->D->update_by_field(array('id'=>$id),array('comment2'=>$comment2))){echo '1';}
}


/*确认出货,权限*/
elseif($detail == 'modoutstock'){
	/*防止逆向操作*/
	$process = $this->S->dao('process');
	$strid = stripslashes($strid);
	$backdata = $process->D->get_allstr(' and order_id in('.$strid.')','','','statu,muser');

    if(!$this->C->service('admin_access')->checkResRight('r_w_sureout')){$this->C->ajaxmsg(0);}

	/*执行确认*/
	$sid = $process->D->update_sql('where order_id in('.$strid.')',array('statu'=>'3','active'=>'1','output'=>'1','ruser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time())));
	if($sid){echo '确认成功';}else{echo '确认失败';}

}

/*确认打印*/
elseif($detail == 'modprint'){

    //2出库订单1物料调拨
    if ($t == '2')
        if(!$this->C->service('admin_access')->checkResRight('r_w_modprint')){$this->C->ajaxmsg(0);}
    if ($t == '1')
       if(!$this->C->service('admin_access')->checkResRight('r_p_waitleave')){$this->C->ajaxmsg(0);}

	/*防止逆向操作*/
	$process = $this->S->dao('process');
	$strid = stripslashes($strid);
	$backdata = $process->D->get_allstr(' and order_id in('.$strid.')','','','statu,muser');

	foreach ($backdata as $val){
		if($val['statu'] !='1' || $val['muser']!=$_SESSION['eng_name']){$this->C->ajaxmsg(0,'不合理操作,只能操作本人在已接收中的记录!');}
	}

	/*执行确认*/
	$sid = $process->D->update_sql('where order_id in('.$strid.')',array('statu'=>'2','muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time()),'ruser'=>$_SESSION['eng_name'],'rdate'=>date('Y-m-d H:i:s',time())));
	if($sid){echo '1';}else{echo '0';}

}

/*接收出货订单*/
elseif($detail == 'recemod'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_receive')){$this->C->ajaxmsg(0);}//接收权限判断

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


/*添加出货单填写页面*/
elseif($detail == 'neworder'){
    if(!$this->C->service('admin_access')->checkResRight('r_w_addmod')){$this->C->sendmsg();}

	$default_warehouse 	= $this->C->service('global')->sys_settings('makeshipment_house','sys');
	$whouse 			= $this->C->service('warehouse')->get_whouse('houseid','name','id','id',$default_warehouse);/*获得仓库*/
	$coin_code 			= $this->C->service('warehouse')->get_coincode_html('coin_code'); /*取得币种列表*/
	$e_shippingstr 		= $this->C->service('global')->get_shipping('e_shipping[]','s_name','s_name','s_name',$backdata['e_shipping']);/*生成物流下拉*/
	$sold_accountstr	= $this->C->service('global')->get_sold_way(1,'sold_account[]','account_name','','onchange="autonext(this)" style="width:100px;"');/*销售帐号下拉*/
	$sold_payrecstr		= $this->C->service('global')->get_sold_way(1,'finance_payrec_account','payrec_account','','style="width:110px"');/*收付款帐号*/
    $b2b_customersstr   = $this->C->service('global')->get_b2b_customers(1,'b2bcorpbsl[]','corpname','','style="width:110px"');/*b2b客户*/
    
	$this->V->mark(array('title'=>'添加出货-出货列表(list)','whouse'=>$whouse,'coin_code'=>$coin_code,'e_shippingstr'=>$e_shippingstr,'sold_accountstr'=>$sold_accountstr,'sold_payrecstr'=>$sold_payrecstr,'b2b_customers'=>$b2b_customersstr));
	$this->V->set_tpl('adminweb/process_shipment');
	display();
}

/*执行保存添加出货单*/
elseif($detail == 'modneworder'){
	if($fid){

		$error_sku	 = '';
		for ($i=0;$i<count($fid);$i++){
			if(empty($pid[$i]))	$error_sku .=  $sku[$i].',\n';
		}
		if($error_sku){
			$this->C->success('保存失败，存在无法获取产品ID的SKU:\n'.$error_sku.'请检查重试!','index.php?action=process_shipment&detail=neworder');
			exit();
		}

		$process 	= $this->S->dao('process');
		$objrelax	= $this->S->dao('sold_relation_conf');
					  $this->C->service('exchange_rate');//实例化自动包含文件
		$finansvice = $this->C->service('finance');
        $order      = $this->C->service('order');
        $product_cost = $this->S->dao('product_cost');
        


		/*生成出仓单号,取得出仓最大单号，并取出数字，w+7位数字，不够补0*/
		$max = $this->C->service('warehouse')->get_maxorder_manay('出仓单','x',$process);
		$coin_rate = 100;

		/*获得当前汇率与期号*/
		$backrate = $this->C->service('warehouse')->get_stage_rate($coin_code);

        $arrinsert = array();
        $oid       = 0;
        $error_id  = 0;
        $process->D->query('begin');//开启事务

        $backurl = 'index.php?action=process_shipment&detail=list'; //检测是否锁表
        if(!$order->checklock('begin',$url)){$this->C->sendmsg($backurl);}
        
		for($i=0;$i<count($fid);$i++){

			/*扩展内容处理*/
			$extends = array('e_listing'=>$e_listing[$i],'e_item_tax'=>$e_item_tax[$i],'e_shipping_price'=>$e_shipping_price[$i],'e_shipping_tax'=>$e_shipping_tax[$i],'e_performance_fee'=>$e_performance_fee[$i],'e_shipping_fee'=>$e_shipping_fee[$i],'e_shipping'=>$e_shipping[$i],'e_receperson'=>$e_receperson[$i],'e_tel'=>$e_tel[$i],'e_email'=>$e_email[$i],'e_address1'=>$e_address1[$i],'e_address2'=>$e_address2[$i],'e_city'=>$e_city[$i],'e_state'=>$e_state[$i],'e_country'=>$e_country[$i],'e_post_code'=>$e_post_code[$i]);

			/*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
			$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

			/*保存即时成本，币别统一转换本位币(CNY)*/
			$price2 = $finansvice->get_productcost($pid[$i]);

			$backwayid = $objrelax->D->get_one_by_field(array('account_id'=>$sold_account[$i]),'way_id,payrec_id');//取得渠道ID

			/*如果收款帐号没填，取关联的*/
			$finance_payrec_account[$i] = empty($finance_payrec_account[$i])?$backwayid['payrec_id']:$finance_payrec_account[$i];
            
            /*组的sku是否有子sku,子sku有的话下单*/
            $sku_data = $process->output_child_sku(' and s.pid='.$pid[$i]);
            
            if ($sku_data && is_array($sku_data)){
                foreach($sku_data as $v){
                    $market_price = $product_cost->D->get_one_by_field(array('pid'=>$v['child_pid']),'cost3');
                    $arrinsert = array(
                        'market_price'=>$market_price['cost3'],
                        'provider_id'=>$houseid,
                        'sku'=>$v['sku'],
                        'fid'=>$fid[$i],
                        'deal_id'=>$deal_id[$i],
                        'pid'=>$v['child_pid'],
                        'product_name'=>$v['product_name'],
                        'price'=>$price[$i],
                        'price2'=>$price2,
                        'coin_code'=>$coin_code,
                        'stage_rate'=>$backrate['stage_rate'],
                        'quantity'=>$quantity[$i],
                        'cdate'=>date('Y-m-d H:i:s',time()),
                        'cuser'=>$_SESSION['eng_name'],
                        'muser'=>$_SESSION['eng_name'],
                        'mdate'=>date('Y-m-d H:i:s',time()),
                        'ruser'=>$_SESSION['eng_name'],
                        'rdate'=>date('Y-m-d H:i:s',time()),
                        'sold_way'=>$backwayid['way_id'],
                        'b2b_customers'=>$b2bcorpbsl[$i],
                        'sold_id'=>$sold_account[$i],
                        'payrec_id'=>$finance_payrec_account[$i],
                        'buyer_id'=>$buyer_id[$i],
                        'property'=>'出仓单',
                        'protype'=>'售出',
                        'extends'=>$extends,
                        'comment'=>$comment[$i],
                        'isover'=>'N'
                    );
                    //是否是异常订单 
                    /*if (in_array($fid[$i],$istrue)){
                        $arrinsert['isover']='Y';
                        $arrinsert['comment3']='1';
        
                        $_order_id = $process->D->get_one(array('fid'=>$fid[$i]),'order_id');
        
                        if(!$_order_id){
                       	    $_order_id = 'x'.sprintf("%07d",substr($max,1)+$oid);
                            $oid++;
                        }
        
                        $arrinsert['order_id'] = $_order_id;
                    }else{*/
                        $arrinsert['order_id']   = 'x'.sprintf("%07d",substr($max,1)+$oid);
                        $oid++;
                    //}
                    $sid = $process->D->insert($arrinsert);
                    if (!$sid) $error_id++;
                }
            }else{
                $market_price = $product_cost->D->get_one_by_field(array('pid'=>$pid[$i]),'cost3');
                $arrinsert = array(
                    'market_price'=>$market_price['cost3'],
                    'provider_id'=>$houseid,
                    'sku'=>$sku[$i],
                    'fid'=>$fid[$i],
                    'deal_id'=>$deal_id[$i],
                    'pid'=>$pid[$i],
                    'product_name'=>$product_name[$i],
                    'price'=>$price[$i],
                    'price2'=>$price2,
                    'coin_code'=>$coin_code,
                    'stage_rate'=>$backrate['stage_rate'],
                    'quantity'=>$quantity[$i],
                    'cdate'=>date('Y-m-d H:i:s',time()),
                    'cuser'=>$_SESSION['eng_name'],
                    'muser'=>$_SESSION['eng_name'],
                    'mdate'=>date('Y-m-d H:i:s',time()),
                    'ruser'=>$_SESSION['eng_name'],
                    'rdate'=>date('Y-m-d H:i:s',time()),
                    'sold_way'=>$backwayid['way_id'],
                    'b2b_customers'=>$b2bcorpbsl[$i],
                    'sold_id'=>$sold_account[$i],
                    'payrec_id'=>$finance_payrec_account[$i],
                    'buyer_id'=>$buyer_id[$i],
                    'property'=>'出仓单',
                    'protype'=>'售出',
                    'extends'=>$extends,
                    'comment'=>$comment[$i],
                    'isover'=>'N'
                    );
                //是否是异常订单
                /*if (in_array($fid[$i],$istrue)){
                    $arrinsert['isover']='Y';
                    $arrinsert['comment3']='1';
    
                    $_order_id = $process->D->get_one(array('fid'=>$fid[$i]),'order_id');
    
                    if(!$_order_id){
                   	    $_order_id = 'x'.sprintf("%07d",substr($max,1)+$oid);
                        $oid++;
                    }
    
                    $arrinsert['order_id'] = $_order_id;
                }else{*/
                    $arrinsert['order_id']   = 'x'.sprintf("%07d",substr($max,1)+$oid);
                    $oid++;
                //}
                
                $sid = $process->D->insert($arrinsert);
                if (!$sid) $error_id++;
            }
		}

        if(empty($error_id)){
			    $process->D->query('commit');
                $msg='添加成功';
			}else{
                $process->D->query('rollback');
                $msg='添加失败';
	   }

       $did = $order->checklock('end');

       if($did){
            $this->C->success($msg,$backurl);
        }else{
            $order->checklock('end');$this->C->success($msg,$backurl);
       }

	}
}


/*检测SKU取得PID添加到隐藏表单,用于明细表的Pid*/
elseif($detail == 'get_pid'){
	$backdata = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name,unit_box');
	echo  json_encode($backdata);
}

/*
 * create on 2012-05-03
 * by wall
 * 判断库存是否充足
 * 传入数组可能有重复SKU
 * */
elseif ($detail == 'check_quantity') {
    
    $product = $this->S->dao('product');
    $process = $this->S->dao('process');
	$length = count($sku);
	$skuquantity = array();
	$result = array();
    $cur_sku_count = array();
	if ( $length == 0 ) {
		$result['msg'] = '请输入数据！不允许提交空数据！';
	}
	else {
		$checkid = $this->C->service('global')->get_needchk_whouse($houseid);
		for($i=0; $i<$length; $i++) {
			$tempsku = $sku[$i];
			$rs = $this->S->dao('product')->get_product_by_sku($tempsku);
			if ($rs) {
				if ($skuquantity[$tempsku] != -2) {
                    
					if ($checkid) {
					    $_pid       = $product->D->get_one_by_field(array('sku'=>$tempsku),'pid');
                        $pid_data   = $process->output_child_sku(' and s.pid='.$_pid['pid']);
                        //如果组合sku存在子sku
                        if ($pid_data && is_array($pid_data)){
                            foreach($pid_data as $v){
                                $cur_sku_count[$houseid][$v['sku']] += $quantity[$i] * $v['quantity'];
                                $re = $process->get_allw_allsku(' and temp.sku="'.$v['sku'].'" and temp.wid='.$houseid);
                                if ($re['0']['sums'] < $cur_sku_count[$houseid][$v['sku']]) {
                                    $skipp.= 'SKU：'.$v['sku'].'库存不足，可发：'.($re['0']['sums']?$re['0']['sums']:'0').'个。';
                                    $skuquantity[$tempsku] = -2;
                                }  
                            }
                             if($skipp) $skip = '这是组装产品，提取出的'.$skipp;
                        }else{
                            $cur_sku_count[$houseid][$tempsku] += $quantity[$i];
                            $kc = $process->get_allw_allsku(' and temp.sku="'.$tempsku.'" and temp.wid='.$houseid);
        					if ($kc['0']['sums'] < $cur_sku_count[$houseid][$tempsku]) {
        					    $skip= 'SKU：'.$tempsku.'库存不足，可发：'.($kc['0']['sums']?$kc['0']['sums']:'0').'个';
        						$skuquantity[$tempsku] = -2;
				            }
                        }
				    }
                }
            }else {
                $skuquantity[$tempsku] = -1; 
            }
            
			$result[] = array('sku' => $tempsku, 'quantity'=> $skuquantity[$tempsku], 'num'=>$i,'skip'=>$skip);
	   }
    }
	echo json_encode($result);
}


/*
 * 2012-09-04 by hanson
 * 检测第三方单号，不能与系统的重复*/
elseif($detail == 'check_fid'){

	$process = $this->S->dao('process');
	$backarr = array();

	for($i = 0; $i<count($fidarr); $i++){
		$backfid = $process->D->get_one_by_field(array('fid'=>$fidarr[$i]),'id');
		if($backfid['id']){
			$backarr[] = $i;
		}
	}

	if(!$backarr) $backarr['msg'] = '0';

	echo json_encode($backarr);
}
/*
 * create on 2012-05-22
 * by wall
 * 导出DHL发货订单
 * */
elseif ($detail == 'outport_dhl') {
	$datalist = $this->S->dao('process')->get_dhl_list();

	/*转换货币，默认人民币不用转,导出统一用人民币显示*/
	foreach($datalist as &$val){
		if($val['coin_code']!='USD'){
			$new_price = $this->C->service('exchange_rate')->change_usd($val['coin_code'],$val['cost2']);
			$val['cost2'] = number_format($new_price,2);//全站数据保留两位小数
		}
	}

	$dhl_list_num = 0;
	$dhl_list = array();
	//产品条目数量*
	$dhl_item_num = 1;
	//总申报价值
	$dhl_total_price = 0;
	$datalistlen = count($datalist);
	for($i=0; $i<$datalistlen; $i++) {
		$datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);//将数组中的压缩内容解压并作为字段增加入数据中

		$dhl_total_price += $datalist[$i]['quantity']*$datalist[$i]['price'];

		if ($i>0 && $datalist[$i]['order_id'] == $datalist[$i-1]['order_id']) {
			$dhl_list[$dhl_list_num]['proforma'] = '|'.$dhl_item_num;								//产品条目数量*
			$dhl_list[$dhl_list_num]['shipment_declared_value'] = '|'.$dhl_total_price;				//总申报价值*
		}
		else {
			$res = $this->S->dao('shipping_code')->get_code_by_country($datalist[$i]['e_country']);
			$code = $res['code2'];
			$country = $this->C->service('country')->country_array[$code];
			$dhl_list[$dhl_list_num] = array();
			$dhl_list[$dhl_list_num]['receiver_company_name'] = '|';								//收件人公司名
			$dhl_list[$dhl_list_num]['receiver_attention'] = '|'.$datalist[$i]['e_receperson'];		//收件人*
			$dhl_list[$dhl_list_num]['receiver_address1'] = '|'.$datalist[$i]['e_address1'];		//收件人地址1*
			$dhl_list[$dhl_list_num]['receiver_address2'] = '|'.$datalist[$i]['e_address2'];		//收件人地址2*
			$dhl_list[$dhl_list_num]['receiver_address3'] = '|';									//收件人地址3*
			$dhl_list[$dhl_list_num]['receiver_city'] = '|'.$datalist[$i]['e_city'];				//城市*
			$dhl_list[$dhl_list_num]['receiver_postcode'] = '|'.$datalist[$i]['e_post_code'];		//邮编*
			$dhl_list[$dhl_list_num]['receiver_country'] = '|'.$country;							//国家名*
			$dhl_list[$dhl_list_num]['receiver_country_code'] = '|'.$code;							//通过输入的国家名称获取国家编码*
			$dhl_list[$dhl_list_num]['receiver_phone'] = '|'.$datalist[$i]['e_tel'];				//电话*
			$dhl_list[$dhl_list_num]['receiver_reference'] = '|'.$datalist[$i]['order_id'];			//订单号及备注*
			$dhl_list[$dhl_list_num]['contents1'] = '|'.$datalist[$i]['product_name'];				//品名1*
			$dhl_list[$dhl_list_num]['contents2'] = '|';											//品名2*
			$dhl_list[$dhl_list_num]['contents3'] = '|';											//品名3*
			$dhl_list[$dhl_list_num]['shipment_pieces'] = '|1';										//总件数*
			$dhl_list[$dhl_list_num]['shipment_weight'] = '|0.5';									//总重量*
			$dhl_list[$dhl_list_num]['shipment_declared_value'] = '|'.$dhl_total_price;				//总申报价值*
			$dhl_list[$dhl_list_num]['local_product_code'] = '|P';									//产品代码*
			$dhl_list[$dhl_list_num]['Harmonized_commodity_code'] = '|';							//HS CODE 海关编码*
			$dhl_list[$dhl_list_num]['proforma'] = '|'.$dhl_item_num;								//产品条目数量*

			//不需要erp导入
			$dhl_list[$dhl_list_num]['dutiable_logo'] = '|Y';										//应纳关税标识
			$dhl_list[$dhl_list_num]['export_license'] = '|';										//出口许可证号
			$dhl_list[$dhl_list_num]['export_tariff'] = '|';										//出口税号
			$dhl_list[$dhl_list_num]['certificate_No'] = '|';										//证书号
			$dhl_list[$dhl_list_num]['import_license'] = '|';										//进口许可证
			$dhl_list[$dhl_list_num]['import_tariff'] = '|';										//进口税号
			$dhl_list[$dhl_list_num]['terms_of_trade'] = '|DDU';									//贸易条款
			$dhl_list[$dhl_list_num]['reason_for_export'] = '|';									//出口原因
			$dhl_list[$dhl_list_num]['recipients_of_taxpayer'] = '|';								//收件人纳税号
			$dhl_list[$dhl_list_num]['the_sender_tax_number'] = '|';								//发件人纳税号
			$dhl_list[$dhl_list_num]['recipient_of_national_tax_code'] = '|';						//收件人国家税码
		}

		//产品信息
		$dhl_list[$dhl_list_num]['quantity'.$dhl_item_num] = '|'.$datalist[$i]['quantity'];					//产品数量*						//产品件数
		$dhl_list[$dhl_list_num]['units'.$dhl_item_num] = '|件';												//件数单位*
		$dhl_list[$dhl_list_num]['shipping_weight'.$dhl_item_num] = '|'.$datalist[$i]['shipping_weight'];	//单件重量*
		$dhl_list[$dhl_list_num]['single_units'.$dhl_item_num] = '|KGS';									//单件单位*
		$dhl_list[$dhl_list_num]['doc_num'.$dhl_item_num] = '|1';											//小数点后位数*
		$dhl_list[$dhl_list_num]['actual_weight'.$dhl_item_num] = '|'.$datalist[$i]['shipping_weight'];		//实际体积重*
		$dhl_list[$dhl_list_num]['length'.$dhl_item_num] = '|';												//长
		$dhl_list[$dhl_list_num]['width'.$dhl_item_num] = '|';												//宽
		$dhl_list[$dhl_list_num]['height'.$dhl_item_num] = '|';												//高
		$dhl_list[$dhl_list_num]['coin_code'.$dhl_item_num] = '|USD';										//货币单位*
		$dhl_list[$dhl_list_num]['price_doc_num'.$dhl_item_num] = '|1';										//价格小数点后位值*
		$dhl_list[$dhl_list_num]['price'.$dhl_item_num] = '|'.$datalist[$i]['price'];						//单价*
		$dhl_list[$dhl_list_num]['country_of_origin'.$dhl_item_num] = '|';									//原产地*
		$dhl_list[$dhl_list_num]['product_name'.$dhl_item_num] = '|'.$datalist[$i]['product_name'];			//货物描述*

		//产品信息填充*
		for ($j=1; $j<15; $j++) {
			$dhl_list[$dhl_list_num]['product_other'.$j.$dhl_item_num] = '|';
		}

		$dhl_item_num++;
		if ($i+1<$datalistlen && $datalist[$i]['order_id'] != $datalist[$i+1]['order_id']) {
			$dhl_list_num++;
			$dhl_item_num = 1;
			$dhl_total_price = 0;
		}
		//echo ($i+1).'-'.$datalistlen.'-'.$datalist[$i]['order_id'].'-'.$datalist[$i+1]['order_id'].'<br>';
	}

	$max_length = 0;
	$max_item = 0;
	for ($i=0; $i<$dhl_list_num; $i++){
		if ($max_length < count($dhl_list[$i])) {
			$max_item = $i;
			$max_length = count($dhl_list[$i]);
		}
	}

	$item_key = array_keys($dhl_list[$max_item]);
	$head_array = array();
	foreach ($item_key as $val) {
		$head_array[$val] = $val;
	}

	$filename = 'DHL_order_'.date('Y-m-d',time());
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$dhl_list);
}

/*
 * create by wall
 * on 2012-08-10
 * 单独修改shipping
 * */
elseif ($detail == 'update_shipping') {
	$process = $this->S->dao('process');
	$res = $process->get_extends_by_id($id);
	$extends_arr = $this->C->service('warehouse')->decodejson($res);
	unset($extends_arr['extends']);
	$extends_arr['e_shipping'] = $shipping;
	$update_extends = $extends = get_magic_quotes_gpc()?addslashes(json_encode($extends_arr)):json_encode($extends_arr);
	$process->D->update_by_field(array('id'=>$id), array('extends'=>$update_extends));
}

/**
 * create on 2012-12-11 by hanson
 * @title 添加出货页面，选择了销售账号自动选择关联的收款账号
 */
elseif($detail == 'autonext'){
	echo $this->S->dao('sold_relation_conf')->D->get_one(array('account_id'=>$account_id),'payrec_id');
}

/**
 * @title 导出数据到最终表
 * @author Jerry
 * @create on 2013-03-19
 */
elseif ($detail == 'printfinalorder') {
    if($sqlstr) $sqlstr = str_replace('cuser','p.cuser',$sqlstr);
    $sqlstr.=' and (protype="售出" or protype="重发") and isover="N"  ';
    $datalist = $this->S->dao('process')->outputfinalorder($sqlstr);
    for ($i = 0 ; $i<count($datalist); $i++){
        
        $datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);
        $datalist[$i]['cdate']  = date('Y-m-d',strtotime($datalist[$i]['cdate']));
        $datalist[$i]['rdate']  = date('Y-m-d',strtotime($datalist[$i]['rdate']));
        $datalist[$i]['desc']   = $datalist[$i]['product_name'];
        $datalist[$i]['erpsku'] = $datalist[$i]['sku'];
        $temptimec = (strtotime($datalist[$i]['cdate'])-strtotime('2012-01-01'))/3600/24;
        $temptimer = (strtotime($datalist[$i]['rdate'])-strtotime('2012-01-01'))/3600/24;
        $datalist[$i]['cdate'] = $temptimec + 40909;
        $datalist[$i]['rdate'] = $temptimer + 40909;
        $datalist[$i]['price'] = number_format($datalist[$i]['price'],2);
    }

    $filename  = 'finalorder_'.date('Y-m-d');
    $head_array = array(
        'cdate'         =>'日期',
        'deal_id'       =>'平台订单号',
        'fid'           =>'第三方单号',
        'sku'           =>'平台SKU',
        'erpsku'        =>'ERP SKU',
        'jin_sku'       =>'金碟SKU',
        'product_name'  =>'产品名称',
        'desc'          =>'中文描述',
        'quantity'      =>'数量',
        'coincode'      =>'币别',
        'price'         =>'收入',
        'e_shipping_price'=>'运费收入',
        'amazonprice'   =>'amazon代收运费',
        'amazoncost'    =>'amazon fee',
        'othercost'     =>'其它平台费用',
        'paypalcost'    =>'paypal费',
        'ebaycost'      =>'ebay fee',
        'e_shipping'    =>'发货方式',
        'warename'      =>'发货仓库',
        'rdate'         =>'发货时间',
        'e_country'     =>'国家',
        'account_name'  =>'销售账号',
        'payrec_account'=>'收款账号',
        'cuser'         =>'制单人',
        'comment'       =>'备注',
    );
    $this->C->service('upload_excel')->download_xls($filename, $head_array, $datalist);

}
/*模板定义*/
if($detail =='list' || $detail == 'neworder' ||$detail == 'editshipment' || $detail == 'cancel_order' || $detail == 'import_exl' || $detail =='return' || $detail =='refundmode' || $detail =='remoneymode' || $detail == 'replace'){

 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

}
?>
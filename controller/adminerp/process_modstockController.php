<?php
/*
 * Created on 2011-11-24
 * by hanson
 * @title 采购操作
 */

/*采购列表页*/
 if($detail == 'list'){

	$stypech = ($statu=='N'||!isset($statu))?'备货单号':'采购单号';/*搜索显示切换*/

	/*搜索选项*/
	$stypemu = array(
		'statu-h-e'			=>'状态：',
		'order-h-r'			=>'分页',//h使分页会赋值,r排序用不会生成SQL语句
		'sku-s-l'			=>'&nbsp; &nbsp; SKU：',
		'product_name-s-l'	=>'产品名称：',
		'order_id-s-e'		=>"$stypech".'：',
		'ispay-a-e'			=>'付款状态：',
		'ruser-s-l'			=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 接收人：',
		'mdate-t-t'			=>'订单日期：'
	);

	if (isset($statu) && $statu != 'N') {
		$stypemu['supplier-s-l'] = '&nbsp; &nbsp; 供应商：';
	}

	$ispayarr = array(''=>'=全部=','0'=>'未付款','1'=>'已付款');

	/*标签导航选项*/
	$tab_menu_stypemu = array(
		'statu-N'=>'预采购',
		'statu-0'=>'未审核',
		'statu-1'=>'已审核',
		'statu-3'=>'已下单',
	);


	/*初始打开默认显示-已接收，但未下采购订单的-有效的-备货单。*/
	if(empty($sqlstr) && !isset($statu)){$sqlstr = ' and p.statu="3" and p.isover="N" and p.property="备货单" ';$statu='N';}

	/*选择预采购取已接收的有效备货单，选择其他标签取有效采购单*/
	if($sqlstr){
		$sqlstr = ($statu=='N')?str_replace('and statu="N"','and p.statu="3" and p.isover="N" and p.property="备货单"',$sqlstr):$sqlstr.' and p.isover="N" and p.property="采购单" ';
	}

	/*已下单状态,显示所有采购单，包括关闭的*/
	if($sqlstr && $statu == "3") {
			$sqlstr = str_replace('and p.isover="N"','',$sqlstr);
			$sqlstr = str_replace('and statu="3"','and (p.statu="3" or p.statu="4")',$sqlstr);
	}elseif($statu == '1' or $statu =='0'){
			$sqlstr = str_replace('statu','p.statu',$sqlstr);
	}

	/*默认显示未付款的采购单*/
	if ($statu != 'N' && !isset($ispay)) {$sqlstr.=' and p.ispay="0" ';$ispay='0';}

	/*区分当前是显示可备货单还是采购单的处理*/
	if($statu == 'N'){
		$reperson 		= 'ruser';
		$date	  		= 'rdate';
		$show_date		= '接收日期';
		$show_order_id 	= '备货单号';
		$show_countnum 	= '已订数量';
		$show_quantity  = '审批数量';
		$shwhouse_name  = '备货仓库';
	}else{
		$reperson 		= 'cuser';
		$date	  		= 'cdate';
		$show_date		= '下单日期';
		$show_order_id  = '采购单号';
		$show_countnum 	= '入库数量';
		$show_quantity  = '采购数量';
		$shwhouse_name  = '入库仓库';
	}

	/*区别接收人查备货单与采购单时不同字段。*/
	if($statu != 'N') {$sqlstr = str_replace(' and ruser',' and p.cuser',$sqlstr);}

 	/*采购单时才有供应商。*/
	if($statu != 'N') {$sqlstr = str_replace('supplier','s.name',$sqlstr);}

	/*分页参数,默认15,注意放在$statu处理之后,查表之前*/
	$showperhtml = $this->C->service('warehouse')->perpage_show_html(array('0'=>'15','1'=>'50','2'=>'200','3'=>'1000'),$selfval_set,$statu);

 	/**
	 * update on 2012-05-09
	 * by wall
	 * 工作提醒传过时间查找
	 */
	if (!empty($job_alert_time)) {
		$sqlstr .= ' and p.mdate like "%'.$job_alert_time.'%" ' ;
		$pageshow = array('job_alert_time' => $job_alert_time);
	}

	/*排序处理*/
	$sku_img 	= 'both_nonorder';
	if ($order) {
		switch ($order) {
				case "sku_asc":
					$sku_img 		= 'both_asc';
					break;
				case "sku_desc":
					$sku_img 		= 'both_desc';
					break;
		}
		$orders		= str_replace("_"," ", $order);
		$orders	   .= ',';
	}

	$process = $this->S->dao('process');
	$datalist = $process->showneedsto($sqlstr, $orders);

	for($i=0;$i<count($datalist);$i++){

		/*用于选中的chceckbox*/
		$datalist[$i]['orderid']  = $datalist[$i]['orderidd'] = $datalist[$i]['order_id'];
                
                /*将额外信息存进输出数组$datalist*/
 		$extends = json_decode($datalist[$i]['extends'],true);

		/*预采购状态下，单条点击进入采购页面*/
		if($statu == 'N'){$datalist[$i]['orderidd'] = '<a title="点击下单" href=index.php?action=process_modstock&detail=getcont_to_stock&strid='.$datalist[$i]['id'].'>'.$datalist[$i]['order_id'].'</a>';}

		/*需要另外定义orderidd(默认等order_id,重复才置空,多条备货一次采购只显示一个采购单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		elseif($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']) {

			$datalist[$i]['orderidd'] 	= '';
			$datalist[$i]['bothre']		= '';
			$datalist[$i]['sysba_re']	= '';
		}

		/*其它状态点击进入打印预览*/
		else{
			$datalist[$i]['orderidd'] 	= '<a title="打印预览" target="_blank" href=index.php?action=process_modstock&detail=print_stockorder&prid='.$datalist[$i]['order_id'].'>'.$datalist[$i]['order_id'].'</a>';

			/*未审核状态的编辑与删除*/
			if($statu == '0') {
				$datalist[$i]['bothre'] = '<a href="index.php?action=process_modstock&detail=editorder&order_id='.$datalist[$i]['order_id'].'"><img src="./staticment/images/editbody.gif" border=0></a> ';
				$datalist[$i]['bothre'].= '<a href=javascript:void(0);delitem("index.php?action=process_modstock&detail=backorder&order_id='.$datalist[$i]['order_id'].'","确定删除？成功后关联的备货单将重新显示在预采购。")><img src="./staticment/images/deletebody.gif" border=0></a>';
			}

			/*反审*/
			if($statu == '1' || $statu == '3'){
                                if($extends['img_url']){
                                    $datalist[$i]['sysba_re']= '<a title="回退" href=javascript:void(0);alert("在回退之前，请先删除水单（付款状态）！")><img src="./staticment/images/sysback.gif" border=0></a>';
                                }else{
                                    $statu_back_msg = $statu == '1' ? '确定反审核？成功后订单将回退至未审核' : '确定反下单?成功后订单将回退至已审核';
                                    $datalist[$i]['sysba_re']= '<a title="回退" href=javascript:void(0);delitem("index.php?action=process_modstock&detail=setprev&order_id='.$datalist[$i]['order_id'].'","'.$statu_back_msg.'")><img src="./staticment/images/sysback.gif" border=0></a>';
                                }  
			}
		}

		/*红单显示删除*/
		if($statu == '3' && $datalist[$i]['statu'] == '4'){
			$datalist[$i]['sysba_re'] = '<a href=javascript:void(0);delitem("index.php?action=process_modstock&detail=deletered&id='.$datalist[$i]['id'].'")  title=删除红单><font color=red>&times;</font></a>';
		}


		/*AJAX关闭该备货单*/
		$datalist[$i]['turnoff'] = '<a href="javascript:void(0)" title="点击关闭" onclick=turnoff("'.$datalist[$i]['id'].'")>关闭</a>';
                $datalist[$i]['buytime_notice'] = '<a href=javascript:void(0);self.parent.addMenutab(102121033333,"采购延迟原因","index.php?action=process_modstock&detail=buytime_comment&id='.$datalist[$i]['id'].'")  title=延迟采购原因>延迟原因</a>';
		 
 		$datalist[$i]['e_iprice']  = number_format($extends['e_iprice'],2);
 		$datalist[$i]['e_siprice'] = number_format($extends['e_siprice'],2);
 		$datalist[$i]['e_sprice']  = number_format($extends['e_sprice'],2);
 		$datalist[$i]['e_cost']    = number_format($extends['e_cost'],2);
 		$datalist[$i]['e_scost']   = number_format($extends['e_scost'],2);

 		if($datalist[$i]['e_scost'] == 0.00){
 			$datalist[$i]['allpay']= $datalist[$i]['e_siprice'];//老数据，付款金额=价税合计
 		}else{
	 		$datalist[$i]['allpay']= number_format($extends['e_siprice'] + $datalist[$i]['fee'], 2);//付款金额
 		}

 		$datalist[$i]['e_account'] = $extends['e_account'];

 		$datalist[$i]['comment']   = empty($datalist[$i]['comment'])?'--':$datalist[$i]['comment'];

		/*水单图片显示处理,多条纪录只一条显示'未付款'*/
		if($extends['img_url']){
			$datalist[$i]['imispay'] = '';
			for ($j=0;$j<count($extends['img_url']);$j++){
				$datalist[$i]['imispay'].= '<a href='.$extends['img_url'][$j].' title="点击查看大图" target="_blank"><img src='.$extends['img_url'][$j].' style="border:solid 1px #828482; width:50px;height:50px;"></a>&nbsp;';
			}
		}elseif($datalist[$i]['order_id'] == $datalist[$i-1]['order_id'] && $datalist[$i]['statu'] != '4'){
			$datalist[$i]['imispay'] = '';
		}elseif($datalist[$i]['ispay']=='0' && $datalist[$i]['statu'] != '4'){
			$datalist[$i]['imispay'] = '未付款';
		}


		/*已下单状态，备货单显示生成红色注销单*/
		if($statu == '3' && $datalist[$i]['statu'] == '3'){
			$datalist[$i]['fid'] = '<a href="index.php?action=process_modstock&detail=make_cancle&id='.$datalist[$i]['id'].'&order_id='.$datalist[$i]['order_id'].'" title="点击生成红色注销单">'.$datalist[$i]['fid'].'</a>';
		}

		if($statu != 'N') {
			$back_d_u 				= $process->D->get_one_by_field(array('id'=>$datalist[$i]['detail_id']),'cuser');
			$datalist[$i]['pcuser'] = $back_d_u['cuser'];
		}

		/*采购红单作红色高亮处理*/
		if($datalist[$i]['statu'] == '4'){
			$datalist[$i]['fid'] 			= '<a title="点击上传红单水单" href="index.php?action=process_modstock&detail=upspay&id='.$datalist[$i]['id'].'"><font color=red>'.$datalist[$i]['fid'].'</font></a>';
			$datalist[$i]['sku'] 			= '<font color=red>'.$datalist[$i]['sku'].'</font>';
			$datalist[$i]['pcuser']			= '<font color=red>'.$datalist[$i]['pcuser'].'</font>';
			$datalist[$i]['product_name']	= '<font color=red>'.$datalist[$i]['product_name'].'</font>';
			$datalist[$i]['suppliername']	= '<font color=red>'.$datalist[$i]['suppliername'].'</font>';
			$datalist[$i]['whouse_name']	= '<font color=red>'.$datalist[$i]['whouse_name'].'</font>';
			$datalist[$i]['quantity'] 		= '<font color=red>'.$datalist[$i]['quantity'].'</font>';
			$datalist[$i]['countnum'] 		= '<font color=red>'.$datalist[$i]['countnum'].'</font>';
			$datalist[$i]['price']	 		= '<font color=red>'.$datalist[$i]['price'].'</font>';
			$datalist[$i]['e_sprice'] 		= '<font color=red>'.$datalist[$i]['e_sprice'].'</font>';
			$datalist[$i]['fee'] 			= '<font color=red>'.$datalist[$i]['fee'].'</font>';
			$datalist[$i]['fee2'] 			= '<font color=red>'.$datalist[$i]['fee2'].'</font>';
			$datalist[$i]['fee3'] 			= '<font color=red>'.$datalist[$i]['fee3'].'</font>';
			$datalist[$i]['allpay'] 		= '<font color=red>'.$datalist[$i]['allpay'].'</font>';
			$datalist[$i]['e_cost'] 		= '<font color=red>'.$datalist[$i]['e_cost'].'</font>';
			$datalist[$i]['e_siprice'] 		= '<font color=red>'.$datalist[$i]['e_siprice'].'</font>';
			$datalist[$i]['price2'] 		= '<font color=red>'.$datalist[$i]['price2'].'</font>';
			$datalist[$i]['e_scost'] 		= '<font color=red>'.$datalist[$i]['e_scost'].'</font>';
			$datalist[$i]['e_iprice'] 		= '<font color=red>'.$datalist[$i]['e_iprice'].'</font>';
			$datalist[$i]['coin_code']		= '<font color=red>'.$datalist[$i]['coin_code'].'</font>';
			$datalist[$i]['cuser'] 			= '<font color=red>'.$datalist[$i]['cuser'].'</font>';
			$datalist[$i]['cdate'] 			= '<font color=red>'.$datalist[$i]['cdate'].'</font>';
			$datalist[$i]['isover'] 		= '<font color=red>'.$datalist[$i]['isover'].'</font>';
			$datalist[$i]['comment'] 		= '<font color=red>'.$datalist[$i]['comment'].'</font>';
		}


	}

	/*数据列表显示处理*/
 	$displayarr = array();

	if($statu != 'N') {$displayarr['orderid']= array('showname'=>'checkbox','title'=>'全选','width'=>'45');}//采购单状态复选框内容是订单号。
	else{$displayarr['id']= array('showname'=>'checkbox','title'=>'全选','width'=>'40');}

	$displayarr['orderidd']			= array('showname'=>$show_order_id,'width'=>'80');

	/*采购单状态显示多一列源单号。*/
	if($statu != 'N'){
		$displayarr['fid']			= array('showname'=>'备货单号','width'=>'80');
		$displayarr['pcuser']		= array('showname'=>'备货人','width'=>'80');
	}
	if($statu == 'N'){
		$displayarr['sku'] 			= array('showname'=>'产品SKU','width'=>'80','orderlink_desc'=>'&order=sku_desc','orderlink_asc'=>'&order=sku_asc','order_type'=>$sku_img);
	}else{
		$displayarr['sku'] 			= array('showname'=>'产品SKU','width'=>'80');
	}
 	$displayarr['product_name'] 	= array('showname'=>'产品名称','width'=>'150');

 	$displayarr['suppliername'] 	= array('showname'=>'供应商','width'=>'100');
	if($statu=='N')$displayarr['e_account'] = array('showname'=>'定价','width'=>'50');

 	$displayarr['whouse_name']  	= array('showname'=>$shwhouse_name,'width'=>'100');
 	$displayarr['quantity'] 		= array('showname'=>$show_quantity,'width'=>'80');
 	$displayarr['countnum'] 		= array('showname'=>$show_countnum,'width'=>'80');
 	if($statu != 'N'){
 		$displayarr['price']	 	= array('showname'=>'不含税单价','width'=>'105');
 		$displayarr['e_sprice']	 	= array('showname'=>'不含税合计','width'=>'105');
 		$displayarr['e_iprice'] 	= array('showname'=>'含税单价','width'=>'80');
 		$displayarr['e_siprice'] 	= array('showname'=>'价税合计','width'=>'80');
		$displayarr['fee']			= array('showname'=>'即付运费','width'=>'80');
		$displayarr['allpay']		= array('showname'=>'付款金额','width'=>'80');
		$displayarr['e_cost']		= array('showname'=>'采购成本','width'=>'80');
		$displayarr['fee3']			= array('showname'=>'其它费用','width'=>'80');
		$displayarr['fee2']			= array('showname'=>'到付运费','width'=>'80');
 		$displayarr['price2']	 	= array('showname'=>'单位总成本','width'=>'100');
 		$displayarr['e_scost']	 	= array('showname'=>'总成本','width'=>'80');
 		$displayarr['coin_code'] 	= array('showname'=>'币别','width'=>'50');
 	}
 	$displayarr[$reperson]			= array('showname'=>'备货接收人','width'=>'100');
 	if($statu =='1') {$displayarr['muser']= array('showname'=>'审核人','width'=>'100');}
 	if($statu == 'N'){$displayarr['turnoff']= array('showname'=>'订单操作','width'=>'80');$displayarr['buytime_notice'] = array('showname'=>'延迟采购','width'=>'60');}
 	if($statu=='1' ||$statu=='3'){$displayarr['imispay']= array('showname'=>'付款状态','width'=>'80');}
 	$displayarr[$date] 				= array('showname'=>$show_date,'width'=>'100');
 	$displayarr['comment'] 			= array('showname'=>'备注','width'=>'200','clickedit'=>'id','detail'=>'editcoment');

 	/*采购订单未审核状态的*/
 	if($statu == '0'){
 		$displayarr['bothre'] 		= array('showname'=>'操作','width'=>'50');
 	}elseif($statu=='1'){
 		$displayarr['sysba_re']		= array('showname'=>'反审','width'=>'50','title'=>'反审核');
 	}elseif($statu == '3'){
 		$displayarr['isover']		= array('showname'=>'关闭标志','title'=>'N正常,Y关闭','width'=>'100');
 		$displayarr['sysba_re']		= array('showname'=>'操作','width'=>'50');
 	}



	/*数据流操作按钮*/
	$this->C->service('global')->disconnect_modbutton(array('N'=>&$mod_disabled_n,'0'=>&$mod_disabled_0,'1'=>&$mod_disabled_1,'1-3'=>&$mod_disabled_2,'3'=>&$mod_disabled_3),$statu);

	$bannerstr = '<button onclick=stockdo() '.$mod_disabled_n.' >采购下单</button>';
	$bannerstr.= '<button onclick=modrecorde("audit") '.$mod_disabled_0.' >审核选中</button>';
	$bannerstr.= '<button onclick=modrecorde("ensure") '.$mod_disabled_1.' >采购确认</button>';
	$bannerstr.= ' &nbsp;';
	$bannerstr.= '<button onclick=upspay() '.$mod_disabled_2.' >上传水单</button>';
	$bannerstr.= '<button onclick=addepay() '.$mod_disabled_3.'>增加费用</button>';
	if ($statu != 'N' && $sqlstr != '') {
		$bannerstrarr[] = array('url'=>'index.php?action=process_modstock&detail=output&statu='.$statu,'value'=>'导出数据');
	}
    
    /*金蝶导出数据*/
    if ($statu == '0' && $sqlstr != ''){
        $bannerstrarr[] = array('url'=>'index.php?action=process_modstock&detail=jindieoutput&statu='.$statu,'value'=>'金蝶导出');
    }
	$bannerstr.= $showperhtml;

	$jslink = "<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />\n";
	$jslink .= "<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>\n";
	$jslink .= "<script src='./staticment/js/process_modstock.js?20120821'></script>\n";
 	$this->V->mark(array('title'=>'采购列表'));
 	$temp = 'pub_list';

 }

/*反审核与反下单*/
elseif($detail == 'setprev'){

	$process 	= $this->S->dao('process');
	$backdata	= $process->D->get_all(array('order_id'=>$order_id),'','','id,statu,muser,ruser,isover');

	if($backdata['0']['statu'] == '1')//反审核
	{
		/*取配置权限判断是否有权限*/
		if(!$this->C->service('admin_access')->checkResRight('none','follow',$backdata['0']['muser'])) $this->C->ajaxmsg(0);

		$sid = $process->D->query('update process set mdate=cdate,muser="",statu="0" where order_id="'.$order_id.'"');
	}

	elseif($backdata['0']['statu'] == '3')//反下单
	{
		/*权限判断*/
		if(!$this->C->service('admin_access')->checkResRight('none','follow',$backdata['0']['ruser'])) $this->C->ajaxmsg(0);

		/*判断红单，有红单不允许回退*/
		foreach($backdata as $val){
			if($val['statu'] == '4') {$this->C->ajaxmsg(0,'操作失败,存在红单无法反下单');}
			if($val['isover'] == 'Y') {$this->C->ajaxmsg(0,'操作失败,该采购单已完成入库，无法回退；可通知仓管反入库后再操作。');}
			if($process->D->get_one(array('detail_id'=>$val['id'],'protype'=>'采购'),'id')) {$this->C->ajaxmsg(0,'操作失败,该采购单已入过库，无法回退；可通知仓管反入库后再操作。');}

		}

		$sid = $process->D->query('update process set rdate="",ruser="",statu="1" where order_id="'.$order_id.'"');

	}


	if($sid){$this->C->ajaxmsg(1,'操作成功');}else{$this->C->ajaxmsg(0,'操作失败');}

}

/*删除红单*/
elseif($detail == 'deletered'){

	$process = $this->S->dao('process');
	if(!$this->C->service('admin_access')->checkResRight('r_w_delred','mod',$process->D->get_one(array('id'=>$id),'cuser'))){$this->C->ajaxmsg(0);}/*权限判断*/

	$sid = $process->D->delete_by_field(array('id'=>$id));
 	if($sid) {$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0,'删除失败');}

}

/*保存生成注销单*/
elseif($detail == 'mod_make_cancle'){


	$process 	= $this->S->dao('process');
	$copydata 	= $process->D->get_one(array('id'=>$id),'provider_id,receiver_id,coin_code,stage_rate,sku,fid,pid,product_name,mdate,order_id,ispay,isover,cuser');

	if(!$this->C->service('admin_access')->checkResRight('r_w_makered','mod',$copydata['cuser'])){$this->C->sendmsg();}/*权限判断*/
	$extends	= array('e_siprice'=>$e_siprice);

	$extends	= get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

	$sid		= $process->D->insert(array('provider_id'=>$copydata['provider_id'],'receiver_id'=>$copydata['receiver_id'],'coin_code'=>$copydata['coin_code'],'stage_rate'=>$copydata['stage_rate'],'sku'=>$copydata['sku'],'detail_id'=>$id,'fid'=>$copydata['fid'],'pid'=>$copydata['pid'],'product_name'=>$copydata['product_name'],'quantity'=>$quantity,'order_id'=>$copydata['order_id'],'ispay'=>$copydata['ispay'],'statu'=>'4','property'=>'采购单','isover'=>$copydata['isover'],'cuser'=>$_SESSION['eng_name'],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>$copydata['mdate'],'extends'=>$extends));
	if($sid) $this->C->success('保存成功','index.php?action=process_modstock&detail=list&statu=3&ispay=&order_id='.$order_id);
}

/*生成注销单*/
elseif($detail == 'make_cancle'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_makered')){$this->C->sendmsg();}/*权限判断*/

	if($id){
		$backdata = $this->S->dao('process')->D->get_one(array('id'=>$id),'quantity,countnum,coin_code,fee,extends');
		if($backdata['quantity'] <= $backdata['countnum']) $this->C->sendmsg('已完成入库，无法再做注销单');

		$backdata = $this->C->service('warehouse')->decodejson($backdata);
	}

	$bannerstr	= '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:695px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
	$bannerstr .= '<font size="-1" color=red>温馨提醒：注销单的数量与退款金额都是负数，系统会自动读取原单数据，也可手动修改！</font></div>';

	$conform 	= array('method'=>'post','action'=>'index.php?action=process_modstock&detail=mod_make_cancle');
	$colwidth 	= array('1'=>'100','2'=>'300','3'=>'300');
	$disinputarr= array();

	$disinputarr['id']			= array('showname'=>'id','datatype'=>'h','value'=>$id);
	$disinputarr['order_id'] 	= array('showname'=>'订单号','datatype'=>'se','datastr'=>$order_id);
	$disinputarr['quantity'] 	= array('showname'=>'数量','showtips'=>'<font color="#c6a8c6">* 默认取订单数量，可修改。</font>','value'=>'-'.$backdata['quantity'],'inextra'=>'class="check_isnum_ddn"');
	$disinputarr['e_siprice'] 	= array('showname'=>'退款金额','showtips'=>'<font color="#c6a8c6">* 默认取订单付款金额，可修改。</font>','value'=>'-'.$backdata['e_siprice']-$backdata['fee'],'inextra'=>'class="check_isnum_ddn check_isnum_dd2"');
	$disinputarr['coin_code']	= array('showname'=>'币别','value'=>$backdata['coin_code'],'inextra'=>'disabled','showtips'=>'<font color="#c6a8c6">&nbsp;退款币别与原单一致，无法修改。</font>');

	$this->V->mark(array('title'=>'注销单-采购列表(list)'));
	$temp = 'pub_edit';
}


/*采购订单删除,权限取配置，默认只有建单人才能删*/
elseif($detail == 'backorder'){

	$process = $this->S->dao('process');
	$backdata = $process->D->get_all(array('order_id'=>$order_id),'','','quantity,detail_id,statu,cuser,muser');

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_w_deldostock','mod',$backdata['0']['cuser'])) $this->C->ajaxmsg(0);

	/*取出上一级ID(备货单的ID)*/
	foreach ($backdata as $val){
		$detailarray[] = $val['detail_id'];
	}

	/*成功统计量*/
	$successnum = 0;

	/*开始一个事务*/
	$process->D->query('begin');

	/*回写累计执行量，源单的countnum=源单的countnum-当前订单的quantity,并非将countnum置0*/
	for($i=0;$i<count($detailarray);$i++){
		$cid = $process->D->query('update process set countnum=countnum-'.$backdata[$i]['quantity'].' ,isover="N" where id='.$detailarray[$i]);
		if($cid){$successnum++;}
	}

	$sid= $process->D->delete_by_field(array('order_id'=>$order_id));/*删除采购单*/

	/*回写与删除都成功则提交事务，否则回滚*/
	if($cid==count($detailarray) && $sid){$process->D->query('commit');$this->C->ajaxmsg(1);}else{$process->D->query('rollback');$this->C->ajaxmsg(0,'删除失败');}

}

/*编辑采购订单，权限取配置*/
elseif($detail == 'editorder'){
	$backdata = $this->S->dao('process')->showneedsto(' and order_id="'.$order_id.'"');
	if($backdata['0']['statu']!=0 || $backdata['0']['property']!='采购单') {$this->C->sendmsg('非法操作');}

	/*取配置权限判断是否有权限*/
	if(!$this->C->service('admin_access')->checkResRight('r_w_editdostock','mod',$backdata['0']['cuser'])) $this->C->sendmsg();

	/*取得仓库*/
	$wdata		= $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');

	$shipping	= 0;

	/*数据处理*/
	for($i=0;$i<count($backdata);$i++){

		/*将额外信息存进输出数组$backdata*/
 		$extends = json_decode($backdata[$i]['extends'],true);
 		$backdata[$i]['e_sprice']  = $extends['e_sprice'];
 		$backdata[$i]['e_siprice'] = $extends['e_siprice'];
 		$backdata[$i]['e_recdate'] = $extends['e_recdate'];

		/*生成仓库下拉,默认选中之前的仓库*/
		$backdata[$i]['sourcehtml'] = '<select name=receiver_id[]>';
		foreach ($wdata as $val){
			$selected = ($val['id']==$backdata[$i]['receiver_id'])?'selected':'';
			$backdata[$i]['sourcehtml'].= '<option value='.$val['id'].' '.$selected.'>'.$val['name'].'</option>';
		}
		$backdata[$i]['sourcehtml'].= '</select>';

		$shipping+= $backdata[$i]['fee'];

	}
	/*取得供应商名称*/
	$backprovider = $this->S->dao('esse')->D->select('name','id="'.$backdata['0']['provider_id'].'"');


	/*生成币别下拉*/
	$currencyhtml = $this->C->service('warehouse')->get_coincode_html('coin_code','',$backdata['0']['coin_code']);
	/*--End*/


	$this->V->mark(array('title'=>'修改订单-采购列表(list)','order_id'=>$order_id,'shipping'=>$shipping,'sourcehtml'=>$sourcehtml,'datalistt'=>$backdata,'supplier'=>$backprovider['name'],'currencyhtml'=>$currencyhtml,'moddetail'=>'editordermod'));
	$this->V->set_tpl('adminweb/process_editstock');
	display();
}

/*保存编辑采购订单,$detail_id-采购订单的上一级单ID*/
elseif($detail == 'editordermod'){

	/*根据供应商名称取得实体供应商ID作为明细表供应者ID*/
	if(!$provider_id = $this->S->dao('esse')->D->get_one(array('name'=>$suppliername),'id')) exit('系统未录入的供应商!');

	/*统计页面总数量*/
	$pagesums		= 0;
	for($i=0; $i<count($quantity); $i++){$pagesums+= $quantity[$i];}

	$pershare		= number_format($shipping/$pagesums, 2);//每个产品平摊的运费
	$extrafare		= $pershare*$pagesums - $shipping;//计算存在四舍五入计算零头

	$process 		= $this->S->dao('process');
	$olddetailarr	= array();
	$de_qu			= array();
	$olddetails		= $process->D->get_allstr(' and order_id="'.$order_id.'"','','','detail_id,quantity');//查找订单原来的detail_id。
	foreach($olddetails as $v){
		$olddetailarr[] 		= $v['detail_id'];
		$de_qu[$v['detail_id']] = $v['quantity'];
	}


	$error_count 	= 0;

	/*开始事务*/
	$process->D->query('begin');

	/*根据页面传过来的detail_id值与原单detail_id值比较，需要删除的detail行*/
	$needcancel		= array_diff($olddetailarr, $detail_id = $detail_id?$detail_id:array());

	if($needcancel){
		foreach($needcancel as $v){//注意别用for，键值非从0开始
			$did = $process->D->delete(array('order_id'=>$order_id,'detail_id'=>$v));//删除采购单记录。
			$uid = $process->D->update(array('id'=>$v),array('isover'=>'N','extra_sql'=>' countnum = countnum-'.$de_qu[$v]));//对应备货单累计执行量相减。
			if(!$did || !$uid) $error_count++;
		}
	}

	for($i=0;$i<count($detail_id);$i++){

		$extendsarr = array();

		$backextends= $process->D->get_one(array('id'=>$id[$i]),'extends,fee2,fee3');//总到付运费与其它费用

		/*最后一条记录，存在可能上传过水单的订单回退编辑*/
		if($i == 0){//注意订单排序不能随便调
			$extendsarr	= json_decode($backextends['extends'],1);
		}

		/*不含税单价*/
		$price 					= $e_sprice[$i]/$quantity[$i];

		$update_arr				= array('provider_id'=>$provider_id,'coin_code'=>$coin_code,'receiver_id'=>$receiver_id[$i],'quantity'=>$quantity[$i],'comment'=>$comment[$i]);
		$update_arr['price']	= $price;


		/*编辑以第一条算多差少补，因为按倒序排了*/
		$update_arr['fee']		= ($i == 0) ? ($pershare*$quantity[$i] - $extrafare) : $pershare*$quantity[$i];

		/*扩展内容处理*/
		$extendsarr['e_sprice'] = $e_sprice[$i];//不含税合计
		$extendsarr['e_siprice']= $e_siprice[$i];//价税合计
		$extendsarr['e_iprice']	= $e_siprice[$i]/$quantity[$i];//含税单价=价税合计/数量
		$extendsarr['e_recdate']= $e_recdate[$i];
		$extendsarr['e_cost']	= $price + $pershare;//单个采购成本=不含税单价+单个平摊费用
		$extendsarr['e_scost']	= $e_sprice[$i] + $update_arr['fee'] + $backextends['fee2'] + $backextends['fee3'];//总成本=不含税合计+该条记录平摊的费用
		$extends 				= get_magic_quotes_gpc()?addslashes(json_encode($extendsarr)):json_encode($extendsarr);//如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
		$update_arr['extends']	= $extends;

		/*单位总成本=总成本/数量,下单时到付运费为空，即等于采购成本，后期若增加费用需要更新*/
		$update_arr['price2']	= $extendsarr['e_scost']/$quantity[$i];

		/*更新采购单*/
		$sid 		= $process->D->update(array('id'=>$id[$i]),$update_arr);

		/*查该备货单累计完成量*/
		$backcntnum	= $process->D->get_one(array('detail_id'=>$detail_id[$i]),'sum(quantity)');

		/*查该备货单的需求量*/
		$needcntnum	= $process->D->get_one(array('id'=>$detail_id[$i]),'quantity');

		/*修改后的累计完成量大于或等于备货需求量，更新累计执行量并关闭订单*/
		if($backcntnum >= $needcntnum){
			$cid = $process->D->update(array('id'=>$detail_id[$i]),array('countnum'=>$backcntnum,'isover'=>'Y'));
		}elseif($backcntnum < $needcntnum){
			$cid = $process->D->update_by_field(array('id'=>$detail_id[$i]),array('countnum'=>$backcntnum,'isover'=>'N'));
		}

		/*修改成功并且回写成功，成功统计量+1*/
		if(!$sid || !$cid){$error_count++;}
	}

	$overjumpurl = 'index.php?action=process_modstock&detail=list&statu=0&ispay=';
	/*全部成功才提交事务,否则回滚*/
	if(empty($error_count)){
		$process->D->query('commit');
		$this->C->success(empty($detail_id)?'该采购单已删除 !':'修改成功 !',$overjumpurl);
	}else{
		$process->D->query('rollback');
		$this->C->success('修改失败 !',$overjumpurl);
	}

}


/*增加其他费用填写页面*/
elseif($detail == 'modaddpay'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_addepay')){$this->C->sendmsg();}//增加其它费用权限判断

	/*检测选中的纪录是否包含不符合状态的，防止逆向操作，只有已下单采购单的才能增加其他费用*/
	$datalist = $this->S->dao('process')->D->get_one_by_field(array('order_id'=>$order_id),'statu,property,coin_code,isover');
	if($datalist['statu']!='3' || $datalist['property']!='采购单'){
		$this->C->sendmsg('不合理操作');
	}
	elseif($datalist['isover'] == 'Y'){
		$this->C->sendmsg('该采购单已完成入库，无法增加费用');
	}

	/*表单配置*/
	$jump 		= 'index.php?action=process_modstock&detail=saveaddpay';
	$conform 	= array('method'=>'post','action'=>$jump,'width'=>'750');
	$colwidth 	= array('1'=>'100','2'=>'200','3'=>'300');

	$disinputarr 					= array();
	$disinputarr['order_id'] 		= array('showname'=>'订单号','datatype'=>'h','value'=>$order_id);
	$disinputarr['order_farename'] 	= array('showname'=>'费用名称','showtips'=>'<font color=c6a8c6>&nbsp;如:\'运费\'</font>');
	$disinputarr['order_fareprice'] = array('showname'=>'费用金额','showtips'=>'<font color=c6a8c6>&nbsp;注：原订单币别'.$datalist['coin_code'].'，所以填写的费用必须也是 '.$datalist['coin_code'].'</font>');

	$bannerstr = '<p style="font-size:12px">&nbsp; 订单号：'.$order_id.'</p>';

	$this->V->mark(array('title'=>'其它费用-采购列表(list)'));
	$temp = 'pub_edit';

}

/*增加其他费用保存操作*/
elseif($detail == 'saveaddpay'){

	/*增加其它费用权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_w_addepay')){$this->C->sendmsg();}

	$process = $this->S->dao('process');
	$process->D->query('begin');

	/*平摊产品成本*/
	$backdata 	 = $this->C->service('finance')->stockorder_addfare($process,$order_id,$order_farename,$order_fareprice,'fee3');

	$overjumpurl = 'index.php?action=process_modstock&detail=list&statu=3&ispay=';


	if(empty($backdata['error'])){
		$process->D->query('commit');
		$this->C->success('保存成功!',$overjumpurl);
	}else{
		$process->D->query('rollback');
		$this->C->success('保存失败!',$overjumpurl);
	}
}


/*上传水单页面*/
elseif ($detail == 'upspay'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_chanpay')){$this->C->sendmsg();}//更改付款状态权限判断

	/*采购水单*/
	if($order_id){

		/*检测选中的纪录是否包含不符合状态的，防止逆向操作，只有已审核与已下单的才能上传水单*/
		$datalist = $this->S->dao('process')->D->get_one_sql("select property,statu,ispay,extends from process where order_id = '".$order_id."' and statu!='4' order by id desc");
		if(!is_array($datalist) || $datalist['statu']=='0') {$this->C->sendmsg('不合理操作');}
		$jump_statu = $datalist['statu'];

	}elseif($id){
		$datalist = $this->S->dao('process')->D->get_one_by_field(array('id'=>$id),'extends');
		$jump_statu = 3;
	}

	/*取出原有图片*/
	$datalist = $this->C->service('warehouse')->decodejson($datalist);

	$backstrArray = '';
	foreach ($datalist['img_url'] as $key=>$images_aryy) {
				$backstr = '';
				$backstr.= "<div style='width:116px;height:125px;float:left;padding:3px;padding-top:15px;'>";
				$backstr.= "<div style='float:left'><img src=".$images_aryy." alt='' style='border:solid 1px #828482; width:115px;height:115px;'></div>";
				$backstr.= "<div style='float:left;background:#caddfe;width:23px'>";
				$backstr.= "<input type='hidden' name='img_url[]' value=".$images_aryy.">&nbsp;<span title='delete' style='cursor:pointer;color:#828482;' onclick=$(this).parent().parent().remove();>&times;</span></div></div>";

				$backstrArray.= $backstr;
	}

	if($id) 	{$backstrArray.= '<input type=hidden name=id value='.$id.'>';}
	$backstrArray.= '<input type=hidden name=extra_url_statu value='.$jump_statu.'>';

	$showstr 	= '<div>保存后页面跳转：<select name=jumpto><option value=1>返回本订单查看</option><option value=2>已审核的未付款订单</option></select></div>';

	$this->V->mark(array('title'=>'上传水单-采购列表(list)','order_id'=>$order_id,'backstr'=>$backstrArray,'showstr'=>$showstr));
	$this->V->set_tpl('adminweb/upstock_image');
	display();
}

/*上传图片操作，只保存在采购单中的上一级单号最大的记录中*/
elseif ($detail == 'modupspay'){

	if(!$this->C->service('admin_access')->checkResRight('r_w_chanpay')){$this->C->sendmsg();}//更改付款状态权限判断

	/*跳转URL*/
	if($jumpto == 1)
	{
		$extra_url = '&order_id='.$order_id.'&statu='.$extra_url_statu.'&ispay=';
	}else
	{
		$extra_url = '&statu=1&ispay=0';
	}

	/*对图片信息压缩*/
	$process  = $this->S->dao('process');


	/*采购水单*/
	if(empty($id)){
		$backdata = $process->get_maxfid($order_id);
		$extends 			= json_decode($backdata['0']['extends'],true);
		$extends['img_url'] = $img_url;
		$extends			= json_encode($extends);

		$process->D->query('BEGIN');//开始一个事务

		$sid = $process->D->update_by_field(array('id'=>$backdata['0']['id']),array('extends'=>$extends));//扩展内容增加图片。
		$cid = $process->D->update_by_field(array('order_id'=>$order_id),array('ispay'=>'1'));//更新状态为已付款，用于搜索。

		/*如果插入与回写都成功，提交事务*/
		if($sid && $cid) {$process->D->query('commit');$this->C->success('保存成功','index.php?action=process_modstock&detail=list'.$extra_url);}
		else{$process->D->query('rollback');$this->C->success('保存失败','index.php?action=process_modstock&detail=list'.$extra_url);}
	}else{

		$backdata 			= $process->D->get_one_by_field(array('id'=>$id),'extends');
		$extends 			= json_decode($backdata['extends'],true);
		$extends['img_url'] = $img_url;
		$extends			= json_encode($extends);
		$sid 				= $process->D->update_by_field(array('id'=>$id),array('extends'=>$extends));
		$jump_eurl			= 'index.php?action=process_modstock&detail=list'.$extra_url;
		if($sid)
		{
			$this->C->success('保存成功',$jump_eurl);
		}else
		{
			$this->C->success('保存失败',$jump_eurl);
		}
	}

}


/*打印采购订单*/
elseif($detail == 'print_stockorder'){

	if(empty($prid)) exit('缺乏订单号！');

	/*正常单*/
	$process	= $this->S->dao('process');
	$datalist	= $process->print_getmsg(' and order_id="'.$prid.'" and statu!="4" ');
	$ordernums	= $process->D->get_one(' and order_id="'.$prid.'" and statu!="4"','sum(quantity) as quantity,sum(fee) as fee');

	$e_extends	= json_decode($datalist['0']['e_extends'],true);//供应商扩展内容

	/*即付运费*/
	$shipfare	= $ordernums['fee'];

	/*取出非循环内容*/
	$sput = array();
	$sput['order_id'] 		= $prid;//订单号
	$sput['provider'] 		= $datalist['0']['name'];//供应商名称
	$sput['cuser'] 			= $datalist['0']['cname'];//制单人
	$sput['muser'] 			= $datalist['0']['mname'];//审核人

	$sput['e_address'] 		= $e_extends['e_address'];//供应商地址
	$sput['e_person'] 		= $e_extends['e_person'];//供应商联系人
	$sput['e_tel'] 			= $e_extends['e_tel'];//供应商电话
	$sput['e_bankaddr'] 	= $e_extends['e_bankaddr'];//供应商开户银行
	$sput['e_bankid'] 		= $e_extends['e_bankid'];//供应商银行帐号
	$sput['e_bankuser']		= $e_extends['e_bankuser'];//户名

	/*删除已取出的内容，减小标记变量内容*/
	$count_sprice = 0;$count_nums = 0;
	foreach ($datalist as &$val){
		unset($val['e_extends'],$val['name'],$val['cname'],$val['mname']);

		/*将额外信息存进输出数组$datalist*/
		$val['product_name'] = strlen($val['product_name'])>=80?'<span style="font-size:8px;">'.$val['product_name'].'</span>':$val['product_name'];
 		$extends = json_decode($val['p_extends'],true);
 		$val['e_iprice']  = number_format($extends['e_iprice']+$ordernums['fee']/$ordernums['quantity'],6);//含税单价=含税单价+即付运费，为方便录金碟

 		$val['e_cost']	  = number_format($extends['e_cost'],2);
 		$val['e_siprice'] = ($val['e_cost'] == 0.00)?$extends['e_siprice']:number_format($extends['e_siprice']+$val['fee'],2);//照顾老数据

		$val['e_recdate'] = $extends['e_recdate'];

		$count_nums+=$val['quantity'];
		$count_sprice+=$extends['e_siprice'];
	}

	/*打印分页*/
	$pageid = ceil(count($datalist)/10);
	$allpay = $ordernums['fee'] + $count_sprice;//付款金额

	$big_count_sprice = $this->getLibrary('changenum')->num2rmb($allpay);
	$this->V->mark(array('allpay'=>number_format($allpay, 2),'shipfare'=>$shipfare,'pageid'=>$pageid,'title'=>'','sput'=>$sput,'datalist'=>$datalist,'count_nums'=>$count_nums,'count_sprice'=>number_format($count_sprice,2),'big_count_sprice'=>$big_count_sprice,'cdate'=>date('Y-m-d',strtotime($datalist[0]['cdate']))));


	/*红单*/
	$datalist_red = $this->S->dao('process')->print_getmsg(' and order_id="'.$prid.'" and statu="4" ');

	if($datalist_red){

		$count_sprice_red = 0;$count_nums_red = 0;
		foreach ($datalist_red as &$val){

			/*将额外信息存进输出数组$datalist_red*/
			$val['product_name'] = strlen($val['product_name'])>=80?'<span style="font-size:8px;">'.$val['product_name'].'</span>':$val['product_name'];
	 		$extends = json_decode($val['p_extends'],true);
	 		$val['e_iprice']  = number_format($extends['e_iprice'],2);//两们小数
	 		$val['e_siprice'] = number_format($extends['e_siprice'],2);
			$val['e_recdate'] = $extends['e_recdate'];

			$count_nums_red+=$val['quantity'];
			$count_sprice_red+=$extends['e_siprice'];
		}

		/*打印分页*/
		$pageid_red = ceil(count($datalist_red)/10);
		$big_count_sprice_red = '负 '.$this->getLibrary('changenum')->num2rmb(-$count_sprice_red);
		$this->V->mark(array('pageid_red'=>$pageid_red,'title'=>'','datalist_red'=>$datalist_red,'count_nums_red'=>$count_nums_red,'count_sprice_red'=>number_format($count_sprice_red,2),'big_count_sprice_red'=>$big_count_sprice_red));

	}

	/*模板输出*/
	$this->V->set_tpl('adminweb/print_stockorder');
	display();
}

/*关闭订单*/
elseif($detail == 'turnoff'){
	if(!$this->C->service('admin_access')->checkResRight('r_w_colcigou')){$this->C->ajaxmsg(0);}//关闭权限判断
	if($delid){
		$sid = $this->S->dao('process')->D->update_by_field(array('id'=>$delid),array('isover'=>'Y'));
		if($sid) echo '已关闭';
	}
}

/*AJAX修改采购备注*/
elseif($detail == 'editcoment'){
	if($this->S->dao('process')->D->update_by_field(array('id'=>$id),array('comment'=>$comment))){echo '1';}
}



/*批量采购获取资料*/
elseif($detail == 'getcont_to_stock'){

	if(empty($strid))exit('无选择的记录');

	$process = $this->S->dao('process');
	$warehou = $this->C->service('warehouse');

	/*检测选中的纪录是否包含不符合状态的，防止逆向操作，只有预采购状态的备货记录才能采购*/
	$datalist = $process->D->get_all_sql("select provider_id,ruser,property from process where id in (".$strid.")");
	if(!is_array($datalist)) {$this->C->sendmsg('不合理操作！');}

	/*取得仓库下拉,默认选择仓库取设置的中值--Start*/
	$default_makestock_warehouse = $this->C->service('global')->sys_settings('makestock_warehouse','sys');
	$sourcehtml		= $warehou->get_whouse('receiver_id[]','name','id','id',$default_makestock_warehouse);

	/*权限检测*/
	for($ii = 0; $ii < count($datalist); $ii++){

		if(!$this->C->service('admin_access')->checkResRight('none','follow',$datalist[$ii]['ruser'])) $this->C->sendmsg('不合理操作<br><br>不能采购非本人接收的订单！');
		if($ii != 0) {if($datalist[$ii]['provider_id'] != $datalist[$ii-1]['provider_id']) $this->C->sendmsg('不合理操作<br><br>不同供应商的订单不能统一下单！');}

	}

	$supplier 		= $this->S->dao('esse')->D->get_one(array('id'=>$datalist['0']['provider_id']),'name');//取供应商名称
	$datalist 		= $process->gostockorder($strid);
	foreach($datalist as &$val) {
		$val['sourcehtml'] = $sourcehtml;
		$val['detail_id']  = $val['id'];
		$val['quantity']   = $val['quantity'] - $val['countnum'];
	}


	/*生成币别下拉，默认选中人民币*/
	$currencyhtml	= $warehou->get_coincode_html('coin_code','','CNY');

	$this->V->mark(array('title'=>'采购下单-采购列表(list)','datalistt'=>$datalist,'currencyhtml'=>$currencyhtml,'supplier'=>$supplier,'moddetail'=>'stockmod'));
	$this->V->set_tpl('adminweb/process_editstock');//采购填写页的独立模板
	display();

}

/*采购下单保存*/
elseif($detail == 'stockmod'){

	if(empty($suppliername)) exit('<font size=-1>请选择供应商！</font>');

	sort($detail_id);//批量采购时对传过来的上级明细ID进行升序排序处理，否则数据不正确。


	/*根据供应商名称取得实体供应商ID作为明细表供应者ID*/
	$backdata 		= $this->S->dao('esse')->D->select('id','name="'.$suppliername.'"');
	if(!$backdata['id']) exit('<font size=-1>系统未录入的供应商！</font>');

	/*取得复制的内容,批量采购也是取一次,取多条作为二维数组,不放在循环里重复取*/
	$strid 			= '('.implode(',',$detail_id).')';
	$copydata		= $this->S->dao('process')->D->get_allstr(' and id in'.$strid,'','id asc','receiver_id,sku,fid,pid,product_name,countnum,quantity');

	/*生成采购单号,取得采购最大订单号,并取出数字,c+7位数字,不够补0,批量采购用同一个采购订单号*/
	$sqlstr			= ' and property="采购单"';
	$max			= $this->S->dao('process')->maxorder($sqlstr);
	$order_id		= 'c'.sprintf("%07d",substr($max,1)+1);

	/*取最新期号*/
	$backrate		= $this->S->dao('exchange_rate')->D->get_one_by_field(array('code'=>$coin_code,'isnew'=>1),'stage_rate');

	/*统计页面总数量*/
	$pagesums		= 0;
	for($i=0; $i<count($quantity); $i++){$pagesums+= $quantity[$i];}


	$pershare		= number_format($shipping/$pagesums, 2);//每个产品平摊的运费
	$extrafare		= $pershare*$pagesums - $shipping;//计算存在四舍五入计算零头

	$count_success	= array();
	$objprocess		= $this->S->dao('process');
	$objprocess->D->query('BEGIN');//开始一个事务

	/*数据操作*/
	for($i=0; $i<count($detail_id); $i++){

		/*不含税单价*/
		$price 		= $e_sprice[$i]/$quantity[$i];

		/*①采购单数据插入*/
		$insert_arr = array('provider_id'=>$backdata['id'],'coin_code'=>$coin_code,'stage_rate'=>$backrate['stage_rate'],'receiver_id'=>$receiver_id[$i],'sku'=>$copydata[$i]['sku'],'detail_id'=>$detail_id[$i],'fid'=>$copydata[$i]['fid'],'pid'=>$copydata[$i]['pid'],'product_name'=>$copydata[$i]['product_name'],'quantity'=>$quantity[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'order_id'=>$order_id,'property'=>'采购单','comment'=>$comment[$i]);

		$insert_arr['price']	= $price;

		/*独立保存已经平摊到产品里的即付运费*/
		if($i == count($detail_id)-1){
			$insert_arr['fee']	= $pershare*$quantity[$i] - $extrafare;//最后一条零头多减少补。
		}else{
			$insert_arr['fee']	= $pershare*$quantity[$i];//该条记录平摊的运费。
		}

		/*扩展内容处理*/
		$extends = array(
			'e_recdate'	=>$e_recdate[$i],//到货日期
			'e_siprice'	=>$e_siprice[$i],//价税合计
			'e_iprice'	=>$e_siprice[$i]/$quantity[$i],//含税单价=价税合计/数量
			'e_sprice'	=>$e_sprice[$i],//不含税合计
			'e_cost'	=>$price + $pershare,//采购成本=不含税单价+平摊费用
			'e_scost'	=>$e_sprice[$i] + $insert_arr['fee'],//总成本=不含税合计+平摊的费用
		);

		/*单位总成本=总成本/数量,下单时到付运费为空，即等于采购成本，后期若增加费用需要更新*/
		$insert_arr['price2']	= $extends['e_scost']/$quantity[$i];

		$extends				= get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);//如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
		$insert_arr['extends']	= $extends;

		$sid 					= $objprocess->D->insert($insert_arr);

		/*②回写该备货单的累计执行量*/
		$jid 					= $objprocess->D->update_by_field(array('id'=>$detail_id[$i]),array('countnum'=>$quantity[$i]+$copydata[$i]['countnum']));


		/*如果插入与回写都成功，提交事务*/
		if($sid && $jid){

			$jbackdata = $objprocess->D->get_one_by_field(array('id'=>$detail_id[$i]),'quantity,countnum');
			if($jbackdata['quantity'] == $jbackdata['countnum']){
				$bid = $objprocess->D->update_by_field(array('id'=>$detail_id[$i]),array('isover'=>'Y'));

				/*成功关闭订单了才提交事务*/
				if($bid) {$objprocess->D->query('commit');}else{$objprocess->D->query('rollback');$count_success[] = $copydata[$i]['fid'];}
			}else{
				/*无段关闭订单，直接提交事务*/
				$objprocess->D->query('commit');
			}

		}else{
			$objprocess->D->query('rollback');$count_success[] = $copydata[$i]['fid'];
		}

	}

	/*判断是否存在失败，并返回失败的单号*/
	$overjumpurl = 'index.php?action=process_modstock&detail=list';
	if(!$count_success){
		$this->C->success('下单成功,请等待审核!',$overjumpurl);
	}else{
		$failfid = implode(',',$count_success);
		$this->C->success('存在失败纪录:'.$failfid,$overjumpurl);
	}

}

/*
 * update on 2012-04-24
 * 搜索供应商显示于下拉框*/
elseif($detail == 'getsupplier'){
	$q = strtolower($_GET["q"]);
	if (!$q) return;
	$datalist = $this->S->dao('esse')->D->get_list(' and type=3 and name like"%'.$q.'%"','','','id,name');
	foreach ($datalist as $val){
		echo $val['name']."\n";
	}
}

/*审核采购订单与采购确认操作*/
elseif($detail == 'auditfull'){
	$process = $this->S->dao('process');
	$strid = stripslashes($strid);

	/*审核*/
	if($act == 'audit'){
		if(!$this->C->service('admin_access')->checkResRight('r_w_cigou')){$this->C->ajaxmsg(0);}//审核权限判断

		/*检测选中的纪录是否包含不符合状态的，防止逆向操作*/
		$datalist = $process->D->get_all_sql("select statu from process where order_id in (".$strid.")");
		if(!is_array($datalist)) {$this->C->ajaxmsg(0,'不合理操作');}
		foreach ($datalist as $val){
			if($val['statu'] !='0'){$this->C->ajaxmsg(0,'只能操作未审核的记录，请检查状态');}//只能对未审核的采购单记录审核
		}

		date_default_timezone_set('Etc/GMT-8');
		$sid = $process->D->update_sql('where order_id in('.$strid.')',array('statu'=>1,'muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time())));
		if($sid){$this->C->ajaxmsg(1,'审核成功');}
	}

	/*采购确认*/
	elseif($act == 'ensure'){

		if(!$this->C->service('admin_access')->checkResRight('r_w_surcigou')){$this->C->ajaxmsg(0);}//确认采购权限判断
		$not_payorder = '';

		/*检测选中的纪录是否包含不符合状态的，防止逆向操作*/
		$datalist = $process->D->get_all_sql("select statu,ispay,property,order_id from process where order_id in (".$strid.")");
		if(!is_array($datalist)) {$this->C->ajaxmsg(0,'不合理操作！');}
		foreach ($datalist as $val){
			if($val['statu'] !='1' || $val['property']=='备货单'){$this->C->ajaxmsg(0,'只能操作已审核的采购单记录，请检查状态');}//只能对已审核的采购单记录确认
			if($val['ispay'] == '0') $not_payorder.= $val['order_id'].',';//检测是否存在未上传水单的。
		}
		if($not_payorder) {
			$this->C->ajaxmsg(0,'操作失败，存在未上传水单的订单：'.$not_payorder);
		}
		$sid = $process->D->update_sql('where order_id in('.$strid.')',array('statu'=>3,'ruser'=>$_SESSION['eng_name'],'rdate'=>date('Y-m-d H:i:s',time())));//对于采购单，状态3代表已确认。
		if($sid){$this->C->ajaxmsg(1,'确认成功');}else{$this->C->ajaxmsg(0,'确认失败，请稍候重试！');}
	}
}

/**
 * create on 2012-08-20
 * 导出表格
 *
 */
elseif ($detail == "output") {
	$sqlstr .= ' and isover="N" and property="采购单" ';

	/*已下单状态,显示所有采购单，包括关闭的*/
	if($statu == "3") {
		$sqlstr = str_replace('and isover="N"','',$sqlstr);
		$sqlstr = str_replace('and statu="3"','and (statu="3" or statu="4")',$sqlstr);
	}
	/*默认显示未付款的采购单*/
	if (!isset($ispay)) {$sqlstr.=' and ispay="0" ';$ispay='0';}

	/*区分当前是显示可备货单还是采购单的处理*/
	$reperson 		= 'cuser';
	$date	  		= 'cdate';
	$show_date		= '下单日期';
	$show_order_id  = '采购单号';
	$show_countnum 	= '入库数量';
	$show_quantity  = '采购数量';
	$shwhouse_name  = '入库仓库';

	$sqlstr = str_replace(' and ruser',' and p.cuser',$sqlstr);
	$sqlstr = str_replace('supplier','s.name',$sqlstr);

	$process	= $this->S->dao('process');
	$datalist	= $process->showneedsto($sqlstr);

	for($i=0;$i<count($datalist);$i++){

		/*需要另外定义orderidd(默认等order_id,重复才置空,多条备货一次采购只显示一个采购单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] 	== $datalist[$i-1]['order_id']) {

			$datalist[$i]['orderidd'] 	= '';
		}else{
			$datalist[$i]['orderidd'] 	= $datalist[$i]['order_id'];
		}


		/*将额外信息存进输出数组$datalist*/
 		$extends 				   	= json_decode($datalist[$i]['extends'],true);

 		$datalist[$i]['allpay']	   	= number_format($extends['e_siprice'] + $datalist[$i]['fee'], 2);//付款金额

 		$datalist[$i]['e_iprice']  	= number_format($extends['e_iprice'],2);
 		$datalist[$i]['e_siprice'] 	= number_format($extends['e_siprice'],2);
 		$datalist[$i]['e_sprice']  	= number_format($extends['e_sprice'],2);
 		$datalist[$i]['e_cost']  	= number_format($extends['e_cost'],2);
 		$datalist[$i]['e_scost']  	= number_format($extends['e_scost'],2);

 		$datalist[$i]['comment']   		= empty($datalist[$i]['comment'])?'--':$datalist[$i]['comment'];

		/*水单图片显示处理,多条纪录只一条显示'未付款'*/
		if($extends['img_url']){
			$datalist[$i]['imispay'] 	= '';
			for ($j=0;$j<count($extends['img_url']);$j++){
				$datalist[$i]['imispay'].= '已付款';
			}
		}elseif($datalist[$i]['order_id']== $datalist[$i-1]['order_id'] && $datalist[$i]['statu'] != '4'){
			$datalist[$i]['imispay'] 	= '';
		}elseif($datalist[$i]['ispay']	=='0' && $datalist[$i]['statu'] != '4'){
			$datalist[$i]['imispay'] 	= '未付款';
		}

		/*调出备货人*/
		$back_d_u 						= $process->D->get_one_by_field(array('id'=>$datalist[$i]['detail_id']),'cuser');
		$datalist[$i]['pcuser'] 		= $back_d_u['cuser'];

		/*采购红单作红色高亮处理*/
		if($datalist[$i]['statu'] == '4'){
			$datalist[$i]['fid'] 			= '<font color=red>'.$datalist[$i]['fid'].'</font>';
			$datalist[$i]['sku'] 			= '<font color=red>'.$datalist[$i]['sku'].'</font>';
			$datalist[$i]['pcuser']			= '<font color=red>'.$datalist[$i]['pcuser'].'</font>';
			$datalist[$i]['product_name']	= '<font color=red>'.$datalist[$i]['product_name'].'</font>';
			$datalist[$i]['suppliername']	= '<font color=red>'.$datalist[$i]['suppliername'].'</font>';
			$datalist[$i]['whouse_name']	= '<font color=red>'.$datalist[$i]['whouse_name'].'</font>';
			$datalist[$i]['quantity'] 		= '<font color=red>'.$datalist[$i]['quantity'].'</font>';
			$datalist[$i]['countnum'] 		= '<font color=red>'.$datalist[$i]['countnum'].'</font>';
			$datalist[$i]['price']	 		= '<font color=red>'.$datalist[$i]['price'].'</font>';
			$datalist[$i]['e_sprice'] 		= '<font color=red>'.$datalist[$i]['e_sprice'].'</font>';
			$datalist[$i]['fee'] 			= '<font color=red>'.$datalist[$i]['fee'].'</font>';
			$datalist[$i]['fee2'] 			= '<font color=red>'.$datalist[$i]['fee2'].'</font>';
			$datalist[$i]['fee3'] 			= '<font color=red>'.$datalist[$i]['fee3'].'</font>';
			$datalist[$i]['allpay'] 		= '<font color=red>'.$datalist[$i]['allpay'].'</font>';
			$datalist[$i]['e_cost'] 		= '<font color=red>'.$datalist[$i]['e_cost'].'</font>';
			$datalist[$i]['e_siprice'] 		= '<font color=red>'.$datalist[$i]['e_siprice'].'</font>';
			$datalist[$i]['price2'] 		= '<font color=red>'.$datalist[$i]['price2'].'</font>';
			$datalist[$i]['e_scost'] 		= '<font color=red>'.$datalist[$i]['e_scost'].'</font>';
			$datalist[$i]['e_iprice'] 		= '<font color=red>'.$datalist[$i]['e_iprice'].'</font>';
			$datalist[$i]['coin_code']		= '<font color=red>'.$datalist[$i]['coin_code'].'</font>';
			$datalist[$i]['cuser'] 			= '<font color=red>'.$datalist[$i]['cuser'].'</font>';
			$datalist[$i]['cdate'] 			= '<font color=red>'.$datalist[$i]['cdate'].'</font>';
			$datalist[$i]['isover'] 		= '<font color=red>'.$datalist[$i]['isover'].'</font>';
			$datalist[$i]['comment'] 		= '<font color=red>'.$datalist[$i]['comment'].'</font>';
		}

	}


	//导出字段
    $head_array = array(
    	'orderidd'		=> '采购单号',
		'fid'			=> '备货单号',
		'pcuser'		=> '备货人',
		'sku'			=> '产品SKU',
		'product_name'	=> '产品名称',
		'suppliername'	=> '供应商',
		'whouse_name'	=> '入库仓库',
		'quantity'		=> '采购数量',
		'countnum'		=> '入库数量',
		'price'			=> '不含税单价',
		'e_sprice'		=> '不含税合计',
		'e_iprice'		=> '含税单价',
		'e_siprice'		=> '价税合计',
		'fee'			=> '即付运费',
		'allpay'		=> '付款金额',
		'e_cost'		=> '采购成本',
		'fee2'			=> '到付运费',
		'fee3'			=> '其它费用',
		'price2'		=> '单位总成本',
		'e_scost'		=> '总成本',
		'coin_code'		=> '币别',
		'cuser'			=> '备货接收人',
    	'cdate'			=> '下单日期',
		'comment'		=> '备注'
    );

    if ($statu == '1') {
    	$head_array['muser'] 	= '审核人';
    }
	if ($statu=='1' || $statu=='3') {
		$head_array['imispay'] 	= '付款状态';
	}
	if ($statu == 3) {
		$head_array['isover'] 	= '关闭标志';
	}

	$filename 					= '采购单'.date('Y_m_d_h_i_s', time());

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}


/**
 * @title 采购发票--金蝶导出数据
 * @author Jerry
 * @create on 2012-12-27
 */ 
 
elseif ($detail == 'jindieoutput') {
    $process = $this->S->dao('process');
    	
    $sqlstr = str_replace('statu','p.statu',$sqlstr);
    
    /*区别接收人查备货单与采购单时不同字段。*/
	if($statu != 'N') {$sqlstr = str_replace(' and ruser',' and p.cuser',$sqlstr);}

 	/*采购单时才有供应商。*/
	if($statu != 'N') {$sqlstr = str_replace('supplier','s.name',$sqlstr);}
    
    $sqlstr .= ' and p.isover="N" and p.property="采购单" ';
	$datalist  = $process->showjingdieoutput($sqlstr);
 
    $filename  = 'jingdieoutput_'.date('Y-m-d');

    for($i = 0; $i < count($datalist); $i++){
        $datalist[$i]['oncredit']       = '赊购';//采购方式
        $datalist[$i]['exchangerate']   = 1;//汇率默认为1
        $datalist[$i]['coincode']       = '人民币';//币别默认为人民币
        $datalist[$i]['paytime']        = date('Y-m-d',strtotime($datalist[$i]['cdate'].'+30 day'));//到期付款日 = 建单日期+30天
        $datalist[$i]['discountrate']   = 0;//折扣税率
        $extend                         = json_decode($datalist[$i]['extends'],true);
        $datalist[$i]['p_cost']         = $datalist[$i]['fee']/$datalist[$i]['quantity'];
        $datalist[$i]['cdate']          = date('Y-m-d', strtotime($datalist[$i]['cdate']));
        $datalist[$i]['model']          = 'pc';
        $datalist[$i]['sku']            = preg_replace('/-/','.',$datalist[$i]['sku'],1);
        $datalist[$i]['skuname']        = '';
        $datalist[$i]['weight']         = ''; 
        //含税单价+每个sku的即时运费
        $everycost                      = $extend['e_iprice']+$datalist[$i]['p_cost'];
        
        //付款金额 = 价税合计+即付运费
        //税率=（付款金额-采购成本*数量）/采购成本*数量
        $_allpay    = $extend['e_siprice'] + $datalist[$i]['fee'];
        $_money     = $_allpay-$everycost *$datalist[$i]['quantity'];
        $_dmoney    = $everycost*$datalist[$i]['quantity'];
        $datalist[$i]['taxrate']       = number_format($_money/$_dmoney,2); 
        $datalist[$i]['e_cost']        = round($everycost,6);   
    }
    
    $head_array = array(
            'order_id'      => '采购发票号', 
            'cdate'         => '单据日期', 
            'coincode'      => '币别',
            'suppliername'  => '供应商',
            'exchangerate'  => '汇率',
            'oncredit'      => '采购方式', 
            'paytime'       => '付款期限', 
            'sku'           => '物料代码', 
            'skuname'       => '物料代码名称',
            'weight'        => '规格型号',
            'model'         => '单位', 
            'quantity'      => '数量',
            'e_cost'        => '单价',
            'taxrate'       => '税率',
            'discountrate'  => '折扣税率'
    );
                
    $this->C->service('upload_excel')->download_excel($filename, $head_array, $datalist);

}

/*记录延迟采购原因*/
elseif ($detail == 'buytime_comment') {

    if(!$this->C->service('admin_access')->checkResRight('buytime_notice')){$this->C->sendmsg();}//权限判断
	if(empty($id))exit('没有id!');
	$process  = $this->S->dao('process');
    $data = $process->D->get_one_by_field(array('id'=>$id),'*');
    $data['extends'] = json_decode($data['extends'],true);
	$jump = 'index.php?action=process_modstock&detail=buytime_commentmod';
    $this->V->view['title'] = '延迟采购时间';
    
	/*表单配置*/
	$conform = Array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = Array('1'=>'100','2'=>'220','3'=>'80');
    $datastr = "<input type=text name=buytime class='find-T twodate' onclick='WdatePicker({minDate:\"%y-%M-%d\"})' value=".$data['extends']['buytime'].">";
    
	$disinputarr = Array();
	$disinputarr['id'] 	       = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['order_id']   = array('showname'=>'备货单号','value'=>$data['order_id'],'inextra'=>'readonly');
	$disinputarr['buytime']    = array('showname'=>'时间','datatype'=>'se','datastr'=>$datastr);
	$disinputarr['remark']     = array('showname'=>'备注','value'=>'');
	$temp = 'pub_edit';
    $jslink = "<script language='javascript' type='text/javascript' src='./staticment/js/My97DatePicker/WdatePicker.js'></script>";
}

/*记录延迟采购原因操作*/
elseif ($detail == 'buytime_commentmod'){
    
    $friend_message     = $this->S->dao('friend_message');
    $process            = $this->S->dao('process');
    $time               = date('Y-m-d',time());
    
    $datalist           = $process->D->get_one_by_field(array('order_id'=>$order_id),'sku,cuser,extends');
    $user               = $this->S->dao('user');
    $backUdata          = $user->D->get_one_by_field(array('eng_name'=>$datalist['cuser']),'uid');
    $json_extends       = json_decode($datalist['extends'],true);
    $json_extends['buytime']  = $buytime;
    $extends = get_magic_quotes_gpc()?addslashes(json_encode($json_extends)):json_encode($json_extends);
    $data = $process->D->update_by_field(array('id'=>$id),array('extends'=>$extends));
    if ($data){
        $content    = '你好,订单号：'.$order_id.',该备货单被延迟采购,延迟到'.$buytime.',制单人'.$datalist['cuser'].',原因：'.$remark.',请知悉！';
        $r = $friend_message->insert_one_leave_message($backUdata['uid'], '', 'System', $content, 0, $time, '', $time);
        if ($r>0){$this->C->success("延迟采购时间成功！","index.php?action=process_modstock&detail=buytime_comment&id=".$id."");}else{$this->C->success("操作失败！","index.php?action=process_modstock&detail=buytime_comment&id=".$id."");}
    }
}

/*模板定义*/
if($detail =='list' || $detail == 'getcont_to_stock' || $detail =='upspay' || $detail =='modaddpay' || $detail =='editorder' || $detail == 'make_cancle' || $detail == 'buytime_comment'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

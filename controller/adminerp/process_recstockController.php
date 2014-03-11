
<?php
/*
 * Created on 2011-12-8
 * by hanson
 *
 * @title 入仓操作
 *
 * @class 采购入库，转仓入库，退货入库，其他入库
 */

 if($detail == 'list'){
	if($statu == '3' || !isset($statu)) {$stypeshow = '订单号：';}elseif($statu == 'e') {$stypeshow = '库单号：';}
	/*搜索选项*/
	$stypemu = array(
		'statu-h-e'=>'状态:',
		'sku-s-l'=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;SKU：',
		'order_id-s-e'=>'&nbsp; &nbsp;'.$stypeshow,
		'receiver_id-a-e'=>'&nbsp; &nbsp; 仓库：',
		'rece_type-a-e'=>'类型：',
		'fid-s-e'=>'<br>&nbsp;运单编号：',
		'comment2-s-l'=>'&nbsp;&nbsp;&nbsp;跟踪号：',
		'cuser-s-l'=>'&nbsp;&nbsp;&nbsp;&nbsp;制单：',
	);

	$sqlstr = str_replace('cuser','p.cuser',$sqlstr);

	/*搜索条件变更处理*/
	if($sqlstr && !empty($rece_type)){
		if($statu == '3'){//在预入库中按类型搜索
			switch($rece_type){
				case 'cr':$sqlstr = str_replace('and rece_type="cr"','and p.property="采购单"',$sqlstr);break;
				case 'tr':$sqlstr = str_replace('and rece_type="tr"','and p.protype="退货" and p.active="1" and p.input="0"',$sqlstr);break;
				case 'hr':$sqlstr = str_replace('and rece_type="hr"','and p.property="转仓单" and p.active="1" and p.output="1" and p.input="0" ',$sqlstr);break;
			}
		}elseif($statu == 'e'){//在已入库中搜入库类型
			$linksql = ' and p.active="1" and p.input="1" ';
			switch($rece_type){
				case 'cr':$sqlstr = str_replace('and rece_type="cr"','and p.property="进仓单" and p.protype="采购" '.$linksql,$sqlstr);break;
				case 'tr':$sqlstr = str_replace('and rece_type="tr"','and p.protype="退货" '.$linksql,$sqlstr);break;
				case 'hr':$sqlstr = str_replace('and rece_type="hr"','and p.property="转仓单" '.$linksql,$sqlstr);break;
				case 'er':$sqlstr = str_replace('and rece_type="er"','and p.property="进仓单" and p.protype="其它" '.$linksql,$sqlstr);break;
			}
		}
	}


	/*默认打开的显示,注意statu=3*/
	if(empty($sqlstr) && !isset($statu)){
		$sqlstr.= ' and (statu="3" or statu="4") ';$statu='3';
	}

	/*入库类型为空时，显示所有可入库的单*/
	if($statu=='3' && empty($rece_type)){
		$sqlstr.= ' and (p.property="采购单" or (p.property="转仓单" and p.output="1" and p.input="0") or (p.property="进仓单" and p.input="0" and p.statu="3")) ';
	}elseif($statu=='e' && empty($rece_type)){
		$sqlstr.= ' and (p.property="进仓单" or p.property="转仓单") and p.input="1" ';
	}

	/*过滤已没用的前置条件*/
	if($statu == 'e'){
		$sqlstr = str_replace('and statu="e"','',$sqlstr);
	}elseif($statu=='3'){
		$sqlstr.=' and p.isover ="N"';//订单状态需要加上未关闭条件
	}

	/*如果是预入库状态，显示入库类型*/
	$rece_typearr = array(''=>'=请选择=','cr'=>'采购入库','hr'=>'转仓入库','tr'=>'退货入库');

	if($statu == '3'){
		$orders = ' order by p.mdate asc,p.order_id asc,p.statu asc,p.id desc';//按顺序排列
		$sqlstr = str_replace('and statu="3"','and (p.statu="3" or p.statu="4")',$sqlstr);//为读出采购红单做的替换
	}elseif($statu == 'e'){
		$rece_typearr['er'] = '其他入库';//如果是入库单显示页面，搜索增加其他入库
		$orders = ' order by p.rdate desc,p.id desc';//按倒序排列
	}


	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	$receiver_idarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$receiver_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

	/*标签导航选项*/
	$tab_menu_stypemu = array(
		'statu-3'=>'在途',
		'statu-e'=>'已入库',
	);

 	/*
	 * update on 2012-05-09
	 * by wall
	 * 工作提醒传过时间查找
	 * */
	if (!empty($job_alert_time)) {
		$sqlstr .= ' and p.rdate like "%'.$job_alert_time.'%" ' ;
		$pageshow = array('job_alert_time' => $job_alert_time);
	}
	$InitPHP_conf['pageval'] = 15;
	$process = $this->S->dao('process');

	$datalist = $process->showneedrec($sqlstr,$orders);



	/*数据处理*/
	for($i=0;$i<count($datalist);$i++){


		/*需要另外定义orderidd(默认等order_id,重复才置空,多条备货一次采购只显示一个采购单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['orderidd'] = '';
		}else{
			$datalist[$i]['orderidd'] = $statu =='3'?'<a href=index.php?action=process_recstock&detail=dorece&order_id='.$datalist[$i]['order_id'].'&page='.$page.'>'.$datalist[$i]['order_id'].'</a>':$datalist[$i]['order_id'];
		}

		/*如果是其它入库单，显示删除*/
		if(substr($datalist[$i]['order_id'],0,1) == 'e'){
			$datalist[$i]['dele'] = '<a href=javascript:void(0);delitem("index.php?action=process_recstock&detail=delextra&order_id='.$datalist[$i]['order_id'].'")  title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
		}

		/*采购入库的入库单，显示反入库*/
		elseif(substr($datalist[$i]['order_id'],0,1) == 'w' && $datalist[$i]['order_id'] != $datalist[$i-1]['order_id']){
			$back_c_oid 				= $process->D->get_one_by_field(array('id'=>$datalist[$i]['detail_id']),'order_id');
			$datalist[$i]['preorderid'] = $back_c_oid['order_id'];
			$datalist[$i]['orderidd'] 	= '<span title="上级操作单号：'.$back_c_oid['order_id'].'">'.$datalist[$i]['orderidd'].'</span>';
			$datalist[$i]['dele'] 		= '<a href=javascript:void(0);delitem("index.php?action=process_recstock&detail=delstock&order_id='.$datalist[$i]['order_id'].'","确定反入库？操作成功后该入库单将被删除，对应的采购单重新显示在途！")  title=反入库><img src="./staticment/images/sysback.gif" border="0"></a>';
		}

		/*转仓入库也显示反入库*/
		elseif(substr($datalist[$i]['order_id'],0,1) == 'f' && $statu == 'e' && $datalist[$i]['order_id'] != $datalist[$i-1]['order_id']){
			$datalist[$i]['dele'] 		= '<a href=javascript:void(0);delitem("index.php?action=process_recstock&detail=backtranf&order_id='.$datalist[$i]['order_id'].'","确定反入库？操作成功后该转仓单将显示在途！")  title=反入库><img src="./staticment/images/sysback.gif" border="0"></a>';
		}

		/*如果是采购单，显示关闭按钮*/
		elseif($datalist[$i]['property']=='采购单' && $datalist[$i]['order_id']!=$datalist[$i-1]['order_id']){
			$datalist[$i]['dele'] =  '<a href=javascript:void(0);delitem("index.php?action=process_recstock&detail=closeorder&order_id='.$datalist[$i]['order_id'].'","确定关闭？关闭后该采购单将不再显示，无法入库；请确保该订单已完成入库或无需再入库！")  title=手动关闭该采购单>&times;</a>';
		}
		else{
			$datalist[$i]['dele'] = '<font color=#c6a8c6>--</font>';
		}

		if($datalist[$i]['warehouse'] == ''){
			$datalist[$i]['warehouse'] 	= '不良品仓';
			$datalist[$i]['fid'] 		= '';
		}

		if(($datalist[$i]['property'] == '转仓单' || $datalist[$i]['protype'] == '退货') && $statu == 'e'){
			$datalist[$i]['cuser'] = $datalist[$i]['muser'];
		}

		/*作红色高亮处理*/
		if($datalist[$i]['statu'] == '4'){
			$datalist[$i]['sku'] 			= '<font color=red>'.$datalist[$i]['sku'].'</font>';
			$datalist[$i]['product_name']	= '<font color=red>'.$datalist[$i]['product_name'].'</font>';
			$datalist[$i]['warehouse']		= '<font color=red>'.$datalist[$i]['warehouse'].'</font>';
			$datalist[$i]['quantity'] 		= '<font color=red>'.$datalist[$i]['quantity'].'</font>';
			$datalist[$i]['countnum'] 		= '<font color=red>'.$datalist[$i]['countnum'].'</font>';
			$datalist[$i]['cuser'] 			= '<font color=red>'.$datalist[$i]['cuser'].'</font>';
			$datalist[$i]['cdate'] 			= '<font color=red>'.$datalist[$i]['cdate'].'</font>';
		}

		/*显示备货人*/
		if($rece_type == 'cr'){
			$datalist[$i]['pcuser'] 		= $process->D->get_one(array('order_id'=>$datalist[$i]['fid']),'cuser');
			if($statu == 'e') $datalist[$i]['modstockcuser'] 	= $process->D->get_one(array('id'=>$datalist[$i]['detail_id']),'cuser');
		}

	}

	/*显示表头不同处理*/
	if($statu == '3'){
		$rperson = '制单';
		$ranum 	 = '数量';
		$rcdate  = '下单日期';
		$chuser  = 'cuser';
		$showarr_date= 'cdate';
		$chanorderid = '订单号';
	}elseif($statu == 'e'){
		$rperson = '经办';
		$ranum 	 = '数量';
		$rcdate  = '入库日期';
		$chuser  = 'ruser';
		$showarr_date= 'rdate';
		$chanorderid = '入库单号';
	}

	/*定义输出数组*/
	$displayarr = array();
	$tablewidth = '1100';

	$displayarr['order_id'] 	= array('showname'=>'checkbox','title'=>'全选','width'=>'50');
	$displayarr['orderidd'] 	= array('showname'=>$chanorderid,'width'=>'80');

	/*采购已入库需要显示的*/
	if($statu == 'e' && $rece_type == 'cr'){
		$displayarr['preorderid'] = array('showname'=>'上级单号','width'=>'90');
	}

	/*转仓需要显示的*/
	if($rece_type == 'tr' || $rece_type == 'hr'|| $receiver_id == '7' || $receiver_id == '9') {
		$displayarr['fid'] 		= array('showname'=>'第三方单号','width'=>'90');
		$displayarr['comment2']	= array('showname'=>'跟踪号','width'=>'100');
	}

	$displayarr['sku'] 			= array('showname'=>'产品SKU','width'=>'95');
	$displayarr['product_name'] = array('showname'=>'产品名称','width'=>'150');
	$displayarr['warehouse']	= array('showname'=>'接收仓库','width'=>'110');
	$displayarr['quantity'] 	= array('showname'=>$ranum,'width'=>'50');
	if($statu == '3') {$displayarr['countnum'] = array('showname'=>'已入库','width'=>'60');}

	if($rece_type == 'cr'){
		$displayarr['pcuser'] 	= array('showname'=>'备货','width'=>'70');
		if($statu == 'e') $displayarr['modstockcuser'] 	= array('showname'=>'制单','width'=>'70');
	}
	$displayarr[$chuser] 		= array('showname'=>$rperson,'width'=>'70');

	$displayarr[$showarr_date] 	= array('showname'=>$rcdate,'width'=>'90');
    $displayarr['comment']      = array('showname'=>'备注','width'=>'100');
	$displayarr['dele'] 		= array('showname'=>'操作','width'=>'50');

	$jslink = "<script src='./staticment/js/process_recstock.js'></script>\n";
 	$this->V->mark(array('title'=>'入仓操作'));
 	$temp = 'pub_list';

 }

/*转仓反下单*/
elseif($detail == 'backtranf'){

	$process = $this->S->dao('process');
	if(!$this->C->service('admin_access')->checkResRight('r_w_backtranf', 'mod', $process->D->get_one(array('order_id'=>$order_id),'ruser'))){$this->C->ajaxmsg();}//权限判断

	$sid = $process->D->update(array('order_id'=>$order_id),array('input'=>0,'ruser'=>'','rdate'=>''));
	if($sid){$this->C->ajaxmsg(0,0,1);}else{echo '操作失败，请重试！';}
}

/*采购反下单*/
elseif($detail == 'delstock'){

	$process 	= $this->S->dao('process');

	if(empty($order_id)) exit('非法操作');
	$error_num	= 0;
	$backdata	= $process->D->get_allstr(' and order_id="'.$order_id.'"','','','detail_id,quantity,cuser,receiver_id,sku');//取得入库单的上级ID
	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_w_backware')){$this->C->ajaxmsg();}
    
    
	$process->D->query('begin');
	for($i = 0; $i < count($backdata); $i++){
	   
        //如果是蛇口仓库，限制反入库数量大于原库存
        if ($backdata[$i]['receiver_id'] == 10)
        {
           // $where = ' and temp.sku ="'.$backdata[$i]['sku'].'" and temp.wid = 10';
            $datalist	= $process->get_allw_allsku(' and temp.sku="'.$backdata[$i]['sku'].'" and temp.wid=10 ');
            //echo '<pre>';print_r($datalist);die();
            if ($datalist[0]['sums'] < $backdata[$i]['quantity'])
            {   
                $error_num++;
            }else{
                $sid = $process->D->update(array('id'=>$backdata[$i]['detail_id']),'countnum = countnum - '.$backdata[$i]['quantity']);
                if(!$sid) $error_num++;
            }
	        
	   }else{
	       $sid = $process->D->update(array('id'=>$backdata[$i]['detail_id']),'countnum = countnum - '.$backdata[$i]['quantity']);
           if(!$sid) $error_num++;
	   }
        
    }
    
    if (!$error_num){
	   $c_orderid	= $process->D->get_one(array('id'=>$backdata['0']['detail_id']),'order_id');//取入库单对应的采购单
	   $uid 		= $process->D->update(array('order_id'=>$c_orderid),array('isover'=>'N'));//有反下单则原单所有记录显示在预入库中。

	   $cid = $process->D->delete_by_field(array('order_id'=>$order_id));//删除入库单
    }
	if(!$cid || !$uid) $error_num++;
	if(empty($error_num)){$process->D->query('commit');$this->C->ajaxmsg(0,0,1);}else{$process->D->query('rollback');echo '操作失败，请重试，请检查sku库存是否充足！';}

}

 /*执行库存清零*/
 elseif($detail == 'modclearup'){
	if(empty($houseid)) $this->C->sendmsg('不存在仓库！');

	/*权限判断*/
 	if(!$this->C->service('admin_access')->checkResRight('r_w_extrainstock')){$this->C->sendmsg();}

	$process	= $this->S->dao('process');
	$warehouse	= $this->C->service('warehouse');
 	$datalist 	= $process->get_allw_allsku(' and temp.wid='.$houseid,2);
 	$count_num 	= count($datalist);
	$msdate		= date('Y-m-d H:i:s',time());

	/*实例化自动包含文件*/
	$this->C->service('exchange_rate');
	$finansvice 	= $this->C->service('finance');

 	/*执行清零*/
 	for($i=0;$i<$count_num;$i++){

		if($datalist[$i]['sums']>0) {
			$key_vider_id = 'provider_id';
			$key_property = '出仓单';
			$key_put	  = 'output';
			$order_id	  = $warehouse->get_maxorder_manay('出仓单','t',$process);;
			$do_insert	  = 1;
		} elseif($datalist[$i]['sums']<0) {
			$key_vider_id = 'receiver_id';
			$key_property = '进仓单';
			$key_put   	  = 'input';
			$order_id	  = $warehouse->get_maxorder_manay('进仓单','e',$process);
			$datalist[$i]['sums'] = -$datalist[$i]['sums'];
			$do_insert	  = 1;
		}
		if($do_insert == 1){

			$insert_arr = array("$key_vider_id"=>$houseid,'sku'=>$datalist[$i]['sku'],'pid'=>$datalist[$i]['pid'],'product_name'=>$datalist[$i]['product_name'],'quantity'=>$datalist[$i]['sums'],'cdate'=>$msdate,'mdate'=>$msdate,'rdate'=>$msdate,'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>$key_property,'protype'=>'其它',"$key_put"=>'1','comment'=>'库存清零调整');

			/*增加保存即时成本，期号，币别(CNY)*/
			$finansvice->rewrite_inorup_arr(&$insert_arr,$datalist[$i]['pid']);
			$process->D->insert($insert_arr);
			$do_insert = 0;
		}
 	}

	$this->C->success('清零完毕','index.php?action=process_recstock&detail=clearup_page');

 }

 /*库存清0,慎用此功能,一般用于重新清零库存导入盘点表格*/
 elseif($detail == 'clearup_page'){

	/*生成仓库下拉*/
	$houseidstr = $this->C->service('warehouse')->get_whouse('houseid','name','id');

	/*表单配置*/
	$conform 	= array('method'=>'post','action'=>'index.php?action=process_recstock&detail=modclearup');
	$colwidth 	= array('1'=>'50','2'=>'250','3'=>'350');

	$disinputarr = array();
	$disinputarr['houseid']	= array('showname'=>'仓库','datatype'=>'se','datastr'=>$houseidstr,'showtips'=>'<span class=tips> *选择需要进行库存清零的仓库</span>');

	$bannerstr = '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:645px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
	$bannerstr.= '该功能慎用，使用后该仓库的库存将被清零，一般用于盘点录入前的清空。';
	$bannerstr.= '</div>';

	$this->V->mark(array('title'=>'库存清零-导入盘点表(sumsinstock)'));
	$temp = 'pub_edit';
 }

 /*手动关闭采购单，权限同采购入库*/
 elseif($detail == 'closeorder'){

 	if(!$this->C->service('admin_access')->checkResRight('r_w_clsware')){$this->C->ajaxmsg(0);}
 	$sid = $this->S->dao('process')->D->update_by_field(array('order_id'=>$order_id),array('isover'=>'Y'));
 	if($sid) {$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(0,'关闭失败');}


 }

 /*删除其它入库的入库单*/
 elseif($detail == 'delextra'){

 	/*删除权限判断*/
 	if(!$this->C->service('admin_access')->checkResRight('r_w_deloutorder')){$this->C->ajaxmsg(0,$msg);}

	$sid = $this->S->dao('process')->D->delete_by_field(array('order_id'=>$order_id));
	if($sid){$this->C->ajaxmsg(1);}else{exit('删除失败!');}
 }


 /*盘点录入*/
 elseif($detail == 'sumsinstock'){
	/*权限判断*/
 	if(!$this->C->service('admin_access')->checkResRight('r_w_extrainstock')){$this->C->sendmsg();}

	//取上传的文件的数组
	$upload_dir = "./data/uploadexl/sums_instock/";//上传文件保存路径的目录
	$fieldarray = array('A','B','C');//有效的excel列表值
	$head = 1;//以第一行为表头

	$tablelist = '';
	/*读取已经上传的文件*/
	if($filepath){


		$all_arr =  $this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);

		/*生成出仓单号,取得出仓最大单号，并取出数字，x+7位数字，不够补0*/
		$process = $this->S->dao('process');
		$max = $this->C->service('warehouse')->get_maxorder_manay('进仓单','e',$process);

		/*成功统计量*/
		$successnum 	= 0;
		$msdate			= date('Y-m-d H:i:s',time());

		/*实例化自动包含文件*/
		$this->C->service('exchange_rate');
		$finansvice 	= $this->C->service('finance');

		/*开始一个事务，用事务处理，全部成功才插入。避免导入者不知哪些是成功的，保留以后优化*/
		$process->D->query('begin');

		/*录入等于插入其它进仓单*/
		for($i=1;$i<count($all_arr);$i++){

			/*取最大订单号*/
			$order_id = 'e'.sprintf("%07d",substr($max,1)+$i-1);

			$backpidpname[$i] 				= $process->D->get_one_sql('select pid,product_name from product where sku="'.trim($all_arr[$i]['sku']).'"');

			/*获得产品ID*/
			$all_arr[$i]['pid'] 			= $backpidpname[$i]['pid'];
			$all_arr[$i]['product_name'] 	= $backpidpname[$i]['product_name'];


			$insert_arr = array('receiver_id'=>$houseid,'sku'=>trim($all_arr[$i]['sku']),'pid'=>$all_arr[$i]['pid'],'product_name'=>$all_arr[$i]['product_name'],'price'=>$all_arr[$i]['costp'],'quantity'=>$all_arr[$i]['num'],'cdate'=>$msdate,'mdate'=>$msdate,'rdate'=>$msdate,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','order_id'=>$order_id,'property'=>'进仓单','protype'=>'其它','input'=>'1');

			/*增加保存即时成本，期号，币别(CNY)*/
			$finansvice->rewrite_inorup_arr(&$insert_arr,$all_arr[$i]['pid']);

			$sid = $process->D->insert($insert_arr);

			if($all_arr[$i]['costp']) {$cid = $finansvice->updatecost($all_arr[$i]['pid'],$all_arr[$i]['costp'],$all_arr[$i]['costp']*1.05,date('Y-m-d H:i:s',time()));if($sid && $cid) $successnum++;}//更新产品成本，币种CNY
			elseif($sid){$successnum++;}
		}

		/*事后处理*/
		$overjumpurl = 'index.php?action=process_recstock&detail=sumsinstock';

		if($successnum == count($all_arr)-1){//需要减1，表头不算
		 	$process->D->query('COMMIT');
			$this->C->success('入库成功!',$overjumpurl);
		}else{
			$process->D->query('ROLLBACK');
			$this->C->success('入库失败!',$overjumpurl);
		}
	}

	/*上传文件并读取*/
	else{
		$product 	= $this->S->dao('product');
		$all_arr 	= $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath 	= $this->getLibrary('basefuns')->getsession('filepath');
		$tablelist 	= '';
		$tablelist .= '<table id="mytable">';

		/*表头特殊显示处理*/
		$tablelist .= '<tr>';
		$table_thkeys = array_keys($all_arr['0']);
		for ($i=0;$i<count($table_thkeys);$i++){
			$tablelist.='<th class=list width=120>'.$all_arr['0'][$table_thkeys[$i]].'</th>';
		}
		$tablelist .= '</tr>';
		unset($all_arr['0']);

		//处理数组
		$exl_row = 0;
		$data_error = 0;

		foreach($all_arr as $k=>$val){
			$exl_row++;
			$tablelist .= '<tr>';
			if(is_array($val)){
				foreach( $val as $j=>$value) {
					$error_style = '';
					/*判断是否存在SKU格式错误*/
					if($j=='sku'){
						 if(!preg_match("/(^(\d)+-\d+-(\d+)$)|(^(\d)+-\d+-\d+-(\w+)$)/",trim($value))){//此处是去除前后空格后，保存处理注意也需作此处理
							$error_style = ' bgcolor="red" title="SKU格式不对,格式如(236-41-48或者236-41-48-CD001)"';
							$data_error++;
						 }else{
							$back_pdutid = $product->D->get_one_by_field(array('sku'=>trim($value)),'pid');
							if(!$back_pdutid['pid']){
								$error_style = ' bgcolor="red" title="系统不存的SKU，请先在产品管理中录入该SKU。"';
								$data_error++;
							}
						 }
					}

					if($j == 'num'){
						if(!preg_match("/^\d*$/",$value)){
							$error_style = ' bgcolor="red" title="请填写正确的整数！"';
							$data_error++;
						}
					}

					if($j=='costp'){
						if($value){
							if(!preg_match("/^\d+(.[\d]{2})?$/",$value)){
								$error_style = ' bgcolor="red" title="成本格式不正确，最多保留两位小数！"';
								$data_error++;
							}
						}else{
							$error_style = 'title=不写costp则不改变系统产品原成本价';
						}
					}
					$tablelist .= '<td '.$error_style.' >&nbsp;'.$value.'</td>';
				}
			}
			$tablelist .= '</tr>';
		}
		$tablelist .= '</table>';
		$tablelist .= "<script src='./staticment/js/process_recstock.js'></script>\n";


		/*仓库选择*/
		$warehouse_html = $this->C->service('warehouse')->get_whouse('houseid onchange=showhouseid(this.value)','name','id','id',$houseid);
		$warehouse_html.= '<font color="#bdbdbd" size="-1">&nbsp;(请选择了仓库再上传文件，如果该仓库已存在物料库存，必须先进行</font><a href="index.php?action=process_recstock&detail=clearup_page" title="点击转至库存清零页面"><font color=#577dc6 size=-1><u>库存清零</u></font></a> )<br><br>';

		$tablelist     .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
		$tablelist     .= '<input type="hidden" name="houseid" value="'.$houseid.'" />';

		/*错误判断*/
		if(!$data_error && isset($all_arr) && $houseid){
			$tablelist .= '<span id=checkhouse ><input type="submit" value="确认提交"></span>';
			unset($warehouse_html);
		}elseif($data_error){
			$exl_error_msg= '总共有<b> '.$data_error.' </b>处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);//有错的文件删除掉
		}
		elseif(empty($houseid) && isset($all_arr)){
			$exl_error_msg= ' 请选择仓库！';
			$tablelist.= '<span id=checkhouse  style="display:none" ><input type="submit" value="确认并提交"><input type="reset" value="取消" onclick=window.location="index.php?action=process_shipment&detail=list"></span>';
		}

	}

	$submit_action  = 'index.php?action=process_recstock&detail=sumsinstock';
	$temlate_exlurl = 'data/uploadexl/sample/sums_instock.xls';

	$this->V->mark(array('' .
			'title'=>'导入盘点表',
			'message_upload'=>$warehouse_html,
			'tablelist'=>$tablelist,
			'submit_action'=>$submit_action,
			'temlate_exlurl'=>$temlate_exlurl,
			'exl_error_msg'=>$exl_error_msg,
			'exl_error_width'=>600,
	));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
 }

 /*其他入库填写资料*/
 elseif($detail == 'extra'){

	/*权限判断*/
 	if(!$this->C->service('admin_access')->checkResRight('r_w_extrainstock')){$this->C->sendmsg();}

	$whouse = $this->C->service('warehouse')->get_whouse('houseid[]','name','id','id',$this->C->service('global')->sys_settings('extra_inhouse','sys'));/*获得仓库*/
	$this->V->mark(array('type'=>'input','whouse'=>$whouse,'title'=>'其它入库','in_or_out_whouse'=>'入库仓库','jump_action'=>'index.php?action=process_recstock&detail=modextra'));
	$this->V->set_tpl('adminweb/process_recextrastock');
 	display();

 }

 /*其他出库填写资料*/
 elseif($detail == 'extraout'){

	$whouse = $this->C->service('warehouse')->get_whouse('houseid[]','name','id','id',$this->C->service('global')->sys_settings('extra_outhouse','sys'));/*获得仓库*/ 
	$this->V->mark(array('type'=>'output','whouse'=>$whouse,'title'=>'其它出库','in_or_out_whouse'=>'出库仓库','jump_action'=>'index.php?action=process_recstock&detail=modextraout'));
	$this->V->set_tpl('adminweb/process_recextrastock_extractout');
 	display();

 }

 /*批量导入其它出入库*/
 elseif($detail == 'import_extra'){
    
    $process		= $this->S->dao('process');
	if($type == 'output'){
		$title		= '其它出库(extraout)';
    	/*生成出仓单号,取得其它出仓最大单号，并取出数字，t+7位数字，不够补0*/
		$max = $this->C->service('warehouse')->get_maxorder_manay('出仓单','t',$process);
		$property	= '出仓单';
        $_eid       = 'provider_id';
	}else{
		$title		= '其它入库(extra)';
        /*生成入仓单号,取得进仓最大单号，并取出数字，e+7位数字，不够补0*/
	    $max = $this->C->service('warehouse')->get_maxorder_manay('进仓单','e',$process);
		$property	= '进仓单';
        $_eid       = 'receiver_id';
	}
 
	if($_FILES["upload_file"]["name"] || $filepath){

		$upload_exl_service = $this->C->Service('upload_excel');
		$upload_dir 		= "./data/uploadexl/temp/";//上传文件保存路径的目录

		$fieldarray			= array('A','B','C','D');//有效的excel列表值
		$headarray			= array('sku','num','warehouse','comment');//用于表头检测
		$objproduct			= $this->S->dao('product');
		$objwareh			= $this->S->dao('esse');
        
		/*用于检测仓库*/
		$backhouse		= $objwareh->D->get_allstr(' and type="2"','','','id,name');
		$backhouseArr   = array();
		foreach($backhouse as $val){
			$backhouseArr[$val['name']] = $val['id'];
		}

		if($filepath){
			$all_arr		= $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray, 1);
			$errorcount		= 0;
			unset($all_arr['0']);//删除表头
			unlink($filepath);//删除文件

			/*实例化自动包含文件，用于取得即时成本*/
			$this->C->service('exchange_rate');
			$finansvice 	= $this->C->service('finance');

			$process->D->query('begin');
          
			foreach($all_arr as $k=> $val){
                $backpdut	= $objproduct->D->get_one(array('sku'=>$val['sku']),'pid,product_name');
                if ($backpdut['pid'])	{
                    $backassem = $process->output_child_sku(' and s.pid='.$backpdut['pid']);
                }
                if($type == 'output')
                    $order_id = 't'.sprintf("%07d",substr($max,1)+$k);
                else
                    $order_id = 'e'.sprintf("%07d",substr($max,1)+$k);
                    
                $ktime		= date('Y-m-d H:i:s',time());
                /*组装的SKU*/
                if(count($backassem)>1){
                    foreach ($backassem as $key => $value) {
                        $quantitynew = intval($val['num']*$value['quantity']);
                        $insert_arr = array($_eid=>$backhouseArr[$val['warehouse']],'sku'=>$value['sku'],'pid'=>$value['pid'],'product_name'=>$value['product_name'],'quantity'=>$quantitynew,'cdate'=>$ktime,'mdate'=>$ktime,'rdate'=>$ktime,'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>$property,'protype'=>'其它',$type=>'1','comment'=>$val['comment']);
                        /*保存即时成本，期号，币别*/
                        $finansvice->rewrite_inorup_arr(&$insert_arr,$value['pid']);
                        $sid = $process->D->insert($insert_arr);
                        if(!$sid)$errorcount++;
                    }
                }else{
                    $insert_arr = array($_eid=>$backhouseArr[$val['warehouse']],'sku'=>$val['sku'],'pid'=>$backpdut['pid'],'product_name'=>$backpdut['product_name'],'quantity'=>$val['num'],'cdate'=>$ktime,'mdate'=>$ktime,'rdate'=>$ktime,'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>$property,'protype'=>'其它',$type=>'1','comment'=>$val['comment']);
                    /*增加保存即时成本，期号，币别(CNY)*/
                    $finansvice->rewrite_inorup_arr(&$insert_arr,$backpdut['pid']);
                    $sid = $process->D->insert($insert_arr);
                    if(!$sid)$errorcount++;
                }
			}

			/*事后处理*/
			$overjumpurl = 'index.php?action=process_recstock&detail=import_extra&type='.$type;
			if(empty($errorcount)){
			 	$process->D->query('COMMIT');
				$this->C->success('导入成功!',$overjumpurl);
			}else{
				$process->D->query('ROLLBACK');
				$this->C->success('导入失败!',$overjumpurl);
			}

		}else{
			$data_error 	= 0;
			$tablelist 		= '<table id="mytable">';
			$all_arr		= $upload_exl_service->get_upload_excel_datas($upload_dir, $fieldarray, 1);//上传并获取数据
			$filepath		= $_SESSION['filepath'];
            

            $cur_sku_count = array();
            $product      = $this->S->dao('product');
            $sku_assembly = $this->S->dao('sku_assembly');
            $process      = $this->S->dao('process');
            $cur_sku      = array();
            
			/*表头检测，若有错，显示表头*/
			$tablelist	   .= $upload_exl_service->checkmod_head(&$all_arr,&$data_error,$headarray);
			foreach($all_arr as $k=>$val){
                
			$tablelist .= '<tr>';
            
            /***********************start*********************************/
             /*
            *@title 其他出库检测库存，不足则不可提交
            *@authr Jerry
            *@create on 2013-07-19
            */
            if ($type == 'output' && $backhouseArr[$val['warehouse']] == TRANSFER_HOUSE){
                //默认设置需要检查库存的仓库-中国蛇口仓   
                $sku = $val['sku'];
                $houseid = $backhouseArr[$val['warehouse']];
                $quantity = $val['num'];
        
        		$rs = $product->get_product_by_sku($sku);
        
        		if ($rs) {
                    $backchecksku = $product->D->get_one_by_field(array('sku'=>$sku),'pid');
                    if ($backchecksku['pid'])	{
                        $backassem = $sku_assembly->get_sonlist(' and s.pid='.$backchecksku['pid']);
                    }
                    
                    /*非组装SKU，单一判断*/
                    if(!$backassem){
        					$cur_sku_count[$houseid][$sku]+= $quantity;
        				    $kc = $this->S->dao('process')->get_allw_allsku(' and temp.sku="'.$sku.'" and temp.wid='.$houseid);
        					if ($kc['0']['sums'] < $cur_sku_count[$houseid][$sku]) {
        					    $skip= 'SKU：'.$sku.'库存不足，可发：'.($kc['0']['sums']?$kc['0']['sums']:'0').'个';
                                $error_style_quantity = ' bgcolor="red" title="'.$skip.'"';
                                $data_error++;
        					}
        				
                    }
                    /*组装的SKU，提取原SKU判断逐一判断库存*/
                    elseif($backassem && is_array($backassem)){
                        if ($houseid) {
            					$skipp= '';
            					for($ass = 0 ; $ass < count($backassem); $ass++){
            					   $_sku = $backassem[$ass]['sku'];//当前子SKU
        
                                   $back_enough = $process->get_allw_allsku(' and temp.sku="'.$_sku.'" and temp.wid='.$houseid);//原有子SKU总量
            					   $cur_sku_count[$houseid][$_sku] += $backassem[$ass]['quantity']*$quantity;//需要的累计总量
                                   if ($cur_sku_count[$houseid][$_sku] > $back_enough['0']['sums'] ) {
                                        $skipp.= 'SKU：'.$_sku.'库存不足，可发：'.($back_enough['0']['sums']?$back_enough['0']['sums']:'0').'个。';
                                        
                                   }
            					}
                                if($skipp) {
                                    $skip = '这是组装产品，提取出的'.$skipp;
                                    $error_style_quantity = ' bgcolor="red" title="'.$skip.'"';
                                    $data_error++;
                                }
                        }
                         
                    }
        
    		      }
            }
            /***********************end*********************************/
                
				foreach($val as $j=>$value) {
					$error_style = '';

					/*检测SKU*/
					if($j == 'sku'){
						if(!$objproduct->D->get_one(array('sku'=>$value),'pid')){
							$error_style = ' bgcolor="red" title="系统不存在的SKU！"';
							$data_error++;
						}
					}

					/*检测数量不能为空并且是正整数*/
					if($j == 'num'){
						if(empty($value) || !preg_match('/^[\d]*$/',$value)  || $value<0){
							$error_style = ' bgcolor="red" title="请检查数量！"';
							$data_error++;
						}
					}

					/*检测仓库*/
					if($j == 'warehouse'){
						if(!$backhouseArr[$value]){
							$error_style = ' bgcolor="red" title="系统不存在的仓库！"';
							$data_error++;
						}
                        
					}
                    
					$tablelist  .= '<td '.$error_style.$error_style_quantity.' >&nbsp;'.$value.'</td>';
				}
                
                    
				$tablelist .= '</tr>';
			}
			$tablelist	   .= '</table>';

			if(!$data_error && isset($all_arr)){
				$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
				$tablelist .= '<input type="submit" value="确认并提交">';
			}elseif($data_error){
				$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
				unlink($filepath);//有错的文件删除掉
			}
		}
	}

	$temlate_exlurl = 'data/uploadexl/sample/other_output.xls';
	$this->V->set_tpl('adminweb/commom_excel_import');
	$this->V->mark(array('title'=>'批量导入-'.$title,'tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600','temlate_exlurl'=>$temlate_exlurl));
	display();
 }

 
 /*执行保存其它出库*/
 elseif($detail == 'modextraout'){
 	/*权限判断*/
 	if(!$this->C->service('admin_access')->checkResRight('r_w_extrainstock')){$this->C->sendmsg();}

	$process = $this->S->dao('process');

	/*成功统计量*/
 	$successcout = 0;

 	$error_sku	 = '';
	for ($i=0;$i<count($sku);$i++){
		if(empty($pid[$i]))	$error_sku .=  $sku[$i].',\n';
	}
	if($error_sku){
		$this->C->success('保存失败，存在无法获取产品ID的SKU:\n'.$error_sku.'请检查重试!','index.php?action=process_recstock&detail=extraout');
		exit();
	}

	/*实例化自动包含文件*/
	$this->C->service('exchange_rate');
	$finansvice 	= $this->C->service('finance');


	/*开始一个事务*/
	$process->D->query('begin');

	/*生成出仓单号,取得其它出仓最大单号，并取出数字，t+7位数字，不够补0*/
	$max = $this->C->service('warehouse')->get_maxorder_manay('出仓单','t',$process);
    $skunum = count($sku);
	for ($i=0;$i<count($sku);$i++){
        
         if ($pid[$i])	{
            $backassem = $process->output_child_sku(' and s.pid='.$pid[$i]);
         }
         $order_id 	= 't'.sprintf("%07d",substr($max,1)+$i);
         /*组装的SKU*/
         if($backassem && is_array($backassem)){
             foreach ($backassem as $key => $value) {
                $quantitynew = intval($quantity[$i]*$value['quantity']);
                
                $insert_arr = array('provider_id'=>$houseid[$i],'sku'=>$value['sku'],'pid'=>$value['pid'],'product_name'=>$value['product_name'],'price'=>$price[$i],'quantity'=>$quantitynew,'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'出仓单','protype'=>'其它','output'=>'1','comment'=>$comment[$i]);
                /*保存即时成本，期号，币别*/
                $finansvice->rewrite_inorup_arr(&$insert_arr,$value['pid']);
                $sid = $process->D->insert($insert_arr);
                if($sid){$successcout++;}
            }
            $skunum = $skunum-1+count($backassem);
         }else{
            $insert_arr = array('provider_id'=>$houseid[$i],'sku'=>$sku[$i],'pid'=>$pid[$i],'product_name'=>$product_name[$i],'price'=>$price[$i],'quantity'=>$quantity[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'出仓单','protype'=>'其它','output'=>'1','comment'=>$comment[$i]);

            /*增加保存即时成本，期号，币别(CNY)*/
            $finansvice->rewrite_inorup_arr(&$insert_arr,$pid[$i]);

            $sid 		= $process->D->insert($insert_arr);
            if($sid) $successcout++;
         }   
		
	}

	/*事后处理*/
	$overjumpurl = 'index.php?action=process_recstock&detail=extraout';
	if($successcout == $skunum){
	 	$process->D->query('COMMIT');
		$this->C->success('出库成功!',$overjumpurl);
	}else{
		$process->D->query('ROLLBACK');
		$this->C->success('出库失败!',$overjumpurl);
	}
 }


 /*执行保存其他入库*/
 elseif($detail == 'modextra'){

 	$process = $this->S->dao('process');

 	/*成功统计量*/
 	$successcout = 0;

 	$error_sku	 = '';
	for ($i=0;$i<count($sku);$i++){
		if(empty($pid[$i]))	$error_sku .=  $sku[$i].',\n';
	}
	if($error_sku){
		$this->C->success('保存失败，存在无法获取产品ID的SKU:\n'.$error_sku.'请检查重试!','index.php?action=process_recstock&detail=extra');
		exit();
	}

	/*实例化自动包含文件*/
	$this->C->service('exchange_rate');
	$finansvice 	= $this->C->service('finance');


	/*开始一个事务*/
	$process->D->query('begin');

	/*生成出仓单号,取得进仓最大单号，并取出数字，e+7位数字，不够补0*/
	$max = $this->C->service('warehouse')->get_maxorder_manay('进仓单','e',$process);
    $skunum = count($sku);
 	/*录入数据，$price会赋予$cost1(即costp)*/
	for ($i=0;$i<count($sku);$i++){
         if ($pid[$i])	{
            $backassem = $process->output_child_sku(' and s.pid='.$pid[$i]);
         }
         $order_id 	= 'e'.sprintf("%07d",substr($max,1)+$i);
         /*组装的SKU*/
         if(count($backassem)>1){
             foreach ($backassem as $key => $value) {
                $quantitynew = intval($quantity[$i]*$value['quantity']);
                $insert_arr = array('receiver_id'=>$houseid[$i],'sku'=>$value['sku'],'pid'=>$value['pid'],'product_name'=>$value['product_name'],'price'=>$price[$i],'quantity'=>$quantitynew,'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'进仓单','protype'=>'其它','input'=>'1','comment'=>$comment[$i]);
                /*保存即时成本，期号，币别*/
                $finansvice->rewrite_inorup_arr(&$insert_arr,$value['pid']);
                $sid = $process->D->insert($insert_arr);
                if($sid){$successcout++;}
            }
            $skunum = $skunum-1+count($backassem);
         }else{
            $insert_arr = array('receiver_id'=>$houseid[$i],'sku'=>$sku[$i],'pid'=>$pid[$i],'product_name'=>$product_name[$i],'price'=>$price[$i],'quantity'=>$quantity[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'进仓单','protype'=>'其它','input'=>'1','comment'=>$comment[$i]);
             /*保存即时成本，期号，币别*/
            $finansvice->rewrite_inorup_arr(&$insert_arr,$pid[$i]);
            $sid = $process->D->insert($insert_arr);
            if($sid){$successcout++;}
         }  
	}


	/*事后处理*/
	$overjumpurl = 'index.php?action=process_recstock&detail=extra';
	if($successcout == $skunum){
	 	$process->D->query('COMMIT');
		$this->C->success('入库成功!',$overjumpurl);
	}else{
		$process->D->query('ROLLBACK');
		$this->C->success('入库失败!',$overjumpurl);
	}

 }


/*入库区别入库类型跳转填写资料页面,*/
 elseif($detail == 'dorece'){

	if(empty($order_id)) exit('缺乏订单号！');

	$order_pre = substr($order_id,0,1);

	switch ($order_pre){
		case 'c':$moddetail = 'stockmod';if(!$this->C->service('admin_access')->checkResRight('r_w_ctoware')){$this->C->sendmsg();}$this->V->set_tpl('adminweb/process_recstock');break;//采购入库detail与模板
		case 'f':$moddetail = 'frecmod';if(!$this->C->service('admin_access')->checkResRight('r_w_ftoware')){$this->C->sendmsg();}$this->V->set_tpl('adminweb/process_transfer_recstock');break;//转仓入库detail与模板
		case 'r':$moddetail = 'frecmod';if(!$this->C->service('admin_access')->checkResRight('r_w_rtoware')){$this->C->sendmsg();}$this->V->set_tpl('adminweb/process_transfer_recstock');break;//退货入库detail与模板
	}

	$process	= $this->S->dao('process');


	/*采购入库*/
	if($order_pre == 'c'){
		$datalist	= $process->gorecstockcigou($order_id);
		$backdata_statu = $process->D->get_one_by_field(array('order_id'=>$order_id,'statu'=>'4'),'id');

		/*判断是否存在注销单，用于提醒仓管*/
		if($backdata_statu) { $this->V->view['bannerstr'] = '提示：该采购单存在注销单，订单数量是减去抵消单数量后的数量。'; }
	}

	/*转仓入库与采购入库*/
	elseif($order_pre == 'f' || $order_pre =='r'){
		$datalist 	= $process->gorecstockorder($order_id);
	}

 	$this->V->mark(array('title'=>'入库-常规入库(list)','datalist'=>$datalist,'sourcehtml'=>$sourcehtml,'order_id'=>$order_id,'moddetail'=>$moddetail,'page'=>$page));
 	display();
 }



 /*采购入库保存操作*/
 elseif($detail == 'stockmod'){

	if(!is_array($detail_id)){exit('没有操作记录！');}else{
		sort($detail_id);/*批量采购时对传过来的上级明细ID进行升序排序处理，否则数据不正确。*/
	}

	$process = $this->S->dao('process');

	$worder_id = $this->C->service('warehouse')->get_maxorder_manay('进仓单','w',$process);


	/*取得需复制数据*/
 	$strid = '('.implode(',',$detail_id).')';
 	$copydata = $process->D->get_allstr(' and id in'.$strid,'','id asc','id,quantity,countnum,provider_id,receiver_id,sku,fid,pid,product_name,price,coin_code,stage_rate,cuser');

	/*失败记录统计*/
	$error_num		= 0;
	$announce_array	= array();
	$announce_caiar = array();
	$sucess_msg		= '入库成功';
	$failed_msg		= '入库失败，请重试';
	$this->C->service('exchange_rate');//实体化自动包含文件，用于成本转换
	$finance		= $this->C->service('finance');
	$product_cost	= $this->S->dao('product_cost');

	/*事务开始---------------START*/
	$process->D->query('BEGIN');

 	/*有填写到付运费，回写采购单的产品采购成本*/
 	$rewrite_errornum 		= 0;
 	if($shipfare){
		$backdata 			= $finance->stockorder_addfare($process,$order_id,'到付运费',$shipfare,'fee2');
		$rewrite_errornum	= $backdata['error'];
 	}

	/*插入进仓单，复制采购单部分内容,备注与数量根据页面填写传送过来,($sid插入进仓单,$cid回写累计执行量,$jid回写产品成本价)*/
 	for($i=0;$i<count($copydata);$i++){

 		if(!empty($quantity[$i])){

			/*移动加权平均更新产品成本(CNY)*/
			$backcount_cost 	= $finance->countnum_cost($process, $copydata[$i]['id'], $quantity[$i]);
			$jid 				= $finance->updatecost($copydata[$i]['pid'],$backcount_cost,1.05*$backcount_cost,date('Y-m-d H:i:s',time()));

			/*插入进仓单*/
		 	$sid 				= $process->D->insert(array('provider_id'=>$copydata[$i]['provider_id'],'receiver_id'=>$copydata[$i]['receiver_id'],'sku'=>$copydata[$i]['sku'],'detail_id'=>$detail_id[$i],'fid'=>$copydata[$i]['fid'],'pid'=>$copydata[$i]['pid'],'product_name'=>$copydata[$i]['product_name'],'price'=>$copydata[$i]['price'],'price2'=>$backcount_cost,'coin_code'=>$copydata[$i]['coin_code'],'stage_rate'=>$copydata[$i]['stage_rate'],'quantity'=>$quantity[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','order_id'=>$worder_id,'property'=>'进仓单','protype'=>'采购','input'=>'1','comment'=>$comment[$i]));

			/*回写累计执行量*/
			$newcountnum		= $copydata[$i]['countnum']+$quantity[$i];
			$cid 				= $process->D->update_by_field(array('id'=>$detail_id[$i]),array('countnum'=>$newcountnum));

			/*累计执行量若小于原单数量，记录通知采购*/
			if($newcountnum < $copydata[$i]['quantity']){
				$announce_caiar[] = array('order_id'=>$order_id,'quantity'=>$quantity[$i],'squantity'=>$copydata[$i]['quantity'],'sku'=>$copydata[$i]['sku'],'countnum'=>$newcountnum,'cuser'=>$copydata[$i]['cuser']);
			}

			/*到货通知销售客服*/
			$announce_array[] 	= array('c_id'=>$detail_id[$i],'quantity'=>$quantity[$i],'countnum'=>$newcountnum);
			if(!$sid || !$cid || !$jid) $error_num++;
 		}
 	}


 	/*判断是否全部成功*/
 	$overjumpurl = 'index.php?action=process_recstock&detail=list&statu=3&rece_type=cr&page='.$page;
 	if (empty($error_num) && empty($rewrite_errornum)){

 		/*统计订单中每条记录的累计执行量,如果相等，关闭该订单*/
 		$newbacklist	= $process->D->get_allstr(' and order_id="'.$order_id.'" and statu!="4" ','','','id,quantity,countnum');
 		$checkisover	= 0;//订单完成量标记
 		foreach($newbacklist as $val){
 			if($val['countnum'] < $val['quantity']){//如果累计执行量比原单数量要小
 				$calnums = $process->D->get_one(array('detail_id'=>$val['id'],'property'=>'采购单'),'sum(quantity)');
 				if($calnums){
	 				if(($val['quantity'] - $val['countnum']) > -$calnums) $checkisover++;//如需入库的数量比红单数量大，则算未完成入库。
 				}else{
 					$checkisover++;//不存在红单，则未算完成入库。
 				}
 			}
 		}

 		if(empty($checkisover)){//标记完成
 			$bid = $process->D->update_by_field(array('order_id'=>$order_id),array('isover'=>'Y'));
	 		if($bid){
	 			$process->D->query('COMMIT');//如果全部记录复制成功，并回写成功，再更新状态成功才提交。
				$this->C->success($sucess_msg,$overjumpurl);
				$this->C->service('global')->announce_restock($announce_array,$process,'upstock');//发起通知
	 		}else{
	 			$process->D->query('ROLLBACK');
	 			$this->C->success($failed_msg,$overjumpurl);
	 		}
 		}else{
 			/*如果累计执行量不够，不关闭订单，直接提交。该订单会继续在预入库显示*/
			$process->D->query('COMMIT');
			$this->C->service('global')->announce_restock($announce_array,$process,'upstock');//发起通知
			$this->C->service('global')->announce_restock($announce_caiar,$process,'modstock');//通知采购入库数小于订单数
			$this->C->success($sucess_msg,$overjumpurl);
 		}

 	}else{
 		/*失败回滚*/
 		$process->D->query('ROLLBACK');
 		$this->C->success($failed_msg,$overjumpurl);
 	}

 	/*事务结束----------------END*/

 }


 /*转仓入库与退货入仓操作*/
 elseif($detail == 'frecmod'){

	/*需要存在本条数据的ID才执行以下操作*/
 	if($detail_id){

		/*损坏情况数组*/
 		$damagedarr 	= array('defective_customer','damaged_distributor','damaged_carrier','damaged_warehouse','damaged');

 		/*成功与失败统计量*/
 		$successcount 	= 0;
 		$failroll 		= 0;
		$process 		= $this->S->dao('process');

		/*实例化自动包含文件*/
		$this->C->service('exchange_rate');
		$finansvice 	= $this->C->service('finance');

		$process->D->query('begin');
		$backdata 		= $process->D->get_all(array('order_id'=>$order_id),'id','asc','receiver_id,sku,pid,product_name');//读取需复制的资料，此处需要按顺序读取

		for($i=0;$i<count($detail_id);$i++){

			$sid = $process->D->update_by_field(array('id'=>$detail_id[$i]),array('input'=>'1','ruser'=>$_SESSION['eng_name'],'rdate'=>date('Y-m-d H:i:s',time())));//更新转仓单接收置1,同时更新修改时间为入库时间。

			/*如果各种损坏情况有写，则插入转仓单(接收仓(作为供应者)->不良仓(接收者为空)的转仓单即为不良品仓,comment2标记不同状态)*/
			for($j=0;$j<count($damagedarr);$j++){
				if(${$damagedarr[$j]}[$i]){
					$max_id = $this->C->service('warehouse')->get_maxorder('转仓单','f',$process);

					$insert_arr = array('provider_id'=>$backdata[$i]['receiver_id'],'sku'=>$backdata[$i]['sku'],'detail_id'=>$detail_id[$i],'fid'=>$detail_id[$i],'pid'=>$backdata[$i]['pid'],'product_name'=>$backdata[$i]['product_name'],'quantity'=>${$damagedarr[$j]}[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','order_id'=>$max_id,'property'=>'转仓单','output'=>'1','input'=>'1','comment2'=>$damagedarr[$j]);

					/*增加保存即时成本，期号，币别(CNY)*/
					$finansvice->rewrite_inorup_arr(&$insert_arr,$backdata[$i]['pid']);

					$cid = $process->D->insert($insert_arr);
					if(!$cid) $failroll++;
				}
			}

			if($sid && $failroll==0) $successcount++;

		}


		/*最后判断提交*/
		$rece_type	= (substr($order_id,0,1) == 'f')?'hr':'tr';
		$overjump	= 'index.php?action=process_recstock&detail=list&statu=3&rece_type='.$rece_type.'&page='.$page;
		if($successcount == count($detail_id)) {$this->C->success('操作成功',$overjump);$process->D->query('commit');}else{$this->C->success('操作失败',$overjump);$process->D->query('rollback');}
 	}

 }


 /*模板定义*/
 if($detail == 'list' || $detail == 'dorece' ||$detail =='extra' || $detail =='damae' || $detail == 'extraout' || $detail == 'sumsinstock' || $detail == 'clearup_page' || $detail =='import_extra'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

 }
?>

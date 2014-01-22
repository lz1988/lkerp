<?php
/*
* Created on 2011-11-16
* by hanson
* @title 备货申请
*/
include_once('./api/FBAInventoryServiceMWS/function.php');

/*状态映射*/
$conditionerp_arr = array(
			    ""          =>"正常",
			    "normal"    =>"正常",
			    "emptying"  =>"清库",
			    "quality"   =>"停止销售-质量问题",
			    "profit"    =>"停止销售-低利润",
			    "tort"      =>"停止销售-侵权"
);

/*备货列表*/
if($detail == 'list'){
    /*搜索选项*/
    $stypemu = array(
        'statu-h-e'			=>'&nbsp; &nbsp; 状态：',
        'sku-s-l'			=>'&nbsp; &nbsp; SKU：',
        'product_name-s-l'	=>'&nbsp; &nbsp; 产品名称：',
        'order_id-s-e'		=>'&nbsp; &nbsp; 单号：',
        'cuser-s-l'			=>'&nbsp; &nbsp; 备货人：',
        'cdate-t-t'			=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 日期：',
        'isover-a-e'		=>'&nbsp;&nbsp;&nbsp;&nbsp;订单状态：',
    );

    $isoverarr = array(''=>'=请选择=','Y'=>'已关闭','N'=>'未完成');

    /*标签导航选项*/
    $tab_menu_stypemu = array(

		        'statu-0'	=>'未审核',
		        'statu-1'	=>'已审核',
		        'statu-3'	=>'已接收',
		        'statu-2'	=>'不通过',
		        'statu-'	=>'全部',

    );

    $InitPHP_conf['pageval'] = 20;

	/*初始打开默认显示未审核，注意此处要赋值字符值'0'*/
    if(empty($sqlstr) && !isset($statu)){$sqlstr = ' and statu="0"';$statu='0';}

	/*最后都得加上这个条件，只能罗列未关闭的备货单。*/
    $sqlstr.=' and property ="备货单" ';
    $sqlstr = strtr($sqlstr,array('sku'=>'p.sku','product_name'=>'p.product_name'));

    /*联表作前辍替换*/
    $sqlstr = strtr($sqlstr,array('cuser'=>'p.cuser','cdate'=>'p.cdate'));

	/*在已接收的搜索结果中按接收时间显示。*/
    $orders = $statu == '3'?' order by p.rdate DESC,id DESC':'ORDER BY p.ID DESC';

    /*
    * update on 2012-05-09
    * by wall
    * 工作提醒传过时间查找
    */
    if (!empty($job_alert_time)) {
        $sqlstr  	   .= ' and mdate like "%'.$job_alert_time.'%" ' ;
        $pageshow 		= array('job_alert_time' => $job_alert_time);
    }

	/*备货权限应用*/
    $hadadut 			= $this->C->service('admin_access')->checkResRight('r_w_stock');

	/*分页参数,默认15,注意放在statu处理之后,查表之前*/
	$showperhtml = $this->C->service('warehouse')->perpage_show_html(array('0'=>'15','1'=>'50','2'=>'200','3'=>'1000'),$selfval_set,$statu);

	/*查询列表数据*/
    $datalist 			= $this->S->dao('process')->upstock_list($sqlstr,$orders);

    /*数据解压等处理*/
    foreach ($datalist as &$val){

    	/*如果订单信息是已接收状态，此处全部用statu等于空值，所以上面未审核状态要用字符值'0'*/
        $showprocess 	= ($statu == '3' || $statu == '')?'class="showprocess"':'';

        /*标签随机ID*/
        $tag_id			= mt_rand(2000,3000);
        $val['fid'] 	= '<a href=javascript:void(0);self.parent.addMenutab('.$tag_id.',"备货信息'.$val['fid'].'","index.php?action=process_upstock&detail=view_order&tag_statu='.$statu.'&id='.$val['id'].'") '.$showprocess.' id='.$val['id'].'  title="点击进入查看">'.$val['fid'].'</a>';

        switch ($val['statu']){
             case '0':$val['statu'] = '未审核';break;
             case '1':$val['statu'] = '已审核';break;
             case '2':$val['statu'] = '不通过';break;
             case '3':$val['statu'] = '已接收';break;
        }

        /*已审核显示反审核*/
        if($statu == '1'){
            $val['sysba_re']= '<a title="反审核" href=javascript:void(0);delitem("index.php?action=process_upstock&detail=setprev&id='.$val['id'].'","确定反审核？成功后订单将退至未审核")><img src="./staticment/images/sysback.gif" border=0></a>';
        }

        /*将额外信息存进输出数组$datalist*/
        $extends = json_decode($val['extends'],true);
        $extendskeys = array_keys($extends);
         for($i=0;$i<count($extendskeys);$i++){
            $val["$extendskeys[$i]"] = $extends["$extendskeys[$i]"];
        }

        $val['e_lastself'] 		= empty($val['e_lastself'])?'0':$val['e_lastself'];
		$val['e_lastbackrate']	= empty($val['e_lastbackrate'])?'0':$val['e_lastbackrate'];
		$val['e_upc_or_ean']	= empty($val['e_upc_or_ean'])?'0':$val['e_upc_or_ean'];

        $val['e_aprice']    	= number_format($val['e_aprice'],2);
        $val['e_sprice']    	= number_format($val['e_sprice'],2);
        $val['e_rprice']    	= number_format($val['e_rprice'],2);
        $val['profit']      	= number_format($val['e_aprice']/$val['e_rprice'],4) * 100 .'%';
        $val['comment']     	= $val['comment']?$val['comment']:'--';
        $val['e_account']       = $val['e_account'];
        $val['e_issuetime']     = $val['e_issuetime'];

		/*未审核状态并且拥有权限*/
        if ($statu == '0' && $hadadut) {
            $val['conditionerp'] = '<span class="ajax_conditionerp" id="'.$val['id'].'"  title="双击改变状态">'.$conditionerp_arr[$val['conditionerp']].'</span>';
        } else {
            $val['conditionerp'] = $conditionerp_arr[$val['conditionerp']];
        }

        /*采购成本*/
        if($val['coin_code'] != 'USD'){    $val['cost1'] = $this->C->service('exchange_rate')->change_usd($val['coin_code'],$val['cost1']);     }

        /*将采购成本隐藏放置在'单个总成本'之后*/
        $val['price'] = $val['price'].'<span id="cost1" style="display:none;">'.$val['cost1'].'</span>';

        $val['back'] =  '<a href="index.php?action=process_upstock&detail=rollbackpage&id='.$val['id'].'"  title="回退至未审核状态"><img src="./staticment/images/sysback.gif" border="0"></a>';
        $val['back'].=  '&nbsp;&nbsp;<a href="index.php?action=process_upstock&detail=editsuaccount&id='.$val['id'].'"  title="修改账期"><img src="./staticment/images/editbody.gif" border="0"></a>';
    }

    /*定义输出数组*/
    $displayarr = array();
    $tablewidth = '100%';

    $displayarr['id'] 			 = array('showname'=>'checkbox','width'=>'45','title'=>'全选');

    if($statu == '0'){
        $displayarr['both'] 	 = array(
            'showname'  =>'操作',
            'ajax'      =>1,
            'url_e'     =>'index.php?action=process_upstock&detail=edit&id={id}',
            'url_d'     =>'index.php?action=process_upstock&detail=del&id={id}',
            'width'     =>'60');
    }

    if($statu == '1') $displayarr['sysba_re']= array('showname'=>'回退','width'=>'50');
    if($statu == '3') {
        $displayarr['back']      = array('showname'=>'操作','width'=>'60');
    }
    $displayarr['fid']           = array('showname'=>'备货单号','width'=>'80');
    $displayarr['e_stockname']   = array('showname'=>'备货名称','width'=>'150');
    $displayarr['sku']           = array('showname'=>'产品SKU','width'=>'100');
    /*账期信息*/
    $displayarr['esseid']         = array('showname'=>'供货商','width'=>'100');
    $displayarr['e_issuetime']     = array('showname'=>'账期','width'=>'80');
    $displayarr['e_account']       = array('showname'=>'定价','width'=>'80');

    $displayarr['product_name']  = array('showname'=>'产品名称','width'=>'150');
    $displayarr['conditionerp']  = array('showname'=>'产品状态','width'=>'90');
    if($statu == '0'){$displayarr['quantity']      = array('showname'=>'审批数量','width'=>'80','clickedit'=>'id','detail'=>'upquantity');}else{$displayarr['quantity'] = array('showname'=>'审批数量','width'=>'80');}
    $displayarr['receiver_id']   = array('showname'=>'备货仓库','width'=>'100');
    $displayarr['e_inware']      = array('showname'=>'可发库存','width'=>'80');
    $displayarr['e_inwareching'] = array('showname'=>'采购在途数量','width'=>'110');
    $displayarr['e_instocking']  = array('showname'=>'备货在途数量','width'=>'110');
    $displayarr['e_lastself']    = array('showname'=>'销售历史(两周)','width'=>'130','title'=>'过去两周销售纪录');
    $displayarr['e_futureself']  = array('showname'=>'销售预估(两周)','width'=>'130','title'=>'预估到货两周内的销售纪录');
    $displayarr['e_quantity']    = array('showname'=>'备货数量','width'=>'90');
    $displayarr['e_express']     = array('showname'=>'物流方式','width'=>'100');
    $displayarr['price']         = array('showname'=>'单个总成本','width'=>'110');
    $displayarr['e_rprice']      = array('showname'=>'销售价格','width'=>'80');
    $displayarr['e_aprice']      = array('showname'=>'单个利润','width'=>'80');
    $displayarr['profit']        = array('showname'=>'利润率','width'=>'80');
    $displayarr['e_sprice']      = array('showname'=>'总利润','width'=>'70');
    $displayarr['e_lastbackrate']= array('showname'=>'退货率','width'=>'70','title'=>'因质量问题退货率(过去一个月)');
    $displayarr['e_upc_or_ean']  = array('showname'=>'MOQ','width'=>'50');
    $displayarr['cuser']         = array('showname'=>'备货人','width'=>'70');
    $displayarr['cdate']         = array('showname'=>'备货日期','width'=>'110');
    if($statu == '3'){
        $displayarr['ruser']     = array('showname'=>'备货接收人','width'=>'110');
        $displayarr['rdate']     = array('showname'=>'接收日期','width'=>'110');
    }
    $displayarr['comment']       = array('showname'=>'备注','width'=>'120','clickedit'=>'id','detail'=>'upcomment');

    if($statu == ''){
        $displayarr['statu']     = array('showname'=>'订单状态','width'=>'80');
    }

    $this->C->service('global')->disconnect_modbutton(array('0'=>&$mod_disabled_0,'1'=>&$mod_disabled_1),$statu);

    $bannerstr = '<button onclick="window.location=\'index.php?action=process_upstock&detail=add\'" '.$mod_disabled_0.'>备货申请</button>';
    $bannerstr.= '<button onclick=window.location="index.php?action=process_upstock&detail=import_e" '.$mod_disabled_0.'>导入表格</button>';
    $bannerstr.= '&nbsp;<button onclick=audit("che") '.$mod_disabled_0.'>审核通过</button>';
    $bannerstr.= '<button onclick=audit("unche") '.$mod_disabled_0.' style="width:80px">审核不过</button>'."<br>";
    $bannerstr.= '<button onclick=\'audit("rec")\' '.$mod_disabled_1.' >接收选中</button>';
    $bannerstr.= '<button onclick=audit("del") '.$mod_disabled_0.'>删除选中</button>';
    $bannerstr.= '&nbsp;<button onclick="window.location=\'index.php?action=process_upstock&detail=output\'" '.$mod_disabled_0.' >导出数据</button>';
    $bannerstr.= $showperhtml;


    /*显示备货总成本和备货总利润*/
    if($hadadut){
        $bannerstr.= '<span id="total_cost" style="font-size:14px; color:#ff0000; font-weight: bold;"></span>&nbsp;&nbsp;';
        $bannerstr.= '<span id="gross_profits" style="font-size:14px; color:#ff0000; font-weight: bold;"></span>&nbsp;&nbsp;';
    }

    $jslink = "<script src='./staticment/js/stockproduct.js?version=".time()."'></script>\n";
    $jslink.= "<script src='./staticment/js/pagejump.js'></script>\n";
    $this->V->mark(array('title'=>'备货列表'));
    $temp = 'pub_list';
}

/*导出数据*/
elseif($detail == 'output'){

	/*最后都得加上这个条件，只能罗列未关闭的备货单。*/
    $sqlstr	    .= ' and statu="0" and property ="备货单" ';
    $sqlstr 	 = strtr($sqlstr,array('sku'=>'p.sku','product_name'=>'p.product_name'));

    /*联表作前辍替换*/
    $sqlstr 	 = strtr($sqlstr,array('cuser'=>'p.cuser','cdate'=>'p.cdate'));

    /*在已接收的搜索结果中按接收时间显示。*/
    $orders = $statu == '3'?' order by p.rdate DESC,id DESC':'ORDER BY p.ID DESC';

	$warehouse	 = $this->C->service('warehouse');
	$datalist 	 = $this->S->dao('process')->upstock_list($sqlstr,$orders);

	/*数据处理*/
	foreach($datalist as &$val){

		$val['conditionerp'] 	= $conditionerp_arr[$val['conditionerp']];

        /*将额外信息存进输出数组$datalist*/
        $extends 				= json_decode($val['extends'],true);
        $extendskeys 			= array_keys($extends);
        for($i=0;$i<count($extendskeys);$i++){$val["$extendskeys[$i]"] = $extends["$extendskeys[$i]"];}

        $val['e_lastself'] 		= empty($val['e_lastself'])?'0':$val['e_lastself'];
		$val['e_lastbackrate']	= empty($val['e_lastbackrate'])?'0':$val['e_lastbackrate'];
		$val['e_upc_or_ean']	= empty($val['e_upc_or_ean'])?'0':$val['e_upc_or_ean'];

        $val['e_aprice']    	= number_format($val['e_aprice'],2);
        $val['e_sprice']    	= number_format($val['e_sprice'],2);
        $val['e_rprice']    	= number_format($val['e_rprice'],2);
        $val['profit']      	= number_format($val['e_aprice']/$val['e_rprice'],4) * 100 .'%';
        $val['comment']     	= $val['comment']?$val['comment']:'--';
        $val['e_issuetime']     = $val['e_issuetime'];
        $val['e_account']       = $val['e_account'];
	}

	$filename	 = '备货_'.date('Y-m-d-',time());
	$head_array  = array(
		'fid'			=>'备货单号',
		'e_stockname'	=>'备货名称',
		'sku'			=>'sku',
        'sname'         =>'供货商',
        'e_issuetime'     =>'账期',
        'e_account'       =>'价格',
		'product_name'	=>'产品名称',
		'conditionerp'	=>'产品状态',
		'quantity'		=>'数量',
		'receiver_id'	=>'备货仓库',
		'e_inware'		=>'可发库存',
		'e_inwareching'	=>'采购在途数量',
		'e_instocking'	=>'备货在途数量',
		'e_lastself'	=>'销售历史(两周)',
		'e_futureself'	=>'销售预估(两周)',
		'price'			=>'单个总成本',
		'e_rprice'		=>'销售价格',
		'e_aprice'		=>'单个利润',
		'profit'		=>'利润率',
		'e_sprice'		=>'总利润',
		'e_lastbackrate'=>'退货率',
		'e_upc_or_ean'	=>'MOQ',
		'cuser'        	=>'备货人',
		'cdate'        	=>'备货日期',
		'comment'		=>'备注',
	);
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

/*查看备货详细信息*/
elseif($detail == 'view_order'){

	$process 		= $this->S->dao('process');
	$sqlstr		    = ' and property ="备货单" ';
	$sqlstr	   	   .= (!empty($tag_statu) || $tag_statu =='0')?' and statu="'.$tag_statu.'" ':'';

	/*查下一条的ID(列表下一条即时间较早的，因列表按倒序排)、上一条*/
	$back_next		= $process->D->get_one_sql('select id from process where 1 '.$sqlstr.' and id<'.$id.' order by id desc');
	$back_prev		= $process->D->get_one_sql('select id from process where 1 '.$sqlstr.' and id>'.$id);


	/*页面传值的重新定义sqlstr*/
	$sqlstr 		= ' and p.id='.$id;

	$backdata 		= $process->upstock_list($sqlstr);
	$backdata		= $backdata['0'];

	/*上条下条操作*/
	$backdata['prev_mod'] 		= $back_prev['id']? ' onclick=window.location="index.php?action=process_upstock&detail=view_order&id='.$back_prev['id'].'&tag_statu='.$tag_statu.'" ':'disabled';
	$backdata['next_mod'] 		= $back_next['id']? ' onclick=window.location="index.php?action=process_upstock&detail=view_order&id='.$back_next['id'].'&tag_statu='.$tag_statu.'" ':'disabled';

    /*将额外信息存进输出数组$backdata*/
	$backdata 					= $this->C->service('warehouse')->decodejson($backdata);

	/*数据显示处理*/
	$backdata['e_lastself'] 	= empty($backdata['e_lastself'])?'0':$backdata['e_lastself'];
	$backdata['e_lastbackrate']	= empty($backdata['e_lastbackrate'])?'0':$backdata['e_lastbackrate'];
	$backdata['e_upc_or_ean']	= empty($backdata['e_upc_or_ean'])?'0':$backdata['e_upc_or_ean'];

	/*利润率*/
	$backdata['profit']      	= number_format($backdata['e_aprice']/$backdata['e_rprice'],4) * 100 .'%';

	/*检测审核权限*/
	$hadadut 					= $this->C->service('admin_access')->checkResRight('r_w_stock');
	$backdata['order_adut']		= $hadadut?1:0;

	/*显示审核按钮*/
	if($backdata['order_adut'] && $backdata['statu'] == 0){
		$backdata['adut_statu'] = 1;
	}

	/*进度显示处理--初始化*/
	for($csi = 1; $csi<=5; $csi++){
		$backdata['css_'.$csi.'_n'] = $backdata['css_'.$csi.'_p'] = 'wait';
	}

	/*未审核*/
	if($backdata['statu'] >= 0){
		$backdata['css_1_n'] 	= 'ready';
		$backdata['css_1_p'] 	= 'half';
		$backdata['mod_time1']	= $backdata['cdate'];
		$backdata['mod_user1']	= $backdata['cuser'];
	}

	/*已审核注意除掉不通过的*/
	if($backdata['statu'] >= 1 && $backdata['statu'] != 2){
		$backdata['css_1_p'] 	= 'ready';
		$backdata['css_2_n'] 	= 'ready';
		$backdata['mod_time2']	= $backdata['mdate'];
		$backdata['mod_user2']	= $backdata['muser'];
	}

	/*已接收*/
	if($backdata['statu'] >= 3){
		$backdata['css_2_p'] 	= 'ready';
		$backdata['css_3_n'] 	= 'ready';
		$backdata['mod_time3']	= $backdata['rdate'];
		$backdata['mod_user3']	= $backdata['ruser'];
	}

	/*采购录单*/
	$backmstock = $process->D->get_one_by_field(array('detail_id'=>$backdata['id']),'id,cdate,cuser');
	if($backmstock){
		$backdata['css_3_p'] 	= 'ready';
		$backdata['css_4_p'] 	= 'half';
		$backdata['css_4_n'] 	= 'ready';

		/*显示第一次采购时间与采购者*/
		$backdata['mod_time4']	= $backmstock['cdate'];
		$backdata['mod_user4']	= $backmstock['cuser'];
	}

	/*完成入库，需要存在采购单*/
	$backover	= $process->D->get_allstr(' and fid="'.$backdata['order_id'].'" and property!="进仓单"','','','isover');
	$allover	= '';
	foreach($backover as $chkover){	if($chkover['isover'] == 'N') $allover = '1';	}
	if(empty($allover) && $backmstock){
		$backdata['css_4_p'] = 'ready';
		$backdata['css_5_n'] = 'ready';

		$backoverdata = $process->D->get_allstr(' and fid="'.$backdata['order_id'].'" and property="进仓单"','','','cdate,cuser');

		/*以最后一条入库记录作为完成入库时间*/
		$backdata['mod_time5']	= $backoverdata['0']['cdate'];
		$backdata['mod_user5']	= $backoverdata['0']['cuser'];
	}

	$this->V->set_tpl('adminweb/upstock_detail');
	$this->V->mark(array('title'=>'备货信息','backdata'=>$backdata));
	display();
}

/*反审核*/
elseif($detail == 'setprev'){

	$process = $this->S->dao('process');
    if(!$this->C->service('admin_access')->checkResRight('none','follow',$process->D->get_one(array('id'=>$id),'muser'))){$this->C->ajaxmsg(0);}//权限判断

    $sid = $process->D->update(array('id'=>$id),'statu="0",mdate=cdate,muser=cuser');
    if($sid){$this->C->ajaxmsg(0,0,1);}else{$this->C->ajaxmsg(1,'回退失败');}
}

/*回退页面*/
elseif($detail == 'rollbackpage'){

	$backdata	= $this->S->dao('process')->D->get_one(array('id'=>$id),'ruser,countnum,isover,order_id');

	if(!$this->C->service('admin_access')->checkResRight('none','follow',$backdata['ruser'])){
		$this->C->sendmsg();
	}elseif($backdata['countnum'] != 0){
		$this->C->sendmsg('该备货单已采购，无法回退 !');
	}elseif($backdata['isover'] == 'Y'){
		$this->C->sendmsg('该备货单关闭，无法回退 !');
	}

	/*回退原因*/
	$reasonArr	= array('=请选择=','无现货，货期延长','该产品已停产','价格调整','质量不稳定','其它');
	$reasonstr	= '<select name=reason  class="check_notnull" >';
	foreach($reasonArr as $val){
		$reasonstr.='<option value='.$val.'>'.$val.'</option>';
	}
	$reasonstr.= '</select>';

	/*表单配置*/
	$conform	= array('method'=>'post','action'=>'index.php?action=process_upstock&detail=rollback','width'=>'');
	$colwidth	= array('1'=>'100','2'=>'200','3'=>'200');
	$disinputarr= array();
	$disinputarr['id']		= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['order_id']= array('showname'=>'备货单号','value'=>$backdata['order_id'],'inextra'=>'disabled');
	$disinputarr['reason']	= array('showname'=>'选择原因','datatype'=>'se','datastr'=>$reasonstr);
	$disinputarr['other']	= array('showname'=>'其它原因','datatype'=>'se','datastr'=>'<textarea name=other rows="3" cols="25" ></textarea>','showtips'=>'若选择其它原因，请填写！');

	$this->V->view['title']	= '选择回退原因-备货列表(list)';
	$temp 		= 'pub_edit';

}

/*回退备货单*/
elseif($detail == 'rollback'){

	$fialedUrl	= 'index.php?action=process_upstock&detail=rollbackpage&id='.$id;
	if($reason == '=请选择=' && empty($other)) $this->C->sendmsg('请选择或填写回退原因！',$fialedUrl);

    $process	= $this->S->dao('process');
    $backdata	= $process->D->get_one_by_field(array('id'=>$id),'cuser,ruser,countnum,isover,order_id');

    if($backdata['ruser'] != $_SESSION['eng_name']){$this->C->ajaxmsg(0,'回退失败，只能回退本人接收的备货单 !');}elseif($backdata['countnum'] != 0){$this->C->ajaxmsg(0,'回退失败，该备货单已采购，无法回退 !');}elseif($backdata['isover'] == 'Y'){$this->C->ajaxmsg(0,'回退失败，该备货单关闭，无法回退 !'); }

    $sid		= $this->S->dao('process')->D->update(array('id'=>$id),array('ruser'=>'','rdate'=>'','statu'=>'0'));

    $time		= date('Y-m-d H:i:s');
    $reason		= ($reason == '其它')?'':$reason;
    $reason		= ($other && $reason)?$reason.'，':'';
    $content	= '你好，你申请的备货单('.$backdata['order_id'].')已被'.$SESSION['eng_name'].'回退至“未审核”状态，回退原因：'.$reason.$other.' (--此通知来自系统)';
    $cuid		= $this->S->dao('user')->D->get_one(array('eng_name'=>$backdata['cuser']),'uid');

    $this->S->dao('friend_message')->insert_one_leave_message($cuid, '', 'System', $content, 0, $time, '', $time);
    if($sid){$this->C->success('回退成功，订单已在未审核状态！','index.php?action=process_upstock&detail=list&statu=3&isover=');}
    	else{$this->C->sendmsg('回退失败，请重试！',$fialedUrl);}
}

/*选择仓库后，取出该仓库的已在备货数，采购途数, 仓库可发数*/
elseif($detail == 'get_in_wcbsums'){
    $sqlstr   = ' and temp.sku="'.$sku.'" and temp.wid='.$houseid;
    $back = array();
    $process  = $this->S->dao('process');
    $backdata_instock = $process->get_all_incoming_byskuhouse($sku,$houseid,1);//查库采购在途
    $backdata_upstock = $process->get_upstock_bysku($sku,$houseid);//查库已下备货数量

    if ($checkid != '') {
        $res = $process->get_info_by_id($checkid);
        if ($res['sku'] == $sku && $res['receiver_id'] == $houseid) {
            $backdata_upstock -= $res['quantity'];
        }
    }

    if ($this->C->service('global')->is_other_warehouse($houseid)) {
        /*查库存数(实实在在库存可发数量不包括损益的)*/
        $sku_alias = $this->S->dao('sku_alias');
        $sql = ' and sold_way="Amazon组" and pro_sku ="'.$sku.'" ';
        $sku_code = $sku_alias->select_sku_code($sql);
        foreach ($sku_code as $val) {
            $code_arr[] = $val['sku_code'];
        }

        $length = count($code_arr);
        if ($length > 0) {
            $extends = $this->S->dao('esse')->get_extends_by_id($houseid);
            $amazon_info = $this->S->dao('info_amazon')->get_one_by_id($extends['a_id']);
            $amazon_res_arr = get_amazon_warehouse($amazon_info, $code_arr);
            if (!$amazon_res_arr) {
                 $back['sums']    = '-1';
                $back['instock'] = $backdata_instock;
                $back['upstock'] = $backdata_upstock;
                echo json_encode($back);
                 exit();
            }

            //统计某项sku的总在途库存和总可发库存
            $instockquantity = 0;
            $loadquantity = 0;
            for ($i = 0; $i < $length; $i++) {
                // 可发库存
                $temp = $amazon_res_arr[$sku_code[$i]['sku_code']][1];
                $instockquantity += $temp;

                // 在途库存
                $temp = $amazon_res_arr[$sku_code[$i]['sku_code']][0] - $amazon_res_arr[$sku_code[$i]['sku_code']][1];
                $loadquantity += $temp;
            }
            $backdata_inware['0']['sums'] = $instockquantity;
            $backdata_inware['0']['instock'] = $loadquantity;
        } else {
            $backdata_inware['0']['sums']    = '-1';
        }
    } else {
        $backdata_inware  = $process->get_allw_allsku($sqlstr);//查库存数
    }
    $back['sums']        = $backdata_inware['0']['sums']?$backdata_inware['0']['sums']:'0';
    $back['fbainstock'] = $backdata_inware['0']['instock']?$backdata_inware['0']['instock']:'0';
    $back['instock']     = $backdata_instock;
    $back['upstock']     = $backdata_upstock;

    echo json_encode($back);
    exit();
}

/*添加申请填写资料页面*/
elseif($detail == 'add' || $detail == 'edit') {
    /*JS包含*/
    $jslink = "<script src='./staticment/js/jquery.js'></script>\n";
    $jslink.= "<script src='./staticment/js/stockproduct.js?version=".time()."'></script>\n";
    $jslink.= "<script language='javascript' type='text/javascript' src='./staticment/js/My97DatePicker/WdatePicker.js'></script>";
    if ($detail == 'add') {
    	if(!$this->C->service('admin_access')->checkResRight('r_add_stocks')){$this->C->sendmsg();}//权限判断
        $submit_action = 'index.php?action=process_upstock&detail=addmod';
        $title = '备货申请-备货列表(list)';
    } else {
        $dataresult = $this->S->dao('process')->D->get_one_by_field(array('id'=>$id),'*');
        if(!$this->C->service('admin_access')->checkResRight('r_w_edit_stock','mod',$dataresult['cuser'])){$this->C->sendmsg();}//权限判断
        if($dataresult['statu'] != '0'){$this->C->sendmsg('无法修改<br>只能对未审核订单编辑！');}
        $submit_action = 'index.php?action=process_upstock&detail=editmod';
        $title = '修改申请-备货列表(list)';
        $extends = json_decode($dataresult['extends']);
        foreach ($extends as $key=>$val) {
            $dataresult[$key] = $val;
        }
        $jslink .= '<script type="text/javascript"></script>';
    }
    /*取得仓库下拉--Start*/
    $datalist = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
    $stockstr = '<select id="stockware" disabled="disabled" onChange="get_in_wcbsums(this.value)" name="stockware"><option value="">=请选择仓库=</option>';

    foreach ($datalist as $val){
        $stockstr.= '<option value='.$val['id'].' '.($dataresult['receiver_id'] == $val['id']?'selected':'').' >'.$val['name'].'</option>';
    }
    $stockstr.= '</select>&nbsp;*';

    /*账期信息下拉列表*/
    $sql.= ' and s.pid='.$dataresult['pid'];

    $suacunid = $this->S->dao('supplieraccount')->D->get_one(array('pid'=>$dataresult['pid'],'eid'=>$dataresult['provider_id']),'id');

    $data     = $this->S->dao('supplieraccount')->getupstocklist($sql);

    $sel = '<select id=provider_id name=provider_id><option value="">--请选择--</option>';
    foreach($data as $k=>$v){
        $sel .='<option value="'.$v['id'].'" '.($v['id'] == $suacunid?'selected':'').' >'.$v['esseid'].' | '.$v['issuetime'].' | '.$v['account'].'</option>';
    }
    $sel .='</select>';
    $dataresult['stockstr'] = $stockstr;
    $data['id']             = $sel;
    $dataresult['coincode'] = $this->C->service('warehouse')->get_coincode_rate_html('class="coin_code js_select"', ($dataresult['id'] == ''?'':'USD'));

    $this->V->mark(array('title'=>$title,'submit_action'=>$submit_action,'jslink'=>$jslink, 'dataresult'=>$dataresult,'data'=>$data));

    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L');
    $this->V->set_tpl('adminweb/upstock_add');
    display();
}

/*提交备货申请*/
elseif($detail == 'addmod'){
    date_default_timezone_set('Etc/GMT-8');//北京时间
    $process   = $this->S->dao('process');
    $suaccount = $this->S->dao('supplieraccount');
    $max_b     = $this->S->dao('max_b');

    //账期信息
    $_arr = $suaccount->D->get_one(array('id'=>$provider_id),'eid,issuetime,account');

    /*护展内容*/
    $arraystocks = array("buytime"=>$buytime,"e_issuetime"=>$_arr['issuetime'],"e_account"=>$_arr['account'],"cost2"=>$cost2,"e_stockname"=>$e_stockname,'e_inware'=>$e_inware, 'e_fbainware'=>$e_fbainware, 'e_inwareching'=>$e_inwareching,'e_instocking'=>$e_instocking,'e_lastself'=>$e_lastself,'e_futureself'=>$e_futureself,'e_express'=>$e_express,'e_quantity'=>$e_quantity,'e_aprice'=>$e_aprice,'e_rprice'=>$e_rprice,'e_sprice'=>$e_sprice,'e_lastbackrate'=>$e_lastbackrate,'e_upc_or_ean'=>$e_upc_or_ean);

    //如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
    $extends = get_magic_quotes_gpc()?addslashes(json_encode($arraystocks)):json_encode($arraystocks);

    //获取订单
    $max_id = $max_b->D->insert(array('id'=>''));
    $fid    = 'b'.sprintf("%07d",$max_id);//始订单号

    $cdate = date('Y-m-d H:i:s',time());
    $comment = empty($comment)?'--':$comment;//如果备注为空时，显示'--'，因为点击可编辑需要有内容点击。
    $sid = $process->D->insert(array(
    'receiver_id'=>$stockware,
    'sku'=>$sku,
    'fid'=>$fid,
    'pid'=>$pid,
    'product_name'=>$product_name,
    'price'=>$price,
    'cdate'=>$cdate,'mdate'=>$cdate,
    'muser'=>$_SESSION['eng_name'],
    'cuser'=>$_SESSION['eng_name'],'order_id'=>$fid,
    'property'=>'备货单',
    'extends'=>$extends,'comment'=>$comment,'quantity'=>$e_quantity,
    'provider_id'=>$_arr['eid']));
    if($sid) $this->C->success('添加成功','index.php?action=process_upstock&detail=list');
}

/*检测SKU并调出相关的内容到表单*/
elseif($detail == 'checksku'){
    if(empty($sku)){exit($backdata['pid']);}
    $backdata = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name,upc_or_ean');
    if($backdata){
        /*取得成本价cost2,统一转换成美元*/
        $datacost = $this->S->dao('product_cost')->D->get_one_by_field(array('pid'=>$backdata['pid']),'cost2,coin_code');
        if($datacost['coin_code'] != 'USD'){
            $datacost['cost2'] = $this->C->service('exchange_rate')->change_rate($datacost['coin_code'],'USD',$datacost['cost2']);
        }
        $backdata['cost2']=number_format($datacost['cost2'],2);
    }
    $data = json_encode($backdata);
    echo  $data;
}

/*AJAX删除*/
elseif($detail == 'deletefull'){

	$process	= $this->S->dao('process');
	$delidArr	= explode(',',$strid);
	$success	= 0;
	$errornum	= 0;
	$failed		= 0;

	foreach($delidArr as $val){
		if($this->C->service('admin_access')->checkResRight('r_w_delsto','mod',$process->D->get_one(array('id'=>$val),'cuser'))){
			$sid = $process->D->delete(array('id'=>$val));
			if($sid) {$success++;}else{$errornum++;}
		}else{
			$failed++;
		}
	}

    if(empty($errornum)) {
		$this->C->ajaxmsg(1,'成功删除 '.$success.'条记录，权限不足失败 '.$failed.'条。');
    }else{
	    $this->C->ajaxmsg(0);
    }

}

/*单条删除*/
elseif($detail == 'del'){

    $process = $this->S->dao('process');

    if(!$this->C->service('admin_access')->checkResRight('r_w_delsto','mod',$process->D->get_one(array('id'=>$id),'cuser'))){$this->C->ajaxmsg(0);}//删除权限判断

    $sid = $process->D->delete_by_field(array('id'=>$id));
    if($sid){
        $this->C->ajaxmsg(1,'删除成功');
    }else{
        $this->C->ajaxmsg(0,'删除失败');
    }

}

/*AJAX审核，接收，需要审核或接收权限*/
elseif($detail == 'auditfull'){
    $process = $this->S->dao('process');

    /*审核--1通过，2不通过*/
    if($sign == '1' || $sign == '2'){
        if(!$this->C->service('admin_access')->checkResRight('r_w_stock')){$this->C->ajaxmsg(0);}//审核权限判断

        /*检测选中的纪录是否包含不符合状态的，防止逆向操作*/
        $datalist = $process->D->get_all_sql('select statu from process where id in('.$strid.')');
        foreach ($datalist as $val){
            if($val['statu'] !='0'){$this->C->ajaxmsg(0,'只能操作未审核的记录，请检查状态');}
        }
        $sid = $process->D->update_sql('where id in('.$strid.')',array('statu'=>$sign,'muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time())));
        if($sid){$this->C->ajaxmsg(1,'审核成功');}
    }

    /*标记接收*/
    if($sign == '3'){

        if(!$this->C->service('admin_access')->checkResRight('r_w_rec')){$this->C->ajaxmsg(0);}//接收权限判断

        /*检测选中的纪录是否包含不符合状态的，防止逆向操作*/
        $datalist = $process->D->get_all_sql('select statu from process where id in('.$strid.')');
        foreach ($datalist as $val){
            if($val['statu'] !='1'){$this->C->ajaxmsg(0,'只能操作已审核的记录，请检查状态 !');}
        }

        $mdate = date('Y-m-d H:i:s',time());
        $sid = $process->D->update_sql('where id in('.$strid.')',array('statu'=>$sign,'ruser'=>$_SESSION['eng_name'],'rdate'=>$mdate));
        if($sid){$this->C->ajaxmsg(1,'已接收');}
    }
}

/*审核时修改审核数量*/
elseif($detail == 'upquantity'){
    if(!$this->C->service('admin_access')->checkResRight('r_w_stock')){$this->C->ajaxmsg(0);}//审核权限判断
    $backdata = $this->S->dao('process')->D->get_one_by_field(array('id'=>$id),'statu');//检查是否处于可修改状态
    if($backdata['statu'] !='0'){exit('无法修改，请检查是否属于"未审核"！状态');}//

    if($this->S->dao('process')->D->update_by_field(array('id'=>$id),array('quantity'=>$quantity))){echo '1';}
}

/*审核时修改备注*/
elseif($detail == 'upcomment'){
    if($this->S->dao('process')->D->update_by_field(array('id'=>$id),array('comment'=>$comment))){echo '1';}
}

/*保存修改*/
elseif($detail == 'editmod') {
    date_default_timezone_set('Etc/GMT-8');//北京时间
    $process   = $this->S->dao('process');
    $suaccount = $this->S->dao('supplieraccount');

    //账期信息
    $_arr = $suaccount->D->get_one(array('id'=>$provider_id),'eid,issuetime,account');

    /*护展内容*/
    $arraystocks = array("buytime"=>$buytime,"e_issuetime"=>$_arr['issuetime'],"e_account"=>$_arr['account'],"cost2"=>$cost2,"e_stockname"=>$e_stockname,'e_inware'=>$e_inware, 'e_fbainware'=>$e_fbainware,'e_inwareching'=>$e_inwareching,'e_instocking'=>$e_instocking,'e_lastself'=>$e_lastself,'e_futureself'=>$e_futureself,'e_express'=>$e_express,'e_quantity'=>$e_quantity,'e_aprice'=>$e_aprice,'e_sprice'=>$e_sprice,'e_rprice'=>$e_rprice,'e_lastbackrate'=>$e_lastbackrate,'e_upc_or_ean'=>$e_upc_or_ean);

    //如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
    $extends = get_magic_quotes_gpc()?addslashes(json_encode($arraystocks)):json_encode($arraystocks);

    $cdate = date('Y-m-d H:i:s',time());
    $comment = empty($comment)?'--':$comment;//如果备注为空时，显示'--'，因为点击可编辑需要有内容点击。
    $sid = $process->D->update_by_field(array('id'=>$checkid),array('receiver_id'=>$stockware,'provider_id'=>$_arr['eid'],'sku'=>$sku, 'pid'=>$pid,'product_name'=>$product_name,'price'=>$price,'mdate'=>$cdate,'muser'=>$_SESSION['eng_name'],'extends'=>$extends,'comment'=>$comment,'quantity'=>$e_quantity));
    if($sid){$this->C->success('修改成功','index.php?action=process_upstock&detail=list');}
}

/*返回状态，用于备货未审核点击修改状态*/
elseif($detail == 'get_conditionerp'){
    $conditionerpsel = '<select class="ajax_select_conditionerp">';
    $conditionerpsel .= '<option value=>=请选择=</option>';
    $conditionerpsel .= '<option value="normal" '.($conditionerp==$conditionerp_arr["normal"]?'selected="selected"':'').'>正常</option>';
    $conditionerpsel .= '<option value="emptying" '.($conditionerp==$conditionerp_arr["emptying"]?'selected="selected"':'').'>清库</option>';
    $conditionerpsel .= '<option value="quality" '.($conditionerp==$conditionerp_arr["quality"]?'selected="selected"':'').'>停止销售-质量问题</option>';
    $conditionerpsel .= '<option value="profit" '.($conditionerp==$conditionerp_arr["profit"]?'selected="selected"':'').'>停止销售-低利润</option>';
    $conditionerpsel .= '<option value="tort" '.($conditionerp==$conditionerp_arr["tort"]?'selected="selected"':'').'>停止销售-侵权</option>';
    $conditionerpsel .= '</select>';

    echo $conditionerpsel;
}

/*单独修改shipping*/
elseif($detail == 'update_conditionerp') {
    $process = $this->S->dao('process');
    $product = $this->S->dao('product');
    $res = $process->get_extends_by_id($id);
    $product->D->update_by_field(array('pid'=>$res['pid']), array('conditionerp'=>$conditionerp));
    echo $conditionerp_arr[$conditionerp];
}

/*查询采购与入库情况*/
elseif($detail == 'get_moddetail'){
    $sqlstr         = ' and p1.id='.$id;
    $process        = $this->S->dao('process');
    $backdata       = $process->get_moddetail($sqlstr);
    $return_detail  = '';
    $tipsmsg        = '';
    $return         = '<table cellpadding="3"  cellspacing="0" width=199 style="font-size:12px;color:#000";text-align:center>';

    /*如果已关闭则提醒*/
    if($backdata['0']['isover'] == 'Y'){
        $tipsmsg         = '<div style="margin-top:12px;color:#ccc;border-top:1px dotted #ccc;padding-top:3px">该备货单已关闭，不再采购，如有疑问请联系备货接收人！</div>';
    }

    /*已下采购单*/
    if($backdata['0']['id']){
        foreach($backdata as $val){

            switch ($val['statu']){
                    case '0':$val['status'] = '未审核';break;
                    case '1':$val['status'] = '已审核';break;
                    case '3':$val['status'] = '已下单';break;
            }

            /*如果是已下单并且存在入库，显示已入库数量*/
            if($val['statu'] == '3'){
                $backin         = $process->D->get_one_by_field(array('detail_id'=>$val['id'],'protype'=>'采购'),'sum(quantity) as nums');//指明查采购进仓，否则查出红单负数量
                if($backin['nums']) $val['status']    = '已入库('.$backin['nums'].'pcs)';
            }

            $return_detail.= '<tr><td>采购('.$val['quantity'].'pcs)</td><td>'.$val['status'].'</td></tr>';
        }

        $return.= '<tr><td><b>批次</b></td><td><b>状态</b></td></tr>';
        $return.= $return_detail;
        $return.= '</table>'.$tipsmsg;

    }else{
        $dhowsmg= empty($tipsmsg)?'&nbsp; &nbsp; &nbsp; 未采购，请过段时间再查!':$tipsmsg;
        $return.= '<tr><td valign="middle" width="100%" height="100">'.$dhowsmg.'</td></tr>';
        $return.= '</table>';
    }

    echo $return;
}

/*
 *获取备货申请账期列表
 * @author Jerry
 * @create on 2012-11-05
 */

 elseif ($detail == 'getupstocklist') {

    $sqlstr .= ' and s.pid='.$pid;
    $datalist = $this->S->dao('supplieraccount')->getupstocklist($sqlstr);
    echo json_encode(array('data'=>$datalist));

 }

 /*
  * 备货订单-已接收-供应商账期修改
  * @author by Jerry
  * @create on 2012-11-15
  */

 elseif ($detail =='editsuaccount') {

    $jump = 'index.php?action=process_upstock&detail=editsuaccountmod';
    $this->V->view['title'] = '编辑供应商账期-备货列表(list)';
    /*表单配置*/
    $conform  = array('method'=>'post','action'=>$jump,'width'=>'490');
    $colwidth = array('1'=>'100','2'=>'220','3'=>'80');

    //获取process表的产品pid
    $_arr = $this->S->dao('process')->D->get_one(array('id'=>$id),'pid,provider_id,cuser');

    /**权限判断**/
    if(!$this->C->service('admin_access')->checkResRight('r_w_edit_stock','mod',$_arr['cuser'])){$this->C->sendmsg();}

    /*账期信息下拉列表*/
    $suacunid = $this->S->dao('supplieraccount')->D->get_one(array('pid'=>$_arr['pid'],'eid'=>$_arr['provider_id']),'id');
    $data     = $this->S->dao('supplieraccount')->getupstocklist('and s.pid='.$_arr['pid']);

    $sel = '<select id=provider_id name=provider_id><option value="">--请选择--</option>';
    foreach($data as $k=>$v){
        $sel .='<option value="'.$v['eid'].'" '.($v['id'] == $suacunid?'selected':'').' >'.$v['esseid'].' | '.$v['issuetime'].' | '.$v['account'].'</option>';
    }
    $sel .='</select>';

    $disinputarr = array();
    $disinputarr['id'] = array('showname'=>'编辑id','value'=>$id,'width'=>'490','datatype'=>'h');
    $disinputarr['dd'] = array('showname'=>'供应商账期','width'=>'490','datastr'=>$sel,'datatype'=>'se');
    $temp = 'pub_edit';

    $jslink  = "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
    $jslink = "<script src='./staticment/js/stockproduct.js?version=".time()."'></script>\n";

 }

 /*
  *备货下单-已接收-供应商账期修改处理
  *@author by Jerry
  *@create on 2012-11-15
  */

 elseif ($detail =='editsuaccountmod') {

      //获取process的pid
      $_p              = $this->S->dao('process')->D->get_one(array('id'=>$id),'pid,extends,cuser');

      $_obj = json_decode($_p['extends'],1);

      //根据process的pid和provider_id获取supplieraccount当条数据
      $_arr         = $this->S->dao('supplieraccount')->D->get_one(array('pid'=>$_p['pid'],'eid'=>$provider_id),'issuetime,account');

      $_obj['e_issuetime']  = $_arr['issuetime'];
      $_obj['e_account']    = $_arr['account'];

      //如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。
      $extends = get_magic_quotes_gpc()?addslashes(json_encode($_obj)):json_encode($_obj);

      $_done = $this->S->dao('process')->D->update(array('id'=>$id),array('provider_id'=>$provider_id,'extends'=>$extends));
      if($_done) $this->C->success('修改成功','index.php?action=process_upstock&detail=list&statu=3');
  }


 /* 备货下单-导入表格文件处理
  * @author by Jerry
  * @create on 2012-11-19
  *
  */

  elseif ($detail =='import_e') {

  	if(!$this->C->service('admin_access')->checkResRight('r_add_stocks')){$this->C->sendmsg();}//权限判断

    $upload_dir = './data/uploadexl/temp/';//上传目录
    $fieldarray = array('A','B','C','D','E','F','G','H','I','J','K','L');//有效的excel列表值
	$head 		= 1;//以第一行为表头
    $tablelist = '';

    $process         = $this->S->dao('process');
    $product         = $this->S->dao('product');
    $esse            = $this->S->dao('esse');
    $exchange_rate   = $this->S->dao('exchange_rate');
    $_exchange_rate  = $this->C->service('exchange_rate');
    $_uploadservice  = $this->C->service('upload_excel');
    $suaccount       = $this->S->dao('supplieraccount');
    $product_cost    = $this->S->dao('product_cost');
    $max_b           = $this->S->dao('max_b');

    if ($filepath) {

        $all_arr = $_uploadservice->get_excel_datas_withkey($filepath,$fieldarray,$head);
        unlink($filepath);

        //事务处理备货导入数据
        $process->D->query('begin');
        $_error = 0;
        for($i = 1; $i < count($all_arr); $i++){

            $provider_id = $esse->D->get_one(array('type'=>3,'esseid'=>$all_arr[$i]['供应商']),'id');//供应商provider_id

            $arr         = $product->D->get_one(array('sku'=>$all_arr[$i]['SKU']),'pid,product_name,upc_or_ean');//产品信息

            if($arr){
            /*取得成本价cost2,统一转换成美元*/
                $datacost = $product_cost->D->get_one_by_field(array('pid'=>$arr['pid']),'cost2,coin_code');
                if($datacost['coin_code'] != 'USD'){
                    $datacost['cost2'] = $this->C->service('exchange_rate')->change_rate($datacost['coin_code'],'USD',$datacost['cost2']);
            }
            $arr['cost2']=number_format($datacost['cost2'],2);
            }

            //获取订单
            $max_id = $max_b->D->insert(array('id'=>''));

            if(!$max_id) $_error++;
            $fid    = 'b'.sprintf("%07d",$max_id);//始订单号

            $receiver_id = $esse->D->get_one(array('type'=>2,'name'=>$all_arr[$i]['备货仓库']),'id');//备货仓库

            $su_arr      = $suaccount->D->get_one(array('eid'=>$provider_id,'pid'=>$arr['pid']),'issuetime,account');//账期信息

            $back_enough = $process->get_allw_allsku(' and temp.sku="'.$all_arr[$i]['SKU'].'" and temp.wid='.$receiver_id);//可发库存

            $backdata_upstock = $process->get_upstock_bysku($all_arr[$i]['SKU'],$receiver_id);//查库已下备货数量

            $sell_account = $_exchange_rate->change_rate($all_arr[$i]['售价币别'],'USD',$all_arr[$i]['销售价格']);//销价
            $buy_account  = $_exchange_rate->change_rate($all_arr[$i]['成本币别'],'USD',$all_arr[$i]['单个总成本']);//单个总成本

            //备货数量
            $back_quantity = $all_arr[$i]['备货数量'];

            //单个利润=销价-单个总成本
            $sell_money    = $sell_account - $buy_account;

            //预计总利润 = 单个利润*备货数量
            $sum_money     = $sell_money*$back_quantity;

            //利润率 = 单个利润/售价
            $order_sumsell = $sell_money/$sell_account;

            $_extends      = array(
                'e_issuetime'   =>$su_arr['issuetime'],//账期
                'e_account'     =>$su_arr['account'],//定价
                'e_stockname'   =>$all_arr[$i]['备货名称'],
                'e_inware'      =>$back_enough[0]['sums'], //可发库存
                'e_lastself'    =>$all_arr[$i]['销售历史'],
                'e_futureself'  =>$all_arr[$i]['销售预估'],
                'e_upc_or_ean'  =>$arr['upc_or_ean'],//MQQ
                'e_aprice'      =>$sell_money,//单个利润
                'e_rprice'      =>$sell_account,//销售价格
                'e_sprice'      =>$sum_money,//预计总利润
                'e_instocking'  =>$backdata_upstock,//已下备货单数量
                'cost2'         =>$arr['cost2'],
                'e_quantity'    =>$back_quantity
                );


            //如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
            $extends    = get_magic_quotes_gpc()?addslashes(json_encode($_extends)):json_encode($_extends);

            $id = $process->D->insert(array(
                'provider_id'   =>$provider_id,
                'receiver_id'   =>$receiver_id,
                'sku'           =>$all_arr[$i]['SKU'],
                'fid'           =>$fid,
                'pid'           =>$arr['pid'],
                'product_name'  =>$arr['product_name'],
                'cdate'         =>date('Y-m-d H:i:s'),
                'mdate'         =>date('Y-m-d H:i:s'),
                'muser'         =>$_SESSION['eng_name'],
                'cuser'         =>$_SESSION['eng_name'],
                'property'      =>'备货单',
                'comment'       =>$all_arr[$i]['备注'],
                'order_id'      =>$fid,
                'price'         =>$buy_account,//单个总成本
                'quantity'      =>$back_quantity,
                'extends'       =>$extends,

            ));

            if(!$id) $_error++;

        }

        if(empty($_error)){
            $process->D->query('commit');$this->C->success('备货导入成功!','index.php?action=process_upstock&detail=list');
		}else{
			$process->D->query('rollback');$this->C->success('抱歉：备货导入失败!','index.php?action=process_upstock&detail=list');
        }

    }else{

        //币别
        $coincodeArr = $_uploadservice->upcel_check('exchange_rate','code',' and isnew="1"');

        //仓库
        $roomArr      = $_uploadservice->upcel_check('esse','name','  and type="2" ');

        $data_error = '';
        $all_arr    = $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir,$fieldarray,$head);
        $filepath   = $this->getLibrary('basefuns')->getsession('filepath');

        $tablelist .= '<table id="mytable">';
        $tablelist .= $this->C->service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'process_upstock');

        foreach($all_arr as $key=>$val) {

            $tablelist .= '<tr>';
            foreach($val as $v=>$value){

                $error_style = '';
                if($v =='备货名称' && $value == ''){
                        $error_style = 'bgcolor="red" title="备货名称不可为空！"';
                        $data_error++;
                }

                if($v == 'SKU'){
                    if(empty($value)){
					 	$error_style = '  bgcolor="red" title="SKU不能为空" ';
					 	$data_error++;
					}
                    elseif(!preg_match("/(^(\d)+-\d+-(\d+)$)|(^(\d)+-\d+-\d+-(\w+)$)/",$value)){
						$error_style = ' bgcolor="red" title="SKU格式不对,格式如(236-41-48或者236-41-48-CD001)" ';
						$data_error++;
					}

                    $_pid = $product->D->get_one(array('sku'=>$value),'pid');
                    if(!$_pid){
                        $error_style = 'bgcolor="red" title="该sku不存在"';
                        $data_error++;
                    }
                }

                if($v == '供应商'){
                    $_eid = $esse->D->get_one(array('type'=>3,'esseid'=>$value),'id');
                    if(!$_eid){
                        $error_style = 'bgcolor="red" title="该供应商不存在！"';
                        $data_error++;
                    }else{
                        $_id = $suaccount->D->get_one(array('eid'=>$_eid,'pid'=>$_pid),'id');
                        if(!$_id){
                            $error_style = 'bgcolor="red" title="账期不存在！"';
                            $data_error++;
                        }
                    }

                }

                if($v =='备货数量' && (!preg_match('/^[0-9]{1,9}$/',$value))){

                        $error_style = 'bgcolor="red" title="备货数量错误！"';
                        $data_error++;
                }

                if($v =='备货仓库' && (!in_array($value,$roomArr))){

                        $error_style = 'bgcolor="red" title="备货仓库不存在！"';
                        $data_error++;
                }

                if($v =='单个总成本' && $value == ''){

                        $error_style = 'bgcolor="red" title="单个总成本不可为空！"';
                        $data_error++;
                }

                if($v =='成本币别' && (!in_array($value,$coincodeArr) || empty($value))){

                        $error_style = 'bgcolor="red" title="成本币别错误！"';
                        $data_error++;
                }

                 if($v == '销售价格' && $value == ''){

                         $error_style = 'bgcolor="red" title="销售价格不可为空！"';
                         $data_error++;
                }

                if($v == '售价币别' && (!in_array($value,$coincodeArr) || empty($value))){

                        $error_style = 'bgcolor="red" title="销价币别错误！"';
                        $data_error++;
                }

                if($v == '销售历史' && !preg_match('/^[0-9]{1,9}$/',$value)){

                        $error_style = 'bgcolor="red" title="销售历史必须为数字！"';
                        $data_error++;
                }

                if($v == '销售预估'  && !preg_match('/^[0-9]{1,9}$/',$value)){

                        $error_style = 'bgcolor="red" title="销售预估必须为数字！"';
                        $data_error++;
                }

                $tablelist .= '<td '.$error_style.'>&nbsp;'.$value.'</td>';
            }

            $tablelist .= '</tr>';
        }
        $tablelist .= '</table>';

        if (!$data_error && isset($all_arr)){
            $tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'"/>';
            $tablelist .= '<input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=process_upstock&detail=list">';
            }elseif ($data_error){
                $exl_error_msg = '<font color="red" size="-1" >总共有'.$data_error.'处错误,请修正后重新上传(鼠标移到红色处可查看错误原因)</font>';
                unlink($filepath);
        }
    }
    $submit_action = 'index.php?action=process_upstock&detail=import_e';
	$temlate_exlurl = 'data/uploadexl/sample/order_upstock.xls';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('exl_error_msg'=>$exl_error_msg,'exl_error_width'=>600,'title'=>'导入订单-备货列表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();

  }

/*模板定义*/
if($detail =='list' || $detail == 'add' || $detail == 'edit' || $detail =='editsuaccount' || $detail =='import_excel' || $detail == 'rollbackpage'){
    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L');
}
?>
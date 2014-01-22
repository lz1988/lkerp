<?php
/*
 * Created on 2012-1-18
 *
 *@title 出库明细表
 *
 *@author by hanson
 */
 if($detail == 'list'){
    /*搜索选项*/
	$stypemu = array(
		'sku-s-l'=>'&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;SKU：',
		'order_id-s-e'=>'&nbsp; &nbsp; 单号：',
		'receiver_id-a-e'=>'&nbsp; &nbsp; &nbsp; &nbsp; 仓库：',
		'rece_type-a-e'=>'类型：',
		'fid-s-e'=>'<br>&nbsp;运单编号：',
		'cuser-s-l'=>'制单人：',
		'rdate-t-t'=>'入库日期：',
	);
	$sqlstr = str_replace('cuser','p.cuser',$sqlstr);
    /*搜索条件变更处理*/
	if($sqlstr && !empty($rece_type)){
        $linksql = ' and active="1" and input="1" ';
		switch($rece_type){
			case 'cr':$sqlstr = str_replace('and rece_type="cr"','and property="进仓单" and protype="采购" '.$linksql,$sqlstr);break;
			case 'tr':$sqlstr = str_replace('and rece_type="tr"','and protype="退货" '.$linksql,$sqlstr);break;
			case 'hr':$sqlstr = str_replace('and rece_type="hr"','and property="转仓单" '.$linksql,$sqlstr);break;
			case 'er':$sqlstr = str_replace('and rece_type="er"','and property="进仓单" and protype="其它" '.$linksql,$sqlstr);break;
		}
	}

    if (empty($rece_type)) {
        $sqlstr.= ' and (property="进仓单" or (property="转仓单" and input="1"))';
    }

    $rece_typearr = array(''=>'=请选择=','cr'=>'采购入库','hr'=>'转仓入库','tr'=>'退货入库','er'=>'其他入库');
	$orders = ' order by p.rdate desc,p.id desc';//按倒序排列

    /*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	$receiver_idarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$receiver_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

	$InitPHP_conf['pageval'] = 15;
	$datalist = $this->S->dao('process')->showneedrec($sqlstr,$orders,1);

    /*数据处理*/
	for($i=0;$i<count($datalist);$i++){


		/*需要另外定义orderidd(默认等order_id,重复才置空,多条备货一次采购只显示一个采购单号)不能改变原有的order_id,影响下一个($i-1)的判断*/
		if($datalist[$i]['order_id'] == $datalist[$i-1]['order_id']){
			$datalist[$i]['orderidd'] = '';
		}else{
			$datalist[$i]['orderidd'] = $datalist[$i]['order_id'];
		}

		/*如果是其它入库单，显示删除*/
		if(substr($datalist[$i]['order_id'],0,1) == 'e'){
			$datalist[$i]['dele'] = '<a href=javascript:void(0);delitem("index.php?action=process_instock&detail=delextra&order_id='.$datalist[$i]['order_id'].'")  title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
		}
		/*如果是采购单，显示关闭按钮*/
		elseif($datalist[$i]['property']=='采购单' && $datalist[$i]['order_id']!=$datalist[$i-1]['order_id']){
			$datalist[$i]['dele'] =  '<a href=javascript:void(0);delitem("index.php?action=process_recstock&detail=closeorder&order_id='.$datalist[$i]['order_id'].'")  title=手动关闭该采购单>&times;</a>';
		}
		else{
			$datalist[$i]['dele'] = '<font color=#c6a8c6>--</font>';
		}

		if($datalist[$i]['warehouse'] == ''){$datalist[$i]['warehouse'] ='不良品仓';}
		if(($datalist[$i]['property'] == '转仓单' || $datalist[$i]['protype'] == '退货')){
			$datalist[$i]['cuser'] = $datalist[$i]['muser'];
		}
	}
    $rperson = '入库者';
	$ranum 	 = '数量';
	$rcdate  = '入库日期';
	$chuser  = 'ruser';
	$showarr_date= 'rdate';
	$chanorderid = '入库单号';
    
    //统计列表总数量
    $_quantity = 0;
    foreach($datalist as $data){
        $_quantity  += $data['quantity'];
    }
    $datalist[] = array('orderidd'=>'<font color="red">总数量</font>','quantity'=>'<font color="red">'.$_quantity.'</font>');
    
    /*定义输出数组*/
	$displayarr = array();
	$tablewidth = '1100';

	$displayarr['order_id'] 	= array('showname'=>'checkbox','title'=>'全选','width'=>'50');
	$displayarr['orderidd'] 	= array('showname'=>$chanorderid,'width'=>'80','title'=>'单号前辍说明：f转仓单，e其它入库，w采购入库');
	if($rece_type == 'tr' || $rece_type == 'hr'|| $receiver_id == '7' || $receiver_id == '9') $displayarr['fid'] 			= array('showname'=>'第三方单号','width'=>'100');
	$displayarr['sku'] 			= array('showname'=>'产品SKU','width'=>'95');
	$displayarr['jin_sku']		= array('showname'=>'金碟SKU','width'=>'95');
	$displayarr['product_name'] = array('showname'=>'产品名称','width'=>'150');
	$displayarr['warehouse']	= array('showname'=>'接收仓库','width'=>'110');
	$displayarr['quantity'] 	= array('showname'=>$ranum,'width'=>'50');
	$displayarr[$chuser] 		= array('showname'=>$rperson,'width'=>'70');
	$displayarr[$showarr_date] 	= array('showname'=>$rcdate,'width'=>'90');
    //其他入库显示备注
    if ($rece_type == 'er')
        $displayarr['comment']  = array('showname'=>'备注','width'=>'120');
	$displayarr['dele'] 		= array('showname'=>'操作','width'=>'50');

	$jslink = "<script src='./staticment/js/process_recstock.js'></script>\n";
	$bannerstrarr[] = array('url'=>'index.php?action=process_instock&detail=outport','value'=>'导出数据');
 	$this->V->mark(array('title'=>'入库明细'));
 	$temp = 'pub_list';
 }

 /*删除其它入库*/
 elseif($detail == 'delextra'){

	/*删除权限判断*/
 	if(!$this->C->service('admin_access')->checkResRight('r_w_deloutorder')){$this->C->ajaxmsg(0,$msg);}

	$sid = $this->S->dao('process')->D->delete_by_field(array('order_id'=>$order_id));
	if($sid){$this->C->ajaxmsg(1);}else{exit('删除失败!');}
 }

 /*导出数据*/
 elseif($detail == 'outport'){

    /*搜索条件变更处理*/
	$sqlstr = str_replace('cuser','p.cuser',$sqlstr);
	if($sqlstr && !empty($rece_type)){
        $linksql = ' and active="1" and input="1" ';
		switch($rece_type){
			case 'cr':$sqlstr = str_replace('and rece_type="cr"','and property="进仓单" and protype="采购" '.$linksql,$sqlstr);break;
			case 'tr':$sqlstr = str_replace('and rece_type="tr"','and protype="退货" '.$linksql,$sqlstr);break;
			case 'hr':$sqlstr = str_replace('and rece_type="hr"','and property="转仓单" '.$linksql,$sqlstr);break;
			case 'er':$sqlstr = str_replace('and rece_type="er"','and property="进仓单" and protype="其它" '.$linksql,$sqlstr);break;
		}
	}

    if (empty($rece_type)) {
        $sqlstr.= ' and (property="进仓单" or (property="转仓单" and input="1"))';
    }

    $orders = ' order by p.rdate desc,p.id desc';//按倒序排列

 	/*查询数据*/
 	$datalist 	= $this->S->dao('process')->showneedrec($sqlstr,$orders,2);
    for($i = 0;$i<count($datalist);$i++){
        $datalist[$i]['dept']       = '公司'; 
        $datalist[$i]['unit']       = 'pc';
        $datalist[$i]['cdate']      = date('Y-m-d',strtotime($datalist[$i]['cdate']));
        $datalist[$i]['erpname']    = '';//物料名称
        $datalist[$i]['weight']     = '';//规格型号
        
        $datalist[$i]['type']       = '同价调拨';
        $datalist[$i]['mdate']      = date('Y-m-d',strtotime($datalist[$i]['mdate']));//制单日期
        $datalist[$i]['price22']    = $datalist[$i]['price2'];//单价
        
        if(in_array($rece_type,array('er','tr'))){
            $datalist[$i]['sold_way'] = '';
        }
        
        if(in_array($rece_type,array('er','hr','tr')))
            $datalist[$i]['sku']    = preg_replace('/-/','.',$datalist[$i]['sku'],1);
    }
 	$filename 	= 'instock_'.date('Y-m-d',time());
    if ($rece_type == 'er'){
        $head_array = array('order_id'=>'单据编号','cdate'=>'单据日期','dept'=>'部门','sname'=>'供应商','sold_way'=>'客户','sku'=>'物料代码','erpname'=>'物料名称','weight'=>'规格型号','unit'=>'单位','cost1'=>'单价','quantity'=>'实收数量','warehouse'=>'接收仓库','comment'=>'备注');
    }else if($rece_type == 'hr'){//转仓入库
        $head_array = array('order_id'=>'单据编号','mdate'=>'单据日期','type'=>'调拨类型','sku'=>'物料代码','erpname'=>'物料名称','weight'=>'规格型号','unit'=>'单位','sname'=>'调出仓库','warehouse'=>'调入仓库','price2'=>'调出单价','price22'=>'调入单价','quantity'=>'数量');
    }else if($rece_type == 'tr'){//退货入库
        $head_array = array('order_id'=>'单据编号','cdate'=>'单据日期','dept'=>'部门','sname'=>'供应商','sold_way'=>'客户','sku'=>'物料代码','erpname'=>'物料名称','weight'=>'规格型号','unit'=>'单位','cost1'=>'单价','quantity'=>'实收数量','warehouse'=>'收料仓库');
    }else{
        $head_array = array('order_id'=>'入库单号','sku'=>'sku','jin_sku'=>'金碟sku','product_name'=>'产品名称','warehouse'=>'接收仓库','quantity'=>'数量','ruser'=>'入库者','rdate'=>'时间');
    } 
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);

 }

 /*模板定义*/
 if($detail == 'list'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>

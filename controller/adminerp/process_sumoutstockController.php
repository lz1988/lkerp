<?php

/**
 * 出库汇总
 * @author  by Jerry
 * @create on 2012-10-31
 */

/*出库汇总列表页*/
if ($detail == "list")
{

    /*搜索选项*/
    $stypemu = array(
        'sku-s-l' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SKU：',
        'product_name-s-l' => '产品名称：',
        'cuser-s-l' => '&nbsp;&nbsp;&nbsp;制单人：',
        'provider_id-a-e' => '发货仓库：',
        'mdate-t-t' => '<br>出库时间：',
        'sold_way-a-e'=>'销售渠道：',
        'rece_type-a-e'=>'出库类型：',
        );

    /*取得发货仓库下拉列表*/
    $wdata = $this->S->dao('esse')->D->get_all(array('type' => 2), 'id', 'desc','id,name');
    
    /*显示出库类型*/
	$rece_typearr = array(''=>'=请选择=','xr'=>'销售下单出库','fr'=>'物料调拨出库','tr'=>'其它出库');

	if(!empty($rece_type)){
		switch($rece_type){
			case 'xr':$sqlstr = str_replace('and rece_type="xr"','and (p.protype="售出" or p.protype="重发")',$sqlstr);break;
			case 'fr':$sqlstr = str_replace('and rece_type="fr"','and p.property="转仓单"',$sqlstr);break;
			case 'tr':$sqlstr = str_replace('and rece_type="tr"','and p.property="出仓单" and protype="其它"',$sqlstr);break;
	
    }
    }
    
    /*生成渠道搜索*/
	$sold_wayarr = $this->C->service('global')->get_sold_way(0,'sold_way','wayname');

    /*初始打开默认中国仓库*/
	if(empty($sqlstr)){$provider_id = $this->C->service('global')->sys_settings('listshipment_house','sys');}
    $provider_idarr = array('=' => '=请选择=');
    for ($i = 0; $i < count($wdata); $i++)
    {
        $provider_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
    }

	if($sqlstr){

	    /*出库汇总分页结果集*/
	    $InitPHP_conf['pageval'] = 20;
	    //$sqlstr.= ' and (p.property="出仓单" or p.property="转仓单")';
        $sqlstr = str_replace("sold_way","p.sold_way",$sqlstr);
	    $datalist  = $this->S->dao('process')->showsumoutstocklist($sqlstr);

	    $_quantity = 0;
	    foreach($datalist as $data){
	        $_quantity  += $data['quantity'];
	    }

	    $datalist[] = array('sku'=>'<font color="red">总数量</font>','jin_sku'=>'<font color="red">'.$_quantity.'</font>');
	    $bannerstrarr[] = array('url'=>'index.php?action=process_sumoutstock&detail=outportsumoutstock', 'value'=>'导出汇总表', 'class'=>'six');
	}
    /*输出表单列表头*/
    $displayarr = array();
    $tablewidth = '1100';

    $displayarr['sku']          = array('showname' => '产品SKU', 'width' => '80');
    $displayarr['jin_sku']      = array('showname' => '金蝶SKU', 'width' => '80');
    $displayarr['product_name'] = array('showname' => '产品名称', 'width' => '300');
    $displayarr['quantity']     = array('showname' => '数量', 'width' => '50');


    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
    $this->V->mark(array('title' => '出库汇总'));
    $temp = 'pub_list';

}
/**
 * @出库汇总导出
 * @author Jerry
 * @create on 2012-11-01
 */

elseif ($detail == "outportsumoutstock") {
    
    if(!empty($rece_type)){
		switch($rece_type){
			case 'xr':$sqlstr = str_replace('and rece_type="xr"','and (p.protype="售出" or p.protype="重发")',$sqlstr);break;
			case 'fr':$sqlstr = str_replace('and rece_type="fr"','and p.property="转仓单"',$sqlstr);break;
			case 'tr':$sqlstr = str_replace('and rece_type="tr"','and p.property="出仓单" and protype="其它"',$sqlstr);break;
	
    }
    }
    
    $datalist  = $this->S->dao('process')->showsumoutstocklist($sqlstr);
    $filename  = 'sumstock_'.date('Y-m-d');

   	$_quantity = 0;
    foreach($datalist as $data){
        $_quantity  += $data['quantity'];
    }

    $datalist[] = array('product_name'=>'<font color="red">总数量</font>','quantity'=>'<font color="red">'.$_quantity.'</font>');

    $head_array = array('sku'=>'产品sku', 'jin_sku'=>'金蝶sku', 'product_name'=>'产品名称', 'quantity'=>'数量');
    $this->C->service('upload_excel')->download_excel($filename, $head_array, $datalist);

}

?>
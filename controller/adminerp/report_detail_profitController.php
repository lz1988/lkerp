<?php
/**
 +-----------------------------------------------
 * @title 利润明细报表
 * @author Jerry
 * @create on 2014-1-27
 +-----------------------------------------------
 */
 if ($detail == 'list'){
    $stypemu = array(
        'brand-a-e'        => '&nbsp;&nbsp;品&nbsp;&nbsp;&nbsp;牌：',
        'item_id-a-e'      => '&nbsp;&nbsp;项&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;目：',
        'profit_date-b-'   => '&nbsp;期&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;间：',
		'account_id-a-e'   => '<br>&nbsp;&nbsp;账&nbsp;&nbsp;&nbsp;号：',
        'fstcreate-t-t'    => '&nbsp;创建时间：',
    );
   	
    $sqlstr = str_replace('fstcreate','profit.fstcreate',$sqlstr);
    
    $profit_datestr  = "<input type=text name=profit_datea class=twodate onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$profit_datea." >";
    $profit_datestr .= "&nbsp;-&nbsp;<input type=text name=profit_dateb  class=twodate onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$profit_dateb." >";
    
    if ($profit_datea) $sqlstr.= ' and date_format(profit_date,"%Y-%m") >="'.$profit_datea.'"';
    if ($profit_dateb) $sqlstr.= ' and date_format(profit_date,"%Y-%m") <="'.$profit_dateb.'"';
    
    //品牌
    $brandarr = array(''=>'-请选择-','LOFTEK'=>'LOFTEK','MelodySusie'=>'MelodySusie','Miu Color'=>'Miu Color','中性'=>'中性','前期不用帐号'=>'前期不用帐号','国内MIU'=>'国内MIU','其他收入'=>'其他收入');
    
    /*取得销售账号下拉*/
    $type_account = $this->S->dao('type_account')->D->get_allstr('','','','id,account_code');
    $account_idarr	 = array(''=>'=请选择=');
    for($i = 0; $i < count($type_account); $i++){
        $account_idarr[$type_account[$i]['id']] = $type_account[$i]['account_code'];
    }
    
    /*取得项目下拉*/
    $itemdata = $this->S->dao('item')->D->get_allstr('','','','id,item_name');
    $item_idarr	 = array(''=>'=请选择=');
    for($i = 0; $i < count($itemdata); $i++){
        $item_idarr[$itemdata[$i]['id']] = $itemdata[$i]['item_name'];
    }
    
    
    $InitPHP_conf['pageval'] = 15; //分页数
    $datalist 	= $this->S->dao('profit')->get_detail_list($sqlstr);
    for($i =0; $i < count($datalist); $i++){
        $datalist[$i]['profit_date'] = date('Y-m',strtotime($datalist[$i]['profit_date']));
    }
    
    $displayarr = array();
    $tablewith  = '900';
    $displayarr['item_name']      = array('showname'=>'项目名称',  'width'=>'60');
    $displayarr['account_code']   = array('showname'=>'账号', 'width'=>'100');
   	$displayarr['profit_date']    = array('showname'=>'期间', 'width'=>'100');
    $displayarr['costs']          = array('showname'=>'金额','width'=>'100');
    $displayarr['both']           = array('showname'=>'操作', 'ajax'=>1, 'width'=>'60', 'url_d'=>'index.php?action=report_detail_profit&detail=del&id={id}', 'url_e'=>'index.php?action=report_detail_profit&detail=update&id={id}');
    //$bannerstr 		= '<button class="six" onclick="window.location=\'index.php?action=warehouseshipping&detail=add\'">录入运费明细</button>';
    $bannerstr 	= '<button class="six" onclick="add_detail_profit()">导入利润明细</button>';
    
    $jslink = "<script language='javascript' type='text/javascript' src='./staticment/js/report_detail_profitmod.js'></script>";
    $this->V->mark(array('title'=>'利润明细列表'));
    $temp = 'pub_list';
 }
 
 /**
  *@title 导入利润明细报表 
  *@author Jerry 
  *@create on 2014-1-27
  */ 
 elseif ($detail =='insert') {
    
    if (!$profit_datea || !$profit_dateb || ($profit_datea != $profit_dateb)){
        $this->C->success("请选择起始和结束期间","index.php?action=report_detail_profit&detail=list");
    }
    
   	/*上传文件保存路径的目录*/
	$upload_dir = "./data/uploadexl/";
	//$fieldarray = array('A','B','C','D','E','F','G','H','I','J');//有效的excel列表值
	$head 		= 1;//以第一行为表头
	$tablelist 	= '';
    
    $profit     = $this->S->dao('profit');
    $global     = $this->C->service('global');
    $item       = $this->S->dao('item');
    $type_account= $this->S->dao('type_account');
    
    $type_account_data = $type_account->D->get_all(array('brand'=>$brand),'','','account_code');
    for($i=0;$i<count($type_account_data);$i++){
        $type_account_dataarr[] = $type_account_data[$i]['account_code'];
    }
        
    $n = 'A';
    for($j = 0; $j <= count($type_account_dataarr); $j++){
        $fieldarray[] = $n++; 
    }

	/*读取已经上传的文件*/
	if($filepath){
        
		$all_arr 	=	$this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);
		unlink($filepath);
		$error_dd	= 0;
        //$n          = 0;
        $success_dd = 0;
    
		$profit->D->query('begin');
        unset($all_arr[0]);
        foreach($all_arr as $key=>$valarr){
            foreach($valarr as $k=>$val){
               
                if ($valarr['项目']){
                    $item_arr           = $item->D->get_one_by_field(array('item_name'=>$valarr['项目']),'id'); 
                }
                if ($k != '项目'){
                    $type_account_id    = $type_account->D->get_one_by_field(array('brand'=>$brand,'account_code'=>$k),'id');
                    $check_profit_count = $profit->D->get_one_by_field(array('item_id'=>$item_arr['id'],'account_id'=>$type_account_id['id'],'profit_date'=>date('Y-m-d H:i:s',strtotime($profit_date))),'id');
                    //echo'<pre>';print_r($check_profit_count);
                    if ($check_profit_count){
                        $sid                = $profit->D->update_by_field(array('id'=>$check_profit_count['id']),array('item_id'=>$item_arr['id'],'account_id'=>$type_account_id['id'],'costs'=>sprintf ("%01.2f",$val),'profit_date'=>date('Y-m-d H:i:s',strtotime($profit_date))));
                        //$n++;
                    }else{
                        $sid                = $profit->D->insert(array('item_id'=>$item_arr['id'],'account_id'=>$type_account_id['id'],'costs'=>sprintf ("%01.2f",$val),'profit_date'=>date('Y-m-d H:i:s',strtotime($profit_date))));
                        if(!$sid) $error_dd++;else $success_dd++;
                    }
                }
            }
        }
        
       
		if(empty($error_dd)){
			$profit->D->query('commit');$this->C->success('操作成功','index.php?action=report_detail_profit&detail=list');
		}else{
			$profit->D->query('rollback');$this->C->success('添加失败,请稍候重试!','index.php?action=report_detail_profit&detail=list');
		}
	}

	/*上传文件*/
	else{
		$data_error		= '';
		$all_arr		=  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath		=  $this->getLibrary('basefuns')->getsession('filepath');
		$tablelist	   .= '<table id="mytable">';
        array_unshift($type_account_dataarr,'项目');
        
		/*表头特殊显示处理*/
		$tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,$type_account_dataarr);
        
		/*数据显示*/
		foreach($all_arr as $k=>$val){
			$exl_row++;
			$tablelist .= '<tr>';

			foreach( $val as $j=>$value) {
				$error_style = '';
    
                //检测仓库是否存在
				if($j == '项目'){
				    $dataid = $item->D->get_one_by_field(array('item_name'=>$value),'id');
					if(!$dataid){
						$error_style = ' bgcolor="red" title="不存在的项目名称!" ';
						$data_error++;
					}
				}
               
                //检测金额
                if ($j != '项目'){
                    if (!preg_match('/^\-?[\d]+(\.?[\d]+)?$/',$value)){
                       $error_style = 'bgcolor="red" title="金额必须为数字"';
                       $data_error++;
                    }
                }
                
				$tablelist .= '<td '.$error_style.'>&nbsp;'.$value.'</td>';
			}
			$tablelist .= '</tr>';
		}

		$tablelist.= '</table>';

		/*错误判断*/
		if(!$data_error && isset($all_arr)){
			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="hidden" name="profit_date" value="'.$profit_datea.'"/><input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=report_detail_profit&detail=list">';
		}elseif($data_error){
			$exl_error_msg .= '<font color="#577dc6" size="-1">总共有'.$data_error.'处错误，请修正后重新上传（鼠标移到红色处可查看错误原因）。</font>';
			unlink($filepath);//有错的文件删除掉
		}
    }

    $temlate_exlurl = 'index.php?action=report_detail_profit&detail=download_report_detail&brand='.$brand.'';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('exl_error_msg'=>$exl_error_msg,'exl_error_width'=>500,'title'=>'导入利润明细-利润明细列表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}

elseif ($detail == 'download_report_detail'){
    
    $type_account   = $this->S->dao('type_account')->D->get_allstr(' and brand="'.$brand.'"','','id asc','account_code');
   	$head_array     = array('项目'=>'项目');
    for($i = 0; $i < count($type_account); $i++){
        $head_array[$type_account[$i]['account_code']] = $type_account[$i]['account_code'];
    }

    $datalist = array();
    $filename = $brand.'_report_detail';
	$this->C->service('upload_excel')->download_xls($filename,$head_array,$datalist);
}

/**
 *@title 删除利润明细 
 *@author Jerry 
 *@create on 2014-2-12 
 */
elseif ($detail =='del') {
    if($id) {
        if($this->S->dao('profit')->D->delete_by_field(array('id'=>$id)));
            $this->C->ajaxmsg(1);
    }

 }
 /**
 * @添加或者编辑利润明细报表
 * @author by Jerry
 * @create on 2014-2-12
 */

elseif($detail == 'update' || $detail == 'add'){

	if($detail == 'update'){
		if(empty($id))exit('没有ID!');
        
		$profit  	= $this->S->dao('profit');
		$data	    = $profit->D->get_one_by_field(array('id'=>$id),'*');
        $checktimestr = "<input type=text name=profit_date class='find-T check_notnull' value=".$data['profit_date']." onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' >";
		$this->V->view['title'] = '编辑利润明细-利润明细列表(list)';
		$jump = 'index.php?action=report_detail_profit&detail=updatemod';
	}elseif($detail == 'add'){
		$this->V->view['title'] = '添加仓库运费-仓库运费列表(list)';
		$jump 		= 'index.php?action=warehouseshipping&detail=insertmod';
        $checktimestr = "<input type=text name=checktime class='find-T check_notnull'  onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' >";
	}
    
    /*取得项目下拉*/
	$itemdata = $this->S->dao('item')->D->get_all('','','','id,item_name');
	for($i=0;$i<count($itemdata);$i++){
		$itemarr[$itemdata[$i]['id']] = $itemdata[$i]['item_name'];
	}
    
    /*取得账号下拉*/
	$type_accountdata = $this->S->dao('type_account')->D->get_all('','','','id,account_code');
	for($i=0;$i<count($type_accountdata);$i++){
		$type_accountarr[$type_accountdata[$i]['id']] = $type_accountdata[$i]['account_code'];
	}
    
	/*表单配置*/
	$conform = Array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = Array('1'=>'100','2'=>'220','3'=>'80');
    
    //项目列表
    $itemselstr .= '<select name="item_id"id="item_id">';
    foreach ($itemarr as $k=>$v){
        $itemselstr .='<option value="'.$k.'"';
        if($k == $data['item_id'])$itemselstr .= 'selected="selected"';$itemselstr .='>'.$v.'</option>';
    }
    $itemselstr .= '</select>';
    
    //账号列表
    $selstr .= '<select name="account_id"id="account_id">';
    foreach ($type_accountarr as $k=>$v){
        $selstr .='<option value="'.$k.'"';
        if($k == $data['account_id'])$selstr .= 'selected="selected"';$selstr .='>'.$v.'</option>';
    }
    $selstr .= '</select>';

	$disinputarr = Array();
	$disinputarr['id'] 	       = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['item_id']    = array('showname'=>'项目','value'=>$itemarr[$data['item_id']],'extra'=>'*','datatype'=>'se','datastr'=>$itemselstr);
    $disinputarr['account_id'] = array('showname'=>'账号','value'=>$account_idarr[$data['account_id']],'extra'=>'*','datatype'=>'se','datastr'=>$selstr);
	$disinputarr['costs']      = array('showname'=>'运费','value'=>$data['costs'],'extra'=>'*','inextra'=>'class="check_notnull Check_isnum_dd2"');
	$disinputarr['profit_date']= array('showname'=>'期间','extra'=>'*','datatype'=>'se','datastr'=>$checktimestr);
	$temp = 'pub_edit';

    $jslink  = "<script src='./staticment/js/jquery.js'></script>\n";
    $jslink .= "<script src='./staticment/js/commoncheck.js?version=2'></script>";
    $jslink .= "<script type='text/javascript' src='./staticment/js/My97DatePicker/WdatePicker.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
}

/**
 * 新增仓库运费
 * @author Jerry
 * @create on 2012-11-05
 */

elseif ($detail =='insertmod') {
    
    $warehouseshipping = $this->S->dao('warehouseshipping');
    $countid = $warehouseshipping->D->get_count(array('warehouse'=>$warehouse,'checktime'=>$checktime));
    if (!$countid){
	   $wid = $warehouseshipping->D->insert(array('warehouse'=>$warehouse,'checktime'=>$checktime,'shipping'=>sprintf("%01.2f",$shipping)));
	   if($wid)$this->C->success('添加成功','index.php?action=warehouseshipping&detail=list');
    }else{
        $this->C->success('该仓库对应日期运费已存在','index.php?action=warehouseshipping&detail=list');
    }
}

/**
 * 编辑利润报表
 * @author Jerry
 * @create on 2014-2-12
 */
elseif ($detail == 'updatemod') {
    
    $profit = $this->S->dao('profit');
    //die($profit_date);
    $countid = $profit->D->get_count(array('item_id'=>$item_id,'account_id'=>$account_id,'profit_date'=>$profit_date));
    if (!$countid){
        $sid = $this->S->dao('profit')->D->update_by_field(array('id'=>$id),array('item_id'=>$item_id,'account_id'=>$account_id,'profit_date'=>date('Y-m-01',strtotime($profit_date)),'costs'=>sprintf("%01.2f",$costs)));
        if($sid) $this->C->success('修改成功','index.php?action=report_detail_profit&detail=list');
    }else{
        $sid = $this->S->dao('profit')->D->update_by_field(array('id'=>$id),array('item_id'=>$item_id,'account_id'=>$account_id,'profit_date'=>date('Y-m-01',strtotime($profit_date)),'costs'=>sprintf("%01.2f",$costs)));
        if($sid) $this->C->success('覆盖数据成功','index.php?action=report_detail_profit&detail=list');
    }
}

/*模板输出*/
 if($detail == 'list' ||$detail =='update' ||$detail =='insert' || $detail == 'add'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>
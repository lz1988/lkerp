<?php
/**
 +-----------------------------------------------
 * @title 利润报表（账号）
 * @author Jerry
 * @create on 2014-2-17
 +-----------------------------------------------
 */
 if ($detail == 'list'){
    $stypemu = array(
        'profit_date-b-'   => '&nbsp;期&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;间：',
    );
   	
    $profit_datestr  = "<input type=text name=profit_datea class=twodate onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$profit_datea." >";
    $type_account = array();
    
    if (!empty($profit_datea)){
        $sqlstr = str_replace('account_id','type_account.id',$sqlstr);
        
        $type_account = $this->S->dao('type_account')->D->get_allstr('','','brand','id,account_code,brand');
        
        if ($profit_datea) $sqlstr.= ' and date_format(profit_date,"%Y-%m") ="'.$profit_datea.'"';
       
        foreach($type_account as $val){
            $sql .= 'SUM(CASE WHEN account_code="'.$val['account_code'].'" THEN costs  END) AS "'.$val['account_code'].'",';
        }
        $sql = trim($sql,',');
       
        $datalist 	= $this->S->dao('profit')->get_report_profit_by_account($sqlstr,$sql);
        
        if (is_array($datalist) && count($datalist) > 0){
            $bannerstrarr[] = array('url'=>'index.php?action=report_profit_by_account&detail=outport&profit_datea='.$profit_datea,'value'=>'导出数据');
        }
    }
    
    $displayarr = array();
    $displayarr['item_name']    = array('showname'=>'项目',  'width'=>'120');
    $displayarr['sumcosts']     = array('showname'=>'汇总(USD)', 'width'=>'100');
    
    for($j = 0; $j < count($type_account); $j++){
        $displayarr[$type_account[$j]['account_code']] = array('showname'=>$type_account[$j]['brand'].'<br/>'.$type_account[$j]['account_code'],'width'=>'100');
    }
 
    $jslink = '<script language="javascript" type="text/javascript" src="./staticment/js/My97DatePicker/WdatePicker.js"></script>';
    $this->V->mark(array('title'=>'利润(账号)报表列表'));
    $temp = 'pub_list';
 }
 
 elseif($detail == 'outport'){
    $sqlstr = str_replace('account_id','type_account.id',$sqlstr);
    $type_account = $this->S->dao('type_account')->D->get_allstr('','','brand','id,account_code,brand');
    
    if ($profit_datea) $sqlstr.= ' and date_format(profit_date,"%Y-%m") ="'.$profit_datea.'"';
   
    foreach($type_account as $val){
        $sql .= 'SUM(CASE WHEN account_code="'.$val['account_code'].'" THEN costs  END) AS "'.$val['account_code'].'",';
    }
    $sql = trim($sql,',');
    $datalist 	= $this->S->dao('profit')->get_report_profit_by_account($sqlstr,$sql);
    
    $filename = 'report_profit_by_account-'.date('Y-m-d');
    $_headarr = array('item_name'=>'项目','sumcosts'=>'汇总(USD)');
    
    for($j = 0; $j < count($type_account); $j++){
        $arr[$type_account[$j]['account_code']] = $type_account[$j]['brand'].'<br/>'.$type_account[$j]['account_code'];
    }
    $head_array = array_merge($_headarr,$arr);
    $this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
        
 }
 /*模板输出*/
 if($detail == 'list'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
 
?>
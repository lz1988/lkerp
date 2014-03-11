<?php
/**
 +-----------------------------------------------
 * @title 利润报表(期间)
 * @author Jerry
 * @create on 2014-1-23
 +-----------------------------------------------
 */
 if ($detail == 'list'){
    $stypemu = array(
        'brand-a-e'        => '&nbsp;&nbsp;品&nbsp;&nbsp;&nbsp;牌：',
        'profit_date-b-'   => '期&nbsp;&nbsp;间：',
		'account_id-a-e'   => '<br>&nbsp;&nbsp;账&nbsp;&nbsp;&nbsp;号：',
    );
   	
    $brandarr = array(''=>'-请选择-','LOFTEK'=>'LOFTEK','MelodySusie'=>'MelodySusie','Miu Color'=>'Miu Color','中性'=>'中性','前期不用帐号'=>'前期不用帐号','国内MIU'=>'国内MIU','其他收入'=>'其他收入');
    $profit_datestr  = "<input type=text name=profit_datea class=twodate onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$profit_datea." >";
    $profit_datestr .= "&nbsp;&nbsp;到&nbsp;&nbsp;<input type=text name=profit_dateb  class=twodate onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$profit_dateb." >";
    
    $arr_time = array();
    
    /*取得销售账号下拉*/
    $type_account = $this->S->dao('type_account')->D->get_allstr('','','','id,account_code');
    $account_idarr	 = array(''=>'=请选择=');
    for($i = 0; $i < count($type_account); $i++){
        $account_idarr[$type_account[$i]['id']] = $type_account[$i]['account_code'];
    }
    
    if ($profit_datea && $profit_dateb){
        //$InitPHP_conf['pageval'] = 15; //分页数
        $sqlstr = str_replace('account_id','type_account.id',$sqlstr);
        
        if ($profit_datea) $sqlstr.= ' and date_format(profit_date,"%Y-%m") >="'.$profit_datea.'"';
        if ($profit_dateb) $sqlstr.= ' and date_format(profit_date,"%Y-%m") <="'.$profit_dateb.'"';
        
        $bannerstrarr[] = array('url'=>'index.php?action=report_profit&detail=outport&profit_datea='.$profit_datea.'&profit_dateb='.$profit_dateb.'','value'=>'导出数据');
        
        $profit_datea = strtotime($profit_datea);
        $profit_dateb = strtotime($profit_dateb);
       
       
        while($profit_datea <= $profit_dateb){
    	    $arr_time[]   = date('Y-m',$profit_datea);
            $profit_datea = strtotime("+1 month",$profit_datea);
        }
        
        foreach($arr_time as $val){
            $sql .= 'SUM(CASE WHEN DATE_FORMAT(profit_date,"%Y-%m")="'.$val.'" THEN costs ELSE 0 END) AS "'.$val.'",';
        }
        $sql = trim($sql,',');
        
        $datalist 	= $this->S->dao('profit')->get_list($sqlstr,$sql);
        
        for($i = 0; $i < count($datalist); $i++){
            for($j = 0; $j < count($arr_time); $j++){
                $datalist[$i]['sumcosts'] += $datalist[$i][$arr_time[$j]];  
            }
        }
        
         
     }
    
    $displayarr = array();
    $displayarr['item_name']    = array('showname'=>'项目',  'width'=>'120');
    $displayarr['sumcosts']     = array('showname'=>'汇总(USD)', 'width'=>'100');
    foreach($arr_time as $v){
        $displayarr[$v]         = array('showname'=>$v, 'width'=>'100');
    }
    
   
    //$bannerstr 		= '<button class="six" onclick="window.location=\'index.php?action=warehouseshipping&detail=add\'">录入数据</button>';
    $jslink = '<script language="javascript" type="text/javascript" src="./staticment/js/My97DatePicker/WdatePicker.js"></script>';
    $this->V->mark(array('title'=>'利润(期间)报表列表'));
    $temp = 'pub_list';
 }
 else if ($detail == 'outport'){
        
        $sqlstr = str_replace('account_id','type_account.id',$sqlstr);
        
        if ($profit_datea) $sqlstr.= ' and date_format(profit_date,"%Y-%m") >="'.$profit_datea.'"';
        if ($profit_dateb) $sqlstr.= ' and date_format(profit_date,"%Y-%m") <="'.$profit_dateb.'"';
     
        $profit_datea = strtotime($profit_datea);
        $profit_dateb = strtotime($profit_dateb);
       
        $arr_time = array();
        while($profit_datea <= $profit_dateb){
    	    $arr_time[]   = date('Y-m',$profit_datea);
            $profit_datea = strtotime("+1 month",$profit_datea);
        }
        foreach($arr_time as $val){
            $sql .= 'SUM(CASE WHEN DATE_FORMAT(profit_date,"%Y-%m")="'.$val.'" THEN costs ELSE 0 END) AS "'.$val.'",';
        }
        $sql = trim($sql,',');
        
        $datalist 	= $this->S->dao('profit')->get_list($sqlstr,$sql);
        for($i = 0; $i < count($datalist); $i++){
            for($j = 0; $j < count($arr_time); $j++){
                $datalist[$i]['sumcosts'] += $datalist[$i][$arr_time[$j]];  
            }
        }
        $filename = 'report_profit'.date('Ymd',strtotime($profit_datea));
        
        $head_array = array('item_name'=>'项目','sumcosts'=>'汇总');
        
        $temp = array();
        foreach($arr_time as $v){
            $temp[$v] = $v;
        }
        $_headarr = array_merge($head_array,$temp);
    	$this->C->service('upload_excel')->download_excel($filename,$_headarr,$datalist);
 }
 /*模板输出*/
 if($detail == 'list'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
 
?>

<?php
/**
 * @title b2b客户报表
 * @author Jerry
 * @create on 2013-06-19
 */
 
 if ($detail == 'list') {
     $stypemu = array(
        'sku-s-l' 	    => 'SKU：',
		'order_id-s-e'	=> '&nbsp;&nbsp;订单号：',
        'corpname-s-l'=>'公司名：',
		'rdate-t-t'  => '&nbsp;&nbsp;出库时间：',
    );
    $process    = $this->S->dao('process');
    $datalist   = $process->get_b2bcorpbslinfo($sqlstr);
    
    $displayarr = array();
    $tablewith  = '900';
    
    $displayarr['order_id']   	= array('showname'=>'订单号', 'width'=>'60');
   	$displayarr['sku']          = array('showname'=>'SKU', 'width'=>'60');
    $displayarr['corpname']     = array('showname'=>'公司名', 'width'=>'100');
    $displayarr['contactname']  = array('showname'=>'联系人', 'width'=>'60');
    $displayarr['contactemail'] = array('showname'=>'邮箱', 'width'=>'60');
    $displayarr['rdate']        = array('showname'=>'出库时间', 'width'=>'100');
    
    $bannerstrarr[] = array('url'=>'index.php?action=report_b2bcorpbsl&detail=output', 'value'=>'导出数据');
    
    $this->V->view['title'] = 'b2b客户报表';
    $temp = 'pub_list';
 }
 /**
  * @title 导出
  * @author Jerry
  * @create on 2013-06-19
  */ 
elseif ($detail == 'output') {
    
    $process    = $this->S->dao('process');
    $datalist   = $process->get_b2bcorpbslinfo($sqlstr);
    $filename   = 'report_b2bcorpbsl_'.date('Y/m/d');
	$head_array = array(
				'order_id'			=> '订单号',
				'sku'				=> 'SKU',
				'corpname' 			=> '公司名',
				'contactname'		=> '联系人',
				'contacttel'		=> '联系电话',
				'contactemail' 		=> '联系邮箱',
				'rdate' 	  	    => '出库时间',
				
	);
    
    $this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}
    
    
    
if($detail == 'list'){
    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L');
} 
?>
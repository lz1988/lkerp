<?php
/*
 * create on 2012-05-08
 * by wall
 * 工作提醒模块
 * */
 
/*
 * create on 2012-05-08
 * by wall
 * 需要验证的权限数组:
 * r_w_stock        备货审核权限
 * r_w_rec          接收备货权限
 * r_w_surcigou     采购确认权限
 * r_w_cigou        采购审核权限
 * r_w_chanpay      上传水单权限
 * r_w_receoutw     出库单权限 & 物料调拨权限
 * r_w_ctoware      常规入库权限
 * r_w_addepay      增加其他费用（采购单）权限
 * */
$admin_access_arr = array(
        'r_w_stock',
        'r_w_rec',
        'r_w_surcigou',
        'r_w_cigou',
        'r_w_chanpay',
        'r_w_receoutw',
        'r_w_ctoware',
        'r_w_ftoware',
        'r_w_rtoware');
     
//查询条件组
$sqlgroupstr = ' group by TO_DAYS(mdate) ';
//查询排序条件
$sqlorderstr = ' order by mdate desc';
        
 /*
  * create on 2012-05-08
  * by wall
  * 权限对应查询数据条件语句数组：
  * */
$admin_access_sqlstr_arr = array(
        'r_w_stock' => ' select "r_w_stock" as admin_access,property,mdate, count(*) as count, sum(quantity) as sum from process where statu="0" and isover="N" and property ="备货单"  group by TO_DAYS(mdate) ',
        'r_w_rec' => ' select "r_w_rec" as admin_access,property,mdate, count(*) as count, sum(quantity) as sum from process where statu="1" and property ="备货单" and isover="N" group by TO_DAYS(mdate) ',
        'r_w_surcigou' => ' select "r_w_surcigou" as admin_access,property,mdate, count(*) as count, sum(quantity) as sum from process where statu="1" and isover="N" and property="采购单" group by TO_DAYS(mdate) ',
        'r_w_cigou' => ' select "r_w_cigou" as admin_access,property,mdate, count(*) as count, sum(quantity) as sum from process where statu="0" and isover="N" and property="采购单" group by TO_DAYS(mdate) ',
        'r_w_chanpay' => ' select "r_w_chanpay" as admin_access,property,mdate, count(*) as count, sum(quantity) as sum from process where statu="1" and ispay="0" and isover="N" and property="采购单" group by TO_DAYS(mdate) ',
        'r_w_receoutw' => ' select "r_w_receoutw" as admin_access,property,rdate as mdate, count(*) as count, sum(quantity) as sum from process where statu="0" and (protype="售出" or protype="重发") and isover="N" group by TO_DAYS(rdate) union all select "r_w_receoutw1" as admin_access,property,rdate as mdate, count(*) as count, sum(quantity) as sum from process where receiver_id>0 and statu="0" and output="0" and property="转仓单" and isover="N" and output="0" group by TO_DAYS(rdate) ',
        'r_w_ctoware' => ' select "r_w_ctoware" as admin_access,property,rdate as mdate, count(*) as count, sum(quantity) as sum from process where (statu="3" or statu="4") and property="采购单" and isover ="N" group by TO_DAYS(rdate) ',
        'r_w_ftoware' => ' select "r_w_ftoware" as admin_access,property,rdate as mdate, count(*) as count, sum(quantity) as sum from process where (statu="3" or statu="4") and property="转仓单" and active="1" and output="1" and input="0" and isover ="N" group by TO_DAYS(rdate) ',
        'r_w_rtoware' => ' select "r_w_rtoware" as admin_access,property,rdate as mdate, count(*) as count, sum(quantity) as sum from process where (statu="3" or statu="4") and protype="退货" and active="1" and input="0" and isover ="N" group by TO_DAYS(rdate) '
        ); 

/*
 * create on 2012-05-09
 * by wall 
 * 查询结果对应当前状态、目标状态数组
 * */
$admin_access_statu_arr = array(
        'r_w_stock' => array('nowstatu' => '未审核', 'nextstatu' => '已审核', 'link_url' => 'index.php?action=process_upstock&detail=list&statu=0'),
        'r_w_rec' => array('nowstatu' => '已审核', 'nextstatu' => '已接收', 'link_url' => 'index.php?action=process_upstock&detail=list&statu=1'),
        'r_w_surcigou' => array('nowstatu' => '已审核', 'nextstatu' => '已下单', 'link_url' => 'index.php?action=process_modstock&detail=list&statu=1&ispay='),
        'r_w_cigou' => array('nowstatu' => '未审核', 'nextstatu' => '已审核', 'link_url' => 'index.php?action=process_modstock&detail=list&statu=0'),
        'r_w_chanpay' => array('nowstatu' => '未付款', 'nextstatu' => '已付款', 'link_url' => 'index.php?action=process_modstock&detail=list&statu=1&ispay=0'),
        'r_w_receoutw' => array('nowstatu' => '预出库', 'nextstatu' => '已接收', 'link_url' => 'index.php?action=process_shipment&detail=list&statu=0'),
        'r_w_ctoware' => array('nowstatu' => '预入库', 'nextstatu' => '已入库', 'link_url' => 'index.php?action=process_recstock&detail=list&statu=3&rece_type=cr'),        
        'r_w_ftoware' => array('nowstatu' => '预入库', 'nextstatu' => '已入库', 'link_url' => 'index.php?action=process_recstock&detail=list&statu=3&rece_type=hr'),
        'r_w_rtoware' => array('nowstatu' => '预入库', 'nextstatu' => '已入库', 'link_url' => 'index.php?action=process_recstock&detail=list&statu=3&rece_type=tr'),
        'r_w_receoutw1' => array('nowstatu' => '预出库', 'nextstatu' => '已接收', 'link_url' => 'index.php?action=process_transfer&detail=list&statu=0')
        ); 
 
if ($detail == 'list') {
    
    /*用于跳转至采购页面提前生成SESSION用*/
    $_SESSION['process_modstock_stypemu'] = json_encode(array('statu-s-e'=>' ','ispay'=>' ')); 
    $_SESSION['process_upstock_stypemu'] = json_encode(array('statu-s-e'=>' ')); 
    $_SESSION['process_shipment_stypemu'] = json_encode(array('statu-s-e'=>' ')); 
    $_SESSION['process_recstock_stypemu'] = json_encode(array('statu-s-e'=>' ', 'rece_type-a-e'=>' ')); 
    $_SESSION['process_transfer_stypemu'] = json_encode(array('statu-s-e'=>' ')); 
    
    $admin_access = $this->C->service('admin_access');
    $sqlstr = 'select property,admin_access,mdate,count,sum from (';
    //不取个人费用提醒
    $accessnum = count($admin_access_arr);
    
    $unionstr = '';
    for ($i = 0; $i < $accessnum; $i++) {
        if ($admin_access->checkResRight($admin_access_arr[$i])) {
            if (!empty($unionstr)) {
                $unionstr .= ' union all ';
            }           
            $unionstr .= $admin_access_sqlstr_arr[$admin_access_arr[$i]];           
        }        
    }
    $sqlstr .= $unionstr;
    $sqlstr .= ') as p '.$sqlorderstr;
    $InitPHP_conf['pageval'] = 10;
    $datalist = $this->S->dao('process')->D->query_array($sqlstr);
    foreach ($datalist as &$val) {
        $val['count'] = '<a href="'.$admin_access_statu_arr[$val['admin_access']]['link_url'].'&job_alert_time='.date('Y-m-d', strtotime($val['mdate'])).'" target="_blank">'.$val['count'].'个'.$val['property'].'</a>';        
        $val['sum'] .= '件产品';
        $val['mdate'] = date('m-d', strtotime($val['mdate']));
        $val['nowstatu'] = $admin_access_statu_arr[$val['admin_access']]['nowstatu'];
        $val['nextstatu'] = $admin_access_statu_arr[$val['admin_access']]['nextstatu'];
    }
    
    
    $displaykey = array('count','mdate','sum','nowstatu');
    
    $flush_url = 'index.php?action=job_alerts&detail=list';
	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'button_str'=>$button_str, 'flush_url'=>$flush_url));
	$this->V->set_tpl('adminweb/single_model');
	display();
}
?>
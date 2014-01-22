<?php
/*
 * Created on 2012-6-21
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 *
 * 物流造假用
 */

 if($detail == 'list'){  
    $stypemu = array(
        'sku-s-l' => '&nbsp; &nbsp;SKU：',
        'trackno-s-l'=>'TRACK.NO：',
        'pname-s-l' => '&nbsp; &nbsp;产品名称：',
        'date-t-r' => '&nbsp; &nbsp;截止时间：',
    );

    $InitPHP_conf['pageval'] = 20;
    $shipping_fare  = $this->C->service('shipping_fare');
    $shipping_area  = $this->S->dao('shipping_area');
    
    /*获取物流方式*/
    $shipping       = $this->S->dao('shipping');
    $shippinglist   = $shipping->D->get_allstr('','','id','id,s_name');
    $shippingarr    = array();
    foreach($shippinglist as $v){
        $shippingarr[$v['id']] = $v['s_name'];
    } 
    $datalist = $this->S->dao('mike')->D->get_list($sqlstr,'','id desc','*');
    
    //获取货贷充值金额总和
    if ($endTime){$date = 'cdate<="'.$endTime.' 23:59:59"';}else{$date = 1;}
    $rechargelist = $this->S->dao('mike_recharge')->D->select('sum(price) as price',$date);
    
    //获取货代支出金额
    if ($endTime){$mikesql = 'date<="'.$endTime.' 23:59:59"';}else{$mikesql = 1;}
    $costsum = $this->S->dao('mike')->D->select('sum(cost) as cost',$mikesql);
    
    for($i =0; $i<count($datalist); $i++){
        $datalist[$i]['shipping_id'] = $shippingarr[$datalist[$i]['shipping_id']];
    }
    $paycostsum = number_format($rechargelist['price']-$costsum['cost'],2);
    $tablewidth = '900'; 
    $displayarr['id']           = array('showname'=>'checkbox','width'=>'50');
    $displayarr['date']         = array('showname'=>'日期','width'=>'150');
    $displayarr['sku']          = array('showname'=>'SKU','width'=>'100');
    $displayarr['pname']        = array('showname'=>'产品名称','width'=>'120');
    $displayarr['trackno']  	= array('showname'=>'TRACK.NO','width'=>'150');
    $displayarr['pono']         = array('showname'=>'PO.NO','width'=>'150');
    $displayarr['shipping_id']  = array('showname'=>'发货方式','width'=>'100');
    $displayarr['cost']         = array('showname'=>'预估运费','width'=>'80');
    $displayarr['quantity'] 	= array('showname'=>'件数','width'=>'100');
    $displayarr['weight']       = array('showname'=>'重量','width'=>'100');
    $displayarr['tohouse']      = array('showname'=>'目的地(简码)','width'=>'150');

    $bannerstr .= '<button onclick=window.location="index.php?action=mike&detail=import">导入产品</button>';
    $bannerstr .= '<button onclick=javascript:del();>删除选中</button>';
    $bannerstr .= '<script>function del(){var did = get_orderid(); if(!did){alert("请选择数据！");return false;}else{CommomAjax("POST","index.php?action=mike&detail=moddel",{"did":did},function(msgd){alert(msgd);if(msgd =="删除成功"){window.location.reload();} });}}</script>';
    $bannerstr .= '<button onclick=window.location="index.php?action=mike&detail=output">导出产品</button>';
    $bannerstr .='&nbsp;&nbsp;可用余额：<font color="red">'.$paycostsum.'</font>';
    if($this->C->service('admin_access')->checkResRight('mike_recharge_add'))
        $bannerstr .='&nbsp;&nbsp;支出金额：<font color="red">'.$costsum['cost'].'</font>';
    $jslink = "<script src='./staticment/js/process_shipment.js'></script>\n";
    $this->V->mark(array('title'=>'产品列表'));
    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L'); 
    $temp = 'pub_list';
 }

/*执行删除*/
 elseif($detail == 'moddel'){
    /*去掉由框架强制开启魔法变量添加的反斜杠*/
    $did = stripslashes($did);
    $cid = $this->S->dao('mike')->D->delete_sql(' where id in ('.$did.')');
    if($cid) {echo  '删除成功';}else{echo  '删除失败';}
 }
 /*导入*/
 elseif($detail == 'import'){
    $tablelist 	= '<table  id="mytable" ><tr><td></td></tr></table>';//需要定义一个ID为Mytable的空表格，否则公共拖动的JS出错。 
    //取上传的文件的数组
    $upload_dir = "./data/uploadexl/temp/";//上传文件保存路径的目录
    $fieldarray = array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
    $tablearray = array('date','sku','pname','pono','trackno','shipping_id','quantity','weight','tohouse');
    
    $head = 1;//以第一行为表头 
    $all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
    $datalist = array();
    $check_i = 0;
    /*检查表头*/
    foreach($all_arr[0] as $val){
        if($val != $tablearray[$check_i]) {echo '<font size=-1>无效的表头：'.$val.'，正确表头应为：'.$tablearray[$check_i].'</font>';exit();}
        $check_i++;
    }
    
    if($all_arr){
        $shipping  = $this->S->dao('shipping');
        $datalist  = $shipping->D->get_allstr('','','id','id,s_name');
        $shippingarr = array();
        foreach($datalist as $v){
            $shippingarr[$v['id']] = $v['s_name'];
        } 
        $mike = $this->S->dao('mike');  
        $shipping_area  = $this->S->dao('shipping_area');
        //获取预估金额的总数
        $shipping_fare  = $this->C->service('shipping_fare');
        $costsum2 = $mike->D->select('sum(cost) as cost','1'); 
        //获取货贷充值金额总和
        $rechargelist = $this->S->dao('mike_recharge')->D->select('sum(price) as price','1');
        $error_num = 0;
        $succc_num = 0;
        unset($all_arr[0]); 
        $shippingsum = 0;
        foreach($all_arr as $key=> $val){
            $shipping_id = $val['shipping_id'];
            if (array_search(trim($shipping_id),$shippingarr) == false){echo '<font size=-1>发送方式错误，正确应为：'.implode(',',$shippingarr).'</font>';exit();}
            if (!preg_match('/^[a-z_A-Z]{1,}$/',$val['tohouse'])){echo '<font size=-1>目的国家编码错误，正确应为：国家的简码形式</font>';exit();}
            $area_id  = $shipping_area->D->get_one_by_field(array('pinyin'=>$val['tohouse']),'id');
            if (empty($area_id)){echo '<font size=-1>目的地找不到对应的(简码)，请核对后在操作！</font>';exit();}
            $all_arr[$key]['shipping']  = array_search($shipping_id,$shippingarr);
            
            if (strpos($val['date'],'-') == false){
                $n = intval(($val['date'] - 25569) * 3600 * 24); //转换成1970年以来的秒数
                $all_arr[$key]['date'] = date('Y-m-d H:i:s', $n);//格式化时间
            } 
            $area_id  = $shipping_area->D->get_one_by_field(array('pinyin'=>$val['tohouse']),'id');
            //应财务要求 货代的数量为1
            $cost = $shipping_fare->getshipping_cost($val['weight'],'18600000000',$area_id['id'],$all_arr[$key]['shipping'],'CNY',1,true); 
            $all_arr[$key]['cost'] = sprintf("%.2f", $cost); 
            $shippingsum += $cost;
        }
        
        //可用余额是否大于支出金额
        if((double)$rechargelist['price']>=((double)$costsum2['cost']+(double)$shippingsum)){
             foreach($all_arr as $val){
                $sid = $mike->D->insert(array(
                    'date'        =>$val['date'],
                    'sku'         =>mt_rand(10,99).'-'.mt_rand(1000,9999).'-'.mt_rand(100,999),
                    'pname'       =>$val['pname'],
                    'trackno'     =>$val['trackno'],
                    'pono'        =>$val['pono'],
                    'shipping_id' =>$val['shipping'],
                    'quantity'    =>$val['quantity'],
                    'weight'      =>$val['weight'],
                    'tohouse'     =>$val['tohouse'],
                    'cost'        =>$val['cost'],   
                )); 
                if(!$sid) {$error_num++;}else{$succc_num++;}
            } 
            if(empty($error_num)) {$this->C->success('导入成功，共录入 '.$succc_num.' 条记录','index.php?action=mike&detail=list');}{$this->C->success('导入失败，请重试','index.php?action=mike&detail=list');}
        }else{
            $this->C->success('导入失败，当前支出金额('.((double)$shippingsum).')大于可用余额('.((double)$rechargelist['price']-(double)$costsum2['cost']).'),请增加可用余额','index.php?action=mike&detail=list');
        }   
    } 
    
    $temlate_exlurl = 'data/uploadexl/sample/shippenting.xls';
    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L');
    $this->V->mark(array('title'=>'导入追踪单号-产品列表(list)','temlate_exlurl'=>$temlate_exlurl,'tablelist'=>$tablelist));
    $this->V->set_tpl('adminweb/commom_excel_import');
    display();

 }
 
 /*产品导出*/
 elseif ($detail == 'output') {
    
    $shipping_area = $this->S->dao('shipping_area');
    $shipping_fare = $this->C->service('shipping_fare');
    
    /*获取物流方式*/
    $shipping = $this->S->dao('shipping');
    $data = $shipping->D->get_allstr('','','id','id,s_name');
    $shippingarr = array();
    foreach($data as $v){
        $shippingarr[$v['id']] = $v['s_name'];
    } 
    
    $datalist = $this->S->dao('mike')->D->get_allstr($sqlstr,'','id desc','*');
    for($i =0; $i<count($datalist); $i++){
        $area_id                = $shipping_area->D->get_one_by_field(array('pinyin'=>$datalist[$i]['tohouse']),'id');
        $cost                   = $shipping_fare->getshipping_cost($datalist[$i]['weight'],'18600000000',$area_id['id'],$datalist[$i]['shipping_id'],'CNY',$datalist[$i]['quantity']);
        $datalist[$i]['cost']   = $cost;
        $datalist[$i]['shipping_id']    = $shippingarr[$datalist[$i]['shipping_id']];
    }

    $filename = 'mike_'.date('Y-m-d');
    $head_array = array('date'=>'日期', 'sku'=>'SKU', 'pname'=>'产品名称','trackno'=>'TRACT.NO','pono'=>'PONO','shipping_id'=>'发货方式','cost'=>'预估运费','quantity'=>'数量','weight'=>'重量','tohouse'=>'目的地(简码)');
    $this->C->service('upload_excel')->download_excel($filename, $head_array, $datalist);
 }
 
/*新增页面*/
elseif($detail == 'add'){
	//if(!$this->C->service('admin_access')->checkResRight('mike_recharge_edit')){$this->C->sendmsg();}//权限判断 
        
        $showtitle = '新增';
	$modurl = 'modadd';
	 
	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=mike&detail='.$modurl,'width'=>'500');
	$colwidth= array('1'=>'100','2'=>'300','3'=>'100');
	
	$disinputarr = array();
	$disinputarr['id'] = array('showname'=>'编辑ID','value'=>0,'datatype'=>'h');
	$disinputarr['price'] = array('showname'=>'金额','value'=>0);
        $disinputarr['price'] = array('showname'=>'金额','value'=>0);
	$this->V->view['title'] = $showtitle.'-列表(list)';
	$temp = 'pub_edit';
}
/*保存添加*/
elseif($detail == 'modadd'){
    
	$sid = $this->S->dao('mike_recharge')->D->insert(array('cdate'=>date('Y-m-d H:i:s', time()),'price'=>$price,));
	if($sid){
 		$this->C->success('保存成功','index.php?action=mike_recharge&detail=list');
	}else{
		$this->C->success('保存失败','index.php?action=mike_recharge&detail=add');
	}
}
?>

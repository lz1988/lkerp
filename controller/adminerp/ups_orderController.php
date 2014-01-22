<?php
/**
 * @title ups订单
 * @author color
 * @create on 2013-04-8
 */  
if($detail == 'list'){
    /*搜索选项*/
	$stypemu = array(
        'statu-a-e'=>'订单状态：',
        'rtype-a-e'=>'出库类型：',
        'order_id-s-l'=>'订单号：', 
	);
	$statuarr = array('1'=>'已接收','2'=>'待发货');
    $rtypearr = array('1'=>'物料调拨','2'=>'出库订单');  
	if(empty($statu)) { $sqlstr = ' and statu="1" ';$statu = 1;}
    $InitPHP_conf['pageval'] = 20;
    //物料调拨
    if(empty($rtype) or (int)$rtype==1) {$sqlstr =  str_replace('and rtype="1"', '', $sqlstr); $sqlstr .= ' and p.receiver_id>0 and p.property="转仓单"  ';$rtype = 1;}
    //出库订单
    if((int)$rtype == 2) {$sqlstr =  str_replace('and rtype="2"', '', $sqlstr);$sqlstr .= ' and p.provider_id="10" and (p.protype="售出" or p.protype="重发")'; } 
    $sqlstr .= ' and p.isover="N" and locate("ups",p.extends,1)>0 '; 

    $arr 	= $this->S->dao('process')->get_emsorder($sqlstr);  
    /*数据处理*/
    if($arr){
        for($i=0;$i<count($arr);$i++){  
                $extends = json_decode($arr[$i]['extends'],true);
                $arr[$i]['shipping'] = $extends['e_shipping'];
                $arr[$i]['e_tel'] = $extends['e_tel'];
                $arr[$i]['e_address1'] = $extends['e_address1'];
                $arr[$i]['e_address2'] = $extends['e_address2'];
                $arr[$i]['e_city'] = $extends['e_city'];
                $arr[$i]['e_state'] = $extends['e_state'];
                $arr[$i]['e_country'] = $extends['e_country'];
                $arr[$i]['e_post_code'] = $extends['e_post_code'];
                //<span title="点击编辑" onclick="goput('64049','comment2')" id="span_comment2_64049">54473147ddfsfs</span>
                //<input type="text" onblur="backput('64049','comment2','process_shipment','editcomment')" id="input_comment2_64049" style="display:none;width:100px;">
                $extends['c_temporary_price']=!empty($extends['c_temporary_price'])?$extends['c_temporary_price']:'0.00';
                $extends['c_temporary_boxnum']=!empty($extends['c_temporary_boxnum'])?$extends['c_temporary_boxnum']:0;
                $extends['c_temporary_weight']=!empty($extends['c_temporary_weight'])?$extends['c_temporary_weight']:'0.00';
                $arr[$i]['c_temporary_price'] = '<span style="width:100px" title="点击编辑" onclick="pubgoput('.$arr[$i]['id'].',\'c_temporary_price\')" id="span_c_temporary_price_'.$arr[$i]['id'].'">'.$extends['c_temporary_price'].'</span><input type="text" name="c_temporary_price" id="input_c_temporary_price_'.$arr[$i]['id'].'" title="系统保留俩位小数"   style="display:none;width:100px;"  value="'.$extends['c_temporary_price'].'" onblur="checkupsisnan(\'c_temporary_price\','.$arr[$i]['id'].')"/>';
                $arr[$i]['c_temporary_boxnum'] = '<span style="width:100px" title="点击编辑" onclick="pubgoput('.$arr[$i]['id'].',\'c_temporary_boxnum\')" id="span_c_temporary_boxnum_'.$arr[$i]['id'].'">'.$extends['c_temporary_boxnum'].'</span><input type="text" name="c_temporary_boxnum" id="input_c_temporary_boxnum_'.$arr[$i]['id'].'" title="整数"   style="display:none;width:100px;"  value="'.$extends['c_temporary_boxnum'].'" onblur="checkupsisnan(\'c_temporary_boxnum\','.$arr[$i]['id'].')"/>';
                $arr[$i]['c_temporary_weight'] = '<span style="width:100px" title="点击编辑" onclick="pubgoput('.$arr[$i]['id'].',\'c_temporary_weight\')" id="span_c_temporary_weight_'.$arr[$i]['id'].'">'.$extends['c_temporary_weight'].'</span><input type="text" name="c_temporary_weight" id="input_c_temporary_weight_'.$arr[$i]['id'].'" title="系统保留俩位小数"   style="display:none;width:100px;"  value="'.$extends['c_temporary_weight'].'" onblur="checkupsisnan(\'c_temporary_weight\','.$arr[$i]['id'].')"/>';
                $arr[$i]['c_order_all'] = $extends['c_order_all'];
                //$arr[$i]['order_idd'] = $arr[$i]['order_id']; 
                if($arr[$i]['order_id'] ==  $arr[$i-1]['order_id']){
                    $arr[$i]['order_idd'] = '';
                }else{
                    $arr[$i]['order_idd'] = $arr[$i]['order_id']; 
                } 
                $arr[$i]['idd'] = '<input type="checkbox" name="checkmod[]" title="'.$arr[$i]['id'].'" value="'.$arr[$i]['id'].'" />';
                if(!empty($extends['c_order_all'])){ 
                     $datalist[] = $arr[$i];
                        for($j=0;$j<count($arr);$j++){    
                        if(strpos($extends['c_order_all'],$arr[$j]['id'])!==false){
                            $extendsj = json_decode($arr[$j]['extends'],true);
                            $arr[$j]['shipping'] = $extendsj['e_shipping'];
                            $arr[$j]['e_tel'] = $extendsj['e_tel'];
                            $arr[$j]['e_address1'] = $extendsj['e_address1'];
                            $arr[$j]['e_address2'] = $extendsj['e_address2'];
                            $arr[$j]['e_city'] = $extendsj['e_city'];
                            $arr[$j]['e_state'] = $extendsj['e_state'];
                            $arr[$j]['e_country'] = $extendsj['e_country'];
                            $arr[$j]['e_post_code'] = $extendsj['e_post_code'];
                            $arr[$j]['idd'] = '';
                            $extendsj['c_temporary_price'] = !empty($extendsj['c_temporary_price'])?$extendsj['c_temporary_price']:'0.00';
                            $arr[$j]['c_temporary_price'] = '<span style="width:100px" title="点击编辑" onclick="pubgoput('.$arr[$j]['id'].',\'c_temporary_price\')" id="span_c_temporary_price_'.$arr[$j]['id'].'">'.$extendsj['c_temporary_price'].'</span><input type="text" name="c_temporary_price" id="input_c_temporary_price_'.$arr[$j]['id'].'" title="系统保留俩位小数"   style="display:none;width:100px;"  value="'.$extendsj['c_temporary_price'].'" onblur="checkupsisnan(\'c_temporary_price\','.$arr[$j]['id'].')"/>';
                            $num = count($datalist)-1;
                            if($datalist[$num]['order_id'] ==  $arr[$j]['order_id']){
                                $arr[$j]['order_idd'] = '';
                            }else{
                                $arr[$j]['order_idd'] = '<font color="red">同上</font>('.$arr[$j]['order_id'].')';
                            } 
                            $datalist[] = $arr[$j];
                            $keyarr[] = $j;
                            unset($arr[$j]); 
                        }    
                    }   
                }else{ 
                    if(!in_array($i, $keyarr)){ 
                        $datalist[]=$arr[$i]; 
                    }
                } 
            }       
    } 
	/*数据显示*/
	$displayarr = array();
	$tablewidth = '1100'; 
	$displayarr['idd']                    = array('showname'=>'选择','width'=>'50','title'=>'反选');
	$displayarr['order_idd']             = array('showname'=>'订单号','width'=>'80');
	$displayarr['fid']                   = array('showname'=>'第三方单号','width'=>'150');
	$displayarr['sku']                   = array('showname'=>'SKU','width'=>'80');
	$displayarr['product_name']          = array('showname'=>'产品名称','width'=>'250');
	$displayarr['quantity']              = array('showname'=>'数量','width'=>'50');
	$displayarr['cuser']                 = array('showname'=>'制单人','width'=>'60');
	$displayarr['shipping']              = array('showname'=>'发货方式','width'=>'80');
	$displayarr['warehouse']             = array('showname'=>'发货仓库','width'=>'100');
    $displayarr['e_tel']                 = array('showname'=>'联系电话','width'=>'100');
	$displayarr['e_address1']            = array('showname'=>'地址1','width'=>'150');
	$displayarr['e_address2']            = array('showname'=>'地址2','width'=>'150');
	$displayarr['e_city']                = array('showname'=>'城市','width'=>'80');
	$displayarr['e_state']               = array('showname'=>'洲','width'=>'50');
	$displayarr['e_country']             = array('showname'=>'国家','width'=>'80');
	$displayarr['e_post_code']           = array('showname'=>'邮编','width'=>'80');
    $displayarr['c_temporary_price'] 	 = array('showname'=>'产品价格','width'=>'80','title'=>'保留俩位小数');
    $displayarr['c_temporary_boxnum'] 	 = array('showname'=>'总箱数','width'=>'80');
    $displayarr['c_temporary_weight'] 	 = array('showname'=>'总重量','width'=>'80','title'=>'保留俩位小数');

	$bannerstr = '<button onclick="get_upsord()" class="six" style="width:98px">获取运单编号</button>';
    $bannerstr .= '<button onclick="checkmergeorder()" class="six" style="width:98px">不同单号打包</button>';
    $bannerstr .= '<input type="hidden" name="hrtypea" value="'.$rtype.'"/>';
    $bannerstr .= '<input type="hidden" name="hrstatu" value="'.$statu.'"/>';
	$jslink = "<script src='./staticment/js/process_shipment.js'></script>\n"; 
        
        /*标签导航选项*/
	$tab_menu_stypemu = array(
		'statu-1'=>'已接收',
		'statu-2'=>'待发货',
	); 
	$this->V->mark(array('title'=>'UPS'));
	$temp = 'pub_list';
}
/*虚拟增加箱数，重量，金额*/
elseif($detail == 'postupscontent'){
    if(isset($id) and !empty($name) and isset($val)){
        $process = $this->S->dao('process');
        $extends = $process->D->get_one(array('id'=>$id),'extends');
        $extendsarr = json_decode($extends, true);
        $error=0;
        if($extendsarr['c_order_all'] && $name!='c_temporary_price'){
            $extendsarr[$name] = $name=='c_temporary_boxnum'?intval($val):sprintf("%.2f", $val);
             /*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
            $json_extends = get_magic_quotes_gpc()?addslashes(json_encode($extendsarr)):json_encode($extendsarr);
            $process->D->update(array('id'=>$id),array('extends'=>$json_extends));
            $c_order_all_arr = explode('-',$extendsarr['c_order_all']);
            foreach ($c_order_all_arr as $key => $value){
                $c_extends = $process->D->get_one(array('id'=>$value),'extends');
                $c_extendsarr = json_decode($c_extends, true);
                $c_extendsarr[$name] = $name=='c_temporary_boxnum'?intval($val):sprintf("%.2f", $val);
                $json_extends = get_magic_quotes_gpc()?addslashes(json_encode($c_extendsarr)):json_encode($c_extendsarr);
                $sid = $process->D->update(array('id'=>$value),array('extends'=>$json_extends));
                if(!$sid){
                    $error++;
                    echo '保存失败，系统繁忙';
                    exit;
                }
            }
             echo $error==0?'保存成功':'系统繁忙';
        }else{
            $extendsarr[$name] = $name=='c_temporary_boxnum'?intval($val):sprintf("%.2f", $val);
            /*如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉。*/
            $json_extends = get_magic_quotes_gpc()?addslashes(json_encode($extendsarr)):json_encode($extendsarr);
            $sid = $process->D->update(array('id'=>$id),array('extends'=>$json_extends));
            echo $sid?'保存成功':'系统繁忙';
        }
    }else{
        echo '保存失败';
    }
    exit;
}
/*同一个shipto 可以打包*/
elseif($detail == 'checkmergeorder'){
    $strid    = stripslashes($strid);
	$datalist = $this->S->dao('process')->D->get_allstr(' and id in('.$strid.')','','','id,order_id,extends');
    $error = 0;
    $num = 0; 
    if(count($datalist)<2){/*echo '至少选择两个订单';*/echo -4;exit;}
    $this->S->dao('process')->D->query('begin');
	for($i=0; $i<count($datalist); $i++){
        $extends = json_decode($datalist[$i]['extends'],true);
        for($j=0;$j<count($datalist);$j++){
            if($datalist[$i]['id'] != $datalist[$j]['id']){
                if($datalist[$i]['order_id'] == $datalist[$j]['order_id']){
                    $num++;
                } 
                $extendslast = json_decode($datalist[$j]['extends'],true);
                if($extendslast['c_order_all']){/*echo '打包失败！已经是包裹不可以在打包！';*/echo -1;exit;}
                if($extends['e_shipping'] == $extendslast['e_shipping'] and $extends['e_tel'] == $extendslast['e_tel'] and $extends['e_address1'] == $extendslast['e_address1'] and 
                    $extends['e_address2'] == $extendslast['e_address2'] and $extends['e_city'] == $extendslast['e_city'] and $extends['e_state'] == $extendslast['e_state'] and 
                    $extends['e_country'] == $extendslast['e_country'] and $extends['e_post_code'] == $extendslast['e_post_code']  )
                {
                     if(strpos($extends['c_order_all'], $datalist[$j]['id'])!==false){
                         $extends['c_order_all'] = $extends['c_order_all'];
                     }else{
                         $extends['c_order_all'] = empty($extends['c_order_all'])?$datalist[$j]['id']:$extends['c_order_all'].'-'.$datalist[$j]['id']; 
                     }
                }else{
                    //$this->C->success('打包失败，请检查信息不一致！','index.php?action=ups_order&detail=list');
                    //echo '打包失败！发货方式，联系电话，地址，城市，洲，国家，邮编信息不一致请检查';
                    echo -3;
                    exit;
                }
            }  
        }
        if($num==count($datalist)){
             //echo '打包失败！同一个订单不能打包！';
             echo -2;
             exit;
        }
        $json_extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);
        $sid = $this->S->dao('process')->D->update(array('id'=>$datalist[$i]['id']),array('extends'=>$json_extends));
        if(!$sid){$error++;}
	}
    if($error){
        $this->S->dao('process')->D->query('rollback');
    }else{
        $this->S->dao('process')->D->query('commit');
    }
    echo $error;
    exit;
}
/*检查所选的订单ID是否属于不同订单，如果是则弹出提示*/
elseif($detail == 'checkemsid'){
	$strid 	= stripslashes($strid);
	$datalist 	= $this->S->dao('process')->D->get_allstr(' and id in('.$strid.')','','','id,extends');
    $idarr =  str_replace("'", '', $strid);
    $idarr = explode(',',$idarr); 
	for($i=1; $i<count($datalist); $i++){
        $extends = json_decode($datalist[$i-1]['extends'],true);
        if($extends['c_order_all']){
            if(count($idarr)!=1){
                 echo '1';
                 exit;
            }
	   }else{
            if($datalist[$i]['order_id'] != $datalist[$i-1]['order_id']){
                echo '1';
                exit();
            }
       } 
    }
}
/*取得运单编号并下载*/
elseif($detail == 'get_upsd'){  
        $strid 	= stripslashes($strid);
        //$rtype = (int)$rtype;//出库类型 1.物料调拨 2.出库订单
        $exchange_rate  = $this->C->service('exchange_rate');
        $productobj = $this->S->dao('product');
        $productcostobj = $this->S->dao('product_cost');
        $datalist   = $this->S->dao('process')->D->get_allstr(' and id in('.$strid.') and statu="'.$statu.'"','','id asc','id,order_id,receiver_id,pid,product_name,quantity,extends'); 
        
        $array = array();
        $orderarr =array();
        if($datalist){
            for($i=0;$i<count($datalist);$i++){
                $datalist[$i] = $this->C->service('warehouse')->decodejson($datalist,$i);
                $weight = $productobj->D->get_one_by_field(array('pid'=>$datalist[$i]['pid']),'shipping_weight,product_desc3');
                 
                $array[$datalist[$i]['order_id']]['e_tel']   = $datalist[$i]['e_tel'];  
                $array[$datalist[$i]['order_id']]['e_address1']  = $datalist[$i]['e_address1'];  
                $array[$datalist[$i]['order_id']]['e_address2']  = $datalist[$i]['e_address2']; 
                $array[$datalist[$i]['order_id']]['e_city']  = $datalist[$i]['e_city']; 
                $array[$datalist[$i]['order_id']]['e_state']  = $datalist[$i]['e_state']; 
                $array[$datalist[$i]['order_id']]['e_country'] = $datalist[$i]['e_country']; 
                $array[$datalist[$i]['order_id']]['e_post_code'] = $datalist[$i]['e_post_code'];
                $array[$datalist[$i]['order_id']]['e_company'] = $datalist[$i]['e_company']; 
                $array[$datalist[$i]['order_id']]['e_shipping'] = $datalist[$i]['e_shipping'];
                $array[$datalist[$i]['order_id']]['e_receperson']   = $datalist[$i]['e_receperson'];
                
                
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['product_name'] = $datalist[$i]['product_name'];
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['quantity'] = $datalist[$i]['quantity'];
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['product_desc3'] = $weight['product_desc3'];
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['pid'] = $datalist[$i]['pid'];
                
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['c_temporary_price'] = $datalist[$i]['c_temporary_price'];
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['c_temporary_boxnum'] = $datalist[$i]['c_temporary_boxnum'];
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['c_temporary_weight'] = $datalist[$i]['c_temporary_weight'];
                $array[$datalist[$i]['order_id']]['goods'][$datalist[$i]['id']]['order_id'] = $datalist[$i]['order_id'];
                $orderarr[$datalist[$i]['order_id']] = $datalist[$i]['order_id'];
                if($datalist[$i]['c_order_all']){ 
                    $array[$datalist[$i]['order_id']]['c_temporary_boxnum'] = $datalist[$i]['c_temporary_boxnum'];
                    $array[$datalist[$i]['order_id']]['c_temporary_weight'] = $datalist[$i]['c_temporary_weight'];
                    $idd = str_replace('-', ',', $datalist[$i]['c_order_all']);
                    $dataarr  = $this->S->dao('process')->D->get_allstr(' and id in('.$idd.') and statu="'.$statu.'"','','id asc','id,order_id,receiver_id,pid,product_name,quantity,extends');
                    for($j=0;$j<count($dataarr);$j++){
                        $dataarr[$j] = $this->C->service('warehouse')->decodejson($dataarr,$j);
                       
                        $desctxt = $productobj->D->get_one_by_field(array('pid'=>$dataarr[$j]['pid']),'product_desc3');
                       
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['product_name'] = $dataarr[$j]['product_name'];
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['quantity'] = $dataarr[$j]['quantity'];
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['product_desc3'] = $desctxt['product_desc3'];
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['pid'] = $dataarr[$j]['pid'];
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['order_id'] = $dataarr[$j]['order_id'];
                        $orderarr[$dataarr[$j]['order_id']] = $dataarr[$j]['order_id'];
                        
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['c_temporary_price'] = $dataarr[$j]['c_temporary_price'];
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['c_temporary_boxnum'] = $dataarr[$j]['c_temporary_boxnum'];
                        $array[$datalist[$i]['order_id']]['goods'][$dataarr[$j]['id']]['c_temporary_weight'] = $dataarr[$j]['c_temporary_weight']; 
                    }  
                }else{
                    $array[$datalist[$i]['order_id']]['c_temporary_boxnum'] += $datalist[$i]['c_temporary_boxnum'];
                    $array[$datalist[$i]['order_id']]['c_temporary_weight'] += $datalist[$i]['c_temporary_weight'];
                } 
            }  
            
            $xmldata = '<?xml version="1.0" encoding="UTF-8"?>';
            $xmldata.= '<OpenShipments xmlns="x-schema:OpenShipments.xdr">';
            //$xmldata.= '<OpenShipments xmlns="">';
            $xmldata.= '<OpenShipment ShipmentOption="" ProcessStatus="">';  
            
            $servicetype = array('ups-express '=>'SV','ups-expedited'=>'EX');
            $productstr = '';  
           
            foreach ($array as $key => $value) {
                if(empty($value['e_company'])){
                    echo "<script>alert('CompanyOrName为空，生成失败！');history.back(-1)</script>";
                    exit;
                }elseif(empty($value['e_receperson'])){
                    echo "<script>alert('e_receperson为空，生成失败！');history.back(-1)</script>";
                    exit;
                }elseif(empty($value['e_address1'])){
                    echo "<script>alert('e_address1为空，生成失败！');history.back(-1)</script>";
                    exit;
                }elseif(empty($value['e_country'])){
                    echo "<script>alert('e_country为空，生成失败！');history.back(-1)</script>";
                    exit;
                }elseif(empty($servicetype[$value['e_shipping']])){
                    echo "<script>alert('ServiceType为空，生成失败！');history.back(-1)</script>";
                    exit;
                }elseif(empty($value['c_temporary_weight'])){
                    echo "<script>alert('ShipmentActualWeight为空，生成失败！');history.back(-1)</script>";
                    exit;
                }elseif(empty($value['c_temporary_boxnum']) and $rtype==1){
                    echo "<script>alert('NumberOfPackages为空，生成失败！');history.back(-1)</script>";
                    exit;
                }elseif(count($orderarr)<1 and $rtype==2){
                    echo "<script>alert('NumberOfPackages为空，生成失败！');history.back(-1)</script>";
                    exit;
                }
                 
                //收件方的所有信息
                $xmldata.= '<ShipTo>';
                $xmldata.= '<CustomerID></CustomerID>';
                switch ($rtype) {
                    case 1: 
                        $xmldata.= '<CompanyOrName>'.$value['e_company'].'</CompanyOrName>';//r
                        $xmldata.= '<Attention>'.$value['e_receperson'].'</Attention>'; //r
                        break;
                    case 2: 
                        $xmldata.= '<CompanyOrName>'.$value['e_receperson'].'</CompanyOrName>';  //r
                        $xmldata.= '<Attention>'.$value['e_receperson'].'</Attention>'; //r
                        break; 
                    default:
                        break;
                }  
                $xmldata.= '<Address1>'.$value['e_address1'].'</Address1>';  //r
                $xmldata.= '<Address2>'.$value['e_address2'].'</Address2>';
                $xmldata.= '<Address3></Address3>';
                $xmldata.= '<CountryTerritory>'.$value['e_country'].'</CountryTerritory>';  //r
                $xmldata.= '<PostalCode>'.$value['e_post_code'].'</PostalCode>'; 
                $xmldata.= '<CityOrTown>'.$value['e_city'].'</CityOrTown>';  
                $xmldata.= '<StateProvinceCounty>'.$value['e_state'].'</StateProvinceCounty>'; 
                $xmldata.= '<Telephone>'.$value['e_tel'].'</Telephone>';  
                $xmldata.= '<ReceiverUpsAccountNumber></ReceiverUpsAccountNumber>';
                $xmldata.= '</ShipTo>';

                //指第三方付费的所有信息
                $xmldata.= '<ThirdParty>';
                $xmldata.= '<CustomerID></CustomerID>'; 
                $xmldata.= '<CompanyOrName>LOFTK INTERNATIONAL TRADING CO., LTD</CompanyOrName>';
                $xmldata.= '<Attention></Attention>';
                $xmldata.= '<Address1>FLAT/RM 603 6/F HANG PONT COMM</Address1>';
                $xmldata.= '<Address2></Address2>';
                $xmldata.= '<Address3></Address3>';
                $xmldata.= '<CountryTerritory>HONG KONG</CountryTerritory>';
                $xmldata.= '<PostalCode></PostalCode>';
                $xmldata.= '<CityOrTown>CHEUNG SHA WAN</CityOrTown>';
                $xmldata.= '<StateProvinceCounty></StateProvinceCounty>';
                $xmldata.= '<Telephone></Telephone>';
                $xmldata.= '<UpsAccountNumber>V8Y314</UpsAccountNumber>';
                $xmldata.= '</ThirdParty>';
                //发货信息
                $xmldata.= '<ShipmentInformation>';
                $xmldata.= '<ServiceType>'.$servicetype[$value['e_shipping']].'</ServiceType>';//r
                $xmldata.= '<PackageType>cp</PackageType>';
//                if(!isset($value['c_temporary_boxnum'])){
//                    $value['c_temporary_boxnum'] = 1;
//                }
                 switch ($rtype) {
                    case 1: 
                        $xmldata.= '<NumberOfPackages>'.$value['c_temporary_boxnum'].'</NumberOfPackages>';  //r
                        break;
                    case 2: 
                        $xmldata.= '<NumberOfPackages>'.count($orderarr).'</NumberOfPackages>'; //r
                        break; 
                    default:
                        break;
                }
                $xmldata.= '<ShipmentActualWeight>'.$value['c_temporary_weight'].'</ShipmentActualWeight>';//r
                $xmldata.= '<DescriptionOfGoods>Refer to Invoice</DescriptionOfGoods>';
                $xmldata.= '<BillingOption>TF</BillingOption>';
                $xmldata.= '</ShipmentInformation>';
                //包
//                $xmldata.= '<Package>';
//                $xmldata.= '<PackageType>cp</PackageType>';
//               
//                $xmldata.= '<Reference1></Reference1>';
//                $xmldata.= '<Reference2></Reference2>';
//                $xmldata.= '</Package>';
                //国际文献
                $xmldata.= '<InternationalDocumentation>';
                $xmldata.= '<InvoiceCurrencyCode>US</InvoiceCurrencyCode>';
                $xmldata.= '</InternationalDocumentation>';
                //商品信息
                if(is_array($value['goods'])){
                    foreach ($value['goods'] as $key => $val) {
                        $xmldata.= '<Goods>';
                        $xmldata.= '<DescriptionOfGood>'.$val['product_desc3'].'</DescriptionOfGood>';
                        $xmldata.= '<Inv-NAFTA-CO-CountryTerritoryOfOrigin>CN</Inv-NAFTA-CO-CountryTerritoryOfOrigin>';
                        $xmldata.= '<InvoiceUnits>'.$val['quantity'].'</InvoiceUnits>';
                        $xmldata.= '<InvoiceUnitOfMeasure>PCS</InvoiceUnitOfMeasure>';
                        $xmldata.= '<Invoice-SED-UnitPrice>'.$val['c_temporary_price'].'</Invoice-SED-UnitPrice>';
                        $xmldata.= '<InvoiceCurrencyCode>USD</InvoiceCurrencyCode>';
                        $xmldata.= '</Goods>';  
                    } 
                } 
            }

        }
        
        $xmlname = implode('_', $orderarr);
        $xmldata.= '</OpenShipment>';
        $xmldata.= '</OpenShipments>';
        $xmlname = isset($xmlname)?$xmlname:'error';
        header('Content-Type: text/xml;'); 
        header('Content-Disposition: attachment; filename="'.$xmlname.'.xml"'); 
        echo $xmldata; 
        exit;  
}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}

?>

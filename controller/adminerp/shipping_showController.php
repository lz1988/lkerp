<?php


if ($detail == 'list'){
    $stypemu = array(
        'select-b-' =>'', 
        'from-b-'      =>'发送国家：',
        'to-b-'        =>'目的国家：',
        'f-h-'         =>'发送地',
        't-h-'         =>'目地地',
        'sku-s-l'       =>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S K U：',
        'quantity-s-l'  =>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数 量：',
        'volume-s-l'    =>'<br/>&nbsp;&nbsp;长 宽 高：',
        'weight-s-'    =>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;重 量：',
        'currency-a-e' =>'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;币 别：',  
    );
    
    $selectstr = '<a href="index.php?action=shipping_countnew&detail=list">点击进入管理查看>></a><br>';
    $fromstr    = $this->C->service('shipping_fare')->getArea($f,'f').'<br>';
    $tostr      = $this->C->service('shipping_fare')->getArea($t,'t').'<br>';
    /*生成转换货币下拉*/
    $currency = $currency?$currency:'CNY';
    $exchange_rate_data  = $this->S->dao('exchange_rate')->D->get_all(array('isnew'=>'1'));
    $currencyarr = array(''=>'=请选择=');
    for ($i = 0;$i<count($exchange_rate_data);$i++){
      $currencyarr[$exchange_rate_data[$i]['code']] = $exchange_rate_data[$i]['c_name'];
    }
    $shipping_cost = $this->S->dao('shipping_cost');
    if ($weight){
        $shipping_freight   = $this->S->dao('shipping_freight');
        $datalist           = $shipping_freight->get_datalist($sql);
        $exchange_rate      = $this->C->service('exchange_rate'); 
        foreach($datalist as $k=>&$val){
            if($val['sendtime']){
                $sendtimeArr	= explode(',',$val['sendtime']);
                $val['sendtime']= ($sendtimeArr['0']?$sendtimeArr['0'].'d':'').'~'.($sendtimeArr['1']?$sendtimeArr['1'].'d':'');
            }
            $newweight = 0;
            if ($val['basis_height'] == 1){
                if ($volume){
                    $newvolume = str_replace('x','*',$volume);
                    eval("\$_volume = $newvolume;");
                    $newweight = $_volume/5000;
                }
            }
            $_weight = $newweight>$weight?$newweight:$weight;
            if ($val['unit']=='g'){
                $_gweight =$_weight*1000;
            }else{
                $_gweight = $_weight;
            }
            //重量*数量 再去计算min_weight 和 max_weight之间的运费
            if(!empty($quantity)){$_gweight = $_gweight*$quantity;}    
            
            if ($val['expression']){
                $c_f = $val['from'] == '0'?'1':($val['from']==$f?'1':'0');
                $c_t = $val['to'] == '0'?'1':($val['to']==$t?'1':'0');
                if($c_f && $c_t){
                    $_str = strtr($val['expression'],array('x'=>$_gweight));
                    eval("\$newstr = $_str;");
                    $_quantity = !$quantity?'1':$quantity;
                    $sumcost = $newstr * $_quantity;
                    $val['cost'] =  $val['expression']?$sumcost:'0.00';
                }
            }else{        
               /*if ($city = substr($t,0,9).'00'){
                    $ftwhere = '`from` ='.$f.' and `to`='.$city.'';
                    $condition  = ' and min_weight<='.$weight.' and max_weight>'.$weight.' and '.$ftwhere.' and shipping_id ='.$val['shipping_id'];
                    $data       = $shipping_cost->D->get_allstr($condition,'','','cost');
                    if ($data) {$isflag = true;array_push($ardata,$data);}
               }
               if ($province = substr($t,0,7).'0000'){
                    $ftwhere = '`from` ='.$f.' and `to`='.$province.'';
                    $condition  = ' and min_weight<='.$weight.' and max_weight>'.$weight.' and '.$ftwhere.' and shipping_id ='.$val['shipping_id'];
                    $data       = $shipping_cost->D->get_allstr($condition,'','','cost');
                    if ($data && $isflag != true) {$isflag = true;array_push($ardata,$data);}
               }
               if ($nation = substr($t,0,3).'00000000'){
                    $ftwhere = '`from` ='.$f.' and `to` ='.$nation.'';
                    $condition  = ' and min_weight<='.$weight.' and max_weight>'.$weight.' and '.$ftwhere.' and shipping_id ='.$val['shipping_id'];
                    $data       = $shipping_cost->D->get_allstr($condition,'','','cost');
                    if ($data && $isflag != true){ $isflag = true;array_push($ardata,$data);}
               }    
    
               if ($isflag == true){
                print_r($ardata[0][0]['cost']);
                    $val['cost'] = $ardata[0][0]['cost']?$ardata[0][0]['cost']:'0.00';
               }*/
                 
             
               $condition  = ' and min_weight<'.$_gweight.' and max_weight>='.$_gweight.' and `from` ='.$f.' and `to`='.$t.' and shipping_id ='.$val['shipping_id'];
               $data = $shipping_cost->D->get_allstr($condition,'','','cost,ctype'); 
               if ($data[0]['ctype'] == 'per_price')
                    $val['cost'] = $data[0]['cost'] * $_gweight;
                else
                    $val['cost'] = $data[0]['cost']; 
            }
            
            if ($currency){
                //if (!empty($quantity))
                //$val['cost']       = $val['cost'] * $quantity;
                $val['cost']  = $exchange_rate->change_rate($val['coin_code'],$currency,$val['cost']);
                $val['fuel_surcharge'] = $val['cost'] * $val['fuel_surcharge']/100;
                $val['operating_cost'] = $exchange_rate->change_rate($val['coin_code'],$currency,$val['operating_cost']);
                $val['registered_fee'] = $exchange_rate->change_rate($val['coin_code'],$currency,$val['registered_fee']);
            }
            
            $val['account']         = number_format($val['cost'] + $val['fuel_surcharge'] + $val['operating_cost'] + $val['registered_fee'],2);
            $val['cost']            = number_format($val['cost'],2);
            $val['fuel_surcharge']  = number_format($val['fuel_surcharge'],2);
            $val['operating_cost']  = number_format($val['operating_cost'],2);
            $val['registered_fee']  = number_format($val['registered_fee'],2);
            $val['basis_height']= ($val['basis_height'] == '1')?'体积重、实重':'实重';
            
            if ($val['cost']== '0'){
                unset($datalist[$k]);
            }
        }
    }
    
    $tablewidth = '1000';
    $displayarr['s_name']           = array('showname'=>'物流方式','width'=>'100');
    $displayarr['cost']             = array('showname'=>'基本费用','width'=>'80');
    $displayarr['fuel_surcharge']   = array('showname'=>'燃油附加费','width'=>'80');
    $displayarr['operating_cost']   = array('showname'=>'操作费','width'=>'80');
    $displayarr['registered_fee']   = array('showname'=>'挂号费','width'=>'80');
    $displayarr['account']          = array('showname'=>'总费用','width'=>'80');
    $displayarr['sendtime']         = array('showname'=>'递用时间','width'=>'80');
    $displayarr['tips']             = array('showname'=>'可追踪','width'=>'60');
    $displayarr['basis_height']     = array('showname'=>'体积计算/实重计算','width'=>'120');
            
    $this->V->mark(array('title'=>'运费查询'));
    $temp = 'pub_list';
    
    $jslink .= "<script src='./staticment/js/shipping_show.js'></script>\n";
    $jslink .= "<script src='./staticment/js/freight.js'></script>\n";
}

elseif ($detail == 'getskuinfo') {
    $data   = $this->S->dao('product')->D->get_one(array('sku'=>$sku),'product_dimensions,shipping_weight');
    echo json_encode($data);
}

if ($detail =='list') {

    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L');
}
?>
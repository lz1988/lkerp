<?php
/*
 * Created on 2011-10-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class shipping_fareService extends S{

    public function reset_shipping_fare($stockware, $shipping_com, $shipping_type, $shipping_datas){
		$ret = 0;
		$ap = $this->S->dao('shipping_fare');
		//删除对应的数据
		$ap->D->delete_by_field(array('shipping_com'=>$shipping_com, 'type'=>$shipping_type));

		//插入对应的数据
		if($stockware=='中国仓库-蛇口'){//处理fedex,ups
			$fields = get_object_vars($shipping_datas[2]);
			foreach($shipping_datas as $col=>$obj_da){
				$da = get_object_vars($obj_da);
				//第一条数据取对应的属性名称
				if($col>2){
					foreach($da as $key=>$value){
						if($fields[$key]=='cal_type'){
							$cal_type = '';
							if($value=='total_price'){
								$cal_type = 1;
							}
							if($value=='per_kg_price'){
								$cal_type = 2;
							}
						}elseif($fields[$key]=='min_weight'){
							$min_weight = $value;
						}elseif($fields[$key]=='max_weight'){
							$max_weight = $value;
						}else{//更新数据
							$area_code = $fields[$key];
							$price = $value;
							$fee = '';
							$ap->D->insert(array('shipping_com'=>$shipping_com, 'type'=>$shipping_type, 'min_weight'=>$min_weight, 'max_weight'=>$max_weight, 'area_code'=>$area_code, 'price'=>$price, 'fee'=>$fee, 'cal_type'=>$cal_type));
							$ret++;
						}
					}
				}
			}
		}
		return $ret;
    }

    public function getArea($val,$inputname){

        $backdataNation = $this->muiltilevel_nation($val,$inputname);
        $backdataProvinc= $this->muiltilevel_province($val,$inputname);
        $backdataCity   = $this->muiltilevel_city($val,$inputname);

        return $backdataNation.$backdataProvinc.$backdataCity;

    }

    //获取级联菜单-国家
    public function muiltilevel_nation($nationid,$inputname){
        $nationid = substr($nationid,0,3).'00000000';
        $shipping_area  = $this->S->dao('shipping_area');
        $nation_data    = $shipping_area->D->get_allstr(' and grade=-1','','','id,nation');
        $armuitilevel .= '国家&nbsp;<select onchange=getarea(this.value,"'.$inputname.'") class="check_notnull" name="nation_'.$inputname.'" style="width:80px;"><option value="-1">请选择</option>';
        foreach($nation_data as $nval){
            $selected = ($nval['id'] == $nationid)?'selected':'';
            $armuitilevel .= '<option value="'.$nval['id'].'" '.$selected.'>'.$nval['nation'].'</option>';
        }
        $armuitilevel .= '</select>';

        return $armuitilevel;
    }

    //获取级联菜单-省份
    public function muiltilevel_province($id,$inputname,$v=''){

        $newid = substr($id,0,7).'0000';
        $shipping_area  = $this->S->dao('shipping_area');
        if ($id){
            $id =   substr($id,0,3);
        }
        $data           = $shipping_area->D->get_allstr('and substring(id,1,3)="'.$id.'"  and grade=0','province','','substring(id,1,3) as id,id,province');

        $armuitilevel.= $v?'':'<span id="province_'.$inputname.'">';
        $armuitilevel.= '&nbsp;&nbsp;省份&nbsp;<select onchange=getprovince(this.value,"'.$inputname.'") style="width:80px;" name="province_'.$inputname.'"><option value="-1">请选择</option>';
        foreach($data as $pval){

            $selected = ($pval['id'] == $newid)?'selected':'';
            $armuitilevel .= '<option value="'.$pval['id'].'" '.$selected.'>'.$pval['province'].'</option>';
        }
        $armuitilevel.='</select>';
        $armuitilevel.= $v?'':'</span>';

        return $armuitilevel;
    }

    //获取级联菜单-城市
    public function muiltilevel_city($id,$inputname,$v = ''){
        $newid = $id;
        $shipping_area  = $this->S->dao('shipping_area');
        if ($id){
            $id = substr($id,5,2);
        }

        $data           = $shipping_area->D->get_allstr('and substring(id,6,2)="'.$id.'"  and grade=1','','','substring(id,6,2),id,city');

        $armuitilevel .= $v?'':'<span id="city_'.$inputname.'">';
        $armuitilevel.= '&nbsp;&nbsp;城市&nbsp;<select onchange=getcity(this.value,"'.$inputname.'") style="width:80px;" name="city_'.$inputname.'"><option value="-1">请选择</option>';
        foreach($data as $pval){
            $selected = ($pval['id'] == $newid)?'selected':'';
            $armuitilevel .= '<option value="'.$pval['id'].'" '.$selected.'>'.$pval['city'].'</option>';
        }
        $armuitilevel .= '</select>';
        $armuitilevel.= $v?'':'</span>';
        return $armuitilevel;
    }

    /*运费调用公共方法*/
    /*start 是否格式化*/
    /*$quantity 数量*/
    public function getshipping_cost($weight, $from ,$to ,$shipping_id,$coin_code,$quantity=1,$start=false){
        $shipping_freight   = $this->S->dao('shipping_freight');
        $shipping_cost      = $this->S->dao('shipping_cost');
        $exchange_rate      = $this->C->service('exchange_rate');

        $where  = ' and (`from`='.$from.' or `from`="0") and (`to`='.$to.' or `to` ="0") and shipping_id='.$shipping_id.'';
        $result = $shipping_freight->D->get_allstr($where,'','','*');


        if ($result[0]['unit']=='g'){
            $_gweight =$weight*1000;
        }else{
            $_gweight = $weight;
        }
         //重量*数量 再去计算min_weight 和 max_weight之间的运费
        $quantity = $quantity>0?$quantity:1;
        $_gweight = $_gweight*$quantity;   
        
        $expression = $result[0]['expression'];
        if ($expression){
            $_str = strtr($expression,array('x'=>$_gweight));
            eval("\$cost = $_str;");
            $cost = $cost?$cost:'0.00';
        }else{
            $condition  = ' and min_weight<'.$_gweight.' and max_weight>='.$_gweight.' and `from` ='.$from.' and `to` ='.$to.' and shipping_id ='.$shipping_id;
            $data       = $shipping_cost->D->get_allstr($condition,'','','cost,ctype');
            if ($data[0]['ctype'] == 'per_price')
                $cost = $data[0]['cost'] * $_gweight;
            else
                $cost = $data[0]['cost'];
        }

        $fuel_surcharge = $cost * $result[0]['fuel_surcharge']/100;
        $account        = $cost + $result[0]['operating_cost'] + $result[0]['registered_fee'] + $fuel_surcharge;
        
        if($start){
            $account = $exchange_rate->change_rate($result[0]['coin_code'],$coin_code,$account);
        }else{
            $account =  number_format($exchange_rate->change_rate($result[0]['coin_code'],$coin_code,$account),2);
        } 
        return $account;

    }

 }
?>

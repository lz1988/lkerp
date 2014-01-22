<?php
/*
 * Created on 2011-12-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class warehouseService extends S{

 	/**
 	 *生成仓库下拉
 	 *
 	 *@param string $showname 下拉的name
 	 *@param string $name 显示的名称，填字段名
 	 *@param string $vval option的值,填字段名
 	 *@param string $depend 依据来默认选中的名称
 	 *@param string $dval 依据来默认选中的值
 	 */
 	public function get_whouse($showname,$name,$vval,$depend,$dval){

		$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
		$wback = '';
		$wback = '<select name='.$showname.'>';
		$wback.= '<option value=>=请选择仓库=</option>';
		foreach ($wdata as $val){
			if($depend&&$dval){$selected = ($val[$depend]==$dval)?'selected':'';}
			$wback.= '<option value='.$val[$vval].' '.$selected.'>'.$val[$name].'</option>';
		}
		$wback.= '</select>';
		return $wback;
 	}

	//获取供应商名称列表，参数分别表示show_supplier_select($name搜索名称,$show_hidden_supplier_id显示显示Hidden服务商id, $showdiv是否显示div)
 	public function show_supplier_select($name,$show_hidden_supplier_id, $showdiv){
		if($name){
			$InitPHP_conf['pageval'] = 10;
			$datalist = $this->S->dao('esse')->D->get_list(' and type=3 and name like"%'.$name.'%"','','','id,name');
			$outstr = '<table id=suplist cellspacing=2 width=100%>';
			foreach ($datalist as $val){
				$val['name'] = str_replace($name,'<b>'.$name.'</b>',$val['name']);
				if($showdiv){
					$outstr.='<tr onmouseover="this.style.background=\'#E2EAFF\'" onmouseout="this.style.background=\'#fff\'"  onclick=select(this.childNodes[0],'.$val['id'].',1)><td class=point>'.$val['name'].'</td></tr>';
				}else{
					if($show_hidden_supplier_id){
						$outstr.='<tr onmouseover="this.style.background=\'#E2EAFF\'" onmouseout="this.style.background=\'#fff\'" onclick=select(this.childNodes[0],'.$val['id'].',0)><td class=point>'.$val['name'].'</td></tr>';
					}else{
						$outstr.='<tr onmouseover="this.style.background=\'#E2EAFF\'" onmouseout="this.style.background=\'#fff\'" onclick=select(this.childNodes[0],0,0)><td class=point>'.$val['name'].'</td></tr>';
					}
				}
			}
			$outstr.='</table>';
			return $outstr;
		}
 	}

	/**
	 * title-将数组中的压缩内容解压并作为字段增加入数据中
	 * $datalist-处理的数组
	 * $i：0一维数组，非0二维数组。
	 */
 	public function decodejson($datalist,$i=''){

		if($i===''){
			$extends = json_decode($datalist['extends'],true);
			$extendskeys = array_keys($extends);
			for($j=0;$j<count($extendskeys);$j++){$datalist["$extendskeys[$j]"] = $extends["$extendskeys[$j]"];}
			return $datalist;
		}else{
			$extends = json_decode($datalist[$i]['extends'],true);
			$extendskeys = array_keys($extends);
			for($j=0;$j<count($extendskeys);$j++){$datalist[$i]["$extendskeys[$j]"] = $extends["$extendskeys[$j]"];}
			return $datalist[$i];
		}

 	}

	/**
	 * title-获取最大单号并加上1
	 * @param string 单类型
	 * @param string 前辍
	 * @param obj 表对象
	 *
	 */
	public function get_maxorder($property,$pre,$process){

		$max = $process->maxorder(' and property="'.$property.'"');
		$order_id = $pre.sprintf("%07d",substr($max,1)+1);
		return $order_id;

	}

	/*像进仓，出仓单这种有不同字母开头的同一种单*/
	public function get_maxorder_manay($property,$pre,$process,$tablename){

		$tablename = empty($tablename)?'process':$tablename;
		$sqlstr	   = empty($property)?'':' and property="'.$property.'" ';

		$sql = 'select max( SUBSTRING( order_id, 2, 7 ) ) as order_id from '.$tablename.' where 1 '.$sqlstr;
		$max =  $process->D->get_one_sql($sql);
		$worder_id = $pre.sprintf("%07d",$max['order_id']+1);
		return $worder_id;
	}


	/*同组权限判断*/
	public function check_thegroup_right($name,$value,$isajax = 0){

		//$backsoldway = $this->S->dao('admin_group')->D->get_one_by_field(array('id'=>$_SESSION['groupid']),'`desc`');
		$process  = $this->S->dao('process');
		$backcomeway = $process->D->get_one_by_field(array("$name"=>$value),'cuser');
		if($_SESSION['eng_name'] != $backcomeway['cuser']) {
			$msg = '只能对自己的订单执行操作';
			if($isajax == '0'){echo $msg;exit();}else{exit("<meta http-equiv='Refresh' content='"."0".";URL=index.php?action=msg&detail=pronounce&content=".urlencode($msg)."'>");}
			//exit();
		};
	}

	/*生成退款原因下拉*/
	public function get_backreaseon_html($name){
		$reasonarr = array('unsatisfactory_with_item'=>'Unsatisfactory with item','wrong_purchase'=>'Wrong purchase','sent_wrong_item'=>'Sent wrong item','sent_less_item'=>'Sent less item','warehouse_delay_shipment'=>'Warehouse Delay shipment','lost_on_the_way'=>'Lost on the way','damage_in_delivery'=>'Damage in delivery','carrier_delay_shipment'=>'Carrier Delay shipment','defective_item'=>'Defective item','provide_wrong_description_on_listing'=>'Provide wrong description on listing','mislead_customer_on_listing'=>'Mislead customer on listing','provide_wrong_product_info/tech_support'=>'Provide wrong product info/tech support','others'=>'others');
		$reasonarr_keys = array_keys($reasonarr);

		$reason_datastr = '<select name='.$name.'>';
		$reason_datastr.= '<option value=>=请选择原因=</option>';
		for($i=0;$i<count($reasonarr_keys);$i++){
			$reason_datastr.= '<option value='.$reasonarr_keys[$i].'>'.$reasonarr[$reasonarr_keys[$i]].'</option>';
		}
		$reason_datastr.='</select>';
		return $reason_datastr;
	}

	/*取消原因数组管理*/
	public function get_canceltype($type,$value,$selval=''){
		$reason_arr = array (''=>'=选择取消原因=','1'=>'库存不足','2'=>'客户取消','3'=>'客户换货','4'=>'客户商议退款','5'=>'收货地址无效','6'=>'其它渠道发货','7'=>'同品替代发货','8'=>'已重新做单','9'=>'未收到付款','10'=>'无法采购');
		if($type == 0){return $reason_arr;}
		elseif($type == 1){
			switch ($value){
			case '1':$value = '库存不足';break;
			case '2':$value = '客户取消';break;
			case '3':$value = '客户换货';break;
			case '4':$value = '客户商议退款';break;
			case '5':$value = '收货地址无效';break;
			case '6':$value = '其它渠道发货';break;
			case '7':$value = '同品替代发货';break;
			case '8':$value = '已重新做单';break;
			case '9':$value = '未收到付款';break;
			case '10':$value= '无法采购';break;
			}
			return $value;
		}
		elseif($type == 2){

			$reasonarr_keys = array_keys($reason_arr);

			$reason_datastr = '<select name="'.$value.'">';
			for($i=0;$i<count($reasonarr_keys);$i++){
				$selected = $reasonarr_keys[$i] == $selval?'selected':'';
				$reason_datastr.= '<option value="'.$reasonarr_keys[$i].'" '.$selected.'>'.$reason_arr[$reasonarr_keys[$i]].'</option>';
			}
			$reason_datastr.='</select>';
			return $reason_datastr;
		}
	}

	/*生成币种选择下拉*/
	public function get_coincode_html($name,$extra = '',$selectstr = ''){
		$backlist = $this->S->dao('exchange_rate')->D->get_allstr(' and isnew="1" ','','id asc','c_name,code');
		$coinhtml = '<select name="'.$name.'" '.$extra.'>';
		$coinhtml.= '<option value=>=请选择币别=</option>';

		foreach ($backlist as $val){
			$coinhtml.= '<option value='.$val['code'].' '.($selectstr == $val['code']?'selected':'').'>'.$val['code'].'-'.$val['c_name'].'</option>';
		}
		$coinhtml.= '</select>';
		return $coinhtml;

	}

	/*
	 * create on 2012-07-20
	 * by wall
	 * 生成带汇率的币种选择下拉，暂用于备货操作
	 * */
 	public function get_coincode_rate_html($attr, $selectstr){
		$backlist = $this->S->dao('exchange_rate')->D->get_allstr(' and isnew="1" ','','id asc','rate,code');
		$coinhtml = '<select '.$attr.' >';
		$coinhtml.= '<option value=>---</option>';

		foreach ($backlist as $val){
			$coinhtml.= '<option value='.$val['rate'].' '.($selectstr == $val['code']?'selected':'').' >'.$val['code'].'</option>';
		}
		$coinhtml .= '</select>';
		return $coinhtml;

	}

	/**
	 * 每页显示多少条的公供调用，注意需要JS作跳转支持
	 *
	 */
	public function perpage_show_html($pagearr,$selfval_set,$statu,$extra = ''){
		global $InitPHP_conf;
		$pageEveyval = $_REQUEST['action'].'_pageval';//不同的action页生成不同的自定义分页数

		if(empty($selfval_set) && empty($_SESSION[$pageEveyval])){
			$_SESSION[$pageEveyval]	= 15;
		}elseif(!empty($selfval_set) && !empty($_SESSION[$pageEveyval])){
			$_SESSION[$pageEveyval]	= $selfval_set;
		}
		$InitPHP_conf['pageval'] = $_SESSION[$pageEveyval];

		/*HTML生成*/
		$showperhtml = '<select onchange=jumppage(this.value,"'.$statu.'","'.$_GET['action'].'","'.$extra.'")>';
		for($i=0;$i<count($pagearr);$i++){
			$per_selected = $_SESSION[$pageEveyval]==$pagearr[$i]?'selected':'';
			$showperhtml.='<option value='.$pagearr[$i].' '.$per_selected.'>'.$pagearr[$i].'条'.'</option>';
		}
		$showperhtml.= '</select>/页';
		return $showperhtml;
	}

	/**
	 *取得当前币种对美元的汇率*
	 *return 期号，汇率
	 */
	public function get_stage_rate($coin_code){
		return $this->S->dao('exchange_rate')->D->get_one_by_field(array('code'=>$coin_code,'isnew'=>1),'stage_rate,rate');
	}

 }
?>

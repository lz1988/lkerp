<?
/**
 * 运费估算
 * by hanson
 */
if($detail == 'price'){


	/*标记变量与定义模板*/
	$this->V->mark(array('title'=>'运费估算'));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');

	/*表单配置*/
	$conform = array('method'=>'post','action'=>'index.php?action=shipping_count&detail=price','width'=>'100%','extra'=>'onsubmit=\'return&nbsp;countmod()\' id=countform');
	$colwidth = array('1'=>'25%','2'=>'60%','3'=>'15%');//不设置时，默认是25%,50%,25%

	$shipping_code = $this->S->dao('shipping_code');
	$countrylist = $shipping_code->D->get_all('','','','country');

	/*生成目的地下拉,默认美国东部*/
	$counthtml = '<select name=country>';
	$counthtml.= '<option value=\'\'>==请选择国家==</option>';
	foreach($countrylist as $key=>$val){
		$selected =  $val['country']=='United States East'?'selected':'';
		$counthtml.='<option value="'.$val['country'].'" '.$selected.'>'.$val['country'].'</option>';
	}
	$counthtml.= '</select>';

	/*生成始发地下拉*/
	//$sourcehtml = '<select name=scountry>';
	//$sourcehtml.= '<option value=中国仓库>中国仓库</option><option value=Amazon-US仓库>Amazon-US仓库</option><option value=美国仓库>美国仓库</option><option value=Amazon-UK仓库>Amazon-UK仓库</option>';
	//$sourcehtml.= '</select>';


	/*取得仓库下拉--Start*/
	$datalist = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	$sourcehtml = '<select name=stockware>';

	foreach ($datalist as $val){
		$selected = ($val['name']=='中国仓库-蛇口')?'selected':'';
		$sourcehtml.= '<option value='.$val['name'].' '.$selected.'>'.$val['name'].'</option>';
	}
	$sourcehtml.= '</select>';
	/*--End*/


	/*生成转换货币下拉，默认人民币*/
	$datalist = $this->S->dao('exchange_rate')->D->get_all(array('isnew'=>'1'));
	$currencyhtml = '<select name=currency>';
	$currencyhtml.= '<option value=\'\'>==选择币种==</option>';
	foreach ($datalist as $val){
		$selected =  $val['code']=='CNY'?'selected':'';
		$currencyhtml.= '<option value="'.$val['code'].'" '.$selected.'>'.$val['c_name'].'-'.$val['code'].'</option>';
	}
	$currencyhtml.= '</select>';


	/**
	 * 表单内容:
	 * showname		显示的元素。
	 * showtips		最右侧的提示。
	 * extra		位于input右的额外的输出信息。
	 * inextra		位于Input里的额外信息。
	 * datatype		表单类型，不写默认为type=text，h-hidden,不会在页面显示,se-自定义的如select,radio。
	 * value		初始值。
	 * datastr      自定义的内容。
	 */
	$p_extra = '<span class="tips">cm</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weight：<input type="text" name="shipping_weight"><span class="tips">kg</span>';
	$b_extra = '<span class="tips">cm</span>&nbsp;Weight(box)：<input type="text" name="box_shipping_weight"><span class="tips">kg</span>';
	$sku_extra = ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; Quantity：<input type="text" name="quantity" value>';
	$cu_extra = ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Unit_box：<input type=text name=unit_box value><span class="tips">unit_box</span>';

	$disinputarr = array();
	$disinputarr['fromplace'] = array('showname'=>'Warehouse','datatype'=>'se','datastr'=>$sourcehtml,'extra'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO &nbsp; &nbsp;Country：'.$counthtml);
	$disinputarr['sku'] = array('showname'=>'SKU','inextra'=>'onblur=getparse()','extra'=>$sku_extra);
	$disinputarr['product_dimensions'] = array('showname'=>'length×width×height','extra'=>$p_extra,'showtips'=>'<input type="radio" name="type"  value="1" title="单个" checked="checked" id="radio"/>');
	$disinputarr['box_product_dimensions'] = array('showname'=>'length×width×height(box)','extra'=>$b_extra,'showtips'=>'<input type="radio" name="type"  value="2" title="一箱" id="radio"/>');
	$disinputarr['currency'] = array('showname'=>'Currency','datatype'=>'se','datastr'=>$currencyhtml,'extra'=>$cu_extra);


	/*生成输出表单*/
	$bannerstr ='<button onclick=window.location="index.php?action=shipping_count&detail=infare">导入运费</button>';
	$temp = 'pub_edit';


	/*JS包含*/
	$jslink = "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink.= "<script src='./staticment/js/shippingfare.js' charset='utf-8'></script>\n";
	$jslink.= "<script src='./staticment/js/new.js'></script>\n";

}

/*导入运费*/
elseif($detail == 'infare'){

	/*取得仓库下拉--Start*/
	$datalist = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	$sourcehtml = 'stockware:<select name=stockware onchange=\'document.form-infare.submit();\'>';

	foreach ($datalist as $val){
		$selected = ($val['name']=='中国仓库-蛇口')?'selected':'';
		$sourcehtml.= '<option value='.$val['name'].' '.$selected.'>'.$val['name'].'</option>';
	}
	$sourcehtml.= '</select>';
	/*--End*/

	$tablelist = '';
	$upload_ok = 0;
	$upload_file=$_FILES["upload_file"]["name"];        //获取文件名
	$upload_tmp_file=$_FILES["upload_file"]["tmp_name"];      //获取临时文件名
	$upload_filetype=$_FILES["upload_file"]["type"];    //获取文件类型
	$upload_status=$_FILES["upload_file"]["error"];   //获取文件出错情况
	$upload_dir="./data/uploadexl/";               //指定文件存储路径
	$errorchar=array ("-"," ","~","!","@","#","$","%","^","&","(",")","+",","," （","）","？","！","《","》","：","；","——");//非法字符
	foreach($errorchar as $char)
	{
		if(strpos($upload_file,$char)){
			$upload_file=str_replace($char,"_",$upload_file);
		}
	}//循环排除替换文件名中的非法字符
	$upload_path=$upload_dir.time().$upload_file;   //定义文件最终的存储路径和名称
	if(is_uploaded_file($upload_tmp_file) ){
		switch($upload_status){
			case 0:$message_upload="";break;
			case 1:$message_upload="上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。";break;
			case 2:$message_upload="上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。";break;
			case 3:$message_upload="文件只有部分被上传。";break;
			case 4:$message_upload="没有文件被上传。";break;
			case 6:$message_upload="没有找到临时文件目录。";break;
			case 7:$message_upload="文件写入失败。";break;
		}                   //分析文件出错情况并给出提示
		if(file_exists($upload_path)){
			$message_upload="同名文件已经存在，请修改你要上传的文件名！";              //检查是否有相同文件存在
		}else if(move_uploaded_file($upload_tmp_file,$upload_path)){
			 $message_upload="文件已经成功上传，请核对下面的数据是否正常";
			 $upload_ok = 1;//读取已经成功上传的文件
			if($upload_ok){//检查数据是否正确
				require_once('lib/PHPExcel.php');
				$php_excel_obj = new PHPExcel();
				$php_reader = new PHPExcel_Reader_Excel2007();

				$file_name = $upload_path;

				if(!$php_reader->canRead($file_name)){
					$php_reader= new PHPExcel_Reader_Excel5();
					if(!$php_reader->canRead($file_name)){
						$message_upload = 'NO Excel!';
					}
				}
				$php_excel_obj = $php_reader->load($file_name);
				$current_sheet =$php_excel_obj->getSheet(0);

				$all_column =$current_sheet->getHighestColumn();
				$all_row =$current_sheet->getHighestRow();

				$all_arr = array();
				$c_arr = array();

				//字符对照表
				for($r_i = 1; $r_i<=$all_row; $r_i++){
					$c_arr= array();
					for($c_i= 'A'; $c_i<= $all_column; $c_i++){
						$adr= $c_i . $r_i;
					    $value= $current_sheet->getCell($adr)->getValue();
						if($c_i== 'A' && empty($value) ) break;
						if(is_object($value)) $value= $value->__toString();
						$c_arr[$c_i]= $value;
					}
					$all_arr[] =  $c_arr;
				}//excel表数据转化为数组

				 $tablelist = '';
				 $tablelist .= '<table border="1">';
				//处理数组
				$cur_com_type = array('Fedex'=>array('ie', 'ip'), 'ups'=>array('Express', 'Expedited'),'DHL'=>array('none'));
				$cur_com_area = array('Fedex'=>array('A', 'B', 'D', 'E', 'F', 'G', 'H', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z', '1US-West', '2US-East'), 'ups'=>array(11, 12, 13, 14, 15, 16, 17, 18, 19),'DHL'=>array(6,7));
				$exl_row = 0;
				$data_error = 0;
				foreach($all_arr as $k=>$val){
					$exl_row++;
					$tablelist .= '<tr>';

					if( is_array($val) ){
						foreach( $val as $j=>$value) {
							$error_style = '';

							//检查物流公司和物流方式是否正确
							if($exl_row==2){
								if($j=='A'){//判断物流公司是否正确
									if(!array_key_exists($value, $cur_com_type)){
										$error_style = ' bgcolor="red" title="物流公司名称不正确"';
									}else{
										$cur_com = $value;
									}
								}

								if($j=='B'){
									if(!in_array($value, $cur_com_type[$cur_com])){
										$error_style = ' bgcolor="red" title="物流公司对应的物流方式不正确"';
									}else{
										$set_com_type = $value;
									}
								}
							}

							//检查物流公司和对应的编码是否正确
							if($exl_row==3){
								if(!in_array($j, array('A', 'B', 'C')) && !in_array($value, $cur_com_area[$cur_com])){
									$error_style = ' bgcolor="red" title="物流公司对应区域编码不正确"';
								}
							}

							//检查重量是否一致
							if($j=='B' && $exl_row>3){
								$min_weight = $value;
								if($min_weight!=$max_weight){
									$error_style = ' bgcolor="red" title="重量最小值跟上一条记录的最大值不相等"';
								}
							}
							if($j=='C'){
								$max_weight = $value;
							}
							//结束检查重量

							//检查数值是否正常
							if($j!='A' && $exl_row>3 && !is_numeric($value)){
								$error_style = ' bgcolor="red" title="数据为非数字"';
							}
							if($error_style){ $data_error++;}
							$tablelist .= '<td '.$error_style.'>'.$value.'</td>';
						}
					}
					$tablelist .= '</tr>';
				}
				$tablelist .= '</table>';

				if(!$data_error){
					$tablelist .= '<input type="hidden" name="stockware" value='.urlencode($stockware).' />';
					$tablelist .= '<input type="hidden" name="shipping_com" value='.$cur_com.' />';
					$tablelist .= '<input type="hidden" name="shipping_type" value='.$set_com_type.' />';
					unset($all_arr[0]);
					unset($all_arr[1]);
					$tablelist .= '<input type="hidden" name="exl_datas" value='.json_encode($all_arr).' />';
					$tablelist .= '<input type="submit" value="确认并提交"><input type="reset" value="取消">';
				}else{
					$tablelist .= '总共有'.$data_error.'处错误，请修正后再提交。';
				}
			}
		}else{
			 $message_upload="文件上传失败。";
		}
	}

	//处理提交的数据并更新数据库
	if($_POST['shipping_com']){

		$exl_datas = get_object_vars(json_decode(stripslashes($_POST['exl_datas'])));
		$stockware = urldecode($stockware);
		if($this->C->service('shipping_fare')->reset_shipping_fare($stockware, $shipping_com, $shipping_type, $exl_datas)){
			echo "<script>alert('数据已经成功导入！')</script>";
		}
	}

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('sourcehtml'=>$sourcehtml, 'title'=>'运费导入-运费估算(price)', 'message_upload'=>$message_upload, 'tablelist'=>$tablelist));
	$this->V->set_tpl('adminweb/shipping_infare');
	display();
}

/*取出某SKU的长宽高重量等属性*/
elseif($detail == 'getparse'){

	$product = $this->S->dao('product');
	$parses = $product->D->get_one_by_field(array('sku'=>$sku),'product_dimensions,shipping_weight,box_product_dimensions,box_shipping_weight,unit_box');
	$parses = json_encode($parses);
	echo $parses;
}


/*计算并显示搜索结果*/
elseif($detail == 'list'){

	/**
	 *@param string $bannerstr--位于搜索与列表之间的内容。
	 *@param array $xxarr--下拉的框数组。	 *
	 *@param array $quittype-- 取得两个不同快递的地区编号。
	 */
	$quantity = empty($mydetail['quantity'])?1:$mydetail['quantity'];//个数，默认为1
	$stockware = $mydetail['stockware'];

	/*取得重量，比较体积重与实重，取大值*/
	$count_weight = 1;
	$dimensions = $mydetail['type']==1?$mydetail['product_dimensions']:$mydetail['box_product_dimensions'];//取得单个或者一箱的规格
	$dimenarr = explode('x',$dimensions);


	$real_weight = $mydetail['type']==1?$mydetail['shipping_weight']:$mydetail['box_shipping_weight'];//实体重


	/*中国发货的运费计算-Start*/
	if($stockware == '中国仓库-蛇口'){
		for($i=0;$i<count($dimenarr);$i++){
			$count_weight*= $dimenarr[$i];//长宽高相乘
		}
		$count_weight = $count_weight/5000;	//体积重

		$weight = $count_weight>$real_weight?$count_weight:$real_weight;
		$weight = $weight*$quantity;//乘以个数得出新的重量。


		/*根据重量取得不同物流的运费*/
		$shipping_code = $this->S->dao('shipping_code');
		$quittype = $shipping_code->D->get_one_by_field(array('country'=>$mydetail['country']),'upsArea,fedexArea,DHL');

		$datalist = $this->S->dao('shipping_fare')->farelist($weight,$quittype['upsArea'],$quittype['fedexArea'],$quittype['DHL']);

		/*结果处理--Start*/
		$extrafare = array('ups'=>1.20,'Fedex'=>1.19,'DHL'=>1.24);  //燃油附加费
		foreach($datalist as $key=>&$val){


			/*计算运费,区别是单个运费还是总运费*/
			if($val['shipping_com'] == 'ups'){
				if($val['cal_type']==1){
					$val['price']*=$extrafare['ups'];
				}elseif($val['cal_type']==2){
					$val['price']*=$extrafare['ups']*$weight;
				}
			}

			elseif($val['shipping_com'] == 'Fedex'){
				if($val['cal_type']==1){
					$val['price']*=$extrafare['Fedex'];
				}elseif($val['cal_type']==2){
					$val['price']*=$extrafare['Fedex']*$weight;
				}
			}

			elseif($val['shipping_com'] == 'DHL'){
				if($val['cal_type']==1){
					$val['price']*=$extrafare['DHL'];
				}elseif($val['cal_type']==2){
					$val['price']*=$extrafare['DHL']*$weight;
				}
			}

			/*估算时间*/
			switch ($val['type'])
			{
				case 'Express':$val['days'] = '2d~4d';	break;
				case 'Expedited':$val['days'] = '4d~7d';break;
				case 'ie':$val['days'] = '4d~7d';break;
				case 'ip':$val['days'] = '2d~4d';break;
			}
			if($val['shipping_com'] == 'DHL') {
				$val['days'] = '3d~4d';//DHL天数
				$val['type'] = '--';
			}

			$val['guahao'] = 'Y';
			$val['Currency'] = 'CNY';
			$val['price'] = number_format($val['price'],2);

		}

		$currency = empty($mydetail['currency'])?'CNY':$mydetail['currency'];//取得币种代码,默认CNY

		/*计算另外两个快递HK小包与E邮宝的运费，不需读库,不需分体积重与实重,直接乘以每公斤价格，保留两位小数*/

		$ems_fare = ($real_weight*$quantity <= 0.06)?4.8+7 : 80*$real_weight*$quantity+7;//E邮宝的60克以内(含60g)4.8元,61克以上80元/公斤。+每件7元的操作费

		$extra_price =array('price_hk_n'=>number_format($real_weight*$quantity*75,2),'price_hk_y'=>number_format($real_weight*$quantity*100+13,2),'price_e_n'=>number_format($ems_fare,2));


		/*HK小包与E邮宝的额外处理*/
		if($datalist){
		$datalist[] = array('shipping_com'=>'HK小包','type'=>'HK小包','price'=>$extra_price['price_hk_n'],'Currency'=>$currency,'days'=>'15~20d','guahao'=>'N');
		$datalist[] = array('shipping_com'=>'HK小包','type'=>'HK小包','price'=>$extra_price['price_hk_y'],'Currency'=>$currency,'days'=>'15~20d','guahao'=>'Y');
		$datalist[] = array('shipping_com'=>'E邮宝','type'=>'E邮宝','price'=>$extra_price['price_e_n'],'Currency'=>$currency,'days'=>'7~10d','guahao'=>'Y');
		}
		/*结果处理--End*/


		/*转换货币，默认人民币不用转*/
		if($mydetail['currency'] && $mydetail['currency']!='CNY'){

			foreach($datalist as $key=>&$val){
				$val['price'] = str_replace(',','',$val['price']);//此处需要统一清除逗号再转换，否则出错。
				$new_price = $this->C->service('exchange_rate')->change_rate('CNY',$mydetail['currency'],$val['price']);
				$val['price'] = number_format($new_price,2);//全站数据保留两位小数
				$val['Currency'] = $mydetail['currency'];
			}
		}

	}
	/*中国发货的运费计算--End--*/


	/*美国发货运费计算(只发到美国)--Start*/
	elseif($stockware == 'Amazon-US仓库' && ($mydetail['country']=='United States West' || $mydetail['country']== 'United States East')){
		$stockware = 'Amazon_us';
		$real_weight = $real_weight*$quantity;//乘以个数
		$datalist = $this->S->dao('shipping_fare')->amazon_us($real_weight,$mydetail['country'],$stockware);
		foreach ($datalist as &$val){
			if($val['cal_type'] == '11'){$val['price'] = ceil($real_weight*2.203*16)*$val['price']/35.248+$val['fee'];$val['shipping_com'].='(amazon-amazon)';}
			elseif($val['cal_type'] == '12'){$val['price'] = ceil($real_weight-$val['min_weight'])*1.1*2.203*$val['price']/2.203+0.6*$quantity+$val['fee'];$val['shipping_com'].='(amazon-3rd)';}

			/*数据库结果是美元为单位,要显示的结果不是USD即需转换*/
			$mydetail['currency'] = empty($mydetail['currency'])?'CNY':$mydetail['currency'];//取得币种代码,默认CNY
			if($mydetail['currency'] && $mydetail['currency']!='USD'){
					$val['price'] = $this->C->service('exchange_rate')->change_rate('USD',$mydetail['currency'],$val['price']);
			}
			$val['Currency'] = $mydetail['currency'];
			$val['price'] = number_format($val['price'],2);//全站数据保留两位小数
			$val['guahao'] = 'N';

			/*时间估算*/
			switch ($val['type']){
				case 'Expedited':$val['days'] = '2d~3d';break;
				case 'Priority':$val['days'] = '1d~2d';break;
				case 'Standard':$val['days'] = '2d~4d';break;
			}
		}

	}
	/*美国发货运费计算--End*/


	/*英国运费估算(只发到英国)--Start*/
	elseif($stockware == 'Amazon-UK仓库' && $mydetail['country'] == 'United Kingdom'){
		$stockware = 'Amazon_uk';
		$real_weight = $real_weight*$quantity;//乘以个数
		$datalist = $this->S->dao('shipping_fare')->amazon_us($real_weight,$mydetail['country'],$stockware);

		foreach ($datalist as &$val){

			if($val['cal_type'] == '21'){//大过2KG的公式不同。
				if($real_weight<=2){
					$val['price'] = ceil($real_weight*10)*$val['price']/10+(0.6*$quantity+$val['fee']);
				}elseif($real_weight>2){
					$val['price'] = ceil(($real_weight-$val['min_weight'])*10)*$val['price']/10+(0.6*$quantity+$val['fee']);
				}
			$val['shipping_com'].= '(amazon-amazon)';
			}elseif($val['cal_type'] == '22'){

				$val['price'] = ceil(($real_weight-$val['min_weight'])*1.1*10)*$val['price']/10+(1.35*$quantity+$val['fee']);
				$val['shipping_com'].= '(amazon-3rd)';

			}




			/*数据库结果是英镑为单位,要显示的结果不是GBP即需转换*/
			$mydetail['currency'] = empty($mydetail['currency'])?'CNY':$mydetail['currency'];//取得币种代码,默认CNY
			if($mydetail['currency'] && $mydetail['currency']!='GBP'){
					$val['price'] = $this->C->service('exchange_rate')->change_rate('GBP',$mydetail['currency'],$val['price']);
			}
			$val['Currency'] = $mydetail['currency'];
			$val['price'] = number_format($val['price'],2);//全站数据保留两位小数
			$val['guahao'] = 'N';

			/*时间估算*/
			switch ($val['type']){
				case 'Expedited':$val['days'] = '2d~3d';break;
				case 'Priority':$val['days'] = '1d~2d';break;
				case 'Standard':$val['days'] = '2d~4d';break;
			}
		}

	}
	/*--End*/



	/*输出模板选项*/
	$displayarr['shipping_com'] = array('showname'=>'运输公司');
	$displayarr['type']     	= array('showname'=>'运输方式');
	$displayarr['price'] 		= array('showname'=>'价格');
	$displayarr['Currency'] 	= array('showname'=>'币种');
	$displayarr['days']  		= array('showname'=>'运输时间');
	$displayarr['guahao']  		= array('showname'=>'是否有跟踪号');

	$temp = 'pub_list';
}
?>
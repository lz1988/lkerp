<?php
/*
 * Created on 2011-12-23
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class upload_excelService extends S{

	/*导入表格的表头控制*/
	protected $exl_table_head = array(

		/*销售下单的*/
		'order_shipment' =>array('deal_id','3rd_part_id','sku','listing','quantity','item_price','item_tax','shipping_price','shipping_tax','performance_fee','shipping_fee','currency','sold_account','payrec_account','b2b_customers','warehouse','shipping','receive_person','buyer_id','tel','email','address1','address2','city','state','country','post_code','comment','status'),

		/*物料调拨的*/
		'order_transfer' =>array('sku','provider_warehouse','receiver_warehouse','unit_box','box','quantity','shipping_id','remeber_id','tel','address1','address2','city','state','country','post_code','receperson','company','comment'),

		/*导入供应商的*/
		'supplier'		 =>array('name','memerid','shortname','address','person','tel','fax','bankaddr','bankid','bankuser','compuser','taxesrate','taxesnum','countmthod'),

		/*产品导入*/
		'product'		 =>array('sku','cost','costp','code','product_name','cat_name','shipping_weight','box_shipping_weight','product_dimensions','box_product_dimensions','MOQ','product_desc','color','unit_box','key_product_features','attestation','qualitycheck'),

		/*导入订单号映射*/
		'order_maps'	 =>array('order_default','order_trd'),

		/*明细表--生成财务表用*/
		'order_detail'	 =>array('Date','Order ID','3rd_part_id','SKU','Transaction type','Payment Type','Payment Detail','Currency','Amount','Quantity','Product Title','Ship Country','Shipment Date'),

		/*出库表-只对需要取值的表头进行判断-生成财务表用*/
		'order_outbound' =>array('amazon-order-id','shipment-date','sku'),

		/*allorder表-只对需要取值的表头进行判断-生成财务表用*/
		'order_allorder' =>array('amazon-order-id','merchant-order-id','sku','ship-country'),

		/*最终明细表导入保存*/
		'order_final'	 =>array('日期','平台订单号','第三方单号','平台SKU','ERP SKU','金碟SKU','产品名称','中文描述','数量','币别','收入','运费收入','amazon代收运费','amazon fee','其它平台费用','paypal费','ebay fee','发货方式','发货仓库','发货时间','国家','销售账号','收款账号','制单人','备注'),
                'order_reckoning' =>array('日期','平台订单号','收入','其它平台费用'),

        /*供应商账期导入*/
        'supplieraccount'=>array('sku','name','issuetime','account'),

        /*备货-导入*/
        'process_upstock'=>array('备货名称','SKU','供应商','备货数量','备货仓库','单个总成本','成本币别','销售价格','售价币别','销售历史','销售预估','备注'),

        /*亚马逊出库单导入*/
        'amazon_outbond' =>array('sold_account','warehouse','shipping','amazon-order-id','merchant-order-id','shipment-item-id','purchase-date','shipment-date','reporting-date','buyer-email','buyer-name','sku','quantity-shipped','currency','item-price','item-tax','shipping-price','shipping-tax','ship-address-1','ship-address-2','ship-city','ship-state','ship-postal-code','ship-country',),

        /*新格式明细表*/
        'order_detailnew'=>array('date/time','settlement id','type','order id','3rd_part_id','sku','description','quantity','marketplace','fulfillment','order city','order state','order postal','product sales','shipping credits','gift wrap credits','promotional rebates','sales tax collected','selling fees','fba fees','other transaction fees','other','total','currency','ship country','shipment date'),

         /*订单退款明细表*/
        'order_refund'=>array('date/time','type','order id','sku','quantity','total','currency','sold_account'),
        
        /*仓库运费导入*/
        'warehouseshipping'=>array('warehouse','shipping','checktime'),
	);

	/*生成导出用，生成的表格表头同样是导进去的表头-by hanson 2012-09-20*/
	public function output_mkhead($exl_head){
		$head_array = $this->exl_table_head[$exl_head];
		$mkhead		= array();

		for($i = 0; $i < count($head_array); $i++){
			$mkhead[$head_array[$i]] = $head_array[$i];
		}

		return $mkhead;
	}


	/*检查表头是否符合定义的表头-by hanson*/
	public function checkmod_head($all_arr,$data_error,$type){

		$headArr		= is_array($type)?$type:$this->exl_table_head[$type];//判断是否已自定义表头数组
        //echo '<pre>';print_r($headArr);
		$tablelist 		= '<tr>';
		$table_thkeys 	= array_keys($all_arr['0']);
        //echo '<pre>';print_r($table_thkeys);
		for ($i=0; $i<count($table_thkeys); $i++){

			/*导入的表格不在规定的表头里，则报红*/
			$error_style			= '';
            //echo $all_arr['0'][$table_thkeys[$i]]." ";
            //echo $headArr[$i]."<br/>";
			if($all_arr['0'][$table_thkeys[$i]]  != $headArr[$i]){
				$error_style 		= ' style="color:red"; title="无效的表头，应为'.$headArr[$i].'" ';
				$data_error++;
				$all_arr['0'][$table_thkeys[$i]] = empty($all_arr['0'][$table_thkeys[$i]])?'wrong!':$all_arr['0'][$table_thkeys[$i]];
			}

			$tablelist.='<th class=list '.$error_style.'>'.$all_arr['0'][$table_thkeys[$i]].'</th>';

		}
		$tablelist .= '</tr>';
		unset($all_arr['0']);//删除ABC...这行字母表头
		return $tablelist;

	}

    public function get_upload_excel_datas($upload_dir, $fieldarray, $head, $num = ''){
		$upload_ok = 0;
		$upload_file=$_FILES["upload_file$num"]["name"];        //获取文件名
		$upload_tmp_file=$_FILES["upload_file$num"]["tmp_name"];      //获取临时文件名
		$upload_filetype=$_FILES["upload_file$num"]["type"];    //获取文件类型
		$upload_status=$_FILES["upload_file$num"]["error"];   //获取文件出错情况
		              //指定文件存储路径
		$errorchar=array ("-"," ","~","!","@","#","$","%","^","&","(",")","+",","," （","）","？","！","《","》","：","；","——");//非法字符
		foreach($errorchar as $char)
		{
			if(strpos($upload_file,$char)){
				$upload_file=str_replace($char,"_",$upload_file);
			}
		}//循环排除替换文件名中的非法字符

		$upload_path=$upload_dir.date('Y_m_d_his',time()).$upload_file;   //定义文件最终的存储路径和名称
		$this->getLibrary('basefuns')->setsession('filepath'.$num, $upload_path);
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

					$all_arr = $this->get_excel_datas_withkey($upload_path, $fieldarray, $head);
				}
    		}
		}

		return $all_arr;
	}

	//指定列表值来取得数据，并按照对应的key来处理
	public function get_excel_datas_withkey($filepath, $fieldarray,$head){
		if(file_exists($filepath)){
			require_once('lib/PHPExcel.php');
			$php_excel_obj = new PHPExcel();
			$php_reader = new PHPExcel_Reader_Excel2007();

			$file_name = $filepath;

			if(!$php_reader->canRead($file_name)){
				$php_reader= new PHPExcel_Reader_Excel5();
				if(!$php_reader->canRead($file_name)){
					$message_upload = 'NO Excel!';
				}
			}
			$php_excel_obj = $php_reader->load($file_name);
			$current_sheet =$php_excel_obj->getSheet(0);

			$all_column =$current_sheet->getHighestColumn();
			$column_len = strlen($all_column);
			$all_row =$current_sheet->getHighestRow();

			$all_arr = array();
			$c_arr = array();

			$head = ($head and is_int($head))?$head:1;
			$key_arr = array();
			//字符对照表
			//ini_set("memory_limit","100M");
			for($r_i = 1; $r_i<=$all_row; $r_i++){
				$c_arr= array();
				if($r_i==$head){
					foreach($fieldarray as $c_i){
						$adr= $c_i . $r_i;
						$value= $current_sheet->getCell($adr)->getValue();
						if(is_object($value)) $value= $value->__toString();
						$key_arr[$c_i]= trim($value);//取第$head行作为列的key值
					}
					$all_arr[] =  $key_arr;
				}else{//取其它行的数值
					foreach($fieldarray as $c_i){
						$adr= $c_i . $r_i;
						$value= $current_sheet->getCell($adr)->getValue();
						if(is_object($value)) $value= $value->__toString();
						if(strlen($key_arr[$c_i])){
							$c_arr[$key_arr[$c_i]]= trim($value);
						}
					}
					$all_arr[] =  $c_arr;
				}
			}//excel表数据转化为数组
			//unset($all_arr[$head]);
			return $all_arr;
		}
		//else{
		//	echo '文件名不存在';
		//}
	}


	public function get_excel_datas($filepath){
		if(file_exists($filepath)){
			require_once('lib/PHPExcel.php');
			$php_excel_obj = new PHPExcel();
			$php_reader = new PHPExcel_Reader_Excel2007();

			$file_name = $filepath;

			if(!$php_reader->canRead($file_name)){
				$php_reader= new PHPExcel_Reader_Excel5();
				if(!$php_reader->canRead($file_name)){
					$message_upload = 'NO Excel!';
				}
			}
			$php_excel_obj = $php_reader->load($file_name);
			$current_sheet =$php_excel_obj->getSheet(0);

			$all_column =$current_sheet->getHighestColumn();
			$column_len = strlen($all_column);
			$all_row =$current_sheet->getHighestRow();

			$all_arr = array();
			$c_arr = array();

			//字符对照表
			//ini_set("memory_limit","100M");
			for($r_i = 0; $r_i<=$all_row; $r_i++){
				$c_arr= array();
				if($column_len==2){

					for($c_i= 'A'; $c_i<= 'Z'; $c_i++){
						$adr= $c_i . $r_i;
						$value= $current_sheet->getCell($adr)->getValue();
						if($c_i== 'A' && empty($value) ) break;
						if(is_object($value)) $value= $value->__toString();
						$c_arr[$c_i]= trim($value);
					}

					for($c_i= 'A'; $c_i<= substr($all_column,0,1); $c_i++){
						for($c_j= 'A'; $c_j<= substr($all_column,1,1); $c_j++){
							$adr= $c_i. $c_j.$r_i;
							$value= $current_sheet->getCell($adr)->getValue();
							if(is_object($value)) $value= $value->__toString();
						}
					}
					$all_arr[] =  $c_arr;
				}else{
					for($c_i= 'A'; $c_i<= $all_column; $c_i++){
						$adr= $c_i . $r_i;
						$value= $current_sheet->getCell($adr)->getValue();
						if($c_i== 'A' && empty($value) ) break;
						if(is_object($value)) $value= $value->__toString();
						$c_arr[$c_i]= trim($value);
					}
					$all_arr[] =  $c_arr;
				}
			}//excel表数据转化为数组
			return $all_arr;
		}

		/*else{
			echo '文件名不存在';
		}*/
	}


	/*导出表格，利用插件导出，导出的也是表格格式非网页格式，缺点处理速度较慢，不宜用于导出数据庞大并且不需要循环导入的数据*/
        
	public function download_xls($filename, $head_array, $datalist){
		require_once("lib/PHPExcel.php"); //生成excel的基本类定义(注意文件名的大小写)
		require_once("lib/PHPExcel/IOFactory.php");

		$m_objPHPExcel			= new PHPExcel();
		$m_exportType  			= "excel";
		$m_strOutputExcelFileName	= $filename.".xls"; //输出EXCEL文件名
		$objActSheet			= $m_objPHPExcel->getActiveSheet();//取得当前活动表

		/*写入数据至表格*/
		$head_keys			= array_keys($head_array);
		$closnum			= count($head_keys);
		$cellsign			= 'A';

		/*表头*/
		for($i = 0; $i < $closnum; $i++){
			$thisC = $cellsign.'1';
			$objActSheet->setCellValue($thisC , $head_array[$head_keys[$i]]);
			/* $objActSheet->getColumnDimension($cellsign)->setAutoSize(true) 根据内容自动适应列宽，但不适合于中文 */
			//$objActSheet->getColumnDimension($cellsign)->setWidth(strlen($head_array[$head_keys[$i]])); 
			$cellsign++;
		}
 
		/*数据部分*/
		$rowsign  = 2;//从第二行开始
		foreach($datalist as $val){
                    $cellsign = 'A';//重定义从A开始
                    for($i = 0; $i < $closnum; $i++){
                        $thisC = $cellsign.$rowsign;  
                        //匹配<font color="red">sku</font>
                        if(preg_match('/<font(.*?)color=\"(.*)\">(.*)<[^>]*>/', $val[$head_keys[$i]],$stylecolor)){
                            $objActSheet->setCellValue($thisC , $stylecolor[3]);
                            switch ($stylecolor[2]) {
                                case 'red':
                                    $color = "FFFF0000";
                                    break;
                                case 'blue':
                                    $color = "FF0000FF";
                                    break;
                                case 'green':
                                    $color = "FF00FF00";
                                    break;
                                case 'yellow':
                                    $color = "FFFFFF00";
                                    break;
                                default:
                                    $color = "FF000000";
                                    break;
                            }
                            $objActSheet->getStyle($thisC)->getFont()->getColor()->setARGB($color);    
                        }else{
                            $objActSheet->setCellValue($thisC , $val[$head_keys[$i]]);
                        }
                        $cellsign++;
                    }
                    $rowsign++;
		} 
            
		/* 从浏览器直接输出$m_strOutputExcelFileName */
		$objWriter = PHPExcel_IOFactory::createWriter($m_objPHPExcel, 'Excel5');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type: application/vnd.ms-excel;");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header("Content-Disposition:attachment;filename=".$m_strOutputExcelFileName);
		header("Content-Transfer-Encoding:binary");
		$objWriter->save("php://output");

	}


	/*导出表格,弹出窗口下载*/
	public function download_excel($filename,$head_array,$datalist){

		$output = "<HTML>";
		$output .= "<HEAD>";
		$output .= "<META http-equiv=Content-Type content=\"text/html; charset=utf-8\">";
		$output .= "</HEAD>";
		$output .= "<BODY>";
		$output .= $this->arr_tbl_excel($head_array,$datalist);
		$output .= "</BODY>";
		$output .= "</HTML>";
		header("Content-type:application/msexcel");
		header("Content-disposition: attachment; filename=$filename.xls");
		header("Cache-control: private");
		header("Pragma: private");
		print($output);

	}


	/*导出表格内容处理*/
	function arr_tbl_excel($head_array,$datalist){

		$ret = '';
		$ret .= '<table border=1><tr>';

		/*表头输出*/
		$head_keys = array_keys($head_array);
		for($i=0;$i<count($head_keys);$i++){
			$ret.='<td>'.trim($head_array[$head_keys[$i]]).'</td>';
		}
		$ret.='</tr>';

		/*内容输出*/
		foreach($datalist as $val){
			$ret.='<tr>';
			for($j=0;$j<count($head_keys);$j++){
				//if($head_keys[$j] == 'sku'){
					$ret.='<td>'.trim($val[$head_keys[$j]]).'</td>';
				//}else{
				//	$ret.='<td>'.$val[$head_keys[$j]].'&nbsp;</td>';
				//}
			}
			$ret.='</tr>';
		}

		$ret.= '</table>';
		return $ret;
	}


	/*导出其它格式的文档,弹出窗口下载*/
	public function download($filename,$msg,$ext){

		Header( "Content-type:   application/octet-stream ");
		Header( "Accept-Ranges:   bytes ");
		header( "Content-Disposition:   attachment;   filename=$filename.$ext");
		header( "Expires:   0 ");
		header( "Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 ");
		header( "Pragma:   public ");
		echo $msg;
		exit();
	}


	/* 满足特定需求，自己写的方法
	 * 上传两个文件时，读取第一个文件选取框的文件上传
	 * */

	public function get_upload_excel_datas_one($upload_dir, $fieldarray, $head){
		$upload_ok = 0;
		$upload_file=$_FILES["upload_file1"]["name"];        //获取文件名
		$upload_tmp_file=$_FILES["upload_file1"]["tmp_name"];      //获取临时文件名
		$upload_filetype=$_FILES["upload_file1"]["type"];    //获取文件类型
		$upload_status=$_FILES["upload_file1"]["error"];   //获取文件出错情况
		              //指定文件存储路径
		$errorchar=array ("-"," ","~","!","@","#","$","%","^","&","(",")","+",","," （","）","？","！","《","》","：","；","——");//非法字符
		foreach($errorchar as $char)
		{
			if(strpos($upload_file,$char)){
				$upload_file=str_replace($char,"_",$upload_file);
			}
		}//循环排除替换文件名中的非法字符

		$upload_path=$upload_dir.date('Y_m_d_his',time()).$upload_file;   //定义文件最终的存储路径和名称
		$this->getLibrary('basefuns')->setsession('filepath', $upload_path);
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

					$all_arr = $this->get_excel_datas_withkey($upload_path, $fieldarray, $head);
				}
    		}
		}

		return $all_arr;
	}

	/**
     * @title 一般用于上传表格时的检测，返回一维数组
     * by hanson 2012-11-20
     */
    public function upcel_check($sysboy, $col = '', $sqlstr = ''){

		$backdata = $this->S->dao($sysboy)->D->get_allstr($sqlstr,'','',$col);
		foreach($backdata as $val){
			$backArr[] = $val[$col];
		}
		return $backArr;
    }
}
?>

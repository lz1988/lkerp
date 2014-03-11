<?php
/*
 * Created on 2013-3-21
 *
 * @title 运费反写 create by hanson 2013-03-21
 */
 
 set_time_limit(0);
 /*主页选择*/
 if($detail == 'main'){

 	$bannerstr .= '<br><button class="eight" onclick=window.location="index.php?action=shipping_farerewrite&detail=import">运费账单导入</button><br><font color=#bdbdbd size=-1> &nbsp;导入运费账单，包括ups、fedex、dhl。</font>';
 	$bannerstr .= '<br><br><button class="eight" onclick=window.location="index.php?action=shipping_farerewrite&detail=list">查看运费账单</button><br><font color=#bdbdbd size=-1>  &nbsp;查看导入的运费账单明细。</font>';

	$this->V->mark(array('title'=>'运费反写'));
	$temp = 'pub_list';
 }

 /*列表查询*/
 elseif($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'num-s-l'			=>'账单号码：',
		'track_no-s-l'		=>'物流跟踪号：',
		'shipping-a-e'		=>'*发货方式：',
		'date1-t-t'			=>'<br>账单日期：',
		'date2-t-t'			=>'&nbsp;&nbsp;&nbsp;&nbsp;出口日期：',
	);

	$shippingarr 	= array(''=>'=请选择=','dhl'=>'dhl','fedex'=>'fedex','ups'=>'ups');
	$shippingobj	= $this->S->dao('shipping_farerewrite');

	/*当选择了发货方式才会去查数据*/
	if($shipping){
		$datalist	= $shippingobj->D->get_list($sqlstr);


		/*fedex的处理*/
		if($datalist['0']['shipping'] == 'fedex'){

			$displayarr['shipping']	= array('showname'=>'shipping',	'width'=>'100','title'=>'发货方式');
			$displayarr['num'] 		= array('showname'=>'Trx Ctrl Num','width'=>'100','title'=>'账单号');
			$displayarr['track_no'] = array('showname'=>'Airbill Nbr','width'=>'100','title'=>'跟踪号');
			$displayarr['a'] 		= array('showname'=>'Rated Amt','width'=>'100','title'=>'额定运费');
			$displayarr['_discount1']= array('showname'=>'Discount Amt','width'=>'100','title'=>'运费折扣');
			$displayarr['b'] 		= array('showname'=>'Insurance Amt','width'=>'100','title'=>'保险费');
			$displayarr['c'] 		= array('showname'=>'Oda Amt','width'=>'100','title'=>'超范围派送费');
			$displayarr['d'] 		= array('showname'=>'Opa Amt','width'=>'100','title'=>'超范围取件费');
			$displayarr['e'] 		= array('showname'=>'Sat Delivery Amt','width'=>'100','title'=>'周末派送费');
			$displayarr['f'] 		= array('showname'=>'Addr Correction Amt','width'=>'100','title'=>'地址更改费');
			$displayarr['g'] 		= array('showname'=>'Other Charges Amt','width'=>'100','title'=>'其它');
			$displayarr['h'] 		= array('showname'=>'Taxes Amt','width'=>'100','title'=>'税金');
			$displayarr['total'] 	= array('showname'=>'Billed Amt','width'=>'100','title'=>'合计');
			$displayarr['date1'] 	= array('showname'=>'账单日期','width'=>'80','title'=>'');
			$displayarr['date2'] 	= array('showname'=>'出口日期','width'=>'80','title'=>'');
		}

		/*ups的处理*/
		elseif($datalist['0']['shipping'] == 'ups'){

			$displayarr['shipping']	= array('showname'=>'shipping',	'width'=>'100','title'=>'发货方式');
			$displayarr['num'] 		= array('showname'=>'账单号码','width'=>'100');
			$displayarr['track_no'] = array('showname'=>'运单号（长）','width'=>'100');
			$displayarr['total'] 	= array('showname'=>'应收费用','width'=>'100');
			$displayarr['a'] 		= array('showname'=>'运费','width'=>'100');
			$displayarr['_discount1']= array('showname'=>'普通折扣','width'=>'100');
			$displayarr['b'] 		= array('showname'=>'运费附加费','width'=>'100');
			$displayarr['c'] 		= array('showname'=>'税金附加费','width'=>'100');
			$displayarr['d'] 		= array('showname'=>'阶梯折扣','width'=>'100');
			$displayarr['date1'] 	= array('showname'=>'账单日期','width'=>'80','title'=>'');
			$displayarr['date2'] 	= array('showname'=>'出口日期','width'=>'80','title'=>'');
		}

		/*dhl的处理*/
		elseif($datalist['0']['shipping'] == 'dhl'){

			$displayarr['shipping']	= array('showname'=>'shipping',	'width'=>'100','title'=>'发货方式');
			$displayarr['num'] 		= array('showname'=>'INVCE_NO','width'=>'100','title'=>'账单号');
			$displayarr['track_no'] = array('showname'=>'HAWB_NO','width'=>'100','title'=>'跟踪号');
			$displayarr['a'] 		= array('showname'=>'WT_CHRG','width'=>'100','title'=>'运费');
			$displayarr['b'] 		= array('showname'=>'FU_CHRG','width'=>'100','title'=>'燃油附加');
			$displayarr['c'] 		= array('showname'=>'SE_CHRG','width'=>'100','title'=>'');
			$displayarr['d'] 		= array('showname'=>'RAS_PICK','width'=>'100','title'=>'');
			$displayarr['e'] 		= array('showname'=>'RAS_DELI','width'=>'100','title'=>'偏远地区派送费');
			$displayarr['f'] 		= array('showname'=>'DDP_ADMIN','width'=>'100','title'=>'');
			$displayarr['g'] 		= array('showname'=>'OTHERS','width'=>'100','title'=>'超重费');
			$displayarr['h'] 		= array('showname'=>'SII_CHRG','width'=>'100','title'=>'');
			$displayarr['total'] 	= array('showname'=>'TOTAL','width'=>'100','title'=>'合计');
			$displayarr['date1'] 	= array('showname'=>'账单日期','width'=>'80','title'=>'');
			$displayarr['date2'] 	= array('showname'=>'出口日期','width'=>'80','title'=>'');
		}

	}

 	$this->V->mark(array('title'=>'运费账单-运费反写(main)'));
	$temp = 'pub_list';

 }


 /*导入运费账单*/
 elseif($detail == 'import'){

 	if(!$this->C->service('admin_access')->checkResRight('shipping_farerewrite')){$this->C->sendmsg();}

	if($_FILES["upload_file"]["name"] || $filepath){

		$upload_exl_service = $this->C->Service('upload_excel');
		$global_service		= $this->C->Service('global');
		$upload_dir 		= "./data/uploadexl/temp/";//上传文件保存路径的目录
        $process            = $this->S->dao('process');
        $sku_shipping       = $this->S->dao('sku_shipping');

		/*有效的excel列表值*/
		$fieldarray			= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');

		/*用于检测表头*/
		$headCheckArr		= array(
								'fedex'	=>array('shipping','Date Doc','Ship Date','Trx Ctrl Num','Airbill Nbr','Rated Amt','Discount Amt','Insurance Amt','Oda Amt','Opa Amt','Sat Delivery Amt','Addr Correction Amt','Other Charges Amt','Taxes Amt','Billed Amt'),
								'ups'	=>array('shipping','账单日期','出口日期','账单号码','运单号（长）','应收费用','运费','普通折扣','运费附加费','税金附加费','阶梯折扣'),
								'dhl'	=>array('shipping','INVCE_DATE','SHP_DATE','INVCE_NO','HAWB_NO','WT_CHRG','FU_CHRG','SE_CHRG','RAS_PICK','RAS_DELI','DDP_ADMIN','OTHERS','SII_CHRG','TOTAL'),
		);

		/*保存运费数据*/
		if($filepath){

			$all_arr		= $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray, 1);
			$shippingobj	= $this->S->dao('shipping_farerewrite');
			$errorcount		= 0;
			$donecount		= 0;//成功统计
			$exitscount		= 0;//存在跳过统计
			unset($all_arr['0']);//删除表头
			unlink($filepath);//删除文件

			$shippingobj->D->query('begin');

			/*循环插入，以num、track_no是否存在判断重复导入则跳过*/
			foreach($all_arr as $val){

				/*fedex的处理*/
				if($shipping == 'fedex'){
					$exists = $shippingobj->D->get_one(array('num'=>$val['Trx Ctrl Num'],'track_no'=>$val['Airbill Nbr'],'total'=>$val['Billed Amt']),'id');
					if(!$exists){
						$insertArr = array(
							'shipping'	=>$shipping,
							'total'		=>$val['Billed Amt'],
							'num'		=>$val['Trx Ctrl Num'],
							'track_no'	=>$val['Airbill Nbr'],
							'a'			=>$val['Rated Amt'],
							'b'			=>$val['Insurance Amt'],
							'c'			=>$val['Oda Amt'],
							'd'			=>$val['Opa Amt'],
							'e'			=>$val['Sat Delivery Amt'],
							'f'			=>$val['Addr Correction Amt'],
							'g'			=>$val['Other Charges Amt'],
							'h'			=>$val['Taxes Amt'],
							'_discount1'=>$val['Discount Amt'],
							'date1'		=>$global_service->changetime($val['Date Doc']),
							'date2'		=>$global_service->changetime($val['Ship Date']),
						);
						$sid = $shippingobj->D->insert($insertArr);
						if(!$sid) {$errorcount++;}else{$donecount++;}
					}else{
						$exitscount++;
					}
				}

				/*ups的处理*/
				elseif($shipping == 'ups'){
					$exists = $shippingobj->D->get_one(array('num'=>$val['账单号码'],'track_no'=>$val['运单号（长）'],'total'=>$val['应收费用']),'id');
					if(!$exists){
						$insertArr = array(
							'shipping'	=>$shipping,
							'total'		=>$val['应收费用'],
							'num'		=>$val['账单号码'],
							'track_no'	=>$val['运单号（长）'],
							'a'			=>$val['运费'],
							'b'			=>$val['运费附加费'],
							'c'			=>$val['税金附加费'],
							'd'			=>$val['阶梯折扣'],
							'_discount1'=>$val['普通折扣'],
							'date1'		=>$global_service->changetime($val['账单日期']),
							'date2'		=>$global_service->changetime($val['出口日期']),
						);
						$sid = $shippingobj->D->insert($insertArr);
						if(!$sid) {$errorcount++;}else{$donecount++;}
					}else{
						$exitscount++;
					}
				}

				/*dhl的处理*/
				elseif($shipping == 'dhl'){
					$exists = $shippingobj->D->get_one(array('num'=>$val['INVCE_NO'],'track_no'=>$val['HAWB_NO'],'total'=>$val['TOTAL']),'id');
					if(!$exists){
						$insertArr = array(
							'shipping'	=>$shipping,
							'total'		=>$val['TOTAL'],
							'num'		=>$val['INVCE_NO'],
							'track_no'	=>$val['HAWB_NO'],
							'a'			=>$val['WT_CHRG'],
							'b'			=>$val['FU_CHRG'],
							'c'			=>$val['SE_CHRG'],
							'd'			=>$val['RAS_PICK'],
							'e'			=>$val['RAS_DELI'],
							'f'			=>$val['DDP_ADMIN'],
							'g'			=>$val['OTHERS'],
							'h'			=>$val['SII_CHRG'],
							'date1'		=>$global_service->changetime($val['INVCE_DATE']),
							'date2'		=>$global_service->changetime($val['SHP_DATE']),
						);
						$sid = $shippingobj->D->insert($insertArr);
						if(!$sid) {$errorcount++;}else{$donecount++;}
					}else{
						$exitscount++;
					}
				}
			}

			$existsmsg = empty($exitscount)?'':'，存在重复跳过 '.$exitscount.' 条！';

			if(empty($errorcount)){
  	             
                 $shippingobj->D->query('commit');
                 //导入运费，运费进行分摊
                 $datalist = $process->get_shipping_farerewrite();
                
                 for($i = 0; $i < count($datalist); $i++)
                 {
                    $skuweight = ($datalist[$i]['sum_product_size'] > $datalist[$i]['sum_product_weight'])?$datalist[$i]['sum_product_size']:$datalist[$i]['sum_product_weight'];
                    if ($skuweight)
                    {
                        $firewritecost  = $process->showtransfer_sumfarewritecost_s($datalist[$i]['comment2']);
                        
                        $sumweight      = $firewritecost['sumweight'];
                        $shipping_fee   = $datalist[$i]['shipping_fee'];
                        $skucost        = $skuweight/$sumweight;
                        //echo '<pre>';echo $skuweight.'*';
                        //echo '<pre>';echo $sumweight.'**';
                        //echo '<prE>';echo $shipping_fee.'***';
                        //echo '<prE>';$skucost;
                        //die();
                        $farewrite = $skucost*$shipping_fee;
                        //更新运费
                        $process->D->update_by_field(array('id'=>$datalist[$i]['id']),array('shipping_farerewrite'=>$farewrite));
                        
                        //运费加权平均
                        $sku_data = $process->get_sku_house_shipping();
                        
                        //删除sku_shipping表
                        $sku_shipping->D->delete('');
                        
                        //重新统计运费加权平均
                        foreach($sku_data as $data){
                            $insert_data['pid']         = $data['pid'];
                            $insert_data['eid']         = $data['id'];
                            $insert_data['shipping']    = $data['shipping'];
                            $sku_shipping->D->insert($insert_data);
                        }
                    }
                 }
                
			
				$this->C->success('成功导入数据 '.$donecount.' 条'.$existsmsg,"index.php?action=shipping_farerewrite&detail=main");
			}else{
				$shippingobj->D->query('rollback');
				$this->C->success('导入失败，请重试',"index.php?action=shipping_farerewrite&detail=import");
			}
		}

		/*导入并显示预览*/
		else{

			$data_error 	= 0;
			$tablelist 		= '<table id="mytable">';
			$all_arr		= $upload_exl_service->get_upload_excel_datas($upload_dir, $fieldarray, 1);//上传并获取数据
			$filepath		= $_SESSION['filepath'];

			/*取得表头用来检测*/
			$headarray 		= $headCheckArr[$all_arr['1']['shipping']];

			/*表头检测，若有错，显示表头*/
			$tablelist	   .= $upload_exl_service->checkmod_head(&$all_arr,&$data_error,$headarray);
			foreach($all_arr as $k=>$val){
				$tablelist .= '<tr>';
				foreach($val as $j=>$value) {
					$error_style = '';

					/*检测发货方式*/
					if($j == 'shipping'){
						if(!in_array($value,array_keys($headCheckArr))){
							$error_style = ' bgcolor="red" title="只允许填写fedex,ups,dhl"';
							$data_error++;
						}
					}

					/*检测账单号，物流跟踪号不能为空，应收款三款不能为空(需要做重复检测的依据)*/
					if($j == 'INVCE_NO' || $j == 'HAWB_NO' || $j== 'Trx Ctrl Num' || $j == 'Airbill Nbr' || $j == '账单号码' || $j == '运单号（长）' || $j == 'Billed Amt' || $j == '应收费用' || $j == 'TOTAL'){                                                                                                                               //
						if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
					}

					/*日期不能为空*/
					if($j == 'Date Doc' || $j == 'Ship Date' || $j == '账单日期' || $j == '出口日期' || $j == 'INVCE_DATE' || $j == 'SHP_DATE'){
						if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}else{
							$newtime= date('Y-m-d',strtotime($value));
							if(!$global_service->changetime($value)){//检测时间，兼容41309格式
								$error_style = ' bgcolor="red" title="时间格式错误！"';
								$data_error++;
							}
						}
					}
					$tablelist  .= '<td '.$error_style.' >&nbsp;'.$value.'</td>';
				}
				$tablelist .= '</tr>';
			}
			$tablelist	   .= '</table>';

			if(!$data_error && isset($all_arr)){
				$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
				$tablelist .= '<input type="hidden" name="shipping" value="'.$all_arr['1']['shipping'].'" />';
				$tablelist .= '<input type="submit" value="确认并提交">';
			}elseif($data_error){
				$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
				unlink($filepath);//有错的文件删除掉
			}

		}
		$this->V->mark(array('tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
	}

	$downloadtpl = '<div style="font-size:12px;line-height:20px;margin-bottom:30px"><div><a href="./data/uploadexl/sample/ups_tpl.xls" title="下载UPS模板">点击下载UPS模板>></a></div>';
	$downloadtpl.= '<div><a href="./data/uploadexl/sample/fedex_tpl.xls" title="下载Fedex模板">点击下载Fedex模板>></a></div>';
	$downloadtpl.= '<div><a href="./data/uploadexl/sample/dhl_tpl.xls" title="下载DHL模板">点击下载DHL模板>></a></div>';
	$downloadtpl.= '</div>';
	$this->V->mark(array('title'=>'运费账单导入-运费反写(main)','message_upload'=>$downloadtpl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();

}


/*头尾包含*/
if($detail == 'main' || $detail == 'import' || $detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}

?>

<?php
/**
 +-----------------------------------------------
 * @title 关税反写
 * @author Jerry
 * @create on 2014-1-21
 +-----------------------------------------------
 */
 if ($detail == 'list'){
    
    $stypemu = array(
        'shipping-a-e'  =>'发货方式：',
        'track_no-s-l'  =>'物流追踪号：',
        'ship_date-t-t' =>'出口日期：',    
    );
    
   	$shippingarr 	= array(''=>'=请选择=','dhl'=>'dhl','fedex'=>'fedex','ups'=>'ups');
    
    $tariff_rewrite = $this->S->dao('tariff_rewrite');
    $datalist         = $tariff_rewrite->D->get_list($sqlstr);
    
    $displayarr = array();

    $displayarr['shipping']         = array('showname'=>'发货方式',  'width'=>'60');
   	$displayarr['track_no']         = array('showname'=>'物流追踪号', 'width'=>'100');
     $displayarr['ship_date']       = array('showname'=>'出口日期', 'width'=>'100');
    $displayarr['tariff_costs']  	= array('showname'=>'关税', 'width'=>'60');
    $bannerstr 		= '<button  onclick="window.location=\'index.php?action=tariff_rewrite&detail=import\'">导入关税</button>';

    $this->V->view['title'] = '关税反写列表';
    $temp = 'pub_list';
 }
 
 /*导入关税运费*/
 elseif($detail == 'import'){

	if($_FILES["upload_file"]["name"] || $filepath){

		$upload_exl_service = $this->C->Service('upload_excel');
		$global_service		= $this->C->Service('global');
		$upload_dir 		= "./data/uploadexl/temp/";//上传文件保存路径的目录

		/*有效的excel列表值*/
		$fieldarray			= array('A','B','C','D');

		/*用于检测表头*/
		$headarray		   = array('shipping','出口日期','运单号','关税费用');

		/*保存运费数据*/
		if($filepath){

			$all_arr		= $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray, 1);
			$shippingobj	= $this->S->dao('tariff_rewrite');
			$errorcount		= 0;
			$donecount		= 0;//成功统计
			$exitscount		= 0;//存在跳过统计
			unset($all_arr['0']);//删除表头
			unlink($filepath);//删除文件

			$shippingobj->D->query('begin');

			/*循环插入，以shipping、track_no,track_no是否存在判断重复导入则跳过*/
			foreach($all_arr as $val){

				$exists = $shippingobj->D->get_one(array('shipping'=>$val['shipping'],'track_no'=>$val['运单号'],'tariff_costs'=>$val['关税费用']),'id');
                
				if(!$exists){
					$insertArr = array(
						'shipping'	      =>$val['shipping'],
						'track_no'	      =>$val['运单号'],
						'tariff_costs'	  =>$val['关税费用'],
						'ship_date'		  =>$global_service->changetime($val['出口日期']),
					);
					$sid = $shippingobj->D->insert($insertArr);
					if(!$sid) {$errorcount++;}else{$donecount++;}
				}else{
					$exitscount++;
				}
            }
				
			
			$existsmsg = empty($exitscount)?'':'，存在重复跳过 '.$exitscount.' 条！';

			if(empty($errorcount)){
                $shippingobj->D->query('commit');
				$this->C->success('成功导入数据 '.$donecount.' 条'.$existsmsg,"index.php?action=tariff_rewrite&detail=list");
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
        
            $headCheck = array('dhl'=>'dhl','fedex'=>'fedex','ups'=>'ups');
            
			/*表头检测，若有错，显示表头*/
			$tablelist	   .= $upload_exl_service->checkmod_head(&$all_arr,&$data_error,$headarray);
			foreach($all_arr as $k=>$val){
				$tablelist .= '<tr>';
				foreach($val as $j=>$value) {
					$error_style = '';

					/*检测发货方式*/
					if($j == 'shipping'){
						if(!in_array($value,array_keys($headCheck))){
							$error_style = ' bgcolor="red" title="只允许填写fedex,ups,dhl"';
							$data_error++;
						}
					}

					/*检测账单号，物流跟踪号不能为空，应收款三款不能为空(需要做重复检测的依据)*/
					if($j == '账单号码'){                                                                                                                               //
						if(empty($value)){
							$error_style = ' bgcolor="red" title="不能为空！"';
							$data_error++;
						}
					}

					/*日期不能为空*/
					if($j == '出口日期'){
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
				$tablelist .= '<input type="submit" value="确认并提交">';
			}elseif($data_error){
				$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
				unlink($filepath);//有错的文件删除掉
			}

		}
		$this->V->mark(array('tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
	}

	$downloadtpl = '<div style="font-size:12px;line-height:20px;margin-bottom:30px"><div><a href="./data/uploadexl/sample/tariff_rewrite_tpl.xls" title="下载关税费用模板">下载关税费用模板>></a></div>';
	$downloadtpl.= '</div>';
	$this->V->mark(array('title'=>'关税导入-关税反写(list)','message_upload'=>$downloadtpl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}
 
if($detail == 'list' || $detail == 'import'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>
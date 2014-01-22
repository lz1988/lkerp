<?php
/*
 *	Created on 2012-8-30
 *	订单号映射 by hanson
 *
 */

 $order_mapp = $this->S->dao('order_mapp');

 if($detail == 'list'){

	$stypemu = array(
		'order_default-s-l'	=>'平台订单号：',
		'order_trd-s-l'		=>'第三方单号：',
		'cuser-s-l'			=>'录入：'
	);

	$datalist = $order_mapp->D->get_list($sqlstr);

	/*配置输出表单*/
	$displayarr = array();
	$tablewidth = '800';

	$displayarr['order_default'] = array('showname'=>'平台订单号','width'=>'130');
	$displayarr['order_trd']	 = array('showname'=>'第三方单号','width'=>'130');
	$displayarr['cuser']	 	 = array('showname'=>'录入','width'=>'70');
	$displayarr['cdate']	 	 = array('showname'=>'时间','width'=>'100');
	$displayarr['both']		 	 = array('showname'=>'操作','width'=>'60','url_e'=>'index.php?action=order_mapping&detail=editmap&id={id}','url_d'=>'index.php?action=order_mapping&detail=delmap&id={id}','ajax'=>1);

	$bannerstr		.= '<button onclick=window.location="index.php?action=order_mapping&detail=addmap" >添加映射</button>';
	$bannerstr		.= '<button onclick=window.location="index.php?action=order_mapping&detail=import" >导入映射</button>';
	$bannerstrarr[]  = array('url'=>'index.php?action=order_mapping&detail=outport','value'=>'导出映射');
	$this->V->mark(array('title'=>'订单号映射列表'));
	$temp = 'pub_list';

 }

 /*添加或编辑映射*/
 elseif($detail == 'addmap' || $detail == 'editmap'){
	if($detail == 'editmap'){

		$backdata 	= $order_mapp->D->get_one(array('id'=>$id));
		if(!$this->C->service('admin_access')->checkResRight('r_r_editmapping','mod',$backdata['cuser'])){$this->C->sendmsg();}
		$singtile 	= '修改映射-订单号映射列表(list)';
		$jump	  	= 'editmod';

	}else{
		if(!$this->C->service('admin_access')->checkResRight('r_r_addmapping')){$this->C->sendmsg();}
		$singtile 	= '添加映射-订单号映射列表(list)';
		$jump	  	= 'addmod';
	}

	/*表单配置*/
	$conform 		= array('method'=>'post','action'=>'index.php?action=order_mapping&detail='.$jump,'width'=>'700');
	$colwidth 		= array('1'=>'150','2'=>'400','3'=>'100');

    $disinputarr 					= array();
    $disinputarr['id'] 				= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['order_default'] 	= array('showname'=>'平台订单号','value'=>$backdata['order_default']);
    $disinputarr['order_trd'] 		= array('showname'=>'第三方单号','value'=>$backdata['order_trd']);

	$this->V->mark(array('title'=>$singtile));
	$temp = 'pub_edit';
 }

 /*导出映射*/
 elseif($detail == 'outport'){

	$datalist = $order_mapp->D->get_allstr($sqlstr);

	$filename = 'order_map'.date('Y-m-d-h-i-s',time());
	$head_array = array('order_default'=>'平台订单号','order_trd'=>'第三方单号','cuser'=>'录入','cdate'=>'录入时间');
	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);

 }

 /*删除*/
 elseif($detail == 'delmap'){

	/*权限*/
	$backdata = $order_mapp->D->get_one_by_field(array('id'=>$id),'cuser');
	if(!$this->C->service('admin_access')->checkResRight('r_r_delmapping','mod',$backdata['cuser'])){$this->C->ajaxmsg(0);}

 	$sid = $order_mapp->D->delete_by_field(array('id'=>$id));
 	if($sid){echo '1';}else{echo '删除失败';}
 }

 /*编辑与新增的保存*/
 elseif($detail == 'editmod' || $detail == 'addmod'){


 	$jumpurl = 'index.php?action=order_mapping&detail=list';

 	if($detail == 'editmod'){
	 	$sid = $order_mapp->D->update_by_field(array('id'=>$id),array('order_default'=>$order_default,'order_trd'=>$order_trd));
 	}elseif($detail == 'addmod'){
		$sid = $order_mapp->D->insert(array('order_default'=>$order_default,'order_trd'=>$order_trd,'cuser'=>$_SESSION['eng_name'],'cdate'=>date('Y-m-d H:i:s',time())));
 	}

 	if($sid){$this->C->success('保存成功',$jumpurl);}else{$this->C->success('保存失败',$jumpurl);}

 }

 /*导入订单映射*/
 elseif($detail == 'import'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_r_addmapping')){$this->C->sendmsg();}

	/*取上传的文件的数组*/
	$upload_dir = "./data/uploadexl/order_map/";//上传文件保存路径的目录
	$fieldarray = array('A','B');//有效的excel列表值
	$head = 1;//以第一行为表头

	if($filepath){
		$all_arr 	=  $this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);
		$cdates  	= date('Y-m-d H:i:s',time());
		$error_num	= 0;

		$order_mapp->D->query('begin');

		for($i=1;$i<count($all_arr);$i++){
			$sid = $order_mapp->D->insert(array('order_default'=>$all_arr[$i]['order_default'],'order_trd'=>$all_arr[$i]['order_trd'],'cuser'=>$_SESSION['eng_name'],'cdate'=>$cdates));
			if(!$sid) $error_num++;
		}

		if(empty($error_num)){
			$order_mapp->D->query('commit');
			$this->C->success('导入成功','index.php?action=order_mapping&detail=list');
		}else{
			$order_mapp->D->query('rollback');
			$this->C->success('导入失败，请重试','index.php?action=order_mapping&detail=import');
		}

	}else{

		$all_arr 		=  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath 		= $this->getLibrary('basefuns')->getsession('filepath');

		$data_error		= 0;
		$tablelist 	   .= '<table id="mytable">';

		/*表头特殊显示处理*/
		$tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'order_maps');

		foreach($all_arr as $k=>$val){
			$tablelist 		 .= '<tr>';

			foreach( $val as $j=>$value) {
				$error_style = '';
				/*检测第三方单号是否存在*/
				if($j == 'order_trd'){
					$backtrd = $order_mapp->D->get_one_by_field(array('order_trd'=>$value),'id');
					if($backtrd){
						$error_style = ' bgcolor="red" title="存在重复的第三方单号！"';
						$data_error++;
					}
				}

				$tablelist .= '<td '.$error_style.'>&nbsp;'.$value.'</td>';
			}
			$tablelist .= '</tr>';
		}
		$tablelist .= '</table>';

		/*错误判断*/
		if(!$data_error && isset($all_arr)){

			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="submit" value="确认并提交"><input type="reset" value="取消" onclick=window.location="index.php?action=order_mapping&detail=list">';
		}elseif($data_error){

			$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
			unlink($filepath);
		}

	}

	$submit_action = 'index.php?action=order_mapping&detail=import';
	$temlate_exlurl = 'data/uploadexl/sample/order_map.xls';

	$this->V->mark(array('title'=>'导入订单号映射表-订单号映射列表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();

 }


 if($detail == 'list' || $detail == 'addmap' || $detail == 'editmap' || $detail == 'import'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }

?>

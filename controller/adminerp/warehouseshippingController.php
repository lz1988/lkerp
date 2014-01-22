<?php
/**
 +-----------------------------------------------
 * @title 仓库运费管理
 * @author Jerry
 * @create on 2014-1-3
 +-----------------------------------------------
 */
 
 if ($detail == 'list'){
    $stypemu = array(
        'warehouse-a-e'    => '&nbsp;&nbsp;&nbsp;仓库：',
		'checktime-b-'	   => '&nbsp;&nbsp;审核期间：',
        'fstcreate-t-t'    => '&nbsp;创建时间：',
    );
   	
    /*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	$warehousearr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$warehousearr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}
    
    $checktimestr = "<input type=text name=checktime  class='find-T' onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' value=".$checktime." >";
    
    if ($checktime) $sqlstr.= ' and checktime="'.$checktime.'"';
 
    $InitPHP_conf['pageval'] = 15; //分页数
    $datalist 	= $this->S->dao('warehouseshipping')->get_list($sqlstr);
    
    $displayarr = array();
    $tablewith  = '900';
    $displayarr['name']      = array('showname'=>'仓库名称',  'width'=>'60');
    $displayarr['shipping']   = array('showname'=>'运费', 'width'=>'100');
   	$displayarr['checktime']  = array('showname'=>'审核期间', 'width'=>'100');
    $displayarr['both']     = array('showname'=>'操作', 'ajax'=>1, 'width'=>'60', 'url_d'=>'index.php?action=warehouseshipping&detail=del&id={id}', 'url_e'=>'index.php?action=warehouseshipping&detail=update&id={id}');
    
    $bannerstr 		= '<button class="six" onclick="window.location=\'index.php?action=warehouseshipping&detail=add\'">录入运费</button>';
    $bannerstr 	   .= '<button class="six" onclick="window.location=\'index.php?action=warehouseshipping&detail=insert\'">导入运费</button>';
    
    $this->V->mark(array('title'=>'仓库运费列表'));
    $temp = 'pub_list';
 }
 
 /**
  *@title 导入仓库运费 
  *@author Jerry 
  *@create on 2014-1-6 
  */ 
 elseif ($detail =='insert') {
    
   	/*上传文件保存路径的目录*/
	$upload_dir = "./data/uploadexl/";
	$fieldarray = array('A','B','C');//有效的excel列表值
	$head 		= 1;//以第一行为表头
	$tablelist 	= '';
	$esse		= $this->S->dao('esse');
    $warehouseshipping  = $this->S->dao('warehouseshipping');
    $global  =   $this->C->service('global');

	/*读取已经上传的文件*/
	if($filepath){

		$all_arr 	=	$this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);
		unlink($filepath);
		$count_all	=	count($all_arr);
		$error_dd	= 0;
        
		$warehouseshipping->D->query('begin');
		for($i = 1; $i < $count_all; $i++){
            $check_time = $global->changetime_ymdhis($all_arr[$i]['checktime']);
            $checktime = date('Y-m',strtotime($check_time));
            $_wareid = $esse->D->get_one(array('name'=>$all_arr[$i]['warehouse'],'type'=>'2'),'id');
            $sid = $warehouseshipping->D->insert(array('warehouse'=>$_wareid,'checktime'=>$checktime,'shipping'=>sprintf ("%01.2f",$all_arr[$i]['shipping'])));
        }
        if(!$sid) $error_dd++;
    
		if(empty($error_dd)){
			$warehouseshipping->D->query('commit');$this->C->success('添加成功!','index.php?action=warehouseshipping&detail=list');
		}else{
			$warehouseshipping->D->query('rollback');$this->C->success('添加失败,请稍候重试!','index.php?action=warehouseshipping&detail=list');
		}
	}

	/*上传文件*/
	else{
		$data_error		= '';
		$all_arr		=  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath		=  $this->getLibrary('basefuns')->getsession('filepath');
		$tablelist	   .= '<table id="mytable">';

		/*表头特殊显示处理*/
		$tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'warehouseshipping');

		/*数据显示*/
		foreach($all_arr as $k=>$val){
			$exl_row++;
			$tablelist .= '<tr>';

			foreach( $val as $j=>$value) {
				$error_style = '';
    
                //检测仓库是否存在
				if($j == 'warehouse'){
					$dataeid = $esse->D->get_one_by_field(array('name'=>$value,'type'=>'2'),'id');
					if(!$dataeid['id']){
						$error_style = ' bgcolor="red" title="不存在的仓库名称!" ';
						$data_error++;
					}

                    //判断重复
                    $checktime = $global->changetime_ymdhis($val['checktime']);
                    $check_time = date('Y-m',strtotime($checktime));
                    $countid = $warehouseshipping->D->get_count(array('warehouse'=>$dataeid['id'],'checktime'=>$check_time));
                    if($countid){
                        $error_style = 'bgcolor="green" title="当前数据与原有重复"';
                        $data_error++;
                    }
				}

                //检测金额
                if ($j == 'shipping'){
                    if (!preg_match('/^[\d]+(\.?[\d]+)?$/',$value)){
                        $error_style = 'bgcolor="red" title="运费必须为数字"';
                        $data_error++;
                    }
                }
                
                //检测期间
                if ($j == 'checktime'){
                    $check_time = $global->changetime_ymdhis($value);
                    if (!$check_time){
                        $error_style = 'bgcolor="red" title="日期必填"';
                        $data_error++;
                    }
                }


				$tablelist .= '<td '.$error_style.'>&nbsp;'.$value.'</td>';
			}
			$tablelist .= '</tr>';
		}

		$tablelist.= '</table>';

		/*错误判断*/
		if(!$data_error && isset($all_arr)){
			$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
			$tablelist .= '<input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=warehouseshipping&detail=list">';
		}elseif($data_error){
			$exl_error_msg .= '<font color="#577dc6" size="-1">总共有'.$data_error.'处错误，请修正后重新上传（鼠标移到红色处可查看错误原因）。</font>';
			unlink($filepath);//有错的文件删除掉
		}
    }

  	$temlate_exlurl = 'data/uploadexl/sample/warehouseshipping.xls';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('exl_error_msg'=>$exl_error_msg,'exl_error_width'=>500,'title'=>'导入仓库运费-仓库运费列表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
}

/**
 *@title 删除仓库对应运费 
 *@author Jerry 
 *@create on 2014-1-6 
 */
elseif ($detail =='del') {
    if($id) {
        if($this->S->dao('warehouseshipping')->D->delete_by_field(array('id'=>$id)));
            $this->C->ajaxmsg(1);
    }

 }
 /**
 * @添加或者编仓库运费页面
 * @author by Jerry
 * @create on 2014-1-06
 */

elseif($detail == 'update' || $detail == 'add'){

	if($detail == 'update'){
		if(empty($id))exit('没有ID!');
        
		$warehouseshipping  	= $this->S->dao('warehouseshipping');
		$data	               	= $warehouseshipping->D->get_one_by_field(array('id'=>$id),'*');
        $checktimestr = "<input type=text name=checktime class='find-T check_notnull' value=".$data['checktime']." onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' >";
		$this->V->view['title'] = '编辑仓库运费-仓库运费列表(list)';
		$jump = 'index.php?action=warehouseshipping&detail=updatemod';
	}elseif($detail == 'add'){
		$this->V->view['title'] = '添加仓库运费-仓库运费列表(list)';
		$jump 		= 'index.php?action=warehouseshipping&detail=insertmod';
        $checktimestr = "<input type=text name=checktime class='find-T check_notnull'  onClick='WdatePicker({dateFmt:\"yyyy-MM\"})' >";
	}
    
    /*取得仓库下拉*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'','','id,name');
	for($i=0;$i<count($wdata);$i++){
		$warehouse[$wdata[$i]['id']] = $wdata[$i]['name'];
	}
    
	/*表单配置*/
	$conform = Array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = Array('1'=>'100','2'=>'220','3'=>'80');
    
    //仓库列表
    $selstr .= '<select name="warehouse"id="warehouse">';
    foreach ($warehouse as $k=>$v){
        $selstr .='<option value="'.$k.'"';
        if($k == $data['warehouse'])$selstr .= 'selected="selected"';$selstr .='>'.$v.'</option>';
    }
    $selstr .= '</select>';

	$disinputarr = Array();
	$disinputarr['id'] 	       = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['warehouse']  = array('showname'=>'仓库','value'=>$warehouse[$data['warehouse']],'extra'=>'*','datatype'=>'se','datastr'=>$selstr);
	$disinputarr['shipping']   = array('showname'=>'运费','value'=>$data['shipping'],'extra'=>'*','inextra'=>'class="check_notnull Check_isnum_dd2"');
	$disinputarr['checktime']  = array('showname'=>'审核期间','extra'=>'*','datatype'=>'se','datastr'=>$checktimestr);
	$temp = 'pub_edit';

    $jslink  = "<script src='./staticment/js/jquery.js'></script>\n";
    $jslink .= "<script src='./staticment/js/commoncheck.js?version=2'></script>";
    $jslink .= "<script type='text/javascript' src='./staticment/js/My97DatePicker/WdatePicker.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
}

/**
 * 新增仓库运费
 * @author Jerry
 * @create on 2012-11-05
 */

elseif ($detail =='insertmod') {
    
    $warehouseshipping = $this->S->dao('warehouseshipping');
    $countid = $warehouseshipping->D->get_count(array('warehouse'=>$warehouse,'checktime'=>$checktime));
    if (!$countid){
	   $wid = $warehouseshipping->D->insert(array('warehouse'=>$warehouse,'checktime'=>$checktime,'shipping'=>sprintf("%01.2f",$shipping)));
	   if($wid)$this->C->success('添加成功','index.php?action=warehouseshipping&detail=list');
    }else{
        $this->C->success('该仓库对应日期运费已存在','index.php?action=warehouseshipping&detail=list');
    }
}

/**
 * 编辑仓库运费
 * @author Jerry
 * @create on 2012-11-05
 */
elseif ($detail == 'updatemod') {
    
    $warehouseshipping = $this->S->dao('warehouseshipping');
    $countid = $warehouseshipping->D->get_count(array('warehouse'=>$warehouse,'checktime'=>$checktime));
    if (!$countid){
        $sid = $this->S->dao('warehouseshipping')->D->update_by_field(array('id'=>$id),array('warehouse'=>$warehouse,'checktime'=>$checktime,'shipping'=>sprintf("%01.2f",$shipping),'lastmodify'=>date('Y-m-d H:i:s')));
        if($sid) $this->C->success('修改成功','index.php?action=warehouseshipping&detail=list');
    }else{
        $this->C->success('该仓库对应日期运费已存在','index.php?action=warehouseshipping&detail=list');
    }
}

/*模板输出*/
 if($detail == 'list' ||$detail =='update' ||$detail =='insert' || $detail == 'add'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>
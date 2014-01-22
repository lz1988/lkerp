<?php
/*
 * Created on 2011-11-25
 * by hanson
 * @title	供应商管理
 */


/*供应商列表*/
 if($detail == 'list'){

 	$stypemu = array(
 		'esseid-s-l'=>'代码：',
 		'name-s-l'=>'名称：',
 	);

	$sqlstr.= ' and type=3';
	$datalist = $this->S->dao('esse')->D->get_list($sqlstr,'','esseid desc','id,esseid,name');

	$displayarr = array();
	$tablewidth = '900';

	$displayarr['esseid'] 	= array('showname'=>'代码','width'=>'60');
	$displayarr['name'] 	= array('showname'=>'名称');
	$displayarr['both']		= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=supplier&detail=delete&id={id}','url_e'=>'index.php?action=supplier&detail=edit&id={id}');

	$bannerstr 		= '<button class="six" onclick="window.location=\'index.php?action=supplier&detail=add\'">录入供应商</button>';
	$bannerstrarr[] = array('url'=>'index.php?action=supplier&detail=outport','value'=>'导出供应商','class'=>'six');
	$this->V->view['title'] = '供应商列表';
	$temp = 'pub_list';

}

/*导入供应商*/
elseif($detail == 'inport'){

    if(!$this->C->service('admin_access')->checkResRight('supplier_add')){$this->C->ajaxmsg();}
	/*上传文件保存路径的目录*/
	$upload_dir = "./data/uploadexl/";
	$fieldarray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');//有效的excel列表值
	$head 		= 1;//以第一行为表头
	$tablelist 	= '';
	$esse		= $this->S->dao('esse');

	/*读取已经上传的文件*/
	if($filepath){
		$all_arr 	=	$this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);
		$count_all	=	count($all_arr);
		$error_dd	= 0;

		$esse->D->query('begin');
		for($i = 1; $i < $count_all; $i++){

			//扩展内容处理，如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
			$extends = array('e_memerid'=>$all_arr[$i]['memerid'],'e_shortname'=>$all_arr[$i]['shortname'],'e_address'=>$all_arr[$i]['address'],'e_person'=>$all_arr[$i]['person'],'e_tel'=>$all_arr[$i]['tel'],'e_fax'=>$all_arr[$i]['fax'],'e_bankaddr'=>$all_arr[$i]['bankaddr'],'e_bankid'=>$all_arr[$i]['bankid'],'e_bankuser'=>$all_arr[$i]['bankuser'],'e_taxesrate'=>$all_arr[$i]['taxesrate'],'e_taxesnum'=>$all_arr[$i]['taxesnum'],'e_countmthod'=>$all_arr[$i]['countmthod']);
			$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

			//取得最大ID+1，实体编码递增
			$maxid = $esse->D->select('max(esseid) as max','type=3');
			$esseid = $maxid['max']+1;

			$sid = $esse->D->insert(array('name'=>$all_arr[$i]['name'],'cuser'=>$_SESSION['eng_name'],'type'=>'3 ','esseid'=>$esseid,'extends'=>$extends,'cdate'=>date('Y-m-d',time())));
			if(!$sid) $error_dd++;
		}

		if(empty($error_dd)){
			$esse->D->query('commit');$this->C->success('添加成功!','index.php?action=supplier&detail=list');
		}else{
			$esse->D->query('rollback');$this->C->success('添加失败,请稍候重试!','index.php?action=supplier&detail=list');
		}

	}

	/*上传文件*/
	else{

		$data_error		= '';
		$all_arr		=  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath		=  $this->getLibrary('basefuns')->getsession('filepath');
		$tablelist	   .= '<table id="mytable">';

		/*表头特殊显示处理*/
		$tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'supplier');

		/*数据显示*/
		foreach($all_arr as $k=>$val){
			$exl_row++;
			$tablelist .= '<tr>';

			foreach( $val as $j=>$value) {
				$error_style = '';
				if($j == 'name'){
					$backdata = $esse->D->get_one_by_field(array('name'=>$value,'type'=>'3'),'id');
					if($backdata['id']){
						$error_style = ' bgcolor="red" title="已存在的供应商名称!" ';
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
			$tablelist .= '<input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=supplier&detail=list">';
		}elseif($data_error){
			$tablelist .= '<font color="#577dc6" size="-1">总共有'.$data_error.'处错误，请修正后重新上传（鼠标移到红色处可查看错误原因）。</font>';
			unlink($filepath);//有错的文件删除掉
		}

	}

	$temlate_exlurl = 'data/uploadexl/sample/supplier.xls';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'导入供应商-添加供应商(add)-供应商列表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();

}

/*导出供应商*/
elseif($detail == 'outport'){

	$sqlstr.= ' and type=3';
	$datalist 	= $this->S->dao('esse')->D->get_allstr($sqlstr,'','','esseid,name,extends');
	$count_da	= count($datalist);
	for($i = 0; $i < $count_da; $i++){
		$datalist[$i] 	= $this->C->service('warehouse')->decodejson($datalist[$i]);
	}

	$filename 	= 'suppliser_'.date('Y-m-d',time());
	$head_array	= array('name'=>'名称','e_memerid'=>'助记码','e_shortname'=>'简称','e_address'=>'地址','e_person'=>'联系人','e_tel'=>'电话','e_fax'=>'传真','e_bankaddr'=>'开户银行','e_bankid'=>'银行帐号','e_bankuser'=>'银行户名','e_compuser'=>'法人代表','e_taxesrate'=>'增值税率','e_taxesnum'=>'税务登记号','e_countmthod'=>'结算方式');

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

/*添加供应商操作*/
elseif($detail == 'addmod'){

	if(!$this->C->service('admin_access')->checkResRight('supplier_add')){$this->C->sendmsg();}//权限判断


	date_default_timezone_set('Etc/GMT-8');//北京时间

	//扩展内容处理，如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
	$extends = array('e_memerid'=>$e_memerid,'e_shortname'=>$e_shortname,'e_address'=>$e_address,'e_person'=>$e_person,'e_tel'=>$e_tel,'e_fax'=>$e_fax,'e_bankaddr'=>$e_bankaddr,'e_bankid'=>$e_bankid,'e_bankuser'=>$e_bankuser,'e_taxesrate'=>$e_taxesrate,'e_taxesnum'=>$e_taxesnum,'e_countmthod'=>$e_countmthod);
	$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

	//取得最大ID+1，实体编码递增
	$esse  = $this->S->dao('esse');
	$maxid = $esse->D->select('max(esseid) as max','type=3');
	$esseid = $maxid['max']+1;

	$sid = $esse->D->insert(array('name'=>$name,'cuser'=>$_SESSION['eng_name'],'type'=>'3 ','esseid'=>$esseid,'extends'=>$extends,'cdate'=>date('Y-m-d',time())));
	if($sid) $this->C->success('添加成功','index.php?action=supplier&detail=list');
}


/*删除*/
elseif($detail == 'delete'){
	if(!$this->C->service('admin_access')->checkResRight('supplier_del')){$this->C->ajaxmsg(0);}//权限判断
	if($id){if($this->S->dao('esse')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

/*编辑操作*/
elseif($detail == 'editmod'){
	if(!$this->C->service('admin_access')->checkResRight('supplier_edit')){$this->C->sendmsg();}//权限判断

	//扩展内容处理，如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
	$extends = array('e_memerid'=>$e_memerid,'e_shortname'=>$e_shortname,'e_address'=>$e_address,'e_person'=>$e_person,'e_tel'=>$e_tel,'e_fax'=>$e_fax,'e_bankaddr'=>$e_bankaddr,'e_bankid'=>$e_bankid,'e_bankuser'=>$e_bankuser,'e_compuser'=>$e_compuser,'e_taxesrate'=>$e_taxesrate,'e_taxesnum'=>$e_taxesnum,'e_countmthod'=>$e_countmthod);
	$extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);

	$sid = $this->S->dao('esse')->D->update_by_field(array('id'=>$id),array('name'=>$name,'extends'=>$extends));
	if($sid) $this->C->success('修改成功','index.php?action=supplier&detail=list');
}


/*添加或者编辑供应商页面*/
elseif($detail == 'edit' || $detail == 'add'){

	if($detail == 'edit'){
	    if(!$this->C->service('admin_access')->checkResRight('supplier_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$esse  		= $this->S->dao('esse');
		$data 		= $esse->D->select('name,extends','id='.$id);
		$extends 	= json_decode($data['extends'],true);
		$this->V->view['title'] = '编辑供应商-供应商列表(list)';
		$jump = 'index.php?action=supplier&detail=editmod';
	}elseif($detail == 'add'){
	    if(!$this->C->service('admin_access')->checkResRight('supplier_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '添加供应商-供应商列表(list)';
		$jump 		= 'index.php?action=supplier&detail=addmod';
		$bannerstr 	= '<p style="color:#c6a8c6; font-size:12px">需要录入一批供应商信息，可以整理成表格，然后 <a href="index.php?action=supplier&detail=inport">点此导入</a></p>';
	}

	/*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'80');

	$disinputarr = array();
	$disinputarr['id'] 	 		= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['name'] 	 	= array('showname'=>'名称','value'=>$data['name'],'extra'=>'*','inextra'=>'onblur=checkname()');
	$disinputarr['e_memerid']   = array('showname'=>'助记码','value'=>$extends['e_memerid']);
	$disinputarr['e_shortname'] = array('showname'=>'简称','value'=>$extends['e_shortname']);
	$disinputarr['e_address'] 	= array('showname'=>'地址','value'=>$extends['e_address'],'extra'=>'*');
	$disinputarr['e_person']    = array('showname'=>'联系人','value'=>$extends['e_person'],'extra'=>'*');
	$disinputarr['e_tel']       = array('showname'=>'电话','value'=>$extends['e_tel'],'extra'=>'*');
	$disinputarr['e_fax']       = array('showname'=>'传真','value'=>$extends['e_fax']);
	$disinputarr['e_bankaddr']  = array('showname'=>'开户银行','value'=>$extends['e_bankaddr'],'extra'=>'*');
	$disinputarr['e_bankid']    = array('showname'=>'银行帐号','value'=>$extends['e_bankid'],'extra'=>'*');
	$disinputarr['e_bankuser']  = array('showname'=>'银行户名','value'=>$extends['e_bankuser'],'extra'=>'*');
	$disinputarr['e_compuser']  = array('showname'=>'法人代表','value'=>$extends['e_bankuser']);
	$disinputarr['e_taxesrate'] = array('showname'=>'增值税率','value'=>$extends['e_taxesrate']);
	$disinputarr['e_taxesnum']  = array('showname'=>'税务登记号','value'=>$extends['e_taxesnum']);
	$disinputarr['e_countmthod']= array('showname'=>'结算方式','value'=>$extends['e_countmthod']);

	$temp = 'pub_edit';
	$jslink  = "<script src='./staticment/js/supplier.js'></script>\n";
	$jslink .= "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
}

/*检查供应商重名，不能重名*/
elseif($detail == 'checkname'){
	$backdata = $this->S->dao('esse')->D->get_one_by_field(array('name'=>$name),'id');
	if($backdata) {echo '1';}else{echo '0';}
}



/**
 * @账期管理列表
 * @author by Jerry
 * @create on 2012-11-01
 */

 elseif ($detail == "accountmanage") {

    $stypemu = array(
        'sku-s-l' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SKU：',
		'name-s-l'	=> '&nbsp;&nbsp;供应商名称：',
		'esseid-s-l'=> '&nbsp;&nbsp;供应商编码：',
    );



    $accontrihs = $this->C->service('admin_access')->checkResRight('r_t_suppliaccount');//判断是否有账期信息管理权限

	/*根据权限来区别可搜索的条件*/
	if(!$accontrihs) array_splice($stypemu, 1, 1);

    $sqlstr    .= '  and e.type=3 order by id desc ';//sql条件
    $InitPHP_conf['pageval'] = 15; //分页数
    $datalist 	= $this->S->dao('supplieraccount')->getsupplieraccount($sqlstr);
    $displayarr = array();
    $tablewith  = '900';

    $displayarr['sku']      = array('showname'=>'sku',  'width'=>'60');

    /*有权限则显示名称，无权限显示编码*/
    if($accontrihs)
    $displayarr['name']   	= array('showname'=>'供应商名称', 'width'=>'100');
   	$displayarr['esseid']   = array('showname'=>'供应商编码', 'width'=>'100');
    $displayarr['issuetime']= array('showname'=>'账期', 'width'=>'60');
    $displayarr['account']  = array('showname'=>'定价', 'width'=>'60');
    $displayarr['remark']  	= array('showname'=>'备注', 'width'=>'60');

	/*有权限则显示操作*/
    if($accontrihs)
    $displayarr['both']     = array('showname'=>'操作', 'ajax'=>1, 'width'=>'60', 'url_d'=>'index.php?action=supplier&detail=del&id={id}', 'url_e'=>'index.php?action=supplier&detail=update&id={id}');
    $bannerstr 		= '<button class="six" onclick="window.location=\'index.php?action=supplier&detail=insert\'">录入账期</button>';
    $bannerstrarr[] = array('url'=>'index.php?action=supplier&detail=outputname', 'value'=>'导出账期', 'class'=>'six');

    $this->V->view['title'] = '账期列表';
    $temp = 'pub_list';
 }
/**
 * @账期删除
 * @author by Jerry
 * @create on 2012-11-01
 */

 elseif ($detail =='del') {
    if(!$this->C->service('admin_access')->checkResRight('supplieraccount_del')){$this->C->ajaxmsg(0);}//权限判断

    if($id) {
        if($this->S->dao('supplieraccount')->D->delete_by_field(array('id'=>$id)));
            $this->C->ajaxmsg(1);
    }

 }

/**
 * @添加或者编辑账期页面
 * @author by Jerry
 * @create on 2012-11-02
 */

elseif($detail == 'update' || $detail == 'insert'){

	if($detail == 'update'){
	    if(!$this->C->service('admin_access')->checkResRight('supplieraccount_edit')){$this->C->sendmsg();}//权限判断
		if(empty($id))exit('没有ID!');
		$supplieraccount  		= $this->S->dao('supplieraccount');
        $sqlstr.= ' where s.id='.$id;
		$datas	               	= $supplieraccount->getsupplieraccount($sqlstr);
        $data                   = $datas[0];
		$this->V->view['title'] = '编辑账期-账期列表(accountmanage)';
		$jump = 'index.php?action=supplier&detail=updatemod';
	}elseif($detail == 'insert'){
        if(!$this->C->service('admin_access')->checkResRight('supplieraccount_add')){$this->C->sendmsg();}//权限判断
		$this->V->view['title'] = '添加账期-账期列表(accountmanage)';
        $bannerstr 	= '<p style="color:#c6a8c6; font-size:12px">需要录入一批供应商账期信息，可以整理成表格，然后 <a href="index.php?action=supplier&detail=inputport">点此导入</a></p>';
		$jump 		= 'index.php?action=supplier&detail=insertmod';
	}

	/*表单配置*/
	$conform = Array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = Array('1'=>'100','2'=>'220','3'=>'80');

	$disinputarr = Array();
	$disinputarr['id'] 	       = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['sku'] 	   = array('showname'=>'sku','value'=>$data['sku'],'extra'=>'*','inextra'=>'onblur=checksku()');
	$disinputarr['name'] 	   = array('showname'=>'供应商名称','value'=>$data['name'],'extra'=>'*','inextra'=>'onblur=checkname()');

    //账期列表
    $selarr = array(''=>'=请选择=','货到付款'=>'货到付款','款到发货'=>'款到发货','周结'=>'周结','半月结'=>'半月结','月结'=>'月结','月结30天'=>'月结30天','月结45天'=>'月结45天','月结60天'=>'月结60天');
    $selstr .= '<select name="issuetime" id="issuetime" class="check_notnull" >';

    foreach ($selarr as $k=>$v){
        $selstr .='<option value="'.$k.'"';
        if($k == $data['issuetime'])
            $selstr .= 'selected="selected"';
        $selstr .='>'.$v.'</option>';
    }
    $selstr .= '</select>*';

	$disinputarr['issuetime']  = array('showname'=>'账期','value'=>$data['issuetime'],'datatype'=>'se','datastr'=>$selstr);
	$disinputarr['account']    = array('showname'=>'价格','value'=>$data['account'],'inextra'=>'class="check_notnull Check_isnum_dd2"','extra'=>'*');
	$disinputarr['remark']     = array('showname'=>'备注','value'=>$data['remark']);
	$temp = 'pub_edit';

    $jslink  = "<script src='./staticment/js/jquery.js'></script>\n";
    $jslink .= "<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />\n";
    $jslink .= "<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
    $jslink .= "<script src='./staticment/js/supplieraccount.js?version=".time()."'></script>\n";
}

/**
 * 新增账期
 * @author Jerry
 * @create on 2012-11-05
 */

elseif ($detail =='insertmod') {

	if(!$this->C->service('admin_access')->checkResRight('supplieraccount_add')) $this->C->sendmsg();//权限控制
    $eid = $this->S->dao('esse')->D->get_one(array('type'=>3,'name'=>$name),'id');
    if($eid == 0){$this->C->sendmsg('系统不存在供应商!');exit;}

    $pid = $this->S->dao('product')->D->get_one(array('sku'=>$sku),'pid');
    if($pid == 0){$this->C->sendmsg('系统不存在的SKU！');exit;}
	$supplierid = $this->S->dao('supplieraccount')->D->insert(array('eid'=>$eid,'pid'=>$pid,'issuetime'=>$issuetime,'account'=>sprintf("%01.2f",$account),'remark'=>$remark));
	if($supplierid)$this->C->success('添加成功','index.php?action=supplier&detail=accountmanage');

}

/**
 * 编辑账期
 * @author Jerry
 * @create on 2012-11-05
 */
elseif ($detail == 'updatemod') {

	if(!$this->C->service('admin_access')->checkResRight('supplieraccount_edit')){$this->C->sendmsg();}//权限判断

    $eid = $this->S->dao('esse')->D->get_one(array('type'=>3,'name'=>$name),'id');
    if($eid == 0){$this->C->sendmsg('系统不存在供应商!');exit;}

    $pid = $this->S->dao('product')->D->get_one(array('sku'=>$sku),'pid');
    if($pid == 0){$this->C->sendmsg('系统不存在的SKU！');exit;}

	$sid = $this->S->dao('supplieraccount')->D->update_by_field(array('id'=>$id),array('eid'=>$eid,'pid'=>$pid,'issuetime'=>$issuetime,'account'=>$account,'remark'=>$remark));
	if($sid) $this->C->success('修改成功','index.php?action=supplier&detail=accountmanage');
}

/*自动获取供应商列表*/
elseif ($detail == 'getsupplier') {
    $q = strtolower(trim($_GET["q"]));
	if (!$q) return;
    $result = array();
	$esseres = $this->S->dao('esse')->D->get_allstr(' and type=3 and name like "%'.$q.'%"','','','id,name');
	foreach ($esseres as $val) {
        $result[] = array(
            'id' => $val['id'],
            'name' => $val['name']
        );
    }

    echo json_encode($result);
}

/*
 *@导出账期
 *@authoer by Jerry
 *@create on 2012-11-12
 */

 elseif ($detail =='outputname') {

    $sqlstr.= '  and e.type=3';//sql条件
    $datalist = $this->S->dao('supplieraccount')->getsupplieraccount($sqlstr);
    $filename = '账期导出_'.date('Y-m-d');

	$head_array = array('sku'=>'产品sku','name'=>'供应商名称','esseid'=>'供应编码','issuetime'=>'账期','account'=>'金额');

	/*有管理权限可看供应商名称*/
	if(!$this->C->service('admin_access')->checkResRight('r_t_suppliaccount'))	array_splice($head_array,1,1);

    $this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
 }

 /*
  *@导入账期
  * @author by Jerry
  * @create on 2012-11-12
  */

elseif ($detail =='inputport') {
	if(!$this->C->service('admin_access')->checkResRight('supplieraccount_add')){$this->C->sendmsg();}//权限判断

   	/*上传文件保存路径的目录*/
	$upload_dir = "./data/uploadexl/";
	$fieldarray = array('A','B','C','D');//有效的excel列表值
	$head 		= 1;//以第一行为表头
	$tablelist 	= '';
	$esse		= $this->S->dao('esse');
    $product    = $this->S->dao('product');
    $suaccount  = $this->S->dao('supplieraccount');

	/*读取已经上传的文件*/
	if($filepath){

		$all_arr 	=	$this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);
		unlink($filepath);
		$count_all	=	count($all_arr);
		$error_dd	= 0;

		$suaccount->D->query('begin');
		for($i = 1; $i < $count_all; $i++){

            //取得sku的pid
            if (isset($all_arr[$i]['sku'])){
                $_pid = $product->D->get_one(array('sku'=>$all_arr[$i]['sku']),'pid');
            }

            //获取供应商的eid
            if (isset($all_arr[$i]['name'])){
                $_eid= $esse->D->get_one(array('name'=>$all_arr[$i]['name'],'type'=>'3'),'id');
            }

            //修改重复
            $_count = $suaccount->D->get_count(array('eid'=>$_eid,'pid'=>$_pid));
            if($_count){
                $sid = $suaccount->D->update(array('eid'=>$_eid,'pid'=>$_pid),array('issuetime'=>$all_arr[$i]['issuetime'],'account'=>sprintf("%01.2f",$all_arr[$i]['account'])));

            }else{//新增
                $sid = $suaccount->D->insert(array('pid'=>$_pid,'eid'=>$_eid,'issuetime'=>$all_arr[$i]['issuetime'],'account'=>sprintf ("%01.2f",$all_arr[$i]['account'])));
            }
            if(!$sid) $error_dd++;
		}

		if(empty($error_dd)){
			$suaccount->D->query('commit');$this->C->success('添加成功!','index.php?action=supplier&detail=accountmanage');
		}else{
			$suaccount->D->query('rollback');$this->C->success('添加失败,请稍候重试!','index.php?action=supplier&detail=accountmanage');
		}

	}

	/*上传文件*/
	else{
		$data_error		= '';
		$all_arr		=  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
		$filepath		=  $this->getLibrary('basefuns')->getsession('filepath');
		$tablelist	   .= '<table id="mytable">';

		/*表头特殊显示处理*/
		$tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'supplieraccount');

		/*数据显示*/
		foreach($all_arr as $k=>$val){
			$exl_row++;
			$tablelist .= '<tr>';

			foreach( $val as $j=>$value) {
				$error_style = '';
                //检测sku格式
                if($j == 'sku'){
                if(!preg_match('/^(\d{2}-\d{4}-\d{3})+$/',$value)){
                    $error_style = ' bgcolor="red" title="sku格式错误,类似于：xx-xxxx-xxx"';
                    $data_error++;
                    }

               	$datapid = $product->D->get_one_by_field(array('sku'=>$value),'pid');
                if (!$datapid['pid']){
                    $error_style = 'bgcolor="red" title="该sku不存在"';
                    $data_error++;
                }
                }

                //检测供应商是否存在
				if($j == 'name'){
					$dataeid = $esse->D->get_one_by_field(array('name'=>$value,'type'=>'3'),'id');
					if(!$dataeid['id']){
						$error_style = ' bgcolor="red" title="不存在的供应商名称!" ';
						$data_error++;
					}

                    //判断重复
                    $countid = $suaccount->D->get_count(array('eid'=>$dataeid['id'],'pid'=>$datapid['pid']));
                    if($countid){
                        $error_style = 'bgcolor="green" title="当前数据与原有重复，提交后将覆盖原有的账期与定价"';
                        $data_msg++;
                    }
				}

                //检测账期类别
                if($j == 'issuetime'){
                    $selary = array('货到付款','款到发货','周结','半月结','月结','月结30天','月结60天');
                    if (!in_array($value,$selary)){
                        $error_style = 'bgcolor="red" title="账期类型错误,类似于：'.implode(',',$selary).'"';
                        $data_error++;
                    }
                }

                //检测金额
                if ($j == 'account'){
                    if (!preg_match('/^[\d]+(\.?[\d]+)?$/',$value)){
                        $error_style = 'bgcolor="red" title="采购价必须为数字"';
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
			$tablelist .= '<input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=supplier&detail=list">';
            if($data_msg)
                $exl_error_msg .= '<font color="#577dc6" size="-1">总共有'.$data_msg.'处重复，提交后将覆盖原有的账期与定价。</font>';
		}elseif($data_error){
			$exl_error_msg .= '<font color="#577dc6" size="-1">总共有'.$data_error.'处错误，请修正后重新上传（鼠标移到红色处可查看错误原因）。</font>';
			unlink($filepath);//有错的文件删除掉
		}
    }

  	$temlate_exlurl = 'data/uploadexl/sample/suaccount.xls';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('exl_error_msg'=>$exl_error_msg,'exl_error_width'=>500,'title'=>'导入供应商账期-添加账期(insert)-账期列表(accountmanage)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();

}

/*模板输出*/
 if($detail == 'list' || $detail == 'add' || $detail == 'edit' ||$detail =='accountmanage' ||$detail =='update' ||$detail =='insert'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>

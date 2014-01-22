<?php
/*
 * create by wall on 2012-03-27 
 * */

/* create by wall on 2012-03-27
 * 客户列表模块*/
if ($detail == 'list') {
	$stypemu = array(
 		'esseid-s-l'=>'代码：',
 		'name-s-l'=>'名称：',
 	);

	$sqlstr.= ' and type=1';
	$datalist = $this->S->dao('esse')->D->get_list($sqlstr,'','esseid desc','id,esseid,name');
	$bannerstr = '<button onclick="window.location=\'index.php?action=customer&detail=add\'">录入客户</button>';

	$displayarr = array();
	$tablewidth = '700';

	$displayarr['esseid'] = array('showname'=>'代码','width'=>'60');
	$displayarr['name'] = array('showname'=>'名称');
	$displayarr['both'] = array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=customer&detail=delete&id={id}','url_e'=>'index.php?action=customer&detail=edit&id={id}');

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->view['title'] = '客户列表';
	$temp = 'pub_list';
	
}
/*删除*/
elseif($detail == 'delete'){	
	if($id){if($this->S->dao('esse')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}
elseif ($detail == 'add' || $detail == 'edit') {
	if($detail == 'edit'){
		if(empty($id))exit('没有ID!');
		$esse  = $this->S->dao('esse');
		$data = $esse->D->select('name,extends','id='.$id);
		$extends = json_decode($data['extends'],true);
		$this->V->view['title'] = '编辑客户';
		$jump = 'index.php?action=customer&detail=editmod';
	}elseif($detail == 'add'){
		$this->V->view['title'] = '添加客户';
		$jump = 'index.php?action=customer&detail=addmod';
	}

	/*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'80');
	 
	$disinputarr = array();
	$disinputarr['id'] 	 		= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['name'] 	 	= array('showname'=>'名称','value'=>$data['name'],'extra'=>'*','inextra'=>'onblur=checkname()');
	$disinputarr['e_allname']   = array('showname'=>'全名','value'=>$extends['e_allname']);
	$disinputarr['e_memerid']   = array('showname'=>'助记码','value'=>$extends['e_memerid']);
	$disinputarr['e_shortname'] = array('showname'=>'简称','value'=>$extends['e_shortname']);
	$disinputarr['e_address'] 	= array('showname'=>'地址','value'=>$extends['e_address'],'extra'=>'*');
	$disinputarr['e_area'] 		= array('showname'=>'区域','value'=>$extends['e_area']);
	$disinputarr['e_industry'] 	= array('showname'=>'行业','value'=>$extends['e_industry']);
	$disinputarr['e_person']    = array('showname'=>'联系人','value'=>$extends['e_person'],'extra'=>'*');
	$disinputarr['e_tel']       = array('showname'=>'电话','value'=>$extends['e_tel'],'extra'=>'*');
	$disinputarr['e_fax']       = array('showname'=>'传真','value'=>$extends['e_fax']);
	$disinputarr['e_credit']	= array('showname'=>'信用额度','value'=>$extends['e_credit']);
	$disinputarr['e_period']    = array('showname'=>'结算期限','value'=>$extends['e_period']);
	$disinputarr['e_zip']       = array('showname'=>'邮编','value'=>$extends['e_zip']);
	$disinputarr['e_email']     = array('showname'=>'邮件地址','value'=>$extends['e_email']);
	$disinputarr['e_bankaddr']  = array('showname'=>'开户银行','value'=>$extends['e_bankaddr'],'extra'=>'*');
	$disinputarr['e_bankid']    = array('showname'=>'银行帐号','value'=>$extends['e_bankid'],'extra'=>'*');
	$disinputarr['e_bankuser']  = array('showname'=>'银行户名','value'=>$extends['e_bankuser'],'extra'=>'*');
	$disinputarr['e_compuser']  = array('showname'=>'法人代表','value'=>$extends['e_bankuser']);
	$disinputarr['e_taxesrate'] = array('showname'=>'增值税率','value'=>$extends['e_taxesrate']);
	$disinputarr['e_taxesnum']  = array('showname'=>'税务登记号','value'=>$extends['e_taxesnum']);
	$disinputarr['e_countmthod']= array('showname'=>'结算方式','value'=>$extends['e_countmthod']);
	$disinputarr['e_city']		= array('showname'=>'城市','value'=>$extends['e_city']);
	$disinputarr['e_province']	= array('showname'=>'省份','value'=>$extends['e_province']);
	$disinputarr['e_country']	= array('showname'=>'国家','value'=>$extends['e_country']);
	$disinputarr['e_homepage']	= array('showname'=>'公司主页','value'=>$extends['e_homepage']);
	$disinputarr['e_legal']		= array('showname'=>'法人代表','value'=>$extends['e_legal']);
	$disinputarr['e_class']		= array('showname'=>'客户分类','value'=>$extends['e_class']);
	$disinputarr['e_type']		= array('showname'=>'销售方式','value'=>$extends['e_type']);
	$disinputarr['e_depa']		= array('showname'=>'分管部门','value'=>$extends['e_depa']);
	$disinputarr['e_bussman']	= array('showname'=>'专营业务员','value'=>$extends['e_bussman']);

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_edit';
	$jslink  = "<script src='./staticment/js/supplier.js'></script>\n";
	$jslink .= "<script src='./staticment/js/jquery.js'></script>\n";
	$jslink .= "<script src='./staticment/js/new.js'></script>\n";
}
elseif ($detail == 'addmod') {
	date_default_timezone_set('Etc/GMT-8');//北京时间

	//扩展内容处理，如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
	$extends = array('e_allname'=>$e_allname,
					'e_memerid'=>$e_memerid,
					'e_shortname'=>$e_shortname,
					'e_address'=>$e_address,
					'e_area'=>$e_area,
					'e_industry'=>$e_industry,
					'e_person'=>$e_person,
					'e_tel'=>$e_tel,
					'e_fax'=>$e_fax,
					'e_credit'=>$e_credit,
					'e_period'=>$e_period,
					'e_zip'=>$e_zip,
					'e_email'=>$e_email,
					'e_bankaddr'=>$e_bankaddr,
					'e_bankid'=>$e_bankid,
					'e_bankuser'=>$e_bankuser,
					'e_compuser'=>$e_compuser,
					'e_taxesrate'=>$e_taxesrate,
					'e_taxesnum'=>$e_taxesnum,
					'e_countmthod'=>$e_countmthod,
					'e_city'=>$e_city,
					'e_province'=>$e_province,
					'e_country'=>$e_country,
					'e_homepage'=>$e_homepage,
					'e_legal'=>$e_legal,
					'e_class'=>$e_class,
					'e_type'=>$e_type,
					'e_depa'=>$e_depa,
					'e_bussman'=>$e_bussman);
	$extends = function_exists('get_magic_quotes_gpc')?addslashes(json_encode($extends)):json_encode($extends);

	//取得最大ID+1，实体编码递增
	$esse  = $this->S->dao('esse');
	$maxid = $esse->D->select('max(esseid) as max','type=1');
	$esseid = $maxid['max']+1;

	$sid = $esse->D->insert(array('name'=>$name,'cuser'=>$_SESSION['eng_name'],'type'=>'1 ','esseid'=>$esseid,'extends'=>$extends,'cdate'=>date('Y-m-d',time())));
	if($sid) $this->C->success('添加成功','index.php?action=customer&detail=list');
}
elseif ($detail == 'editmod') {
	//扩展内容处理，如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。
	$extends = array('e_allname'=>$e_allname,
					'e_memerid'=>$e_memerid,
					'e_shortname'=>$e_shortname,
					'e_address'=>$e_address,
					'e_area'=>$e_area,
					'e_industry'=>$e_industry,
					'e_person'=>$e_person,
					'e_tel'=>$e_tel,
					'e_fax'=>$e_fax,
					'e_credit'=>$e_credit,
					'e_period'=>$e_period,
					'e_zip'=>$e_zip,
					'e_email'=>$e_email,
					'e_bankaddr'=>$e_bankaddr,
					'e_bankid'=>$e_bankid,
					'e_bankuser'=>$e_bankuser,
					'e_compuser'=>$e_compuser,
					'e_taxesrate'=>$e_taxesrate,
					'e_taxesnum'=>$e_taxesnum,
					'e_countmthod'=>$e_countmthod,
					'e_city'=>$e_city,
					'e_province'=>$e_province,
					'e_country'=>$e_country,
					'e_homepage'=>$e_homepage,
					'e_legal'=>$e_legal,
					'e_class'=>$e_class,
					'e_type'=>$e_type,
					'e_depa'=>$e_depa,
					'e_bussman'=>$e_bussman);
	$extends = function_exists('get_magic_quotes_gpc')?addslashes(json_encode($extends)):json_encode($extends);

	$sid = $this->S->dao('esse')->D->update_by_field(array('id'=>$id),array('name'=>$name,'extends'=>$extends));
	if($sid) $this->C->success('修改成功','index.php?action=customer&detail=list');
}
?>
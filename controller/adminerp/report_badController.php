<?php
/**
 * Created on 2012-10-17
 * by hanson 不良品查询
 *
 */

/*列表页*/
if($detail == 'list'){

	/*搜索选项*/
	$stypemu = array(
		'sku-s-l'=>'SKU：',
		'product_name-s-l'=>'品名：',
		'provider_id-a-e'=>'不良品归属仓：',
	);

	/*join表查询替换*/
	$sqlstr = strtr($sqlstr,array('sku'=>'p.sku','product_name'=>'d.product_name'));

	/*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
	$provider_idarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$provider_idarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}

	$InitPHP_conf['pageval']		= 20;
	$tablewidth 					= '1000';
	$datalist 						= $this->S->dao('process')->get_badstatu($sqlstr);

	foreach($datalist as &$val){
		$val['sku'] 				= '<a href=javascript:void(0);self.parent.addMenutab('.mt_rand(2000,3000).',"SKU不良品调拨","index.php?action=process_bad&detail=list&provider_id='.$val['provider_id'].'&sku='.$val['sku'].'")  title="点击查看明细">'.$val['sku'].'</a>';
	}

	$displayarr 					= array();
	$displayarr['sku'] 		 		= array('showname'=>'sku','width'=>'80');
	$displayarr['product_name']		= array('showname'=>'品名','width'=>'250');
	$displayarr['name'] 	 	 	= array('showname'=>'不良品归属仓','width'=>'100');
	$displayarr['sums'] 	 	 	= array('showname'=>'不良品数','width'=>'80');

	$bannerstrarr[] = array('url'=>'index.php?action=report_bad&detail=output','value'=>'导出数据');

	$this->V->mark(array('title'=>'不良品查询'));
	$temp = 'pub_list';
}

/*导出*/
elseif($detail == 'output'){

	/*join表查询替换*/
	$sqlstr		= strtr($sqlstr,array('sku'=>'p.sku','product_name'=>'d.product_name'));
	$datalist 	= $this->S->dao('process')->get_badstatu($sqlstr);

	$filename	= 'report_bad_'.date('Y-m-d',time());
	$head_array = array('sku'=>'SKU','product_name'=>'品名','name'=>'不良品归属仓','sums'=>'不良品数');

	$this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

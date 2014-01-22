<?php
/*
 * Created on 2012-7-12
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

if($detail == 'list'){

	$stypemu = array(
		'sku_code-s-l'=>'平台SKU：',
	);

	$datalist = $this->S->dao('sku_alias_api')->D->get_allstr($sqlstr,'sku_code','','id,sku_code');
	foreach($datalist as &$val){
		$val['sku_codee'] = $val['sku_coded'] = $val['sku_code'];//用于批量删除
	}


	$displayarr = array();
	$tablewidth = '550';

	$displayarr['sku_codee']	= array('showname'=>'checkbox','width'=>'50');
	$displayarr['sku_code']		= array('showname'=>'平台SKU','width'=>'450');
	$displayarr['delete']		= array('showname'=>'删除','width'=>'50','ajax'=>'1','url'=>'index.php?action=sku_alias_api&detail=del&id={id}');

	$this->V->view['title'] = '平台SKU';
	$bannerstr 				= '<button onclick=get_sku_code()>批量删除</button>';
	$jslink 				= "<script src='./staticment/js/process_shipment.js'></script>\n";
	$temp 					= 'pub_list';

}

/*单条删除*/
elseif($detail == 'del'){

	if(empty($id)) $this->C->sendmsg();
	$sku_alias_api	= $this->S->dao('sku_alias_api');
	$sku_alias		= $this->S->dao('sku_alias');

	$backdata		= $sku_alias_api->D->get_one_by_field(array('id'=>$id),'sku_code');

	$cid = $sku_alias->D->get_one_by_field(array('sku_code'=>$backdata['sku_code']),'pro_sku');
	if(!$cid['pro_sku']) $this->C->ajaxmsg(0,'删除失败，该别名未录入系统，无法删除！');

	$sid = $sku_alias_api->D->delete_by_field(array('sku_code'=>$backdata['sku_code']));
	if($sid) {echo '1';}else{echo '删除失败';}



}

/*批量删除*/
elseif($detail == 'delsome'){


	$errsku = '';
	$sku 	= stripslashes($sku);			/*去掉框架自加上的反斜杠*/
	$skuu	= strtr($sku,array("'"=>""));	/*删除引号*/
	$skuarr = explode(',',$skuu);
	$delsku = '';
	$candel = 0;
	$nodel 	= 0;

	$sku_alias_api	= $this->S->dao('sku_alias_api');
	$sku_alias		= $this->S->dao('sku_alias');

	/*检测SKU别名*/
	for($i = 0; $i<count($skuarr); $i++){
		$sid = $sku_alias->D->get_one_by_field(array('sku_code'=>$skuarr[$i]),'pro_sku');
		if(!$sid) {
			$nodel++;
		}else{
			$candel++;
			$delsku.= '"'.$skuarr[$i].'",';
		}
	}

	if($delsku){
		$delsku = substr($delsku,0,strlen($delsku)-1);
	}else{
		echo '删除 0 个，请先录入别名';exit();
	}

	$cid = $sku_alias_api->D->query('delete from sku_alias_api where sku_code in('.$delsku.')');

	if($cid) {echo '成功删除 '.$candel.' 个，失败 '.$nodel.' 个。'.$nodel.'个失败原因：未录入别名。';}else{echo '操作失败，请重试';}


}

if($detail == 'list'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}


?>

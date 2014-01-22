<?php
/*
 * create on 2012-06-04
 * by wall
 * 亚马逊接口管理模块
 * */

if ($detail == 'list') {
	//设置分页数为10
	$InitPHP_conf['pageval'] = 15;
	$datalist = $this->S->dao('info_amazon')->get_all_list('');

	$this->V->mark(array('title'=>'MWS的信息管理'));

	$bannerstr = '<button onclick="window.location=\'index.php?action=amazon_interface&detail=add\'">添加帐号</button>';

	$displayarr = array();
	$displayarr['ia_seller_id'] 	    	= array('showname'=>'卖家帐号');
	$displayarr['ia_merchant_id'] 	    	= array('showname'=>'卖家编号');
    $displayarr['ia_marketplace_id']    	= array('showname'=>'市场编号');
	$displayarr['ia_aws_access_key_id'] 	= array('showname'=>'访问密匙编号');
	$displayarr['ia_aws_secret_access_key'] = array('showname'=>'MWS 的密匙');
	$displayarr['ia_port'] 	    			= array('showname'=>'端点');
	$displayarr['ia_sold_way']				= array('showname'=>'销售渠道');
	$displayarr['housename']				= array('showname'=>'关联仓库');
	$displayarr['both']         			= array('showname'=>'操作','ajax'=>1,'url_d'=>'index.php?action=amazon_interface&detail=delete&id={id}','url_e'=>'index.php?action=amazon_interface&detail=edit&id={id}');

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}


/*删除帐户*/
elseif ($detail == 'delete') {
	if (!$this->C->service('admin_access')->checkResRight('a_t_sys_amazon')) {
		$this->C->ajaxmsg(0);
	}

	if ($this->S->dao('info_amazon')->D->delete_by_field(array('id'=>$id))) {
		$this->C->ajaxmsg(1);
	}
}


/*添加编辑*/
elseif ($detail == 'add' || $detail== 'edit') {

	if (!$this->C->service('admin_access')->checkResRight('a_t_sys_amazon')) {
		$this->C->ajaxmsg(0);
	}

	/*端点数组*/
	$portarr = array(
		'US -- 美国'=>'https://mws.amazonservices.com',
		'UK -- 英国'=>'https://mws.amazonservices.co.uk',
		'FR -- 法国'=>'https://mws.amazonservices.fr'
	);


    if ($detail == 'add') {
        $title = '增加MWS的信息-MWS的信息管理(list)';
        $conform = array('method'=>'post','action'=>'index.php?action=amazon_interface&detail=addmod','width'=>'650px');
    }
    elseif ($detail == 'edit') {
        $title = '修改MWS的信息-MWS的信息管理(list)';
        $conform = array('method'=>'post','action'=>'index.php?action=amazon_interface&detail=editmod&id='.$id,'width'=>'650px');
        $data = $this->S->dao('info_amazon')->get_one_by_id($id);
    }

    // 端点
    $ia_port = '<select name="ia_port"><option value="">== 请选择 ==</option>';
    foreach ($portarr as $key=>$val) {
    	$ia_port .= '<option value="'.$val.'" ';
    	if ($data['ia_port'] == $val) {
    		$ia_port .= ' selected="selected" ';
    	}
    	$ia_port .= ' >'.$key.'</option>';
    }
    $ia_prot .= '</select>';

    /*销售渠道*/
	$ia_sold_way = $this->C->service('global')->get_sold_way('1','sold_way','wayname',$data['ia_sold_way']);
	$ia_sold_way = strtr($ia_sold_way,array('sold_way'=>'ia_sold_way'));


	/*关联仓库*/

	$sqlhouse 	= 'select id,name from esse where type="2"  and id not in (select ia_houseid from info_amazon where ia_houseid !="'.$data['ia_houseid'].'")';
	$bachdata 	= $this->S->dao('esse')->D->query_array($sqlhouse);

	$ia_houseid = '<select name="ia_houseid" ><option value="">== 请选择 ==</option>';

	foreach($bachdata as $val){
		$sele_house = ($val['id'] == $data['ia_houseid'])?'selected':'';
		$ia_houseid.= '<option value="'.$val['id'].'" '.$sele_house.' >'.$val['name'].'</option>';
	}
	$ia_houseid.= '</select>';


	$colwidth = array('1'=>'200','2'=>'300','3'=>'200');
	$displayarr = array();
	$disinputarr['ia_seller_id'] 	    			= array('showname'=>'卖家帐号', 'value'=>$data['ia_seller_id'], 'showtips'=>'SELLER_ID');
	$disinputarr['ia_merchant_id']     				= array('showname'=>'卖家编号', 'value' => $data['ia_merchant_id'], 'showtips'=>'Merchant_ID');
	$disinputarr['ia_marketplace_id']     			= array('showname'=>'市场编号', 'value' => $data['ia_marketplace_id'], 'showtips'=>'Marketplace ID');
	$disinputarr['ia_aws_access_key_id']     		= array('showname'=>'访问密匙编号', 'value' => $data['ia_aws_access_key_id'], 'showtips'=>'AWS_ACCESS_KEY_ID');
	$disinputarr['ia_aws_secret_access_key']     	= array('showname'=>'MWS 的密匙', 'value' => $data['ia_aws_secret_access_key'], 'showtips'=>'AWS_SECRET_ACCESS_KEY');
	$disinputarr['ia_port']     					= array('showname'=>'端点', 'datatype'=>'se','datastr'=>$ia_port, 'showtips'=>'');
	$disinputarr['ia_sold_way']						= array('showname'=>'销售渠道','datatype'=>'se','datastr'=>$ia_sold_way);
	$disinputarr['ia_houseid']						= array('showname'=>'关联仓库','datatype'=>'se','datastr'=>$ia_houseid);

	$this->V->mark(array('title'=>$title));
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_edit';
}


/*保存添加操作*/
elseif ($detail == 'addmod') {
	if (!$this->C->service('admin_access')->checkResRight('a_t_sys_amazon')) {
		$this->C->ajaxmsg(0);
	}
	if($this->S->dao('info_amazon')->D->insert(array('ia_seller_id'=>$ia_seller_id,'ia_merchant_id'=>$ia_merchant_id,'ia_marketplace_id'=>$ia_marketplace_id, 'ia_aws_access_key_id'=>$ia_aws_access_key_id, 'ia_aws_secret_access_key'=>$ia_aws_secret_access_key, 'ia_port'=>$ia_port, 'ia_sold_way'=>$ia_sold_way,'ia_houseid'=>$ia_houseid))){
		$this->C->success('添加成功','index.php?action=amazon_interface&detail=list');
	}

}

/*保存编辑操作*/
elseif ($detail == 'editmod') {
	if (!$this->C->service('admin_access')->checkResRight('a_t_sys_amazon')) {
		$this->C->ajaxmsg(0);
	}
	if($id){
		if($this->S->dao('info_amazon')->D->update_by_field(array('id'=>$id),array('ia_seller_id'=>$ia_seller_id,'ia_merchant_id'=>$ia_merchant_id,'ia_marketplace_id'=>$ia_marketplace_id, 'ia_aws_access_key_id'=>$ia_aws_access_key_id, 'ia_aws_secret_access_key'=>$ia_aws_secret_access_key, 'ia_port'=>$ia_port, 'ia_sold_way'=>$ia_sold_way,'ia_houseid'=>$ia_houseid)))
		$this->C->success('修改成功','index.php?action=amazon_interface&detail=list');
	}
}
?>
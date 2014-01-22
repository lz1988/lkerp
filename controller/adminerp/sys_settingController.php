<?php
/*@title - 系统信息设置
 *
 * 图片上传 BY wall
 * 其它设置 BY hanson
 */

$upload = 'index.php?action=sys_setting&detail=upload';
$commit = 'index.php?action=sys_setting&detail=commit';
$width = 300;
$height = 90;
$imgurl = './data/images/logo/logo.png';
$is_conallset	= '';
$is_conallset 	= $this->C->service('admin_access')->checkResRight('a_t_sys_settings');

if ($detail == 'setting') {

	$sys_setting	= $this->S->dao('sys_setting');
	$set_arr		= array();
	$set_datalist	= $sys_setting->get_allsettings($is_conallset);

	/*转换为一维数组*/
	foreach($set_datalist as $val){
		$set_arr[$val['remer']] =  $val['value'];
	}

	/*如果拥有其它配置的权限*/
	if($is_conallset){

		/*各页面默认仓库的设置*/
		$default_house_arr = array(
				'makestock_warehouse'=>'采购下单的默认仓库：&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp;',
				'makeshipment_house'=>'销售下单(添加订单)默认的发货仓库：',
				'listshipment_house'=>'销售下单(列表页)的默认筛选仓库：&nbsp; &nbsp; ',
				'maketransfer_house'=>'物料调拨添加订单默认的发货仓库：&nbsp; &nbsp;',
				'recetransfer_house'=>'物料调拨添加订单默认的目的仓库：&nbsp; &nbsp;',
				'extra_inhouse'=>'其它入库默认的入库仓库：&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
				'extra_outhouse'=>'其它出库默认的出库仓库：&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
				'badtrans_house'=>'不良品调拨默认的归属仓库：&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;',
				);

		$default_house_arrkeys = array_keys($default_house_arr);

		$whouse = '';
		for($i=0; $i<count($default_house_arrkeys); $i++){

			$whouse.= $default_house_arr[$default_house_arrkeys[$i]];
			$whouse.= $this->C->service('warehouse')->get_whouse($default_house_arrkeys[$i],'name','id','id',$set_arr[$default_house_arrkeys[$i]]);
			$whouse.= '<br>';

		}

		/*判断库存的仓库设置--Start*/
		$check_stock_whouse_html 	= '';
		$backdata_whouses			= $this->S->dao('esse')->D->get_allstr(' and type="2"','','','name,id');

		/*如果未有配置，则默认是选中*/
		$backdaat_set_arr			=  $sys_setting->D->get_one_by_field(array('remer'=>'check_stock_whouse'),'value,id');
		if(empty($backdaat_set_arr['id'])) $che_checked_default  = 'checked';

		$check_stock_whouse 		= json_decode($backdaat_set_arr['value'],true);//解压设置的仓库

		/*生成仓库选择框*/
		foreach($backdata_whouses as $val){
			if(in_array($val['id'],$check_stock_whouse)) {
				$che_checked= 'checked';
			}else{
				$che_checked= $che_checked_default == 'checked'?'checked':'';
			}
			$check_stock_whouse_html.= '<label><input type=checkbox name=check_stock_whouse[] value='.$val['id'].' '.$che_checked.'>'.$val['name'].'&nbsp; </label>';
		}
		/*判断库存的仓库设置--End*/


		/*禁止匹配直库的仓库设置--Start*/
		$check_fbden_whouse = json_decode($set_arr['check_fbden_whouse'],true);//解压设置的仓库
		foreach($backdata_whouses as $val){
			$che_checked 			 = in_array($val['id'],$check_fbden_whouse)?'checked':'';
			$check_fbden_whouse_html.= '<label><input type=checkbox name=check_fbden_whouse[] value='.$val['id'].' '.$che_checked.'>'.$val['name'].'&nbsp; </label>';
		}

		/*禁止匹配直库的仓库设置--End*/



		/*产品列表的默认币别*/
		$product_list_cost_html = '';
		$product_list_cost_sele = $set_arr['product_list_cost'] == 'CNY'|| empty($set_arr['product_list_cost'])?'checked':'';
		$product_list_cost_html.='<label><input type="radio" name="product_list_cost" value="CNY" '.$product_list_cost_sele.'>人民币(CNY)</label> &nbsp; ';
		$product_list_cost_sele = $set_arr['product_list_cost'] == 'USD'?'checked':'';
		$product_list_cost_html.='<label><input type="radio" name="product_list_cost" value="USD" '.$product_list_cost_sele.' >美元(USD)</label> &nbsp; ';
		$product_list_cost_sele = $set_arr['product_list_cost'] == 'GBP'?'checked':'';
		$product_list_cost_html.='<label><input type="radio" name="product_list_cost" value="GBP" '.$product_list_cost_sele.' >英磅(GBP)</label>';

		/*运费估算*/
		$selling_countship_html = '';
		$selling_countship_check= empty($set_arr['selling_countship'])?'checked':'';
		$selling_countship_html.= '<label><input type="radio" name="selling_countship" '.$selling_countship_check.' value="0">不估算</label> &nbsp; ';
		$selling_countship_check= empty($set_arr['selling_countship'])?'':'checked';
		$selling_countship_html.= '<label><input type="radio" name="selling_countship" '.$selling_countship_check.' value="1">估算</label>';


		/*系统本位币设置--(暂只允许技术设定)*/
		$system_defaultcoin_html = '';
		$system_defaultcoin_disb = empty($set_arr['system_defaultcoin'])?'':'disabled';

		$system_defaultcoin_sele = $set_arr['system_defaultcoin'] == 'CNY'?'checked':'';
		$system_defaultcoin_html.= '<label><input type="radio" name="system_defaultcoin" value="CNY" '.$system_defaultcoin_sele.' '.$system_defaultcoin_disb.'>人民币</label> &nbsp;';
		$system_defaultcoin_sele = $set_arr['system_defaultcoin'] == 'USD'?'checked':'';
		$system_defaultcoin_html.= '<label><input type="radio" name="system_defaultcoin" value="USD" '.$system_defaultcoin_sele.' '.$system_defaultcoin_disb.'>美元</label>';


	}

    $title = '信息设置';
    $this->V->mark(array('title'					=> $title,
						'showurl' 					=> $imgurl,
						'imgwidth' 					=> $width,
						'imgheight' 				=> $height,
						'uploadaction' 				=> $upload,
						'commitaction' 				=> $commit,
						'set_arr'					=> $set_arr,
						'is_conallset'				=> $is_conallset,
						'whouse'					=> $whouse,
						'check_stock_whouse_html'	=> $check_stock_whouse_html,
						'check_fbden_whouse_html'	=> $check_fbden_whouse_html,
						'product_list_cost_html'	=> $product_list_cost_html,
						'selling_countship_html'	=> $selling_countship_html,
						'system_defaultcoin_html'	=> $system_defaultcoin_html,
	));

	$this->V->set_tpl('adminweb/sys_setting');
    display();
}

/*上传图片*/
elseif ($detail == 'upload') {
    $big_image = $_FILES['filename'];
	$big_name = $big_image['name'];
	$tmp_name = $big_image['tmp_name'];
    $extName = substr($big_name,strrpos($big_name,'.')+1);
	$newname = date("YmdHis") . '_' . rand(10000, 99999) . '.' .$extName;
	$filepath = "./data/images/logo/".$newname;
	$val = move_uploaded_file($tmp_name, $filepath);
    if ($val) {
        echo "<script>parent.callback({msg:'',filename:'".$filepath."'})</script>";
    }
    else {
        echo "<script>parent.callback({msg:''})</script>";
    }
}

/*提交保存*/
elseif ($detail == 'commit') {

	$sys_setting	= $this->S->dao('sys_setting');

	/*个人设置数组*/
	$settings_arr= array(
			'0'=>'dbclick_tag'	//双击标签
	);


	/*个人设置*/
	for($i = 0; $i<count($settings_arr); $i++){

		/*先查找是否存在*/
		$backdata = $sys_setting->D->get_one_by_field(array('remer'=>$settings_arr[$i],'bid'=>$_SESSION['uid']),'id');

		if($backdata['id']){
			$sys_setting->D->update_by_field(array('remer'=>$settings_arr[$i],'bid'=>$_SESSION['uid']),array('value'=>$$settings_arr[$i]));
		}else{
			$sys_setting->D->insert(array('remer'=>$settings_arr[$i],'bid'=>$_SESSION['uid'],'value'=>$$settings_arr[$i]));
		}
	}


	/*有系统设置权限*/
	if($is_conallset){

			/*系统设置数组*/
			$settings_sysarr = array(
			'makestock_warehouse',
			'makeshipment_house',
			'listshipment_house',
			'maketransfer_house',
			'recetransfer_house',
			'extra_inhouse',
			'extra_outhouse',
			'badtrans_house',
			'check_stock_whouse',//设置需要检测发货仓库库存的仓库。
			'check_fbden_whouse',//禁止匹配出库的仓库
			'product_list_cost',//产品列表的成本价币别
			'selling_countship',//销售下单时估算运费
			'system_defaultcoin'//本位币
			);



		/*系统设置*/
		for($i=0; $i<count($settings_sysarr); $i++){

			/*先查找是否存在*/
			$backdata = $sys_setting->D->get_one_by_field(array('remer'=>$settings_sysarr[$i],'bid'=>'sys'),'id');

			/*数组的统一压缩json格式*/
			$$settings_sysarr[$i] = is_array($$settings_sysarr[$i])?json_encode($$settings_sysarr[$i]):$$settings_sysarr[$i];

			/*本位币只允许一次添加*/
			if($settings_sysarr[$i] == 'system_defaultcoin'){
				if(!$backdata['id']){
					$sys_setting->D->insert(array('remer'=>$settings_sysarr[$i],'bid'=>'sys','value'=>$$settings_sysarr[$i]));
				}
			}else{
				if($backdata['id']){
					$sys_setting->D->update_by_field(array('remer'=>$settings_sysarr[$i],'bid'=>'sys'),array('value'=>$$settings_sysarr[$i]));
				}else{
					$sys_setting->D->insert(array('remer'=>$settings_sysarr[$i],'bid'=>'sys','value'=>$$settings_sysarr[$i]));
				}
			}


		}

		/*更改logo*/
	    $newfilename = $_REQUEST['newfilename'];
	    if (!empty($newfilename)) {
	        $this->C->service('global')->my_image_resize($newfilename,$imgurl, $width, $height);
	    }
	}


	echo "<script>parent.callback({msg:'保存成功',isreload:'1'})</script>";

}

/*加载标签配置*/
elseif($detail == 'load_setting'){
	echo  $this->C->service('global')->sys_settings('dbclick_tag',$_SESSION['uid']);
}
?>

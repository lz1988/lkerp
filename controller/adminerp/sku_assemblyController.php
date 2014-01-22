<?php
/**
 * create by wall,update by hanson
 * prefix_split,suffix_split 拆分表前缀，后缀
 * prefix_assemble,suffix_assemble 组装表前缀，后缀
 *
 */
$prefix_split 		= '';
$prefix_assemble 	= 'as_';
$suffix_split 		= '[]';
$suffix_assemble 	= '';
//组装编号前缀
$assemble_prefix	= 'split';

/**
 * create by wall on 2012-03-26
 * 组装产品方案列表
 *
 */

if ($detail == 'list'){
	$stypemu = array(
		'assembleid-s-l'	=> '方案：',
		'assemble_sku-s-l'	=> '合SKU：',
		'split_sku-s-l'		=> '分SKU：',
		'cuser-s-l'			=>'建立者：',
	);
	/*** 处理查询语句 ***/
    //echo $sqlstr;
	$sqlstr = str_replace('assemble_sku', 'sa.pid=p.pid and p.sku', $sqlstr);
	$sqlstr = str_replace('split_sku', 'sa.child_pid=cp.pid and cp.sku', $sqlstr);
	$sqlstr = str_replace('cuser', 'sa.cuser', $sqlstr);

	$sku_assembly = $this->S->dao('sku_assembly');
	$product      = $this->S->dao('product');
	$process      = $this->S->dao('process');
    $product_cost = $this->S->dao('product_cost');

	$bannerstr    = '<button onclick=window.location="index.php?action=sku_assembly&detail=sku_assemble">组装产品</button>';
	//$bannerstr   .= '<button onclick=window.location="index.php?action=sku_assembly&detail=sku_split">拆分产品</button>';
    
    /*** 导出功能 ***/
    //$bannerstr   .= '<button onclick=window.location="index.php?action=sku_assembly&detail=sku_output">导出拆装</button>';
    $bannerstrarr[] = array('url'=>'index.php?action=sku_assembly&detail=sku_output', 'value'=>'导出数据');

	/*** 设置分页数为15 ***/
	$InitPHP_conf['pageval'] = 15;

	$listrs = $sku_assembly->get_list_in_assembleid($sqlstr);

	$i = 0;//单页显示的第i行

	/*** 取消分页 ***/
	unset($InitPHP_conf['pageval']);
	foreach ($listrs as $val) {
		$datalist[$i] 			= $val;
		$rs 					= $sku_assembly->select_by_assembleid($val['assembleid']);
		$datalist[$i]['cuser']	= $val['cuser'];
		$rs1 					= $product->get_product_by_id($rs['0']['pid']);
		$datalist[$i]['assemblesku'] 	= $rs1['sku'];
		$datalist[$i]['assemblename'] 	= $rs1['product_name'];
		$datalist[$i]['oper'] 			= '<a href="index.php?action=sku_assembly&detail=sku_assemble&assembleid='.$val['assembleid'].'" title="组装"><img src="./staticment/images/nav_resetPassword.gif" border="0" width="16" height="16"></a>';
		//$datalist[$i]['oper'] 		   .= '<a href="index.php?action=sku_assembly&detail=sku_split&assembleid='.$val['assembleid'].'" title="拆分"><img src="./staticment/images/nav_changePassword.gif" border="0" width="16" height="16"></a>';
		$datalist[$i]['oper'] 		   .= '<a href="index.php?action=sku_assembly&detail=delete&assembleid='.$val['assembleid'].'" title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';

        $_sum   = 0;
        $k      = 0;
		foreach ($rs as $k=>$val1) {
			$rs1 = $product->get_product_by_id($val1['child_pid']);
            /*获得cost2*/
        	$cost = $product_cost->D->select('cost2','pid='.$rs1['pid']);
            $_sumcost2[$i]['sum'] = $cost['cost2']*$val1['quantity'];
            $_sum += $_sumcost2[$i]['sum'];
			$datalist[$i]['splitsku'] = $rs1['sku'].'('.$val1['quantity'].'件)';
			$datalist[$i]['splitname'] = $rs1['product_name'];
            $k++;
			$i++;
		}

         $datalist[$i-$k]['sum'] = $_sum;
	}

	$InitPHP_conf['pageval'] = 15;

	/*** 获取分页 ***/
	$sku_assembly->get_list_in_assembleid($sqlstr);
	$tablewidth = '980';
	$displayarr['assembleid'] 		= array('showname' => '组装编号', 'width' => '100');
	$displayarr['assemblesku'] 		= array('showname' => '组装产品SKU', 'width' => '150');
    $displayarr['sum']              = array('showname' => '总成本价','width'=>'100');
	$displayarr['assemblename']		= array('showname' => '组装产品名称', 'width' => '200');
	$displayarr['splitsku']			= array('showname' => '拆分产品SKU', 'width' => '150');
	$displayarr['splitname']		= array('showname' => '拆分产品名称', 'width' => '200');
	$displayarr['cuser']			= array('showname' => '方案建立者', 'width' => '100');
	$displayarr['oper']				= array('showname' => '操作', 'width' => '80');


	$jslink .= "<script src='./staticment/js/pushdiv.js'></script>\n";
	$jslink .= '<link href="./staticment/css/base.css" rel="stylesheet" type="text/css" />';

	$this->V->view['title'] = 'SKU组装表';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$temp = 'pub_list';
}

/**
 * create by wall on 2012-03-26
 * 删除组装方案编号的组装方案
 *
 */
elseif ($detail == 'delete') {
      
    //权限控制
    if(!$this->C->service('admin_access')->checkResRight('r_s_del')){$this->C->sendmsg();}
    
	$did = $this->S->dao('sku_assembly')->D->delete_by_field(array('assembleid'=>$assembleid));
	if ($did) {
		$this->C->success('删除成功','index.php?action=sku_assembly&detail=list');
	}
	else {
		$this->C->success('删除失败','index.php?action=sku_assembly&detail=list');
	}
}
/**
 * create by wall
 * sku 组装界面
 *
 */
elseif ($detail == 'sku_assemble') {
    
   	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_s_add')){$this->C->sendmsg();}
	//条件框
	$condition = array(
		array('label'=>'仓 库 :&nbsp;&nbsp;', 'plug'=>$this->C->service('warehouse')->get_whouse('warehouse', 'name', 'id', 'id')),
		array('label'=>'&nbsp;&nbsp;方 案 :&nbsp;&nbsp;', 'plug'=>'<input class="select_assembletype" type="text" value="'.$assembleid.'"/>'),
		array('label'=>'&nbsp;&nbsp;', 'plug'=>'<input class="select_sumbit" type="button" value="查询">')
	);
	//div表格
	$classdiv = array(
		array(
			'classname' => 'split_sku',			//类名，决定加载哪个表格split_sku为拆分表，assemble_sku为组装表
			'class' => 'wall_addrow',			//第一个按钮classname，wall_addrow为添加一行，wall_submit为提交, hideinput为隐藏按钮
			'value' => '增加一行',					//第一个按钮显示内容
		),
		array(
			'classname' => 'assemble_sku',		//类名，决定加载哪个表格split_sku为拆分表，assemble_sku为组装表
			'class' => 'wall_submit',			//第一个按钮classname，wall_addrow为添加一行，wall_submit为提交
			'value' => '组装检测',					//第一个按钮显示内容
			'extend'=> 'subtype="assemble"'		//扩展，用于检验提交
		)
	);
	//表单提交url
	$form_action = 'index.php?action=sku_assembly&detail=modassemble';
	$this->V->view['title'] = 'SKU组装-SKU组装表(list)';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/sku_assembly');
	$this->V->mark(
				array(
					'condition'=>$condition,
					'classdiv'=>$classdiv,
					'prefix_split'=>$prefix_split,
					'suffix_split'=>$suffix_split,
					'prefix_assemble'=>$prefix_assemble,
					'suffix_assemble'=>$suffix_assemble,
					'form_action'=>$form_action));
	display();
}
/* create by wall
 * sku 拆分界面
 * */
if ($detail == 'sku_split') {
    
    	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('r_s_edit')){$this->C->sendmsg();}
	//条件框
	$condition = array(
		array('label'=>'仓 库 :&nbsp;&nbsp;', 'plug'=>$this->C->service('warehouse')->get_whouse('warehouse', 'name', 'id', 'id' )),
		array('label'=>'&nbsp;&nbsp;方 案 :&nbsp;&nbsp;', 'plug'=>'<input class="select_assembletype" type="text" value="'.$assembleid.'"/>'),
		array('label'=>'&nbsp;&nbsp;', 'plug'=>'<input class="select_sumbit" type="button" value="查询">')
	);
	//div表格
	$classdiv = array(
		array(
			'classname' => 'assemble_sku',				//类名，决定加载哪个表格split_sku为拆分表，assemble_sku为组装表
			'class' => 'wall_submit',				//第一个按钮classname，wall_addrow为添加一行，wall_submit为提交
			'value' => '拆分检测',						//第一个按钮显示内容
			'extend'=> 'subtype="split"'		//扩展，用于检验提交
		),
		array(
			'classname' => 'split_sku',					//类名，决定加载哪个表格split_sku为拆分表，assemble_sku为组装表
			'class' => 'wall_addrow',				//第一个按钮classname，wall_addrow为添加一行，wall_submit为提交, hideinput为隐藏按钮
			'value' => '加一行',					//第一个按钮显示内容
		)

	);
	$form_action = 'index.php?action=sku_assembly&detail=modsplit';
	$this->V->view['title'] = 'SKU拆分-SKU组装表(list)';
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/sku_assembly');
	$this->V->mark(
				array(
					'condition'=>$condition,
					'classdiv'=>$classdiv,
					'prefix_split'=>$prefix_split,
					'suffix_split'=>$suffix_split,
					'prefix_assemble'=>$prefix_assemble,
					'suffix_assemble'=>$suffix_assemble,
					'form_action'=>$form_action));
	display();
}
/**
 *  create by wall
 * 拆分表模块
 */
elseif ($detail == 'split_table') {
	//如果传入了组装方案，获取列表，所有列不可操作
	if ($assembleid) {
		$datalist = $this->S->dao('sku_assembly')->select_by_assembleid($assembleid);
		foreach($datalist as &$val) {
			$backdata = $this->S->dao('product')->D->get_one_by_field(array('pid'=>$val['child_pid']),'sku,product_name,upc_or_ean');
			$val['sku'] = $backdata['sku'];
			$val['product_name'] = $backdata['product_name'];
			/*取得成本价cost2,统一转换成美元*/
			$datacost = $this->S->dao('product_cost')->D->get_one_by_field(array('pid'=>$val['child_pid']),'cost2,coin_code');
			if($datacost['coin_code'] != 'USD'){
				$datacost['cost2'] = $this->C->service('exchange_rate')->change_rate($datacost['coin_code'],'USD',$datacost['cost2']);
			}
			$val['cost'] = number_format($datacost['cost2'],2);
			$val['price'] = $val['cost'] * $val['quantity'];
			$kc = $this->S->dao('process')->get_allw_allsku(' and temp.sku="'.$val['sku'].'" and temp.wid='.$wid);
			$val['stocks']=$kc['0']['sums'];
			if (!$val['stocks']) {
				$val['stocks'] = 0;
			}
			$val['total'] = 1;
			$val['totalprice'] = $val['price'];
			$val['status'] = '-';
			$val['oper'] = '-';
		}


		$displayarr['sku']  			= array('showname'=>'sku', 'width'=>'160');
		$displayarr['product_name'] 	= array('showname'=>'产品名称', 'width'=>'300');
		$displayarr['quantity']  		= array('showname'=>'单组装数', 'width'=>'100');
		$displayarr['cost']  			= array('showname'=>'单价', 'width'=>'100');
		$displayarr['price']  			= array('showname'=>'组装单价', 'width'=>'100');
		$displayarr['stocks']			= array('showname'=>'库存', 'width'=>'100');
		$displayarr['total']  			= array('showname'=>'组装总数', 'width'=>'100');
		$displayarr['totalprice']  		= array('showname'=>'组装总价', 'width'=>'100');
		$displayarr['comment']			= array('showname'=>'备注', 'width'=>'200');
		$displayarr['status']			= array('showname'=>'状态', 'width'=>'100');
		$displayarr['oper']				= array('showname'=>'操作', 'width'=>'80');

	}
	//如果未传入组装方案，生成一行，能输入SKU,单件组装所需产品数量
	else {
		$displayarr['sku']  			= array('showname'=>'sku', 'width'=>'160', 'classname'=>'edittd', 'extend' => 'other="sku"');
		$displayarr['product_name'] 	= array('showname'=>'产品名称', 'width'=>'300');
		$displayarr['quantity']  		= array('showname'=>'单组装数', 'width'=>'100', 'classname'=>'edittd', 'extend' => 'other="quantity"', 'inputextend' => 'num');
		$displayarr['cost']  			= array('showname'=>'单价', 'width'=>'100');
		$displayarr['price']  			= array('showname'=>'组装单价', 'width'=>'100');
		$displayarr['stocks']			= array('showname'=>'库存', 'width'=>'100');
		$displayarr['total']  			= array('showname'=>'组装总数', 'width'=>'100');
		$displayarr['totalprice']  		= array('showname'=>'组装总价', 'width'=>'100');
		$displayarr['comment']			= array('showname'=>'备注', 'width'=>'200');
		$displayarr['status']			= array('showname'=>'状态', 'width'=>'100');
		$displayarr['oper']				= array('showname'=>'操作', 'width'=>'80', 'default'=>'1');
	}

	/*取得键名*/
	$displaykey = array_keys($displayarr);
	$this->V->mark(array('displayarr'=>$displayarr, 'datalist'=>$datalist, 'displaykey'=>$displaykey, 'prefix'=>$prefix_split, 'suffix'=>$suffix_split));
	$this->V->set_tpl('adminweb/ajax_table');
	display();
}
/* create by wall
 * 组装表模块
 * */
elseif ($detail == 'assemble_table') {
	//如果传入了组装方案，获取列表，所有列不可操作
	if ($assembleid) {
		$datalist = $this->S->dao('sku_assembly')->get_pid_by_assembleid($assembleid);
		foreach($datalist as &$val) {
			$backdata = $this->S->dao('product')->D->get_one_by_field(array('pid'=>$val['pid']),'sku,product_name,upc_or_ean');
			$val['sku'] = $backdata['sku'];
			$val['product_name'] = $backdata['product_name'];
			/*取得成本价cost2,统一转换成美元*/
			$datacost = $this->S->dao('product_cost')->D->get_one_by_field(array('pid'=>$val['pid']),'cost2,coin_code');
			if($datacost['coin_code'] != 'USD'){
				$datacost['cost2'] = $this->C->service('exchange_rate')->change_rate($datacost['coin_code'],'USD',$datacost['cost2']);
			}
			$val['cost'] = number_format($datacost['cost2'],2);
			$val['price'] = $val['cost'] * $val['quantity'];
			$kc = $this->S->dao('process')->get_allw_allsku(' and temp.sku="'.$val['sku'].'" and temp.wid='.$wid);
			$val['stocks']=$kc['0']['sums'];
			if (!$val['stocks']) {
				$val['stocks'] = 0;
			}
			$val['total'] = 1;
			$val['totalprice'] = $val['price'];
			$val['status'] = '-';
		}

		//echo '<pre>'.print_r($datalist,1).'</pre>';

		$displayarr['sku']  			= array('showname'=>'sku', 'width'=>'160');
		$displayarr['product_name'] 	= array('showname'=>'产品名称', 'width'=>'300');
		$displayarr['cost']  			= array('showname'=>'单价', 'width'=>'100');
		$displayarr['stocks']			= array('showname'=>'库存', 'width'=>'100');
		$displayarr['total']  			= array('showname'=>'组装总数', 'width'=>'100', 'inputextend' => 'num', 'classname'=>'edittd', 'extend' => 'other="total"');
		$displayarr['totalprice']  		= array('showname'=>'组装总价', 'width'=>'100');
		$displayarr['comment']			= array('showname'=>'备注', 'width'=>'200', 'classname'=>'edittd', 'extend' => 'other="comment"');
		$displayarr['status']			= array('showname'=>'状态', 'width'=>'100');

	}
	else {
		$displayarr['sku']  			= array('showname'=>'sku', 'width'=>'160', 'classname'=>'edittd', 'extend' => 'other="sku"');
		$displayarr['product_name'] 	= array('showname'=>'产品名称', 'width'=>'300');
		$displayarr['cost']  			= array('showname'=>'单价', 'width'=>'100', 'inputextend' => 'num');
		$displayarr['stocks']			= array('showname'=>'库存', 'width'=>'100');
		$displayarr['total']  			= array('showname'=>'组装总数', 'width'=>'100', 'inputextend' => 'num', 'classname'=>'edittd', 'extend' => 'other="total"');
		$displayarr['totalprice']  		= array('showname'=>'组装总价', 'width'=>'100');
		$displayarr['comment']			= array('showname'=>'备注', 'width'=>'200', 'classname'=>'edittd', 'extend' => 'other="comment"');
		$displayarr['status']			= array('showname'=>'状态', 'width'=>'100');
	}

	/*取得键名*/
	$displaykey = array_keys($displayarr);
	$this->V->mark(array('displayarr'=>$displayarr, 'datalist'=>$datalist, 'displaykey'=>$displaykey, 'prefix'=>$prefix_assemble, 'suffix'=>$suffix_assemble));
	$this->V->set_tpl('adminweb/ajax_table');
	display();
}
//拆分表sku校验
elseif ($detail == 'checksplitsku') {
	if(empty($sku)){exit($backdata['pid']);}
	$backdata = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name,upc_or_ean');
	if($backdata){

		/*取得成本价cost2,统一转换成美元*/
		$datacost = $this->S->dao('product_cost')->D->get_one_by_field(array('pid'=>$backdata['pid']),'cost2,coin_code');
		if($datacost['coin_code'] != 'USD'){
			$datacost['cost2'] = $this->C->service('exchange_rate')->change_rate($datacost['coin_code'],'USD',$datacost['cost2']);
		}
		$backdata['cost2']=number_format($datacost['cost2'],2);
		$kc = $this->S->dao('process')->get_allw_allsku(' and temp.sku="'.$sku.'" and temp.wid='.$wid);
		$backdata['stocks']=$kc['0']['sums'];
	}
	$data = json_encode($backdata);
	echo  $data;
}
/* create by all
 * 组装表sku校验
 * return:
 * $data 	已经存在组装产品单价，名称，库存
 * -1	 	产品已存在，不能作为组装sku
 * 0		产品不存在，可以作为组装sku*/
elseif ($detail == 'checkassemblesku') {
	if(empty($sku)){exit('-1');}
	$backdata = $this->S->dao('product')->D->get_one_by_field(array('sku'=>$sku),'pid,product_name,upc_or_ean');
	if($backdata){
		$sid = $this->S->dao('sku_assembly')->get_all_type_by_pid($backdata['pid']);
		//产品已存在，但不是组装产品，不能作为组装sku
		//可组装，给提示
		/*
		if (!$sid) {
			exit('-1');
		}
		*/
		//组装产品存在，返回产品信息
		/*取得成本价cost2,统一转换成美元*/
		$datacost = $this->S->dao('product_cost')->D->get_one_by_field(array('pid'=>$backdata['pid']),'cost2,coin_code');
		if($datacost['coin_code'] != 'USD'){
			$datacost['cost2'] = $this->C->service('exchange_rate')->change_rate($datacost['coin_code'],'USD',$datacost['cost2']);
		}
		$backdata['cost2']=number_format($datacost['cost2'],2);
		$kc = $this->S->dao('process')->get_allw_allsku(' and temp.sku="'.$sku.'" and temp.wid='.$wid);
		$backdata['stocks']=$kc['0']['sums'];
	}
	//产品不存在，可以作为组装sku
	else {
		exit('0');
	}
	$data = json_encode($backdata);
	echo  $data;
}

/* create by all
 * 获取组装方案
 * return:
 * 组装方案下来列表
 * */
elseif ($detail == 'get_type') {
	$typenum = array();
	$returnstr = '';
	if ($ispid) {
		for ($i = 0; $i < count($sku); $i++) {
			$temp = $this->S->dao('product')->get_product_by_sku($sku[$i]);
			$pid = $temp['pid'];
			$type = $this->S->dao('sku_assembly')->select_assembleid_by_pid($pid);
			if (!$type) {
				exit('0');
			}

			foreach ($type as $val) {
				$typenum[$val['assembleid']] ++;
			}
		}
		foreach ($typenum as $key=>&$val) {
			$returnstr .= '<option value="'.$key.'">'.$key.'</option>';
		}
	}
	else {
		for ($i = 0; $i < count($sku); $i++) {
			$temp = $this->S->dao('product')->get_product_by_sku($sku[$i]);
			$child_pid = $temp['pid'];
			$type = $this->S->dao('sku_assembly')->select_assembleid_by_childpid($child_pid);
			if (!$type) {
				exit('0');
			}
			foreach ($type as $val) {
				$typenum[$val['assembleid']] ++;
			}
		}
		foreach ($typenum as $key=>&$val) {
			//判断该方案产品数量是否等于传入产品数量，小于说明非本方案
			if (count($sku) == $val) {
				$returnstr .= '<option value="'.$key.'">'.$key.'</option>';
			}
		}
	}

	echo $returnstr;
	//echo '<pre>'.print_r($typenum,1).'</pre>';
}


/* create by all
 * 组装提交校验
 * return:
 * -1   新定义组装方案（组装方案，产品都不存在）
 * 0 	新定义组装方案（组装方案不存在，产品存在）
 * 正数	已经存在的组装方案
 * */
elseif ($detail == 'assemble_type') {
	$pro = $this->S->dao('product')->get_product_by_sku($sku);
	if (!$pro) {
		exit('-1');
	}
	$pid = $pro['pid'];
	//方案子产品出现种类
	$typenum = array();

	array_splice($child_sku,0,1);
	array_splice($quantity,0,1);

	//echo $sku;
	//echo '<pre>'.print_r($child_sku,1).'</pre>';
	//echo '<pre>'.print_r($quantity,1).'</pre>';

	for ($i = 0; $i < count($quantity); $i++) {
		$temp = $this->S->dao('product')->get_product_by_sku($child_sku[$i]);
		$child_pid = $temp['pid'];
		//echo '\n'.$pid.'-'.$child_pid.'-'.$quantity[$i];
		$type = $this->S->dao('sku_assembly')->get_type_by_one($pid, $child_pid, $quantity[$i]);
		//echo '<pre>'.print_r($type,1).'</pre>';
		if (!$type) {
			exit('0');
		}
		foreach ($type as $val) {
			$typenum[$val['type']] ++;
		}
	}

	foreach ($typenum as $key=>&$val) {
		//判断该方案产品数量是否等于传入产品数量，小于说明非本方案
		if (count($quantity) > $val) {
			$val = 0;
		}

		$temp = $this->S->dao('sku_assembly')->get_count_type_by_one($pid, $key);
		//echo '\n'.$key.'-'.$val;
		//判断该方案产品数量是否小于实际所需产品数量，小于说明非本方案
		if ($temp['sums'] > $val) {
			$val = 0;
		}
		//echo '<pre>'.print_r($temp,1).'</pre>';
	}
	arsort($typenum);
	$result = array_keys($typenum);
	if ($typenum[$result[0]] == 0) {
		exit('0');
	}
	echo $result[0];
}
/* create by all
 * 拆分提交校验
 * return:
 * 0 	组装方案不存在
 * ^0	已经存在的组装方案
 * */
elseif ($detail == 'split_type') {
	$pro = $this->S->dao('product')->get_product_by_sku($sku);
	if (!$pro) {
		exit('0');
	}
	$pid = $pro['pid'];
	//方案子产品出现种类
	$typenum = array();

	array_splice($child_sku,0,1);
	array_splice($quantity,0,1);

	//echo $sku;
	//echo '<pre>'.print_r($child_sku,1).'</pre>';
	//echo '<pre>'.print_r($quantity,1).'</pre>';

	for ($i = 0; $i < count($quantity); $i++) {
		$temp = $this->S->dao('product')->get_product_by_sku($child_sku[$i]);
		$child_pid = $temp['pid'];
		//echo '\n'.$pid.'-'.$child_pid.'-'.$quantity[$i];
		$type = $this->S->dao('sku_assembly')->get_type_by_one($pid, $child_pid, $quantity[$i]);
		//echo '<pre>'.print_r($type,1).'</pre>';
		if (!$type) {
			exit('0');
		}
		foreach ($type as $val) {
			$typenum[$val['type']] ++;
		}
	}

	foreach ($typenum as $key=>&$val) {
		//判断该方案产品数量是否等于传入产品数量，小于说明非本方案
		if (count($quantity) > $val) {
			$val = 0;
		}

		$temp = $this->S->dao('sku_assembly')->get_count_type_by_one($pid, $key);
		//echo '\n'.$key.'-'.$val;
		//判断该方案产品数量是否小于实际所需产品数量，小于说明非本方案
		if ($temp['sums'] > $val) {
			$val = 0;
		}
		//echo '<pre>'.print_r($temp,1).'</pre>';
	}
	arsort($typenum);
	$result = array_keys($typenum);
	if ($typenum[$result[0]] == 0) {
		exit('0');
	}
	echo $result[0];
}
/*
 * create by wall
 * 根据出入的assembletype判定将要进行的保存步骤
 * assembletype：
 * -1： 		新增产品，新增组装方案，产品出入库
 * 0：   	新增组装方案，产品出入库
 * 其他：	产品出入库
 * 组装产品
 * */
elseif ($detail == 'modassemble') {
	$product 		= $this->S->dao('product');
	$sku_assembly 	= $this->S->dao('sku_assembly');
	//$process 		= $this->S->dao('process');
	//事务是否提交
	$iscommit = 1;
	$product->D->query('begin'); /*采用事务*/
	$type = 1;
	//新增产品
	if ($assembletype == '-1') {
		$weight = 0.15;
		for ($i = 0; $i < count($sku); $i++) {
			$temp = $product->get_product_by_sku($sku[$i]);
			//echo '<pre>'.print_r($temp,1).'</pre>';
			$weight += (floatval($temp['shipping_weight'])-0.15) * intval($quantity[$i]);
			//echo $temp['shipping_weight'].'-0.15*'.$total[$i];
		}
		$rs1 = 	$product->D->insert(array('sku'=>$as_sku,'product_name'=>$as_product_name,'shipping_weight'=>$weight,'create_user'=>$_SESSION['eng_name'],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time())));


		if (!$rs1) {
			$iscommit = 0;
		}
		else {
			$pid = $rs1;
		}
	}
	//获取组合产品id
	if (!isset($pid)) {
		$temp = $product->get_product_by_sku($as_sku);
		$pid = $temp['pid'];
	}
	//新增组装方案，
	if ($assembletype == '-1' || $assembletype == '0') {
		$rs = $sku_assembly->get_max_type_by_pid($pid);
		if ($rs) {
			$type = $rs['type'] + 1;
		}
		for ($i = 0; $i < count($sku); $i++) {
			$result = $product->get_product_by_sku($sku[$i]);
			$rid = $sku_assembly->D->insert(array(
											'assembleid' => $assemble_prefix.'_'.$pid.'_'.$type,
											'pid' => $pid,
											'child_pid' => $result['pid'],
											'quantity' => $quantity[$i],
											'type' => $type,
											'cuser' => $_SESSION['eng_name']));
			if (!$rid) {
				$iscommit = 0;
			}
		}
	}


    /*start----------应财务要求，操作组装产品，系统库存不发生改变*/
	//第三方单号fid
    
	//$temp = $sku_assembly->get_assembleid_by_pid($pid, $type);
	//$fid = $temp['assembleid'].'_'.date('YmdHis',time());

	//总数为0时，不生成订单表，相当于生成组装方案
	//if ($as_total) {
		/*取最新期号*/
		//$backrate = $this->S->dao('exchange_rate')->D->get_one_by_field(array('code'=>'USD','isnew'=>1),'stage_rate');

		//生成出仓单
		/*生成出仓单号,取得其它出仓最大单号，并取出数字，t+7位数字，不够补0*/
		/*$max = $this->C->service('warehouse')->get_maxorder_manay('出仓单','t',$process);
		$order_id = 't'.sprintf("%07d",substr($max,1));
		for ($i = 0; $i < count($sku); $i++) {
			$temp = $product->get_product_by_sku($sku[$i]);
			$sid = $process->D->insert(array('provider_id'=>$warehouseid,'sku'=>$sku[$i],'pid'=>$temp['pid'],'product_name'=>$product_name[$i],'price'=>$totalprice[$i],'quantity'=>$total[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'出仓单','protype'=>'其它','output'=>'1','comment'=>$comment[$i],'coin_code'=>'USD','stage_rate'=>$backrate['stage_rate'],'price2'=>$cost[$i],'fid'=>$fid));
			if(!$sid) {
				$iscommit = 0;
			}
		}
		$max = $this->C->service('warehouse')->get_maxorder_manay('进仓单','e',$process);
		$order_id = 'e'.sprintf("%07d",substr($max,1));
		//生成进仓单
		$sid = $process->D->insert(array('receiver_id'=>$warehouseid,'sku'=>$as_sku,'pid'=>$pid,'product_name'=>$as_product_name,'price'=>$as_totalprice,'quantity'=>$as_total,'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'进仓单','protype'=>'其它','input'=>'1','comment'=>$as_comment,'coin_code'=>'USD','stage_rate'=>$backrate['stage_rate'],'price2'=>$as_cost,'fid'=>$fid));
		if (!$sid) {
			$iscommit = 0;
		}
	}*/
     /*end----------应财务要求，操作组装产品，系统库存不发生改变*/
	//修改成本
	//$jbackdata =  $this->S->dao('process')->D->get_one_by_field(array('property'=>'进仓单','protype'=>'其它','sku'=>$as_sku),'sum(price) as totalprice,sum(quantity) as total');
	//if ($jbackdata) {
	//	$as_total = $jbackdata['total'];
	//	$as_totalprice = $jbackdata['totalprice'];
	//}

	$costp = $this->C->service('exchange_rate')->change_rate('USD','CNY',$as_cost);
	$jid = $this->S->dao('product_cost')->updatecost($pid,$costp/1.05,$costp,date('Y-m-d H:i:s',time()));
	if (!$jid) {
		$iscommit = 0;
	}
	//事务回滚
	if ($iscommit == 0) {
		$product->D->query('rollback');
		$this->C->success('组装失败','index.php?action=sku_assembly&detail=list');
	}
	//事务提交
	else {
		$product->D->query('commit');
		$this->C->success('组装成功','index.php?action=sku_assembly&detail=list');
	}
}
/*
 * create by wall
 * 拆分产品
 * */
elseif ($detail == 'modsplit') {
	$product = $this->S->dao('product');
	$sku_assembly = $this->S->dao('sku_assembly');
	$process = $this->S->dao('process');
	//事务是否提交
	$iscommit = 1;
	$process->D->query('begin'); /*采用事务*/

	//获取组合产品id
	$temp = $product->get_product_by_sku($as_sku);
	$pid = $temp['pid'];

	//第三方单号fid
	$temp = $sku_assembly->get_assembleid_by_pid($pid, $assembletype);
	$fid = $temp['assembleid'].'_'.date('YmdHis',time());

	//总数为0时，不生成订单表，相当于生成组装方案
	if ($as_total) {
		/*取最新期号*/
		$backrate = $this->S->dao('exchange_rate')->D->get_one_by_field(array('code'=>'USD','isnew'=>1),'stage_rate');

		$max = $this->C->service('warehouse')->get_maxorder_manay('出仓单','t',$process);
		$order_id = 't'.sprintf("%07d",substr($max,1));
		//生成出仓单
		$sid = $process->D->insert(array('provider_id'=>$warehouseid,'sku'=>$as_sku,'pid'=>$pid,'product_name'=>$as_product_name,'price'=>$as_totalprice,'quantity'=>$as_total,'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'出仓单','protype'=>'其它','output'=>'1','comment'=>$as_comment,'coin_code'=>'USD','stage_rate'=>$backrate['stage_rate'],'price2'=>$as_cost,'fid'=>$fid));
		if (!$sid) {
			$iscommit = 0;
		}

		//生成进仓单
		/*生成进仓单号,取得其它进仓最大单号，并取出数字，e+7位数字，不够补0*/
		$max = $this->C->service('warehouse')->get_maxorder_manay('进仓单','e',$process);
		$order_id = 'e'.sprintf("%07d",substr($max,1));
		for ($i = 0; $i < count($sku); $i++) {
			$temp = $product->get_product_by_sku($sku[$i]);
			$sid = $process->D->insert(array('receiver_id'=>$warehouseid,'sku'=>$sku[$i],'pid'=>$temp['pid'],'product_name'=>$product_name[$i],'price'=>$totalprice[$i],'quantity'=>$total[$i],'cdate'=>date('Y-m-d H:i:s',time()),'mdate'=>date('Y-m-d H:i:s',time()),'rdate'=>date('Y-m-d H:i:s',time()),'order_id'=>$order_id,'cuser'=>$_SESSION['eng_name'],'muser'=>$_SESSION['eng_name'],'ruser'=>$_SESSION['eng_name'],'active'=>'1','property'=>'进仓单','protype'=>'其它','input'=>'1','comment'=>$comment[$i],'coin_code'=>'USD','stage_rate'=>$backrate['stage_rate'],'price2'=>$cost[$i],'fid'=>$fid));
			if(!$sid) {
				$iscommit = 0;
			}
		}

	}
	//事务回滚
	if ($iscommit == 0) {
		$process->D->query('rollback');
		$this->C->success('拆分失败','index.php?action=sku_assembly&detail=list');
	}
	//事务提交
	else {
		$process->D->query('commit');
		$this->C->success('拆分成功','index.php?action=sku_assembly&detail=list');
	}
}
/**
 * @title 判断是否是组合sku，是的话，就不可以进行再次组装
 * @author Jerry
 * @create on 2013-09-03
 */ 
elseif ($detail == 'assemble_type_sku') {
    //echo $sku;
    /*if (!$sku){
        exit('-1');
    }*/
    
    $pro = $this->S->dao('product')->get_product_by_sku($sku);
	if (!$pro) {
		exit('no');
	}
	$pid = $pro['pid'];
    
    $sku_assembly = $this->S->dao('sku_assembly');
    $ret = $sku_assembly->D->get_count(array('pid'=>$pid));
    if ($ret > 1){
        echo 'yes';
    }else{
        echo 'no';
    }
}
/*
 * 导出拆装
 * @author by Jerry
 * @certae on 2012-11-12
 */

 elseif ($detail == 'sku_output') {
	//处理查询语句
	$sqlstr = str_replace('assemble_sku', 'sa.pid=p.pid and p.sku', $sqlstr);
	$sqlstr = str_replace('split_sku', 'sa.child_pid=cp.pid and cp.sku', $sqlstr);
	$sqlstr = str_replace('cuser', 'sa.cuser', $sqlstr);

	$sku_assembly = $this->S->dao('sku_assembly');
    $process      = $this->S->dao('process');
    $product      = $this->S->dao('product');
    $product_cost = $this->S->dao('product_cost');

	$listrs = $sku_assembly->get_list_in_assembleid($sqlstr);
	//单页显示的第i行
	$i = 0;
    $k = 0;
	//取消分页
	unset($InitPHP_conf['pageval']);
	foreach ($listrs as $val) {
		$datalist[$i] = $val;
		$rs = $sku_assembly->select_by_assembleid($val['assembleid']);
		$datalist[$i]['cuser'] = $val['cuser'];
		$rs1 = $product->get_product_by_id($rs['0']['pid']);
		$datalist[$i]['assemblesku'] = $rs1['sku'];
		$datalist[$i]['assemblename'] = $rs1['product_name'];
		foreach ($rs as $k=>$val1) {
			$rs1 = $product->get_product_by_id($val1['child_pid']);
            /*获得cost2*/
        	$cost = $product_cost->D->select('cost2','pid='.$rs1['pid']);
            $_sumcost2[$i]['sum'] = $cost['cost2']*$val1['quantity'];
            $_sum += $_sumcost2[$i]['sum'];
			$datalist[$i]['splitsku'] = $rs1['sku'].'('.$val1['quantity'].'件)';
			$datalist[$i]['splitname'] = $rs1['product_name'];
			$i++;
            $k++;
		}
        $datalist[$i-$k]['sum'] = $_sum;
	}

    $filename = '拆装_'.date('Y-m-d');
    $head_array = array(
        'assembleid'    =>'组装编号',
        'assemblesku'   =>'sku',
        'assemblename'  =>'组装产品名称',
        'sum'           =>'总成本价',
        'splitsku'      =>'assemblename',
        'splitname'     =>'拆分产品sku',
        'cuser'         =>'方案创建者'
    );

    $this->C->service('upload_excel')->download_excel($filename, $head_array, $datalist);
 }
?>
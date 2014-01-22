<?php
/*
 * Created on 2012-03-15
 *
 * by wall
 * @title 个人中心产品信息定制模块
 */

//订单状态数组
$status_array = array(
	'备货单' 	=> array('-未审核', '-已审核', '-未通过', '-已接收'),
	'采购单' 	=> array('-未审核','-已审核','','-已下单'),
	'出仓单'		=> array('-预出库','-已出库','-待发货','-已出库'),
	'进仓单' 	=> array('','','','-退货'),
	'转仓单' 	=> array('-预出库','-已接收','','-已出库')
);

//个人中心单个模块
if ($detail == 'product_custom') {
	//当前登录用户ID
	$sqlstr = ' and uid='.$_SESSION['uid'].' ';
	//获取所有定制产品ID（有产品操作的信息）
	$InitPHP_conf['pageval'] = 9;
	$datalist = $this->S->dao('product_custom')->get_pid_by_uid($_SESSION['uid']);
	
	//根据所有定制产品ID,在订单表（process）中
	//获取SKU,修改时间mdate,订单类型property,数量quantity
	//按时间倒序，获取第一条
	foreach($datalist as &$val) {
		$result = $this->S->dao('process')->get_one_by_pid_desc_mdate($val['pid']);		
		//通过点击SKU进入单个定制产品的订单列表
		$val['sku'] = '<a class="aajax" href="javascript:void(0)" url="index.php?action=center_productcustom&detail=list_by_pid&pid='.$val['pid'].'">'.$result['sku'].'</a>';
		$val['mdate'] = date('Y-m-d', strtotime($result['mdate']));
		$val['quantity'] = $result['quantity'].'件';		
		$val['show'] = substr($result['property'],0, 6).$status_array[$result['property']][$result['statu']];;


	}
	$datalist = $this->C->array_sort($datalist, 'mdate', 1);


	$displaykey = array('sku','mdate','show','quantity');



	/*模板显示*/
	$button_str = array(
		'url'=>'index.php?action=center_productcustom&detail=add_by_sku_list'
		, 'value'=>'添加+'
		, 'classname' => 'wall_include');
	$flush_url = 'index.php?action=center_productcustom&detail=product_custom';
	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'button_str'=>$button_str, 'flush_url'=>$flush_url));

	$this->V->set_tpl('adminweb/single_model');

	display();
}
elseif ($detail == 'list_by_pid') {
	$process = $this->S->dao('process');
	$InitPHP_conf['pageval'] = 9;
	//需要显示的定制产品的ID，获取产品订单列表
	$sqlstr = ' and isover="N" and pid='.$pid;
	//获取制定ID的所有订单。
	if ($isday) {
		$datalist = $process->get_list_groub_by_day($sqlstr);
	} 
	else{
		$datalist = $process->D->get_list($sqlstr, '', 'mdate desc', 'order_id,mdate,quantity,property,statu');
	}

	foreach($datalist as &$val) {
		$val['mdate'] = $isday?date('Y-m-d', strtotime($val['mdate'])):date('m-d h:i', strtotime($val['mdate']));
		$val['quantity'] = $val['quantity'].'件';
		//显示订单状态
		$val['show'] = substr($val['property'],0, 6).$status_array[$val['property']][$val['statu']];;
	}
	$num = count($datalist);
	$datalist[$num]['order_id'] = '按天统计';
	if ($isday) {
		$datalist[$num]['quantity'] = '<input type="checkbox" class="wall_check_day" url="index.php?action=center_productcustom&detail=list_by_pid&pid='.$pid.'" checked="true" />';
		$displaykey = array('mdate','show','quantity');
	}
	else {
		$datalist[$num]['quantity'] = '<input type="checkbox" class="wall_check_day" url="index.php?action=center_productcustom&detail=list_by_pid&pid='.$pid.'" />';
		$displaykey = array('order_id','mdate','show','quantity');
	}
	//显示列
	

	//分页所需定制产品的ID
	$pageshow = array('pid'=>$pid);

	/*模板显示*/
	//按钮
	$button_str = array(
		'url'=>'index.php?action=center_productcustom&detail=product_custom'
		, 'value'=>'返回'
		, 'classname' => 'wall_return');

	$flush_url = 'index.php?action=center_productcustom&detail=list_by_pid&pid='.$pid;
	if ($isday) {
		$flush_url .= '&isday=1';
	}
	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'button_str'=>$button_str, 'flush_url'=>$flush_url));
	
	if ($isday) {
		$pageshow['isday'] = '1';
	}
	$this->V->set_tpl('adminweb/single_model');

	display();
}
elseif ($detail == 'add_by_sku_list') {
	$InitPHP_conf['pageval'] = 9;
	//当前登录用户ID
	$sqlstr = ' and uid='.$_SESSION['uid'].' ';
	if ($sku) {
		$sqlstr .= ' and sku like "%'.$sku.'%" ';
	}
	//获取所有定制产品ID
	$datalist = $this->S->dao('product_custom')->get_product_by_join($sqlstr);//->D->get_list($sqlstr,'','id desc','id,pid');

	//获取已定制产品SKU,产品名称
	foreach($datalist as &$val) {
		$result = $this->S->dao('product')->get_product_by_id($val['pid']);

		$val['sku'] = $result['sku'];
		$val['product_name'] = $result['product_name'];
		//移除已定制的产品
		$val['oper'] = '<a class="aajax" href="javascript:void(0);" url="index.php?action=center_productcustom&detail=delete_by_pid&id='.$val['id'].'" title="移除定制"><img src="./staticment/images/deletebody.gif" border="0"></a>';

	}

	$displaykey = array('sku','product_name','oper');

	/* create by wall
	 * 自定义input控件
	 * class = wall_submit 为提交
	 * 提交附加数据为type=text的
	 * 数组key为input属性名称
	 * 数组value为input属性值
	 * */
	$searchlist[] = array(
		'type'=>'text',
		'name'=>'sku',
		'value'=>$sku
		);
	$searchlist[] = array(
		'type'=>'button',
		'url'=>'index.php?action=center_productcustom&detail=add_by_sku_list',
		'value'=>'查询',
		'class' => 'wall_submit'
		);


	/* create by wall
	 * 自定义input控件
	 * class = wall_submit 为提交
	 * 提交附加数据为type=text的
	 * 数组key为input属性名称
	 * 数组value为input属性值
	 * */
	$inputlist[] = array(
		'type'=>'text'	,
		'name'=>'sku',
		'value'=>'',
		'class'=>'checkempty'
		);
	$inputlist[] = array(
		'type'=>'button',
		'url'=>'index.php?action=center_productcustom&detail=add_by_sku',
		'value'=>'添加+',
		'class' => 'wall_submit'
		);

	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'inputlist'=>$inputlist, 'searchlist'=>$searchlist));

	$this->V->set_tpl('adminweb/div_model');

	display();
}
elseif ($detail == 'add_by_sku') {
	$result = $this->S->dao('product')->D->select('pid','sku="'.$sku.'"');
	if ( !$result ) {
		echo 'Sku is error!<br><input type="button" class="wall_return" url="index.php?action=center_productcustom&detail=add_by_sku_list" value="返回" />';
		exit();
	}
	$sid = $this->S->dao('product_custom')->get_one_by_uid_pid($_SESSION['uid'], $result['pid']);
	if ($sid) {
		echo 'Sku is customed!<br><input type="button" class="wall_return" url="index.php?action=center_productcustom&detail=add_by_sku_list" value="返回" />';
		exit();
	}
	$iid = $this->S->dao('product_custom')->D->insert(array('uid' => $_SESSION['uid'], 'pid' => $result['pid'], 'cdate' => date("Y-m-d H:i:s")));;
	if ($iid) {
		header("Location: index.php?action=center_productcustom&detail=add_by_sku_list");
	}
}
elseif ($detail == 'delete_by_pid') {
	$did = $this->S->dao('product_custom')->D->delete_by_field(array('id'=>$id));
	if ($did) {
		header("Location: index.php?action=center_productcustom&detail=add_by_sku_list");
	}
}
/*
 * create on 2012-04-23
 * by wall
 * 查询定制产品
 * */
elseif ($detail == 'select_by_sku') {
	$process = $this->S->dao('process');
	if ($sku) {
		$result = $this->S->dao('product')->D->select('pid','sku="'.$sku.'"');
		if ( !$result ) {
			echo 'Sku is error!<br><input type="button" class="wall_return" url="index.php?action=center_productcustom&detail=select_by_sku" value="返回" />';
			exit();
		}
		$InitPHP_conf['pageval'] = 9;
		//需要显示的定制产品的ID，获取产品订单列表
		$sqlstr = ' and isover="N" and pid='.$result['pid'];
		//获取制定ID的所有订单。
		if ($isday) {
			$datalist = $process->get_list_groub_by_day($sqlstr);
		} 
		else{
			$datalist = $process->D->get_list($sqlstr, '', 'mdate desc', 'order_id,mdate,quantity,property,statu');
		}
	
		foreach($datalist as &$val) {
			$val['mdate'] = $isday?date('Y-m-d', strtotime($val['mdate'])):date('m-d h:i', strtotime($val['mdate']));
			$val['quantity'] = $val['quantity'].'件';
			//显示订单状态
			$val['show'] = substr($val['property'],0, 6).$status_array[$val['property']][$val['statu']];;
		}
		$num = count($datalist);
		$datalist[$num]['order_id'] = '按天统计';
		if ($isday) {
			$datalist[$num]['quantity'] = '<input type="checkbox" class="wall_check_day" url="index.php?action=center_productcustom&detail=select_by_sku&sku='.$sku.'" checked="true" />';
			$displaykey = array('mdate','show','quantity');
		}
		else {
			$datalist[$num]['quantity'] = '<input type="checkbox" class="wall_check_day" url="index.php?action=center_productcustom&detail=select_by_sku&sku='.$sku.'" />';
			$displaykey = array('order_id','mdate','show','quantity');
		}
		//显示列
		
	
		//分页所需定制产品的ID
		$pageshow = array('sku'=>$sku);			
		
		if ($isday) {
			$pageshow['isday'] = '1';
		}
	}
	/* create on 2012-04-23
	 * by wall
	 * 自定义input控件
	 * class = wall_submit 为提交
	 * 提交附加数据为type=text的
	 * 数组key为input属性名称
	 * 数组value为input属性值
	 * */
	$searchlist[] = array(
		'type'=>'text',
		'name'=>'sku',
		'value'=>$sku
		);
	$searchlist[] = array(
		'type'=>'button',
		'url'=>'index.php?action=center_productcustom&detail=select_by_sku',
		'value'=>'查询',
		'class' => 'wall_submit'
		);
		
	$this->V->mark(array('datalist'=>$datalist, 'displaykey'=>$displaykey, 'searchlist'=>$searchlist));

	$this->V->set_tpl('adminweb/div_model');

	display();
}
?>
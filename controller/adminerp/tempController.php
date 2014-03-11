<?php
/*替换产品名称，重量与成本*/
 if($detail == 'upcost'){
	//取上传的文件的数组
	$upload_dir		= "./data/uploadexl/temp/";//上传文件保存路径的目录
	$fieldarray		= array('A','B','C','D','E','F','G','H','I');//有效的excel列表值
	$head			= 1;//以第一行为表头
	$success		= 0;
	$error			= 0;
	$notfountd		= 0;
	$no_sku			= '';
	$product_cost	= $this->S->dao('product_cost');
	$product		= $this->S->dao('product');
	//$exchanges		= $this->C->service('exchange_rate');
    $product = $this->S->dao('product');

	$all_arr	=  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
	unset($all_arr[0]);
	$filepath	= $this->getLibrary('basefuns')->getsession('filepath');

	unlink($filepath);
    
	if($all_arr){
		foreach($all_arr as $val)
        {
            //更新市场指导价方法
            $sid = $product->D->get_one_by_field(array('sku'=>$val['sku']),'pid');
            if(!empty($sid)){
                $ret = $product_cost->D->update(array('pid'=>$sid['pid']),array(
                    'cost3'=>$val['cost3'],
                ));
                if($ret) {$success++;}else{$error++;}
            }
            
            //$sid = $product->D->get_one_by_field(array('sku'=>$val['sku']),'pid');
            //$product_dimensions = $val['product_dimensions_a']."x".$val['product_dimensions_b']."x".$val['product_dimensions_c'];
            //$box_product_dimensions = $val['box_product_dimensions_a']."x".$val['box_product_dimensions_b']."x".$val['box_product_dimensions_c'];
            /*
            if ($val['unit_box']){
                $ret = $product->D->update(array('sku'=>$val['sku']),array(
                    'product_dimensions'=>$product_dimensions,
                    'shipping_weight'=>$val['shipping_weight'],
                    'box_product_dimensions'=>$box_product_dimensions,
                    'unit_box'=>$val['unit_box']
                ));
            }else{
                $ret = $product->D->update(array('sku'=>$val['sku']),array(
                    'product_dimensions'=>$product_dimensions,
                    'shipping_weight'=>$val['shipping_weight'],
                ));
            }
            if($ret) {$success++;}else{$error++;}
            */    
        }
        /*
        if (!empty($val['zone'])){
            $data   = array('area_id'=>$val['area_id'],'code'=>'d'.$val['zone'],'shipping_id'=>$val['shipping_id']);
            $result = $shipping_encode->D->insert($data);
        }
        if ($result){$success++;}else{$error++;}
            
		$pid = $product->D->get_one(array('sku'=>$val['sku']),'pid');
		if($pid){
			$backcost= $product_cost->D->get_one(array('pid'=>$pid),'cost3,coin_code');
			if($backcost['cost3']){
				$backcost['cost3'] = $exchanges->change_rate('CNY',$backcost['coin_code'],$val['cost']);
		    }

			$sid = $product_cost->D->update(array('pid'=>$pid),array('cost3'=>$backcost['cost3']));
			if($sid) {$success++;}else{$error++;}

		}else{
			$no_sku.= $val['sku'].'<br>';
			$notfountd++;
		}
        */
    }

	echo '成功更新 '.$success.'个，失败 '.$error.'个';

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->mark(array('title'=>'成本替换'));
	$this->V->set_tpl('adminweb/commom_excel_import');
	display();
 }

 elseif($detail == 'php'){
 	echo phpinfo();
 }

 /*标签应用*/
 elseif($detail == 'tab'){


	//$this->V->mark(array('title'=>'标签测试'));
 	//$this->V->set_tpl('admintag/tag_header','F');
	//$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/tab');
	display();

 }
 elseif ($detail == 'login'){
     return $this->C->service('temp')->login('123','456');
 }

 elseif($detail == 'rate'){

	$d =  $this->S->dao('product')->D->get_one(' and pid= 1867','sku,product_name');
	//echo $d;
	echo '<pre>'.print_r($d,1).'</pre>';

 }


 elseif($detail == 'cost'){

 	if(empty($_SESSION['alldddd'])){
 		$_SESSION['alldddd']	= 0;
 	}

	$InitPHP_conf['pageval']	= 10000;

	$orders_detail				= $this->S->dao('orders_detail');
 	$datalist					= $orders_detail->D->get_list(' and cost1=""','','','id,pid');
 	$productcost				= $this->S->dao('product_cost');
 	$exservice					= $this->C->service('exchange_rate');

	$count						= 0;

 	foreach($datalist as $val){

		if($val['pid']){

			/*取得转换为USD的产品成本*/
			$backcostArr		= $productcost->D->get_one(array('pid'=>$val['pid']));

			if($backcostArr['cost1']!='0.00'){

				$val['cost1']		= $exservice->change_rate($backcostArr['coin_code'],'USD',$backcostArr['cost1']);
				$sid				= $orders_detail->D->update(array('id'=>$val['id']),array('cost1'=>$val['cost1']));
				if($sid)  $count++;
			}
		}
 	}

 	$_SESSION['alldddd']+=$count;

 	echo '成功更新：'.$count.'；累积已更新：<b>'.$_SESSION['alldddd'].'</b>';

 }


 elseif($detail == 'getpid'){

 	$count = 0;

 	$orders_detail				= $this->S->dao('orders_detail');
 	$obj_product				= $this->S->dao('product');

 	$datalist					= $orders_detail->D->get_allstr(' and pid=""','','','id,erp_sku');

 	foreach($datalist as $val){//echo $val['id'].'<br>';
		$ppid = $obj_product->D->get_one(array('sku'=>trim($val['erp_sku'])),'pid');

		if($ppid){echo $ppid.'<br>';
			$sid = $orders_detail->D->update(array('id'=>$val['id']),array('pid'=>$ppid));
			if($sid) $count++;
		}

 	}

 	echo '更新 ：'.$count.'个SKU';

 }


 elseif($detail == 'getdata'){

	$xml = "<graph caption='Monthly Sales Summary' subcaption='For the year 2004' xAxisName='Month' yAxisMinValue='15000' yAxisName='Sales' decimalPrecision='0' formatNumberScale='0' numberPrefix='$' showNames='1' showValues='0'  showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5' ><set name='Jan' value='17400' hoverText='January'/><set name='Feb' value='19800' hoverText='February'/></graph>";
    echo $xml;
 }


/*近15次采购价与时间*/
elseif($detail == 'getallcost')
{
	$backdata = $this->S->dao('process')->D->get_list(' and property="采购单" and sku="91-8858-001" ','','','price,cdate,sku');
	$xmlcont  = "<graph caption='".$backdata[0]['sku']."' subcaption='Monthly Sales Summary For the near 12 month' xAxisName='Year-Month' yAxisName='Sales' decimalPrecision='0' formatNumberScale='0' numberPrefix='$' showNames='1' showValues='1'  showAlternateHGridColor='1' AlternateHGridColor='ff5904' divLineColor='ff5904' divLineAlpha='20' alternateHGridAlpha='5' >";


	for($i = count($backdata)-1; $i>=0; $i--){

	}

	$this->V->mark(array('title'=>'统计表','xmlfile'=>'test.xml','Type'=>'FCF_Line.swf'));
	$this->V->set_tpl('adminweb/report_chart');
	display();

}
 

elseif ($detail == 'getshipping_cost'){
    $shipping_fare  = $this->C->service('shipping_fare');
    $data = $shipping_fare->getshipping_cost('8','18600000000','34500000000','20','CNY','1');
    print_r($data);
}
 
/*条形码生成*/
elseif ($detail == 'barcode'){
    $codebar = 'BCGcode39';
    $temp = $this->C->service('temp');
    return $temp->barcode($codebar,$sku);
}
 
elseif ($detail == 'call'){
    $global = $this->C->service('global');
    $global->buytimenotice();
}
 
elseif ($detail == 'come'){
    $global = $this->C->service('global');
    $global->comeproductnotice();
}
 
elseif ($detail == 'temp'){
    //return 'saaaaaaaaa';
    echo 'ddd';
}
 
elseif ($detail == 'ajax_message') {
	$this->V->set_tpl('adminweb/ajax_message');
    display();
}

//由于国内事业部更改的客服的操作方式，原来是又人工填写订单中的单价
//考虑到人手操作的复杂度，所以改为在表格的商品列中只有一个订单的总金额，收入根据市场指导价均摊
//这个是用来更新导入错误的数据
elseif ($detail == 'update_price') {
    $process = $this->S->dao('process');
	$datalist = $process->D->get_all_sql("SELECT order_id,COUNT(id) AS qty,COUNT(price),MAX(price),MIN(price) FROM process WHERE cdate>='2014-02-24' AND market_price IS NOT NULL GROUP BY order_id HAVING qty>1 AND MAX(price)>0 AND MAX(price) = MIN(price) ORDER BY qty DESC");
    
    foreach($datalist as $val)
    {
        $order_id = $val['order_id'];
        
        $price_data = $process->D->get_one_by_field(array('order_id'=>$order_id),'price');
        $price = $price_data["price"];//订单总金额
        
        $total_market_price_data = $process->get_total_market_price(" where order_id='".$order_id."'");
        $total_market_price = $total_market_price_data["total_market_price"];//市场指导价总金额
        
        //计算每份市场价涉及的平均值
        $every = $price/$total_market_price;
        
        $order_data = $process->D->get_allstr(" and order_id='".$order_id."'",'','','id,quantity,market_price');
        foreach($order_data as $vals)
        {
            $item_price = $vals['market_price']*$vals['quantity']*$every;
            //将这个金额更新到Price字段中
            $process->D->update_sql(" where id='".$vals['id']."'",array('price'=>$item_price));
        }
    }
}
?>
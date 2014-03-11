<?php
/* create on 2012-04-26
 * by wall
 * 导出表格的显示字段数组
 * */
include_once('./api/FBAInventoryServiceMWS/function.php');
$exportarr = array(
    'sku'                   => 'sku',
    'product_name'          => '产品名称',
    'create_user'           => '产品录入者',
    'cost1'                 => '原始成本',
    'cost2'                 => '销售成本',
    'cost3'                 => '市场指导价',
    'shipping_weight'       => '产品重量(kg)',
    'box_shipping_weight'   => '产品重量(标准箱)(kg)',
    'product_dimensions'    => '长宽高(cm)',
    'box_product_dimensions'=> '长宽高(标准箱)(cm)',
    'nowquantity'           => '即时库存(蛇口仓)',
    //'shipping'              => '运费',
);

 if($this->C->service('admin_access')->checkResRight('r_t_suplimod')){
    $exportarr['name'] = '供应商';
 }

if ($eid){
    //$_eid = $this->S->dao('esse')->D->get_one_by_field(array('id'=>$eid),'name');
    //$exportarr['shipping'] = '运费('.$_eid['name'].')(CNY)';
}else{
    $exportarr['shipping'] = '运费';
}
    
$product 	= $this->S->dao('product');
$basefuns 	= $this->getLibrary('basefuns');


/*产品列表*/
if ($detail == 'list') {
	$stypemu = array(
        'sku-s-l' 		=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SKU：',
        'product_name-s-l'	=> '&nbsp;&nbsp;产品名称：',
        'cdate-t-t'		=> '&nbsp;&nbsp;添加时间：',
        'eid-a-e'	=>'目的仓库：',
        'cat_id-a-e'		=> '<br>&nbsp;&nbsp;类&nbsp;&nbsp;&nbsp;&nbsp;别：',
	    'create_user-s-e'	=> '&nbsp;&nbsp;录&nbsp;&nbsp;入&nbsp;&nbsp;者：',
        'label_id-a-e'		=> '&nbsp;&nbsp;标&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;签：',
        'assembleid-a-e'=>'sku&nbsp;&nbsp;类型：',
        
    );

    /*判断权限*/
    if($this->C->service('admin_access')->checkResRight('r_t_suplimod')){
        $stypemu['supplier-s'] 		= '<br>&nbsp;&nbsp;供应商：';
        $stypemu['supplier_id-h'] 	= '';
    }
    
    

    /*获得产品有效类别，生成类别下拉框*/
    $cat_idarr 	= array(''=>'=请选择=');
    $category 	= $this->S->dao('category');
    $catlist    = $category->category_treelist();
    foreach($catlist as $key=>$val){
        $cat_idarr[$val['cat_id']] = $val['parent_id']?'...'.$val['cat_name']:$val['cat_name'];
    }
    /*获得用户标签，生成类别下拉框*/
    $label_idarr= array(''=>'=请选择=');
    $label 	= $this->S->dao('user_label');
    $labellist 	= $label->getUserLabelList();

    foreach($labellist as $key=>$val){
        $label_idarr[$val['id']] = $val['label_name'];
    }
    
    $assembleidarr = array(''=>'=请选择=','组合'=>'组合','单个'=>'单个');
    
    /*取得仓库下拉-用于生成搜索条件*/
	$wdata = $this->S->dao('esse')->D->get_all(array('type'=>2),'id','desc','id,name');
	$eidarr = array(''=>'=请选择=');
	for($i=0;$i<count($wdata);$i++){
		$eidarr[$wdata[$i]['id']] = $wdata[$i]['name'];
	}
    

    /*产品可使用的模板*/
    $product_template	= $this->S->dao('product_template');
    $data = $product_template->D->get_allstr('','','','id,template_value,tempalte_name');

    $template_select 	= "<select name='template_id'>";
    foreach($data as $key=>$val){
        $template_select .= "<option value=".$val['template_value'].">".$val['tempalte_name']."</option>";
    }
    $template_select   .= "</select>";

    $sqlstr 					= str_replace('cdate','p.cdate',$sqlstr);
    $sqlstr 					= str_replace('cat_id','p.cat_id',$sqlstr);
    $sqlstr                                     .= empty($supplier_id)?'':" and manufacturer like '%@".$supplier_id.",%' ";
    $sqlstr 					= str_replace('sku like "%', 'sku like "', $sqlstr);

    if ($assembleid == "单个")
        $sqlstr = str_replace('assembleid="单个"',' assembleid is NULL',$sqlstr);
    if ($assembleid == "组合")
        $sqlstr  = str_replace('assembleid="组合"',' assembleid <> ""',$sqlstr);
        
   
    $process					= $this->S->dao('process');
   
    $exchange_rate				= $this->C->service('exchange_rate');

	/*读取个人标签*/
    $user_label 				= $this->S->dao('user_label');
    $label_data 				= $user_label->getUserLabelList();

    /*获得cost2*/
    $product_cost 				= $this->S->dao('product_cost');
    $back_set_list				= $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'product_list_cost'),'value');
    $back_set_coin				= empty($back_set_list['value'])?'CNY':$back_set_list['value'];

    $InitPHP_conf['pageval']	= 15;
    $datalist 					= $product->getProductList($sqlstr,' order by p.pid desc ');
    $pidstr					= '';

	/*查上次采购成本用于提示涨降价*/
	foreach($datalist as $val){
		$pidstr.= $val['pid'].',';
	}
	$bklacost 	= $product_cost->get_last_info_by_pid(' and pid in ('.rtrim($pidstr,',').')');
	$c			= 0;
	$img_up		= '<img src="./staticment/images/rose_up.png" />';
	$img_dn		= '<img src="./staticment/images/fall_down.png" />';


	/*数据处理*/
	for($i=0;$i<count($datalist);$i++){

        /*用户可使用的标签*/
        $label_select = "<select name='label_id'>";
        $label_select .= "<option value=''>请选择</option>";
        foreach($label_data as $key=>$val){
            if($datalist[$i]['label_id'] == $val['id']){
                $sel 	= 'selected="selected"';
            } else {
                $sel 	= '';
            }
            $label_select .= "<option value=".$val['id']." $sel>".$val['label_name']."</option>";
        }
        $label_select  .= "</select>";

		/*成本币别统一转换成配置的，用于列表的成本价*/
		$cost					= array('cost1'=>$datalist[$i]['cost1'],'cost2'=>$datalist[$i]['cost2'],'coin_code'=>$datalist[$i]['coin_code']);
		$count_cost				= ($cost && $cost['coin_code'] != $back_set_coin)? $exchange_rate->change_rate($cost['coin_code'],$back_set_coin,$cost['cost2']) : $cost['cost2'];
		$datalist[$i]['cost2'] 	= number_format($count_cost,2);

		/*真实成本转换原始成本CNY，用于下面比较上一次采购成本*/
		if($datalist[$i]['pid'] == $bklacost[$c]['pid']){
			$d_price = $datalist[$i]['coin_code'] 	== 'CNY' ? $datalist[$i]['cost1'] 	: $exchange_rate->change_rate($datalist[$i]['coin_code'],'CNY',$datalist[$i]['cost1']);
			$n_price = $bklacost[$c]['coin_code'] 	== 'CNY' ? $bklacost[$c]['price'] 	: $exchange_rate->change_rate_by_stage($bklacost[$c]['price'], $bklacost[$c]['coin_code'], 'CNY', $bklacost[$c]['stage_rate']);
			$datalist[$i]['cost2']					.= ($n_price > $d_price) ? $img_up  : ($n_price < $d_price ? $img_dn : '');
			$c++;
		}


        /*Amazon FBA BP的指导价*/
        $order_fee 		= 1.90;//每订单的费用
        $product_fee 	= 0.60;//每个产品的费用
        $kg_weight 		= $datalist[$i]['shipping_weight'];
        $lb_weight 		= $this->C->service('global')->kg_to_lb($kg_weight);
        if($lb_weight <= 15){
            $weight_fee = 0.45;
        } elseif ($lb_weight > 15 && $lb_weight <= 20){
            $weight_fee = 0.90;
        }
        $datalist[$i]['fba_bp']	= ($datalist[$i]['cost2'] != 0) ? $datalist[$i]['cost2'] + $order_fee + $product_fee + $weight_fee : 0;



        /*判断当前时间和产品添加时间的时间差，3天内的显示红色，7天内的显示橙色，超过7天的不黑色*/
        $cdate = strtotime($datalist[$i]['cdate']);
        $ndate = time();
        $ddata = $ndate-$cdate;
		$color = $ddata<=259200?"#F00":($ddata<= 604800?"#F60":"#000");
        
        $zuhecolor = $datalist[$i]['assembleid']?"#F00":"";

        $datalist[$i]['cdate'] = date('Y-m-d', strtotime($datalist[$i]['cdate']));

		/*图片显示--以pic,pic.jpg,pic.gif,pic.png首先显示*/
		$images_ary 				= json_decode($datalist[$i]['images'],true);
		foreach($images_ary as $val){
			if($val['desc']){
				$thisdesc = strtolower($val['desc']);
				if($thisdesc == 'pic' || $thisdesc == 'pic.jpg' || $thisdesc == 'pic.gif' || $thisdesc == 'pic.png'){
					$datalist[$i]['image_url'] = $val['url'];
				}
			}
		}

		$img_length					= count($images_ary)-1;
		if(!$datalist[$i]['image_url']) {
			$datalist[$i]['image_url'] = empty($images_ary[$img_length]['url'])?'./staticment/images/no_picture.gif':$images_ary[$img_length]['url'];
		}

        $datalist[$i]['showimages'] = '<a href="'.$datalist[$i]['image_url'].'" target="_blank" title="点击查看大图" class="tooltip"><img src="'.$datalist[$i]['image_url'].'" style="border:solid 1px #828482; width:50px;height:50px;"></a>&nbsp;';
        $datalist[$i]['stock']      = '<span id="'.$datalist[$i]['sku'].'"></span><a href="javascript:void(0);" name="btn_stock" id="btn_stock"><font color="red">点击查询</font></a>';
        $datalist[$i]['barcode']    = '<a target="_blank" href="index.php?action=barcode&detail=skubarcode&sku='.urlencode($datalist[$i]['sku']).'" title="点击查看">sku条码</a>';
        $datalist[$i]['sku']        = '<b><a href="index.php?action=product_list&detail=edit&isshow=1&pid='.$datalist[$i]['pid'].'" title="查看产品，红色为3天内新产品，橙色为7天内新产品"><font color="'.$color.'">'.$datalist[$i]['sku'].'</font></a>&nbsp;</b>';
        $datalist[$i]['template']   = $template_select.'<span id="'.$datalist[$i]['pid'].'"></span><input type="button" name="btn_template" id="btn_template" value="导出" />';
	    $datalist[$i]['label']      = $label_select.'<span id="'.$datalist[$i]['pid'].'"></span><input type="button" name="btn_label" id="btn_label" value="保存" />';;
        $datalist[$i]['product_name']  = '<font title="红色为组合SKU" color="'.$zuhecolor.'">'.$datalist[$i]['product_name'].'</font>';
        
        //运费显示
        $datalist[$i]['shippings']    = nl2br(str_replace(",","\n",$datalist[$i]['shippings']));
        

        /*产品状态（正常：normal，清库：emptying，停止销售-质量问题：quality，停止销售-低利润：profit，停止销售-侵权：tort）*/
        switch($datalist[$i]['conditionerp']){
        	case ''					:$datalist[$i]['conditionerp'] = '正常';break;
        	case 'normal'			:$datalist[$i]['conditionerp'] = '正常';break;
        	case 'emptying'			:$datalist[$i]['conditionerp'] = '清库';break;
        	case 'quality'			:$datalist[$i]['conditionerp'] = '停止销售-质量问题';break;
        	case 'profit'			:$datalist[$i]['conditionerp'] = '停止销售-低利润';break;
        	case 'tort'				:$datalist[$i]['conditionerp'] = '停止销售-侵权';break;
        }

    }

    $tablewidth = '1720';
    $displayarr['pid'] 			 		= array('showname'=>'checkbox','width'=>'45','title'=>'全选');
    $displayarr['both'] 	            = array('showname'=>'操作','width'=>'60','ajax'=>'1','url_e'=>'index.php?action=product_list&detail=edit&pid={pid}','url_d'=>'index.php?action=product_list&detail=delete&pid={pid}');
    $displayarr['sku'] 	                = array('showname'=>'产品SKU','width'=>'100');
    if (strtolower($create_user) == strtolower($_SESSION['eng_name'])) {
    	$displayarr['product_name'] 	= array('showname'=>'产品名称','width'=>'120','clickedit'=>'pid','detail'=>'edit_product_name');
    } else {
    	$displayarr['product_name'] 	= array('showname'=>'产品名称','width'=>'220');
    }
    $displayarr['barcode']              = array('showname'=>'sku条码','width'=>'60');
    $displayarr['showimages'] 	        = array('showname'=>'产品图片','width'=>'80');
    $displayarr['cat_name'] 	        = array('showname'=>'类别名称','width'=>'120');
    $displayarr['cost2'] 	            = array('showname'=>'成本价('.$back_set_coin.')','width'=>'120');

    if($back_set_coin == 'USD'){//米悠不显示
	    $displayarr['fba_bp'] 	            = array('showname'=>'FBA BP('.$back_set_coin.')','width'=>'120');
    }
    $displayarr['shippings']             = array('showname'=>'运费(CNY)','width'=>'120');
    $displayarr['label'] 	            = array('showname'=>'产品标签','width'=>'200');
    $displayarr['stock'] 	            = array('showname'=>'可发库存','width'=>'180');
    $displayarr['conditionerp'] 	    = array('showname'=>'状态','width'=>'100');
    $displayarr['create_user'] 		    = array('showname'=>'录入者','width'=>'100');
    $displayarr['cdate'] 	            = array('showname'=>'添加时间','width'=>'100');
    $displayarr['template'] 	        = array('showname'=>'导出模板','width'=>'225');

	/*JS文件包含与功能按钮*/
    $jslink 	= "<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />\n";
    $jslink    .= "<link rel='stylesheet' type='text/css' href='./staticment/css/product_list.css' />\n";
    $jslink    .= "<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>\n";
	$jslink    .= "<script src='./staticment/js/product_list.js?version=".time()."'></script>\n";

    $bannerstr 	= '<button onclick=window.location="index.php?action=product_new&detail=selectcategory&trh=list">新增产品</button>';
    $bannerstr .= '<button onclick=window.location="index.php?action=product_list&detail=import">导入产品</button>';
    $bannerstr .= '<button onclick=window.location="index.php?action=product_list&detail=outsport&sku='.$sku.'&product_name='.$product_name.'&startTime='.$startTime.'&endTime='.$endTime.'&cat_id='.$cat_id.'&supplier_id='.$supplier_id.'&create_user='.$create_user.'&eid='.$eid.'">导出产品</button>';
    $bannerstr .= '<button onclick=window.location="index.php?action=product_list&detail=userlabel">标签管理</button>';
    $bannerstr .= '<button onclick="reset_tag()" id="reset_tag">添至标签</button>';

    $this->V->mark(array('title'=>'产品列表'));
    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
    $temp = 'pub_list';
}

/*编辑产品名称*/
elseif($detail == 'edit_product_name'){

	if(empty($product_name)) {
		echo '1';
		exit();
	}

	$sid = $this->S->dao('product')->D->update_by_field(array('pid'=>$id),array('product_name'=>$product_name));
	if($sid) {
		echo '1';
	}else{
		echo '保存失败';
	}
}

elseif ($detail == 'getsupplier') {
    $q = strtolower($_GET["q"]);
	if (!$q) return;
    $result = array();
	$esseres = $this->S->dao('esse')->D->get_list(' and type=3 and name like"%'.$q.'%"','','','id,name');
	foreach ($esseres as $val) {
        $result[] = array(
            'id' => $val['id'],
            'name' => $val['name']
        );
    }

    echo json_encode($result);
}

/*导出产品SKU重量*/
elseif($detail == 'outsport'){
    //hidden框
    $hidelist = array();
    $hidelist[] = array('name'=>'detail','value'=>'export');
    $hidelist[] = array('name'=>'sku','value'=>$sku);
    $hidelist[] = array('name'=>'product_name','value'=>$product_name);
    $hidelist[] = array('name'=>'startTime','value'=>$startTime);
    $hidelist[] = array('name'=>'endTime','value'=>$endTime);
    $hidelist[] = array('name'=>'cat_id','value'=>$cat_id);
    $hidelist[] = array('name'=>'supplier_id','value'=>$supplier_id);
    $hidelist[] = array('name'=>'create_user','value'=>$create_user);
    $hidelist[] = array('name'=>'eid','value'=>$eid);

    //模版属性
    $export = array(
        'action' => 'index.php?action=product_list',
        'method' => 'post',
        'title' => '产品导出'
    );

    //复选框属性
    $datalist = array();
    foreach ($exportarr as $key=>$val) {
        /*判断权限*/
        if($key == 'cost1' && !$this->C->service('admin_access')->checkResRight('a_p_cost1')){
            continue;
        }
        
        $datalist[] = array('key' => $key, 'value' => $val);

    }

    $this->V->mark(array('title'=>'导出数据-产品列表(list)','hidelist' => $hidelist, 'export' => $export, 'datalist' => $datalist));
    $this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/export');
    display();
}

elseif ($detail == 'export') {
    //查询条件
    $sqlstr = '';
    $sqlstr.=empty($sku)?'':" and sku like '".$sku."%' ";
	$sqlstr.=empty($product_name)?'':" and product_name like '%".$product_name."%' ";
	$sqlstr.=empty($startTime)?'':' and p.cdate>="'.$startTime.' 00:00:00"';
	$sqlstr.=empty($endTime)?'':' and p.cdate<="'.$endTime.' 23:59:59"';
    $sqlstr.=empty($cat_id)?'':' and p.cat_id="'.$cat_id.'"';
	$sqlstr.=empty($supplier_id)?'':" and manufacturer like '%@".$supplier_id.",%' ";
	$sqlstr.=empty($create_user)?'':" and create_user='".$create_user."' ";
    $sqlstr.=empty($eid)?'':" and eid=".$eid;
    

	$process= $this->S->dao('process');
	

    //查询字段
    $selectattr = '';
    //导出字段
    $head_array = array();
    $head_array['images'] = '产品图片';
    if (count($checkattr) > 0) {
        for ($i = 0; $i < count($checkattr); $i++) {
            $selectattr .= $checkattr[$i].',';
            if ($checkattr[$i] == 'cost1') {
            	$head_array['cost1usd'] = '原始成本USD';
        		$head_array['cost1cny'] = '原始成本CNY';
            }
            elseif ($checkattr[$i] == 'cost2') {
            	$head_array['cost2usd'] = '销售成本USD';
        		$head_array['cost2cny'] = '销售成本CNY';
            }
            elseif ($checkattr[$i] == 'cost3') {
            	$head_array['cost3usd'] = '市场指导价USD';
        		$head_array['cost3cny'] = '市场指导价CNY';
            }
            else {
            	$head_array[$checkattr[$i]] = $exportarr[$checkattr[$i]];
            }
        }
        
        $selectattr = str_replace('cost', 'pc.cost', $selectattr);
        //替换即时库存
        $selectattr = str_replace('nowquantity','p.model_number',$selectattr);
        $selectattr .= 'coin_code';
        
        //合并相同sku，不同仓库对应的运费
        $selectattr = str_replace('shipping','GROUP_CONCAT(CONCAT(esse.name,":",shipping)) as shipping',$selectattr);

        $datalist = $product->select_by_attr_str($selectattr, $sqlstr);

        $service_rate = $this->C->service('exchange_rate');


        foreach($datalist as &$val){
            $url = $_SERVER['SERVER_NAME'];
            $backimg = json_decode($val['images'],1);
            $val['images'] = '<a href="http://'.$url.str_replace('staticment/dynamic/../../','',$backimg[0]['url']).'">点击查看</a>';
            
            //即时库存
            if(in_array('nowquantity',$_POST['checkattr'])){
                $where=" and temp.sku like '".$val['sku']."%' and temp.wid =10";
                $datalists = $process->get_allw_allsku($where,2);
                $val['nowquantity'] = $datalists[0]['sums'];
            }
            
            /*if (empty($eid)){
                $sku_shipping = '';
                $sku_house_shipping = $process->get_sku_all_house_shipping(' sku="'.$val['sku'].'"');
                if ($sku_house_shipping){
                    foreach($sku_house_shipping as $sku_data){
                        $sku_shipping .= $sku_data['name'].":".$sku_data['shipping'];
                    }
                    $val['shipping'] = $sku_shipping;
                }
            }*/
        }

        if (isset($head_array['cost1usd'])) {
	        /*转换货币，默认人民币不用转,导出统一用人民币显示*/
			foreach($datalist as &$val){
				$new_price = $service_rate->change_rate($val['coin_code'], 'CNY',$val['cost1']);
				$val['cost1cny'] = number_format($new_price,2);//全站数据保留两位小数
				$new_price = $service_rate->change_rate($val['coin_code'],'USD',$val['cost1']);
				$val['cost1usd'] = number_format($new_price,2);//全站数据保留两位小数
			}
        }
        if (isset($head_array['cost2usd'])) {
	        /*转换货币，默认人民币不用转,导出统一用人民币显示*/
			foreach($datalist as &$val){
				$new_price = $service_rate->change_rate($val['coin_code'], 'CNY',$val['cost2']);
				$val['cost2cny'] = number_format($new_price,2);//全站数据保留两位小数
				$new_price = $service_rate->change_rate($val['coin_code'],'USD',$val['cost2']);
				$val['cost2usd'] = number_format($new_price,2);//全站数据保留两位小数
			}
        }
        if (isset($head_array['cost3usd'])) {
	        /*转换货币，默认人民币不用转,导出统一用人民币显示*/
			foreach($datalist as &$val){
				$new_price = $service_rate->change_rate($val['coin_code'], 'CNY',$val['cost3']);
				$val['cost3cny'] = number_format($new_price,2);//全站数据保留两位小数
				$new_price = $service_rate->change_rate($val['coin_code'],'USD',$val['cost3']);
				$val['cost3usd'] = number_format($new_price,2);//全站数据保留两位小数
			}
        }
    }
    $filename = 'ProductList'.date('Y_m_d_h_i_s', time());
    $this->C->service('upload_excel')->download_excel($filename,$head_array,$datalist);
}

elseif ($detail == 'import') {
    $product = $this->S->dao('product');
    $product_cost = $this->S->dao('product_cost');
    $category = $this->S->dao('category');
    $exchange_rate = $this->S->dao('exchange_rate');
    /*质检图片*/
    $qualitylist  = $this->C->service('product')->get_qualitycheck_list(); 
    //取上传的文件的数组
    $upload_dir = "./data/uploadexl/product_list/";//上传文件保存路径的目录
    $fieldarray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q');//有效的excel列表值
    $head = 1;//以第一行为表头 
    $tablelist = '';
    /*读取已经上传的文件*/
    if($filepath){
        $all_arr =  $this->C->Service('upload_excel')->get_excel_datas_withkey($filepath, $fieldarray, $head);
        /*根据上传人取得渠道*/ 
        $iserror = 0;
        $product->D->query('begin');
        for($i=1; $i < count($all_arr); $i++){
            $insertarr = array(
            'product_name'=>$all_arr[$i]['product_name'],
            'shipping_weight'=>$all_arr[$i]['shipping_weight'],
            'box_shipping_weight'=>$all_arr[$i]['box_shipping_weight'],
            'product_dimensions'=>$all_arr[$i]['product_dimensions'],
            'box_product_dimensions'=>$all_arr[$i]['box_product_dimensions'],
            'upc_or_ean'=>$all_arr[$i]['MOQ'],
            'product_desc'=>$all_arr[$i]['product_desc'],
            'color'=>$all_arr[$i]['color'],
            'create_user'=>$_SESSION['eng_name'],
            'unit_box' => $all_arr[$i]['unit_box'],
            'key_product_features' => json_encode(array($all_arr[$i]['key_product_features'])),
            'attestation' => $all_arr[$i]['attestation'],
            'qualitycheck'=>$qualitylist[$all_arr[$i]['qualitycheck']]    
             );
            if ($all_arr[$i]['cat_name'] != '') {
                $rs1 = $category->get_category_by_name($all_arr[$i]['cat_name']);
                $insertarr['cat_id']=$rs1['cat_id'];
            }
            $insertarr['cdate'] = date('Y-m-d h:i:s', time());
            //检查是否存在sku 存在则更新，不存在则插入
            $checksku = $product->get_product_by_sku($all_arr[$i]['sku']);
            if($checksku){
                $sid = $product->D->update_by_field(array('sku'=>$all_arr[$i]['sku']),$insertarr);
            }else{
                $insertarr['sku']=$all_arr[$i]['sku'];
                $sid = $product->D->insert($insertarr);
            }
            if(!$sid) { $iserror = 1;echo  $b ; }
            if(!$all_arr[$i]['cost']) { $all_arr[$i]['cost'] = 0; }
            if(empty($all_arr[$i]['costp'])) { $all_arr[$i]['costp'] = $all_arr[$i]['cost'] * 1.05; }
            //product_cost 这张表不是自增的，所以insert这个函数及时成功了也会返回0
            //echo '<pre>';print_r($insertarr);die();
            $product_cost->D->insert(array('pid'=>$sid, 'cost1'=>$all_arr[$i]['cost'], 'cost2' => $all_arr[$i]['costp'], 'cdate'=>date('Y-m-d h:i:s', time()), 'coin_code'=>$all_arr[$i]['code']));
        }
        if ($iserror) {
            $product->D->query('rollback');
            $sucss_msg = '导入失败！';
        }
        else {
            $product->D->query('commit');
            $sucss_msg = '导入成功！';
        }
        $this->C->success($sucss_msg,'index.php?action=product_list&detail=list');
    }
    else {
            
            $all_arr =  $this->C->Service('upload_excel')->get_upload_excel_datas($upload_dir, $fieldarray, $head);
            $filepath = $this->getLibrary('basefuns')->getsession('filepath');
            //有错误的记录数量
            $data_error = 0; 
            $iserrorsku = 0;
            $bodylist = '';
            $tablelist .= '<table id="mytable">'; 
            /*表头特殊显示处理*/
            $tablelist.= $this->C->Service('upload_excel')->checkmod_head(&$all_arr,&$data_error,'product');
            //表头
            //$bodylist .= '<tr><th class="list">sku</th><th class="list">产品名称</th><th class="list">产品类型</th><th class="list">costp</th><th class="list">成本(USD)</th><th class="list">币种</th><th class="list">产品重量(kg)</th><th class="list">产品重量(标准箱)(kg)(USD)</th><th class="list">长宽高(cm)</th><th class="list">长宽高(标准箱)(cm)</th><th class="list">最低定量</th><th class="list">官方描述</th><th class="list">颜色</th></tr>';
            for($i=1; $i <= count($all_arr); $i++){
                //本条记录是否有错误
                $iserror = 0; 
                //查看sku是否存在产品
                $rs1 = $product->get_product_by_sku($all_arr[$i]['sku']);
                if (!$rs1) {
                    $bodylist .= '<td>'.$all_arr[$i]['sku'].'</td>';
                }
                //若存在，说明输入sku重复，提示
                else {
                    $bodylist .= '<td bgcolor="green" title="SKU已经存在">'.$all_arr[$i]['sku'].'</td>';
                    //$iserror = 1;
                    $iserrorsku = 1;
                }
                if (!$all_arr[$i]['cost']) {
                    $all_arr[$i]['cost'] = 0;
                }
                if (empty($all_arr[$i]['costp'])) {
                    $all_arr[$i]['costp'] = $all_arr[$i]['cost'] * 1.05;
                }
                $bodylist .= '<td>'.$all_arr[$i]['cost'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['costp'].'</td>';

                if ($all_arr[$i]['cost'] || $all_arr[$i]['costp']) { 
                    if(empty($all_arr[$i]['code'])){ 
                        $bodylist .= '<td bgcolor="red" title="填写了成本则币别不能为空">错误</td>';
                        $iserror = 1;
                    }else{
                        $rs3 = $exchange_rate->get_by_code($all_arr[$i]['code']);
                        if ($rs3) {
                            $bodylist .= '<td>'.$all_arr[$i]['code'].'</td>';
                        }
                        else {
                            $bodylist .= '<td bgcolor="red" title="币别不存在，请在汇率调整中查看系统录入的币别">'.$all_arr[$i]['code'].'</td>';
                            $iserror = 1;
                        }
                    }
                } 
                $bodylist .= '<td>'.$all_arr[$i]['product_name'].'</td>';
                if ($all_arr[$i]['cat_name'] != '') {
                    $rs2 = $category->get_category_by_name($all_arr[$i]['cat_name']);
                    if ($rs2) {
                        $bodylist .= '<td>'.$all_arr[$i]['cat_name'].'</td>';
                    }
                    //若存在，说明输入cat_name不存在，提示
                    else {
                        $bodylist .= '<td bgcolor="red" title="产品类别不存在">'.$all_arr[$i]['cat_name'].'</td>';
                        $iserror = 1;
                    }
                }
                else {
                    $bodylist .= '<td>'.$all_arr[$i]['cat_name'].'</td>';
                }
                $bodylist .= '<td>'.$all_arr[$i]['shipping_weight'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['box_shipping_weight'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['product_dimensions'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['box_product_dimensions'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['MOQ'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['product_desc'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['color'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['unit_box'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['key_product_features'].'</td>';
                $bodylist .= '<td>'.$all_arr[$i]['attestation'].'</td>';  
                //检查质检图片
                 if($all_arr[$i]['qualitycheck']){
                    if($qualitylist){
                        if(!$qualitylist[$all_arr[$i]['qualitycheck']]){
                            $bodylist .= '<td bgcolor="red" title="质检图片不存在">'.$all_arr[$i]['qualitycheck'].'</td>';
                            $iserror = 1;
                        }else{
                            $bodylist .= '<td>'.$all_arr[$i]['qualitycheck'].'</td>';
                        }
                    }else{
                         $bodylist .= '<td bgcolor="red" title="系统错误,获取不到质检图片,请联系管理员">'.$all_arr[$i]['qualitycheck'].'</td>';
                         $iserror = 1;
                    }
                }
                
                $bodylist .= '</tr>';
                if ($iserror) { $data_error++;}
            
            } 
            if (isset($all_arr)){
                    $tablelist .= $bodylist;
            }
            $tablelist .= '</table>';
            /*错误判断*/
//        if(!$data_error && isset($all_arr)){
//            $tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />'.$not_enougth_arryrow; 
//            $tablelist .= '<font color="#577dc6" size="1">'.$not_enougth_skutips.'</font><br><input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=product_list&detail=list">';
//        }elseif($data_error){
//            $tablelist .= '<font color="#577dc6" size="1">总共有'.$data_error.'条记录错误，请修正后重新上传。</font>';
//            unlink($filepath);//有错的文件删除掉
//        }
            if(isset($all_arr)){
                $tablelist .=$iserrorsku==1?'<font color="#577dc6" size="1" >有存在的sku,如果确认提交,则会替换掉以有的商品信息</font><br/>':'';
                if($data_error){
                    $tablelist .= '<font color="red" size="1">总共有'.$data_error.'条记录错误，请修正后重新上传,</font>';
                    unlink($filepath);//有错的文件删除掉
                }else{
                    $tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />'.$not_enougth_arryrow; 
                    $tablelist .= '<font color="#577dc6" size="1">'.$not_enougth_skutips.'</font><br><input type="submit" value="确认并提交" name="submit" id=submit_once><input type="reset" value="取消" onclick=window.location="index.php?action=product_list&detail=list">';  
                } 
            }
    }
    //echo $bodylist;
    $submit_action = 'index.php?action=product_list&detail=import';
    $temlate_exlurl = 'data/uploadexl/sample/product_list.xls';
    $this->V->set_tpl('admintag/tag_header','F');
    $this->V->set_tpl('admintag/tag_footer','L');
    $this->V->mark(array('title'=>'导入产品-产品列表(list)','tablelist'=>$tablelist,'submit_action'=>$submit_action,'temlate_exlurl'=>$temlate_exlurl));
    $this->V->set_tpl('adminweb/commom_excel_import');
    display();
}

elseif($detail == 'delete'){

	$product = $this->S->dao('product');
	if(!$this->C->service('admin_access')->checkResRight('r_p_del')){$this->C->ajaxmsg(0);}
    
    /*如果已经有发生额了，就不能删除*/
    $p_id = $this->S->dao('process')->D->get_count(array('pid'=>$pid));
    if ($p_id > 0){
        echo "删除失败，请先删除出入库记录";exit();
    }
	$sqlerror = 0;
	$product->D->query('begin');
    
    
	//删除产品
	$dpid = $product->D->delete_by_field(array('pid'=>$pid));
	if(!$dpid){
		$sqlerror = 1;
	}
	//删除事务
	$dproid = $this->S->dao('process')->D->delete_by_field(array('pid' => $pid));
	if(!$dproid){
		$sqlerror = 1;
	}
	//删除别名
	$dsaid = $this->S->dao('sku_alias')->D->delete_by_field(array('pid' => $pid));
	if(!$dsaid){
		$sqlerror = 1;
	}
	//删除组装方案
	$sku_assembly = $this->S->dao('sku_assembly');
	$assres = $sku_assembly->D->query_array("select distinct assembleid from sku_assembly where pid={$pid} or child_pid={$pid}");
	foreach ($assres as $val) {
		$dassid = $sku_assembly->D->delete_by_field(array('assembleid' => $val['assembleid']));
		if(!$dassid){
			$sqlerror = 1;
		}
	}
	//删除成本
	$dcid = $this->S->dao('product_cost')->D->delete_by_field(array('pid' => $pid));
	if(!$dcid){
		$sqlerror = 1;
	}
	//删除产品定制
	$dceng_name = $this->S->dao('product_custom')->D->delete_by_field(array('pid' => $pid));
	if(!$dceng_name){
		$sqlerror = 1;
	}
	//删除图片
	$res = $this->S->dao('product_images')->D->select('images',"pid={$pid}");
	$arr = json_decode($res['images'], true);
	$dpiid = $this->S->dao('product_images')->D->delete_by_field(array('pid' => $pid));
	if(!$dpiid){
		$sqlerror = 1;
	}
	if ($sqlerror) {
		$product->D->query('rollback');
		echo "删除失败";
	}
	else {
		$product->D->query('commit');
		foreach ($arr as $val) {
			if (is_file($val['dl_url'])) {
				unlink($val['dl_url']);
			}
		}
		echo '1';
	}

}

/*产品编辑*/
elseif($detail == 'edit'){
    
    //修改产品权限控制
    if($isshow != 1){
    if(!$this->C->service('admin_access')->checkResRight('r_p_edit')){$this->C->sendmsg();}
    }

	if($pid!=''){
		$pid = (int)$pid;
	}else{
		exit('无效的产品ID');
	}

	/*获取产品相关信息*/
	$product 				= $this->S->dao('product');
	$datalist 				= $product->getProductByID($pid);
	$basefuns 				= $this->getLibrary('basefuns');
	$key_product_features 	= $basefuns->tmpGetJsonStrP($datalist[0]['key_product_features'], 'key_product_features', '_k');
	$platinum_keywords 		= $basefuns->tmpGetJsonStrP($datalist[0]['platinum_keywords'], 'platinum_keywords', '_p');
	$related_keywords 		= $basefuns->tmpGetJsonStrP($datalist[0]['related_keywords'], 'related_keywords', '_r');

	/*获得产品有效类别，生成类别下拉框*/
	$category 				= $this->S->dao('category');
	$catlist 				= $category->category_treelist();
	$catstr 				= '<select name="cat_id" id="cat_id">';
	$catstr				   .= '<option value="">===请选择类别===</option>';
	foreach($catlist as $key=>$val){
		$selected 			= $val['cat_id']==$datalist[0]['cat_id']?' selected':'';
		$ifpar 				= $val['parent_id']?'...':'';
		$catstr	   		   .="<option value=".$val['cat_id']. $selected.">&nbsp;".$ifpar.$val['cat_name']."</option>";
	}
	$catstr		    	   .="</select>";
	/*判断供应商权限*/
	$supplier_str 			= '';
	if($this->C->service('admin_access')->checkResRight('r_t_suplimod')){
		$this->V->view['supplier_mod'] = '*';
		$supplier_str 	   .= '<div style="float:left"><input type="text"  name="supplier" id="supplier" /></div>';
		//获取选择的供应商的列表
		$supplierary 		= explode(",",$datalist[0]['manufacturer']);
        //echo '<pre>';print_r($supplierary);
		if(is_array($supplierary) && !empty($supplierary)){//列出此产品所对应的供应商
			$supplier_str  .= '<div style="float:left; margin-left:50px;width:380px; height:100px;overflow:auto; font-size:12px"><ul id="supplierselected">';
			foreach($supplierary as $su){
				$su_id  = (int) substr($su, 1);
				if($su_id){
					$supplier_name	= $this->S->dao('esse')->D->select('name','id='.$su_id);
					$supplier_str  .= '<li style="list-style: circle" name="supplier_id"><input type="hidden" name="supplier_id[]" value="'.$su_id.'">'.$supplier_name[0].'<a onclick="removesupplier(this)" style="cursor:pointer;">×</a></li>';
				}
			}
			$supplier_str  .='</ul></div>';
		}else{
			$supplier_str  .= '<div style="float:left; margin-left:50px;width:380px; height:100px;overflow:auto; font-size:12px"><ul id="supplierselected"></ul></div>';
		}

	}else{
		$supplier_str = '<font color="#b8c4c6" size=2>无查看权限</font>';
	}
	$this->V->view['supplier_str'] = $supplier_str;

	/*获得cost2*/
	$product_cost			= $this->S->dao('product_cost');
	$cost 					= $product_cost->D->select('cost1,cost2,cost3,coin_code','pid='.$pid);

	$iscost = 0;
	if($cost){$iscost =1;}else{$cost['coin_code'] = 'USD';}//如果之前未填cost,则编辑时默认选中USD
	/*判断权限*/
	if($apcost1 = $this->C->service('admin_access')->checkResRight('a_p_cost1')){
		$cost1input 		= "<input type='text' id='cost1' name='cost1' onkeyup='docost()' style='width:120px; background:#EBEBE4' readonly='readonly' value='$cost[cost1]' ><span class=tips>&nbsp;</span>";
        /*上次采购价*/
		$costpre = $this->S->dao('product_cost')->get_precost($pid);
		if($cost['coin_code'] != 'CNY'){
			$costpre = $this->C->service('exchange_rate')->change_rate('CNY', $cost['coin_code'], $costpre);
			$costpre = number_format($costpre, 2);
		}
		$prestockinput		= '<input type="text" id="costpre" name="costpre" style="width:120px; background:#EBEBE4" readonly="readonly" value="'.$costpre.'"/>';
	}else{
		$cost1input 		= '<font color="#b8c4c6" size=2>无查看权限</font><input type="hidden" id="cost1" name="cost1" value='.$cost['cost1'].'>';
		$prestockinput		= '<font color="#b8c4c6" size=2>无查看权限</font><input type="hidden" id="costpre" name="costpre" value='.$costpre.'>';
	}

	/*获得图片相关信息*/
	$product_images 		= $this->S->dao('product_images');
	$imagelist 				= $product_images->D->select('images,listingimg','pid='.$pid);
	$isimage 				= 0;
	if($imagelist){	$images_ary = json_decode($imagelist['images'],true);$listingimages_ary = json_decode($imagelist['listingimg'],true);$isimage = 1;} 
	if($images_ary){
		$backstrArray 		= '';
        $arrnum             = 0;
        $picArr = array();
		foreach ($images_ary as $key=>$images_aryy) {
		  //默认大图是pic.jpg
		  if ($images_aryy['desc'] == 'pic.jpg'){
                //图片显示按照原图显示
                $width  = 115;
                $height = 115;
                $imgurl = substr($images_aryy['url'],strrpos($images_aryy['url'],'data'));
                $image_info = getimagesize($imgurl);//获取图片的width,height
                if ($image_info[0] < $image_info[1])
                $width = $width*($image_info[0]/$image_info[1]);
                else
                $height = $height*($image_info[1]/$image_info[0]);
                $checkall = "<input type='checkbox' title='下载' style='width:15px;height:15px;' value=".$images_aryy['url']." name='checkall' id='checkall' />";
				$descwidth = 80;
				$backstr   .= "<div style='width:116px;height:150px;float:left;padding:3px;padding-top:15px;'>";
				$backstr   .= "<div style='float:left;width:115px;height:115px;text-align:center'><a href='javascript:void(0);'  class='tooltip_d'><img src=".$images_aryy['url']."  class='img0' data='$arrnum' alt='' title='查看图片' style='border:solid 1px #828482; width:".$width.";height:".$height.";'></a></div>";
				$backstr   .= "<div style='float:left;'>".$checkall."<input type='text' name='img_desc[]' value='".$images_aryy['desc']."' style='width:".$descwidth.";' title='input the description'>";
				$backstr   .= "<input type='hidden' name='img_url[]' value=".$images_aryy['url']."><input type='hidden' name=img_type[] value=".$images_aryy['type'].">&nbsp;";
				if(!$isshow){
				   $backstr	.= "<span title='delete' style='cursor:pointer;color:#828482' onclick=$(this).parent().parent().remove();delImage('".$images_aryy['dl_url']."')>&times;</span>";
				}
				$backstr .= "</div></div>";
            }
            //pic1~pic99
            elseif (preg_match('/^pic([0-9]{1,100}(\.jpg))$/',$images_aryy['desc'])){
                $picArr[] = $images_aryy;
            }
            else{ //原始图片
                 //图片显示按照原图显示
                $width  = 115;
                $height = 115;
                $imgurl = substr($images_aryy['url'],strrpos($images_aryy['url'],'data'));
                $image_info = getimagesize($imgurl);//获取图片的width,height

                if ($image_info[0] < $image_info[1])
                    $width = $width*($image_info[0]/$image_info[1]);
                 else
                    $height = $height*($image_info[1]/$image_info[0]);

                $checkall = "<input type='checkbox' title='下载' style='width:15px;height:15px;' value=".$images_aryy['url']." name='checkall' id='checkall' />";
				$descwidth = 80;
				$backstr3   		.= "<div style='width:116px;height:150px;float:left;padding:3px;padding-top:15px;'>";
				$backstr3   		.= "<div style='float:left;width:115px;height:115px;text-align:center'><a href='javascript:void(0);'  class='tooltip_d'><img src=".$images_aryy['url']."  class='img$key' data='$arrnum' alt='' title='查看图片' style='border:solid 1px #828482; width:".$width.";height:".$height.";'></a></div>";
				$backstr3   		.= "<div style='float:left;'>".$checkall."<input type='text' name='img_desc[]' value='".$images_aryy['desc']."' style='width:".$descwidth.";' title='input the description'>";
				$backstr3   		.= "<input type='hidden' name='img_url[]' value=".$images_aryy['url']."><input type='hidden' name=img_type[] value=".$images_aryy['type'].">&nbsp;";
				if(!$isshow){
				   $backstr3		.= "<span title='delete' style='cursor:pointer;color:#828482' onclick=$(this).parent().parent().remove();delImage('".$images_aryy['dl_url']."')>&times;</span>";
				}
				$backstr3   		.= "</div></div>";
                $arrnum++;
            }
		}

        if($picArr){
            //$picArr = $this->getLibrary('array')->array_sort_for($picArr,'desc');
            foreach($picArr as $v) $r[] = substr($v['desc'], 3, -4);
            array_multisort($r, $picArr);
            foreach($picArr as $key=>$images_aryy){
                //图片显示按照原图显示
                $width  = 115;
                $height = 115;
                $imgurl = substr($images_aryy['url'],strrpos($images_aryy['url'],'data'));
                $image_info = getimagesize($imgurl);//获取图片的width,height

                if ($image_info[0] < $image_info[1])
                    $width = $width*($image_info[0]/$image_info[1]);
                 else
                    $height = $height*($image_info[1]/$image_info[0]);

                $checkall = "<input type='checkbox' title='下载' style='width:15px;height:15px;' value=".$images_aryy['url']." name='checkall' id='checkall' />";
    			$descwidth = 80;
    			$backstr2   		.= "<div style='width:116px;height:150px;float:left;padding:3px;padding-top:15px;'>";
    			$backstr2   		.= "<div style='float:left;width:115px;height:115px;text-align:center'><a href='javascript:void(0);'  class='tooltip_d'><img src=".$images_aryy['url']."  class='img$key' data='$arrnum' alt='' title='查看图片' style='border:solid 1px #828482; width:".$width.";height:".$height.";'></a></div>";
    			$backstr2   		.= "<div style='float:left;'>".$checkall."<input type='text' name='img_desc[]' value='".$images_aryy['desc']."' style='width:".$descwidth.";' title='input the description'>";
    			$backstr2   		.= "<input type='hidden' name='img_url[]' value=".$images_aryy['url']."><input type='hidden' name=img_type[] value=".$images_aryy['type'].">&nbsp;";
    			if(!$isshow){
    			   $backstr2		.= "<span title='delete' style='cursor:pointer;color:#828482' onclick=$(this).parent().parent().remove();delImage('".$images_aryy['dl_url']."')>&times;</span>";
    			}
    			$backstr2   		.= "</div></div>";
                $arrnum++;
            }
        }

    $backstrArray = $backstr.$backstr2.$backstr3;
	}
   
    /*listing*/
    if($listingimages_ary){
		foreach ($listingimages_ary as $key=>$images_arr){
            $listingbackstr = '';
            $listingbackstr2 = '';
            $listingbackstr3 = '';
            $listingpicArr = array();
            $arrnum = 0;
            foreach ($images_arr as $k=>$images_aryy) {
                //if($k>2){break;}
                //默认大图是pic.jpg
                if ($images_aryy['desc'] == 'pic.jpg'){
                    //图片显示按照原图显示
                    $width  = 115;
                    $height = 115;
                    $imgurl = substr($images_aryy['url'],strrpos($images_aryy['url'],'data'));
                    $image_info = getimagesize($imgurl);//获取图片的width,height
                    if ($image_info[0] < $image_info[1])
                    $width = $width*($image_info[0]/$image_info[1]);
                    else
                    $height = $height*($image_info[1]/$image_info[0]);
                    $listingcheckall = "<input type='checkbox' checked='true' title='下载' onclick='checkboxlisting(\"".$key."\",\"".$k."\",$(this))' style='width:15px;height:15px;' value=".$images_aryy['url']." name='".$key."checkall' id='".$key."checkall' />";
                    $descwidth = 80; 
                    $listingbackstr   .= "<div style='width:152px;height:135px;float:left;padding:10px 3px 3px'>";
                    $listingbackstr   .= "<div style='float:left;width:115px;height:115px;text-align:center; margin:0 18px;'><a href='javascript:listingimgshow(\"".$key."\",".$k.");'  class='listing_tooltip_d'><img src=".$images_aryy['url']."  class='img$key$k' data='$arrnum' alt='' title='查看图片' style='border:solid 1px red; width:".$width.";height:".$height.";'></a></div>";
                    $listingbackstr   .= "<div style='float:left; margin:4px 13px;'>".$listingcheckall."<input type='text' name='listing_img_desc[]' value='".$images_aryy['desc']."' style='width:".$descwidth.";' title='input the description'>";
                    $listingbackstr   .= "<input type='hidden' name='listing_type[]' value=".$key." ><input type='hidden' name='listing_dl_file_url[]' value=".$images_aryy['dl_url']."><input type='hidden' name='listing_img_url[]' value=".$images_aryy['url']."><input type='hidden' name=listing_img_type[] value=".$images_aryy['type'].">&nbsp;";
                    if(!$isshow){
                    $listingbackstr	.= "<span title='delete' style='cursor:pointer;color:#828482' onclick=$(this).parent().parent().remove();delImage('".$images_aryy['dl_url']."')>&times;</span>";
                    }
                    $listingbackstr .= "</div></div>"; 
                    
                }
                //pic1~pic99
                elseif (preg_match('/^pic([0-9]{1,100}(\.jpg))$/',$images_aryy['desc'])){
                    $listingpicArr[] = $images_aryy;
                }
                else{ //原始图片
                    //图片显示按照原图显示
                    $width  = 115;
                    $height = 115;
                    $imgurl = substr($images_aryy['url'],strrpos($images_aryy['url'],'data'));
                    $image_info = getimagesize($imgurl);//获取图片的width,height 
                    if ($image_info[0] < $image_info[1])
                        $width = $width*($image_info[0]/$image_info[1]);
                    else
                        $height = $height*($image_info[1]/$image_info[0]);
                    
                    $listingcheckall = "<input type='checkbox' checked='true' title='下载' onclick='checkboxlisting(\"".$key."\",\"".$k."\",$(this))' style='width:15px;height:15px;' value=".$images_aryy['url']." name='".$key."checkall' id='".$key."checkall' />";
                    $descwidth = 80;
                    $listingbackstr3   		.= "<div style='width:152px;height:135px;float:left;padding:10px 3px 3px'>";
                    $listingbackstr3   		.= "<div style='float:left;width:115px;height:115px;text-align:center; margin:0 18px;'><a href='javascript:listingimgshow(\"".$key."\",".$k.");'  class='listing_tooltip_d'><img src=".$images_aryy['url']."  class='img$key$k' data='$arrnum' alt='' title='查看图片' style='border:solid 1px red; width:".$width.";height:".$height.";'></a></div>";
                    $listingbackstr3   		.= "<div style='float:left;margin:4px 13px;'>".$listingcheckall."<input type='text' name='listing_img_desc[]' value='".$images_aryy['desc']."' style='width:".$descwidth.";' title='input the description'>";
                    $listingbackstr3   		.= "<input type='hidden' name='listing_type[]' value=".$key." ><input type='hidden' name='listing_dl_file_url[]' value=".$images_aryy['dl_url']."><input type='hidden' name='listing_img_url[]' value=".$images_aryy['url']."><input type='hidden' name=listing_img_type[] value=".$images_aryy['type'].">&nbsp;";
                    if(!$isshow){
                    $listingbackstr3		.= "<span title='delete' style='cursor:pointer;color:#828482' onclick=$(this).parent().parent().remove();delImage('".$images_aryy['dl_url']."')>&times;</span>";
                    }
                    $listingbackstr3   		.= "</div></div>";
                    $arrnum++;
                }  
            }
            
            if($listingpicArr){
                //$picArr = $this->getLibrary('array')->array_sort_for($picArr,'desc');
                foreach($listingpicArr as $v) $listingr[] = substr($v['desc'], 3, -4);
                array_multisort($listingr, $listingpicArr); 
                foreach($listingpicArr as $ke=>$images_aryy){
                    //图片显示按照原图显示
                    $width  = 115;
                    $height = 115;
                    $imgurl = substr($images_aryy['url'],strrpos($images_aryy['url'],'data'));
                    $image_info = getimagesize($imgurl);//获取图片的width,height

                    if ($image_info[0] < $image_info[1])
                        $width = $width*($image_info[0]/$image_info[1]);
                    else
                        $height = $height*($image_info[1]/$image_info[0]);

                    $listingcheckall = "<input type='checkbox' checked='true' title='下载' onclick='checkboxlisting(\"".$key."\",\"".$ke."\",$(this))' style='width:15px;height:15px;' value=".$images_aryy['url']." name='".$key."checkall' id='".$key."checkall' />";
                    $descwidth = 80;
                    $listingbackstr2   		.= "<div style='width:152px;height:135px;float:left;padding:10px 3px 3px'>";
                    $listingbackstr2   		.= "<div style='float:left;width:115px;height:115px;text-align:center; margin:0 18px;'><a href='javascript:listingimgshow(\"".$key."\",".$ke.");'  class='listing_tooltip_d'><img src=".$images_aryy['url']."  class='img$key$ke' data='$arrnum' alt='' title='查看图片' style='border:solid 1px red; width:".$width.";height:".$height.";'></a></div>";
                    $listingbackstr2   		.= "<div style='float:left;margin:4px 13px;'>".$listingcheckall."<input type='text' name='listing_img_desc[]' value='".$images_aryy['desc']."' style='width:".$descwidth.";' title='input the description'>";
                    $listingbackstr2   		.= "<input type='hidden' name='listing_type[]' value=".$key." ><input type='hidden' name='listing_dl_file_url[]' value=".$images_aryy['dl_url']."><input type='hidden' name='listing_img_url[]' value=".$images_aryy['url']."><input type='hidden' name='listing_img_type[]' value=".$images_aryy['type'].">&nbsp;";
                    if(!$isshow){
                    $listingbackstr2		.= "<span title='delete' style='cursor:pointer;color:#828482' onclick=$(this).parent().parent().remove();delImage('".$images_aryy['dl_url']."')>&times;</span>";
                    }
                    $listingbackstr2   		.= "</div></div>";
                    $arrnum++;
                }
            }  
            $this->V->view['listing_'.$key]= $listingbackstr.$listingbackstr2.$listingbackstr3;
            
        }
    } 
    
	/*质检图片*/
	$qualityhtml  = $this->C->service('product')->get_qualitycheck($datalist[0]['qualitycheck']);
    
	$this->V->view['key_product_features']	= $key_product_features;
	$this->V->view['platinum_keywords'] 	= $platinum_keywords;
	$this->V->view['related_keywords']		= $related_keywords;
	$this->V->view['backstr'] 				= $backstrArray;
	$this->V->view['cost2'] 				= $cost['cost2'];
	$this->V->view['cost3'] 				= $cost['cost3'];
	$this->V->view['costpre'] 				= $prestockinput;
	$this->V->view['coin_code'] 			= $cost['coin_code'];
	$this->V->view['catstr'] 				= $catstr;
	$this->V->view['product'] 				= $datalist[0];
	$this->V->view['iscost'] 				= $iscost;
	$this->V->view['cost1input'] 			= $cost1input;
	$this->V->view['isimage'] 				= $isimage;
	$this->V->view['qualityhtml'] 			= $qualityhtml;
	$this->V->view['title'] 				= '编辑产品-产品列表(list)';

    if (isset($isshow)) {
        $this->V->view['isshow'] = '1';
        $this->V->view['title'] = '查看产品-产品列表(list)';
    }

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/productedit');
	display();
}

elseif($detail == 'editmod'){

    /*图片编辑处理*/
if ($sumbit == '提交'){ 
	if($sku == '' && $product_name==''){
        echo "<script>alert('Sku和产品名称不能同时为空！请检查后再输入！');history.back(-1)</script>";
	}else{
		$product    = $this->S->dao('product');
		$sku_alias  = $this->S->dao('sku_alias');
        $pro_esse   = $this->S->dao('prc_esse');

		/*检测产品SKU与名称是否重复*/
		$checkbysku = $product->D->select('pid',"sku='{$sku}'");
		$checkbyname = $product->D->select('pid',"product_name='{$product_name}'");

		if($checkbysku['pid'] && $checkbysku['pid']!=$pid&&$sku!=''){
			echo "<script>alert('Sku重复啦，请检查后再输入！');history.back(-1)</script>";
		}elseif($checkbyname['pid'] && $checkbyname['pid']!=$pid&&$product_name!=''){
			echo "<script>alert('产品名称重复啦，请检查后再输入！');history.back(-1)</script>";
		}else{
			/*开始更新,对产品图片信息整合*/
			if($img_url){
				$i = 0;
				foreach($img_type as $t){
				$images[] = array('type' => $t, 'url'=> $img_url[$i], 'desc' => $img_desc[$i],'dl_url'=>$dl_file_url[$i]);
				$i++;
				}

				if (get_magic_quotes_gpc()){ $images= addslashes(json_encode($images));}else{$images = json_encode($images);}
			}
            /*listing*/ 
            if($listing_img_url){
				$j = 0;
                $listing_images = array();
				foreach($listing_img_type as $t){
				$listing_images[$listing_type[$j]][] = array('type' => $t, 'url'=> $listing_img_url[$j], 'desc' => $listing_img_desc[$j],'dl_url'=>$listing_dl_file_url[$j]);
				$j++;
				} 
				if (get_magic_quotes_gpc()){ $listing_images= addslashes(json_encode($listing_images));}else{$listing_images = json_encode($listing_images);} 
			}  
			$key_product_features = json_encode($key_product_features);
			$platinum_keywords = json_encode($platinum_keywords);
			$related_keywords = json_encode($related_keywords);

			date_default_timezone_set('Asia/Hong_Kong');
			$mdate = date("Y-m-d H:i:s"); 
            
            
           
			/*更新product表，如果没有全权限，则只能修改用户描述区*/
			if(!$this->C->service('admin_access')->checkResRight('r_p_edit')){
				$sid = $product->D->update_by_field(array('pid'=>$pid),array('product_desc2'=>$product_desc2));
				if($sid){echo "<script>alert('修改成功！由于你无全权限，只修改了用户描述部分。');window.location='index.php?action=product_list&detail=edit&pid={$pid}';</script>";}
				exit();
			}
			else {
				$sqlerror = 0;
				$product->D->query('begin');
				/* create on 2012-04-24
				 * by wall
				 * 级联修改事务表，别名表中sku
				 * */
				$skures = $product->D->select('sku', "pid={$pid}");
				//如果修改了产品sku，级联修改事务表，别名表中sku
				if ($skures['sku'] != $sku) {
					$sku_aliasres = $sku_alias->D->select('id', "sku_code='{$sku}' and pid<>{$pid}");
					if ($sku_aliasres) {
						echo "<script>alert('该sku已经为别名。');window.location='index.php?action=product_list&detail=edit&pid={$pid}';</script>";
						exit();
					}

					$said = $sku_alias->D->update_by_field(array('pid' => $pid), array('pro_sku' => $sku));
					if (!$said) {
						$errorstr .= "原名修改错误\n";
						$sqlerror = 1;
					}
					$said = $sku_alias->D->update_by_field(array('pid' => $pid, 'sku_code' => $skures['sku']), array('sku_code' => $sku));
					if (!$said) {
						$errorstr .= "别名修改错误\n";
						$sqlerror = 1;
					}
					$proid = $this->S->dao('process')->D->update_by_field(array('pid' => $pid), array('sku' => $sku));
					if (!$proid) {
						$errorstr .= "订单sku修改错误\n";
						$sqlerror = 1;
					}
				}
                //商品的英文描述
                if(empty($eng_product_desc3)){
                    echo "<script>alert('商品的英文描述不能为空！');history.back(-1)</script>";
                    exit;
                } 
                
				$supplier_id = $_POST['supplier_id'];
                
                //切割product->供应商
                if (is_array($supplier_id)){
                    $ret = $pro_esse->D->delete_by_field(array('pid'=>$pid));
                    foreach($supplier_id as $val){
                        $d['pid'] = $pid;
                        $d['eid'] = $val;
                        if (!$pro_esse->D->insert($d)){$errorstr .= "供应商信息修改失败\n"; $sqlerror = 1;}
                    }
                }
                
				foreach($supplier_id as $seng_name){
					$supplier_id_str .= '@'.$seng_name.',';//特殊处理选择的supplier_id
				}

				$ppid = $product->D->update_by_field(array('pid'=>$pid),array('cat_id'=>$cat_id,'sku'=>$sku,'product_name'=>$product_name,'manufacturer'=>$supplier_id_str,'brand_name'=>$brand_name,'model_number'=>$model_number,'manufacturer_part_number'=>$manufacturer_part_number,'upc_or_ean'=>$upc_or_ean,'conditionerp'=>$conditionerp,'key_product_features'=>$key_product_features,'product_desc'=>$product_desc,'product_desc2'=>$product_desc2,'product_desc3'=>$eng_product_desc3,'platinum_keywords'=>$platinum_keywords,'related_keywords'=>$related_keywords,'target_customers'=>$target_customers,'product_dimensions'=>$product_dimensions,'shipping_weight'=>$shipping_weight,'box_product_dimensions'=>$box_product_dimensions,'box_shipping_weight'=>$box_shipping_weight,'style_name'=>$style_name,'color'=>$color,'size'=>$size,'qualitycheck'=>$qualitycheck,'box_type'=>$box_type,'unit_box'=>$unit_box,'mdate'=>$mdate,'attestation'=>$attestation,'product_weight'=>$product_weight,'product_size'=>$product_size));
				if (!$ppid) {
					$errorstr .= "产品sku修改错误\n";
					$sqlerror = 1;
				}

				/*更新市场指导价*/
				$product_cost = $this->S->dao('product_cost');
				if($iscost == 1){
					$pcid = $product_cost->D->update_by_field(array('pid'=>$pid),array('cost1'=>$cost1,'cost2'=>$cost2,'cost3'=>$cost3,'coin_code'=>$coin_code,'mdate'=>$mdate));
				}
				elseif($iscost ==0){
					$pcid = $product_cost->D->insert(array('pid'=>$pid,'cost3'=>$cost3,'coin_code'=>$coin_code,'cdate'=>$mdate));
				}

				/*更新product_images表*/
				$product_images = $this->S->dao('product_images');
				if($isimage == 1){
					$piid = $product_images->D->update_by_field(array('pid'=>$pid),array('images'=>$images,'mdate'=>$mdate,'listingimg'=>$listing_images));
				}
				elseif($isimage == 0){
					$piid = 1;
					if ($images) {
						$piid = $product_images->D->insert(array('pid'=>$pid,'images'=>$images,'cdate'=>$mdate));
					}
                    if ($listing_images) {
						$piid = $product_images->D->insert(array('pid'=>$pid,'listingimg'=>$listing_images));
					}
                    
				} 
				if ($sqlerror) {
					$product->D->query('rollback');
					echo "<script>alert('修改失败！');window.location='index.php?action=product_list&detail=edit&pid={$pid}';</script>";
				}
				else {
					$product->D->query('commit');
					echo "<script>window.location='index.php?action=product_list&detail=edit&pid={$pid}';</script>";
				}
			}

		}
	}
 }
}

//导出产品信息模板
elseif($detail == 'product_template'){
    //获取产品相关信息
	$product = $this->S->dao('product');
	$images = $this->S->dao('product_images');

	$product_desc = $product->D->get_one_by_field(array('pid'=>$pid),'product_desc');
    $product_images = $images->D->get_one_by_field(array('pid'=>$pid),'images');
    $product_images_arr = json_decode($product_images['images'],true);
    foreach($product_images_arr as $val)
    {
        $product_images_string .= '<img src="http://erp.loftk.com.cn/'.$val["url"].'" width="220" height="220" style="padding:10px 0px" />';
    }

    //获取模版数据
    $filename = "./template/product/".$tid.".html";
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize ($filename));

    //替换模板中的标签
    $contents = str_replace('[template.description]',$product_desc['product_desc'],$contents);
    $contents = str_replace('[template.gallery]',$product_images_string,$contents);

    $this->C->service('upload_excel')->download('product_template_'.time(),$contents,'html');
}

// 查询库存
elseif ($detail == 'check_stock') {
    $esse = $this->S->dao('esse');
    $process = $this->S->dao('process');
    $sku_alias = $this->S->dao('sku_alias');
    $info_amazon = $this->S->dao('info_amazon');

    $warehouse = $esse->get_all_other_warehouse();
	/*如果有接口仓库*/
	if($warehouse){
		foreach($warehouse as $val) {
			$in .= $val['id'].',';
		}
		$in = substr($in, 0, strlen($in)-1);
		$sqlstr .= ' and temp.sku="'.$checksku.'" and e.id not in ('.$in.')';
	}else{
		$sqlstr .= ' and temp.sku="'.$checksku.'" ';
	}


    /*查库存数(实实在在库存可发数量不包括损益的)*/
    $datalist = $process->get_allw_allsku($sqlstr,2);

    // 目标sku别名
    $sql = ' and sold_way="Amazon组" ';
    $sql .= ' and pro_sku ="'.$checksku.'" ';
    $sku_code = $sku_alias->select_sku_code($sql);

    if ($sku_code) {
        foreach ($sku_code as $val) {
            $code_arr[] = $val['sku_code'];
        }
        $length = count($code_arr);
        set_include_path(get_include_path() . PATH_SEPARATOR . './api');
        foreach ($warehouse as $val) {
            $amazon_info = $info_amazon->get_one_by_id($val['a_id']);
            $amazon_res_arr = get_amazon_warehouse($amazon_info, $code_arr);
            if (!$amazon_res_arr) {
                echo '<script>alert("请录入正确sku的amazon别名！")</script>';
                exit();
            }
            $datanum = count($datalist);
            // 统计某项sku的总在途库存和总可发库存
            $loadquantity = 0;
            $instockquantity = 0;
            for ($i = 0; $i < $length; $i++) {
                // 在途库存
                $temp = $amazon_res_arr[$sku_code[$i]['sku_code']][0] - $amazon_res_arr[$sku_code[$i]['sku_code']][1];
                $loadquantity += $temp;
                // 可发库存
                $temp = $amazon_res_arr[$sku_code[$i]['sku_code']][1];
                $instockquantity += $temp;
            }
            $datalist[$datanum]['wid'] = $val['id'];
            $datalist[$datanum]['warename'] = $val['name'];
            $datalist[$datanum]['sku'] = $sku_code[0]['pro_sku'];
            $datalist[$datanum]['pid'] = $sku_code[0]['pid'];
            $datalist[$datanum]['product_name'] = $sku_code[0]['product_name'];
            $datalist[$datanum]['sums'] = $instockquantity;
            $datalist[$datanum]['in_waresums'] = $loadquantity;
            $datalist[$datanum]['in_soldsums'] = 0;
            $datalist[$datanum]['tensoldsums'] = 0;


        }
    }

    if($datalist){
	    $html = '<table cellpadding="0" cellspacing="0"><tbody><tr><td>仓库</td><td>库存</td></tr>';
	    foreach($datalist as $val){
	        $warename = $val['warename'];
	        $sums = $val['sums'];
	        $html .= "<tr><td>".$warename."</td><td>".$sums."</td></tr>";
	    }
	    $html .= '</tbody></table>';
	    echo $html;
    }else{
    	echo '无数据';
    }

}

//抓取描述
elseif ($detail == 'get_description') {
    $contents = file_get_contents($url);

    if($contents){
        //截取标题
        $title_contents = $contents;
        $title_start= strpos($title_contents, 'btAsinTitle');
        $title_contents = substr($title_contents,$title_start);
        $title_end = strpos($title_contents, '</span>');
        $title_contents = substr($title_contents,0,$title_end);
        $title_contents = str_replace('btAsinTitle"','',$title_contents);
        $title_contents = str_replace('>','',$title_contents);

        //截取特征
        $features_contents = $contents;
        $features_start= strpos($features_contents, 'bucket normal');
        $features_contents = substr($features_contents,$features_start);
        $features_end = strpos($features_contents, '</td>');
        $features_contents = substr($features_contents,0,$features_end);
        $features_contents = str_replace('bucket normal">','',$features_contents);

        //描述特征
        $description_contents = $contents;
        $description_start= strpos($description_contents, 'id="productDescription"');
        $description_contents = substr($description_contents,$description_start);
        $description_end = strpos($description_contents, '<div class="emptyClear">');
        $description_contents = substr($description_contents,0,$description_end);
        $description_contents = str_replace('id="productDescription">','',$description_contents);

        $all_html = $title_contents.$features_contents.$description_contents;
    }

    echo $all_html;
}

/*
 * create by august
 * on 2012-08-10
 * 标签设置
*/
//标签设置列表
elseif($detail == 'userlabel'){

    $InitPHP_conf['pageval'] = 15;
    $bannerstr = '<button onclick=window.location="index.php?action=product_list&detail=new_userlabel">添加标签</button>';
    $user_label = $this->S->dao('user_label');
    $datalist = $user_label->getUserLabelList();

    $displayarr = array();
	$tablewidth = '660';

    $displayarr['label_name'] = array('showname'=>'标签名称','width'=>'300');
    $displayarr['create_user'] = array('showname'=>'创建者','width'=>'300');
    $displayarr['both'] = array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=product_list&detail=delete_userlabel&id={id}','url_e'=>'index.php?action=product_list&detail=edit_userlabel&id={id}');

    $temp = 'pub_list';
    $this->V->mark(array(title=>'标签列表-产品列表(list)'));
}

elseif ($detail == 'new_userlabel' || $detail == 'edit_userlabel')
{
    if($detail == 'new_userlabel'){
		$this->V->view['title'] = '添加标签-标签列表(userlabel)-产品列表(list)';
		$jump = 'index.php?action=product_list&detail=save_userlabel&method=insert';

	}elseif($detail == 'edit_userlabel'){
		if(empty($id))exit('没有ID!');
		$user_label = $this->S->dao('user_label');
		$data = $user_label->D->select('label_name,id','id='.$id);
		$this->V->view['title'] = '编辑标签-标签列表(userlabel)-产品列表(list)';
		$jump = 'index.php?action=product_list&detail=save_userlabel&method=update';
	}

    /*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'400');
	$colwidth = array('1'=>'100','2'=>'300');

    $disinputarr = array();
    $disinputarr['id']      = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['label_name'] = array('showname'=>'标签名称','value'=>$data['label_name']);

    $temp = 'pub_edit';
}

elseif($detail == 'save_userlabel'){
    $create_user = $_SESSION['eng_name'];
    $user_label = $this->S->dao('user_label');
    if($method == 'insert'){

        $sid = $user_label->D->insert(array('label_name'=>$label_name,'create_user'=>$create_user));
	    if($sid) $this->C->success('添加成功','index.php?action=product_list&detail=userlabel');
    } elseif($method == 'update') {

        $sid = $user_label->D->update_by_field(array('id'=>$id),array('label_name'=>$label_name));
	    if($sid) $this->C->success('修改成功','index.php?action=product_list&detail=userlabel');
    }
}

elseif($detail == 'delete_userlabel'){
    $user_label = $this->S->dao('user_label');
    $sid = $user_label->D->delete_by_field(array('id'=>$id));
	if($sid){echo "删除成功";}
}

//保存标签
elseif($detail == 'save_label'){
    if(empty($pid) || empty($lid)) {
		echo '保存失败';
		exit();
	} else {
        $row = $this->S->dao('product_label')->D->select('id', "product_id='{$pid}' AND create_user='{$_SESSION['eng_name']}'");
        if ($row) {
        	$result = $this->S->dao('product_label')->D->update_by_field(array('id'=>$row['id']),array('product_id'=>$pid,'label_id'=>$lid,'create_user'=>$_SESSION['eng_name']));
        } else {
            $result = $this->S->dao('product_label')->D->insert(array('product_id'=>$pid,'label_id'=>$lid,'create_user'=>$_SESSION['eng_name']));
        }

    	if($result) {
    		echo '保存成功';
    	}else{
    		echo '保存失败';
    	}
    }
}

//用户自定义标签
elseif($detail == 'user_change_label'){
    $user_label = $this->S->dao('user_label');
    $datalist = $user_label->getUserLabelList();

    echo json_encode($datalist);
}

//保存用户自定义标签
elseif($detail == 'save_user_change_label'){
    $product_id = substr($pid,0,strlen($pid)-1);
    $num = strpos($product_id,',');
    if(!empty($num)){
        $product_arr = explode(',',$product_id);
        foreach($product_arr as $val){
            $row = $this->S->dao('product_label')->D->select('id', "product_id='{$val}' AND create_user='{$_SESSION['eng_name']}'");
            if ($row) {
            	$result = $this->S->dao('product_label')->D->update_by_field(array('id'=>$row['id']),array('product_id'=>$val,'label_id'=>$label_id,'create_user'=>$_SESSION['eng_name']));
            } else {
                $result = $this->S->dao('product_label')->D->insert(array('product_id'=>$val,'label_id'=>$label_id,'create_user'=>$_SESSION['eng_name']));
            }
        }
    }else{
        $row = $this->S->dao('product_label')->D->select('id', "product_id='{$product_id}' AND create_user='{$_SESSION['eng_name']}'");
        if ($row) {
        	$result = $this->S->dao('product_label')->D->update_by_field(array('id'=>$row['id']),array('product_id'=>$product_id,'label_id'=>$label_id,'create_user'=>$_SESSION['eng_name']));
        } else {
            $result = $this->S->dao('product_label')->D->insert(array('product_id'=>$product_id,'label_id'=>$label_id,'create_user'=>$_SESSION['eng_name']));
        }
    }
    if($result){
        echo "1";
    }else{
        echo "0";
    }
}

/**
 * @title 图片批量处理
 * @author Jerry
 * @create on 2013-1-3
 */

elseif ($detail == 'downloadpic') {
    //echo '<pre>';
    //print_r($checkall);die();
    /*如果图片被选中，进行图片批量操作*/

    $checkall = explode(',',$str);
    if (!$checkall)
        $this->C->sendmsg('请选择需要下载的图片'); 
        $aryimg_path = array();
        foreach($checkall as $h_imgs){
            //$aryimg_path[] = str_replace('../','',substr($h_imgs,strrpos($h_imgs,'../')));//获取原图片的路径 
            $aryimg_path[] = substr($h_imgs,strrpos($h_imgs,'data'));//获取原图片的路径  兼容老的和新的 
        } 
        $jump = 'index.php?action=product_list&detail=editimg&imgpaths='.implode(',',$aryimg_path).'&type='.$type;
        $this->C->redirect($jump);exit; 
}

/**
 * @title 产品列表--图片处理
 * @author Jerry
 * @create on 2012-12-05
 */
elseif ($detail == 'editimg') {

    /*表单配置*/
    $jump = 'index.php?action=product_list&detail=editimgmode';
	$conform = Array('method'=>'post','action'=>$jump,'submit'=>'<input  type="submit"  value="下 载"  id="subinput" >');
	$colwidth = Array('1'=>'100','2'=>'','3'=>'80');

    $disinputarr = array();
    if($imgpaths){
        $imgpath = explode(',',$imgpaths);
    if ($imgpath){
        foreach($imgpath as $imgs){
            $imgstr.= ' <img src="'.$imgs.'" alt="" title="点击右键另存为可下载原图" style="width:115px;height:115px;cursor:pointer"/>';
        }
    }
    }
    
    $disinputarr['imgs']        = array('showname'=>'缩略图','datatype'=>'se','datastr'=>$imgstr);
    $disinputarr['himg']        = array('showname'=>'传值用','datatype'=>'h','value'=>$imgpaths);
    $disinputarr['listingtype']        = array('showname'=>'传值用','datatype'=>'h','value'=>$type);
    //$disinputarr['h_imgs']      = array('showname'=>'图片地址','datatype'=>'h','value'=>$imgs);
    if(empty($type)){
        $datastr     = '<select name="water_mark"><option value="">请选择</option><option value="miucolor">miucolor</option><option value="loftek">loftek</option></select>';
        $checkboxstr = '1600*1600<input type=checkbox  title="1600*1600" name=water_size[] style="width:16px;height:16px;" value=1600 />&nbsp;&nbsp;800*800<input style="width:16px;height:16px;" title="800*800" type=checkbox name=water_size[] value=800 />';
        $disinputarr['img_info']    = array('showname'=>'大小','datatype'=>'se','datastr'=>$checkboxstr);
        $disinputarr['water_mark']  = array('showname'=>'水印类别','datatype'=>'se','width'=>'100','datastr'=>$datastr); 
    }
    
    $this->V->mark(array('title'=>'图片下载-'.$sku));
    $temp ='pub_edit';
}

/**
 * @title 产品列表--图片逻辑处理
 * @author Jerry
 * @create on 2012-12-05
 */
elseif ($detail == 'editimgmode') {

    if (empty($himg)) {
        $this->C->sendmsg('请确认需要下载的图片是否存在！');
    }

    if (empty($water_size) and empty($listingtype)) {
        $this->C->sendmsg('请确认，你选择生成图片的大小！');
     }
     
    $error_id = 0;
    $imageInit  = $this->getLibrary('image');//加载图像类
    $zip        = $this->getLibrary('zip');//加载压缩zip格式文件
    $position   = 2;//设置水印位置

     //水印类别
    if ($water_mark == 'miucolor')
        $water_path = 'data/images/water/miucolor.png';
    elseif($water_mark == 'loftek')
        $water_path = 'data/images/water/loftek.png';

    //listing 图片的类型
    $listingtypename = $listingtype;
    $listingtype = empty($listingtype)?'':$listingtype.'/';
    
    if($himg){
        $himgs = explode(',',$himg);
        foreach($himgs as $source_path){

            if(!file_exists($source_path)){$this->C->sendmsg('该图片不存在！');}

            $doc_paths  = dirname($source_path);
            $picname    = substr(strrchr($source_path,'/'),1);//完整文件名字
            $pic_name   = substr($picname,0,strrpos($picname,'.'));//文件名字
            
            //水印大小
            if (is_array($water_size)) {
                foreach($water_size as $val){

                    //生成新的缩略图地址
                    $newname = 'temp/tmp_'.$_SESSION['eng_name'].'/'.$pic_name.'_'.$val;
                    //需要生成水印的图片
                    $sour   = 'temp/tmp_'.$_SESSION['eng_name'].'/'.$pic_name.'_'.$val.'.jpg';
                    //生成缩略
                    $newimgs    = $imageInit->make_thumb($source_path,$newname,$val,$val,$isauto = true);
                    
                    if (count($newimgs) != 2)$error_id++;

                    //图片压缩800大小，超过300k限制
                    if ($val == '800' && filesize($sour)/1024 > 300){
                        $newimgs    = $imageInit->make_thumb($sour,$newname,$val,$val,$isauto = true);
                        if (count($newimgs) != 2)$error_id++;
                    }

                    //生成水印
                    if($water_path){
                        $water_pic  = $imageInit->water_mark($sour, $water_path, $position, $pct=100, $quality=100);
                        if (!$water_pic)$error_id++;
                    }
                }
            }else{
                if($listingtype){
                    //copy新图地址
                    $newname = 'temp/tmp_'.$_SESSION['eng_name'].'/'.$listingtype.$listingtypename.'_'.$pic_name;
                    //copy图片
                    if(!$imageInit->copyimg($source_path,$newname)){$error_id++;}
               }
            } 
        }
        
        if ($error_id >0)
                $this->C->success("图片处理操作失败！",'index.php?action=product_list&detail=editimg&imgs='.$source_path);

        //水印文件是否存在
        if($water_mark)
            $filedir = 'temp/tmp_'.$_SESSION['eng_name'].'/'.$listingtype.'tmp'.$_SESSION['eng_name'];
        else
            $filedir = 'temp/tmp_'.$_SESSION['eng_name'].'/'.$listingtype;

        //压缩文件的名字
        $tmp_zipname = $_SESSION['eng_name'].'_'.date('Ymd').'.zip';
        //需要下载文件
        $zipname = $filedir.$tmp_zipname; 

        $arfile = array();
        $dh = opendir($filedir);
        while(false != ($file = readdir($dh))){
            if ($file != '.' && $file != '..')
                $arfile[] = $filedir.'/'.$file;
        }
        closedir($dh);
        
        //压缩文件
        $newzip   = $zip->zip($zipname, $arfile);
        
        
        if (!$newzip){
            $this->success('压缩文件失败！','index.php?action=product_list&detail=editimg&imgs='.$source_path);
        }
        
        if(!file_exists($zipname)){
            echo 'zipname not fund';
            exit;
        }

        $file = fopen($zipname,'r');
        Header ( "Content-type: application/octet-stream" );
        Header ( "Accept-Ranges: bytes" );
        Header ( "Accept-Length: " . filesize ( $zipname ) );
        Header ( "Content-Disposition: attachment; filename=" .$tmp_zipname );
        //输出文件内容
        //读取文件内容并直接输出到浏览器
        echo fread ( $file, filesize ( $zipname ) );
        fclose ( $file );
        //删除临时文件目录
        $tempdir = './temp/tmp_'.$_SESSION['eng_name'];
        $zip->delDir($tempdir);
        exit ();
    }
}

//查看图片
elseif ($detail == 'showpiclist') {

    $checkall = explode(',',$str);
    if (!$checkall)
        $this->C->sendmsg('请选择需要查看的图片');

        $imgpath = array();
        foreach($checkall as $h_imgs){
            $imgpath[] = str_replace('../','',substr($h_imgs,strrpos($h_imgs,'../')));//获取原图片的路径
        }

    $pic = str_replace('../','',substr($pic,strrpos($pic,'../')));
    if (!$pic)
        $this->C->sendmsg('预览图不存在');
    $picnum = array_keys($imgpath,$pic);
    if (empty($picnum))
        $this->C->sendmsg('请查看被选中的图片！');

    $this->V->view['pic']    = $pic;//获取选中的图片
    $this->V->view['num']    = $picnum[0];//确定图片的位置
    $this->V->view['arypic'] = $imgpath; //获取所有图片
    $this->V->view['title']  = '查看图片-';
    $this->V->set_tpl('adminweb/showpic');
    display();

}


//模板定义
if($detail == 'new_userlabel' || $detail == 'edit_userlabel' || $detail == 'userlabel' || $detail == 'editimg' || $detail == 'showpiclist')
{
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}

?>
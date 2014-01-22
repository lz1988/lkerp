<?php

if($detail == 'selectcategory'){
    if(!$this->C->service('admin_access')->checkResRight('r_p_add')){$this->C->sendmsg();}

	$category 			= $this->S->dao('category');
	$category_rootlist 	= $category->categorylist();

	if($step >= 2){
		$childlist = $category->category_childrenlist($rcat_id);

		$this->V->view['step'] 			= $step;
		$this->V->view['childlist'] 	= $childlist;
		$this->V->view['rcat_id'] 		= $rcat_id;
		$this->V->view['scat_id'] 		= $scat_id;
		$this->V->view['rcat_name'] 	= $rcat_name;
		if($scat_name){$this->V->view['scat_name'] = '--'.$scat_name;}
	}

	$this->V->view['title'] 			= ($trh == 'list')?'<a href="index.php?action=product_list&detail=list">产品列表</a> &raquo; 选择类别' : '选择类别';
	$this->V->view['category_rootlist'] = $category_rootlist;
	$this->V->view['trh'] 				= $trh;

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/productselectcategory');

	$this->V->display();
}
elseif($detail == 'new'){

    if(!$this->C->service('admin_access')->checkResRight('r_p_add')){$this->C->sendmsg();}
	/*类别ID获取*/
	if($scat_id){$cat_id = $scat_id;}elseif($rcat_id){$cat_id = $rcat_id;}
	$this->V->view['showcat'] = $showcat;
	$this->V->view['cat_id'] = $cat_id;

	if($trh == 'list'){
		$this->V->view['title'] = '<a href="index.php?action=product_list&detail=list">产品列表</a> &raquo; <a href="index.php?action=product_new&detail=selectcategory&trh='.$trh.'">选择类别</a>  &raquo; 填写产品信息';
	}else{
		$this->V->view['title'] = '<a href="index.php?action=product_new&detail=selectcategory">选择类别</a>  &raquo; 填写产品信息';
	}

	/*判断权限*/
	if($this->C->service('admin_access')->checkResRight('r_t_suplimod')){
		$supplier_str = '<div style="float:left;width:210px"><input type="text"  name="supplier" id="supplier" /></div><div style="float:left; margin-left:50px;width:380px; height:100px;overflow:auto; font-size:12px"><ul id="supplierselected"></ul></div>';
		$this->V->view['supplier_str'] = $supplier_str;
		$this->V->view['supplier_mod'] = '*';
	}else{
		$this->V->view['supplier_str'] = '<font color="#b8c4c6" size=2>无权查看</font>';
	}

	/*扫描质检图片*/
	$qualityhtml = $this->C->service('product')->get_qualitycheck();

	$this->V->mark(array('trh'=>$trh,'qualityhtml'=>$qualityhtml));
	$this->V->view['trh']			   = $trh;
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/productnew');
	$this->V->display();
}

/*检查SKU与产品名称是否重名*/
elseif($detail == 'checkpro'){

		$product = $this->S->dao('product');

		/*检测产品SKU与名称是否重复*/
		$checkbysku = $product->D->select('pid',"sku='{$sku}'");
		$checkbyname = $product->D->select('pid',"product_name='{$product_name}'");

		if($checkbysku['pid']&&$sku!=''){
			echo '1';
		}elseif($checkbyname['pid']&&$product_name!=''){
			echo '2';
		}else{
			echo '3';
		}

}

/*保存*/
elseif($detail == 'newmod'){
        if(!$this->C->service('admin_access')->checkResRight('r_p_add')){$this->C->sendmsg();}
		$product = $this->S->dao('product');
        $prc_esse = $this->S->dao('prc_esse');

		/*开始更新,对产品图片信息整合*/
		if($img_url){
				$i = 0;
				foreach($img_type as $t){
				$images[] = array('type' => $t, 'url'=> $img_url[$i], 'desc' => $img_desc[$i],'dl_url'=>$dl_file_url[$i]);
				$i++;
				}
				if (get_magic_quotes_gpc()){ $images= addslashes(json_encode($images));}else{$images = json_encode($images);}
		}


			$key_product_features = json_encode($key_product_features);
			$platinum_keywords = json_encode($platinum_keywords);
			$related_keywords = json_encode($related_keywords);

			date_default_timezone_set('Asia/Hong_Kong');
			$cdate = date("Y-m-d H:i:s");
            
			$supplier_id = $_POST['supplier_id'];
            
             //切割product->供应商
            if (is_array($supplier_id))
            {
                foreach($supplier_id as $val){
                    $d['pid'] = $pid;
                    $d['eid'] = $val;
                    if (!$prc_esse->D->insert($d)){$errorstr .= "供应商信息修改失败\n"; $sqlerror = 1;}
                }
            }
            
			foreach($supplier_id as $suid){
				$supplier_id_str .= '@'.$suid.',';//特殊处理选择的supplier_id
			}

			/*插入product表*/
			$pid = $product->D->insert(array('cat_id'=>$cat_id,'sku'=>$sku,'product_name'=>$product_name,'manufacturer'=>$supplier_id_str,'brand_name'=>$brand_name,'model_number'=>$model_number,'manufacturer_part_number'=>$manufacturer_part_number,'upc_or_ean'=>$upc_or_ean,'conditionerp'=>$conditionerp,'key_product_features'=>$key_product_features,'product_desc'=>$product_desc,'product_desc2'=>$product_desc2,'platinum_keywords'=>$platinum_keywords,'related_keywords'=>$related_keywords,'target_customers'=>$target_customers,'product_dimensions'=>$product_dimensions,'shipping_weight'=>$shipping_weight,'box_product_dimensions'=>$box_product_dimensions,'box_shipping_weight'=>$box_shipping_weight,'style_name'=>$style_name,'color'=>$color,'size'=>$size,'qualitycheck'=>$qualitycheck,'box_type'=>$box_type,'unit_box'=>$unit_box,'cdate'=>$cdate,'create_user'=>$_SESSION['eng_name'],'product_weight'=>$product_weight,'product_size'=>$product_size));
            
             //切割product->供应商
            if (is_array($supplier_id) && $pid)
            {
                foreach($supplier_id as $val){
                    $d['pid'] = $pid;
                    $d['eid'] = $val;
                    if (!$prc_esse->D->insert($d)){$errorstr .= "供应商信息修改失败\n"; $sqlerror = 1;}
                }
            }
            
			/*插入product_images表*/
			if($images){
				$product_images = $this->S->dao('product_images');
				$product_images->D->insert(array('pid'=>$pid,'images'=>$images,'cdate'=>$cdate));
			}

			/*插入product_cost*/
           // var_dump($pid);die();
			$this->S->dao('product_cost')->D->insert(array('pid'=>$pid,'cost1'=>$cost1,'cost2'=>$cost2,'cost3'=>$cost3,'cdate'=>$cdate,'coin_code'=>$coin_code));

			if($pid){
				if($trh == 'list'){
					echo "<script>window.location='index.php?action=product_list&detail=list'</script>";
				}else{
					echo "<script>alert('添加成功！');window.location='index.php?action=product_new&detail=selectcategory'</script>";
				}
			}

}

elseif($detail == 'changeprice'){
	$costp 	= $this->C->service('exchange_rate')->change_rate($source,$tobe,$costp);
	$cost2 	= $this->C->service('exchange_rate')->change_rate($source,$tobe,$cost2);
	$cost3 	= $this->C->service('exchange_rate')->change_rate($source,$tobe,$cost3);
	$costpre= $this->C->service('exchange_rate')->change_rate($source,$tobe,$costpre);

	$costboth = array('costp'=>$costp,'cost2'=>$cost2,'cost3'=>$cost3,'costpre'=>$costpre);
	echo json_encode($costboth);
}

/*
 * create by wall
 * on 2012-08-10
 * 检查SKU是否重名*/
elseif($detail == 'checksku'){
	$product = $this->S->dao('product');
	/*检测产品SKU是否重复*/
	$checkbysku = $product->D->select('pid',"sku='{$sku}'");
	if($checkbysku['pid']&&$sku!=''){
		echo '1';
	} else {
		echo '0';
	}
}
?>

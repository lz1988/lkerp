<?php
/**
* Create on 2013-03-05
* by Hanson
* @title ...
*/
$getfrom    = 'f';

/*运费管理数据列表*/
if($detail == 'list'){

	$datalist 	= $this->S->dao('shipping_freight')->get_datalist($sqlstr);

	/*数据处理*/
	foreach($datalist as &$val){

		/*递送时间处理*/
		if($val['sendtime']){
			$sendtimeArr		= explode(',',$val['sendtime']);
			$val['sendtime']	= ($sendtimeArr['0']?$sendtimeArr['0'].'d':'').'~'.($sendtimeArr['1']?$sendtimeArr['1'].'d':'');
		}

		$val['basis_height']	= ($val['basis_height'] == '1')?'体积重、实重':'实重';

		/*若是公式类型则直接显示公式，否则显示点击查看，进入下一级查看详细*/
		$val['basicfare']		= $val['expression']?$val['expression']:'<a href="index.php?action=shipping_countnew&detail=showfare&shipping_id='.$val['shipping_id'].'&from='.$val['from'].'">点击查看&raquo;</a>';

		/*若未选地区则显示任意地*/
		$val['from']			= empty($val['from'])?'任意地':($val['nation'].($val['province']?'&nbsp;|&nbsp;'.$val['province']:'').($val['city']?'&nbsp;|&nbsp;'.$val['city']:''));
		$val['to']				= empty($val['to'])?'任意地':($val['nation2'].($val['province2']?'&nbsp;|&nbsp;'.$val['province2']:'').($val['city2']?'&nbsp;|&nbsp;'.$val['city2']:''));

		$val['fuel_surcharge']	= $val['fuel_surcharge'].'%';
	}

	$displayarr = array();
	$tablewidth = '1500';
	$displayarr['s_name'] 			= array('showname'=>'物流方式','width'=>'100');
	$displayarr['from'] 			= array('showname'=>'发送地','width'=>'100');
	$displayarr['to'] 				= array('showname'=>'目的地','width'=>'100');
	$displayarr['basicfare']		= array('showname'=>'基本费用','width'=>'100');
	$displayarr['fuel_surcharge'] 	= array('showname'=>'燃油附加费','width'=>'100');
	$displayarr['operating_cost'] 	= array('showname'=>'操作费','width'=>'70');
	$displayarr['registered_fee'] 	= array('showname'=>'挂号费','width'=>'70');
	$displayarr['sendtime'] 		= array('showname'=>'递用时间','width'=>'100');
	$displayarr['tips'] 			= array('showname'=>'可否跟踪','width'=>'80');
	$displayarr['basis_height'] 	= array('showname'=>'按体积重/实重计算','width'=>'120');
	$displayarr['unit'] 			= array('showname'=>'计量单位','width'=>'100');
	$displayarr['coin_code'] 		= array('showname'=>'币别','width'=>'60');
	$displayarr['des'] 				= array('showname'=>'描述','width'=>'100');
	$displayarr['fstcreate'] 		= array('showname'=>'最后更新','width'=>'100');
	$displayarr['both'] 			= array('showname'=>'操作','width'=>'60','url_e'=>'index.php?action=shipping_countnew&detail=edit&id={id}','url_d'=>'index.php?action=shipping_countnew&detail=dele&id={id}','ajax'=>'1');
	$bannerstr = '<button onclick="window.location=\'index.php?action=shipping_show&detail=list\'" >返回查询</button>';
	$bannerstr.= '<button onclick="window.location=\'index.php?action=shipping_countnew&detail=add\'" >添加运费</button>';
	$this->V->mark(array('title'=>'运费管理'));
	$temp = 'pub_list';
}


/*导入运费数据*/
elseif($detail == 'import'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('shipping_faremod')){$this->C->sendmsg();}

	$objshipencode		= $this->S->dao('shipping_encode');
	$backdatacheck		= $objshipencode->D->get_one(array('shipping_id'=>$shipping_id),'id');

	/*检测是否已录代码，否则提醒返回*/
	if(!$backdatacheck){
		$bannerstr	= '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:450px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
		$bannerstr .= '请返回上一级先录入“地址与代码映射”再导入运费表格。</div>';
		$temp		= 'pub_list';
	}

	/*导入运费表格*/
	else{

		$exl_error_msg	= '运费导入过程中，系统先自动清除该物流方式已有运费数据，然后更新为导入的表格数据！';
		if($_FILES["upload_file"]["name"] || $filepath){

			$upload_exl_service = $this->C->Service('upload_excel');
			$upload_dir 		= "./data/uploadexl/temp/";//上传文件保存路径的目录

			/*有效的excel列表值，前三列ABC，后面根据录入的代码来扩展*/
			$fieldarray			= array('A','B','C');
			$headarray			= array('ctype','min_weight','max_weight');//用于表头检测
			$fieldend			= 'D';
			$groupcode			= array();
			$groupcodeback		= $objshipencode->D->get_allstr(' and shipping_id='.$shipping_id,'code','code','code');
			for($i = 0; $i<count($groupcodeback); $i++){
				$fieldarray[]	= $fieldend;//用于取值列
				$headarray[]	= $groupcodeback[$i]['code'];//用于判断表头
				$fieldend++;
			}

			/*保存运费数据*/
			if($filepath){

				$all_arr		= $upload_exl_service->get_excel_datas_withkey($filepath, $fieldarray, 1);
				$shipping_cost	= $this->S->dao('shipping_cost');
				$errorcount		= 0;
				unset($all_arr['0']);//删除表头
				unlink($filepath);//删除文件
				$shipping_cost->D->delete(array('shipping_id'=>$shipping_id,'from'=>$from));//先清空已有运费。


				/*对自定义代码循环，更新运费*/
				foreach($groupcodeback as $vcode){

					$code		= $vcode['code'];
					$backcodeid = $objshipencode->D->get_allstr(' and shipping_id='.$shipping_id.' and code="'.$code.'"','','','area_id');

					/*对代码的地址ID循环*/
					foreach($backcodeid as $vid){

						/*对阶梯运费循环*/
						foreach($all_arr as $vfare){
							$insertArr = array('shipping_id'=>$shipping_id,'from'=>$from,'to'=>$vid['area_id'],'ctype'=>$vfare['ctype'],'cost'=>$vfare[$code],'min_weight'=>$vfare['min_weight'],'max_weight'=>$vfare['max_weight']);
							$sid = $shipping_cost->D->insert($insertArr);
							if(!$sid) $errorcount++;
						}
					}
				}

				if(empty($errorcount)){
					$this->C->success('导入成功',"index.php?action=shipping_countnew&detail=showfare&from=$from&shipping_id=$shipping_id");
				}else{
					$this->C->success('导入失败，请重试',"index.php?action=shipping_countnew&detail=import&from=$from&shipping_id=$shipping_id");
				}
			}

			/*导入并显示预览*/
			else{

				$data_error 	= 0;
				$tablelist 		= '<table id="mytable">';
				$all_arr		= $upload_exl_service->get_upload_excel_datas($upload_dir, $fieldarray, 1);//上传并获取数据
				$filepath		= $_SESSION['filepath'];

				/*表头检测，若有错，显示表头*/
				$tablelist	   .= $upload_exl_service->checkmod_head(&$all_arr,&$data_error,$headarray);
				foreach($all_arr as $k=>$val){
					$tablelist .= '<tr>';
					foreach($val as $j=>$value) {
						$error_style = '';

						/*检测ctype类型*/
						if($j == 'ctype' && $value!='total_price' && $value!='per_price'){
							$error_style = ' bgcolor="red" title="类型错误，只能是total_price或者per_price"';
							$data_error++;
						}

						/*检测重量数字*/
						if($j == 'min_weight' || $j == 'max_weight'){
							if(!preg_match('/^[\d]+(\.?[\d]{1,2})?$/',$value)){
								$error_style = ' bgcolor="red" title="只允许整数或小数，最多两位小数！"';
								$data_error++;
							}
						}

						/*判断自定义表头*/
						for($i = 0; $i<count($groupcodeback); $i++){
							if($j == $groupcodeback[$i]['code']){
								if(!preg_match('/^[\d]+(\.?[\d]{1,2})?$/',$value)){
									$error_style = ' bgcolor="red" title="只允许整数或小数，最多两位小数！"';
									$data_error++;
								}
							}
						}

						$tablelist  .= '<td '.$error_style.' >&nbsp;'.$value.'</td>';
					}
					$tablelist .= '</tr>';
				}
				$tablelist	   .= '</table>';

				if(!$data_error && isset($all_arr)){
					$tablelist .= '<input type="hidden" name="filepath" value="'.$filepath.'" />';
					$tablelist .= '<input type="hidden" name="shipping_id" value="'.$shipping_id.'" />';
					$tablelist .= '<input type="hidden" name="from" value="'.$from.'" />';
					$tablelist .= '<input type="submit" value="确认并提交">';
				}elseif($data_error){
					$exl_error_msg= '总共有 <b>'.$data_error.'</b> 处错误，请将鼠标移到红色处查看错误提示，修正后重新上传。';
					unlink($filepath);//有错的文件删除掉
				}

			}
		}
		$this->V->set_tpl('adminweb/commom_excel_import');
		$this->V->mark(array('tablelist'=>$tablelist,'exl_error_msg'=>$exl_error_msg,'exl_error_width'=>'600'));
		display();
	}

	$this->V->mark(array('title'=>'<a href="index.php?action=shipping_countnew&amp;detail=list">运费管理</a> » <a href="index.php?action=shipping_countnew&amp;detail=showfare&amp;shipping_id='.$shipping_id.'&amp;from='.$from.'">查看运费数据</a> » 导入运费'));

}


/*查看运费表格数据*/
elseif($detail == 'showfare'){

	$codeArr			= array();
	$objshipcost		= $this->S->dao('shipping_cost');
	$objshipencode		= $this->S->dao('shipping_encode');

	/*查该发货方式有多少代码，转换成一维数组*/
	$codetoArr			= $objshipencode->D->get_allstr(' and shipping_id='.$shipping_id.' ','code','code','code');
	foreach($codetoArr as $val){
		$codeArr[] = $val['code'];
	}

	/*检测是否有录数据，给予提示*/
	$backdata		= $objshipcost->D->get_one(array('from'=>$from,'shipping_id'=>$shipping_id),'id');
	if(!$backdata){
		$bannerstr	= '<div style="background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:450px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;">';
		$bannerstr .= '该物流尚未录入运费数据；导入数据前，先录入地址与代码映射。</div>';
	}else{
		$datalist	= $objshipcost->showcosttable($codeArr, $shipping_id);
	}

	$bannerstr.= '<button onclick=window.location="index.php?action=shipping_countnew&detail=encodeindex&shipping_id='.$shipping_id.'&from='.$from.'">映射管理</button>';
	$bannerstr.= '<button onclick=window.location="index.php?action=shipping_countnew&detail=import&shipping_id='.$shipping_id.'&from='.$from.'">导入运费</button>';

	$displayarr 					= array();
	$displayarr['ctype'] 			= array('showname'=>'ctype','width'=>'80','title'=>'费用类型，total_price总费用，per_price单位费用');
	$displayarr['min_weight'] 		= array('showname'=>'min_weight','width'=>'100','title'=>'重量区间-左');
	$displayarr['max_weight'] 		= array('showname'=>'max_weight','width'=>'100','title'=>'重量区间-右');

	/*对代码进行还原输出*/
	for($i =0; $i < count($codeArr); $i++){

		/*代码title显示这些代码的国家*/
		$backdata					= $objshipencode->getAreabyCode($codeArr[$i], $shipping_id);
		$titlestr					= '';
		foreach($backdata as $val){
			$titlestr .= ($val['city']?$val['city']:($val['province']?$val['province']:$val['nation'])).',';
		}
		$displayarr[$codeArr[$i]]	= array('showname'=>$codeArr[$i],'width'=>'60','title'=>$titlestr);
	}


	$this->V->mark(array('title'=>'查看运费数据-运费管理(list)'));
	$temp = 'pub_list';

}

/*运费管理新增或编辑页面*/
elseif($detail == 'add' || $detail == 'edit'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('shipping_faremod')){$this->C->sendmsg();}
	if($detail == 'edit'){
		if(empty($id))exit('缺少标识参数！');
		$backdata	= $this->S->dao('shipping_freight')->D->get_one(array('id'=>$id),'*');
		$sendtimeAr = explode(',',$backdata['sendtime']);
		$showtitle  = '编辑物流运费';
		$modurl		= 'modedit';
	}elseif($detail == 'add'){
		$showtitle  = '添加物流运费';
		$modurl		= 'modadd';
	}

	/*发货方式下拉*/
	$shippingstr	= $this->C->service('global')->get_shipping('shipping_id class="check_notnull" ','s_name','id','id',$backdata['shipping_id']).' *';

	/*递送时间*/
	$sendtimestr	= '<input type="text" name=lsendtime style="width:40px" value='.$sendtimeAr[0].' >d~<input type="text"  style="width:40px" name=rsendtime  value='.$sendtimeAr[1].'>d';

	/*可跟踪*/
	$tipsstr		= '<input type="radio" name=tips value="否" style="width:15px;height15px" '.($backdata['tips']=='否'?'checked':($detail!='edit')?'checked':'').'>否 <input type="radio" name=tips value="是" '.($backdata['tips']=='是'?'checked':'').' style="width:15px;height15px">是';

	/*依据重量*/
	$basis_heightstr= '<input type="checkbox" style="width:15px;height15px" disabled checked>实重 <input type="checkbox" name="basis_height" value="1" style="width:15px;height15px" '.($backdata['basis_height']?"checked":"").'>体积重';

	/*计量单位*/
	$unitstr		= '<input type="radio" name=unit value="kg" style="width:15px;height15px" '.($backdata['unit']=='kg'?'checked':($detail!='edit')?'checked':'').'>kg <input type="radio" name=unit value="g" '.($backdata['unit']=='g'?'checked':'').' style="width:15px;height15px" >g';

	/*币别*/
	$coin_codestr	= $this->C->service('warehouse')->get_coincode_html('coin_code',' class="check_notnull"',$backdata['coin_code']).' *';

	/*表单配置*/
	$conform		= array('method'=>'post','action'=>'index.php?action=shipping_countnew&detail='.$modurl,'width'=>'1000');
	$colwidth		= array('1'=>'100','2'=>'450','3'=>'450');

	/*发送地与目的地*/
	$fromstr		= $this->C->service('shipping_fare')->getArea($backdata['from'],'from');
	$tostr			= $this->C->service('shipping_fare')->getArea($backdata['to'],'to');

	$disinputarr = array();
	$disinputarr['id'] 				= array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['shipping_id']		= array('showname'=>'物流方式','datatype'=>'se','datastr'=>$shippingstr);
	$disinputarr['froms']			= array('showname'=>'发送地','datatype'=>'se','datastr'=>$fromstr);
	$disinputarr['tos']				= array('showname'=>'目的地','datatype'=>'se','datastr'=>$tostr);
	$disinputarr['expression']		= array('showname'=>'基本费用','value'=>$backdata['expression'],'showtips'=>'<span class=tips> 请输入公式，公式以“x”代表变量，若不是按公式计算则留空。</span>');
	$disinputarr['fuel_surcharge']	= array('showname'=>'燃油附加费','inextra'=>'class=check_isnum','value'=>$backdata['fuel_surcharge'],'extra'=>'%','showtips'=>'<span class=tips>百分之几，填数字。</span>');
	$disinputarr['operating_cost']	= array('showname'=>'操作费','inextra'=>'class=Check_isnum_dd2','value'=>$backdata['operating_cost'],'showtips'=>'<span class=tips>最多两位小数。</span>');
	$disinputarr['registered_fee']	= array('showname'=>'挂号费','inextra'=>'class=Check_isnum_dd2','value'=>$backdata['registered_fee'],'showtips'=>'<span class=tips>最多两位小数。</span>');
	$disinputarr['coin_code']		= array('showname'=>'币别','datatype'=>'se','datastr'=>$coin_codestr,'showtips'=>'<span class=tips>录进系统的运费数据或公式计算出结果的币别。</span>');
	$disinputarr['sendtime']		= array('showname'=>'递送时间','datatype'=>'se','datastr'=>$sendtimestr,'showtips'=>'<span class=tips>填写大概递送天数范围。</span>');
	$disinputarr['basis_height']	= array('showname'=>'依据重量','datatype'=>'se','datastr'=>$basis_heightstr,'showtips'=>'<span class=tips>若勾选体积重则优先以体积重为计算依据，否则只按实重。</span>');
	$disinputarr['unit']			= array('showname'=>'计量单位','datatype'=>'se','datastr'=>$unitstr,);
	$disinputarr['tips']			= array('showname'=>'可否跟踪','datatype'=>'se','datastr'=>$tipsstr);
	$disinputarr['des']				= array('showname'=>'描述','value'=>$backdata['des']);
	$disinputarr['from']			= array('showname'=>'隐藏发送地','value'=>$backdata['from'],'datatype'=>'h');
	$disinputarr['to']				= array('showname'=>'隐藏目的地','value'=>$backdata['to'],'datatype'=>'h');


	$this->V->view['title'] = $showtitle.'-运费管理(list)';
	$jslink 				= "<script src='./staticment/js/freight.js?v=1'></script>\n";
	$temp					= 'pub_edit';

}

/*保存添加运费管理*/
elseif($detail == 'modadd'){

	$sid = $this->S->dao('shipping_freight')->D->insert(array('shipping_id'=>$shipping_id,'from'=>$from,'to'=>$to,'expression'=>$expression,'fuel_surcharge'=>$fuel_surcharge,'operating_cost'=>$operating_cost,'registered_fee'=>$registered_fee,'sendtime'=>$lsendtime.','.$rsendtime,'tips'=>$tips,'basis_height'=>$basis_height,'unit'=>$unit,'coin_code'=>$coin_code,'des'=>$des));
	if($sid){
 		$this->C->success('保存成功','index.php?action=shipping_countnew&detail=list');
	}else{
		$this->C->success('保存失败','index.php?action=shipping_countnew&detail=add');
	}
}

/*保存编辑运费管理*/
elseif($detail == 'modedit'){
	$sid = $this->S->dao('shipping_freight')->D->update(array('id'=>$id),array('shipping_id'=>$shipping_id,'from'=>$from,'to'=>$to,'expression'=>$expression,'fuel_surcharge'=>$fuel_surcharge,'operating_cost'=>$operating_cost,'registered_fee'=>$registered_fee,'sendtime'=>$lsendtime.','.$rsendtime,'tips'=>$tips,'basis_height'=>$basis_height,'unit'=>$unit,'coin_code'=>$coin_code,'des'=>$des));
	if($sid){
 		$this->C->success('保存成功','index.php?action=shipping_countnew&detail=list');
	}else{
		$this->C->success('保存失败','index.php?action=shipping_countnew&detail=edit&id='.$id);
	}
}

/*AJAX删除运费管理*/
elseif($detail == 'dele'){

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('shipping_faremod')){$this->C->ajaxmsg(0);}

	if($this->S->dao('shipping_freight')->D->delete(array('id'=>$id))) {$this->C->ajaxmsg(1);}else{$this->C->ajaxmsg('删除失败！');}
}


/*地区与编码列表管理*/
elseif ($detail == 'encodeindex') {

    $stypemu = array(
        'nation-s-l'        =>'地址：',
        'code-s-l'          =>'代码：',
        'shipping_id-h-e'   =>'物流方式',//
        'from-h-r'   		=>'发送地',//用于级联返回时，不参与生成SQL
    );

    $sqlstr  					= str_replace('nation like "%'.$nation.'%"','(nation like "%'.$nation.'%" or province like "%'.$nation.'%" or city like "%'.$nation.'%")',$sqlstr);
    $InitPHP_conf['pageval']    = 20;
    $shipping_encode    		= $this->S->dao('shipping_encode');
    $sqlstr 				   .= ' order by id desc';
    $datalist          			= $shipping_encode->getchipping_encode($sqlstr);

    for($i = 0;$i<count($datalist); $i++){
        $datalist[$i]['nation'] = $datalist[$i]['nation'].($datalist[$i]['province']?'&nbsp;|&nbsp;'.$datalist[$i]['province']:'').($datalist[$i]['city']?'&nbsp;|&nbsp;'.$datalist[$i]['city']:'');
    }

    $tablewidth = '850';
    $displayarr['both'] 	= array('showname'=>'操作','width'=>'60','ajax'=>'1','url_e'=>'index.php?action=shipping_countnew&detail=editencode&shipping_id='.$shipping_id.'&id={id}&from='.$from,'url_d'=>'index.php?action=shipping_countnew&detail=deleteencode&shipping_id='.$shipping_id.'&id={id}');
    $displayarr['nation']   = array('showname'=>'国家|省份|城市','width'=>'60');
    $displayarr['code']  	= array('showname'=>'代码','width'=>'100');
    $displayarr['s_name']   = array('showname'=>'物流方式','width'=>'60');
    $bannerstr 				= '<button onclick=window.location="index.php?action=shipping_countnew&detail=addencode&shipping_id='.$shipping_id.'&from='.$from.'">新增映射</button>';

    $this->V->mark(array('title'=>'<a href="index.php?action=shipping_countnew&amp;detail=list">运费管理</a> » <a href="index.php?action=shipping_countnew&amp;detail=showfare&amp;shipping_id='.$shipping_id.'&amp;from='.$from.'">查看运费数据</a> » 映射管理'));
    $temp = 'pub_list';

 }


 /*代码与地址映射表修改页面*/
 elseif ($detail == 'editencode' || $detail == 'addencode') {

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('shipping_faremod')){$this->C->sendmsg();}

	$titlel = '<a href="index.php?action=shipping_countnew&amp;detail=list">运费管理</a> » <a href="index.php?action=shipping_countnew&amp;detail=showfare&amp;shipping_id='.$shipping_id.'&amp;from='.$from.'">查看运费数据</a> » <a href="index.php?action=shipping_countnew&amp;detail=encodeindex&amp;shipping_id='.$shipping_id.'&amp;from='.$from.'">映射管理</a> » ';

    if ($detail == 'editencode'){
        $shipping_encode    = $this->C->dao('shipping_encode');
        $sqlstr 			= ' and id='.$id;
        $sqlstr 			= str_replace('id','shipping_encode.id',$sqlstr);
        $datalists  		= $shipping_encode->getchipping_encode($sqlstr);
        $datalist   		= $datalists[0];

        $jump = 'index.php?action=shipping_countnew&detail=editmodencode';
        $this->V->mark(array('title'=>$titlel.'编辑'));

    }else{
        $jump = 'index.php?action=shipping_countnew&detail=addmodencode';
        $this->V->mark(array('title'=>$titlel.'新增'));

    }

    /*获取物流方式*/
    $sel    		= $this->C->service('global')->get_shipping('shipping_id disabled="disabled"','s_name','id','id',$shipping_id);
    $armuitilevel  .= $this->C->service('shipping_fare')->getArea($datalist['area_id'],$getfrom);

    /*表单配置*/
    $disinputarr				= array();
	$conform					= array('method'=>'post','action'=>$jump,'width'=>'600');
	$colwidth					= array('1'=>'100','2'=>'600','3'=>'100');
    $tablewidth 				= '1000';
    $disinputarr['id']      	= array('showname'=>'主键编号','value'=>$datalist['id'],'datatype'=>'h');
    $disinputarr['shipping_id'] = array('showname'=>'物流方式ID','value'=>$shipping_id,'datatype'=>'h');
    $disinputarr['from']		= array('showname'=>'发送地','value'=>$from,'datatype'=>'h');
    $disinputarr[$getfrom]  	= array('showname'=>'发送国家','value'=>$datalist['area_id'],'datatype'=>'h');
    $disinputarr['s_name']  	= array('showname'=>'物流方式','width'=>'60','datatype'=>'se','datastr'=>$sel);
    $disinputarr['nation']  	= array('showname'=>'地址','datatype'=>'se','datastr'=>$armuitilevel);
    $disinputarr['code']    	= array('showname'=>'代码','value'=>$datalist['code']);
    $temp 						= 'pub_edit';

    $jslink .= "<script src='./staticment/js/freight.js'></script>\n";
    $jslink .= "<script src='./staticment/js/shipping_encode.js'></script>\n";
 }


/*保存修改操作代码与地址映射关系*/
elseif ($detail == 'editmodencode') {

    if (empty($id))exit('参数获取错误');
    if ($$getfrom < 0) exit('国家编号错误');

    /*检测是否修改出现重复*/
    $shipping_encode    = $this->S->dao('shipping_encode');
    $count              = $shipping_encode->D->get_count(array('area_id'=>$$getfrom,'shipping_id'=>$shipping_id,'code'=>$code));

    if ($count > 0){$this->C->sendmsg("对不起，数据出现重复");}

    $res    = $shipping_encode->D->update_by_field(array('id'=>$id),array('area_id'=>$$getfrom,'shipping_id'=>$shipping_id,'code'=>$code));
    if ($res) $this->C->success("保存成功","index.php?action=shipping_countnew&detail=encodeindex&shipping_id=".$shipping_id."&from=".$from);
 }

/*保存新增代码与地址映射关系*/
elseif ($detail == 'addmodencode'){
    if (empty($$getfrom)) exit("国家编号错误");

    /*检测是否修改出现重复*/
    $shipping_encode    = $this->S->dao('shipping_encode');
    $count              = $shipping_encode->D->get_count(array('area_id'=>$$getfrom,'shipping_id'=>$shipping_id,'code'=>$code));
    if ($count > 0){$this->C->sendmsg("对不起，数据出现重复");}

    $data   = array('shipping_id'=>$shipping_id,'code'=>$code,'area_id'=>$$getfrom);
    $res    = $shipping_encode->D->insert($data);
    if ($res){$this->C->success("新增成功","index.php?action=shipping_countnew&detail=encodeindex&shipping_id=".$shipping_id."&from=".$from);}
}


/*代码与地址映射表删除操作*/
elseif ($detail == 'deleteencode') {

	/*权限判断*/
	if(!$this->C->service('admin_access')->checkResRight('shipping_faremod')){$this->C->ajaxmsg(0);}

    $shipping_encode    = $this->S->dao('shipping_encode');
    $data               = $shipping_encode->D->delete_by_field(array('id'=>$id));
    if ($data){$this->C->ajaxmsg(1,'');}else{$this->C->ajaxmsg(0,'删除失败');}
}

 /*获取级联菜单-省份*/
 elseif ($detail == 'muitilevel_province'){
        echo $this->C->service('shipping_fare')->muiltilevel_province($id,$inputname,1);
 }

 /*获取级联菜单-城市*/
 elseif ($detail == 'muitilevel_city') {
         echo $this->C->service('shipping_fare')->muiltilevel_city($id,$inputname,1);
 }

/*头尾模板包含*/
if($detail == 'list' || $detail == 'edit' || $detail == 'add' || $detail == 'showfare' || $detail == 'import' || $detail == 'encodeindex' || $detail == 'addencode' || $detail == 'editencode'){
	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>
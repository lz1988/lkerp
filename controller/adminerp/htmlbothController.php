<?
/**
 *核心公用模板，慎重修改。
 *编辑框与列表框共同输出。但考虑到未知的性能的影响，暂未启用，保留扩展。
 *已知的未知影响：1、重复包含JS，2、模板包含。
 */

/*编辑公共模板*/
if($pub_input == 1){

	/*默认的表单宽度*/
	if(!is_array($colwidth)){$colwidth = array('1'=>'25%','2'=>'50%','3'=>'25%');}

	$hidden_input = '';

	/*取得键名*/
	$inputkey = array_keys($disinputarr);
	$numfor = count($disinputarr);

	/*生成表单,se时为自定义,h时为隐藏*/
	for($i=0;$i<$numfor;$i++){
		$keyname = $inputkey[$i];

		if($disinputarr[$keyname]['datatype']=='se'){//自定的

			if(!$disinputarr[$keyname]['datastr']){exit('error:no $datastr');}
			$disinputarr[$keyname]['disinput'] = $disinputarr[$keyname]['datastr'];

		}elseif($disinputarr[$keyname]['datatype']=='h'){//隐藏的

			$value = $disinputarr[$keyname]['value'];
			$hidden_input.= "<input type='hidden' name=$keyname value=$value>";
			unset($disinputarr[$keyname]);
		}else{

			$value = $disinputarr[$keyname]['value'];//默认的
			$inextra = isset($disinputarr[$keyname]['inextra'])?$disinputarr[$keyname]['inextra']:'';
			$disinputarr[$keyname]['disinput'] = "<input type='text' name=$keyname value='".$value."'" .$inextra.">";
		}
	}

	/*标记变量并利用公共模板输出*/
	$this->V->mark(array('disinputarr'=>$disinputarr,'hidden_input'=>$hidden_input,'jslink'=>$jslink,'conform'=>$conform,'colwidth'=>$colwidth,'pub_input'=>$pub_input));


	//display();

}


/*列表公共模板*/
if($pub_list == 1){

	/*搜索的处理--Start*/
	if($stypemu){

		$search_output = array();
		$serkeys = array_keys($stypemu);
		$search_hidden = '';
		$jumpurl = 'index.php?action='.$_GET['action'].'&detail='.$detail;

		/*处理并生成input--Start*/
		for($s=0;$s<count($stypemu);$s++){
			$exploarr = explode('-',$serkeys[$s]);//分解取得input-name与类型

			/*普通type=text型*/
			if($exploarr[1] == 's'){
				$showinput = $stypemu[$serkeys[$s]].'<input type=text name='.$exploarr[0].' value='.$$exploarr[0].'>';
				$search_output[$s] = array('showinput'=>$showinput);
			}

			/*额外type=hidden型*/
			elseif($exploarr[1] == 'h'){
				$search_hidden.='<input type=hidden name='.$exploarr[0].' value='.$$exploarr[0].'>';
			}

			/*select下拉型*/
			elseif($exploarr[1] == 'a'){
				if(!is_array($acols = ${"$exploarr[0]arr"})){exit('error:no parsearr($'.$exploarr[0].')');}//未定义数组则报错退出
				$acols_keys = array_keys($acols);

				$showinput = $stypemu[$serkeys[$s]].'<select name='.$exploarr[0].'>';
				for($o=0;$o<count($acols);$o++){
					$selected = $$exploarr[0] === $acols_keys[$o]?'selected':'';
					$showinput.='<option value='.$acols_keys[$o].' '.$selected.'>'.$acols[$acols_keys[$o]].'</option>';
				}
				$showinput.='</select>';
				$search_output[$s] = array('showinput'=>$showinput);
			}

		}
		/*处理并生成input--End*/

		/*标记搜索输出*/
		$this->V->mark(array('search_output'=>$search_output,'search_hidden'=>$search_hidden,'jumpurl'=>$jumpurl));
	}
	/*搜索的处理--End*/


	/*取得键名*/
	$displaykey = array_keys($displayarr);
	$numfor = count($displaykey);

	$datakey = array_keys($datalist[0]);//只对第一条纪录取键值

	/*表头额外信息处理*/
	for($i=0;$i<$numfor;$i++){
		$keyname = $displaykey[$i];

		/*处理排序*/
		if(!empty($displayarr[$keyname]['orderlink'])){
			$displayarr[$keyname]['orderoutput'] = '<a href='.$displayarr[$keyname]['orderlink'].'>'.$displayarr[$keyname]['orderimg'].'</a>';
		}

	}

	/*操作处理*/
	if(in_array('edit',$displaykey)){

		if(empty($displayarr['edit']['url'])){exit('error:no modurl(edit)');}
		for($j=0;$j<count($datalist);$j++){

			/*对URL重构，自动取字段参数替换成该字段的值*/
			$url = $displayarr['edit']['url'];
			for($c=0;$c<count($datakey);$c++){
			$url = ereg_replace('{'.$datakey[$c].'}',"{$datalist[$j][$datakey[$c]]}",$url);
			}
			/*End*/

			$datalist[$j]['edit'] = '<a href='.$url.' title=修改><img src="./staticment/images/editbody.gif" border="0"></a>';
		}
	}elseif(in_array('delete',$displaykey)){


		if(empty($displayarr['delete']['url'])){exit('error:no modurl(delete)');}
		for($j=0;$j<count($datalist);$j++){

			/*对URL重构，自动取字段参数替换成该字段的值*/
			$url = $displayarr['delete']['url'];
			for($c=0;$c<count($datakey);$c++){
			$url = ereg_replace('{'.$datakey[$c].'}',"{$datalist[$j][$datakey[$c]]}",$url);
			}
			/*End*/

			$datalist[$j]['delete'] = '<a href='.$url.' title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
		}
	}elseif(in_array('both',$displaykey)){

		if(empty($displayarr['both']['url_e']) || empty($displayarr['both']['url_d'])){exit('error:no modurl(both)');}
		for($j=0;$j<count($datalist);$j++){

			/*取得两个URL*/
			$url_e = $displayarr['both']['url_e'];
			$url_d = $displayarr['both']['url_d'];

			/*对URL重构，自动取字段参数替换成该字段的值*/
			for($c=0;$c<count($datakey);$c++){
			$url_e = ereg_replace('{'.$datakey[$c].'}',"{$datalist[$j][$datakey[$c]]}",$url_e);
			$url_d = ereg_replace('{'.$datakey[$c].'}',"{$datalist[$j][$datakey[$c]]}",$url_d);
			}
			/*End*/
			$datalist[$j]['both'] ='<a href='.$url_e.' title=修改><img src="./staticment/images/editbody.gif" border="0"></a>&nbsp; <a href='.$url_d.' title=删除><img src="./staticment/images/deletebody.gif" border="0"></a>';
		}
	}


	/*标记变量并利用公共模板输出*/
	$this->V->mark(array('displayarr'=>$displayarr,'displaykey'=>$displaykey,'datalist'=>$datalist,'jslink'=>$jslink,'bannerstr'=>$bannerstr,'search_output'=>$search_output,'pub_list'=>$pub_list));

	//$this->V->set_tpl('adminweb/commom_datalist');
	//unset($displayarr,$displaykey,$datalist,$bannerstr,$jslink);
	//display();
}

/*演示到公共模板，再判断加载哪个模板*/
$this->V->set_tpl('adminweb/commom');
display();

?>
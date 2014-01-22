<?php
/*
 * Created on 2011-11-18
 * @title 自动生成搜索条件。
 * @autor hanson.
 *
 */
$stypemuarr = json_decode($_SESSION[$_REQUEST['action'].'_'.'stypemu'],true);

$formkeys = array_keys($stypemuarr);
$sqlstr = '';
$timeCountSystem = 0;


//根据定义的搜索数组，第三个横杠参数，'e'-精确搜索， 'l'-模糊搜索(L的小写).
for($k=0;$k<count($formkeys);$k++){

	$exploarr = explode('-',$formkeys[$k]);
	if($exploarr['1'] == 'a' && $$exploarr['0'] == 'selected'){$$exploarr['0'] = '';}//当下拉型为空时会自动变为selected，需作处理。

	//生成模糊搜索SQL
	if($exploarr['2'] == 'l' && (!empty($$exploarr['0']) || $$exploarr['0']== '0')){//值不为空或或者等0时才会生成SQL
		$sqlstr.= ' and '.$exploarr['0'].' like "%'.$$exploarr['0'].'%"';
	}

	//生成精确搜索SQL
	elseif($exploarr['2'] == 'e' && (!empty($$exploarr['0']) || $$exploarr['0']== '0')){
		$sqlstr.= ' and '.$exploarr['0'].'="'.$$exploarr['0'].'"';
	}

	//生成时间范围搜索
	elseif($exploarr['1'] == 't'){
		$timeCountSystem++;
		if($timeCountSystem <2){
			$sqlstr.= empty($startTime)?'':' and '.$exploarr['0'].'>="'.$startTime.' 00:00:00"';
			$sqlstr.= empty($endTime)?'':' and '.$exploarr['0'].'<="'.$endTime.' 23:59:59"';
		}else{
			$sqlstr.= empty(${$exploarr[0].'startTime'})?'':' and '.$exploarr['0'].'>="'.${$exploarr[0].'startTime'}.' 00:00:00"';
			$sqlstr.= empty(${$exploarr[0].'endTime'})?'':' and '.$exploarr['0'].'<="'.${$exploarr[0].'endTime'}.' 23:59:59"';		
		}		
	}
}

/*删除session,初定不能删除，删除会导致AJAX操作完毕刷新时，无法生成搜索条件而清空;同时跳转也无效*/

//unset($_SESSION['stypemu']);


?>

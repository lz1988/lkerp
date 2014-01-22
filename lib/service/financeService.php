<?php
/*
* Created on 2012-4-20
* @author hanson
* @title 财务服务层方法
*/
class financeService extends S{
    /*
    * title    --    用于删除或修改凭证时，回写财务单。
    * @param obj    $table         -- 凭证表实例化对象。
    * @param string $id            -- 凭证表ID。
    * @param string $type          -- 1删除动作，2修改动作。
    * @param price  $price         -- 更新后的金额(可选,$type=2时要填)。
    */
    public function finance_neend_rewrite($table,$id,$type,$price=''){
        $backdata = $table->D->get_one_by_field(array('id'=>$id),'sorder_id');
        $finance_neend = $this->S->dao('finance_neend');

        /*删除凭证明细，则需将应付帐款count_money置0，并且未完成状态*/
        if($type == 1){
            $sid = $finance_neend->D->update_by_field(array('order_id'=>$backdata['sorder_id']),array('count_money'=>'0','ispay'=>'N','muser'=>'','mdate'=>''));
            return $sid;
        }
        /*更新凭证明细*/
        elseif($type == 2){
            if(empty($price)) return false;
            $backmoney = $finance_neend->D->get_allstr('and order_id="'.$backdata['sorder_id'].'"','order_id','','id,SUM( money) as money,SUM( count_money ) as count_money');
            $allneed   = $backmoney['0']['money'] - $backmoney['0']['count_money'];

            /*如果修改后的金额大于或等于原单金额*/
            if($price >= $allneed){
                /*更新累计量，并置该财务单完成付款*/
                $rid   = $finance_need->D->update_by_field(array('id'=>$backmoney['0']['id']),array('count_money'=>$price));
                $iid   = $finance_neend->D->update_by_field(array('order_id'=>$backdata['sorder_id']),array('ispay'=>'Y','muser'=>$_SESSION['eng_name'],'mdate'=>date('Y-m-d H:i:s',time())));

                if($rid && $iid) {return true;}else{return false;}
            }
            /*如果修改后的金额小于原单金额，更新累计量(回退需手动)。*/
            else{
                $rid   = $finance_need->D->update_by_field(array('id'=>$backmoney['0']['id']),array('count_money'=>$price));
                return $rid;
            }
        }
    }

    /*
    * @title 对采购单增加费用时，平均分摊到所有产品，(忽略红单负数量与负金额)
    * by hanson 2012-08-14
    */
    public function stockorder_addfare($process,$order_id,$name,$fare,$upcolumn=''){

    	$feearr			 = array('fee2'=>'fee3','fee3'=>'fee2');
        $error           = 0;
        $backdata_num    = $process->D->get_one(array('order_id'=>$order_id,'statu'=>3),'sum(quantity)');
        $perfare         = number_format($fare/$backdata_num,2);//算出每个产品的单价应加上的费用

        /*当运费不能被整除的时候，计算多出或少过的费用零头*/
        $extrafare       = $backdata_num * $perfare - $fare;

        $backdatalist    = $process->D->get_all(array('order_id'=>$order_id,'statu'=>3),'id','asc','id,quantity,fee,fee2,fee3,price,extends');


        for($i=0;$i<count($backdatalist);$i++){

            /*将额外信息存进输出数组$datalist*/
            $extends				= json_decode($backdatalist[$i]['extends'],true);
            $update_arr		 		= array();

            /*该行记录分摊的费用，以最后一条算多差少补*/
			$rowfare 				= ($i == count($backdatalist) -1) ? ($perfare*$backdatalist[$i]['quantity'] - $extrafare) : $perfare*$backdatalist[$i]['quantity'];

			$extends['e_scost']		= $extends['e_sprice'] + $backdatalist[$i]['fee'] + $rowfare + $backdatalist[$i][$feearr[$upcolumn]];//总成本=不含税合计+该记录分摊的费用
			$update_arr[$upcolumn]	= $rowfare;
			$update_arr['price2']	= $extends['e_scost']/$backdatalist[$i]['quantity'];//单位总成本=总成本/该条记录的数量


            /*扩展内容重新压缩*/
            $extends = get_magic_quotes_gpc()?addslashes(json_encode($extends)):json_encode($extends);//如果魔法函数开启，注意需事先添加反斜杠，否则json数据被过滤掉s。

			$update_arr['extends'] 	= $extends;

           	$sid = $process->D->update(array('id'=>$backdatalist[$i]['id']), $update_arr);

           	if(!$sid) {$error++;}
        }

        return array('error'=>$error);
    }

   /**
    * by hanson 2012-08-16 @title 移动加权平均 (暂时忽略币别问题)
    *
    */
    public function countnum_cost($process,$id,$quantity){

    	/*** 查库存 ***/
    	$backdata	= $process->D->get_one(array('id'=>$id),'pid,sku,receiver_id,price2');
    	$backwh		= $process->get_allw_allsku(' and temp.sku="'.$backdata['sku'].'" and temp.wid='.$backdata['receiver_id']);
    	$nownum		= $backwh['0']['sums'];
		if ($nownum < 0 ) {$nownum = 0;}

    	/*** 查成本，不是CNY币别转换成CNY ***/
    	$syscost	= $this->S->dao('product_cost')->D->get_one(array('pid'=>$backdata['pid']),'cost1,coin_code');
    	$rateboj	= new exchange_rateService();
    	$istcost	= $rateboj->change_rate($syscost['coin_code'] ,'CNY', $syscost['cost1']);

    	/*** 当前库存*当前产品的成本+本次入库数量*单位总成本(price2)/当前库存+本次入库数量 ***/
    	return ($nownum*$istcost+$quantity*$backdata['price2'])/($nownum+$quantity);

    }


    /**
    * @title 获取产品当前成本，转换成本位币
    * by hanson 2012-08-16
    */
    public function get_productcost($pid){
        $backcost = $this->S->dao('product_cost')->D->get_one_by_field(array('pid'=>$pid),'cost1,coin_code');

        if($backcost){
            $bafault_coin = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'system_defaultcoin','bid'=>'sys'),'value');

            if($backcost['coin_code'] != $bafault_coin['value']){
                $rateboj = new exchange_rateService();
                $backcost['cost1'] = $rateboj->change_rate($backcost['coin_code'],$bafault_coin['value'],$backcost['cost1']);
            }
        }else{
            $backcost['cost1'] = 0;
        }

        return $backcost['cost1'];
    }

    /*
    * @title 回写插入或更新数组
    * by hanson 2012-08-21
    */
    public function rewrite_inorup_arr($data_arr,$pid){
        /*取得本位币*/
        $backdfaultcoin = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'system_defaultcoin','bid'=>'sys'),'value');

        /*期号*/
        $backdfaultstage= $this->S->dao('exchange_rate')->D->get_one_by_field(array('code'=>$backdfaultcoin['value'],'isnew'=>1),'stage_rate');//期号

        /*回写数组*/
        $data_arr['coin_code']     = $backdfaultcoin['value'];
        $data_arr['stage_rate']    = $backdfaultstage['stage_rate'];
        $data_arr['price2']        = $this->get_productcost($pid);//通过PID获取成本(CNY)
    }

	/*更新产品成本*/
	public function updatecost($pid,$cost1,$cost2,$mdate){

		$product_cost = $this->S->dao('product_cost');
		$back	= $product_cost->D->get_one_by_field(array('pid'=>$pid),'pid,cost3,coin_code');

		if($back){

			if($back['cost3'] && $back['coin_code']!='CNY'){//市场指导价币别转换
				$rateboj		= new exchange_rateService();
				$back['cost3']	= $rateboj->change_rate($back['coin_code'] ,'CNY', $back['cost3']);
			}

			return $product_cost->D->update_by_field(array('pid'=>$pid),array('cost1'=>$cost1,'cost2'=>$cost2,'cost3'=>$back['cost3'],'coin_code'=>'CNY','mdate'=>$mdate));

		}else{
			$product_cost->D->insert(array('pid'=>$pid,'cost1'=>$cost1,'cost2'=>$cost2,'coin_code'=>'CNY','cdate'=>$mdate));
			return true;//由于product_cost没有主键，插入不会返回，固手动返回。
		}

	}
}
?>

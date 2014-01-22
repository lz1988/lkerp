<?php
/*
 * Created on 2011-10-20
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class exchange_rateService extends S{
 	private static $rate,$sou,$to,$tousd_rate,$usdto_rate,$tousd_rate_stage,$usdto_rate_stage,$to_stage,$sou_stage,$this_stage;

    public function change_rate($source,$tobe,$val){
    	$exchange_rate = $this->S->dao('exchange_rate');
		if($source == $tobe){return $val;}//直接返回。

		/*美元兑换其他*/
		elseif($source == 'USD' && $tobe != 'USD'){
			if(empty(self::$usdto_rate) || self::$to != $tobe){
				self::$usdto_rate	= $exchange_rate->D->get_one(array('code'=>$tobe,'isnew'=>1),'rate');
				self::$to	 		= $tobe;
			}
			return $val*self::$usdto_rate/100;
		}

		/*其它兑换美元*/
		elseif($source != 'USD' && $tobe == 'USD'){
			if(empty(self::$tousd_rate) || self::$sou != $source){
				self::$tousd_rate	= $exchange_rate->D->get_one(array('code'=>$source,'isnew'=>1),'rate');
				self::$sou			= $source;
			}
			return $val/self::$tousd_rate*100;
		}

		/*其它兑换其他*/
		elseif($source != 'USD' && $tobe != 'USD'){
			$val  = $this->change_rate($source , 'USD', $val);//先兑换成美元
			$val  = $this->change_rate('USD', $tobe, $val);//美元再兑换成其它
			return $val;
		}
    }


    /*当一批需要转换成美元的时候，另外写一个，避免重复查表*/
    public function change_usd($source,$val){
    	if(empty(self::$rate) || self::$sou != $source){
    		$ratearr 	= $this->S->dao('exchange_rate')->D->get_one(array('code'=>$source,'isnew'=>1),'rate');
    		self::$rate = $ratearr;
    		self::$sou	= $source;
    	}
		return number_format($val/self::$rate*100,2);
    }

    /*已知汇率，转换成美元*/
    public function change_usd_rate($rate,$val){
    	return $val/$rate*100;
    }

	/**
	 * create by wall
	 * on 2012-08-15
	 * 传入金额、原币种、目标币种、期号，得到转换后的金额
	 * */
    public function change_rate_by_stage($price, $fromcode, $tocode, $stage) {
    	$exchange_rate = $this->S->dao('exchange_rate');
		if($fromcode == $tocode){return $price;}//美元兑换美元，直接返回。


		elseif($fromcode == 'USD' && $tocode != 'USD'){//美元兑换其他。
			$rate = $exchange_rate->get_by_code_and_stage_rate($tocode, $stage);
			return $price*$rate['rate']/100;
		}


		elseif($fromcode != 'USD' && $tocode == 'USD'){//其它兑换美元，定义静态变量，防止重复查询次数过多
			if(empty(self::$tousd_rate_stage) || self::$sou_stage != $fromcode || self::$this_stage != $stage){
				$rate 					= $exchange_rate->get_by_code_and_stage_rate($fromcode, $stage);
				self::$sou_stage 		= $fromcode;
				self::$tousd_rate_stage = $rate['rate'];
				self::$this_stage 		= $stage;
			}else{
				$rate['rate'] 			= self::$tousd_rate_stage;
			}
			return $price/$rate['rate']*100;
		}


		elseif($fromcode != 'USD' && $tocode != 'USD'){//其它兑换其他
			$rate = $exchange_rate->get_by_code_and_stage_rate($fromcode, $stage);
			$val = $price/$rate['rate']*100;//先兑换成美元
			$rate = $exchange_rate->get_by_code_and_stage_rate($tocode, $stage);
			return  $price*$rate['rate']/100;//美元兑换成其他
		}
    }
 }
?>

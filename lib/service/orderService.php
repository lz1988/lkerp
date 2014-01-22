<?php
/*
 * Created on 2012-10-31
 *
 * To change the template for this generated file go to
 * BY  HANSON 存放订单处理相关的公共方法
 */
  class orderService extends S{


	/**
	 * by hanson 2012-10-31 根据销售帐号取出关联的收款帐号，返回数组。
	 */
  	public function getRecaccountBysoldaccount(){

		$rexlistArr = array();
		$rexlist 	= $this->S->dao('sold_relation_conf')->getSoldRelationConfList();//取得关系列表称
		foreach($rexlist as $val){
			$rexlistArr[$val['account_name']] = $val['payrec_account'];
		}
		return $rexlistArr;
  	}

  	/**
  	 * by hanson 2012-11-21 根据销售帐号取出关联的渠道ID与收付款帐号ID，返回数组。
  	 */
	public function getIdBysoldaccount(){
		$rexlistArr = array();
		$rexlist 	= $this->S->dao('sold_relation_conf')->getSoldRelationConfList();//取得关系列表称
		foreach($rexlist as $val){
			$rexlistArr[$val['account_name']] = array('swid'=>$val['swid'],'fpaid'=>$val['fpaid'],'said'=>$val['said']);
		}
		return $rexlistArr;
	}

	/**
	 * by hanson 2012-11-21 根据仓库无误后，根据形成的仓库名称与ID数组来直接取得仓库ID
	 */
	public function getEsseidByname($tablename, $sqlstr, $key, $id='id'){

		$backArr	= array();
		$backdata	= $this->S->dao($tablename)->D->get_allstr($sqlstr);
		foreach($backdata as $val){
			$backArr[$val[$key]] = $val[$id];
		}
		return $backArr;
	}


	/**
	 * by hanson 2012-11-21 查看锁表标记，若空闲则锁表。
	 */
	public function checklock($type = 'begin',$url){

		$locktab = $this->S->dao('locktab');
		if($type == 'begin'){
			$back_checklock = $locktab->D->get_one(array('type'=>0),'onoff');
			if($back_checklock == '1'){$this->C->sendmsg('服务器繁忙，请重试！', $url);}

			/*标记锁表*/
			return $locktab->D->update(array('type'=>0),array('onoff'=>1));
		}

		/*执行完毕解锁表*/
		elseif($type == 'end'){

			return $locktab->D->update(array('type'=>0),array('onoff'=>0));
		}
	}
    
    //获取销售代码
     public function get_account_code(){
        
        /*取得销售账号下拉*/
    	$soldaccount	= $this->S->dao('sold_account')->D->get_allstr('','','','id,account_code');
    	$addressarr		= array();
    	for($i = 0; $i < count($soldaccount); $i++){
    		$addressarr[$soldaccount[$i]['id']]		= $soldaccount[$i]['account_code'];
    	}
        return $addressarr; 
    }

  }
?>

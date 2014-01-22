<?php
class sold_relation_confDao extends D{
	/*取得所有销售渠道列表*/
	public function getSoldRelationConfList(){
		$sql = 'SELECT sold_relation_conf.id,sold_way.wayname,sold_way.id as swid,sold_account.account_name,sold_account.id as said,finance_payrec_account.payrec_account,finance_payrec_account.id as fpaid FROM sold_relation_conf
                LEFT JOIN sold_way on sold_relation_conf.way_id = sold_way.id
                LEFT JOIN sold_account on sold_relation_conf.account_id = sold_account.id
                LEFT JOIN finance_payrec_account on sold_relation_conf.payrec_id = finance_payrec_account.id;
        ';
		return $this->D->query_array($sql,'fetch_assoc');
	}

	/*通过销售帐号取出关联的收付款帐号*/
	public function get_default_payrec($sold_id){

		$sql 		= 'select s.payrec_account,r.payrec_id,r.way_id from sold_relation_conf as r left join finance_payrec_account as s on r.payrec_id=s.id left join sold_way as w on r.way_id=w.id where r.account_id='.$sold_id;

		$backdata 	= $this->D->query_array($sql);

		return $backdata['0'];
	}

	/*通过销售帐号名称取得关联的收款帐号名称*/
	public function get_default_payrecByname($sold_account){

		$sql = 'select s.payrec_account from sold_relation_conf r left join finance_payrec_account s on r.payrec_id=s.id left join sold_account as a on r.account_id=a.id where a.account_name="'.$sold_account.'"';
		$backdata 	= $this->D->query_array($sql);
		return $backdata['0']['payrec_account'];
	}
}
?>

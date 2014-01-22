<?php
/**
 * @author by Jerry
 * @carete on 2012-11-02
 */

 class supplieraccountDao extends D{

    /**
    * @title 账期管理列表
    * by Jerry  create on 2012-11-01
    */

    public function getsupplieraccount($sqlstr){
        $sql = 'select s.id,s.eid,p.pid,p.sku,e.name,e.esseid,s.issuetime,s.account,s.remark FROM `product` p left JOIN supplieraccount s  ON p.pid=s.pid JOIN esse e ON e.id=s.eid  '.$sqlstr;
        return $this->D->query_array($sql);
    }

	/**
	 * 根据账期ID返回供应商名称
	 */
	public function GetnameByid($id){
		$sql = 'select e.name from supplieraccount s left join esse e on e.id=s.eid where s.id='.$id;
		$barr= $this->D->query_array($sql);
		return $barr['0']['name'];
	}

    /**
     * by Jerry 2012-11-05 备货申请--账期列表
     *
     */
    public function getupstocklist($sqlstr){

        $sql = 'SELECT s.id,e.esseid,e.name,s.pid,s.eid,s.issuetime,s.account FROM supplieraccount s JOIN esse e ON e.id=s.eid WHERE e.type=3 '.$sqlstr;
        //die($sql);
        return $this->D->query_array($sql);
    }
 }
?>
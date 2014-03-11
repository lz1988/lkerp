<?php
class type_accountDao extends D{
	/*取得所有销售帐号列表*/
	public function getTypeAccountList(){
		$sql = 'SELECT * FROM type_account';
        $sql.= ' order by brand asc,id desc ';
		return $this->D->query_array($sql,'fetch_assoc');
	}
}
?>

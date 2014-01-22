<?php
class sold_accountDao extends D{
	/*取得所有销售帐号列表*/
	public function getSoldAccountList(){
		$sql = 'SELECT * FROM sold_account';
		return $this->D->query_array($sql,'fetch_assoc');
	}
}
?>

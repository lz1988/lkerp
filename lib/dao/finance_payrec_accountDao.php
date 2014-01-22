<?php
class finance_payrec_accountDao extends D{
	/*取得所有销售帐号列表*/
	public function getFinancePayrecAccountList(){
		$sql = 'SELECT * FROM finance_payrec_account';
		return $this->D->query_array($sql,'fetch_assoc');
	}
}
?>

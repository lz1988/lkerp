<?php
class user_labelDao extends D{
	/*取得当前登陆用户所有标签列表*/
	public function getUserLabelList(){
		$sql = "SELECT * FROM user_label WHERE create_user='".$_SESSION['eng_name']."'";
		return $this->D->query_array($sql,'fetch_assoc');
	}
}
?>
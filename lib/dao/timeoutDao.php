<?php
/*
 * Created on 2012-5-16
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class timeoutDao extends D{

	protected $time_out_set	= 1800;//关闭浏览器掉线时间。

	/*更新在线人数*/
	public function update_online(){

		$time_uid 		= $this->D->get_one_by_field(array('uid'=>$_SESSION['uid']),'id');
		$time_chtime	= date('Y-m-d H:i:s',time());

		if($time_uid['id']){
			$this->D->update_by_field(array('id'=>$time_uid['id']),array('stamptime'=>$time_chtime));
		}else{
			$this->D->insert(array('uid'=>$_SESSION['uid'],'stamptime'=>$time_chtime));
		}

		/*清除掉线用户*/
		$sqldel = 'delete from timeout where (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(stamptime)) > '.$this->time_out_set;
		$this->D->query($sqldel);

	}
 }
?>

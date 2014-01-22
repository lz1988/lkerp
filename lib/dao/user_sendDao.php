<?php
/**
 * @title 用户提醒信息转接 
 * @author Jerry
 * @create on 2013-03-22
 */
 
 class user_sendDao extends D{
    
    /*获取用户转接对应的用户列表*/
    public function get_user_send($sqlstr){
        $sql = 'SELECT us.id,u.uid,u.eng_name,ud.eng_name AS send_name,us.senduid,admin_group.groupname FROM `user` AS u LEFT JOIN user_send us ON us.uid=u.uid JOIN `user` AS ud ON us.senduid = ud.uid LEFT JOIN admin_group  ON admin_group .id=u.groupid where 1=1  '.$sqlstr.'';
        $sql .= ' order by u.uid desc';
        return $this->D->query_array($sql);
    }
    public function getsenduser($sqlstr){
        $sql = 'select u.eng_name,us.uid,us.senduid from user_send  us join user u on u.uid=us.senduid where 1=1 '.$sqlstr;
        //die($sql);
        return $this->D->query_array($sql);
    }
 }
?>
<?php
/*
 * Created on 2011-10-12
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
if($sql == '1'){
    $_SESSION['issqlcontrol'] = 1;

    if($_SESSION['issqlcontrol'] == 1){
        echo '已开启SQL监控';
    }
}
elseif($sql == '0'){
    $_SESSION['issqlcontrol'] = 0;

    if($_SESSION['issqlcontrol'] == 0){
        echo '已关闭SQL监控';
    }
}
else{
    exit('no such detail work');
}
?>
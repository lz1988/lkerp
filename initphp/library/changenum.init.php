<?php
class changenumInit{

	public function num2rmb ($num){

    $c1="ÁãÒ¼·¡ÈşËÁÎéÂ½Æâ°Æ¾Á";

    $c2="·Ö½ÇÔªÊ°°ÛÇªÍòÊ°°ÛÇªÒÚ";
    $num=round($num,2);

    $num=$num*100;

    $NewNum = ceil($num);

    if(strlen($NewNum)>10){

    return "½ğ¶îÌ«´ó";

    }
    $i=0;

    $c="";
    while (1){

    if($i==0){

    $n=substr($num,strlen($num)-1,1);

    }else{

    $n=$num %10;

    }



    $p1=substr($c1,2*$n,2);

    $p2=substr($c2,2*$i,2);

    if($n!='0' || ($n=='0' &&($p2=='ÒÚ' || $p2=='Íò' || $p2=='Ôª' ))){

    $c=$p1.$p2.$c;

    }else{

    $c=$p1.$c;

    }

    $i=$i+1;

    $num=$num/10;

    $num=(int)$num;



    if($num==0){

    break;

    }

    }//end of while| here, we got a chinese string with some useless character



    //we chop out the useless characters to form the correct output

    $j = 0;

    $slen=strlen($c);

    while ($j< $slen) {

    $m = substr($c,$j,4);

    if ($m=='ÁãÔª' || $m=='ÁãÍò' || $m=='ÁãÒÚ' || $m=='ÁãÁã'){

    $left=substr($c,0,$j);

    $right=substr($c,$j+2);

    $c = $left.$right;

    $j = $j-2;

    $slen = $slen-2;

    }

    $j=$j+2;

    }


    if(substr($c,strlen($c)-2,2)=='Áã'){

    $c=substr($c,0,strlen($c)-2);

    } // if there is a '0' on the end , chop it out


	//return $c;
    return iconv("GBK//IGNORE", "utf-8", $c);

	}// end of function
}
?>

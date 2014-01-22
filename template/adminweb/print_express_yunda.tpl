<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>韵达快递</title>
<style type="text/css">
body {text-align: center; font-family:Times New Roman,Tahoma, Geneva, sans-serif;margin:0; padding:0;-webkit-text-size-adjust:none;font-size:12px; color:#7752aa;}
div,form,img,ul,ol,li,dl,dt,dd,p {margin:0; padding:0; border:0;} 
img{ overflow:hidden;vertical-align:bottom;}
li{list-style:none;}
h1,h2,h3,h4,h5,h6 {margin:0; padding:0;}
table,td,tr,th{font-size:12px;}
* A {POSITION: relative}
.clear{ clear: both;}

a{ color:#7752aa; text-decoration:none; blr:expression(this.onFocus=this.blur()); outline: none}
a:hover{ color:#7752aa; text-decoration: underline;}
.orders_print{width:700px; margin:0 auto; text-align:left; font-size:16px; color:#000000; line-height:30px; padding-top:40px; font-family:"宋体-PUA"}
.orders_print b{ font-size:16px; font-weight:bold;}
.orders_print h2{ font-size:20px; font-weight:bold; text-align:center}
.orders_print .left{ float:left; width:263px; padding-top:30px;}
.orders_print .right{ float:left; width:350px; padding-left:50px; padding-top:5px;}
.orders_print .bottom{ padding-top:25px; clear:both; padding-bottom:150px;}
.orders_print .name{ margin:0; padding:0}
.orders_print .name ul{ margin:0; padding:0}
.orders_print .name ul li{float:left;}
.orders_print .pro_name{ clear:both; width:260px; padding-top:40px}
.orders_print .city{width:200px; float:left; display:inline-block;}
</style>
</head>
<body>

<!--{foreach ($showdata as $val) }-->
<div class="orders_print">
      <div class="left"><b>米悠本色</b><h2>深圳市南山区蛇口沿山路23号胜发大厦B栋102</h2></div>
      <div class="right">
      <div class="city">&nbsp; <!--{echo $val['city']}--></div>
      <div style="float:left"><!--{echo $val['tel']}--></div>
      <div class="clear">&nbsp;</div><!--buyer_id-->
      <p><!--{echo $val['address']}--></p></div>
      <div class="bottom">
          <div class="name">
               <ul>
                   <li style="padding-right:80px;">1894878325</li>
                   <li>518000</li>
                   <li style="padding-left:90px;"><!--{echo $val['e_receperson']}--></li>
                   <li style="padding-left:180px"><!--{echo $val['post_code']}--></li>
               </ul>
          </div>
          <div class="pro_name"><!--{echo $val['showdetail']['0']['product_name']}--></div>
          <!--{$order_num = 0;}-->
          <!--{foreach ($val['showdetail'] as $vall)}-->
          <!--{$order_num+=$vall['quantity']}-->
          <!--{/foreach}-->
          <div style="padding-top:20px;">数量：<!--{echo $order_num}--></div>
      </div>
</div>
<!--{/foreach}-->

</body>
</html>

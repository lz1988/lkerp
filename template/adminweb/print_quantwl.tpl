<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>全峰快递</title>
<style type="text/css">
body {text-align: center; font-family:Times New Roman,Tahoma, Geneva, sans-serif;margin:0; padding:0;-webkit-text-size-adjust:none;font-size:12px; color:#7752aa;}
div,form,img,ul,ol,li,dl,dt,dd,p {margin:0; padding:0; border:0;} 
img{ overflow:hidden;vertical-align:bottom;}
li{list-style:none;}
h1,h2,h3,h4,h5,h6 {margin:0; padding:0;}
table,td,tr,th{font-size:12px;}
* A {POSITION: relative}
.clear{ clear: both;}


.orders_print{
	width:700px;
	margin:0 auto;
	text-align:left;
	font-size:16px;
	color:#000000;
	line-height:25px;
	padding-top:50px;
	font-family:"宋体-PUA"
}
.orders_print b{ font-size:16px; font-weight:bold;}
.orders_print h2{ font-size:20px; font-weight:bold; text-align:center}
.orders_print .left{ float:left; width:263px; padding-top:8px;}
.orders_print .right{ float:left; width:350px; padding-left:80px; padding-top:5px; margin-top:10px;}
.orders_print .bottom{ padding-top:10px; clear:both; padding-bottom:20px;}
.orders_print .name{ margin:0; padding:0}
.orders_print .name ul{ margin:0; padding:0}
.orders_print .name ul li{float:left;}
.orders_print .pro_name{ clear:both; width:260px; padding-top:30px; font-size:14px;}
.orders_print .city{width:170px; float:left; margin-left:20px;}
</style>
</head>
<body>

<!--{foreach ($showdata as $val) }-->
<div class="orders_print">
      <div class="left"><b>深圳市米悠本色文化传播有限公司</b><h4>深圳市南山区蛇口沿山路23号胜发大厦B栋102  0755-26857648  18948783201</h4></div>
      <div class="right">          
          <div style="height:60px; text-align:left; padding-right:110px"><!--{echo $val['address']}--> <!--{echo $val['city']}--> <!--{echo $val['e_receperson']}--> <!--{echo $val['tel']}--></div>
      </div>
      <div class="bottom">
          <div class="name">
               <ul>
                   <li style="padding-right:80px;"></li>
                   <li>&nbsp;</li><!--on_postcode-->
                   <li style="padding-left:50px;">&nbsp;</li>
                   <li style="padding-left:160px"><!--echo $val['tel']--></li><!--to_postcode-->
               </ul>
          </div>
          <div class="pro_name"><!--{echo $val['showdetail']['0']['product_name']}--></div>
          <!--{$order_num = 0;}-->
          <!--{foreach ($val['showdetail'] as $vall)}-->
          <!--{$order_num+=$vall['quantity']}-->
          <!--{/foreach}-->
          <div style="font-size:14px">数量：<!--{echo $order_num}--></div>
      </div>
</div>
<!--{/foreach}-->

</body>
</html>

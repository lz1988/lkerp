<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-07-23 16:47:37, compiled from template/adminweb/print_outbound.tpl */ ?>
<!--打印发货单-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>发货单</title>
<style type="text/css">
body {text-align: center; margin:0; padding:0;-webkit-text-size-adjust:none;font-size:12px; color:#000000;}
div,form,img,ul,ol,li,dl,dt,dd,p {margin:0; padding:0; border:0;} 
img{ overflow:hidden;vertical-align:bottom;}
li{list-style:none;}
h1,h2,h3,h4,h5,h6 {margin:0; padding:0;}
table,td,tr,th{font-size:12px;}
* A {POSITION: relative}
.clear{ clear: both;}
a{ color:#7752aa; text-decoration:none; blr:expression(this.onFocus=this.blur()); outline: none}
a:hover{ color:#7752aa; text-decoration: underline;}

.invoice{ width:650px; margin:0 auto; color:#000000; padding:20px; padding-top:35px; padding-left:0;}
.invoice .invoice_lfet{}
.invoice .invoice_lfet h2{font-size:20px;}
.invoice .invoice_right{float:right; text-align:left; font-size:12px; line-height:25px; padding-bottom:10px;}
.invoice .invoice_right span{padding-left:30px;}
.invoice .name{ clear:both; padding-bottom:20px;}
.invoice .name ul{ padding:0; margin:0px; clear:both; padding-bottom:20px;}
.invoice .name li{ float:left; padding-right:50px;}
.invoice .name span{ padding-right:5px; font-weight:bold}
.invoice .ptable th{ border-top:#000000 solid 1px; border-bottom:#000000 solid 1px;height:30px; line-height:30px; font-weight:bold;}
.invoice .ptable td{ padding-top:5px; height:25px; line-height:25px;text-align:center;}
.invoice .phone{border-top:#000000 solid 1px; text-align:left !important; padding-left:10px;}
</style>
</head>
<body>

<?php foreach ($showdata as $val)  { ?>

<div class="invoice">
  <div class="invoice_lfet"><h2>发货单</h2></div>
  <div class="invoice_right">
         <ul>
             <li>发货日期:<span><?php echo $val['cdate'] ?></span></li>
             <li>订单号:<span><?php echo $val['fid'] ?></span></li>
        </ul>
  </div>
  <div class="name">
          <ul>
          <li><span>买家ID：</span><?php echo $val['buyer_id'] ?></li>
          <li><span>买家姓名：</span><?php echo $val['e_receperson'] ?></li>
          <li><span>联系电话：</span><?php echo $val['tel'] ?></li>
          </ul>
           <ul>
          <li><span>收货地址：</span><?php echo $val['address'] ?></li>
          </ul>
    </div>
    
    <div style="height:270px; ">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="ptable" >
          <tr>
            <th width="6%">序号</th>
            <th width="11%">商家编码</th>
            <th width="68%">宝贝名称</th>
            <th width="8%">&nbsp;</th>
            <th width="7%">数量</th>  
          </tr>
          
          <?php $orders = 1 ?>
          <?php foreach ($val['showdetail'] as $vall) { ?>
          <tr>
            <td><?php echo $orders ?></td>
            <td><?php echo $vall['sku'] ?></td>
            <td><?php echo $vall['product_name'] ?></td>
            <td>&nbsp;</td>
            <td><?php echo $vall['quantity'] ?></td>
          </tr>
          <?php $orders++ ?>
          <?php } ?>
          
          <tr>
              <td colspan="2" class="phone">米悠本色</td>
              <td colspan="3" class="phone">&nbsp; &nbsp; 联系电话：18948783201  &nbsp; &nbsp; 备注：<?php echo $val['comment'] ?></td>
          </tr>
        </table>
    </div>
</div>

<?php } ?>

</body>
</html>

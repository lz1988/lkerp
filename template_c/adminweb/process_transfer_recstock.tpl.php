<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-03 10:14:43, compiled from template/adminweb/process_transfer_recstock.tpl */ ?>
<!--转仓入库模板-->

<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body{font-family: Arial,Helvetica,sans-serif;}
.commomform{color: #00004F;font-size:14px;}
.tips{color:#c6a8c6; font-size:14px;}
.commomform input,#commomform select{width:200px;height:25px;border: double #CCCCFF 1px; font-size:14px}
.big {font-size: 16px}
.point { cursor:pointer;}
#subinput{width:82px; height:30px; border:none; cursor:pointer;}
#mytable input {height:25px;border-left: 1px solid #C2C2C2; border-right: 1px solid #EAEAEA; border-top: 1px solid #C2C2C2;	border-bottom:1px solid #eeeeee;}
#supplier { margin-left:0px; position:absolute;}
#suplist { font-size:14px;}
</style>
<!--填写资料部分-->
<form method="POST" action="index.php?action=process_recstock&detail=<?php echo $moddetail ?>" >
<input type="hidden" name="page" value="<?php echo $page ?>" />
<table id="mytable" cellspacing="0" width="1600">
	<tr>
    	<th class="list" width="50">行号</th>
        <th class="list" width="100">产品SKU</th>
        <th class="list" width="250">产品名称</th>
        <th class="list" width="100">入库仓库</th>        
        <th class="list" width="80">订单数量</th>
        <th class="list" width="80">入库数量</th>
        <th class="list" width="150">备注</th>
        <th class="list" width="80">客户损坏</th>
        <th class="list" width="80">供应商损坏</th>
        <th class="list" width="80">物流损坏</th>
        <th class="list" width="80">仓库损坏</th>
        <th class="list" width="80">其它损坏</th>        
    </tr>
<?php if ($datalist) { ?>
<?php $floor=0 ?>
<?php foreach ($datalist as $val) { ?>
<?php $floor++ ?>
	<tr>
<td><input type="hidden"  name="detail_id[]" value="<?php echo $val['id'] ?>" /><span id="row_<?php echo $floor ?>" class="big"><?php echo $floor ?></span></td>
        <td><input type="text"  name="" style="width:100px" value="<?php echo $val['sku'] ?>" disabled></td><!--产品SKU-->
        <td><input type="text"  name="" style="width:250px" value="<?php echo $val['product_name'] ?>"  title="<?php echo $val['product_name'] ?>" disabled></td><!--产品名称-->
        <td><input type="text"  name="" style="width:100px" value="<?php echo $val['name'] ?>" disabled></td><!--入库仓库-->
        <td><input type="text"  name="" style="width:50px" value="<?php echo $val['quantity'] ?>"   disabled></td><!--订单数量-->
        <td><input type="text"  name="quantity[]" style="width:50" value="<?php echo $val['quantity'] ?>" disabled></td><!--入库数量-->
        <td><input type="text"  name="comment[]" style="width:160px" value="<?php echo $val['comment'] ?>"  title="<?php echo $val['comment'] ?>" disabled></td><!--备注-->
        <td><input type="text"  name="defective_customer[]"   style="width:80px"  /></td><!--客户损坏-->
        <td><input type="text"  name="damaged_distributor[]"   style="width:80px"  /></td><!--供应商损坏-->
        <td><input type="text"  name="damaged_carrier[]"   style="width:80px"  /></td><!--物流损坏-->
        <td><input type="text"  name="damaged_warehouse[]"   style="width:80px"  /></td><!--仓库损坏-->
        <td><input type="text"  name="damaged[]"   style="width:80px" /></td><!--其它损坏-->
   </tr>
<?php } ?>
<?php } ?>
</table>
<br>
<br>
<!--提交按钮-->
<table width="" cellpadding="3" cellspacing="0">
  <tr>
    <td>
    <input type="hidden" name="order_id" value="<?php echo $order_id ?>" >
    <input  type="submit"  value="" style="background-image:url('./staticment/images/sure.gif');" id="subinput">
    <input type="reset" style="background-image:url('./staticment/images/reset.gif');" value="" id="subinput">
    </td>
  </tr>
</table>
</form>
<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-13 10:47:53, compiled from template/adminweb/process_recstock.tpl */ ?>
<!--采购入库模板-->
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script type="text/javascript">
function goosubmit()//防止重复提交
{
	$('.subonce').attr('disabled',true);
}
</script>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body			{ font-family: Arial,Helvetica,sans-serif;}
.commomform		{ color: #00004F;font-size:14px;}
.tips			{ color:#c6a8c6; font-size:14px;}
.big 			{ font-size: 16px}
.point 			{ cursor:pointer;}
#subinput		{ width:82px; height:30px; border:none; cursor:pointer;}
#shipfare		{ border: double #CCCCFF 1px; width:100px;}
#supplier 		{ margin-left:0px; position:absolute;}
#suplist 		{ font-size:14px;}
#mytable input 	{ border-left: 1px solid #C2C2C2; border-right: 1px solid #EAEAEA; border-top: 1px solid #C2C2C2;	border-bottom:1px solid #eeeeee;height:25px;}
#shipfare		{ border-left: 1px solid #C2C2C2; border-right: 1px solid #EAEAEA; border-top: 1px solid #C2C2C2;	border-bottom:1px solid #eeeeee;}
#bannerstr		{ background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:530px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;}
.commomform input,#commomform select{width:200px;height:25px;border: double #CCCCFF 1px; font-size:14px}
</style>

<!--填写资料部分-->
<form method="POST" action="index.php?action=process_recstock&detail=<?php echo $moddetail ?>"  onSubmit="return goosubmit()">

<?php if ($datalist) { ?>
<div style=" font-size:12px">到付运费：<input type="text" name="shipfare"  id="shipfare"/>
<span style="color:red;">&nbsp; (订单运费到付的，请在此如实填写运费，否则留空不填，币别<?php echo $datalist['0']['coin_code'] ?>)</span>
</div>
<?php } ?>

<?php if ($bannerstr) { ?>
<div id="bannerstr">
<?php echo $bannerstr ?>
</div>
<?php } ?>

<div style="height:10px;"></div>

<input type="hidden" name="page" value="<?php echo $page ?>" />
<table id="mytable" cellspacing="0" width="1150">
	<tr>
    	<th class="list" width="50">行号</th>
        <th class="list" width="100">产品SKU</th>
        <th class="list" width="250">产品名称</th>
        <th class="list" width="100">入库仓库</th>        
        <th class="list" width="80">订单数量</th>
        <th class="list" width="80">入库数量</th>
        <th class="list" width="150">备注</th>
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
        <td><input type="text"  name="" style="width:50px" value="<?php echo $val['quantity'] ?>" disabled></td><!--订单数量-->
        <td><input type="text"  name="quantity[]" style="width:80" value="<?php echo $val['quantity']-$val['countnum'] ?>" ></td><!--入库数量-->
        <td><input type="text"  name="comment[]" style="width:160px" value="" ></td><!--备注-->
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
    <input  type="submit"  value="" style="background-image:url('./staticment/images/sure.gif');" id="subinput" class="subonce">
    <input type="reset" style="background-image:url('./staticment/images/reset.gif');" value="" id="subinput">
    </td>
  </tr>
</table>
</form>
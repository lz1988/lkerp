<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-13 10:46:36, compiled from template/adminweb/process_editstock.tpl */ ?>
<!--采购下单与编辑模板，共用-->
<link rel="stylesheet" type="text/css" href="./staticment/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="./staticment/css/thickbox.css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>
<script language="javascript" type="text/javascript" src="./staticment/js/My97DatePicker/WdatePicker.js"></script>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body{font-family: Arial,Helvetica,sans-serif;}
.commomform{color: #00004F;font-size:12px;}
.tips{color:#c6a8c6; font-size:12px;}
.commomform input,#commomform select{width:180px;height:22px;border: double #CCCCFF 1px; font-size:12px}
.big {font-size: 16px}
.point { cursor:pointer;}
#subinput{background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px; border:none;cursor:pointer; margin:2px;}
#mytable input,select,#supplier,#shipping { height:23px;border-left: 1px solid #C2C2C2;border-right: 1px solid #EAEAEA;border-top: 1px solid #C2C2C2;border-bottom:1px solid #eeeeee;}
select 		{ width:160px;}
#supplier 	{ margin-left:0px; position:absolute;}
#shipping 	{ width:100px; background:url('./staticment/images/rmb.jpg') no-repeat; padding-left:20px; height:23px;width:70px;}
#sure		{ background:url(./staticment/images/button_bj_2.gif) no-repeat;border:none;cursor:pointer; margin:2px; width:50px; height:22px;}

</style>
<script type="text/javascript">

/*减少一行*/
function delrow(obj){
	$('#row_'+obj).parent().parent().remove();
}

/*保留两位小数*/
function cuttwo(obj){
		return Math.round((Math.floor(obj*1000)/10))/100;
}

/*自动计算价税合计*/
function countsum(row){
	var qval = $('input[id=quantity_'+row+']').val();
	var pval = $('input[id=price_'+row+']').val();
	var allvar = cuttwo(qval*pval);
	$('input[id=e_sprice_'+row+']').attr('value',allvar);
	$('input[id=e_siprice_'+row+']').attr('value',allvar);
}

/*检测表单*/
function checkform(){	
	
	var isempty = 0;

	if($('#supplier').val() == '') {alert('请填写供应商');$('#supplier').focus();return false;}
	if($('select[name=coin_code]').val() == '') {alert('请选择币别!');return false;}

	$('select[name=receiver_id[]]').each(function(){
		if($(this).val() == '') {alert('请选择入库仓库!');$(this).focus();isempty = 1;return false;}
	})
	
	if(isempty == 0){	
		$('input[name=e_sprice[]]').each(function(){
			if($(this).val() == '') {alert('请填写不含税合计！');$(this).focus();isempty = 1;return false;}
		});
	}
	
	if(isempty == 0){	
		$('input[name=e_siprice[]]').each(function(){
			if($(this).val() == '') {alert('请填写价税合计！');$(this).focus();isempty = 1;return false;}
		});
	}
	
	if(isempty == 1) return false;
}
</script>

<form method="POST" action="index.php?action=process_modstock" onsubmit="return checkform()" >
<input type="hidden" name="detail" value="<?php echo $moddetail ?>" />
<input type="hidden" name="suppliername" value="<?php echo $supplier ?>">
<input type="hidden" name="order_id" value="<?php echo $order_id ?>" />
<!--头部表格-->
<table width="" cellpadding="3" cellspacing="0" class="commomform" border="0">
  <tr>
    <td width="">
    	<font size=-1>供应商：<input type='text'  name="supplier"  value="<?php echo $supplier ?>" id="supplier" disabled="disabled"  title="<?php echo $supplier ?> (供应商由备货人选择，如需更改请联系备货人)" />&nbsp; &nbsp; 
        <span style="margin-left:200px;">币别：<?php echo $currencyhtml; ?></span>
		<span style="margin-left:30px;">运费：<input type="text" title="" id="shipping" name="shipping" value="<?php echo $shipping ?>"/></span>
        </font>
    </td>
  </tr>
</table>
<br />
<!--填写资料部分-->
<table id="mytable" cellspacing="0" width="1500">
	<tr>
    	<th class="list" width="50">行号</th>
        <th class="list" width="100">产品SKU</th>
        <th class="list" width="200">产品名称</th>
        <th class="list" width="150">入库仓库</th>
        <th class="list" width="50">数量</th>
        <th class="list" width="50">单价</th>
        <th class="list" width="100">不含税合计</th>
        <th class="list" width="100">价税合计</th>
        <th class="list" width="100">交货日期</th>
        <th class="list" width="200">备注</th>
    </tr>
<?php if ($datalistt) { ?>
<?php $floor=0 ?>
<?php foreach ($datalistt as $val) { ?>
<?php $floor++ ?>
	<tr>
        <td><span id="row_<?php echo $floor ?>" class="big"><?php echo $floor ?></span>
            <span title=删除 class=point onclick=delrow('<?php echo $floor ?>')>
            <img src="./staticment/images/deletebody.gif" border="0">
            <input type="hidden" name="detail_id[]" value="<?php echo $val['detail_id'] ?>" /><input type="hidden" name="id[]" value="<?php echo $val['id'] ?>" /></td>
        <td><input type="text"  name="" style="width:100px" value="<?php echo $val['sku'] ?>"  disabled></td><!--产品SKU-->
        <td><input type="text"  name="" style="width:200px" value="<?php echo $val['product_name'] ?>"  title="<?php echo $val['product_name'] ?>" disabled></td><!--产品名称-->
        <td><?php echo $val['sourcehtml'] ?></td><!--入库仓库-->
        <td><input type="text"  name="quantity[]" style="width:50px"  value="<?php echo $val['quantity'] ?>"  onblur=countsum("<?php echo $floor ?>") id="quantity_<?php echo $floor ?>" ></td><!--数量-->
        <td><input type="text" style="width:50px"  onblur=countsum("<?php echo $floor ?>")  value="<?php echo $val['price'] ?>" id="price_<?php echo $floor ?>"  title="单价并不保存用户填写，只是方便计算合计！" /></td>
        <td><input type="text"  name="e_sprice[]"  value="<?php echo $val['e_sprice'] ?>" style="width:100px" id="e_sprice_<?php echo $floor ?>" /></td><!--不含税合计-->
        <td><input type="text"  name="e_siprice[]" style="width:100px"  value="<?php echo $val['e_siprice'] ?>" id="e_siprice_<?php echo $floor ?>" /></td><!--价税合计-->
        <td><input type="text"  name="e_recdate[]"  value="<?php echo $val['e_recdate'] ?>" style="width:100px"  onClick="WdatePicker()"  class="Wdate" /></td><!--交货日期-->
        <td><input type="text"  name="comment[]"  value="<?php echo $val['comment'] ?>" style="width:200px"/></td><!--备注-->
   </tr>
<?php } ?>
<?php } ?>
</table>
<br />
<!--提交按钮-->
<table width="" cellpadding="3" cellspacing="0">
  <tr>
    <td>
    <input  type="submit"  value="保 存"  id="subinput">
    <input type="reset" value="重 置" id="subinput">
    </td>
  </tr>
</table>
</form>
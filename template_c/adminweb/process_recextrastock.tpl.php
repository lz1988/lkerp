<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-12-31 15:19:54, compiled from template/adminweb/process_recextrastock.tpl */ ?>
<!--其它出入库共用模板-->
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<link href="./staticment/css/datalist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body{font-family: Arial,Helvetica,sans-serif;}
.tips{color:#c6a8c6; font-size:14px;}
.big {font-size: 16px}
.point { cursor:pointer;}
.button {background:url('./staticment/images/button_bj.gif') no-repeat; width:75px; height:22px; border:none;cursor:pointer; margin:2px 4px 2px 0;}
#subinput{width:82px; height:30px; border:none; cursor:pointer;}
#mytable input,#mytable select{
	height:25px;
	background: url("./staticment/images/input_bg.gif") no-repeat scroll left top transparent;
	border-left: 1px solid #C2C2C2;
    border-right: 1px solid #EAEAEA;
    border-top: 1px solid #C2C2C2;
	border-bottom:1px solid #eeeeee;}


</style>
<script type="text/javascript">

/*增加一行*/
function addrow(){
	
	var tdstr='',maxrow=0;
	
	$('span[id^=row_]').each(function(){maxrow = maxrow<parseInt($(this).html())?parseInt($(this).html()):maxrow;})	
	maxrow+=1;		

	tdstr+='<td><input type="text"  name="sku[]" style="width:100px" onblur="get_pid('+maxrow+')" id="sku_'+maxrow+'"><input type="hidden"  value="" name="pid[]" id="pid_'+maxrow+'"/></td>';//产品sku
	tdstr+='<td><input type="text" name="product_name[]" id="product_name_'+maxrow+'"  value="" style="width:100%"/></td>';//名称
	tdstr+='<td><input type="text"  name="quantity[]" style="width:40px" ></td>';//数量
	//tdstr+='<td><input type="text"  name="price[]" style="width:50px" title="不填则不改变系统原成本价"></td>';//单价	
	tdstr+='<td><?php echo $whouse ?></td>';//入库仓库
	tdstr+='<td><input type="text"  name="comment[]" style="width:100%"></td>';//备注

	$('#mytable').append('<tr><td><span class=big id=row_'+maxrow+'>'+maxrow+'</span><span title=删除 class=point onclick=delrow('+maxrow+')><img src="./staticment/images/deletebody.gif" border="0"></span></td>'+tdstr+'</tr>');
}


/*减少一行*/
function delrow(obj){
	$('#row_'+obj).parent().parent().remove();
}


/*失去SKU焦点时获取产品SKU*/
function get_pid(row){
	var sku,obj;
	obj = $('input[id=sku_'+row+']');
	sku = obj.val();
	
	$.getJSON('index.php?action=process_shipment&detail=get_pid',{'sku':sku},function(msg){
																					  
		$('input[id=pid_'+row+']').attr('value',msg.pid);
		$('input[id=product_name_'+row+']').attr('value',msg.product_name);

	});
}

</script>
<form method="POST" action="<?php echo $jump_action ?>" >
<!--填写资料部分-->
<input type="button"  onclick="window.location='index.php?action=process_recstock&detail=import_extra&type=<?php echo $type ?>' " value="批量导入"  class='button'/>
<table id="mytable" cellspacing="0" width="1100">
	<tr>
    	<th class="list" width="35">行号</th>
        <th class="list" width="100">SKU</th>
        <th class="list" width="200">产品名称</th>
        <th class="list" width="50">数量</th>
       <!-- <th class="list" width="50">价值</th>-->
        <th class="list" width="110"><?php echo $in_or_out_whouse ?></th>
        <th class="list" width="150">备注</th>        
    </tr>
	<tr>
<td><span id="row_1" class="big">1</span><span title=删除 class=point onclick=delrow('1')><img src="./staticment/images/deletebody.gif" border="0"></td>
        <td><input type="text"  name="sku[]" style="width:100px"  onblur="get_pid(1)" id="sku_1"><input type="hidden"  value="" name="pid[]" id="pid_1"/></td>
        <td><input type="text" name="product_name[]" id="product_name_1"  value="" style="width:100%"/></td>
        <td><input type="text"  name="quantity[]" style="width:40px" ></td>
        <!--<td><input type="text"  name="price[]" style="width:50px" title="不填则不改变系统原成本价"></td>-->
        <td><?php echo $whouse ?></td>
        <td><input type="text"  name="comment[]"  style="width:100%"></td><!--备注-->
   </tr>
</table>
<input type="button" value="加一行" onclick="addrow()" class="button"/>&nbsp;&nbsp;
<br>
<br>
<!--提交按钮-->
<table width="" cellpadding="3" cellspacing="0">
  <tr>
    <td>
    <input  type="submit"  value="" style="background-image:url('./staticment/images/sure.gif');" id="subinput">
    <input type="reset" style="background-image:url('./staticment/images/reset.gif');" value="" id="subinput">
    </td>
  </tr>
</table>
</form>
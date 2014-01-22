<!--不良品调拨模板-->
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body{font-family: Arial,Helvetica,sans-serif;}
.tips{color:#c6a8c6; font-size:14px;}
.big {font-size: 16px}
.point { cursor:pointer;}
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
	tdstr+='<td><input type="text" name="product_name[]" id="product_name_'+maxrow+'"  value="" style="width:100%" /></td>';//名称
	tdstr+='<td><!--{echo $whouse}--></td>';//不良品归属仓	
	tdstr+='<td><input type="text"  name="defective_customer[]"   style="width:80px" /></td>';//客户损坏
	tdstr+='<td><input type="text"  name="damaged_distributor[]"   style="width:80px"  /></td>';//<!--供应商损坏-->
	tdstr+='<td><input type="text"  name="damaged_carrier[]"   style="width:80px"  /></td>';//<!--物流损坏-->
	tdstr+='<td><input type="text"  name="damaged_warehouse[]"   style="width:80px"  /></td>';//<!--仓库损坏-->
	tdstr+='<td><input type="text"  name="damaged[]"   style="width:80px"  /></td>';//<!--其它损坏-->
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
<form method="POST" action="index.php?action=process_bad&detail=moddamae" >
<!--填写资料部分-->
<table id="mytable" cellspacing="0" width="1250">
	<tr>
    	<th class="list" width="35">行号</th>
        <th class="list" width="100">SKU</th>
        <th class="list" width="100">产品名称</th>
        <th class="list" width="115">不良品归属仓</th>
        <th class="list" width="80">客户损坏</th>
        <th class="list" width="80">供应商损坏</th>
        <th class="list" width="80">物流损坏</th>
        <th class="list" width="80">仓库损坏</th>
        <th class="list" width="80">其它损坏</th>  
        <th class="list" width="100">备注</th>        
    </tr>
	<tr>
<td width="60"><span id="row_1" class="big">1</span><span title=删除 class=point onclick=delrow('1')><img src="./staticment/images/deletebody.gif" border="0"></td>
        <td><input type="text"  name="sku[]" style="width:100px"  onblur="get_pid(1)" id="sku_1" style="width:100%"><input type="hidden"  value="" name="pid[]" id="pid_1"/></td>
        <td><input type="text" name="product_name[]" id="product_name_1"  value="" style="width:100%"/></td>
        <td><!--{echo $whouse}--></td>
        <td><input type="text"  name="defective_customer[]"   style="width:80px" /></td><!--客户损坏-->
        <td><input type="text"  name="damaged_distributor[]"   style="width:80px"  /></td><!--供应商损坏-->
        <td><input type="text"  name="damaged_carrier[]"   style="width:80px"  /></td><!--物流损坏-->
        <td><input type="text"  name="damaged_warehouse[]"   style="width:80px"  /></td><!--仓库损坏-->
        <td><input type="text"  name="damaged[]"   style="width:80px"  /></td><!--其它损坏-->
        <td><input type="text"  name="comment[]" style="width:100%"></td><!--备注-->
   </tr>
</table>
<input type="button" value="增加" onclick="addrow()"/>
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
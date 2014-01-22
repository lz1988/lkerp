<script src="./staticment/js/upImage_stock.js" type="text/javascript" charset="UTF-8"></script>
<script src="./staticment/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
/*检查图片是否上传*/
function checkimg(){
	var exsist = $('input[name=img_url[]]').val();
	if(!exsist){alert("未上传图片！");	return false;}
}

$(function(){
	$('.qick span').mouseover(function(){
		$(this).css('background','#c6a8c6');
	}).mouseout(function(){
		$(this).css('background','#bdbdbd');
	}).click(function(){
		var src = 'data/stockorder/common/'+$(this).attr('id')+'.jpg';
		var str = "<div style='width:116px;height:125px;float:left;padding:3px;padding-top:15px;'>";
	        str+= "<div style='float:left'><img src="+src+" style='border:solid 1px #828482; width:115px;height:115px;'></div>";
	        str+= "<div style='float:left;'>";
			str+= "<input type='hidden' name='img_url[]' value="+src+" >";
			str+= "&nbsp;<span title='delete' style='cursor:pointer;color:#828482;' onclick=$(this).parent().parent().remove();>&times;</span>";
			str+= "</div></div>";
		$('#exists').append(str);
	});
})
</script>
<style type="text/css">
.qick span{	padding:5px 8px 5px 8px;background:#bdbdbd;margin-right:5px;cursor:pointer;}
</style>
<form action="index.php?action=process_modstock&detail=modupspay" method="post" >

<table cellpadding="0" cellspacing="0" border="0">
<tr>
 <td>
	<? include './staticment/dynamic/upFrame_stock.php'?><div id="show" style="width:500px; text-align:left;margin-left:20px;font-size:12px;"></div>
  </td>
</tr>
<tr>
	<td>
    	<div style="width:500px; text-align:left;margin-left:20px; margin-top:20px;font-size:12px;" class="qick">&nbsp;快速选择：<span id="week">周结</span><span id="half_month">半月结</span><span id="month">月结</span><span id="cash">现金已付</span><span id="express">快递代收</span></div>		
    </td>
</tr>
<tr>
	<td>
    	<div style="width:500px; text-align:left;margin-left:20px; margin-top:20px;font-size:12px;" id="exists">&nbsp;已有的图片：<br/>
			<!--{echo $backstr}-->
		</div>		
    </td>
</tr>
<tr>
	<td height="5">&nbsp;</td>
</tr
><tr>
	<td><div style="margin-left:22px; margin-bottom:15px; font-size:12px"><!--{echo $showstr}--></div>
		<div style='margin-left:22px; float:left'><input type="image" src="./staticment/images/SaveIcon.gif" /></div>
    </td>
</tr>
</table>
<input  type="hidden" value="<!--{echo $order_id}-->"  name='order_id'>
</form>
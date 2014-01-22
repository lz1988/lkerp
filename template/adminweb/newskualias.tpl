<!--{echo $jslink}-->
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body{font-family: Arial,Helvetica,sans-serif;}
.tips{color:#c6a8c6; font-size:14px;}
input,select{
	width:140px; height:22px;font-size: 12px; margin-top:5px;
	border-left: 1px solid #C2C2C2;
    border-right: 1px solid #EAEAEA;
    border-top: 1px solid #C2C2C2;
	border-bottom:1px solid #eeeeee;
	background: url("./staticment/images/input_bg.gif") no-repeat scroll left top transparent;}	
#submit	{ background:url('./staticment/images/button_bj.gif') no-repeat; width:75px; height:22px; border:none;cursor:pointer; margin:2px;}
.subadd,.subdel{
	background:url(./staticment/images/button_bj.gif) -2px 0 no-repeat;
	border:none;
	cursor:pointer;
	border-left:#B0B0B0 1px solid;
	border-right:#B0B0B0 1px solid;}
#add,#del,#checksku{
	background:url(./staticment/images/button_bj_2.gif) no-repeat;
	width:75px; height:22px;
	border:none;cursor:pointer; margin:2px;
}	
.subadd,.subdel{ width:22px;}
#add,#del,#checksku{ width:50px;}
</style>
<script type="text/javascript">
/*var cssobj = {
	'background':'url("./staticment/images/button_bj.gif") -2px 0 no-repeat',
	'border':'none',
	'cursor':'pointer',
	'border-left':'#B0B0B0 1px solid',
	'border-right':'#B0B0B0 1px solid',								
};
$(function(){
	$('input[type=button]').css(cssobj);
})
*/
/*所有输入框值的数组*/
var sku_code_array = new Array();
/*原名SKU数组*/
var sku_array = new Array();
/*确认按钮判定 0:提交 非0:有重复*/
var sku_submit = 0;
$(function(){
	/*添加一行新SKU*/
	$('#add').click(function(){		
		var index = $('.sku_table').children().children('tr').index($('.sku_table').children().children('tr').last());		
		var sku = $('<input name="pro_sku['+index+']" type="text" />');
		var sku_code = $('<input name="sku_code['+index+'][]" type="text" />');
		var add = $('<input type="button" value="+" class="subadd"/>');
		var del = $('<input type="button" value="-" class="subdel"/>');

		var sku_td = $('<td valign="top"></td>').append(sku);
		var sku_code_td = $('<td></td>').append(sku_code).append('&nbsp;').append(add).append('&nbsp;').append(del).append(' ');

		var tr = $('<tr class="additem"></tr>').append(sku_td).append(sku_code_td);
		$('.sku_table').append(tr);
	});

	/*删除一行SKU*/
	$('#del').click(function(){
		$('.additem').last().remove();
	});

	/*添加一条别名SKU*/
	$('.subadd').live('click', function(){
		var index = $('.sku_table').children().children('tr').index($(this).parent().parent()) - 1;
		$(this).before('<input name="sku_code['+index+'][]" type="text" class=addp /> ');
	});

	/*删除一调别名SKU*/
	$('.subdel').live('click', function(){
		$(this).prevAll().filter('.addp').first().remove();
	});

	$('input[type=text]').live('focus', function(){
		$(this).css('color', '#000000');
		$('#submit').hide();
	});

	/*检测按钮单击事件*/
	$('#checksku').click(function() {
		sku_code_array = new Array();
		sku_array = new Array();
		sku_submit = 0;
		
		/*
			是否选择sku别名使用组
		*/
		if ($('select').val() == '') {
			alert('请选择sku别名使用组！');
			$('select').focus();
			$('select').select();
			return false;
		}
		
		/*产品SKU值加入数组，重复不提示，不加入数组
			需要加入产品sku
		*/
		$('input[name^=pro_sku]').each(function(){
			var i = 0;
			for(i=0; i<sku_array.length; i++) {
				if (sku_array[i] == $(this).val()) {
					break;
				}
			}
			if (i==sku_array.length && $(this).val() != '') {
				sku_array[i] = $(this).val();
			}
		});
		/*SKU别名加入数组，有重复时提示，不加入数组*/
		/*$('input[name^=sku_code]').each(function(){
			var i = 0;
			$(this).css('color', '#000000');
			for(i=0; i<sku_code_array.length; i++) {
				if (sku_code_array[i] == $(this).val()) {
					$(this).css('color', 'red');
					sku_submit++;
					break;   
				}
			}
			if (i==sku_code_array.length && $(this).val() != '') {
				sku_code_array[i] = $(this).val();
			}
			for(i=0; i<sku_array.length; i++) {
				if (sku_array[i] == $(this).val()) {
					$(this).css('color', 'red');
					sku_submit++;
					break;
				}
			}
		});*/
        
		if(!sku_submit) {
			$.getJSON("index.php?action=sku_alias&detail=checkallsku",
				{"pro_sku":sku_array,"sku_code":sku_code_array},
				function(msg){
	   				if(msg.length) {
						$('input[type=text]').each(function(){
							for(i=0; i<msg.length; i++) {
								if(msg[i] == $(this).val()) {
									$(this).css('color', 'red');
								}
							}
						});
						alert('数据库没有产品SKU\n或有重复SKU别名！');
	   				}
	   				else {
	   					$('#submit').show();
	   				}
	   			}
	   		);
		}
		/*else {
			alert('输入框有重复SKU别名！');
		}*/
	});
});
</script>

<form id="skuform" action="<!--{echo $jumpurl}-->" method="post">
	<div style="font-size:12px; margin-bottom:10px; margin-top:10px; padding-left:3px;">别名使用组：<!--{echo $sold_wayselect}--></div>
	<table id="mytable" class="sku_table">
		<tr>
			<th class="list" width="160"> SKU </th>
			<th class="list"> SKU别名 </th>
		</tr>
		<tr>
			<td valign="top">
				<input name="pro_sku[0]" type="text" />
			</td>
			<td>
				<input name="sku_code[0][0]" type="text" /> <input class='subadd' type="button" value="+" /> <input class='subdel' type="button" value="-" />
			</td>
		</tr>
	</table>
	<input id='add' type="button" value="添加" />
	<input id='del' type="button" value="删除" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" value="检测..." id="checksku"  />
	<input id="submit" type="submit" value="提交" style="display:none;" />
</form>
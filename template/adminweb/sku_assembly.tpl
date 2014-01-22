<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SKU组装与拆分</title>
<style type="text/css">
.assembly_condition { font-size:12px;font-family: Arial,Helvetica,sans-serif;}
.assembly_condition input{border: double #E3EDFE 1px; font-size: 12px; height:22px;}
.assembly_condition select {border: double #E3EDFE 1px; font-size: 12px;height:22px;}
.wall_submit,.wall_check,.wall_addrow,.select_sumbit {background:url('./staticment/images/button_bj.gif') no-repeat; width:75px; height:22px; border:none !important;cursor:pointer; margin:2px;} 
input { outline:none;}

</style>
<link href="./staticment/css/editTable.css" rel="stylesheet" type="text/css" />
</head>
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script type="text/javascript">
/*保留两位小数*/
function cuttwo(obj){
	return Math.round((Math.floor(obj*1000)/10))/100;
}

/* 产生拆分td非编辑模式*/
function splittddiv(classname, value) {
	var result = value + '<input type="hidden" name="<!--{echo $prefix_split}-->' + classname + '<!--{echo $suffix_split}-->" value="' + value + '" />';
	return result;
}

/* 产生组装td非编辑模式*/
function assembletddiv(classname, value) {
	var result = value + '<input type="hidden" name="<!--{echo $prefix_assemble}-->' + classname + '<!--{echo $suffix_assemble}-->" value="' + value + '" />';
	return result;
}

//统计总价
function statistics_price() {
	var price = 0;	
	var totalprice = 0;
	var total = 0;
	if ($('.assemble_sku .total').children('input').val()) {
		total = parseInt($('.assemble_sku .total').children('input').val());
	}
	
	$('.split_sku tr').each(function() {
		if ($(this).children('.totalprice').children('input').val() && !isNaN($(this).children('.totalprice').children('input').val())) {
			splitprice = cuttwo(parseFloat($(this).children('.price').children('input').val()));
			splittotalprice = cuttwo(splitprice * total);
			$(this).children('.total').html(splittddiv('total', total * parseInt($(this).children('.quantity').children('input').val())));
			$(this).children('.totalprice').html(splittddiv('totalprice', splittotalprice));
			price += splitprice;
			totalprice += splittotalprice;
		}
	});
	$('.assemble_sku .cost').html(assembletddiv('cost', cuttwo(price)));	
	$('.assemble_sku .total').html(assembletddiv('total', total));
	$('.assemble_sku .totalprice').html(assembletddiv('totalprice', cuttwo(totalprice)));
}

//需要首先通过Javascript来解决内容部分奇偶行的背景色不同
//$(document).ready(function(){
//	
//});
//简化的ready写法
$(function(){		
	//隐藏提交按钮
	$('input[type=submit]').hide();
	//我们需要找到所有的可编辑单元格
	var numTd = $(".edittd");
	//给这些单元格注册鼠标点击的事件
	numTd.live('click',function() {	
		if ($('select[name=warehouse]').val()=='') {
			alert('请先选择仓库');
			return false;
		}
		//每次修改表格，隐藏提交按钮，要重新检验
		$('input[type=submit]').hide();
		//找到当前鼠标点击的td,this对应的就是响应了click的那个td
		var tdObj = $(this);
		if (tdObj.children("input[type=text]").length > 0) {
			//当前td中input，不执行click处理
			return false;
		}
		var text = tdObj.children('input').val(); 
		var classname = tdObj.children('input').attr('name'); 
		//清空td中的内容
		tdObj.html("");
		//创建一个文本框
		//去掉文本框的边框
		//设置文本框中的文字字体大小是16px
		//使文本框的宽度和td的宽度相同
		//设置文本框的背景色
		//需要将当前td中的内容放到文本框中
		//将文本框插入到td中
		var inputObj = $("<input type='text'>").css("border-width","0")
			.css("font-size","16px").width(tdObj.width())
			.css("background-color",tdObj.css("background-color"))
			.val(text).appendTo(tdObj);
		//是文本框插入之后就被选中
		inputObj.trigger("focus").trigger("select");
		inputObj.click(function() {
			return false;
		});		
		
		//编辑框筛选非数字
		if (tdObj.attr('extend') == "num") {			
			inputObj.keyup(function() {							
				$(this).val($(this).val().replace(/[^\d]/g,""));	
			});			
		}
		//编辑框丢失焦点
		inputObj.blur(function() {			
			//编辑框内容
			var inputtext = $.trim($(this).val());
			//输入框若输入数字（即数量），空值时，赋默认值1
			if (inputtext=='') {
				inputtext = text;
			}
			//仓库ID
			var warehouseid = $('select[name=warehouse]').find("option:selected").val();			
			//提交使用的隐藏框
			var hiddeninput = inputtext + '<input type="hidden" name="' + classname + '" value="' + inputtext + '" />';
			tdObj.html(hiddeninput);		
			
			//获取当前行数
			var trObj = tdObj.parents('tr');
			//校验SKU获取相应值
			if (tdObj.attr('other') == 'sku') {
				//判断为哪一个表格，拆分表||组装表
				//拆分表
				if (classname != 'as_sku') {	
					$.getJSON('index.php?action=sku_assembly&detail=checksplitsku',{'sku':inputtext, 'wid':warehouseid},function(msg){				
						if(msg.pid){
							trObj.children('.status').html('&radic;&nbsp;存在');								
							trObj.children('.product_name').html(splittddiv('product_name', msg.product_name));						
							trObj.children('.cost').html(splittddiv('cost', msg.cost2));			
							trObj.children('.quantity').html(splittddiv('quantity', 1));
							trObj.children('.price').html(splittddiv('price', msg.cost2));	
							trObj.children('.stocks').html(splittddiv('stocks', msg.stocks?msg.stocks:0));		
							trObj.children('.total').html(splittddiv('total', 1));
							trObj.children('.totalprice').html(splittddiv('totalprice', msg.cost2));	
							trObj.children('.comment').html(splittddiv('comment', $('.assemble_sku .comment').children('input').val()));		
						}
						else{
							trObj.children('.product_name').html(splittddiv('product_name', ""));						
							trObj.children('.cost').html(splittddiv('cost', ""));			
							trObj.children('.quantity').html(splittddiv('quantity', ""));
							trObj.children('.price').html(splittddiv('price', ""));	
							trObj.children('.stocks').html(splittddiv('stocks', ""));		
							trObj.children('.total').html(splittddiv('total', ""));
							trObj.children('.totalprice').html(splittddiv('totalprice',""));
							trObj.children('.comment').html(splittddiv('comment', ""));	
							trObj.children('.status').html('&times;&nbsp;不存在');
						}	
						statistics_price();		
					});	
				}
				//组装表
				else {						
					$.getJSON('index.php?action=sku_assembly&detail=checkassemblesku',{'sku':inputtext, 'wid':warehouseid},function(msg){									
						if(msg == -1){
							trObj.children('.status').html('&times;SKU不能组装');			
							trObj.children('.product_name').html(assembletddiv('product_name', "")).removeClass('edittd');						
							trObj.children('.cost').html(assembletddiv('cost', ""));				
							trObj.children('.stocks').html(assembletddiv('stocks', 0));		
							trObj.children('.total').html(assembletddiv('total', "")).removeClass('edittd');
							trObj.children('.totalprice').html(assembletddiv('totalprice',""));														
						}
						else if (msg == 0){
							trObj.children('.product_name').html(assembletddiv('product_name', "")).addClass('edittd');						
							trObj.children('.cost').html(assembletddiv('cost', ""));				
							trObj.children('.stocks').html(assembletddiv('stocks', 0));		
							trObj.children('.total').html(assembletddiv('total', "")).addClass('edittd');
							trObj.children('.totalprice').html(assembletddiv('totalprice',""));	
							trObj.children('.status').html('&radic;&nbsp;不存在，可用');
							statistics_price();		
						}		
						else {
							trObj.children('.status').html('&radic;&nbsp;sku存在');								
							trObj.children('.product_name').html(assembletddiv('product_name', msg.product_name)).removeClass('edittd');						
							trObj.children('.cost').html(assembletddiv('cost', msg.cost2));	
							trObj.children('.stocks').html(assembletddiv('stocks', msg.stocks));						
							trObj.children('.total').html(assembletddiv('total', "")).addClass('edittd');
							trObj.children('.totalprice').html(assembletddiv('totalprice',""));		
							statistics_price();		
						}		
					});					
				}					
			}
			if (tdObj.attr('other') == 'quantity') {
				var quantity = parseInt(inputtext);
				var price = cuttwo(quantity * parseFloat(trObj.children('.cost').children('input').val()));
				var totalprice = cuttwo(price * parseInt(trObj.children('.total').children('input').val()));
				trObj.children('.price').html(splittddiv('price', price));	
				trObj.children('.totalprice').html(splittddiv('totalprice', totalprice));	
				statistics_price();
			}
			if (tdObj.attr('other') == 'total') {
				var quantity = parseInt(inputtext);
				var thistotalprice = cuttwo(quantity * parseFloat(trObj.children('.cost').children('input').val()));
				trObj.children('.totalprice').html(assembletddiv('totalprice',thistotalprice));
				$('.split_sku tr').each(function() {
					if ($(this).children('.totalprice').children('input').val() && !isNaN($(this).children('.totalprice').children('input').val())) {
						var totalprice = cuttwo(quantity * parseFloat($(this).children('.price').children('input').val()));	
						$(this).children('.total').html(splittddiv('total', quantity * parseInt($(this).children('.quantity').children('input').val())));
						$(this).children('.totalprice').html(splittddiv('totalprice', totalprice));	
					}
				});
			}
			if (tdObj.attr('other') == 'comment') {
				$('.split_sku tr').each(function() {
					if ($(this).children('.totalprice').children('input').val() && !isNaN($(this).children('.totalprice').children('input').val())) {						
						$(this).children('.comment').html(splittddiv('comment', inputtext));	
					}
				});
			}			
		});
		//处理文本框esc按键的操作
		inputObj.keypress(function(event){
			//获取当前按下键盘的键值
			var keycode = event.which;		
			//处理esc的情况
			if (keycode == 27) {
				//将td中的内容还原成text					
				var hiddeninput = text + '<input type="hidden" name="' + classname + '" value="' + text + '" />';
				tdObj.html(hiddeninput);
			}
		});		
	});
	
	//注销页面回车事件，防止按回车直接提交表单
		$('body').keypress(function(event){
			var keycode = event.which;
			if (keycode == 13) {
				return false;
			}
		});
	
	/* 删除当前行*/
	$('.wall_deleteline').live('click', function(){
		//隐藏提交按钮
		$('input[type=submit]').hide();
		$(this).parent().parent().remove();		
	});

	/* 添加一行，默认只有拆分SKU表中才有*/
	$('.wall_addrow').click(function(){
		var tableObj = $('.split_sku').children('table');
		tableObj.append(
			$('<tr></tr>').append($('<td></td>').append(splittddiv('sku', '')).attr('class', 'edittd sku').attr('other','sku'))
						.append($('<td></td>').append(splittddiv('product_name', '')).attr('class','product_name'))
						.append($('<td></td>').append(splittddiv('quantity', '')).attr('class', 'edittd quantity').attr('other','quantity').attr('extend','num'))
						.append($('<td></td>').append(splittddiv('cost', '')).attr('class', 'cost'))
						.append($('<td></td>').append(splittddiv('price', '')).attr('class', 'price'))
						.append($('<td></td>').append(splittddiv('stocks', '')).attr('class', 'stocks'))
						.append($('<td></td>').append(splittddiv('total', '')).attr('class', 'total'))
						.append($('<td></td>').append(splittddiv('totalprice', '')).attr('class', 'totalprice'))
						.append($('<td></td>').append(splittddiv('comment', '')).attr('class', 'comment'))
						.append($('<td></td>').attr('class', 'status'))
						.append($('<td></td>').append('<a class="wall_deleteline" href="javascript:void(0);">删除</a>').attr('class', 'oper'))
		);
	});
	
	/* 提交按钮，检验当前所有SKU是否重复，库存是否充足，sku是否存在，若不满足提交条件，不予提交*/
	$('.wall_submit').click(function(){	
	
		var status = '';	
		//检查所有表单不能为空
		//组装产品sku
		var assemblesku = $('.assemble_sku tr').children('.sku').children('input').val();			
		//拆装产品sku数组
		var splitsku = new Array();
		//拆装产品数量
		var splitnum = new Array();
		//当前拆分产品下标
		var arr_num = 0;
		//检验是否能显示提交
		var is_submit = 1;
		
		var temp = "";
		//检验数据是否全部填写
		$('.wall_loadmain tr input').each(function() {			
			if($(this).val() == '') {
				alert('请检查数据是否完整!');
				is_submit = 0;
				return false;
			}
		});
		
		//数据不全，重新填写，不进行下步检验
		if (!is_submit) {
			return false;
		}
		
		//当前操作是拆分还是组装: split拆分 assemble组装
		var subtype = $(this).attr('subtype');			
		
		//拆分条件下要求组装产品数量大于库存
		/*if (subtype=="split" && parseInt($('.assemble_sku tr').children('.stocks').children('input').val()) < parseInt($('.assemble_sku tr').children('.total').children('input').val())) {
			$('.assemble_sku tr').children('.status').html('库存不足');
			is_submit = 0;
		}*/
		
		//检验所有拆分产品
		$('.split_sku tr').each(function(i) {	
			
			//组装条件下，拆分产品数量大于库存
			/*if (subtype=="assemble" && parseInt($(this).children('.stocks').children('input').val()) < parseInt($(this).children('.total').children('input').val())) {				
				status += ' 库存不足'	;
				is_submit = 0;
			}*/
			
			//组装sku是否为组合sku
			if (subtype == "assemble"){
				_sku = $(this).children('.sku').children('input').val();
				$.post("index.php?action=sku_assembly&detail=assemble_type_sku", {'sku':_sku},
					function(data){
						if (data == "yes"){
							status = '组合sku';
							$('.split_sku tr').eq(i).children('.status').html(status);
							$("#flag").val('1');	
						}	
					}
				);
			}
			
			if ($(this).children('.quantity').children('input').val() == '0') {
				status += ' 产品数量不能为0';
				is_submit = 0;
			}
			var tempsku = $(this).children('.sku').children('input').val();				
			if (tempsku == assemblesku) {
				status += ' sku与组装产品冲突！';
				is_submit = 0;
			}
			else {
				var repeat = 0;
				for (var i = 0; i < arr_num; i++) {
					if (splitsku[i] == tempsku) {
						repeat = 1;
						is_submit = 0;
						status += ' sku有重复，请合并！';
					}
				}
				if (!repeat) {
					splitsku[arr_num] = tempsku;
					splitnum[arr_num] = $(this).children('.quantity').children('input').val();
					arr_num ++;
				}				
			}
			
			//显示当前行状态
			//alert($(this).children('.status').html());
			$(this).children('.status').html(status);	
		});
	
		//数据有误，重新填写，不进行下步检验
		if (!is_submit) {
			return false;
		}
		
		
		//当前为组装产品时，服务器检测组装方案 
		//-1:新定义组装方案（组装方案，产品都不存在）; 
		//0:新定义组装方案（组装方案不存在，产品存在）;
 		//正数:已经存在的组装方案;
		if (subtype=="assemble") {
			$.post(
				"index.php?action=sku_assembly&detail=assemble_type",
				{'sku':assemblesku, 'child_sku':splitsku, 'quantity':splitnum},
				function(msg){
					$('input[name=assembletype]').val(msg);
					$('input[type=submit]').show();
				}
			);
		}
		//当前为拆分产品时，服务器检测，
		if (subtype=="split") {
			$.post(
				"index.php?action=sku_assembly&detail=split_type",
				{'sku':assemblesku, 'child_sku':splitsku, 'quantity':splitnum},
				function(msg){
					if (msg == '0') {
						alert('输入方案不存在，请重新输入\n或通过检测方案生成');
					}
					else {	
						$('input[name=assembletype]').val(msg);					
						$('input[type=submit]').show();
					}
				}
			);
		}	

	});
	
	/* 根据输入sku检测可能的组装方案*/
	$('.wall_check').click(function() {
		var thischeck = $(this);
		var ispid = 0;		
		var is_warning = 0;
		var skuarray = new Array();
		var arr_num = 0;
		//取到该模块最上层div，中的表格
		var tableObj = $(this).parents('.wall_loadmain').children().children('#sku_sp_ass');
		
		//组装表检测，赋值为1
		if (thischeck.prev().attr('class') == 'wall_submit') {
			ispid = 1;
		}		
		//取表格中所有sku的值
		tableObj.children().children().children('.sku').each(function(){
			//sku存在的进行判断
			var tempsku = $(this).children('input').val();
			if (tempsku != '') {
				//sku重复，提示
				var repeat = 0;
				for (var i = 0; i < arr_num; i++) {
					if (skuarray[i] == tempsku) {
						repeat = 1;
						is_warning = 1;						
					}
				}
				if (!repeat) {
					skuarray[arr_num] = tempsku;					
					arr_num ++;
				}	
			}
		});
		if (is_warning) {
			alert('sku有重复，请检查！');
			return false;
		}
		$.post(
			"index.php?action=sku_assembly&detail=get_type",
			{'sku':skuarray, 'ispid':ispid},
			function(msg){
				thischeck.next().html('<option value="0">新方案</option>'); 
				if (msg == '0') {
					alert('无此类SKU组装方案！');
				}
				else {					
					thischeck.next().append(msg); 
				}				
			}
		);
	});
	
	//根据选择的方案显示到界面
	$('.assembletype').change(function() {
		var assembleid = $(this).val();
		var warehouseid = $('select[name=warehouse]').find("option:selected").val();
		if (assembleid == 0) {
			location.reload();
		}
		else { 
			$.post(
				"index.php?action=sku_assembly&detail=split_table",
				{'assembleid': assembleid, 'wid':warehouseid},
				function(msg){
					$('.split_sku').html(msg); 		
					statistics_price();					
				}
			);
			
			$.post(
				"index.php?action=sku_assembly&detail=assemble_table",
				{'assembleid': assembleid, 'wid':warehouseid},
				function(msg){
					$('.assemble_sku').html(msg);
					statistics_price();
				}
			);
			
		}
	});
	
	/* 拆分表*/
	$.post(
		"index.php?action=sku_assembly&detail=split_table",		
		function(msg){
			$('.split_sku').html(msg);
		}
	);
	
	/* 组装表*/
	$.post(
		"index.php?action=sku_assembly&detail=assemble_table",
		function(msg){
			$('.assemble_sku').html(msg);
		}
	);
	
	//仓库选择后变灰,并将仓库号放入warehouseid空间
	$('select[name=warehouse]').change(function() {
		$('input[name=warehouseid]').val($(this).val());
		$(this).attr('disabled','disabled');
	});
	
	
	
	//通过方案，仓库查询详细信息
	$('.select_sumbit').click(function() {
		if ($('select[name=warehouse]').val()=='') {
			alert('请先选择仓库');
			return false;
		}
		var assembleid = $('.select_assembletype').val();
		var warehouseid = $('select[name=warehouse]').find("option:selected").val();
		$.post(
			"index.php?action=sku_assembly&detail=split_table",
			{'assembleid': assembleid, 'wid':warehouseid},
			function(msg){
				$('.split_sku').html(msg); 		
				statistics_price();					
			}
		);
		
		$.post(
			"index.php?action=sku_assembly&detail=assemble_table",
			{'assembleid': assembleid, 'wid':warehouseid},
			function(msg){
				$('.assemble_sku').html(msg);
				statistics_price();
			}
		);
	});
});
function save(){
	var flag = $("#flag").val();
	if (flag == 1){
		alert("方案中子sku存在组合sku！");
		return false;
	}
}
</script>
<body>
<div class="assembly_condition">
	<form action="<!--{echo $form_action}-->" method="post" onsubmit="return save()">
	<div>
		<!--{foreach ($condition as $key=>$val)}-->
    		<!--{echo $val[label].$val[plug]}-->
        <!--{/foreach}-->
	</div>
	<!--{foreach ($classdiv as $key=>$val)}-->

	<div class="wall_loadmain" style="margin-top:20px; margin-bottom:10px;">
		<div class="<!--{echo $val[classname]}-->"></div>
		<div>
        
			<input type="button" class="<!--{echo $val['class']}-->" value="<!--{echo $val['value']}-->" <!--{echo $val['extend']}-->/>
			<input type="button" class="wall_check" value="方案检测" />
			<select class="assembletype">
				<option value="0">新方案</option>				
			</select>
		</div>
	</div>
	<!--{/foreach}-->	
    <input type="hidden" id="flag" value="0"/>
	<input type="hidden" name="assembletype" value="-1"/>
	<input type="hidden" name="warehouseid" /> 
	<input type="submit" value="保 存" style="background:url('./staticment/images/bg_button_blue.gif') no-repeat; width:78px; height:21px;border:none;cursor:pointer;"/>	
	</form>
</div>
</body>
</html>

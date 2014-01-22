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

.msg 	{ background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:600px;line-height:22px;padding-left:25px;color:green;margin:10px 0;}
.notice 	{ background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:600px;line-height:22px;padding-left:25px;color:red;margin:10px 0;}
</style>
<script type="text/javascript">
$(function(){

	$('.check').click(function(){
		var houseidarr 	= new Array();
		var skuarr		 	= new Array();
		var quantityarr		= new Array();
		var num = 0;
		var waring = 0;
		var notice = 0;	
			
		var index = $(this).index('input[name^=sku]');
		$('input[name^=sku]').each(function(){
			if($(this).val() != ''){
				quan = $('input[name^=quantity]').eq($(this).index('input[name^=sku]'));
				houseid = $('select[name^=houseid]').eq($(this).index('input[name^=sku]'));

				if(quan.val() == ''||parseInt(quan.val()) < 1){
					quan.css({'background':'red'});
					quan.attr('title','请填写数量！');
					waring++;
				}
				
				skuarr[num] = $(this).val();
				quantityarr[num] = quan.val();
				houseidarr[num]  = houseid.val();
				num++;
			}	
		})
		
		$('select[name^=houseid]').each(function(i) {

			if ($(this).val() == '') {
				$(this).eq(index).css({'background':'red'});
				$(this).eq(index).attr('title','请选择仓库！');
				waring++;
			
			}else{
				$(this).eq(index).css({'background':''});
				$(this).eq(index).attr('title','');
			}
		});
		//alert(skuarr);alert(quantityarr);alert(houseidarr);
		$.getJSON('index.php?action=process_transfer&detail=check_quantity',{'sku':skuarr,'quantity':quantityarr,'houseid':houseidarr},
		function(msg){
			if (msg.msg){alert(msg.msg);
			}else{
				var res = eval(msg);
				var relen = res.length;
				if(relen > 0){
					for (var i =0; i < relen; i++){
						if (res[i].quantity == -1) {
							$('input[name^=sku]').eq(res[i].num).css({'background':'red'});	
							$('input[name^=sku]').eq(res[i].num).attr('title','sku填写错误');			
							waring++;
						}else if (res[i].quantity == -2){
							$('input[name^=quantity]').eq(res[i].num).css({'background':'red'});
							$('input[name^=quantity]').eq(res[i].num).attr('title',res[i].skip);
							notice++;
						}else{
							$('input[name^=quantity]').eq(res[i].num).css({'background':''});
							$('input[name^=quantity]').eq(res[i].num).attr('title','');
							$('input[name^=sku]').eq(res[i].num).css({'background':''});
							$('input[name^=sku]').eq(res[i].num).attr('title','');	
						}
					}
				}
				
				if (waring) {
					$("#notice").addClass('notice');
					$("#notice").html("总共有 <b>"+waring+"</b> 处错误，请将鼠标移到红色处查看错误提示并修正。");
				}else {
				    $("#notice").removeClass('notice');
					$("#notice").html('');
					
				}
				
				if (notice){
					$("#msg").addClass('notice');
					$("#msg").html("温馨提示：存在库存不足的SKU,不予以提交！")
					
				}else{
					$("#msg").removeClass('notice');
					$("#msg").html('');
				}
				
				if (notice < 1 && waring < 1){
					$('input[type=submit]').css('display', '');		
				}
				
			}
		})
	})
	
	$('input[name^=quantity]').live('focus',function(){
		$(this).css({'background':''});
		$(this).attr('title','');
		$('input[type=submit]').css('display', 'none');
	})
	

	$('input[name^=sku],input[name^=product_name]').live('focus',function(){
		$('input[type=submit]').css('display', 'none');		
	})
	$('select[name^=houseid]').live('focus',function(){
		$(this).css({'background':''});
		$(this).attr('title','');
		$('input[type=submit]').css('display', 'none');		
	})
	
});

/*增加一行*/
function addrow(){
	
	var tdstr='',maxrow=0;
	
	$('span[id^=row_]').each(function(){maxrow = maxrow<parseInt($(this).html())?parseInt($(this).html()):maxrow;})	
	maxrow+=1;		

	tdstr+='<td><input type="text"  name="sku[]" style="width:100px" onblur="get_pid('+maxrow+')" id="sku_'+maxrow+'"><input type="hidden"  value="" name="pid[]" id="pid_'+maxrow+'"/></td>';//产品sku
	tdstr+='<td><input type="text" name="product_name[]" id="product_name_'+maxrow+'"  value="" style="width:100%"/></td>';//名称
	tdstr+='<td><input type="text"  name="quantity[]" style="width:40px" ></td>';//数量
	//tdstr+='<td><input type="text"  name="price[]" style="width:50px" title="不填则不改变系统原成本价"></td>';//单价	
	tdstr+='<td><!--{echo $whouse}--></td>';//入库仓库
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
<form method="POST" action="<!--{echo $jump_action}-->" >
<div id="notice"></div>
<div id="msg"></div>
<!--填写资料部分-->
<input type="button"  onclick="window.location='index.php?action=process_recstock&detail=import_extra&type=<!--{echo $type}-->' " value="批量导入"  class='button'/>
<table id="mytable" cellspacing="0" width="1100">
	<tr>
    	<th class="list" width="35">行号</th>
        <th class="list" width="100">SKU</th>
        <th class="list" width="200">产品名称</th>
        <th class="list" width="50">数量</th>
       <!-- <th class="list" width="50">价值</th>-->
        <th class="list" width="110"><!--{echo $in_or_out_whouse}--></th>
        <th class="list" width="150">备注</th>        
    </tr>
	<tr>
<td><span id="row_1" class="big">1</span><span title=删除 class=point onclick=delrow('1')><img src="./staticment/images/deletebody.gif" border="0"></td>
        <td><input type="text"  name="sku[]" style="width:100px"  onblur="get_pid(1)" id="sku_1"><input type="hidden"  value="" name="pid[]" id="pid_1"/></td>
        <td><input type="text" name="product_name[]" id="product_name_1"  value="" style="width:100%"/></td>
        <td><input type="text"  name="quantity[]" style="width:40px" ></td>
        <!--<td><input type="text"  name="price[]" style="width:50px" title="不填则不改变系统原成本价"></td>-->
        <td><!--{echo $whouse}--></td>
        <td><input type="text"  name="comment[]"  style="width:100%"></td><!--备注-->
   </tr>
</table>
<input type="button" value="加一行" onclick="addrow()" class="button"/>&nbsp;&nbsp;<input type="button" class="check button" value="检测" /><span style="background-color: #F8F8F8; color: #BDBDBD; font-size: 12px; height: 20px; line-height: 20px;">&nbsp;&nbsp;填写完成，请点击检测按钮!&nbsp;&nbsp;</span>
<br>
<br>
<!--提交按钮-->
<table width="" cellpadding="3" cellspacing="0">
  <tr>
    <td>
    <input  type="submit"  value="" style="background-image:url('./staticment/images/sure.gif');display:none;" id="subinput">
    <input type="reset" style="background-image:url('./staticment/images/reset.gif');" value="" id="subinput">
    </td>
  </tr>
</table>
</form>
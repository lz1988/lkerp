<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-03 16:23:32, compiled from template/adminweb/process_shipment.tpl */ ?>
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/WebCalendar.js"></script>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body		{font-family: Arial,Helvetica,sans-serif;}
.big 		{font-size: 16px}
.point 		{ cursor:pointer;}
#subinput	{width:82px; height:30px; border:none; cursor:pointer;}
#mytable input {border: double #CCCCFF 1px;height:25px;}
.waringred	{ background-color: red;}
#mytable input,#mytable select,select{border-left: 1px solid #C2C2C2;border-right: 1px solid #EAEAEA; border-top: 1px solid #C2C2C2;border-bottom:1px solid #eeeeee;}
#mytable input,#mytable select{ height:25px;}
.button		{ background:url(./staticment/images/button_bj_2.gif) no-repeat; width:50px; height:22px; border:none;cursor:pointer; margin:2px;}	
.notice 	{ background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:600px;line-height:22px;padding-left:25px;color:red;margin:10px 0;}
.noticed{background-color:green;}
.msg 	{ background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:600px;line-height:22px;padding-left:25px;color:green;margin:10px 0;}
</style>
<script type="text/javascript">
$(function() {
	$('.check').click(function() {	
		if ($('select[name=houseid]').val() == '') {
			alert('请选择发货仓库！');
			return false;
		}
		var houseid 	= $('select[name=houseid]').val();
		var skuarr 		= new Array();
		var quantityarr = new Array();
		var fidarr 		= new Array();
		var waring 		= 0;
		var notice		= 0;
		var num 		= 0;
		var fidnum		= 0;
		var index = $(this).index('input[name^=sku]');
		$('input[name^=sku]').each(function() {
			if ($(this).val() != '') {				
				quan = $('input[name^=quantity]').eq($(this).index('input[name^=sku]'));
				if (quan.val() == ''||parseInt(quan.val()) < 1) {
					quan.addClass('waringred');
					waring ++;
				}
			
				skuarr[num] = $(this).val();
				quantityarr[num] = quan.val();
				num++;
			}
		});	
		
		
		if (waring) {
			alert('请检查SKU和数量，有未填写错误！');
			return false;
		}
		
		
		$('input[name^=fid]').each(function(){
			if($(this).val() == ''){
				$(this).eq(index).addClass('waringred');
				$(this).eq(index).attr('title','请填写第三方单号');
				waring++;
				
			}else{
				$(this).eq(index).removeClass('waringred');
				$(this).eq(index).attr('title','');
				fidarr[fidnum] = $(this).val();				
			}
			fidnum++;
		});
			
		
		$('select[name^=sold_account]').each(function(i) {

			if ($(this).val() == '') {
				$(this).eq(index).addClass('waringred');
				$(this).eq(index).attr('title','请选择销售账号');
				waring++;
			
			}else{
				$(this).eq(index).removeClass('waringred');
				$(this).eq(index).attr('title','');
			}
		});
		
		$('select[name^=b2bcorpbsl]').each(function(){
			/*b2b客户不可为空*/
			var sold_account 	= $("select[name^=sold_account]").eq(index).find("option:selected").text();
			var b2bcorpbsl		= $(this).val();
			if (sold_account.indexOf('B2B') > -1 && b2bcorpbsl == '')
			{
				$(this).eq(index).addClass('waringred');
				$(this).eq(index).attr('title','请填写B2B客户');
				waring++;
			}else{
				$(this).eq(index).removeClass('waringred');
				$(this).eq(index).attr('title','');
			}
		})
		
		$('select[name^=e_shipping]').each(function(i) {
			if ($(this).val() == '') {
				$(this).eq(index).addClass('waringred');
				$(this).eq(index).attr('title','请选择发货方式')
				waring++;
			
			}else{
				$(this).eq(index).removeClass('waringred');
				$(this).eq(index).attr('title','');
			}
		});
		
		/*检测第三方单号不同重复*/
		$.getJSON('index.php?action=process_shipment&detail=check_fid',{'fidarr':fidarr},function(msg){
			if(msg.msg != 0){
				var relen = msg.length;
				for(var i=0; i<relen; i++){
					$('input[name^=fid]').eq(msg[i]).addClass('waringred');
					$('input[name^=fid]').eq(msg[i]).attr('title','存在与系统重复的第三方单号，请查看页面红色处修改');
					waring++;
				}
			}

		});
		
		/*检测库存*/
		//如果是中国蛇口仓库，就需要去检测库存，其他仓库则相反
		//if (houseid == 10){
			$.getJSON('index.php?action=process_shipment&detail=check_quantity',{'sku': skuarr, 'quantity': quantityarr, 'houseid': houseid},function(msg) {
				if (msg.msg) { 
					alert(msg.msg);
				}
				else {				
					var res = eval(msg);
					var relen = res.length;
					if (relen > 0) {
						$('.wall_check').removeClass('waringred');
						for (var i = 0; i < relen; i++) {
						//alert(res[i].quantity);
							if (res[i].quantity == -1) {
								$('input[name^=sku]').eq(res[i].num).addClass('waringred');	
								$('input[name^=sku]').eq(res[i].num).attr('title','sku填写错误');			
								waring++;
							}
							else if (res[i].quantity == -2) {
								$('input[name^=quantity]').eq(res[i].num).addClass('noticed');	
								$('input[name^=quantity]').eq(res[i].num).attr('title',res[i].skip);
								//var _order_id = $('input[name^=fid]').eq(res[i].num).val();
								//alert(_deal_id);
								//$('input[name^=istrue]').eq(res[i].num).attr('value',_order_id);
	
								//库存不足依旧可添加订单
								notice++;							
							}else{
								$('input[name^=quantity]').eq(res[i].num).removeClass('waringred')
								$('input[name^=quantity]').eq(res[i].num).attr('title','');
								$('input[name^=sku]').eq(res[i].num).removeClass('noticed');
								$('input[name^=sku]').eq(res[i].num).attr('title','');	
							}					
						}					
					}
		
					if (waring) {
						$("#notice").addClass('notice');
						$("#notice").html("总共有 <b>"+waring+"</b> 处错误，请将鼠标移到红色处查看错误提示并修正。");
					}
					else {
						$("#notice").removeClass('notice');
						$("#notice").html('');
					}
					
					if (notice) {
						$("#msg").addClass('msg');
						$("#msg").html("温馨提示：蛇口仓存在库存不足的SKU，不予以提交！");
		
					}else{
						$("#msg").removeClass('msg');
						$("#msg").html('');
					}
					if (notice < 1 && waring < 1){
						$('input[type=submit]').css('display', '');		
					}
					
				}
			});
		/*}else if (waring){
			$("#notice").addClass('notice');
			$("#notice").html("总共有 <b>"+waring+"</b> 处错误，请将鼠标移到红色处查看错误提示并修正。");
		}else{
			$("#notice").removeClass('notice');
			$("#notice").html('');
			$('input[type=submit]').css('display', '');	
		}*/
	});
	
	$('.wall_check').live('focus', function() {
		$('input[type=submit]').css('display', 'none');		
		$(this).removeClass('waringred');
		$(this).removeClass('noticed');
	});
	$('select[name=houseid]').change(function() {
		$('input[type=submit]').css('display', 'none');	
		//一般提醒
		$("#msg").removeClass('msg');
		$("#msg").html('');	
		//库存提醒
		$("#notice").removeClass('notice');
		$("#notice").html('');
		//所有提醒
		$('.wall_check').removeClass('waringred');
		$('.wall_check').removeClass('noticed');
	});
	$('select[name^=sold_account],select[name^=e_shipping],input[name=fid[]],select[name^=b2bcorpbsl]').live('focus', function() {
		$(this).removeClass('waringred');
		$('input[type=submit]').css('display', 'none');		
	});
	$('input[name^=quantity]').live('focus',function(){
		$(this).removeClass('noticed');
		$("input[name^=istrue]").val('');
		$('input[type=submit]').css('display', 'none');
	})
	$('body').keydown(function(e) {
		if (e.keyCode == '13') {
			return false;
		}
	});
});
/*增加一行*/
function addrow(){
	
	var tdstr='',maxrow=0;
	
	$('span[id^=row_]').each(function(){maxrow = maxrow<parseInt($(this).html())?parseInt($(this).html()):maxrow;})	
	maxrow+=1;	
	
	tdstr+='<td><input type="text"  name="deal_id[]" style="width:150px" ></td>';//平台订单号
    tdstr+='<td><input type="text"  name="fid[]" style="width:150px" ></td>';//第三方单号
	tdstr+='<td><input class="wall_check" type="text"  name="sku[]" style="width:100px" onblur="get_pid('+maxrow+')" id="sku_'+maxrow+'"><input type="hidden"  value="" name="pid[]" id="pid_'+maxrow+'"/><input type="hidden" name="product_name[]" id="product_name_'+maxrow+'"  value=""/></td>';//产品sku
	tdstr+='<td><input type="text"  name="e_listing[]" style="width:40px" ></td>';//listing	
	tdstr+='<td><input class="wall_check" type="text"  name="quantity[]" style="width:40px" ><input type="hidden" name="istrue[]" id="istrue" /></td>';//数量
	tdstr+='<td><input type="text"  name="price[]" style="width:50px" ></td>';//单价	
	
	tdstr+='<td><input type="text"  name="e_item_tax[]" style="width:40px" ></td>';
	tdstr+='<td><input type="text"  name="e_shipping_price[]" style="width:40px" ></td>';
	tdstr+='<td><input type="text"  name="e_shipping_tax[]" style="width:40px" ></td>';
	tdstr+='<td><input type="text"  name="e_performance_fee[]" style="width:40px" ></td>';//平台费
	tdstr+='<td><input type="text"  name="e_shipping_fee[]" style="width:40px" ></td>';	//运费
	
	tdstr+='<td><?php echo $sold_accountstr ?></td>';//销售帐号
	tdstr+='<td><?php echo $sold_payrecstr ?></td>';//收款帐号
	tdstr+='<td><?php echo $b2b_customers ?></td>';//b2b客户	
	
    tdstr+='<td><?php echo $e_shippingstr ?></td>';//发货方式	
    tdstr+='<td><input type="text"  name="e_receperson[]" style="width:100px"></td>';//收件人
	tdstr+='<td><input type="text"  name="buyer_id[]" style="width:100px"></td>';//收件人
    tdstr+='<td><input type="text"  name="e_tel[]" style="width:100px"></td>';//电话
	tdstr+='<td><input type="text"  name="e_email[]" style="width:100px"></td>';	
    tdstr+='<td><input type="text"  name="e_address1[]" style="width:100px")></td>';//地址1
	
    tdstr+='<td><input type="text"  name="e_address2[]" style="width:100px")></td>';//地址2
    tdstr+='<td><input type="text"  name="e_city[]" style="width:50px"></td>';//城市
	tdstr+='<td><input type="text"  name="e_state[]" style="width:50px"></td>';//州
	tdstr+='<td><input type="text"  name="e_country[]" style="width:50px"></td>';//国家
	tdstr+='<td><input type="text"  name="e_post_code[]" style="width:50px"></td>';//邮编
	tdstr+='<td><input type="text"  name="comment[]"></td>';//备注

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

/*检测币别不能为空*/
function checkform(){
	
	
	if($('select[name=houseid]').val() == ''){
		alert('请选择发货仓库！');
		return false;
	}
	
	else if($('select[name=coin_code]').val() == ''){
		alert('请选择币别！');
		return false;
	}
		
	else{
		$('input[type=submit]').attr('disabled',true);
	}
		
}

/*根据选择的销售账号，自动带出收付款账号*/
function autonext(obj){
	CommomAjax('POST','index.php?action=process_shipment&detail=autonext',{'account_id':$(obj).val()},function(msg){
		$(obj).parent().next().children().val(msg);
		/*销售账号为b2b，才可选*/
		if ($(obj).find("option:selected").text().toLowerCase() != 'b2b'){
			$(obj).parent("td").parent("tr").find("select").eq(2).attr("disabled",true);
		}else{
			$(obj).parent("td").parent("tr").find("select").eq(2).attr("disabled",false);
		}

	});
}

</script>
<form method="POST" action="index.php?action=process_shipment&detail=modneworder" onsubmit="return checkform()">
<div id="notice"></div>
<div id="msg"></div>
<div style="margin-top:10px; margin-bottom:5px;"><font size='1'>* 发货仓库：<?php echo $whouse ?> &nbsp; &nbsp;* 币种：<?php echo $coin_code ?></font></div>
<!--填写资料部分-->
<table id="mytable" cellspacing="0" cellpadding="0" width="3200">
	<tr>
    	<th class="list" width="60">行号</th>
        <th class="list" width="130"	title="平台交易单号">deal_id</th>        
        <th class="list" width="130"	title="第三方单号(必填，并且不能与系统已存在的重复)">*3rd_part_id</th>
        <th class="list" width="95"		title="产品SKU(必填)">*SKU</th>
        <th class="list" width="50"		title="LISTING">LISTING</th>
        <th class="list" width="80"		title="数量(必填)">*quantity</th>
        <th class="list" width="100"    title="商品总金额">item_price<br/>（商品总金额）</th>
        
        <th class="list" width="100"    title="总税金收入">item_tax<br/>（总税金收入）</th>
        <th class="list" width="100"	title="运费收入">shipping_price</th>
        <th class="list" width="90"		title="运费税金收入">shipping_tax</th>
        <th class="list" width="110"	title="交易费支出">performance_fee</th>
        <th class="list" width="90"		title="运费支出">shipping_fee</th>             
        <th class="list" width="100"	title="销售帐号(必选)">*sold_account</th>             
        <th class="list" width="105"	title="收款帐号">payrec_account</th>                             
        <th class="list" width="105"	title="b2b客户">b2b_customers</th>                             
        
        <th class="list" width="100"	title="发货方式(必选)">*shipping</th>
        <th class="list" width="100"	title="收件人">receive_person</th>
        <th class="list" width="100"	title="买家帐号">buyer_id</th>
        <th class="list" width="100"	title="电话">tel</th>
        <th class="list" width="100"	title="邮箱">email</th>
        <th class="list" width="100"	title="地址一">address1</th>
        
        <th class="list" width="100"	title="地址二">address2</th>        
        <th class="list" width="50"		title="城市">city</th>
        <th class="list" width="50"		title="洲">state</th>
        <th class="list" width="70"		title="国家">country</th>        
        <th class="list" width="80"		title="邮编">post_code</th>
        <th class="list" width="80"		title="备注">comment</th>        
    </tr>
	<tr>
<td width="60"><span id="row_1" class="big">1</span><span title=删除 class=point onclick=delrow('1')><img src="./staticment/images/deletebody.gif" border="0"></td>
        <td><input type="text"  name="deal_id[]" style="width:150px" ></td>
        <td><input type="text"  name="fid[]" style="width:150px" ></td>
        <td><input class="wall_check" type="text"  name="sku[]" style="width:100px"  onblur="get_pid(1)" id="sku_1"><input type="hidden"  value="" name="pid[]" id="pid_1"/><input type="hidden" name="product_name[]" id="product_name_1"  value=""/></td>
        <td><input type="text"  name="e_listing[]" style="width:40px" ></td>        
        <td><input class="wall_check" type="text"  name="quantity[]" style="width:40px" >
        <input type="hidden" name="istrue[]" id="istrue" /></td>
        <td><input type="text"  name="price[]" style="width:50px" ></td>
        
        <td><input type="text"  name="e_item_tax[]" style="width:40px" ></td>
        <td><input type="text"  name="e_shipping_price[]" style="width:40px" ></td>
        <td><input type="text"  name="e_shipping_tax[]" style="width:40px" ></td>
        <td><input type="text"  name="e_performance_fee[]" style="width:40px" ></td>
        <td><input type="text"  name="e_shipping_fee[]" style="width:40px" ></td>
        <td><?php echo $sold_accountstr ?></td>
        <td><?php echo $sold_payrecstr ?></td>
        <td><?php echo $b2b_customers ?></td><!--b2b客户-->
            
        <td><?php echo $e_shippingstr ?></td><!--发货方式-->
        <td><input type="text"  name="e_receperson[]" style="width:100px"></td><!--收件人-->
        <td><input type="text"  name="buyer_id[]" style="width:100px"></td><!--买家ID-->
        <td><input type="text"  name="e_tel[]" style="width:100px"></td>
		<td><input type="text"  name="e_email[]" style="width:100px"></td>               
        <td><input type="text"  name="e_address1[]" style="width:100px")></td>
        
        <td><input type="text"  name="e_address2[]" style="width:100px" /></td>
        <td><input type="text"  name="e_city[]" style="width:50px"></td>
        <td><input type="text"  name="e_state[]" style="width:50px"></td><!--州-->
        <td><input type="text"  name="e_country[]" style="width:50px"></td>
        <td><input type="text"  name="e_post_code[]" style="width:50px"></td>
        <td><input type="text"  name="comment[]"></td><!--备注-->
   </tr>
</table><br>
<input type="button" value="增加" onclick="addrow()" class="button"/>
<input type="button" class="check button" value="检测" /><span style="background-color: #F8F8F8; color: #BDBDBD; font-size: 12px; height: 20px; line-height: 20px;">&nbsp;&nbsp;填写完成，请点击检测按钮!&nbsp;&nbsp;</span>
<br>
<br>
<!--提交按钮-->
<table width="" cellpadding="3" cellspacing="0">
  <tr>
    <td>	
    <input type="submit" style="background-image:url('./staticment/images/sure.gif');display: none;"  value="" id="subinput" />
    </td>
  </tr>
</table>
</form>
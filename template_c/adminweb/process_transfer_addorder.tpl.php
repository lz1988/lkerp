<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-12-10 10:06:16, compiled from template/adminweb/process_transfer_addorder.tpl */ ?>
<!--添加转仓单模板-->
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/process_transfer.js"></script>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style  type="text/css">
body		{ font-family: Arial,Helvetica,sans-serif;}
.big 		{ font-size: 16px}
.point 		{ cursor:pointer;}
#subinput	{ width:82px; height:30px; border:none; cursor:pointer;}
.waringred	{ background-color: red;}
.notice 	{ background:url(./staticment/images/T1WNREXhxGXXXXXXXX-13-16.png) 5px 3px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:600px;line-height:22px;padding-left:25px;color:#ff2a00;margin:10px 0;}
.noticeed 	{ background:green;}
#mytable input,#mytable select{height:25px;
	border-left: 1px solid #C2C2C2;
    border-right: 1px solid #EAEAEA;
    border-top: 1px solid #C2C2C2;
	border-bottom:1px solid #eeeeee;
}
.button		{ background:url(./staticment/images/button_bj_2.gif) no-repeat; width:50px; height:22px; border:none;cursor:pointer; margin:2px;}	
</style>
<script type="text/javascript">
$(function() {
	$('.check').click(function() {		
		var providerarr = new Array();
		var skuarr = new Array();
		var quantityarr = new Array();
		var receiver_warehouse = new Array();
		var waring = 0;
		var notice = 0;
		var num = 0;
		$('input[name^=sku]').each(function() {
			if ($(this).val() != '') {				
				var index = $(this).index('input[name^=sku]');
				var quan  = $('input[name^=quantity]').eq(index);
				var provider = $('select[name^=provider_id]').eq(index);
				var receiver = $('select[name^=receiver_id]').eq(index);
				if (quan.val() == '' || parseInt(quan.val()) < 1) {
					quan.addClass('waringred');
					quan.attr('title' ,'总数量必须为数字！');
					waring ++;
				}
				if (provider.val() == '') {
					provider.addClass('waringred');
					provider.attr('title', '发货仓库错误！');
					waring ++;
				}
				if (receiver.val() == '' ) {
				    receiver.addClass('waringred');
					receiver.attr('title' ,'目的仓库错误');
					waring ++;
				}
				skuarr[num] = $(this).val();
				quantityarr[num] = quan.val();
				providerarr[num] = provider.val();
				num++;
			}
		});	
        
        $('input[name^=e_tel]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        $('input[name^=e_address1]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        $('input[name^=e_city]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        $('input[name^=e_state]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        $('input[name^=e_country]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        $('input[name^=e_post_code]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        $('input[name^=e_receperson]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        $('input[name^=e_company]').each(function(i){
            if($(this).val()==''){
                $(this).addClass('waringred');
                $(this).attr('title','不能为空');
                waring++;
            } 
        });
        
		if (waring) {
			$("#msg").addClass('notice');
			$("#msg").html("总共有 <b>"+waring+"</b> 处错误，请将鼠标移到红色处查看错误提示并修正。");
			return false;
		}
		$.getJSON('index.php?action=process_transfer&detail=check_quantity',{'sku': skuarr, 'quantity': quantityarr, 'houseid': providerarr},function(msg) {
			if (msg.msg) { 
				alert(msg.msg);
			}
			else {				
				var res = eval(msg);
				var relen = res.length;
				if (relen > 0) {
					$('.wall_check').removeClass('waringred');
					for (var i = 0; i < relen; i++) {
						if (res[i].quantity == -1) {
							$('input[name^=sku]').eq(res[i].num).addClass('waringred');
							$('input[name^=sku]').eq(res[i].num).attr('title', 'SKU格式错误！')										
							waring++;
						}
						else if (res[i].quantity == -2) {
							$('input[name^=quantity]').eq(res[i].num).addClass('waringred');
							$('input[name^=quantity]').eq(res[i].num).attr('title' ,res[i].skip);//提示库存情况
							waring++;							
						}
					   if (res[i].countsku == 1 && res[i].countsku != ""){
						    $('input[name^=sku]').eq(res[i].num).addClass('noticeed');	//提示是否组装
						    notice ++; 
						}else {
						    $('input[name^=sku]').eq(res[i].num).removeClass('noticeed');	//提示是否组装
						}					
					}						
				}
				if (waring) {
					$("#msg").addClass('notice');
					$("#msg").html("总共有 <b>"+waring+"</b> 处错误，请将鼠标移到红色处查看错误提示并修正。");
				}
				else {
				 	$("#msg").removeClass('notice');
					$("#msg").html("");
					$('input[name^=quantity]').attr('title' ,'');
					$('input[type=submit]').css('display', '');	
				}
			    if(notice && !waring){
					$("#msg").addClass('notice');
				    $("#msg").html("温馨提示:存在组装的SKU，提交后会系统将自动提取原SKU替代后保存 ！");
				}
			}
		});		
	});
	$('.wall_check').live('focus', function() {
		$('input[type=submit]').css('display', 'none');		
		$(this).removeClass('waringred');
	});
	$('select[name^=provider_id]').live('change', function(){ 
		$('input[type=submit]').css('display', 'none');		
		$(this).removeClass('waringred');
		$(this).attr('title', '');

	});
	$('select[name^=receiver_id]').live('change', function(){
	    $('input[type=submit]').css('display', 'none');
	    $(this).removeClass('waringred'); 
		$(this).attr('title', '');
	})
	$('body').keydown(function(e) {
		if (e.keyCode == '13') {
			return false;
		}
	});
});
$(function(){
	$('form').submit(function(){
		$('input[type=submit]').attr('disabled',true);						 
	});
})

/*增加一行*/
function addrow(){	
	var tdstr='',maxrow=0;
	
	$('span[id^=row_]').each(function(){maxrow = maxrow<parseInt($(this).html())?parseInt($(this).html()):maxrow;})	
	maxrow+=1;

	tdstr+='<td><input class="wall_check" type="text"  name="sku[]" style="width:80px" onblur="get_pid('+maxrow+')" id="sku_'+maxrow+'"><input type="hidden"  value="" name="pid[]" id="pid_'+maxrow+'"/></td>';//产品sku
	tdstr+='<td><input type="text" name="product_name[]" id="product_name_'+maxrow+'"  value=""/></td>';//名称
	tdstr+='<td><?php echo $backdata_p ?></td>';//发货仓库
	tdstr+='<td><?php echo $backdata_r ?></td>';//接收仓库	
	tdstr+='<td><input type="text"  name="e_unit_box[]" style="width:80px" id="e_unit_box_'+maxrow+'" onkeyup="count_sumlist('+maxrow+')"></td>';//单箱数量
	tdstr+='<td><input type="text"  name="e_box[]" style="width:50px"  id="e_box_'+maxrow+'" onkeyup="count_sumlist('+maxrow+')"></td>';//箱数
	tdstr+='<td><input class="wall_check" type="text"  name="quantity[]" id="quantity_'+maxrow+'" style="width:50px" ></td>';//总数	
	tdstr+='<td><input type="text"  name="shipping_id[]" style="width:100px"></td>';//运单编号
	tdstr+='<td><input type="text"  name="e_remeber_id[]" style="width:50px"></td>';//助记码
    tdstr+='<td><input class="wall_check" type="text"  name="e_tel[]" style="width:100px"></td>';//电话
    tdstr+='<td><input class="wall_check" type="text"  name="e_address1[]" style="width:100px"></td>';//地址
    tdstr+='<td><input class="wall_check" type="text"  name="e_address2[]" style="width:100px" /></td>';//地址二
    tdstr+='<td><input class="wall_check" type="text"  name="e_city[]" style="width:50px"></td>';//城市
    tdstr+='<td><input class="wall_check" type="text"  name="e_state[]" style="width:50px"></td>';//州
    tdstr+='<td><input class="wall_check" type="text"  name="e_country[]" style="width:50px"></td>';//国家
    tdstr+='<td><input class="wall_check" type="text"  name="e_post_code[]" style="width:50px"></td>';//邮编
	
    tdstr+='<td><input class="wall_check" type="text"  name="e_receperson[]" style="width:50px"></td>';//<!--收件人-->
    tdstr+='<td><input class="wall_check" type="text"  name="e_company[]" style="width:50px"></td>';//<!--收件公司--> 
    tdstr+='<td><input type="text"  name="comment[]" style="width:50px"></td>';//备注

	$('#mytable').append('<tr><td><span class=big id=row_'+maxrow+'>'+maxrow+'</span><span title=删除 class=point onclick=delrow('+maxrow+')><img src="./staticment/images/deletebody.gif" border="0"></span></td>'+tdstr+'</tr>');
}
</script>
<div id="msg">

</div>
<form method="POST" action="index.php?action=process_transfer&detail=modaddorder" >
<!--填写资料部分-->
<table id="mytable" cellspacing="0" width="1320">
	<tr>
    	<th class="list" width="60">行号</th>
        <th class="list" width="80">SKU</th>
        <th class="list" width="120">产品名称</th>        
        <th class="list" width="120">发货仓库</th>
        <th class="list" width="120">目的仓库</th>
        <th class="list" width="60">单箱数量</th>
        <th class="list" width="50">箱数</th>        
        <th class="list" width="50">总数量</th>
        <th class="list" width="80">运单编号</th>
        <th class="list" width="50">助记码</th>
        <!--和出库订单的增加订单一致 ups生成时需要用到这些信息 把备注里面的信息拆分 start-->
        <th class="list" width="100"	title="电话">电话</th>
        <th class="list" width="100"	title="地址一">地址一</th> 
        <th class="list" width="100"	title="地址二">地址二</th>        
        <th class="list" width="50"		title="城市">城市</th>
        <th class="list" width="50"		title="洲">洲</th>
        <th class="list" width="70"		title="国家">国家</th>        
        <th class="list" width="80"		title="邮编">邮编</th>
        <th class="list" width="80"		title="收件人">收件人</th>
        <th class="list" width="80"		title="收件公司">收件公司</th>
        <!--end-->
        <th class="list" width="50">备注</th>        
    </tr>
	<tr>
<td width="50"><span id="row_1" class="big">1</span><span title=删除 class=point onclick=delrow('1')><img src="./staticment/images/deletebody.gif" border="0"></td>
        <td><input class="wall_check" type="text"  name="sku[]" style="width:80px"  onblur="get_pid(1)" id="sku_1"><input type="hidden"  value="" name="pid[]" id="pid_1"/></td>
        <td><input type="text" name="product_name[]" id="product_name_1"  value=""/></td>
        <td><?php echo $backdata_p ?></td>
        <td><?php echo $backdata_r ?></td>        
        <td><input class="wall_check" type="text"  name="e_unit_box[]" style="width:80px" id="e_unit_box_1" onkeyup="count_sumlist(1)"></td><!--单箱数量-->
        <td><input class="wall_check" type="text"  name="e_box[]" style="width:50px"  id="e_box_1" onkeyup="count_sumlist(1)"></td><!--箱数-->
        <td><input class="wall_check" type="text"  name="quantity[]" style="width:50px" id="quantity_1"/></td><!--总数-->
        <td><input type="text"  name="shipping_id[]" style="width:100px"></td><!--运单编号-->
        <td><input type="text"  name="e_remeber_id[]" style="width:50"></td><!--助记码--> 
        <td><input type="text" class="wall_check"  name="e_tel[]" style="width:100px"></td><!--电话--> 
        <td><input type="text" class="wall_check"  name="e_address1[]" style="width:100px"></td><!--地址一--> 
        <td><input type="text" class="wall_check"  name="e_address2[]" style="width:100px" /></td><!--地址二-->
        <td><input type="text" class="wall_check"  name="e_city[]" style="width:50px"></td><!--城市-->
        <td><input type="text" class="wall_check"  name="e_state[]" style="width:50px"></td><!--州-->
        <td><input type="text" class="wall_check"   name="e_country[]" style="width:50px"></td><!--国家-->
        <td><input type="text" class="wall_check"  name="e_post_code[]" style="width:50px"></td><!--邮编--> 
        <td><input type="text" class="wall_check"  name="e_receperson[]" style="width:50px"></td><!--收件人-->
        <td><input type="text" class="wall_check" name="e_company[]" style="width:50px"></td><!--收件公司--> 
        <td><input type="text"  name="comment[]" style="width:50"></td><!--备注-->
   </tr>
</table>
<br>
<input type="button" value="增加" onclick="addrow()" class="button" />
<input type="button" class="check button" value="检测" />
<span style="background-color: #F8F8F8; color: #BDBDBD; font-size: 12px; height: 20px; line-height: 20px; padding:2px;">&nbsp;&nbsp;填写完成，请点击检测按钮!&nbsp;&nbsp;</span>
<br>
<br>
<!--提交按钮-->
<table width="" cellpadding="3" cellspacing="0">
  <tr>
    <td>	
    <input  type="submit"  value="" style="background-image:url('./staticment/images/sure.gif');display: none;" id="subinput">
    </td>
  </tr>
</table>
</form>
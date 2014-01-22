// JavaScript Document
// 出货JS by hanson

var printed 			= 0;
var rate_msg 			= '* 折算美元：$'

var btop,bleft,bwidth,closetime,closetime_t;

$(function(){
	/******************start*********************/
	//初始化sold_account非b2b，corpbsl不可选
	if ($('select[name=sold_account]').find("option:selected").text().toLowerCase() != 'b2b')
		$('select[name=b2bcorpbsl]').attr("disabled",true);
	
	$('select[name=b2bcorpbsl]').blur(function(){
		if ($('select[name=sold_account]').find("option:selected").text().toLowerCase() == 'b2b'){
			if(check_notnull_fun($(this).val())){
				toggle_waring_alerts($(this), 0, '输入不能为空！', 'check_notnull');
			}	
			else {			
				toggle_waring_alerts($(this), 1, '输入不能为空！', 'check_notnull');
				return false;
			}
		}
	})
	
	$('form').submit(function() {
		if ($('select[name=sold_account]').find("option:selected").text().toLowerCase() == 'b2b'){
			if(check_notnull_fun($('select[name=b2bcorpbsl]').val())){
				toggle_waring_alerts($('select[name=b2bcorpbsl]'), 0, '输入不能为空！', 'check_notnull');
			}	
			else {			
				toggle_waring_alerts($('select[name=b2bcorpbsl]'), 1, '输入不能为空！', 'check_notnull');
				return false;
			}
		}
	
	});
	/******************end*********************/
	
	$('.ajax_shipping').click(function(){
		var $this = $(this);
		var shipping = $(this).html();
		var $id = $(this).attr('id');
		$.post('index.php?action=shipping',{'detail':'get_shipping','shipping':shipping},function(msg) {
			$this.html(msg);
			$('.ajax_select_shipping').focus();
			$('.ajax_select_shipping').click(function() {
				return false;
			}).blur(function() {
				$this.html(shipping);
			}).change(function() {
				var newshipping = $(this).val();
				$.post('index.php?action=process_shipment',{'detail':'update_shipping','shipping':newshipping,'id':$id},function(msg){
					$this.html(newshipping);
				});
			});
		});		
	});
	
	/*失去焦点移除下拉*/	
	$('#bdiv').live('mouseover',function()			{ clearTimeout(closetime);});	
	$('#lidiv').live('mouseover',function()			{ clearTimeout(closetime);
													  clearTimeout(closetime_t);//进入子时，子的消失定时器销毁
													  $('#express').css('background','url(./staticment/images/tree/arrow_collapsed.gif) no-repeat 60px 5px');
													  });
	
	$('#bdiv,#lidiv').live('mouseleave',function()	{ closetime = setTimeout("disappear('#bdiv,#lidiv')",500);});	
	$('#printorder').mouseout(function()			{ closetime = setTimeout("disappear('#bdiv,#lidiv')",500);});
	
	/*移开子联的父时，子消失定时器*/
	$('#express').live('mouseleave',function(){
		closetime_t = setTimeout("disappear('#lidiv')",500);
	})
	
	/*展开子菜单*/
	$('#express').live('mouseover',function(){
			$(this).css('background','url(./staticment/images/tree/arrow_collapsed.gif) no-repeat 60px 5px');
			
			var lidiv = '<div id=lidiv><div class=list_b onclick=print_table("print_express_yunda")>韵达快递</div><div class=list_b onclick=print_table("print_express_shentong")>申通快递</div></div>';
			$("body").append(lidiv);
			set_css($(".list_b"));
			$("#lidiv").css({
					"top"				: (btop+20+ 30) + "px",
					"left"				: (bleft-3+ bwidth+22)  + "px",
					"width"				: (bwidth+20) + "px",
					"height"			: "50px",
					"position"			:'absolute',
					'background-color'	:'#ffffff',
					'font-size'			:'12px',
					'color'				:'black',
					'border'			:'#cdcdcd 1px solid',
					'line-height'		:'18px',
					'padding'			:'0px',
					'margin'			:'0px',
					'box-shadow'		:'1px 1px 2px #CCC',
					'-webkit-box-shadow':'1px 1px 2px #CCC',
					'-moz-box-shadow'	:'1px 1px 2px #CCC'
				})
				.show("fast");			
			
			
	});
})


/*打印发货单*/
function printorder(){
	
	var butobj 	= $('#printorder');
	btop 		= butobj.offset().top;
	bleft		= butobj.offset().left;
	bwidth		= butobj.width();
	
	/*打印快递单原来有申通，韵达，可展开下一级，暂时去掉了，只保留申通，若日后恢复，将onclick=print_table("print_express_shentong") 改为id=express即可*/
	var tdiv 	=  '<div id=bdiv><div class=list_b onclick=print_table("print_outbound")>打印发货单</div><div class=list_b  onclick=print_table("print_express_shentong") >打印申通快递</div><div class=list_b  onclick=print_table("print_quantwl") >打印中通快递</div></div>';
	
	//把它追加到文档中
	$("body").append(tdiv);
	
	set_css($(".list_b"));	
	
	$("#bdiv").css({
					"top"				: (btop+20) + "px",
					"left"				: (bleft-8)  + "px",
					"width"				: (bwidth+26) + "px",
					"height"			: "80px",
					"position"			:'absolute',
					'background-color'	:'#ffffff',
					'font-size'			:'12px',
					'color'				:'black',
					'border'			:'#cdcdcd 1px solid',
					'line-height'		:'18px',
					'padding'			:'0px',
					'margin'			:'0px',
					'box-shadow'		:'1px 1px 2px #CCC',
					'-webkit-box-shadow':'1px 1px 2px #CCC',
					'-moz-box-shadow'	:'1px 1px 2px #CCC'
				})
				.show("fast");//设置x坐标和y坐标，并且显示		
}

/*设置弹出下拉CSS*/
function set_css(obj){
	obj.css({'margin':'0','padding-left':'5px','line-height':'25px','cursor':'pointer'})
		.mouseover(function(){$(this).css({'background':'#f7f7f0'})})
		.mouseout(function(){$(this).css('background','#fff')});
}

/*打印发货明细表与汇总表*/
function print_table(detail){
    var strID='';
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	
	/*生成隐藏表单用POST方式提交，因为GET地址方式某些浏览器支持的长度有限导致订单号无法传送*/
	//if(printed == 0 ){
		htmlform = '<form method="post" target="_blank" action="index.php?action=process_shipment&detail='+detail+'" id=postorder><input type=hidden id=order_id name=order_id value="'+strID+'"></form>';
		$('body').append(htmlform);
	//		printed = 1;		
	//}else if(printed == 1){
	//	$('input[id=order_id]').attr('value',strID);
	//}
	$('#postorder').submit();
	$('#postorder').remove();
}



/*下拉消失*/
function disappear(obj){
	$(obj).remove();
}


/*保留两位小数*/
function cuttwo(obj){
	return Math.round((Math.floor(obj*1000)/10))/100;
}

/*获取美元汇率*/
function getrate(obj){
	
	if($(obj).val() == '')
	{	
		var priceval 		= $('input[name=price]').val();
		var change_to_usd	= $('#change_to_usd');
		
		/*如果从有币别选择了空币别，汇率置空，显示金额等于输入框金额*/
		$('input[name=rate]').attr('value','');
		
		if(priceval == ''){
			change_to_usd.html('*');	
		}else{
			change_to_usd.html(rate_msg+priceval);
		}		
		
		$('input[name=price_h]').attr('value',priceval);
		return false;
	}
	
	CommomAjax('post','index.php?action=exchange_rate&detail=getrate',{'code':$(obj).val(),'type':'rate'},function(rate){
		$('input[name=rate]').attr('value',rate);
		
		/*输入了price再来更改币别，自动重新换算*/
		var nval = $('input[name=price]').val()*100/rate;
		$('#change_to_usd').html(rate_msg+cuttwo(nval));
		$('input[name=price_h]').attr('value',cuttwo(nval));
	});
}

/*输入退款金额，弹出键盘时转换成美元*/
function changeprice(val){
	
	/*如果汇率还没选*/
	if($('select[name=coin_code]').val() == '')
	{
		$('#change_to_usd').html(rate_msg+cuttwo(val));
		$('input[name=price_h]').attr('value',cuttwo(val));
	}
	
	/*如果选择了汇率,按汇率折算*/
	else
	{
		var showhtml = val*100/$('input[name=rate]').val();
		$('#change_to_usd').html(rate_msg+cuttwo(showhtml));
		$('input[name=price_h]').attr('value',cuttwo(showhtml));
	}
}


/*退款填写页面，检查不能为空*,并且退款金额不得大于订单收入总额*/
function check_backmonyform(){
	
	var item_price,item_tax,shipping_price,price_h,shipping_tax,all_back,price;
	price 	= $('input[name=price]').val();
	price_h	= $('input[name=price_h]').val();
	
	if($('select[name=coin_code]').val() == ''){
		alert('请选择币种！');
		return false;
	}
	
	if(price == ''){
		alert('退款金额不能为空！');
		return false;
	}
	
	if(!parseFloat(price)){
		alert('退款金额格式错误！');
		return false;
	}
		
	if($('input[name=order_id]').val() == ''){
		alert('交易ID不能为空！');
		return false;
		}		
	if($('input[name=re_sm]:checked').val() == undefined){
		alert('请选择退款类型！');
		return false;
		}
		
	if($('select[name=comment3]').val() == ''){
		alert('请选择退款原因！');
		return false;
		}
		
	all_back 		= parseFloat($('#haveremony').html());	
	if(price_h > all_back){
		alert('退款金额不得大过可退金额！');
		return false;
		}
}


/*退货填写页面，检查不能为空*/
function check_backproform(){	
	var detail_id,url,quantity;
	
	quantity  = $('input[name=quantity]').val();
	
	if ($('input[name=sold_id]').val() == '') {
		alert('平台ID不能为空');
		return false;
		}
	
	if($('select[name=receiver_id]').val() == ''){
		alert('接收仓库不能为空！');
		return false;
		}
	
	if($('select[name=comment3]').val() == ''){
		alert('请选择退货原因！');
		return false;
		}
	
	if(quantity == ''){
		alert('退货数量不能为空！');
		return false;
		}
}


/*失去SKU焦点时获取产品SKU*/
function get_pid(){
	var sku,obj;
	sku = $('input[name=sku]').val();	
	$.getJSON('index.php?action=process_shipment&detail=get_pid',{'sku':sku},function(msg){
																					  
		$('input[name=pid]').attr('value',msg.pid);
		$('input[name=product_name]').attr('value',msg.product_name);

	});
}


/*合并订单*/
function combine(){
	var strID,url;
	url = 'index.php?action=process_shipment&detail=combine';
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	
	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg != '合并失败')window.location.reload();
	});
}

/*取消合并*/
function celcombine(){
	var strID,url,orPre;
	strID 	= get_orderid();//取得订单号
	orPre	= strID.substr(1,1);
	url 	= 'index.php?action=process_shipment&detail=calcombine&order_pre='+orPre;
	
	if(!strID) {alert('请选择数据！');return;}
	
	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg.substr(0,4) == '操作成功')window.location.reload();
	});
}

/*接收选中(此处与转仓共用)*/
function receselect(){
	
	var strID,url,sjurl;	
	url = 'index.php?action=process_shipment&detail=recemod';
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}	

	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg == '接收成功')window.location.reload();
	});
}

/*确认退款*/
function modrecorde(t){
	var p = confirm('确定进行此操作？');
	if(!p){return;}
	
	strID 	= get_orderid();//取得ID集
	var url;
	if(t == '0'){//退款确认
		url = 'index.php?action=process_backorder&detail=surebackmy';
	}else if(t == '1'){//退货确认
		url = 'index.php?action=process_backpro&detail=suremod';
	}

	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg == '确认成功')window.location.reload();
	});
}

/*确认打印*/
function sureprint(t){
	var p = confirm('确定进行此操作？');
	if(!p){return;}
	var strID,url,sjurl;
	url = 'index.php?action=process_shipment&detail=modprint&t='+t;
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}

	CommomAjax('POST',url,{'strid':strID},function(msg){
		if(msg == '1'){alert("确认成功");window.location.reload();}else{alert("确认失败");return ;}
	});
}

/*确认出货*/
function sureout(){
	
	var p = confirm('确定出库？');
	if(!p){return;}
	
	var strID,url,sjurl;	
	url = 'index.php?action=process_shipment&detail=modoutstock';
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg == '确认成功'){window.location.reload();}
	});

}


/*取得订单ID，过滤重复的*/
function get_orderid(){
	
	var obj=document.getElementsByName('checkmod[]');
	var strarray = new Array();
	var countstr = 0,strID='';
	

	/*取得需要操作的采购订单号,过滤重复的订单号*/
	for(var i=0;i<obj.length;i++){
		
		if(obj[i].checked){			
			strarray[countstr] = obj[i].value;
			countstr++;
		}	

	}
	if(!strarray.length){
		return;
	}

	strID +="'"+strarray[0]+"',";
	for(var j=1;j<strarray.length;j++){
		if(strarray[j] != strarray[j-1]){strID +="'"+strarray[j]+"',";}
	}	
	/*END*/
	
	strID = strID.substr(0,strID.length-1);
	return strID;
}

/*下拉跳转页面*/
function jumppage(val,statu,action,extra){
	window.location='index.php?action='+action+'&detail=list&statu='+statu+'&selfval_set='+val+extra;
}

/*获取运单编号*/
function get_ems(){
	
	strID = get_orderid();//取得id
	if(!strID) {
		alert('请选择数据！');return false;
	}else{		
		CommomAjax('POST','index.php?action=process_emsapi&detail=checkemsid',{'strid':strID},function(msg){
			
			/*如果来自不同订单*/
			if(msg == '1'){
				alert('选择的记录来自不同订单，请检查！');
				return false;
			}
			else{
				window.open('index.php?action=process_emsapi&detail=get_ems&strid='+strID);
			}
		});
	}	
}

/*获取usp订单*/
function get_upsord(){
	strID = get_orderid();//取得id
	if(!strID) {
		alert('请选择数据！');return false;
	}else{ 
        // window.location.href='index.php?action=ups_order&detail=checkemsid&strid='+strID;
            CommomAjax('POST','index.php?action=ups_order&detail=checkemsid',{'strid':strID},function(msg){ 
			/*如果来自不同订单*/
			if(msg == '1'){
				alert('选择的记录来自不同订单，请检查！');
				return false;
			}
			else{
                var hrtypea  =  $("input[name=hrtypea]").val();
                var hrstatu  =  $("input[name=hrstatu]").val();
                //window.open('index.php?action=ups_order&detail=get_upsd&strid='+strID+'&rtype='+hrtypea+'&statu='+hrstatu);
                window.location.href='index.php?action=ups_order&detail=get_upsd&strid='+strID+'&rtype='+hrtypea+'&statu='+hrstatu;
            }
        });
	}	
}

/*获取SKU别名*/
function get_sku_code(){
	var sku = get_orderid();
	if(!sku){
		alert('请选择SKU');return false;
	}else{
		CommomAjax('POST','index.php?action=sku_alias_api&detail=delsome',{'sku':sku},function(msg){			
				alert(msg);window.location.reload();
		})
	}
}

/*根据选择的销售账号，自动带出收付款账号*/
function autonext(obj){
	CommomAjax('POST','index.php?action=process_shipment&detail=autonext',{'account_id':$(obj).val()},function(msg){
		$(obj).parent().parent().next().children().children().val(msg);
		/*sold_account等于b2b*/
		if ($(obj).find("option:selected").text().toLowerCase() != 'b2b'){
			$('select[name=b2bcorpbsl]').attr('disabled',true);
		}else{
			$('select[name=b2bcorpbsl]').attr('disabled',false);	
		}
	});
}
/*ups 编辑框*/
/*点击可编辑*/
function pubgoput(id,name){
	var obj = $("#input_"+name+'_'+id);
	var obs = $("#span_"+name+'_'+id);
	obs.hide();
	if(obs.html() == '--'){
		obj.attr('value','');
	}else{
		obj.attr('value',obs.html());
	}
	obj.show().focus(); 
}
/*ups 添加假的箱数，价格，重量*/
function checkupsisnan(name,id){
    
    var p = confirm('确定要修改吗？');
	if(!p){
        $("#input_"+name+'_'+id).hide();
        $("#span_"+name+'_'+id).show();
        return false;
    }
    
    if(isNaN($("#input_"+name+'_'+id).val()) || $("#input_"+name+'_'+id).val()=='' ){
        alert('必须输入数字.');
        return false;
    }
    else if($("#input_"+name+'_'+id).val()<=0){
        alert('必须大于0.');
        return false;
    }
    else if(!(/^(\+|-)?\d+$/.test($("#input_"+name+'_'+id).val())) && name=='c_temporary_boxnum' ){
        alert('虚拟箱数必须为整数');
        return false;
    }
    else{
        var val = $("#input_"+name+'_'+id).val();
        CommomAjax('POST','index.php?action=ups_order',{'detail':'postupscontent','id':id,'name':name,'val':val},function(msg){
           if(msg == '保存成功'){
                $("#input_"+name+'_'+id).hide();
                $("#span_"+name+'_'+id).html(val);
                $("#span_"+name+'_'+id).show();
           }else{
               alert(msg);
           } 
        });
    }
}


/*同一个shipto 可以假的合并订单*/
function checkmergeorder(){
	strID = get_orderid();//取得id
	if(!strID) {
		alert('请选择数据！');return false;
	}else{
       // window.location.href='index.php?action=ups_order&detail=checkmergeorder&strid='+strID;
           CommomAjax('POST','index.php?action=ups_order&detail=checkmergeorder',{'strid':strID},function(msg){ 
			/*如果来自不同订单*/
			if(msg == -4){
              alert('至少选择两个订单');
              return false;
			}
			else if(msg ==-1){
              alert('打包失败！已经是包裹不可以在打包！');
              return false;
			}
           else if(msg ==-2){
               alert('打包失败！同一个订单不能打包！');
               return false;
            }
           else if(msg ==-3){
               alert('打包失败！发货方式，联系电话，地址，城市，洲，国家，邮编信息不一致请检查');
               return false;   
            }
		    else if(msg != 0){
				alert('打包失败！');
				return false;
			}
            alert('打包成功');  
            window.location.href=location.href;
        });
	}	
}
 
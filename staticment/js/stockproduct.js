/*备货JS*/
$(function(){
    $('.ajax_conditionerp').click(function(){
        var $this = $(this);
        var conditionerp = $(this).html();
        var $id = $(this).attr('id');
        $.post('index.php?action=process_upstock',{'detail':'get_conditionerp','conditionerp':conditionerp},function(msg) {
            $this.html(msg);
            $('.ajax_select_conditionerp').focus();
            $('.ajax_select_conditionerp').click(function() {
                return false;
            }).blur(function() {
                $this.html(conditionerp);
            }).change(function() {
                var newconditionerp = $(this).val();
                $.post('index.php?action=process_upstock',{'detail':'update_conditionerp','conditionerp':newconditionerp,'id':$id},function(msg){
                    $this.html(msg);                    
                });
            });
        });        
    })
    
    $('span[class^=tips_]').css('color','#ffcc88');
    
    $('.js_input').keyup(function() {
        var price = $(this).val();
        var code = $(this).parent().children('select').find('option[selected]').val();
        var msg = '';
        var usdprice = '';
        if (code) {
            usdprice = change_code_usd(price, code)
            if (isNaN(usdprice)) {
                msg = '金额输入错误！';        
                usdprice = '';        
            } else {
                usdprice = cuttwo(usdprice);
                msg = 'USD： $' + usdprice;
            }
        }
        
        $('input[name=' + $(this).attr('obj') + ']').val(usdprice);
        
        $(this).parent().children('span').html(msg);
                
        if ($(this).attr('obj') == 'e_rprice' || $(this).attr('obj') == 'price') {
            get_e_sprice();
            $('input[name=profit_display]').attr("value",$('input[name=e_aprice]').val());
        }
    });
    
    $('.js_select').change(function() {
        var price = $(this).parent().children('input').val();
        var code = $(this).find('option[selected]').val();
        var msg = '';
        var usdprice = '';
        if (code) {
            usdprice = change_code_usd(price, code)
            if (isNaN(usdprice)) {
                msg = '金额输入错误！';
                usdprice = '';    
            } else {
                usdprice = cuttwo(usdprice);
                msg = 'USD： $' + usdprice;
            }
        }
        
        $('input[name=' + $(this).parent().children('input').attr('obj') + ']').val(usdprice);
        
        $(this).parent().children('span').html(msg);
        
        if ( $(this).parent().children('input').attr('obj') == 'e_rprice' || $(this).parent().children('input').attr('obj') == 'price') {
            get_e_sprice();
            $('input[name=profit_display]').attr("value",$('input[name=e_aprice]').val());
        }
    });
    
    if ($('input[name=checkid]').val() != '') {
        $('select[name=stockware]').removeAttr('disabled');
    }
})
var tout;
/*显示采购情况*/
$(function(){
    var dx,dy;
    $('a.showprocess').mouseenter(function(e){
		
		clearTimeout(tout);//清除消除
		
		/*区分列表页或单独页的位置*/
		if(this.target == 'detail'){
			dy = -15;dx = -100;
		}else{
			dy = 140;dx = 20;
		}
		
        CommomAjax('post','index.php?stamptime='+Math.random()+'&action=process_upstock&detail=get_moddetail',{'id':this.id},function(msg){                                                                                                  
            var tooltip 	= '<div id=tooltip>'+msg+'</div>';
			
			//前置清除
			$("#tooltip").remove();
            
            //把它追加到文档中
            $("body").append(tooltip);
            $("#tooltip").css({
                "top"                	: (e.pageY-dy) + "px",
                "left"                	: (e.pageX+dx)  + "px",
				"text-align"			:"left",
                "width"                	: "200px",
                "height"            	: "110px",
                "position"            	:'absolute',
                'background-color'    	:'#ffffff',
                'font-size'            	:'12px',
                'color'                	:'black',
                'border'            	:'#cdcdcd 1px solid',
                'line-height'        	:'18px',
                'padding'            	:'5px 10px 10px 10px',
                'margin'            	:'0px',
                'box-shadow'        	:'1px 1px 1px #77a5cf',
                '-webkit-box-shadow'	:'1px 1px 1px #77a5cf',
                '-moz-box-shadow'    	:'1px 1px 1px #77a5cf'
            }).show("fast");                    
        });
    }).mouseleave(function(){
        $('#tooltip').remove();
		tout = setTimeout('disappear()',500);//定时器防止订单号间快速切换时，有时DIV不消失
    });
})

/*提示框消失*/
function disappear(){
	$('#tooltip').remove();
}

/*保留两位小数*/
function cuttwo(obj){
    return Math.round((Math.floor(obj*1000)/10))/100;
} 

/*传入价格和货币汇率转换成美元价格*/
function change_code_usd(price, code) {
    return price * 100 / code;
}

/*选择仓库后，取出该仓库的已在备货数，采购途数, 仓库可发数*/
function get_in_wcbsums(houseid){
    var sku = $('input[name=sku]').val();
    var checkid = $('input[name=checkid]').val();
    if(!sku) {alert('请输入SKU再选择仓库');return false;}

    $.getJSON('index.php?action=process_upstock&detail=get_in_wcbsums',{'sku':sku,'houseid':houseid,'checkid':checkid},function(msg){    
        if (msg.sums >= 0) {
            $('input[name=e_inware]').attr('value',msg.sums);
            $('input[name=e_fbainware]').val(msg.fbainstock);
            $('input[name=e_inwareching]').attr('value',msg.instock);
            $('input[name=e_instocking]').attr('value',msg.upstock);
            $('input[name=e_inware]').parent().next().children('span').html(' 可发库存 pcs');
        }
        else {
            $('input[name=e_inware]').val(0);
            $('input[name=e_fbainware]').val(0);
            $('input[name=e_inwareching]').val(0);
            $('input[name=e_instocking]').val(0);
            $('input[name=e_inware]').parent().next().children('span').html('<font style="color:red;">sku未录入别名，无法读取平台库存。</font>');
        }
    });
}

/*备货申请取得供货商、账期、价格列表*/
function getsupplierlist(pid){
	$.getJSON('index.php?action=process_upstock&detail=getupstocklist',{'pid':pid},function(data){
			if (data.data){
				var sel = '';
				sel += '<option value="">--请选择--</option>';
				$.each(data.data, function(i, field){
				sel += '<option value='+field.id+'>'+field.esseid+'&nbsp;|&nbsp;'+field.issuetime+'&nbsp;|&nbsp;'+field.account+'</option>';
				});
				$("#provider_id").html(sel);
			}else{
				$("#provider_id").html('<option value="">--请选择--</option>');
			}
	});
}

/*ajax检测SKU是否存在*/
function checksku(obj){
    if (obj == '') {
		$('.tips_产品SKU').html('&times;&nbsp;不可为空');
		$("#provider_id").html('');
        return false;
    }
    $.getJSON('index.php?action=process_upstock&detail=checksku',{'sku':obj},function(msg){
        if(msg.pid){
            $('.tips_产品SKU').html('&radic;&nbsp;存在');
            $('input[name=pid]').val(msg.pid);
            $('input[name=product_name]').val(msg.product_name);
            $('input[name=cost2]').val(msg.cost2);
            $('input[name=e_upc_or_ean]').val(msg.upc_or_ean);
            $('select[name=stockware]').removeAttr('disabled');
            get_in_wcbsums($('select[name=stockware]').find('option[selected]').val());
			getsupplierlist(msg.pid);
        }else{
            $('.tips_产品SKU').html('&times;&nbsp;不存在');
            $('input[name=pid]').val('');
            $('input[name=product_name]').val('');
            $('input[name=cost2]').val('');
            $('input[name=e_upc_or_ean]').val('');
            $('input[name=e_inware]').val('');
            $('input[name=e_fbainware]').val('');
            $('input[name=e_inwareching]').val('');
            $('input[name=e_instocking]').val('');
            $('input[name=e_inware]').parent().next().children('span').html(' 可发库存 pcs');
            //$('select[name=stockware]').find('option[selected]').removeAttr('selected');
            document.getElementById('stockware').selectedIndex = 0;
            $('select[name=stockware]').attr('disabled', 'disabled');
        }
    });
}

/*批量删除与审核,接收*/
function audit(obj){
    var confmsg,url;
    
    if(obj == 'del'){
        confmsg = '确认删除？';url = 'index.php?action=process_upstock&detail=deletefull';
    }else if(obj == 'che') {
        confmsg =  '确认审核？';url = 'index.php?action=process_upstock&detail=auditfull&sign=1';
    }else if(obj == 'unche') {
        confmsg =  '确认审核？';url = 'index.php?action=process_upstock&detail=auditfull&sign=2';
    }else if(obj == 'rec') {
        confmsg =  '确认接收？';url = 'index.php?action=process_upstock&detail=auditfull&sign=3';
    }else if(obj == '') {
        return;
    }
    
    if(confmsg){
        var fg=window.confirm(confmsg);
        if(!fg){
            return;
        }
    }
    
    var obj=document.getElementsByName('checkmod[]');
    var strID='';
    for(var i=0;i<obj.length;i++){
        obj[i].checked?strID +=obj[i].value+',':'';
    }
    
    if(!strID.length){
        alert('请您选择数据');
        return;
    }
    
    strID = strID.substr(0,strID.length-1);
    
    CommomAjaxNew('POST',url,{'strid':strID},isloading('body',0,''),function(msg){closeloading();alert(msg);if(msg=='删除成功'||msg=='审核成功'){window.location.reload();}else if(msg == '已接收'){window.location='index.php?action=process_upstock&detail=list&statu=3'}});
}

/*设定不能为空对象*/
function checkform(){
    if($("select[name=stockware]").val()==''){
        alert('请选择备货仓库！');
        return false;
    }
    
    if($("input[name=sku]").val()==''){
        alert('SKU不能为空！');
        $("input[name=sku]").focus();
        return false;
    }
    
    if($("input[name=e_stockname]").val()==''){
        alert('备货名称不能为空！');
        $("input[name=e_stockname]").focus();
        return false;
    }
    
    if($("input[name=e_quantity]").val()==''){
        alert('备货数量不能为空！');
        $("input[name=e_quantity]").focus();
        return false;
    }
    
    if($("input[name=price]").val()==''){
        alert('单个总成本不能为空！或币别未选择');
        $("input[name=price]").focus();
        return false;
    }
    
    if($("input[name=e_aprice]").val()==''){
        alert('单个利润不能为空！！或币别未选择');
        $("input[name=e_aprice]").focus();
        return false;
    }
    
    if($("input[name=e_rprice]").val()==''){
        alert('销售价格不能为空！！或币别未选择');
        $("input[name=e_rprice]").focus();
        return false;
    }
    
    if($("input[name=e_futureself]").val()==''){
        alert('销售预估不能为空！');
        $("input[name=e_futureself]").focus();
        return false;
    }
	
	if($("input[name=buytime]").val()== ''){
		alert('预估采购时间不能为空！');
		$("input[name=buytime]").focus();
		return false;
	}
	
	if($("select[name=provider_id]").val() =='' ){
		alert('供应商账期不能为空！');
		return false;
	}
}

/*自动计算总利润*/
function get_e_sprice(){
    var profit = $('input[name=e_rprice]').val()-$('input[name=price]').val();
    var val = $('input[name=e_quantity]').val() * profit;
    $('input[name=e_aprice]').attr('value',cuttwo(profit));
    $('input[name=e_sprice]').attr('value',cuttwo(val));
    
    if($('input[name=e_aprice]').val() != 0 && $('input[name=e_rprice]').val() !=0){
        profit_tips = $('input[name=e_aprice]').val()/$('input[name=e_rprice]').val()
        profit_tips_str = "<font color='red'><strong>利润率：" + cuttwo(profit_tips * 100) + "%</strong></font>";
        $('#profit_tips').html(profit_tips_str);
    }    
}

//计算总成本及总利润
$(function(){
    $('#mytable tr').click(function(){
        var e_quantity = 0;
        var price = 0;
        var total_cost = 0;
        var gross_profits = 0;
        
        if ($("input:checked").length > 0) {
            $('input:checked[name^=checkmod]').each(function(){
                //总成本
                var e_quantity_string = $(this).parents('tr').children('td:eq(17)').html();
                var e_quantity = e_quantity_string.replace('&nbsp;','');
                var e_quantity = e_quantity.replace(',','');
                
                var price_string = $(this).parents('tr').children('td:eq(19)').children('#cost1').html();
                var price = price_string.replace('&nbsp;','');
                var price = price.replace(',','');
                
                total_cost = total_cost + (parseInt(e_quantity) * parseFloat(price));
                $('#total_cost').html('总成本：' + total_cost.toFixed(2) + ' USD');
                
                //总利润
                var e_sprice_string = $(this).parents('tr').children('td:eq(23)').html();
                var e_sprice = e_sprice_string.replace('&nbsp;','');
                var e_sprice = e_sprice.replace(',','');
                gross_profits = gross_profits + parseFloat(e_sprice);
                $('#gross_profits').html('总利润：' + gross_profits.toFixed(2) + ' USD');
            });
        } else {
            $('#total_cost').html('');
            $('#gross_profits').html('');
        }
    });
});
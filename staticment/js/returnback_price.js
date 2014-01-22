$(function(){
	//退货单使用
	var price_array = eval(price_list);
	var price_num = price_array.length;
	for (i=0; i<price_num; i++) {
		$('.wall_focus_check').eq(i).data('item_price',price_array[i].item_price);
		$('.wall_focus_check').eq(i).data('e_item_tax',price_array[i].e_item_tax);
		$('.wall_focus_check').eq(i).data('e_shipping_price',price_array[i].e_shipping_price);
		$('.wall_focus_check').eq(i).data('e_shipping_tax',price_array[i].e_shipping_tax);
		$('.wall_focus_check').eq(i).data('haveremony',price_array[i].haveremony);
		$('.wall_focus_check').eq(i).data('out_price',price_array[i].out_price);										
	}
	$('.wall_returnback_price').change(function() {
		if ($(this).attr('checked')) {
			$(this).siblings('.wall_disabled_check').removeAttr('disabled');
			$(this).after($('<input type="hidden" name="price_r[]" class="wall_hidden_check"/>'));
			change_price($('input[name=rate]').val())
		}
		else {
			$(this).siblings('.wall_disabled_check').attr('disabled','disabled');
			$(this).siblings('span').html('');
			$(this).siblings('.wall_hidden_check').remove();
			
		}
	});
	
	
	$('.wall_focus_check').focus(function() {
		var index = $(this).index('.wall_focus_check');
		index = parseInt(index / 2);
		$('input[name=item_price]').val($('.wall_focus_check').eq(index).data('item_price'));
		$('input[name=e_item_tax]').val($('.wall_focus_check').eq(index).data('e_item_tax'));
		$('input[name=e_shipping_price]').val($('.wall_focus_check').eq(index).data('e_shipping_price'));
		$('input[name=e_shipping_tax]').val($('.wall_focus_check').eq(index).data('e_shipping_tax'));
		$('.wall_shipment_total').html('&nbsp; 可退金额约 $<span id=haveremony>'+$('.wall_focus_check').eq(index).data('haveremony') + '</span>，之前已退: $' + $('.wall_focus_check').eq(index).data('out_price'));
	}).blur(function() {
		$('input[name=item_price]').val('');
		$('input[name=e_item_tax]').val('');
		$('input[name=e_shipping_price]').val('');
		$('input[name=e_shipping_tax]').val('');
		$('.wall_shipment_total').html('');
	});
	
	$('.wall_price_change').keyup(function(e) {
		var showhtml = 0;
		var msg = check_isnum_fun($(this).val(), 1, 2);
		if (msg != '') {
			$(this).siblings('span').html(msg);
			return false;
		}
		/*如果汇率还没选*/
		if($('select[name=coin_code]').val() == '')
		{
			showhtml = cuttwo($(this).val());
		}		
		/*如果选择了汇率,按汇率折算*/
		else
		{
			showhtml = $(this).val()*100/$('input[name=rate]').val();			
		}
		$(this).siblings('.wall_hidden_check').val(cuttwo(showhtml));
		$(this).siblings('span').html(' 折算美元：$'+cuttwo(showhtml));
	});
	
	$('select[name=coin_code]').change(function() {
		if($(this).val() == '')
		{				
			$('input[name=rate]').val('');			
			change_price('');			
			return false;
		}
		
		CommomAjax('post','index.php?action=exchange_rate&detail=getrate',{'code':$(this).val(),'type':'rate'},function(rate){
			$('input[name=rate]').val(rate);			
			change_price(rate);
		});
	});
	
	$('#wall_shipment_form').submit(function() {
		var errormsg = '';
		if($('select[name=coin_code]').val() == ''){
			alert('请选择币种！');
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
		if ($('input[name=extends]').val() == '') {
			alert('请填写退款帐号！');
			$('input[name=extends]').focus();
			$('input[name=extends]').select();
			return false;
		}
		if ($('input[name=comment2]').val() == '') {
			alert('请填写联系人！');
			$('input[name=comment2]').focus();
			$('input[name=comment2]').select();
			return false;
		}
		if ($('input[name=sold_id]').val() == '') {
			alert('请填写平台ID！');
			$('input[name=sold_id]').focus();
			$('input[name=sold_id]').select();
			return false;
		}
		$('.wall_returnback_price').each(function() {
			if ($(this).attr('checked')) {				
				if((errormsg =check_isnum_fun($(this).siblings('.wall_price_change').val(), 1, 2)) != '') {					
					return false;
				}
				if($(this).siblings('.wall_shipment_order_id').val() == '交易ID') {
					errormsg = '交易ID不能为空！';
					return false;
				}
			}
		});
		if(errormsg) {
			alert(errormsg);
			return false;
		}
		for (var i = 0; i < price_num; i++) {			
			if (!$('.wall_price_change').eq(i).attr('disabled')) {
				if ($('.wall_focus_check').eq(i).data('haveremony') < parseFloat($('.wall_price_change').eq(i).siblings('.wall_hidden_check').val())) {
					alert('退款金额不得大过可退金额！');
					return false;
				}
			}
		}
	});
});

//statu 0:表示获得焦点;1:表示失去焦点
function default_input(obj, default_str, statu) {
	if (statu == 1 && trim(obj.val()) == '') {
		obj.val(default_str);
	}
	if (statu == 0 && trim(obj.val()) == default_str) {
		obj.val('');
	}
}

//根据传入值修改
function change_price(rate) {
	/*输入了price再来更改币别，自动重新换算*/
	$('.wall_price_change:not(:disabled)').each(function() {
		if ($(this).val() == '退款金额') {
			$(this).siblings('span').html('');
			$(this).siblings('.wall_hidden_check').val('');
		}
		else {
			var nval = $(this).val();
			if (parseFloat(rate)) {
				var nval = nval * 100 / rate;
			}
			$(this).siblings('.wall_hidden_check').val(cuttwo(nval));
			$(this).siblings('span').html(' 折算美元：$'+cuttwo(nval));
		}
	});
}
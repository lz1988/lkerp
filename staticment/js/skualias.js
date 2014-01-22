$(function() {
	$('input[name*=sku]').focus(function(){		
		$(this).css('color', '#000000');
		$('#subinput').hide();	
	}).blur(function() {
		num=0
		$('input[name*=sku]').each(function() {
			if ($(this).val() == '') {
				$(this).focus();
				$(this).parent().next().children('span').html('not null').css('color','red');	
				num++;			
			}
			else {			
				$(this).parent().next().children('span').html('').css('color','red');
			}
		});	
		if	($('input[name=pro_sku]').val()==$('input[name=sku_code]').val()) {
			$('input[name=sku_code]').select();
			$('input[name=sku_code]').parent().next().children('span').html('sku别名不能与原名相同').css('color','red');	
			num++;			
		}
		if (!num) {
			$.getJSON("index.php?action=sku_alias&detail=checksku",
				{"pro_sku":$('input[name=pro_sku]').val(),"sku_code":$('input[name=sku_code]').val(),"id":$('input[name=id]').val()},
				function(msg){
	   				if(msg.length) {						
						for(i=0; i<msg.length; i++) {
							if(msg[i] == $('input[name=pro_sku]').val()) {
								$('input[name=pro_sku]').parent().next().children('span').html('产品SKU不存在').css('color','red');
							}
							if(msg[i] == $('input[name=sku_code]').val()) {
								$('input[name=sku_code]').parent().next().children('span').html('SKU别名已存在').css('color','red');
							}
						}											
	   				}
	   				else {
	   					$('#subinput').show();
	   				}
	   			}
	   		);
		}
	});	
});

$(function() {
	$('#ture_submit select').change(function() {
		if ($(this).val() == '') {
			$('#ture_submit #submit_once').hide().next().hide();
		}
		else {
			$('#ture_submit #submit_once').show().next().show();
		}
	});
});


$(function(){
	$('input[name$=stage_rate]').blur(function(){
		var pricetag = /^([0-9]{4})[.]([0-9]{2})$/;
		if ($(this).val() != '' && !pricetag.exec($(this).val())) {
			alert('会计日期格式（2001.10）错误！重新填写！');
			$(this).focus();
			$(this).select();
			return false;
		}	
		if ($('input[name$=stage_rate]').not($(this)).val() == '') {
			$('input[name$=stage_rate]').not($(this)).val($(this).val());
		}	
		if ($(this).val() == '') {
			$(this).val($('input[name$=stage_rate]').not($(this)).val());
		}
		if ($(this).val() != '') {
			$('input[name$=Time]').val('');
		}
	});
	
	$('input[name$=Time]').blur(function(){
		if ($('input[name$=Time]').not($(this)).val() == '') {
			$('input[name$=Time]').not($(this)).val($(this).val());
		}
		if ($(this).val() != '') {
			$('input[name$=stage_rate]').val('');
		}
	}); 
})
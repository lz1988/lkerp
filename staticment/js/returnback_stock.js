$(function(){
	//退货单使用
	$('.wall_returnback_stock').change(function() {
		if ($(this).attr('checked')) {
			$(this).prev().removeAttr('disabled');
		}
		else {
			$(this).prev().attr('disabled','disabled');
		}
	});
});
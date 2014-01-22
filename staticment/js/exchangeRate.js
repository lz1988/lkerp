// JavaScript Document
$(function() {
	$('.wall_click').click(function() {
		$(this).hide();
		var show = $(this).next();
		show.val($(this).html());
		show.show();
		show.focus();
		show.select();
	});
	
	$('.wall_edit').blur(function(){
		var $this = $(this);
		var id = $(this).attr('rate_id');
		var rate = $(this).val();
		var detail = $(this).attr('detail');
		$.post('index.php?action=exchange_rate',{'id' : id, 'rate' : rate, 'detail' : detail},function(msg){
					closeloading();
					if(msg==1){
						$this.hide();
						$this.prev().html($this.val()).show();
						window.location.reload();
					}else{alert(msg)}
				});
	});
});
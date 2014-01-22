function onreaded() {
	var idarr = new Array();
	var i = 0;
	$('input[name^=checkmod]').each(function(){
		if ($(this).attr('checked')) {
			idarr[i] = $(this).val();
			i++;
		}
	});
	
	if (i == 0) {
		alert('未选中任何公告！');
		return false;
	}
	
	$.post('index.php?action=announcement&detail=readed',{'id':idarr},function(msg) {
		self.parent.parent.topFrame.readed_msg('wall_announcement_remind', msg);
		$('input[name^=checkmod]').each(function(){
			if ($(this).attr('checked')) {
				$(this).parent().parent('tr').removeClass('selected');					
				$(this).parent().parent('tr').find('b').each(function() {
					$(this).replaceWith($(this).html());
				});
				$(this).parent().next().children('img').attr('src','./staticment/images/mail_open.gif');
			}
		});
		$('input[type=checkbox]').removeAttr('checked');
				
	});
}
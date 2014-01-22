// JavaScript Document
(function($){
	$.fn.mylink = function(options) {
		var $this = $(this);
		var opts = $.extend({}, $.fn.defaults, options);
		defaultCss(opts);
		
		$this.click(function() {
			window.location.href = opts.src;
		});
		
		function defaultCss($opts) {
			$this.css('cursor', $opts.cursor);
		}
	};
	
	$.fn.defaults = {
		cursor : 'pointer'
	};
})(jQuery);

$(function() {
	var againlen = 0;
	$('.sendagain').each(function() {
		$(this).data('id', arr[againlen]);
		againlen++;
	});
	
	$('.sendagain').live('click',function() {
		var $this = $(this);
		var $parenttd = $this.parent();
		if ($parenttd.children('span')) {
			$parenttd.children('span').remove();
		}
		$parenttd.append('<span><img src="./staticment/images/loading.gif" class="loadimg" />邮件发送中...</span>');
		$this.removeClass('sendagain');
		failednum-- ;
		$.post(
			"index.php?action=department_message&detail=mail",
			{'content':$('.mailout_body').html(),'mail':$(this).parent().prev().html(),'sl_id':$(this).data('id'), 'ms_id':ms_id, 'failednum' : failednum},
			function(msg){				
				failednum += parseInt(msg);
				//console.log(failednum + '::' +msg+'\n');
				if (msg == 0) {
					$parenttd.html('已投递到对方邮箱<span><img src="./staticment/images/sendsuccess.gif" class="loadimg" />投递成功</span>');
					$parenttd.children('span').css('color', 'green');
				}
				else {
					$this.addClass('sendagain');
					$parenttd.children('span').html('<img src="./staticment/images/sendfailed.gif" class="loadimg" />投递失败，请重试');
					$parenttd.children('span').css('color', 'red');
				}
				if (failednum == 0) {
					$('.attbg').children('span').children('b').html('投递成功');
				}
			}
		); 	
	});
	
	$('#mytable td').each(function(){
		$(this).mylink({src : 'index.php?action=department_message&detail=sendpage&id=' + $(this).children('span').text()});
	});
	
	$('.clickstu').toggle(function() {		
		$('.mailout_status').show();
		$(this).html('[关闭]');
	},function() {
		$('.mailout_status').hide();
		$(this).html('[查看详情]');
	});	
});
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

$(function(){
	$('#mytable td').each(function(){
		$(this).mylink({src : $(this).children('span').attr('src')});
	});
});
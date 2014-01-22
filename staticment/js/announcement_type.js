$(function(){
	$('#color').click(function(){
		var bg = $('<div></div>');
		var obj = $('<div></div>');		
		
		bg.css({'height':'100%','width':'100%','position':'absolute', 'opacity':'0.1', 'left':'0','top':'0','z-index':'1000','background-color':'#111111'});
		bg.click(function() {
			bg.remove();
			obj.remove();
		});
		obj.addClass('wall_color');
		objleft = $('#commomform').width();
		objtop  = $('#commomform').height();
		
		obj.css({
				'position': 'absolute',
				'top': objtop/2,
				"left": objleft+50,
				'z-index': '1001'
			});
		$('body').append(bg).append(obj);
		$.farbtastic('.wall_color').linkTo('#color');		
		
	});	
});
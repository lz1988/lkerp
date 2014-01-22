$(function(){
	var parenttr = null;
	function remove_msgshow(removetr, parenttr) {
		$('.message_show_bg').remove();
		$('.message_show_obj').remove();
		if (removetr) {
			parenttr.remove();
			self.parent.parent.topFrame.readed_msg('wall_friendadd_remind', 1);		
		}
	}
	function friend_message(friendname, status, friendid, parenttr) {
		var message = friendname;
		var button = '';
		if (status == 1) {
			message += '请求添加您为好友！';	
			button += '<span a=' + friendid + '><input class="friend_agree" type="button" value="同意"/><input class="friend_refuse" type="button" value="拒绝"/><input class="friend_lgnore" type="button" value="忽略"/></span>';
		}
		else {
			if (status == 2) {
				message += '接受了您的添加请求并添加您为好友！';		
			}
			else {
				message += '拒绝了您的添加请求！';	
			}
			button += '<input class="close_pop_div" type="button" value="返回"/>';
		}
		$('.g-dialogBox-text').html(message);	
		$('.g-dialogBox-ft-oprt').html(button);
	}
	$('#mytable td').css('cursor', 'pointer').live('click',function(){
		var removetr = 0;		
		var msgstatus = $(this).children('span').attr('b');			// 消息状态，1为好友请求，需要处理，其他为提示信息，看过后自动从列表中删除
		var friendid = $(this).children('span').attr('a');
		var bg = $('<div class="message_show_bg"></div>');
		var obj = $('<div class="message_show_obj"></div>');		
		
		parenttr = $(this).parent();	
			
		bg.css({'height':'100%','width':'100%','position':'absolute', 'opacity':'0.1', 'left':'0','top':'0','z-index':'1000','background-color':'#111111'});
				
		var windowWidth = document.body.offsetWidth ;
		var windowHeight = document.body.offsetHeight;
		var popupHeight = 100;
		var popupWidth = 500;
		
		//console.log(windowWidth + ' ' + windowHeight + ' ' + popupHeight + ' ' + popupWidth + ' ' + msgstatus);
		obj.css({
				'position': 'absolute',
				"top": windowHeight/2-popupHeight/2,
				"left": windowWidth/2-popupWidth/2,
				'width': 500,
				'height': 100,
				'z-index': '1001',
				'background-color': '#FFF',
				'border-radius': '2px 2px 0 0',
				'box-shadow': '0px 0px 15px #888888'
			});
		obj.append($('<div></div>').addClass('g-dialogBox-hd')
			.append($('<span>消息验证</span>').css('font-size', '14px')).append($('<span id="close_pop_div">x</span>').addClass('close_pop_div'))
			)
		.append($('<div></div>').addClass('g-dialogBox-bd').append($('<div></div>').addClass('g-dialogBox-iconText').append($('<div></div>').addClass('g-dialogBox-text'))))
		.append($('<div></div>').addClass('g-dialogBox-ft').append($('<div></div>').addClass('g-dialogBox-ft-oprt')));
		if (msgstatus != 1) {
			$.post('index.php?action=friend_communication&detail=clear_friend_msgstatus',{'friendid': friendid}, function(msg){
				if (msg == 1) {
					removetr = 1;
				}
				else {
					removetr = 0;
				}
			});
		}	
		else {
			removetr = 0;
		}	
		$('body').append(bg).append(obj);
		friend_message(parenttr.find('.friend_name').html(),msgstatus, friendid, parenttr);
		$('.close_pop_div').click(function() {
			remove_msgshow(removetr, parenttr);	
			return false;
		});
	});	
	
	$('.friend_agree').live('click',function() {
		$.getJSON('index.php?action=friend_communication&detail=oper_message',
					{'friendid': $(this).parent().attr('a'),
						'oper' : '1'},
					function(msg){						
						if (msg.status) {
							remove_msgshow(1,parenttr);
						}
						alert(msg.msg);
					});
		return false;
	});
	$('.friend_refuse').live('click',function() {
		$.getJSON('index.php?action=friend_communication&detail=oper_message',
					{'friendid': $(this).parent().attr('a'),
						'oper' : '2'},
					function(msg){						
						if (msg.status) {
							remove_msgshow(1,parenttr);
						}
						alert(msg.msg);
					});
		return false;
	});
	$('.friend_lgnore').live('click',function() {
		$.getJSON('index.php?action=friend_communication&detail=oper_message',
					{'friendid': $(this).parent().attr('a'),
						'oper' : '3'},
					function(msg){		
						//console.log(msg.status);				
						if (msg.status) {
							remove_msgshow(1,parenttr);
						}
					});
		return false;
	});
	$('.float_image').hover(function(){
		parenttr = $(this).parent().parent().parent();
		$(this).css({height:'20',width:'20'});
	},function(){
		$(this).css({height:'16',width:'16'});
	});
});
$(function(){
	var showtime = null;
	$.getJSON('index.php?action=msg&detail=msg',function(msg){
		if (msg.count > 0) {
			var msg_div = '<div class="wall_message_remind" style="text-align: left;">消息提醒</div>';
			$('#newmsgimg').attr('src', './staticment/images/top/newmail.gif');
			$('#HyperLink1').css('color', 'yellow');
			if (msg.announcement_count > 0) {
				msg_div += '<div class="wall_announcement_remind wall_message_remind_content" style="text-align: left;">&nbsp;&nbsp;<span class="wall_announcement_remind_name wall_message_remind_content_first">'+msg.announcement_name+'</span>(<span class="wall_announcement_remind_count">'+msg.announcement_count+'</span>)</div>';
			}
			if (msg.friendadd_count > 0) {
				msg_div += '<div class="wall_friendadd_remind wall_message_remind_content" style="text-align: left;">&nbsp;&nbsp;<span class="wall_friendadd_remind_name wall_message_remind_content_first">'+msg.friendadd_name+'</span>(<span class="wall_friendadd_remind_count">'+msg.friendadd_count+'</span>)</div>';
			}
			if (msg.friendmsg_count > 0) {
				msg_div += '<div class="wall_friendmsg_remind wall_message_remind_content" style="text-align: left;">&nbsp;&nbsp;<span class="wall_friendmsg_remind_name wall_message_remind_content_first">'+msg.friendmsg_name+'</span>(<span class="wall_friendmsg_remind_count">'+msg.friendmsg_count+'</span>)</div>';
			}
			$('#wall_msg_count').html(msg_div);
		}
		$('#HyperLink1').html(msg.count);
	});	
	
	setInterval("check_announcement()", 1800000);
	setInterval("check_friendadd()", 300000);
	setInterval("check_friendmsg()", 300000);
	$('.wall_show_msg_count').hover(function(ev){
		clearTimeout(showtime);
		var Ev = ev || window.event;
		$('#wall_msg_count').css({'left':Ev.clientX,'top':Ev.clientY+15}).show();
	},function(ev){
		showtime = setTimeout(function(){$('#wall_msg_count').hide()}, 100);
	});
	$('#wall_msg_count').hover(function() {
		clearTimeout(showtime);
	},function() {
		$(this).hide();
	});
	$('.wall_announcement_remind').live('click', function() {
		self.parent.mainFrame.addMenutab(1006,'公司公告','index.php?action=announcement&detail=show_list&ml_msgid=2');
	});
	$('.wall_friendadd_remind').live('click', function() {
		self.parent.mainFrame.addMenutab(1007,'验证消息','index.php?action=friend_communication&detail=check_message_list');
	});
	$('.wall_friendmsg_remind').live('click', function() {
		self.parent.mainFrame.addMenutab(1008,'好友留言','index.php?action=friend_communication&detail=new_leave_message_list');
	});
	
	/*$('.wall_show_msg_count').click(function(){
		var msg = {'name':'name','count':'1'};
		change_message_remind('wall_announcement_remind', msg);
		check_announcement();
		check_friendadd();
		return false
	});*/
});

function change_message_remind(class_name, msg) {
	var count = parseInt($('#HyperLink1').html());
	var child_count = parseInt($('.'+class_name+'_count').html());
	msg.count = parseInt(msg.count);
	if (count == 0) {
		var msg_div = '<div class="wall_message_remind" style="text-align: left;">消息提醒</div>';
		msg_div += '<div class="'+class_name+' wall_message_remind_content" style="text-align: left;">&nbsp;&nbsp;<span class="'+class_name+'_name wall_message_remind_content_first">'+msg.name+'</span>(<span class="'+class_name+'_count">'+msg.count+'</span>)</div>';
		$('#wall_msg_count').html(msg_div);
	}
	else if (isNaN(child_count)) {				
		$('#wall_msg_count').append($('<div class="'+class_name+' wall_message_remind_content" style="text-align: left;">&nbsp;&nbsp;<span class="'+class_name+'_name wall_message_remind_content_first">'+msg.name+'</span>(<span class="'+class_name+'_count">'+msg.count+'</span>)</div>'));
		msg.count += count;
	}
	else {
		$('.'+class_name+'_count').html(msg.count);
		msg.count += count - child_count;
	}
	$('#newmsgimg').attr('src', './staticment/images/top/newmail.gif');
	$('#HyperLink1').css('color', 'red').html(msg.count);
}

function check_announcement() {
	$.getJSON('index.php?action=msg&detail=check_announcement',function(msg){		
		if (msg != 0) {
			change_message_remind('wall_announcement_remind',msg);
			/*$('#newmsgimg').attr('src', './staticment/images/top/newmail.gif');
			$('#HyperLink1').css('color', 'red').html(msg.count);*/
		}		
	});	
}
function check_friendadd() {
	$.getJSON('index.php?action=msg&detail=check_friend_add',function(msg){		
		if (msg != 0) {
			change_message_remind('wall_friendadd_remind',msg);
			/*$('#newmsgimg').attr('src', './staticment/images/top/newmail.gif');
			$('#HyperLink1').css('color', 'red').html(msg.count);*/
		}		
	});	
}
function check_friendmsg() {
	$.getJSON('index.php?action=msg&detail=check_friend_msg',function(msg){		
		if (msg != 0) {
			change_message_remind('wall_friendmsg_remind',msg);
			/*$('#newmsgimg').attr('src', './staticment/images/top/newmail.gif');
			$('#HyperLink1').css('color', 'red').html(msg.count);*/
		}		
	});	
}

/*function sub_msg() {
	var num = parseInt($('#HyperLink1').html());
	num--;
	if (num <= 0) {
		$('#newmsgimg').attr('src', './staticment/images/top/mail.gif');
        num = 0;
	}
	$('#HyperLink1').css('color', 'red').html(num);
}
*/
function readed_msg(class_name, num) {
	var total = parseInt($('#HyperLink1').html());
	var count = parseInt($('.'+class_name+'_count').html());
	var showcount = 0;
	if (!isNaN(count)) {
		showcount = count - num;	
		if (showcount >= 0) {
			total -= num;	
		}
		else {
			total -= count;
		}
		if (showcount <= 0) {
			$('.'+class_name).remove();
		}
		else {
			$('.'+class_name+'_count').html(showcount);			
		}
		if (total <= 0) {
			$('#newmsgimg').attr('src', './staticment/images/top/mail.gif');
        	total = 0;
			$('#wall_msg_count').html('');
		}
		else {
			$('#newmsgimg').attr('src', './staticment/images/top/newmail.gif');		
		}
		$('#HyperLink1').css('color', 'red').html(total);
	}		
}


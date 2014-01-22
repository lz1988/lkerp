if (uid > 0) {
	KindEditor.ready(function(K) {
					editor1 = K.create('textarea[name="content"]', {
					items : ['bold', 'italic', 'underline', 'fontname', 'forecolor', 'hilitecolor', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|', 'insertorderedlist', 'insertunorderedlist', '|', 'indent', 'outdent', '|', 'link', 'source'],
					cssPath : 'editor/plugins/code/prettify.css',
					uploadJson : 'editor/php/upload_json.php',
					fileManagerJson : 'editor/php/file_manager_json.php',
					allowFileManager : true,
					afterCreate : function() {
						var self = this;
						K.ctrl(document, 13, function() {
							self.sync();
							K('form[name=example]')[0].submit();
						});
						K.ctrl(self.edit.doc, 13, function() {
							self.sync();
							K('form[name=example]')[0].submit();
						});
					}
				});
	});
}

// 消除字符串两边空格
function trim(str) {
	var notValid = /(^\s)|(\s$)/;
	while (notValid.test(str)) {
		str = str.replace(notValid, '');
	}
	return str;
}

// 查看好友留言详细
$(function() {
	if (unreadcount > 0) {
		self.parent.parent.topFrame.readed_msg('wall_friendmsg_remind', unreadcount);	
	}
	// 留言回复
	$('.reply_message_a').live('click',function(){
		var parentdiv = $(this).parent().parent();
		var replydiv = parentdiv.find('.message_content_in');
		$('.reply_message_div').html('<input class="reply_message_input textinput" type="text" value="我也说一句..."/>');
		if(replydiv.html() == null) {
			parentdiv.append($('<div class="message_content_in"><ul><li><div class="reply_message_div" style="clear:both"><textarea class="reply_message_area textarea"></textarea><input class="reply_message_submit" type="button" value="确定"/></div></li><ul></div>'));
		}
		else {
			replydiv.find('.reply_message_input').parent().html('<textarea class="reply_message_area textarea"></textarea><input class="reply_message_submit" type="button" value="确定"/>');
		}
		$('.reply_message_area').focus();
		$('.reply_message_area').select();		
		return false;
		
	});
	// 可回复输入框获取焦点
	$('.reply_message_input').live('focus',function(){
		var parentdiv = $(this).parent();
		$('.reply_message_div').html('<input class="reply_message_input textinput" type="text" value="我也说一句..."/>');
		parentdiv.html('<textarea class="reply_message_area textarea"></textarea><input class="reply_message_submit" type="button" value="确定"/>');
		$('.reply_message_area').focus();
		$('.reply_message_area').select();		
	});
	/*$('.reply_message_area').live('blur',function(){
		$(this).parent().html('<input class="reply_message_input textinput" type="text" value="我也说一句..."/>');
	});*/
	// 我要留言
	$('.reply_message_for_friend').live('click',function(){
		editor1.focus();
		editor1.select();
	});
	// 留言输入框 
	$('.reply_message_area').live('click',function(){
		return false;
	});
	// 留言回复提交
	$('.reply_message_submit').live('click', function(){		
		var prev = $(this).prev();
		var content = prev.val();
		if (trim(content) == '') {
			alert('回复不能为空！');
			prev.focus();
			prev.select();
			return false;
		}
		$.post(
			'index.php?action=friend_communication&detail=mod_leave_message',
			{
				'uid':uid,						// 该留言目标被留言用户id
				'content':content,
				'previd':$(this).parents('.wall_leave_message_content').attr('a')			// 该留言第一级留言id			
			},
			function(msg){
			if (msg == '1') {
				window.location.reload();
			}
			else {
				alert('留言失败，请重试！');
			}
		});
		return false;
	});
	if (uid > 0) {
		// 回复丢失焦点，并且非提交按钮时，回复框消失
		$('body').live('click',function(){
			$('.reply_message_div').html('<input class="reply_message_input textinput" type="text" value="我也说一句..."/>');
		});
	}
	// 我要留言提交
	$('.want_to_leave_message').click(function(){
		if (editor1.html() == '') {
			alert('留言信息不能为空！');
			editor1.focus();
			editor1.select();
			return false;
		}
		$.post('index.php?action=friend_communication&detail=mod_leave_message',{'uid':uid,'content':editor1.html()},function(msg){
			if (msg == '1') {
				window.location.href='index.php?action=friend_communication&detail=leave_message_list&uid='+uid;
			}
			else {
				alert('留言失败，请重试！');
			}
		});
	});
});

// 好友留言提醒
$(function(){
	$('#mytable td').not($('input[name^=checkmod]').parent()).css('cursor', 'pointer').click(function(){		
		var id = $(this).children('span').attr('a');
		window.location.href = 'index.php?action=friend_communication&detail=single_leave_message&id=' + id;
	});		
});

// 标记已读
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
		alert('未选中任何留言或回复！');
		return false;
	}
	
	$.post('index.php?action=friend_communication&detail=msg_readed',{'id':idarr},function(msg) {
		self.parent.parent.topFrame.readed_msg('wall_friendmsg_remind', msg);
		window.location.reload();				
	});
}
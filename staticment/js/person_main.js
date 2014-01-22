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
    var ajaxstatu;
	$(function(){
        /*
         * create on 2012-04-23
         * update on 2012-05-10
         * by wall
         * 在查看定制sku时，选择是否按当天查看         
         */
        $('.wall_check_day').live('click', function(){ 
            var $content = $(this).parents('.wall_content');
            ajaxstatu = $content.attr('class');
            var url = $(this).attr('url');
            if($(this).attr('checked')) {
                url += '&isday=1';
            }
            $.post(
				url,
				function(msg){
					$content.html(msg);
				}
			);
        });
		$('.aajax').live('click', function(){
			var $content = $(this).parents('.wall_content');
            ajaxstatu = $content.attr('class');
			$.post(
				$(this).attr('url'),
				function(msg){
					$content.html(msg);
				}
			);
		});
		$('select').live('change',function(){
			var $content = $(this).parents('.wall_content');
            ajaxstatu = $content.attr('class');
			$.post(
				$(this).val(),
				function(msg){
					$content.html(msg);
				}
			);
		});	
		
		/* id为wall_main_flush
		 * 表示局部刷新按钮
		 */
		$('#wall_main_flush').click(function(){
			var $content = $(this).parent().next('.wall_content');
            ajaxstatu = $content.attr('class');
			$.post(
				$(this).attr('url'),
				function(msg){
					$content.html(msg);
				}
			);
		});
			
		/* class名称为wall_flush
		 * 表示局部刷新按钮
		 */
		$('.wall_flush').live('click', function(){
			var $content = $(this).parents('.wall_content');
            ajaxstatu = $content.attr('class');
			$.post(
				$(this).attr('url'),
				function(msg){
					$content.html(msg);
				}
			);
		});
		
		/*class名称为wall_return 表示按钮功能为返回上一层*/
		$('.wall_return').live('click', function(){
			var $content = $(this).parents('.wall_content');
            ajaxstatu = $content.attr('class');
			$.post(
				$(this).attr('url'),
				function(msg){
					$content.html(msg);
				}
			);
		});
		
		/* class名称为wall_include
		 * 表示按钮功能为在当前页打开一个div显示*/
		$('.wall_include').live('click', function(){
		 	//调用函数居中窗口
	    	centerPopup();
	    	//调用函数加载窗口
	    	loadPopup();
            ajaxstatu = $('#popupContact').attr('class');
		 	$.post(
				$(this).attr('url'),
				function(msg){
					$('#popupContact').html(msg);
				}
			);

		 });
		 
		 /*
		  * class名称为wall_jumppage
		  * 表示按钮功能为打开一个新属性页显示目标地址
		  */
		 $('.wall_jumppage').live('click', function(){
			parent.addMenutab(1005,'个人留言簿',$(this).attr('url'));
		 });

		/* class名称为wall_submit
		 * 表示按钮为提交按钮，触发处理过程
		 */
		 $('.wall_submit').live('click', function(){
		 	var str = new Array();
		 	var num = 0;
		 	var isempty = 0;
		 	$(this).prevAll('input[type=text]').each(function(){
		 		if($(this).val() == '' && $(this).attr('class') == 'checkempty') {
		 			alert('SKU is Empty!');
		 			isempty = 1;
		 		}
		 		str[num] = $(this).attr('name') + "=" + $(this).val();
		 		num++;
		 	});
		 	if (isempty) {
		 		return false;
		 	}
		 	var poststr = $(this).attr('url') + '&' + str.join('&');
            ajaxstatu = $('#popupContact').attr('class');
		 	$.post(
				poststr,
				function(msg){
					$('#popupContact').html(msg);
				}
			);
		 });
		
		$.post(
			"index.php?action=announcement&detail=person_center_list",
			function(msg){
				$('.company_announcements .wall_content').html(msg);
			}
		); 

        $.post(
			"index.php?action=job_alerts&detail=list",
			function(msg){
				$('.wall_job_alerts .wall_content').html(msg);
			}
		); 
         
		$.post(
			"index.php?action=center_productcustom&detail=product_custom",
			function(msg){
				$('.wall_sku_customize .wall_content').html(msg);
			}
		);
		
		$.post(
			"index.php?action=friend_communication&detail=list",
			function(msg){
				$('.wall_friend_list .wall_content').html(msg);
			}
		);
        
        $('.wall_content').each(function() {
            var content = $(this);
            content.ajaxStart(function() {
                if (content.attr('class') == ajaxstatu) {
                    content.html('<img src="./staticment/images/loading.gif" class="loadimg" />');
                }
            }).ajaxStop(function(){
                ajaxstatu = '';
            });
        });
	});

	//初始化：是否开启DIV弹出窗口功能
	//0 表示开启; 1 表示不开启;
	var popupStatus = 0;

	//使用Jquery加载弹窗
	function loadPopup(){
		//仅在开启标志popupStatus为0的情况下加载
		if(popupStatus==0){
			$("#backgroundPopup").css({
				"opacity": "0.7"
			});
			$("#backgroundPopup").fadeIn("slow");
			$("#popupContact").fadeIn("slow");
			popupStatus = 1;
		}
	}
	//使用Jquery去除弹窗效果
	function disablePopup(){
		//仅在开启标志popupStatus为1的情况下去除
		if(popupStatus==1){
			$("#backgroundPopup").fadeOut("slow");
			$("#popupContact").fadeOut("slow");
			popupStatus = 0;
		}
	}

	//将弹出窗口定位在屏幕的中央
	function centerPopup(){
		//获取系统变量
		var windowWidth = document.body.offsetWidth ;
		var windowHeight = document.body.offsetHeight;
		var popupHeight = $("#popupContact").height();
		var popupWidth = $("#popupContact").width();

		//居中设置
		$("#popupContact").css({
			"position": "absolute",
			"top": windowHeight/2-popupHeight/2,
			"left": windowWidth/2-popupWidth/2
		});
		//以下代码仅在IE6下有效

		$("#backgroundPopup").css({
			"height": windowHeight
		});
	}
	$(document).ready(function(){
		//执行触发事件的代码区域

		//关闭弹出窗口
        $('#popupContactClose').live('click',function() {
            disablePopup();
        });
		//键盘按下ESC时关闭窗口!
		$(document).keypress(function(e){
			if(e.keyCode==27 && popupStatus==1){
				disablePopup();
			}
		});
	});
	
	
	// 联系部门javascript动作
	
	// 发送完成重置留言面板
	function mailsuccess() {		
		$('.textboxsendto').html('<div></div>');
		$('input[type=hidden]').remove();
		$('.textboxrightcontent').find('input[type=checkbox]').attr('checked', false);
		$('.textboxuser').removeClass('selected');
		$('.turnondepart').removeClass('turnon');
		$('.turnondepart').children().attr('src','./staticment/images/plus_noLine.gif');
		$('.turnondepart').nextAll('ul').hide();
		editor1.html('');
	}
	
	// 发送失败
	function mailfailed() {
		alert('发送失败，请重新发送！');
	}

	$(function() {			
		// 邮件发送中状态
		var ismailstatus = 0;
		// 根据传入JQUERY对象，修改接收者邮箱列表
		function changedReceiveByDepart(obj) {
			obj.parent().parent().next().find('.checkbox').each(function(){
				$(this).attr('checked', !$(this).attr('checked'));
				changedReceiveByUser($(this));
				$(this).attr('checked', !$(this).attr('checked'));
				changedReceiveByUser($(this));
			});
		}
		
		function changedReceiveByUser(obj) {
			if (obj.attr('checked')) {
				$('.textboxsendto').append($('<div></div>').html('<nobr>'+obj.attr('name')+';</nobr>'));
				$('.textboxleft').append($('<input />').attr({'type':'hidden','name':'receiveMail[]'}).val(obj.val()));
			}
			else {
				$('.textboxsendto div').each(function() {
					if($(this).text() == obj.attr('name')+ ';') {
						$(this).remove();
					}					
				});
				$('.textboxleft').children('input[type=hidden]').each(function() {
					if ($(this).val() == obj.val()) {
						$(this).remove();
					}
				});
			}
		}
		
		// 展开部门下的员工
		function openUserList(obj) {
			obj.toggleClass('turnon');
			if (obj.hasClass('turnon')) {
				obj.children().attr('src', './staticment/images/minus_noLine.gif');
				obj.nextAll('ul').show();	
			}
			else {
				obj.children().attr('src', './staticment/images/plus_noLine.gif');
				obj.nextAll('ul').hide()
			}
		}
	
		// 选择复选框改变
		/*$('.textboxcheckbox').live('click', function(){
			var checkbox = $(this).find('.checkbox');
			$(this).toggleClass('selected');			
			checkbox.attr('checked', $(this).hasClass('selected'));			
		});*/
		
		// 部门复选框改变
		$('.checkboxdepart').live('click', function() {
			// 复选框改变，同步修改部门下员工复选框
			if ($(this).attr('checked')) {
				$(this).parent().parent().next().find('.textboxcheckbox').addClass('selected');
			}
			else {
				$(this).parent().parent().next().find('.textboxcheckbox').removeClass('selected');
			}
			$(this).parent().parent().next().find('.checkbox').attr('checked', $(this).attr('checked'));
			changedReceiveByDepart($(this));
		});
		/*$('.textboxdepartment').live('click', function(){
			var checkbox = $(this).find('.checkbox');
			// 复选框改变，同步修改部门下员工复选框
			if ($(this).hasClass('selected')) {
				$(this).next().find('.textboxcheckbox').addClass('selected');
			}
			else {
				$(this).next().find('.textboxcheckbox').removeClass('selected');
			}
			$(this).next().find('.checkbox').attr('checked', checkbox.attr('checked'));
			changedReceiveByDepart(checkbox);
		});*/
		
		// 员工复选框改变
		$('.textboxuser').live('click', function() {
			var checkbox = $(this).find('.checkbox');
			$(this).toggleClass('selected');			
			checkbox.attr('checked', $(this).hasClass('selected'));	
			changedReceiveByUser(checkbox);
		});
		
		// 部门展开
		$('.textboxdepart').click(function() {
			openUserList($(this).parent().parent().siblings('.turnondepart'));
		});
		
		// 展开||关闭部门列表
		$('.turnondepart').click(function() {
			openUserList($(this));
		});		
		
		// 发出留言
		$('#senddepartmail').live('click',function() {
			var mailarr = new Array();
			var namearr = new Array();
			if ($('.textboxleft').find('input[type=hidden]').size()==0) {
				alert('请在联系部门选择接收人！');
				return false;
			}
			if (editor1.text() == '') {
				alert('请输入留言内容！');
				return false;
			}
			/* 发送邮件，分批次发送*/
			/*$('.textboxleft').find('input[type=hidden]').each(function() {
				ismailstatus++;
				$.post(
					"index.php?action=person_main&detail=mail",
					{'content':editor1.html(),'mail':$(this).val()},
					function(msg){
						console.log(msg+'\n'+ismailstatus);
						ismailstatus--;
					}
				); 
			});*/
			/* 发送邮件，一次发送所有*/
			var arrlength = 0;
			$('.textboxleft').find('input[type=hidden]').each(function() {
				mailarr[arrlength] = $(this).val();
				arrlength++;
			});
			arrlength = 0;
			$('.textboxsendto div').not(':first').each(function() {
				namearr[arrlength] = $(this).text().substr(0, $(this).text().length-1);
				arrlength++;
			});
			ismailstatus++;
			$.post(
				"index.php?action=department_message&detail=mailall",
				{'content':editor1.html(),'mail':mailarr,'rename':namearr},
				function(msg){
					//console.log(msg+'\n');
					ismailstatus--;					
					mailsuccess();
				}
			); 
			
		});
		
		$('#sendedout').click(function() {
			parent.addMenutab(1005,'已发送留言','index.php?action=department_message&detail=list');
		});
		
		$('.textboxview').ajaxStart(function() {
			if (ismailstatus) {
				$(this).html('<a href="javascript:void(0);">留言</a><div class="textboxview_sendin"><img src="./staticment/images/loading.gif" class="loadimg" />邮件发送中...</div>');
			}
		}).ajaxStop(function() {
			if (ismailstatus == 0) {
				$(this).html('<a id="senddepartmail" href="javascript:void(0);">留言</a>');
			}
		});
	});
	
	/* 好友通讯录脚本 */
	$(function() {
		$('.friend_photo').live('mouseenter',function(){
			$(this).parent().parent().find('.friend_information').show();
		}).live('mouseout', function(){
			$(this).parent().parent().find('.friend_information').hide();
		});
		
		$('.wall_submit_by_class').live('click', function(){
			var idarr = [];
			var num = 0;
			$('.'+$(this).attr('dataclass')).each(function() {
				if ($(this).attr('checked')) {
					idarr[num] = $(this).val();
					num++;	
				}				
			});
			if (num == 0) {
				return false;
			}
			var poststr = $(this).attr('url');
			ajaxstatu = $('#popupContact').attr('class');
		 	$.post(
				poststr,
				{'friendid' : idarr},
				function(msg){
					$('#popupContact').html(msg);
				}
			);
		});
		
		$('.wall_add_friend').live('click', function(){
			var poststr = $('.wall_submit_by_class').attr('url');
			ajaxstatu = $('#popupContact').attr('class');
		 	$.post(
				poststr,
				{'friendid' : $(this).attr('url')},
				function(msg){
					$('#popupContact').html(msg);
				}
			);			
		});
		
		$('.wall_delete_friend').live('click', function() {
			var parenttr = $(this).parent().parent().parent();
			var msg	= '确定删除该好友?';
			var p 	= window.confirm(msg);			
			if (!p) {
				return false;
			}
			var poststr = 'index.php?action=friend_communication&detail=delete';
			var friendid = $(this).parent().attr('a');
			$.post(
				poststr,
				{'friendid' : friendid},
				function(msg){
					if (msg == 1) {
						alert('删除成功！');
						parenttr.remove();
					}
					else {
						alert('删除失败！');
					}
				}
			);
		});
		$('.wall_leave_message').live('click', function() {
			parent.addMenutab(1004,'好友留言','index.php?action=friend_communication&detail=leave_message_list&uid='+$(this).parent().attr('a'));
		});
	});
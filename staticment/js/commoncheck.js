
/* 检测数据不为空 */
function check_notnull_fun(str) {
	return str == ''? false: true;
}

/* 检测是数字
 * str 目标字符串
 * symbol 正负：1正，-1负，0不判定
 * symbol 小数位判定0：不判定，1-4：小数位数
 */
function check_isnum_fun(str, symbol, decimal) {
	if (str == '') {
		return '';
	}
	var format = /^[-]?[0-9]+(\.[0-9]+)?$/;
	var msg = '';
	if (format.exec(str) == null) {
		return "非数字类型！";
	}
	
	switch(decimal) {	
		case 1:
			format = /^[-]?[0-9]+(\.[0-9]{1})?$/;
			msg = "最多一位小数！";
			break;
		case 2:
			format = /^[-]?[0-9]+(\.[0-9]{1,2})?$/;
			msg = "最多二位小数！";
			break;
		case 3:
			format = /^[-]?[0-9]+(\.[0-9]{1,3})?$/;
			msg = "最多三位小数！";
			break;
		case 4:
			format = /^[-]?[0-9]+(\.[0-9]{1,4})?$/;
			msg = "最多四位小数！";			
			break;	
	}
	
	if (msg != '' && format.exec(str) == null) {
		return msg;
	}
	
	switch(symbol) {
		case -1:
			if (str >= 0) {
				return '必须填写负数';
			}
			break;
		case 1:
			if (str <= 0) {
				return '必须填写正数';
			}
			break;
	}
	return '';
}

/* 检测邮箱格式 */
function check_email_fun(str) {
	if (str == '') {
		return true;
	}
	var format = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return format.exec(str)==null?false:true;
}

/* 添加校验错误提醒 
 * obj 目标处理JQuery对象
 * waring 处理类型1：添加错误提醒，0：移除错误提醒
 * str 提示字符串
 */
function toggle_waring_alerts(obj, waring, str, classname) {
	var span = obj.parent().next().children().last('span');
	var spanclassname = classname + "_span";	
	if (waring == 0) {
		span.removeClass(spanclassname);
		var temp = span.attr('class');		
		if (temp == '') {
			str = '';
		}
		else {
			str = span.text().replace(span.data(classname),'');
		}	
		span.removeData(classname);	
	}
	else {
		var tempclass = span.attr('class');	
		var spandata = 	span.data(classname);
		var spantext = span.text();
		if (tempclass == '' || spandata == null || !spantext.match(str)) {
			span.addClass(spanclassname);
			str = spantext + str;
			if (spandata != null) {
				str = str.replace(spandata, '');
			}
		}
		else {
			str = spantext;
		}		
		span.data(classname, str);
	}
	span.text(str);
}

/*
 * 得到目标JQUERY对象焦点选中
 */
function get_focus(obj) {
	setTimeout(function(){
		obj.focus();
		obj.select();
	}, 0);
}

$(function(){
	$('.check_notnull').blur(function() {
		if(check_notnull_fun($(this).val())){
			toggle_waring_alerts($(this), 0, '输入不能为空！', 'check_notnull');
		}	
		else {			
			toggle_waring_alerts($(this), 1, '输入不能为空！', 'check_notnull');
			return false;
		}
	});
	
	$('.check_isnum').blur(function() {
		var waring = check_isnum_fun($(this).val(), 0, 0);
		if (waring) {
			toggle_waring_alerts($(this), 1, waring, 'check_isnum');
			return false;
		}
		else {
			toggle_waring_alerts($(this), 0, waring, 'check_isnum');
		}
	});
	
	$('.check_isnum_dd2').blur(function() {
		var waring = check_isnum_fun($(this).val(), 0, 2);
		if (waring) {
			toggle_waring_alerts($(this), 1, waring, 'check_isnum_dd2');
			return false;
		}
		else {
			toggle_waring_alerts($(this), 0, waring, 'check_isnum_dd2');
		}
	});
	
	$('.check_isnum_ddn').blur(function() {
		var waring = check_isnum_fun($(this).val(), -1, 0);
		if (waring) {
			toggle_waring_alerts($(this), 1, waring, 'check_isnum_ddn');
			return false;
		}
		else {
			toggle_waring_alerts($(this), 0, waring, 'check_isnum_ddn');
		}
	});
	
	$('.check_isnum_dda').blur(function() {
		var waring = check_isnum_fun($(this).val(), 1, 0);
		if (waring) {
			toggle_waring_alerts($(this), 1, waring, 'check_isnum_dda');
			return false;
		}
		else {
			toggle_waring_alerts($(this), 0, waring, 'check_isnum_dda');
		}
	});
	
	$('.check_email').blur(function() {
		if(check_email_fun($(this).val())){
			toggle_waring_alerts($(this), 0, '邮箱格式错误！', 'check_email');
		}	
		else {
			toggle_waring_alerts($(this), 1, '邮箱格式错误！', 'check_email');
			return false;
		}
	});
	
	$('form').submit(function() {
		var iswaring = 0;
		$('.check_notnull').each(function() {
			if(check_notnull_fun($(this).val())){
				toggle_waring_alerts($(this), 0, '输入不能为空！', 'check_notnull');
			}	
			else {
				toggle_waring_alerts($(this), 1, '输入不能为空！', 'check_notnull');
				iswaring = 1;
			}
		});
		$('.check_isnum').each(function() {
			var waring = check_isnum_fun($(this).val(), 0, 0);
			if (waring) {
				toggle_waring_alerts($(this), 1, waring, 'check_isnum');
				iswaring = 1;
			}
			else {
				toggle_waring_alerts($(this), 0, waring, 'check_isnum');
			}
		});
		
		$('.check_isnum_dd2').each(function() {
			var waring = check_isnum_fun($(this).val(), 0, 2);
			if (waring) {
				toggle_waring_alerts($(this), 1, waring, 'check_isnum_dd2');
				iswaring = 1;
			}
			else {
				toggle_waring_alerts($(this), 0, waring, 'check_isnum_dd2');
			}
		});
		
		$('.check_isnum_ddn').each(function() {
			var waring = check_isnum_fun($(this).val(), -1, 0);
			if (waring) {
				toggle_waring_alerts($(this), 1, waring, 'check_isnum_ddn');
				iswaring = 1;
			}
			else {
				toggle_waring_alerts($(this), 0, waring, 'check_isnum_ddn');
			}
		});
		
		$('.check_isnum_dda').each(function() {
			var waring = check_isnum_fun($(this).val(), 1, 0);
			if (waring) {
				toggle_waring_alerts($(this), 1, waring, 'check_isnum_dda');
				iswaring = 1;
			}
			else {
				toggle_waring_alerts($(this), 0, waring, 'check_isnum_dda');
			}
		});
		
		$('.check_email').each(function() {
			if(check_email_fun($(this).val())){
				toggle_waring_alerts($(this), 0, '邮箱格式错误！', 'check_email');
			}	
			else {
				toggle_waring_alerts($(this), 1, '邮箱格式错误！', 'check_email');
				iswaring = 1;
			}
		});
		if (iswaring) {
			return false;
		}
	});
});


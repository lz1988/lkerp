// JavaScript Document
$(function(){
		  
	/*按钮置灰*/
	$('#hdisable').click(function(){
		$(this).attr('disabled',true);
	});
});
/*增加遮罩层与DIV*/
function adiv(parent,fx,fy,fulls,baner,desc){
	var full_x = document.body.clientWidth;
	var full_y = document.body.clientHeight;

	/*是否需要加遮罩*/
	if(fulls == 1){
		var fullcss={
			'left':'0',
			'top':'0',
			'width':full_x+'px',
			'height':full_y+'px',
			'position':'absolute',
			'z-index':'9',
			'background-color':'#fff',
			'filter':'alpha(opacity=60)',
			'opacity':'0.6'
		};
		$(parent).append("<div id='fullscreen'></div>");
		$("#fullscreen").css(fullcss);

	}

	f_l = (full_x-fx)<0?0:(full_x-fx)/2;
	f_t = (full_y-fy)<0?0:(full_y-fy)/2;

	/*是否加标题*/
	if(baner == 1){
		var divbannercss={
			'left':f_l+'px',
			'top':f_t-30+'px',
			'width':fx+'px',
			'height':'30px',
			'position':'absolute',
			'text-align':'center',
			'color':'white',
			'padding-top':'8px',
			'border':'3px outset',
			'border-bottom':'none',
			'z-index':'99',
			'background-image':'url(./staticment/images/bg_nav.gif)',
			'BACKGROUND-REPEAT':'repeat-x'
		}
		$(parent).append("<div id='divbanner'>"+desc+"<span onclick=tclose() style='float:right;margin-right:10px;cursor:pointer;'>&times;</span></div>");
		$("#divbanner").css(divbannercss);


	}

	/* 加载内容 */
	var contcss={
		'left':f_l+'px',
		'top':f_t+'px',
		'width':fx+'px',
		'height':fy+'px',
		'position':'absolute',
		'border':'3px outset',
		'border-top':'none',
		'background-color':'#ffffff',
		'z-index':'99'
	}


	$(parent).append("<div id='cont'></div>");
	$("#cont").css(contcss);
}


/*关闭*/
function tclose(){
	$("#divbanner,#cont,#fullscreen").remove();
}

/*关闭loading*/
function closeloading(){
	var clsid = arguments[0] ? arguments[0] : '';
	$("#isloading"+clsid).remove();
}


/**
 *	显示正在加载通用函数
 *
 *	parent 父窗口ID
 *	fulls 是否需要加遮罩
 *	msg  加载时的附加信息
 */
function isloading(parent,fulls,msg){

	var black = arguments[3] ? arguments[3] : ''; //自定背景色
	var clsid = arguments[4] ? arguments[4] : ''; //自定ID

	var full_x = document.body.clientWidth;
	var full_y = document.body.clientHeight;

	if(fulls == 1){
		var fullcss={
			'left':'0',
			'top':'0',
			'width':full_x+'px',
			'height':full_y+'px',
			'position':'absolute',
			'z-index':'9',
			'background-color':'#fff',
			'filter':'alpha(opacity=60)',
			'opacity':'0.6'
		};
		$(parent).append("<div id='fullscreen'></div>");
		$("#fullscreen").css(fullcss);

	}

	f_l = (full_x-100)<0?0:(full_x-100)/2;
	f_t = (full_y-16)<0?0:(full_y-16)/2;
	f_l = arguments[5]?arguments[5]:f_l;//是否手动定义了左边距;



	var loadingcss={
		'left':f_l+'px',
		'top':f_t+'px',
		'width':100+'px',
		'height':16+'px',
		'position':'absolute',
		'background-color':black,
		'background-image':'url(./staticment/images/loading.gif)',
		'background-repeat':'no-repeat',
		'padding-top':'2px',
		'padding-left':'10px',
		'font-size':'12px',
		'z-index':'999',
		'filter':'alpha(opacity=80)',
		'opacity':'0.8'
	}

	$(parent).append("<div id='isloading"+clsid+"'>&nbsp;&nbsp;&nbsp;"+msg+"</div>");
	$("#isloading"+clsid).css(loadingcss);
}

/* 公用函数AJAX提交 */
function CommomAjaxNew(ajaxType,ajaxUrl,ajaxData,ajaxBfunc,ajaxFunc){
		$.ajax({
		type:ajaxType,
		url:ajaxUrl,
		data:ajaxData,
		beforeSend:ajaxBfunc,
		success:ajaxFunc
		//error:function(){};
	});
}

/* 公用函数AJAX提交-不用检测before */
function CommomAjax(ajaxType,ajaxUrl,ajaxData,ajaxFunc){
		$.ajax({
		type:ajaxType,
		url:ajaxUrl,
		data:ajaxData,
		success:ajaxFunc
	});
	
}


/*取得选中项的ID，公用。
* @parse string msg 确认提示信息.
* 
* return strID 返回数据：1，2，3，4
*/
function commomselect(msg){
	

	if(msg){
		var fg=window.confirm(msg);
		if(!fg){
			return;
		}
	}
	
	var obj=document.getElementsByName('checkmod[]');
	var strID='';
	for(var i=0;i<obj.length;i++){
		obj[i].checked?strID +=obj[i].value+',':'';
	}
	if(!strID.length){
		alert('请您选择数据');
		return;
	}
	strID = strID.substr(0,strID.length-1);
	return strID;
}



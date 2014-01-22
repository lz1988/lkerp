// JavaScript Document

var count_tags	 = 0;
var dbclick_tags = '';

$(function(){
	
	/*初登录加载个人中心信息*/
	addMenutab(1001,'个人中心','index.php?action=person_main&detail=main')
	
	/*显隐左导航按钮*/
	$('.switch_icon').click(function(){
		$('#left_banner').toggle();
		$(this).toggleClass('havehide');$('#right_content').toggleClass('right_content_alt');
	}).css({'height' : document.body.clientHeight-40,'display':'none'});
	

	/*左导航加载...*/
	CommomAjaxNew('POST','index.php?action=left',{'detail':'showleft'},
	isloading('#left_banner',0,'加载中...','','_left','30'),
	function(msg){
		closeloading('_left');
		$("#leftbanner").html(msg);
	});
	
	/*双击标签配置加载*/
	CommomAjax('POST','index.php?action=sys_setting',{'detail':'load_setting'},
		function(msg){
		dbclick_tags = msg;
	});
	
	
	/*非IE的右边DIV宽度调整*/
	if(!window.ActiveXObject){
		$('#divMainTab').css('width','99.5%');
	}
	
	/*关闭按钮*/
	var close_span = $("span[id^=close_tag_]"),close_id,pre_id;
	close_span.css({'color':'#c6a8c6'});

	
	/*关闭标签-1、按钮关闭*/
	close_span.live('click',function(){closeMenutab($(this));});
	

	$('li[id^=li_tag_]').live('dblclick',function(){dotag(this)});

})

/*标签操作*/
function dotag(obj){
	
	/*关闭标签(默认)*/
	if(dbclick_tags == 'close_tag' || dbclick_tags == ''){
		closeMenutab($(obj).children().eq(1).children().eq(0));		
	}
	
	/*新窗口打开*/
	else if(dbclick_tags == 'newopen_tag'){
		var tag_url = document.getElementById("iFrame"+$(obj).attr('id').substring(7)).src;
		window.open(tag_url);
	}
	
	/*本标签内刷新*/
	else if(dbclick_tags == 'return_tag'){
		var tag_obj = document.getElementById("iFrame"+$(obj).attr('id').substring(7));
		tag_obj.src = tag_obj.src;
		//iFrame1001.window.location.reload();
	}

}

/*高度自适--关闭该frame的Loading*/
function iFrameHeight(menu_id) {
	/*var ifm= document.getElementById("iFrame"+obj);
	var subWeb = document.frames ? document.frames["iFrame"+obj].document : ifm.contentDocument;
	if(ifm != null && subWeb != null) {

	ifm.height = subWeb.body.scrollHeight 	< document.body.clientHeight-40 ? document.body.clientHeight-40 : subWeb.body.scrollHeight;
	ifm.width  = subWeb.body.scrollWidth 	< $('#divMainTab').width() ? $('#divMainTab').width() : subWeb.body.scrollWidth;
     ifm.height = document.body.clientHeight-45;
	 ifm.width  = $('#divMainTab').width()-6;*/
	 closeloading(menu_id);//close loading tips
	//}
}

/*滑动门*/
function changeTab(index){
	
	/*获取总标签数*/
	var tag_nums = 0,i,tag_arr = new Array();
	$("li[id^=li_tag_]").each(function(){
		tag_arr[tag_nums] = $(this).attr('id').substring(7);
		tag_nums++;
	});

	
	/*切换处理*/
   	for (i = 0; i<tag_nums; i++){
        
		if(index != tag_arr[i]){
			
	        $("#li_tag_"+tag_arr[i]).attr("class","normal");
	        $("#div_tag_"+tag_arr[i]).css('display','none');			
			
		}
   	}
	$("#li_tag_"+index).attr("class","selected");
	$("#div_tag_"+index).css('display','block');
}

/*添加标签*/
function addMenutab(menu_id){
	
	if($('#li_tag_'+menu_id).attr('id')){ changeTab(menu_id); return;}//如果已在标签中打开，则切换到该标签。
	
	var extra_name= arguments[1] ? arguments[1] : '';
	var extra_url = arguments[2] ? arguments[2] : '';
	var clo_width = 0,title_name='';
	
	count_tags++;
	if(count_tags>9) {alert('标签太多，请关闭一些！');count_tags--;return;}
	$.ajax({
		   type			:'get',		   
		   url			:'index.php?action=left&detail=getTabmenu',
		   data			:{'id':menu_id},dataType: "json",
		   beforeSend	:isloading('#right_content',0,'加载中...','',menu_id),
		   success		:function(msg){
							
							
							/*外部菜单*/
							if(msg == false){
								var msg = {name:extra_name, url:extra_url};
							}
					
							title_name  = msg.name;
							if(title_name.length > 5){title_name = title_name.substr(0,5)+'...';}
							
							/*生成标签与实体内容*/
							$('#divMainTab ul').append('<li id="li_tag_'+menu_id+'" class="normal" onClick="changeTab(\''+menu_id+'\')"><div  class="tabdiv_top_left"><a href="javascript:void(0)" title="'+msg.name+'">'+title_name+'</a></div><div  class="tabdiv_top_right" title="关闭"><span id="close_tag_'+menu_id+'">&times;</span></div></li> ');
							$('#divMainTab').append('<div id="div_tag_'+menu_id+'" style ="display :none; overflow:hidden;" class ="divContent"><iframe src="'+msg.url+'"  scrolling="yes" frameborder="no" id="iFrame'+menu_id+'"  height=100%  width=100%   onLoad="iFrameHeight('+menu_id+')" ></iframe></div>');

							$("#close_tag_"+menu_id).css({'color':'#566984'});
							
							/*IE6高度-40，其它IE版本-40，其它浏览器-45*/
							if(window.ActiveXObject){
								var browser=navigator.appName 
								var b_version=navigator.appVersion 
								var version=b_version.split(";"); 
								var trim_Version=version[1].replace(/[ ]/g,""); 
								if(browser=="Microsoft Internet Explorer" && trim_Version=="MSIE6.0") 
								{ 
									clo_width = 40;
								}else{
									clo_width = 40;
								}
							}else{
								clo_width = 45;
							}

							$("#div_tag_"+menu_id).css('height',document.body.clientHeight-clo_width+'px');							
							changeTab(menu_id);
							
							/*显示切换按钮*/
							if(count_tags == 1)  {$('.switch_icon').css('display','block');}

	}});

}

/*关闭标签*/
function closeMenutab(obj){
	
	/*关闭后默认显示上个标签，如果没有上个标签则显示下个标签*/
	var pre_id = obj.parent().parent().prev().attr('id');
	if(pre_id == null ) pre_id = obj.parent().parent().next().attr('id');
	
	close_id = obj.attr('id');
	close_id = close_id.substring(10);
	$('#li_tag_'+close_id).remove();
	$('#div_tag_'+close_id).remove();
	count_tags--;
	
	/*关闭后再无标签，则切换图标消失*/
	if(count_tags == 0) {$('.switch_icon').css('display','none');return;}
		
	/*显示上一个标签*/
	changeTab(pre_id.substring(7));	
}

/*手动刷新在线人数*/
function refreshOnline(){
	CommomAjax('post','index.php?action=login',{'detail':'refresh_online'},function(msg){		
		window.top.frames['topFrame'].document.getElementById('onlinenum').innerHTML = msg;
	});
}

/*定时刷新*/
setInterval(refreshOnline,1800000);
// JavaScript Document留言板JS
$(function(){
	/*行变换*/
	$("#mytable tr:odd").addClass("oddtrbg");
	$("#mytable tr:even").addClass("eventrbg");
	$("#mytable tr").mouseover(function(){$(this).addClass('hover');})
	$("#mytable tr").mouseout(function(){$(this).removeClass('hover');})
	
	/*删除按钮*/
	$("span[id^=dete_but]").css("display","none");
})


/*显示添加留言按钮*/
function showadd(){
$("#addtable").css('display','block');
}


/*提交留言不能为空判断*/
function checknull(){
	if(addmsg.msg.value == ""){
		alert('内容不能为空!');
		addmsg.msg.focus();
		return false;		
	}else{
	var msg = addmsg.msg.value;
	var pid = addmsg.pid.value;
	CommomAjaxNew('post','index.php?action=product_guestbook',{'detail':'addmod','msg':msg,'pid':pid},
				  function(){$("#checking").css("display","block")},
				  function(data){alert(data);window.location.reload();});
	}
}

/*显示删除*/
function show_dete(obj){
	$("#dete_but"+obj).css("display","inline");
	//alert(obj);
}

/*隐藏删除*/
function hide_dete(obj){
	$("#dete_but"+obj).css("display","none");
}

/*执行删除*/
function dete(obj){
	var p = confirm('确定删除？');
		if(!p) return false;
		CommomAjaxNew('post','index.php?action=product_guestbook',{'detail':'deletemsg','id':obj},isloading('body',0,'删除中...'),function(msg){alert(msg);window.location.reload();});
}

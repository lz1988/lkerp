// JavaScript Document 供应商管理JS

/*供应商AJAX重名检测*/
function checkname(){

	var val = $('input[name=name]').val();
	if(val == ''){$('.tips_供应商名称').html('<font color=#ffcc88>&times;&nbsp;不能为空</font>');return;}
	CommomAjax('POST','index.php?action=supplier&detail=checkname',{'name':val},function(msg){
					var obj = $('.tips_名称');
					if(msg == '1'){obj.html('<font color=#ffcc88>&times;&nbsp;存在重复</font>');}
					else if(msg == '0'){obj.html('<font color=#ffcc88>&radic;&nbsp;可用</font>');}
				});

}

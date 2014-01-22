// JavaScript Document
/*取得键盘ctrl键生成X符号*/
function getkey(obj){
	/*改为按星号键*/
	if(window.event.keyCode==106){
		var keyval = $('input[name='+obj+']').val();
		keyval = keyval.replace('*','');
		$('input[name='+obj+']').attr('value',keyval+'x');
	}

}

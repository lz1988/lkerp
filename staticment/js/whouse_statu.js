// JavaScript Document
/*查看库存状态JS*/
//var haveopen_detail = 0;
function slide(did,is_show,type,houseid,sku){

	if(is_show == 'no'){
	   $('#s'+did).css('background','url(./staticment/images/open_yes.gif)');//改变背景图片
	   $('#d'+did).slideToggle('slow');										//隐藏内容显示
	   $('.c'+did).attr('id','yes');										//更改显示隐藏标志
	   
	   /*查看采购在途*/
	   CommomAjax('post','index.php?action=whouse_statu&detail='+type,{'houseid':houseid,'sku':sku},function(msg){
		   $('#d'+did).html(msg);
	   	 });

	   
	}else if(is_show == 'yes'){
	   $('#s'+did).css('background','url(./staticment/images/open_no.gif)');//改变背景图片
	   $('#d'+did).slideToggle('slow');										//隐藏内容显示
	   $('.c'+did).attr('id','no');										//更改显示隐藏
	}
}
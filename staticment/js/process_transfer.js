// JavaScript Document
/**
 *@title 转仓JS
 *@author hanson
 */


/*确认出货*/
function sureoutse(){
	
	var p = confirm('确定出库？');
	if(!p){return;}
	
	var strID,url,sjurl;	
	url = 'index.php?action=process_transfer&detail=modoutstock';
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}

	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg == '确认成功')window.location.reload();
	});
}



/*取得产品的pid,product_name,unit_box*/
function get_productmsg(){
	var sku,url;
	url = 'index.php?action=process_transfer&detail=get_productmsg';
	sku = $('input[name=sku]').val();
	
	$.getJSON('index.php?action=process_shipment&detail=get_pid',{'sku':sku},function(msg){
																					  
		$('input[name=pid]').attr('value',msg.pid);
		$('input[name=product_name]').attr('value',msg.product_name);
		$('input[name=e_unit_box]').attr('value',msg.unit_box);
	});
}

/*取得箱数与每箱数量,计算总数,用于编辑*/
function count_sum(){
	
	var xnum,unit_box;
	xnum 	= $('input[name=e_box]').val();
	unit_box= $('input[name=e_unit_box]').val();
	
	$('input[name=quantity]').attr('value',xnum*unit_box);
}

/*取得箱数与每箱数量,计算总数,用于新增存在多个时*/
function count_sumlist(row){
	
	var xnum,unit_box;
	xnum 	= $('input[id=e_box_'+row+']').val();
	unit_box= $('input[id=e_unit_box_'+row+']').val();	
	$('input[id=quantity_'+row+']').attr('value',xnum*unit_box);
}



/*减少一行*/
function delrow(obj){
	$('#row_'+obj).parent().parent().remove();
}


/*失去SKU焦点时获取产品SKU等信息*/
function get_pid(row){
	var sku,obj;
	obj = $('input[id=sku_'+row+']');
	sku = obj.val();
	
	$.getJSON('index.php?action=process_shipment&detail=get_pid',{'sku':sku},function(msg){
																					  
		$('input[id=pid_'+row+']').attr('value',msg.pid);
		$('input[id=product_name_'+row+']').attr('value',msg.product_name);
		$('input[id=e_unit_box_'+row+']').attr('value',msg.unit_box);

	});
}

/*打印物料调拨发货明细表*/  
function print_table_detail(detail){	
	var StrID = '';
	StrID = get_orderid();
	if(!StrID) {alert("请选择数据！");return false;}
		htmlform = '<form method="post" target="_blank" action="index.php?action=process_transfer&detail='+detail+'" id=postorder><input type=hidden id=order_id name=order_id value="'+StrID+'"></form>';
	
	$('body').append(htmlform);
	$("#postorder").submit();
	$("#postorder").remove();
}

/*接收选中*/
function receselecttransfer(){
	
	var strID,url,sjurl;	
	url = 'index.php?action=process_transfer&detail=recemod';
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}	

	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg == '接收成功')window.location.reload();
	});
}

/*等待发货*/
function waitleave(){
	var p = confirm('确定此操作吗？');
	if (!p){return;}
	
	var strID,url,sjurl;	
	url = 'index.php?action=process_transfer&detail=waitleave';
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	
	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg == '操作成功'){
			window.location.reload();
		}
	});
	
}

/*确认出库*/
function sureoutseleave(){
	var p = confirm('确定出库？');
	if(!p){return;}

	var strID,url,sjurl;	
	url = 'index.php?action=process_transfer&detail=modoutstock';
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}

	CommomAjax('POST',url,{'strid':strID},function(msg){
		alert(msg);
		if(msg == '确认成功')window.location.reload();
	});

}

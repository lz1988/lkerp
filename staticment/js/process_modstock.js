// JavaScript Document采购操作JS


/*点击采购下单按钮*/
function stockdo(){	

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
	window.location = 'index.php?action=process_modstock&detail=getcont_to_stock&strid='+strID;//跳转去获取选中的纪录的内容。
}


/*审核采购订单与确认采购订单*/
function modrecorde(act){
	

	var confmsg,url,strID='';
	if(act == 'audit'){confmsg = '确定审核?';	}
	else if(act == 'ensure') { confmsg = '采购确认?';}
	
	if(confmsg){
		var fg=window.confirm(confmsg);
		if(!fg){
			return;
		}
	}
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	
	
	url = 'index.php?action=process_modstock&detail=auditfull&act='+act;

		CommomAjaxNew('POST',url,{'strid':strID},isloading('body',0,''),function(msg){closeloading();alert(msg);if(msg=='审核成功'||msg=='确认成功'){window.location.reload();}});
}

/*AJAX关闭备货订单*/
function turnoff(delid){
	var confmsg = '确认关闭？关闭后该订单将不在此显示！';
	if(confmsg){
		var fg=window.confirm(confmsg);
		if(!fg){
			return;
		}
	}	
	var url = 'index.php?action=process_modstock&detail=turnoff';
	CommomAjax('POST',url,{'delid':delid},function(msg){alert(msg); if(msg == '已关闭'){window.location.reload();}})
}


/*AJAX对选中的采购单更改付款状态
function spay(ispay){
	
	var confmsg = '确认执行此操作？';
	if(confmsg){
		var fg=window.confirm(confmsg);
		if(!fg){
			return;
		}
	}
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	var url = 'index.php?action=process_modstock&detail=payorder';
	CommomAjax('POST',url,{'orderid':strID,'ispay':ispay},function(msg){alert(msg); if(msg == '操作成功'){window.location.reload();}})	
}
*/

/*上传水单*/
function upspay(){
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	strID = strID.substr(1,strID.length-2);
	
	if(strID.length>8) {alert('只能对一个订单进行操作');return;}
	window.location='index.php?action=process_modstock&detail=upspay&order_id='+strID;
}

/*增加其他费用*/
function addepay(){
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}
	strID = strID.substr(1,strID.length-2);	
	if(strID.length>8) {alert('只能对一个订单进行操作');return;}
	
	window.location='index.php?action=process_modstock&detail=modaddpay&order_id='+strID;
}

/*取得订单ID，过滤重复的*/
function get_orderid(){
	
	var obj=document.getElementsByName('checkmod[]');
	var strarray = new Array();
	var countstr = 0,strID='';
	

	/*取得需要操作的采购订单号,过滤重复的订单号*/
	for(var i=0;i<obj.length;i++){
		
		if(obj[i].checked){			
			strarray[countstr] = obj[i].value;
			countstr++;
		}	

	}
	if(!strarray.length){
		return;
	}

	strID +="'"+strarray[0]+"',";
	for(var j=1;j<strarray.length;j++){
		if(strarray[j] != strarray[j-1]){strID +="'"+strarray[j]+"',";}
	}	
	/*END*/
	
	strID = strID.substr(0,strID.length-1);
	return strID;
}


/*下拉跳转页面*/
function jumppage(val,statu){
	window.location='index.php?action=process_modstock&detail=list&ispay=&statu='+statu+'&selfval_set='+val;
}

$(function() {
	$("input[name=supplier]").autocomplete('index.php?action=financial_statements&detail=getsupplier',{
									max:50,
									scrollHeight:150,
									dataType: "json",
									parse: function(data) {
										return $.map(data, function(row) {
											return {
												data: row,
												value: row.val,     //返回的formatted数据
												result: row.name   //设置返回Input框给用户看到的数据
											}
										});
									},
                                    formatItem:function(row){return row.val}	//设置显示效果(JSON下也是匹配的效果)
	});
});
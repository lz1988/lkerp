// JavaScript Document
// 订单信息处理JS by hanson

/*审核*/
function audit(statu){
	//alert(statu);
	var strID,url;
	url = 'index.php?action=order_soldmod&detail=audit';
	
	strID = get_orderid();//取得订单号
	if(!strID) {alert('请选择数据！');return;}	
    
    var checktime = $("input[name='checktime']").val();
    if (!checktime && statu == '1'){alert('请选择审核日期！');return;}
    //alert(checktime);return;
	CommomAjax('POST',url,{'strid':strID,'statu':statu,'checktime':checktime},function(msg){
		if(msg == 1){alert('操作成功');window.location.reload();}else{alert(msg);}
	});
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
function jumppage(val,statu,action,extra){
	window.location='index.php?action='+action+'&detail=list&statu='+statu+'&selfval_set='+val+extra;
}
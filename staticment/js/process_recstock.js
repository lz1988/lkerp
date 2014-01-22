// JavaScript Document入库操作JS

/*取得订单ID，过滤重复的,两边带引号*/
function get_orderid(){
	
	var obj=document.getElementsByName('checkmod[]');
	var strarray = new Array();
	var countstr = 0,strID='';
	
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

/*选择仓库*/
function showhouseid(value){
	if(value != ''){
		$('input[name=houseid]').attr('value',value);
		$('#checkhouse').show();
		$('#commomtips').hide();
	}else{
		$('input[name=houseid]').attr('value','');		
		$('#checkhouse').hide();
		$('#commomtips').show();
	}
	
}
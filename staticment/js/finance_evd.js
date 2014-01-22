function delitem(id){
	var p = window.confirm('确定删除?');
	if(!p)return;
	CommomAjax('POST','index.php?action=finance_evd',{'detail':'del_extra_evdplay','id':id},function(msg){alert(msg);if(msg=='删除成功')window.location.reload();});
}

function backitem(id){
	var p = window.confirm('确定回退?');
	if(!p)return;
	CommomAjax('POST','index.php?action=finance_evd',{'detail':'auditfull','sign': '0','strid':id},function(msg){alert(msg);if(msg=='回退成功')window.location='index.php?action=finance_evd&detail=list';});
}

/*批量删除与审核,接收*/
function audit(obj){

	var confmsg,url;
	
	if(obj == 'del'){confmsg = '确认删除？';url = 'index.php?action=finance_evd&detail=deletefull';}
	else if(obj == 'che') {confmsg =  '确认审核？';url = 'index.php?action=finance_evd&detail=auditfull&sign=1';}	
	else if(obj == 'rec') {confmsg =  '确认收帐？';url = 'index.php?action=finance_evd&detail=auditfull&sign=2';}
	else if(obj == '') {return;}
	
	if(confmsg){
		var fg=window.confirm(confmsg);
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
	CommomAjaxNew('POST',
        url,
        {'strid':strID},
        isloading('body',0,''),
        function(msg){
            closeloading();
            alert(msg);
            if(msg=='删除成功'){
                window.location.reload();
            }
            else if(msg == '审核成功'){
                window.location='index.php?action=finance_evd&detail=list&statu=1';
            }
            else if(msg == '过账成功'){
                window.location='index.php?action=finance_evd&detail=list&statu=2';
            }
        });
}

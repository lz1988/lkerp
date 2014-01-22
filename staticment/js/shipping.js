/*批量删除*/
function del(obj){

	var confmsg,url;
	
	if(obj == 'shipping'){confmsg = '确认删除？';url = 'index.php?action=shipping&detail=deletefull';}
	else if(obj == 'shipping_map') {confmsg = '确认删除？';url = 'index.php?action=shipping_map&detail=deletefull';}
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
	CommomAjaxNew(
		'POST',
		url,
		{'strid':strID},
		isloading('body',0,''),
		function(msg){
			closeloading();
			alert(msg);
			if(msg=='删除成功'){
				window.location.reload();
			}
		}
	);
}

$(function(){
	$('#wall_delete_shipping').click(function() {
		del('shipping');
	});
	$('#wall_delete_shipping_map').click(function() {
		del('shipping_map');
	});
	$('input[name=sm_type]').autocomplete('index.php?action=shipping_map&detail=getshipping');
});
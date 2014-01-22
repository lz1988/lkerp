/*点击可编辑*/
function goput(id,name){

	var obj = $("#input_"+name+'_'+id);
	var obs = $("#span_"+name+'_'+id);
	obs.hide();
	if(obs.html() == '--'){
		obj.attr('value','');
	}else{
		obj.attr('value',obs.html());
	}
	obj.show().focus();

}
function backput(id,name,action,detail){
	var val = $("#input_"+name+'_'+id).val();
	var arr = eval("({'detail':'"+detail+"','id':'"+id+"','"+name+"':'"+val+"'})");	
	CommomAjaxNew('post','index.php?action='+action,arr,isloading('body',0,''),function(msg){
					closeloading();
					if(msg==1){
						$("#input_"+name+'_'+id).hide();
						$("#span_"+name+'_'+id).html(val);
						$("#span_"+name+'_'+id).show();
					}else{alert(msg)}
				});
}

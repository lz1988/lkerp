// JavaScript Document
//@author by Jerry
//@create on 2012-11-02

$(function() {	
    
	//自动加载供应商名称
	$("input[name=name]").autocomplete('index.php?action=supplier&detail=getsupplier',{
        max:50,
        scrollHeight:150,
        dataType: "json",
        parse: function(data) {
            return $.map(data, function(row) {
                return {
                    data: row,
                    value: row.id,     //返回的formatted数据
                    result: row.name   //设置返回Input框给用户看到的数据
                }
            });
        },
        formatItem:function(row){return row.name}	//设置显示效果(JSON下也是匹配的效果)
	}).result(function(event, data, formatted){
		//$('input[name=eid]').val(data.id);
	});
	
});

//检查供应商sku
function checksku(){
    var sku = $('input[name=sku]').val();
	var reg = /^(\d{2}-\d{4}-\d{3})+$/;
	var _sku = sku.replace(/(^\s*)|(\s*$)/g, ""); 
	if(_sku == ''){
		$('.tips_sku').html('<font color=#ffcc88>&times;&nbsp;不能为空</font>');return;
	}else if(!reg.test(_sku)){
		$('.tips_sku').html('<font color=#ffcc88>&times;&nbsp;不可用</font>');return;
	}
	
	$.getJSON('index.php?action=process_shipment&detail=get_pid',{'sku':sku},function(msg){
	if (msg.pid>0)
		$('input[name=pid]').attr('value',msg.pid);
		$('.tips_sku').html('<font color="green">&radic;&nbsp;可用</font>');
	});
}

//检查供应商名称
function checkname(){
	var val = $('input[name=name]').val();
	if(val == ''){
		$('.tips_供应商名称').html('<font color=#ffcc88>&times;&nbsp;不能为空</font>');return;
	}else
		$('.tips_供应商名称').html('<font color=green>&radic;&nbsp;可用</font>');
}
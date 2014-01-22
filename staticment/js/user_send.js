// JavaScript Document
$(document).ready(function(){
	//自动加载用户名称
	$("input[name=eng_name]").autocomplete('index.php?action=user_send&detail=getuser',{
        max:50,
        scrollHeight:150,
        dataType: "json",
        parse: function(data) {
            return $.map(data, function(row) {
                return {
                    data: row,
                    value: row.uid,     //返回的formatted数据
                    result: row.eng_name   //设置返回Input框给用户看到的数据
                }
            });
        },
        formatItem:function(row){return row.eng_name}	//设置显示效果(JSON下也是匹配的效果)
	}).result(function(event, data, formatted){
		$('input[name=uid]').val(data.uid);
	});	
	
	//自动加转接人名称
	$("input[name=send_name]").autocomplete('index.php?action=user_send&detail=getuser',{
        max:50,
        scrollHeight:150,
        dataType: "json",
        parse: function(data) {
            return $.map(data, function(row) {
                return {
                    data: row,
                    value: row.uid,     //返回的formatted数据
                    result: row.eng_name   //设置返回Input框给用户看到的数据
                }
            });
        },
        formatItem:function(row){return row.eng_name}	//设置显示效果(JSON下也是匹配的效果)
	}).result(function(event, data, formatted){
		$('input[name=sendid]').val(data.uid);
	});
});
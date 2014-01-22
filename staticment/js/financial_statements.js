// JavaScript Document
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
	
	$('select[name=statements]').change(function() {
		window.location.href = 'index.php?action=financial_statements&detail=list&statements=' + $(this).val();
	});
});
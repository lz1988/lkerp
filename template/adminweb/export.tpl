<script type="text/javascript" src="./staticment/js/jquery.js"></script>
<script type="text/javascript">
$(function(){
	$('#checkall').click(function() {
		$('input[type=checkbox]').attr('checked',$(this).attr('checked'));
	});
});
</script>
<form action="<!--{echo $export[action]}-->" method="<!--{echo $export[method]}-->">
<!--{foreach($hidelist as $val){}-->
	<input type="hidden" name="<!--{echo $val['name']}-->" value="<!--{echo $val['value']}-->"/>
<!--{/foreach}-->    
<fieldset>
	<legend><font color="#566984" size="-1"><!--{echo $export[title]}--></font></legend>
	<div>
		<div>
			<input type="checkbox" id="checkall" />全选
		</div>
		<!--{foreach($datalist as $val){}-->
		<div><input type="checkbox" name="checkattr[]" value="<!--{echo $val['key']}-->" <!--{echo $val['extend']}--> /><!--{echo $val['value']}--></div>  
		<!--{/foreach}-->                    
	</div>
</fieldset>
<input type="submit" value="导出" />
</form>
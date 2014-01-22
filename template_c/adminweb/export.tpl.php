<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 14:49:38, compiled from template/adminweb/export.tpl */ ?>
<script type="text/javascript" src="./staticment/js/jquery.js"></script>
<script type="text/javascript">
$(function(){
	$('#checkall').click(function() {
		$('input[type=checkbox]').attr('checked',$(this).attr('checked'));
	});
});
</script>
<form action="<?php echo $export[action] ?>" method="<?php echo $export[method] ?>">
<?php foreach($hidelist as $val){ ?>
	<input type="hidden" name="<?php echo $val['name'] ?>" value="<?php echo $val['value'] ?>"/>
<?php } ?>    
<fieldset>
	<legend><font color="#566984" size="-1"><?php echo $export[title] ?></font></legend>
	<div>
		<div>
			<input type="checkbox" id="checkall" />全选
		</div>
		<?php foreach($datalist as $val){ ?>
		<div><input type="checkbox" name="checkattr[]" value="<?php echo $val['key'] ?>" <?php echo $val['extend'] ?> /><?php echo $val['value'] ?></div>  
		<?php } ?>                    
	</div>
</fieldset>
<input type="submit" value="导出" />
</form>
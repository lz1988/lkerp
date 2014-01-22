<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-12-27 14:12:18, compiled from template/adminweb/ajax_table.tpl */ ?>
<script type="text/javascript">
$(function(){	
	//找到表格的内容区域中所有的奇数行
	//使用even是为了把通过tbody tr返回的所有tr元素中，在数组里面下标是偶数的元素返回，因为这些元素，实际上才是我们期望的tbody里面的奇数行
	$("tbody tr:even").css("background-color","#FFFFFF");
});
</script>
<table id="sku_sp_ass" width=<?php echo $tablewidth ?>  >
	<tr>
    	<?php foreach ($displayarr as $key=>$show) { ?>
    	<th width="<?php echo $show[width] ?>" title="<?php echo $show[title] ?>"><?php echo $show[showname] ?>
        </th>
        <?php } ?>		
    </tr> 
	
	<?php if ($datalist) { ?>
 	<?php for ($j=0;$j<count($datalist);$j++) { ?>
  	
  	<tr>
		<?php for ($i=0;$i<count($displaykey);$i++) { ?>
	   	<td class="<?php echo $displayarr[$displaykey[$i]][classname] ?> <?php echo $displaykey[$i] ?>" <?php echo $displayarr[$displaykey[$i]][extend] ?> extend="<?php echo $displayarr[$displaykey[$i]][inputextend] ?>"><?php echo $datalist[$j][$displaykey[$i]] ?><input type="hidden" name="<?php echo $prefix.$displaykey[$i].$suffix ?>" value="<?php echo $datalist[$j][$displaykey[$i]] ?>"/></td>
        <?php } ?>

  	</tr>
  	
  	<?php } ?>
	<?php } else { ?>
	
	<tr>
		<?php foreach ($displayarr as $key=>$show) { ?>
    	<td class="<?php echo $show[classname] ?> <?php echo $key ?>" <?php echo $show[extend] ?> extend="<?php echo $show[inputextend] ?>"><input type="hidden" name="<?php echo $prefix.$key.$suffix ?>" value="<?php echo $show['default'] ?>"/></td>
        <?php } ?>		
  	</tr>	
	
  	<?php } ?>  
	
</table>

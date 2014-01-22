<script type="text/javascript">
$(function(){	
	//找到表格的内容区域中所有的奇数行
	//使用even是为了把通过tbody tr返回的所有tr元素中，在数组里面下标是偶数的元素返回，因为这些元素，实际上才是我们期望的tbody里面的奇数行
	$("tbody tr:even").css("background-color","#FFFFFF");
});
</script>
<table id="sku_sp_ass" width=<!--{echo $tablewidth}-->  >
	<tr>
    	<!--{foreach ($displayarr as $key=>$show)}-->
    	<th width="<!--{echo $show[width]}-->" title="<!--{echo $show[title]}-->"><!--{echo $show[showname]}-->
        </th>
        <!--{/foreach}-->		
    </tr> 
	
	<!--{if ($datalist)}-->
 	<!--{for ($j=0;$j<count($datalist);$j++)}-->
  	
  	<tr>
		<!--{for ($i=0;$i<count($displaykey);$i++)}-->
	   	<td class="<!--{echo $displayarr[$displaykey[$i]][classname]}--> <!--{echo $displaykey[$i]}-->" <!--{echo $displayarr[$displaykey[$i]][extend]}--> extend="<!--{echo $displayarr[$displaykey[$i]][inputextend]}-->"><!--{echo $datalist[$j][$displaykey[$i]]}--><input type="hidden" name="<!--{echo $prefix.$displaykey[$i].$suffix}-->" value="<!--{echo $datalist[$j][$displaykey[$i]]}-->"/></td>
        <!--{/for}-->

  	</tr>
  	
  	<!--{/for}-->
	<!--{else}-->
	
	<tr>
		<!--{foreach ($displayarr as $key=>$show)}-->
    	<td class="<!--{echo $show[classname]}--> <!--{echo $key}-->" <!--{echo $show[extend]}--> extend="<!--{echo $show[inputextend]}-->"><input type="hidden" name="<!--{echo $prefix.$key.$suffix}-->" value="<!--{echo $show['default']}-->"/></td>
        <!--{/foreach}-->		
  	</tr>	
	
  	<!--{/if}-->  
	
</table>

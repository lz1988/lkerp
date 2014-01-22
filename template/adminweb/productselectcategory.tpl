<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<!--{if($category_rootlist){}-->
<table cellpadding="3" cellspacing="0">
<tr valign="top"><td><table style="border:#f1f1f1 1px"  cellpadding="3" cellspacing="0" border=1>
<tr><th class="list" >一级类别</th></tr>
<!--{foreach ($category_rootlist as $key=>$r)}-->
<tr>
<td>
<a href="index.php?action=product_new&detail=selectcategory&trh=<!--{echo $trh}-->&step=2&rcat_name=<!--{echo $r['cat_name']}-->&rcat_id=<!--{echo $r['cat_id']}-->"><!--{echo $r['cat_name']}--></a>
</td>
</tr>
<!--{/foreach}-->
</table>
</td>
<!--{/if}-->

<!--{if($step>=2){}-->
<td>
<table style="border:#f1f1f1 1px" border="1" cellpadding="3" cellspacing="0">
<tr><th class="list">二级类别</th></tr>
<!--{foreach ($childlist as $key=>$c)}-->
<tr><td><a href="index.php?action=product_new&detail=selectcategory&trh=<!--{echo $trh}-->&step=3&rcat_name=<!--{echo $rcat_name}-->&rcat_id=<!--{echo $rcat_id}-->&scat_name=<!--{echo $c['cat_name']}-->&scat_id=<!--{echo $c['cat_id']}-->"><!--{echo $c['cat_name']}--></a></td></tr>
<!--{/foreach}-->
</table>
</td>
<!--{/if}-->

<!--{if($step>=2){}-->
<td>
<form id="formp" name="formp" method="post" action="index.php?action=product_new&detail=new&trh=<!--{echo $trh}-->">
<input type="hidden" name="rcat_id" value="<!--{echo $rcat_id}-->"/>
<input type="hidden" name="scat_id" value="<!--{echo $scat_id}-->"/>
<input type="hidden" name="showcat" value="<!--{echo $rcat_name.$scat_name}-->"/>
<table style="border:#f1f1f1 1px; color:#4f6b72" border="1" cellpadding="3" cellspacing="0" align="center">
<tr valign="middle"><th class="list">所选类别</th></tr>
<tr><td>
<!--{echo $rcat_name.$scat_name}-->
</td>
</tr>
<tr><td><input type="submit" name="submit" id="submit" value="提交" />
</td></tr>
</table>
</form></td>
<!--{/if}-->
</tr>
</table>
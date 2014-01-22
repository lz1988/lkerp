<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-13 10:43:04, compiled from template/adminweb/productselectcategory.tpl */ ?>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<?php if($category_rootlist){ ?>
<table cellpadding="3" cellspacing="0">
<tr valign="top"><td><table style="border:#f1f1f1 1px"  cellpadding="3" cellspacing="0" border=1>
<tr><th class="list" >一级类别</th></tr>
<?php foreach ($category_rootlist as $key=>$r) { ?>
<tr>
<td>
<a href="index.php?action=product_new&detail=selectcategory&trh=<?php echo $trh ?>&step=2&rcat_name=<?php echo $r['cat_name'] ?>&rcat_id=<?php echo $r['cat_id'] ?>"><?php echo $r['cat_name'] ?></a>
</td>
</tr>
<?php } ?>
</table>
</td>
<?php } ?>

<?php if($step>=2){ ?>
<td>
<table style="border:#f1f1f1 1px" border="1" cellpadding="3" cellspacing="0">
<tr><th class="list">二级类别</th></tr>
<?php foreach ($childlist as $key=>$c) { ?>
<tr><td><a href="index.php?action=product_new&detail=selectcategory&trh=<?php echo $trh ?>&step=3&rcat_name=<?php echo $rcat_name ?>&rcat_id=<?php echo $rcat_id ?>&scat_name=<?php echo $c['cat_name'] ?>&scat_id=<?php echo $c['cat_id'] ?>"><?php echo $c['cat_name'] ?></a></td></tr>
<?php } ?>
</table>
</td>
<?php } ?>

<?php if($step>=2){ ?>
<td>
<form id="formp" name="formp" method="post" action="index.php?action=product_new&detail=new&trh=<?php echo $trh ?>">
<input type="hidden" name="rcat_id" value="<?php echo $rcat_id ?>"/>
<input type="hidden" name="scat_id" value="<?php echo $scat_id ?>"/>
<input type="hidden" name="showcat" value="<?php echo $rcat_name.$scat_name ?>"/>
<table style="border:#f1f1f1 1px; color:#4f6b72" border="1" cellpadding="3" cellspacing="0" align="center">
<tr valign="middle"><th class="list">所选类别</th></tr>
<tr><td>
<?php echo $rcat_name.$scat_name ?>
</td>
</tr>
<tr><td><input type="submit" name="submit" id="submit" value="提交" />
</td></tr>
</table>
</form></td>
<?php } ?>
</tr>
</table>
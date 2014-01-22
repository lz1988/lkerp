<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-12-30 15:00:54, compiled from template/adminweb/storage_age_byhouse.tpl */ ?>
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<link href="./staticment/css/datalist.css" rel="stylesheet" type="text/css" />
<style type="text/css">
th.list 	{ background: url(./staticment/images/bg_banner2.gif) repeat-x 0 -2px;background-color: #EBEBEB;text-align:center;padding: 0px;height:18px;}
#mytable	{ border-top: 1px solid #eeeeee;}
#mytable td	{ text-align:center;}
</style>
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/datalist.js?version=1"></script>
<body onmousemove="MouseMoveToResize(event);" onmouseup="MouseUpToResize();" >
<?php echo $jslink ?>

<table width="880" border="0" cellpadding="0" cellpadding="0">
<tr><td>

<form name="searchform"  action="index.php?action=storage_age&detail=list" method="post" id="searchform">
<input type="hidden" name="searchmod" value="1">
<span style="margin-right:10px;">SKU：<input type="text" name="sku" value=<?php echo $sku ?> ></span>
<span style="margin-right:10px;">仓库：<?php echo $whouse ?></span>
<span style="margin-right:10px;">查总仓：<?php echo $combine_html ?></span>
<span style="margin-right:10px;">币别：<?php echo $coin_code_html ?></span>
<input  type="submit"  value="搜 索"  id="subre">
</form>
</td></tr>
</table>

<?php echo $bannerstr ?>

<table id="mytable" cellspacing="0" width=<?php echo $tablewidth ?>  >
	<tr>
    	<th class="list" width="" rowspan="2">SKU</th>
    	<th class="list" width="" rowspan="2">仓库</th>        
    	<th class="list" width="" rowspan="2">现存</th>
    	<th class="list" width="" colspan="2">0-30天</th>
    	<th class="list" width="" colspan="2">30-60天</th>
    	<th class="list" width=""  colspan="2">60-90天</th>
    	<th class="list" width=""  colspan="2">90天以上</th>
    </tr>
	<tr>
    	<th class="list" width="" >数量</th>
    	<th class="list" width="" >金额</th>
    	<th class="list" width="" >数量</th>
    	<th class="list" width="" >金额</th>
    	<th class="list" width="" >数量</th>
    	<th class="list" width="" >金额</th>
    	<th class="list" width="" >数量</th>
    	<th class="list" width="" >金额</th>        
    </tr>    
  
  <?php if ($datalist) { ?>
  <?php foreach ($datalist as $key=>$r) { ?>
  <tbody>
  <tr>
	   	<td valign="top" style="text-align:left"><?php echo $r['skushow'] ?></td>
        <td valign="top" style="text-align:left"><?php echo $r['warehouse'] ?></td>        
        <td valign="top"><?php echo $r['stock'] ?></td>
        <td valign="top"><?php echo $r['one'] ?></td>
        <td valign="top"><?php echo $r['one_price'] ?></td>
        <td valign="top"><?php echo $r['two'] ?></td>
        <td valign="top"><?php echo $r['two_price'] ?></td>
        <td valign="top"><?php echo $r['three'] ?></td>
        <td valign="top"><?php echo $r['three_price'] ?></td>
        <td valign="top"><?php echo $r['four'] ?></td>
        <td valign="top"><?php echo $r['four_price'] ?></td>
  </tr>
  </tbody>
  <?php } ?>
  <?php } ?>
</table>
<table width=<?php echo $tablewidth ?> >
  <tr>
  	<td><?php echo $page_html ?></td>
  </tr>
</table>
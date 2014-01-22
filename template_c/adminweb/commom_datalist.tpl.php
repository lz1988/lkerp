<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 16:41:39, compiled from template/adminweb/commom_datalist.tpl */ ?>
<link href="./staticment/css/tablelist.css?v=2" rel="stylesheet" type="text/css" />
<link href="./staticment/css/datalist.css" rel="stylesheet" type="text/css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/datalist.js?version=3"></script>
<body onmousemove="MouseMoveToResize(event);" onmouseup="MouseUpToResize();" >
<?php echo $jslink ?>
<?php if ($search_output) { ?>
<table width="880" border="0" cellpadding="0" cellpadding="0">
<tr><td>
<form name="searchform"  action="<?php echo $jumpurl ?>" method="post" id="searchform">
<?php echo $search_hidden ?>
<?php foreach ($search_output as $key=>$ser) { ?>
<span style="margin-right:10px;"><?php echo $ser['showinput'] ?></span>
<?php } ?>
<input  type="submit"  value="搜 索"  id="subre">
</form>
</td></tr>
</table>
<?php } ?>

<?php echo $bannerstr ?>

<?php if ($tab_menu==1) { ?>
<table  id="tab_menu_list" cellpadding="5" cellspacing="0" border="1">
  <tr>
  <?php echo $tab_menu_output ?>
  </tr>
</table>
<?php } ?>

<table id="mytable" cellspacing="0" width=<?php echo $tablewidth ?>  >
	<tr >
    	<?php foreach ($displayarr as $key=>$show) { ?>
    	<th class="list" width="<?php echo $show[width] ?>" title="<?php echo $show[title] ?>">
        	<div class="clearfix"><?php echo $show[showname] ?><?php echo $show[orderoutput] ?></div>
        </th>
        <?php } ?>
    </tr>
  
  <?php if ($datalist) { ?>
  <?php foreach ($datalist as $key=>$r) { ?>
  <tbody>
  <tr>
		<?php for ($i=0;$i<count($displaykey);$i++) { ?>
	   	<td valign="top"><?php echo $r[$displaykey[$i]] ?>&nbsp;</td>
        <?php } ?>
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
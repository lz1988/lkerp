<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 10:37:37, compiled from template/adminweb/single_model.tpl */ ?>
<div class="contents_body_height">
<table id="mytable" cellspacing="0" width="100%">
  <?php if ($datalist) { ?>
  <?php foreach ($datalist as $key=>$r) { ?>
  <tbody>
  <tr>
		<?php for ($i=0;$i<count($displaykey);$i++) { ?>
	   	<td valign="top"  align="center"><?php echo $r[$displaykey[$i]] ?>&nbsp;</td>
        <?php } ?>

  </tr>
  </tbody>
  <?php } ?>
  <?php } ?>
</table>
</div>
<div class="paging">
<ul>
	<li><span class="wall_flush" url="<?php echo $flush_url ?>" title="刷新"><img src="./staticment/images/refresh.png" /></span></li>
  	<li><?php if ($button_str) { ?><button class="<?php echo $button_str['classname'] ?>" url="<?php echo $button_str['url'] ?>"><?php echo $button_str['value'] ?></button><?php } ?></li>
  	<li>&nbsp;</li>
	<li><?php echo $ajax_page_html ?></li>
</ul
</div>
<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 10:37:31, compiled from template/adminweb/single_model_li.tpl */ ?>
<div class="contents_body_height_li">
	<?php if ($datalist) { ?>
	<ul>
		<?php foreach ($datalist as $key=>$r) { ?>
		<li>
			<?php for ($i=0;$i<count($displaykey);$i++) { ?>
			<span style="<?php echo $r[$displaykey[$i]]['style'] ?>"><?php echo $r[$displaykey[$i]]['text'] ?></span>
			<?php } ?>			
		</li>			
		<?php } ?>
	</ul>
	<?php } else { ?>
	<ul>
		<li>
			暂未发布公告！！！
		</li>			
	</ul>
	<?php } ?>
	<div class="contents_body_height_li_more"><?php echo $more; ?></div>
</div>

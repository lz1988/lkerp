<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 10:37:30, compiled from template/admintag/tag_leftbanner.tpl */ ?>
<!--左导航AJAX+模板调用到前台，方便做权限限制-->
<SCRIPT src="./staticment/js/TreeNode.js" type=text/javascript></SCRIPT>
<SCRIPT src="./staticment/js/Tree.js" type=text/javascript></SCRIPT>
<SCRIPT type=text/javascript>
<?php $row = 1; ?>
<?php foreach ($onemenu_list as $val) { ?>

var tree<?php echo $row ?> = null;var root<?php echo $row ?> = new TreeNode("<?php echo $val['name'] ?>");

<?php foreach ($twomenu_list[$val['id']] as $tkey=>$tval) { ?>
			var fun_m<?php echo $tval['sort_id'] ?> = new TreeNode("<?php echo $tval['name'] ?>", "javascript:void(0);addMenutab(<?php echo $tval[id] ?>);", 'tree_node.gif', null, 'tree_node.gif', null);
			root<?php echo $row ?>.add(fun_m<?php echo $tval['sort_id'] ?>);
<?php } ?>

tree<?php echo $row ?> = new Tree(root<?php echo $row ?>);tree<?php echo $row ?>.show('menuTree');
<?php $row++; ?>
<?php } ?>

</SCRIPT>
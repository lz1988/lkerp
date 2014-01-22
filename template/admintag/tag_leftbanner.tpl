<!--左导航AJAX+模板调用到前台，方便做权限限制-->
<SCRIPT src="./staticment/js/TreeNode.js" type=text/javascript></SCRIPT>
<SCRIPT src="./staticment/js/Tree.js" type=text/javascript></SCRIPT>
<SCRIPT type=text/javascript>
<!--{$row = 1;}-->
<!--{foreach ($onemenu_list as $val)}-->

var tree<!--{echo $row}--> = null;var root<!--{echo $row}--> = new TreeNode("<!--{echo $val['name']}-->");

<!--{foreach ($twomenu_list[$val['id']] as $tkey=>$tval)}-->
			var fun_m<!--{echo $tval['sort_id']}--> = new TreeNode("<!--{echo $tval['name']}-->", "javascript:void(0);addMenutab(<!--{echo $tval[id]}-->);", 'tree_node.gif', null, 'tree_node.gif', null);
			root<!--{echo $row}-->.add(fun_m<!--{echo $tval['sort_id']}-->);
<!--{/foreach}-->

tree<!--{echo $row}--> = new Tree(root<!--{echo $row}-->);tree<!--{echo $row}-->.show('menuTree');
<!--{$row++;}-->
<!--{/foreach}-->

</SCRIPT>
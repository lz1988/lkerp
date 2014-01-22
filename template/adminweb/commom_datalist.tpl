<link href="./staticment/css/tablelist.css?v=2" rel="stylesheet" type="text/css" />
<link href="./staticment/css/datalist.css" rel="stylesheet" type="text/css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/datalist.js?version=3"></script>
<body onmousemove="MouseMoveToResize(event);" onmouseup="MouseUpToResize();" >
<!--{echo $jslink}-->
<!--{if ($search_output)}-->
<table width="880" border="0" cellpadding="0" cellpadding="0">
<tr><td>
<form name="searchform"  action="<!--{echo $jumpurl}-->" method="post" id="searchform">
<!--{echo $search_hidden}-->
<!--{foreach ($search_output as $key=>$ser)}-->
<span style="margin-right:10px;"><!--{echo $ser['showinput']}--></span>
<!--{/foreach}-->
<input  type="submit"  value="搜 索"  id="subre">
</form>
</td></tr>
</table>
<!--{/if}-->

<!--{echo $bannerstr}-->

<!--{if ($tab_menu==1)}-->
<table  id="tab_menu_list" cellpadding="5" cellspacing="0" border="1">
  <tr>
  <!--{echo $tab_menu_output}-->
  </tr>
</table>
<!--{/if}-->

<table id="mytable" cellspacing="0" width=<!--{echo $tablewidth}-->  >
	<tr >
    	<!--{foreach ($displayarr as $key=>$show)}-->
    	<th class="list" width="<!--{echo $show[width]}-->" title="<!--{echo $show[title]}-->">
        	<div class="clearfix"><!--{echo $show[showname]}--><!--{echo $show[orderoutput]}--></div>
        </th>
        <!--{/foreach}-->
    </tr>
  
  <!--{if ($datalist)}-->
  <!--{foreach ($datalist as $key=>$r)}-->
  <tbody>
  <tr>
		<!--{for ($i=0;$i<count($displaykey);$i++)}-->
	   	<td valign="top"><!--{echo $r[$displaykey[$i]]}-->&nbsp;</td>
        <!--{/for}-->
  </tr>
  </tbody>
  <!--{/foreach}-->
  <!--{/if}-->
</table>
<table width=<!--{echo $tablewidth}--> >
  <tr>
  	<td><!--{echo $page_html}--></td>
  </tr>
</table>
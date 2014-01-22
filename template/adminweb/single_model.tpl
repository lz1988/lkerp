<div class="contents_body_height">
<table id="mytable" cellspacing="0" width="100%">
  <!--{if ($datalist)}-->
  <!--{foreach ($datalist as $key=>$r)}-->
  <tbody>
  <tr>
		<!--{for ($i=0;$i<count($displaykey);$i++)}-->
	   	<td valign="top"  align="center"><!--{echo $r[$displaykey[$i]]}-->&nbsp;</td>
        <!--{/for}-->

  </tr>
  </tbody>
  <!--{/foreach}-->
  <!--{/if}-->
</table>
</div>
<div class="paging">
<ul>
	<li><span class="wall_flush" url="<!--{echo $flush_url}-->" title="刷新"><img src="./staticment/images/refresh.png" /></span></li>
  	<li><!--{if ($button_str)}--><button class="<!--{echo $button_str['classname']}-->" url="<!--{echo $button_str['url']}-->"><!--{echo $button_str['value']}--></button><!--{/if}--></li>
  	<li>&nbsp;</li>
	<li><!--{echo $ajax_page_html}--></li>
</ul
</div>
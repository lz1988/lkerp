<a id="popupContactClose" href="javascript:void(0);">X</a>
<table>
  <tr>
  	<td>
  		<!--{for ($i=0;$i<count($searchlist);$i++)}-->
  			<input <!--{foreach ($searchlist[$i] as $key=>$r)}--><!--{echo $key}-->="<!--{echo $r}-->"<!--{/foreach}--> />
  		<!--{/for}-->
  	</td>
  </tr>
</table>
<table id="mytable" cellspacing="0" width="100%">
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
<table>
  <tr>
  	<td><!--{echo $ajax_page_html}--></td>
  </tr>
  <tr>
  	<td>
  		<!--{for ($i=0;$i<count($inputlist);$i++)}-->
  			<input <!--{foreach ($inputlist[$i] as $key=>$r)}--><!--{echo $key}-->="<!--{echo $r}-->"<!--{/foreach}--> />
  		<!--{/for}-->
  	</td>
  </tr>
</table>

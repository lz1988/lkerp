<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/guestbook.js"></script>
<!--留言列表区Start-->
<b>sku</b>：<!--{echo $sku}-->&nbsp;<br><b>product_name</b>：<!--{echo $product_name}-->
<table id="mytable" cellspacing="0" width="100%">
  <tr>
    <th class='list' width="80">floors</th>
    <th class='list' width="120">person</th>
    <th class='list'>message</th>
  </tr>
  
  <!--{if($datalist){}-->
  <!--{foreach($datalist as $key=>$r){}-->
  <tr> 
    <td>第<!--{$floors}-->楼&nbsp;</td><!--{$floors--}-->
    <td><!--{echo $r['person']}-->&nbsp;</td>
    <td onmouseover="show_dete(<!--{$floors}-->)" onmouseout="hide_dete(<!--{$floors}-->)"><font color="#c6a8c6" size="2"><!--{echo $r['ctime']}--> 留言:</font><span id="dete_but<!--{$floors}-->" style="cursor:pointer;" title="删除" onclick="dete(<!--{echo $r['id']}-->)"><img src="./staticment/images/deletebody.gif" border=0 width="14" height="12"></span><br><font  size="3"><!--{echo $r['msg']}--></font>&nbsp;</td>
  </tr>
  <!--{/foreach}-->
  <!--{else}-->
  <tr><td colspan="3" align="center"><font color="red">无留言内容!</font></td></tr>
  <!--{/if}-->
</table>
<table width="100%" border="0">
  <tr>
    <td width="120"><button onClick="showadd()">留言</button>&nbsp;<button onClick="window.history.back(-1)">返回</button></td>
    <td><!--{echo $page_html}--></td>
  </tr>
</table>
<!--留言列表区End-->


<!--增加留言Start区-->
<div id="addtable" style="display:none">
<form action="" method="post" name="addmsg">
<input type="hidden" name="pid" value="<!--{echo $pid}-->">
<table width="400" border="0">
  <tr>
    <td width=""><font color="#777777" size="2"><b>发表你的留言</b>|</font></td>
    <td width="20">&nbsp;</td>
  </tr>
  <tr>
    <td><textarea cols="40" rows="5" name="msg"></textarea></td>
    <td valign="bottom"><font size="2" color="#777777"><div id="checking" style="display:none; width:100px"><img src='./staticment/images/loading.gif'>提交中...</div></font></td>
  </tr>
  <tr>
    <td><input type="button" value="Submit" onClick="checknull()"></td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
</div>
<!--增加留言区End-->
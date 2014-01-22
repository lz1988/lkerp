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
<!--{echo $jslink}-->

<table width="880" border="0" cellpadding="0" cellpadding="0">
<tr><td>

<form name="searchform"  action="index.php?action=sumstorage_age&detail=list" method="post" id="searchform">
<input type="hidden" name="searchmod" value="1">
<input type="hidden" name="houseid" value="1">
<span style="margin-right:10px;">仓库：<!--{echo $whouse}--></span>
<input  type="submit"  value="搜 索"  id="subre">
</form>
</td></tr>
</table>

<!--{echo $bannerstr}-->
<table id="mytable" cellspacing="0" width=<!--{echo $tablewidth}-->  >
	<tr>
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
  
  <!--{if ($data)}-->
  
  <tbody>
  <tr>
        <td valign="top" style="text-align:left"><!--{echo $data['warehouse']}--></td>        
        <td valign="top"><!--{echo $data['stock']}--></td>
        <td valign="top"><!--{echo $data['one']}--></td>
        <td valign="top"><font color=#bdbdbd><!--{echo number_format($data['one_price'],2)}--></font></td>
        <td valign="top"><!--{echo $data['two']}--></td>
        <td valign="top"><font color=#bdbdbd><!--{echo number_format($data['two_price'],2)}--></font></td>
        <td valign="top"><!--{echo $data['three']}--></td>
        <td valign="top"><font color=#bdbdbd><!--{echo number_format($data['three_price'],2)}--></font></td>
        <td valign="top"><!--{echo $data['four']}--></td>
        <td valign="top"><font color=#bdbdbd><!--{echo number_format($data['four_price'],2)}--></font></td>
  </tr>
  </tbody>

  <!--{/if}-->
</table>
<table width=<!--{echo $tablewidth}--> >
  <tr>
  	<td><!--{echo $page_html}--></td>
  </tr>
</table>
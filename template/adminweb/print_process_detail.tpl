<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
body{font-family: Arial,Helvetica,sans-serif; font-size:12px}
#commomform{border:black solid 2px;border-bottom:none;border-right:none;color:black;}
#commomform td{border-right:black solid 1px;border-bottom:black  solid 2px; border-left:none; border-top:none;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印出货单</title>
</head>
<body>
<!--明细表-Start-->
<!--{$pageid = ceil($pageid/40)}-->
<!--{for ($mm=1;$mm<=$pageid;$mm++)}-->
<table width="640" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<td align="center"><h3>深圳诺得潮际电子商务有限公司 出库明细单</h3></td>
  </tr>
  <tr>
    <td align="right">产品总数：<!--{echo $detail_num}-->个 &nbsp; &nbsp; 制表日期：<!--{echo date('Y-m-d',time())}--></td>
  </tr>
</table>
<table width="640"  cellpadding="3" cellspacing="0" id="commomform">
  <tr>
    <td width="35"><b>序号</b></td>
    <td><b>订单号</b></td>
    <td width="100"><b>SKU</b></td>
    <td width="55"><b>应发数</b></td>
    <td width="70"><b>渠道</b></td>
    <td width="130"><b>发货方式</b></td>
    <td><b>物流跟踪号</b></td>
  </tr>  
<!--{$floor = 1;}-->
<!--{for ($mn=($mm-1)*40;$mn<$mm*40;$mn++)}-->
  <tr>
    <td><!--{echo $floor;}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['order_idd']}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['sku'];}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['quantity'];}--></td>
	<td>&nbsp;<!--{echo $datalist[$mn]['sold_way'];}--></td>    
    <td>&nbsp;<!--{echo $datalist[$mn]['e_shipping'];}--></td>
    <td>&nbsp;</td>
  </tr>
<!--{$floor++;}-->
<!--{/for}-->
</table>
<br/>
<br/>
<!--{/for}-->
<!--明细表-End-->

<!--汇总表-Start-->
<!--{$pageid = ceil($pageid_sum/20)}-->
<!--{for ($mm=1;$mm<=$pageid;$mm++)}-->
<table width="640" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<td align="center"><h3>深圳诺得潮际电子商务有限公司 出库汇总单</h3></td>
  </tr>
  <tr>
    <td align="right">产品总数：<!--{echo $all_num}-->个 &nbsp; &nbsp; 制表日期：<!--{echo date('Y-m-d',time())}--></td>
  </tr>
</table>
<table width="640"  cellpadding="3" cellspacing="0" id="commomform">
  <tr>
    <td width="30"><b>序号</b></td>
    <td width="90"><b>SKU</b></td>
    <td width="50"><b>总数</b></td>
    <td width="380"><b>产品名称</b></td>
    <td width="90"><b>金碟</b></td>
  </tr>  
<!--{$floor = 1;}-->
<!--{for ($mn=($mm-1)*20;$mn<$mm*20;$mn++)}-->
  <tr height="37">
    <td>&nbsp;<!--{echo $floor;}--></td>
    <td>&nbsp;<!--{echo $datalist_sum[$mn]['sku']}--></td>
    <td>&nbsp;<!--{echo $datalist_sum[$mn]['num'];}--></td>
    <td style="font-size: 9px">&nbsp;<!--{echo $datalist_sum[$mn]['product_name'];}--></td>
    <td>&nbsp;<!--{echo $datalist_sum[$mn]['sku_code'];}--></td>
  </tr>
<!--{$floor++;}-->
<!--{/for}-->
</table>
<br/>
<!--{/for}-->
<!--汇总表-End-->
</body>
</html>

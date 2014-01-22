
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
body{font-family: Arial,Helvetica,sans-serif; font-size:12px}
#commomform{border:black solid 2px;border-bottom:none;border-right:none;color:black;}
#commomform td{border-right:black solid 1px;border-bottom:black  solid 2px; border-left:none; border-top:none;}
#prc{font-size:10px;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印物料明细出货单</title>
</head>
<body>
<!--明细表-Start-->
<!--{$pageid = ceil($pageid/15)}-->
<!--{for ($mm=1;$mm<=$pageid;$mm++)}-->
<table width="998" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<td align="center"><h3>深圳诺得潮际电子商务有限公司 出库明细单</h3></td>
  </tr>
  <tr>
    <td align="right">产品总数：<!--{echo $detail_num}-->个 &nbsp; &nbsp; 制表日期：<!--{echo date('Y-m-d',time())}--></td>
  </tr>
</table>
<table width="998"  cellpadding="0" cellspacing="0" id="commomform">
  <tr>
    <td width="32"><b>序号</b></td>
    <td width="57"><b>订单号</b></td>
    <td width="57"><b>SKU</b></td>
    <td width="342"><b>产品名称</b></td>
    <td width="122"><b>目的仓库</b></td>
    <td width="60"><b>单箱数量</b></td>
    <td width="32"><b>箱数</b></td>  
    <td width="38"><b>总数</b></td>
    <td width="79"><b>运单编号</b></td>  
    <td width="55"><b>助记码</b></td>
    <td width="55"><b>制单人</b></td>  
  </tr>  
<!--{$floor = 1;}-->
<!--{for ($mn=($mm-1)*15;$mn<$mm*15;$mn++)}-->
  <tr>
    <td height="37"><!--{echo $floor;}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['order_id']}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['sku'];}--></td>
    <td>&nbsp;<span id="prc"><!--{echo $datalist[$mn]['product_name'];}--></span></td>
	<td>&nbsp;<!--{echo $datalist[$mn]['rechouse'];}--></td>    
    <td>&nbsp;<!--{echo $datalist[$mn]['e_unit_box'];}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['e_box']}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['quantity']}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['fid']}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['e_remeber_id']}--></td>
    <td>&nbsp;<!--{echo $datalist[$mn]['cuser']}--></td>
  </tr>
<!--{$floor++;}-->
<!--{/for}-->
</table>
<!--{/for}-->
<!--明细表-End-->
</body>
</html>

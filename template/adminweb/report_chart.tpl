<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="./staticment/js/fusioncharts.js"></script>
<title>统计图表</title>
</head>
<body>
<!--{if ($swType)}-->
<div>
	<button onclick="window.location='index.php?action=<!--{echo $swAction}-->&detail=<!--{echo $swDetail}-->&swType=<!--{echo $swType}-->&Type=<!--{echo $Type}-->&xmlfile=<!--{echo $xmlfile}-->'">切换图形</button>
</div>
<!--{/if}-->

<div id="chartdiv" align="center" >&nbsp;</div>
<script type="text/javascript">
	var chart = new FusionCharts("./staticment/swf/<!--{echo $Type}-->", "ChartId", "1000", "450");
	chart.setDataURL("./data/xml/<!--{echo $xmlfile}-->");
	chart.render("chartdiv");
</script>
</body>
</html>
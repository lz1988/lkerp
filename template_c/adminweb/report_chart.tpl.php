<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-08 12:07:02, compiled from template/adminweb/report_chart.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="./staticment/js/fusioncharts.js"></script>
<title>统计图表</title>
</head>
<body>
<?php if ($swType) { ?>
<div>
	<button onclick="window.location='index.php?action=<?php echo $swAction ?>&detail=<?php echo $swDetail ?>&swType=<?php echo $swType ?>&Type=<?php echo $Type ?>&xmlfile=<?php echo $xmlfile ?>'">切换图形</button>
</div>
<?php } ?>

<div id="chartdiv" align="center" >&nbsp;</div>
<script type="text/javascript">
	var chart = new FusionCharts("./staticment/swf/<?php echo $Type ?>", "ChartId", "1000", "450");
	chart.setDataURL("./data/xml/<?php echo $xmlfile ?>");
	chart.render("chartdiv");
</script>
</body>
</html>
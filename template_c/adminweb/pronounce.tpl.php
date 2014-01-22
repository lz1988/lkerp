<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-07 09:57:57, compiled from template/adminweb/pronounce.tpl */ ?>
<style type="text/css">
*{ margin:0 auto;}
#myform{ height:190px; width:415px; background-image:url(./staticment/images/errorpage.jpg); background-repeat:no-repeat; margin-top:20px; text-align:left; font-size:14px; color:#7b6060;}
#myform td{ text-align:center; height:28px;}
a { color: #333;}
</style>
</head>

<body>
<div style="width:100%;text-align:center">
<table border="0" id='myform' cellpadding="0" cellspacing="0">
  <tr>
    <td width="0">&nbsp;</td>
    <td width="0">&nbsp;</td>
    <td width="0">&nbsp;</td>
    <td width="0">&nbsp;</td>
    <td width="0">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td style="text-align:right">&nbsp;</td>
    <td><?php echo $content ?></td>
    <td></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td style="text-align:right">&nbsp;</td>
    <td><?php echo $back ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
</body>
</html>

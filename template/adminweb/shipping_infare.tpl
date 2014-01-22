<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.tab_selected { background-color:#77A6C6;}
#tab_menu_list {background-color:#828482; text-align:center; font-size:14px; border:none }
#tab_menu_list td a{ color: #033}
#tab_menu_list td {border-left:none;border-right: 1px solid #fff;border-bottom: 1px solid #fff;border-top: 1px solid #fff;}
#searchform input,#searchform select{width:150px;height:25px;border: double #CCCCFF 1px; font-size: 16px; margin-top:5px}
#searchform #subre{width:82px; height:30px; border:none; cursor:pointer;}
</style>
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/datalist.js"></script>
<!--{ echo $message_upload}-->
<form action="index.php?action=shipping_count&detail=infare" name="form-infare-upload" method="post" enctype="multipart/form-data">
<!--{ echo $sourcehtml}-->
<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
<input name="upload_file" type="file" />
<input name="subfile" type="submit" value="上 传" />
</form>
<a href="data/uploadexl/sample/sample_wmsz_fedex_ip.xls">下载样本文件</a>
<form action="index.php?action=shipping_count&detail=infare" name="form-import-upload" method="post">
<!--{ echo $tablelist}-->
</form>
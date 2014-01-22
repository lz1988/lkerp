<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>助记码文件上传</title>
<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />
<style type="text/css">
body{font-family: Arial,Helvetica,sans-serif;}
</style>
<script src="formValidator4.0.1/jquery-1.4.4.js" type="text/javascript"></script>
<script src="./staticment/js/process_upload.js?v=2" type="text/javascript" charset="UTF-8"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
</head>

<body>
<? include './staticment/dynamic/process_upload.php'?>
<input type="hidden" name="type" id="type" value="<!--{echo $type}-->"/>
<input type="hidden" name="engname" id="engname" value="<?= $_SESSION['eng_name']?>" />
</body>
</html>
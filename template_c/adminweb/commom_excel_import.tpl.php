<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-21 17:31:12, compiled from template/adminweb/commom_excel_import.tpl */ ?>
<!--通用的导入表格模板-->
<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/datalist.js"></script>
<script>
$(function(){
	$('input[name=submit]').live('click',function(){ isloading('body',0,'保存中...');});
});


function goosubmit()
{
	  $('#submit_once').attr('disabled',true);
}
</script>
<body onmousemove="MouseMoveToResize(event);" onmouseup="MouseUpToResize();" >

<?php if ($exl_error_msg) { ?>
<div id=commomtips style="background:url(./staticment/images/error_tips.gif) 5px 12px no-repeat #FFFFE5;border:1px solid #ffc674;font-size:12px;font-weight:normal;width:<?php echo  $exl_error_width ?>px;line-height:22px;padding:8px 10px 10px 25px;color:#ff2a00;margin:10px 0;">
<?php echo $exl_error_msg ?>
</div>
<?php } ?>

<form action="<?php echo $submit_action ?>" name="form-import-upload" method="post" enctype="multipart/form-data">
<?php  echo $message_upload ?>
<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
<input name="upload_file" type="file" />
<input name="subfile" type="submit" value="上 传" />
<?php if ($temlate_exlurl) { ?>
<a href="<?php echo $temlate_exlurl ?>"><font color="#577dc6" size="1">下载用于上传的EXCEL模板</font></a>
<?php } ?>
</form>

<form action="<?php echo $submit_action ?>" name="form-import-upload" method="post" id="ture_submit" onSubmit="return goosubmit()">
<?php echo $tablelist ?>
</form>
<button onClick="history.go(-1)">返回</button>

<?php $datalist = '1'; ?><!--随便设置一个$datalsit值,用于footer模板调用可拖动表格JS-->
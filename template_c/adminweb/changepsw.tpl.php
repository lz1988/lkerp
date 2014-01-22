<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-11-28 09:58:18, compiled from template/adminweb/changepsw.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script type="text/javascript">
$(function(){
	
	/*检查原始密码*/
	$('input[name=oldpsw]').blur(function(){
		var val = $(this).val();
		if(val =='') {$("#checkold").css('display','block').html("<img src='./staticment/images/onError.gif' border='0'>不能为空");return false;}
		CommomAjaxNew('post','index.php?action=login',{'detail':'checkold','val':val},
					  function(){$("#checkold").css('display','block')},
					  function(msg){$("#checkold").html(msg);});
	});
	
	/*提示不能为空*/
	$('input[name=newpsw]').blur(function(){
		var val = $(this).val();
		if(val =='') {$("#checknew").html("<img src='./staticment/images/onError.gif' border='0'>不能为空");return false;}else{$("#checknew").html("<img src='./staticment/images/onCorrect.gif' border='0'>正确");}
	});
	
	/*提交*/
	$("#submit").click(function(){
		commit();
	})


})


/*每步检查提示*/
function checkpsw(){
		var newpsw = $('input[name=newpsw]').val();
		var confirmpsw = $('input[name=confirmpsw]').val();
		
		if(newpsw != confirmpsw){
			$("#confirmpsw").html("<img src='./staticment/images/onError.gif' border='0'>两次密码不一致");
		}else{
			$("#confirmpsw").html("<img src='./staticment/images/onCorrect.gif' border='0'>确认一致");
		}
	}

/*提交*/
function commit(){
	var oldpsw = $('input[name=oldpsw]').val();
	var newpsw = $('input[name=newpsw]').val();
	var conpsw = $('input[name=confirmpsw]').val();
	var ismit = $('input[name=ismit]').val();
        var frame = $('input[name=frame]').val();

	if( oldpsw == ""){
		alert('原始密码不能为空!');
		$('input[name=oldpsw]').focus();
		return false;		
	}
	
	else if(newpsw == ""){
		alert('新密码不能为空!');
		$('input[name=newpsw]').focus();
		return false;		
	}
	
	else if( conpsw == ""){
		alert('确认密码不能为空!');
		$('input[name=confirmpsw]').focus();
		return false;		
	}
	else{
        /*
		CommomAjaxNew('post','index.php?action=login',{'detail':'changemod','ismit':ismit,'newpsw':newpsw,'conpsw':conpsw},
					  function(){var i=1;},
					  function(msg){alert(msg);}
		);
        */
        var url ="index.php?action=login&detail=changemod&oldpsw="+oldpsw+"&newpsw="+newpsw+"&conpsw="+conpsw+"&frame="+frame+"";
        location.href=url;
        
	}
	
}
</script>
<title></title>
<style type="text/css">
*{ margin:0 auto;}
#myform{ height:368px; width:671px; background-image:url(./staticment/images/changepswbg.gif); margin-top:20px; text-align:left; font-size:14px; color:#7b6060;}
#myform td{ text-align:left; height:30px;}
#myform td input{ width:200px; height:30px; background-color: #CCFF99; border: double #CCCCFF 1px; font-size:24px;}
</style>
</head>

<body>
<div style="width:100%;text-align:center">
<table border="0" id='myform' cellpadding="0" cellspacing="0">
  <tr>
    <td width="54">&nbsp;</td>
    <td width="111">&nbsp;</td>
    <td width="210">&nbsp;</td>
    <td width="240">&nbsp;</td>
    <td width="22">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td style="text-align:right">原密码：</td>
    <td><input type="password" name="oldpsw" /</td>
    <td><div id="checkold" style=" color:#c8a6c8; font-size:13px; display:none"><img src="./staticment/images/loading.gif" border="0">检查中...</div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td style="text-align:right">新密码：</td>
    <td><input type="password" name="newpsw" /></td>
    <td><div id="checknew" style=" color:#c8a6c8; font-size:13px;"></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td style="text-align:right">确认密码：</td>
    <td><input type="password" name="confirmpsw"   onKeyUp="checkpsw()"/></td>
    <td><div id="confirmpsw" style=" color:#c8a6c8; font-size:13px;"></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><img src="./staticment/images/sure.gif" border="0" style="cursor:pointer" id="submit"><input type="hidden" name="frame" value="<?php echo $frame; ?>" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
</body>
</html>

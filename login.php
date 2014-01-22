<?php
session_start();

/*检测是否同一个域,同一个域再次打开登录页面时，自动跳转到主页*/
$tag_erp_projectURL = $_SERVER["REQUEST_URI"];
$tag_erp_projectURL =  substr($tag_erp_projectURL,1,strlen($tag_erp_projectURL));
$tag_erp_projectURL =  substr($tag_erp_projectURL,0,strpos($tag_erp_projectURL,'/'));

if($_SESSION['logined'] == $tag_erp_projectURL) {header("refresh: 0; url=index.php");}

unset($_SESSION['loginsess']);
$loginsess_c = 'loginsess_'.mt_rand();
$_SESSION['loginsess'] = $loginsess_c;

unset($_SESSION['vscode']);
$ramcode = rand(1000,9999);
$_SESSION['vscode'] = $ramcode;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登陆</title>
<LINK href="./staticment/css/public.css" type='text/css' rel='stylesheet'>
<LINK href="./staticment/css/login.css" type='text/css' rel='stylesheet'>
<script src="./staticment/js/jquery.js" type="text/javascript"></script>
<script>
    var checkall=function(){
        var uname = $("#username");
        var pwd   = $("#password");
        var scode = $("#scode");
        
        if(uname.val() == ''){
            alert("用户名不能为空");
            uname.focus();
            return false;
        } 
        
        if(pwd.val() == ''){
            alert("密码不能为空");
            pwd.focus(); 
            return false;
        }
      
        if(scode.val() == ''){
            alert("验证码不可为空");
            scode.focus();
            return false;
        }
}
</script>
</HEAD>
<BODY>
<DIV id=div1>
<form id="form1" name="form1" method="post"  onsubmit="return checkall();" autocomplete="off" action="index.php?action=login">
<input type='hidden' name='detail' value='login'>
<input type="hidden" name="loginsess_p" value="<?=$loginsess_c?>">
  <TABLE id=login height="100%" cellSpacing=0 cellPadding=0 width=800
align=center>
    <TBODY>
      <TR id=main>
        <TD>
          <TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%">
            <TBODY>
              <TR>
                <TD colSpan=4>&nbsp;</TD>
              </TR>
              <TR height=30>
                <TD width=380>&nbsp;</TD>
                <TD>&nbsp;</TD>
                <TD>&nbsp;</TD>
                <TD>&nbsp;</TD>
              </TR>
              <TR height=40>
                <TD rowSpan=4>&nbsp;</TD>
                <TD>用户名：</TD>
                <TD>
                  <INPUT class="textbox"  id="username" name="username">
                </TD>
                <TD width=120>&nbsp;</TD>
              </TR>
              <TR height=40>
                <TD>密　码：</TD>
                <TD>
                  <INPUT class="textbox" id="password" type="password"
            name="password">
                </TD>
                <TD width=120>&nbsp;</TD>
              </TR>
              <TR height=40>
                <TD>验证码：</TD>
                <TD vAlign=center colSpan=2>
                  <INPUT id="scode" size="4" name="scode"><?=$ramcode?><!--
                  &nbsp; <IMG src="css/default.gif" border=0> <A id=LinkButton1
            href="#">不清楚，再来一张</A>--></TD>
              </TR>
              <TR height=40>
                <TD></TD>
                <TD align=right>
                  <INPUT id="btnLogin" type="submit" value=" 登 录 " name="btnLogin">
                </TD>
                <TD width=120>&nbsp;</TD>
              </TR>
              <TR height=110>
                <TD colSpan=4>&nbsp;</TD>
              </TR>
            </TBODY>
          </TABLE>
        </TD>
      </TR>
      <TR id=root height=104>
        <TD>&nbsp;</TD>
      </TR>
    </TBODY>
  </TABLE>
</form>
</DIV>
<DIV id=div2 style="DISPLAY: none"></DIV>
</BODY>
</HTML>

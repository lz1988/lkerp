<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 10:37:30, compiled from template/adminweb/maintop.tpl */ ?>
<HEAD>
    <STYLE type=text/css>
        * { FONT-SIZE: 12px; COLOR: white} #logo { COLOR: white} #logo A { COLOR:
        white} FORM { MARGIN: 0px} .top_li_banner { height:30px; line-height:30px;
        float:right;padding-left:23px;padding-right:5px;list-style: none; } .top_li_banner
        a { text-decoration:none;}
		#wall_msg_count {padding: 2px; border: 1px solid #000; background-color:#fffee3; color: #000; position: absolute; display: none; z-index: 1000;}
		.wall_message_remind_content, .wall_message_remind, .wall_message_remind_content span{color: #000;}
		.wall_message_remind_content_first{margin-left: 5px;}
		.wall_message_remind_content{cursor: pointer;}
    </STYLE>
    <SCRIPT src="./staticment/js/Clock.js" type=text/javascript>
    </SCRIPT>
    <script src="./staticment/js/jquery.js">
    </script>
    <script src="./staticment/js/msg.js">
    </script>
    <!--[if IE 6]>
        <SCRIPT src="./staticment/js/DD_belatedPNG_0.0.8a.js" language="javascript">
        </SCRIPT>
        <script type="text/javascript">
            DD_belatedPNG.fix('.png,.png:hover,img,li');
        </script>
    <![endif]-->
    <META content="MSHTML 6.00.2900.5848" name=GENERATOR>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY style="BACKGROUND-IMAGE: url(./staticment/images/bg.gif); MARGIN: 0px; BACKGROUND-REPEAT: repeat-x">
    <form id="form1">
        <DIV id=logo style="BACKGROUND-IMAGE: url(./data/images/logo/logo.png); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION:50px -10px;">
            <DIV style="PADDING-RIGHT: 50px; BACKGROUND-POSITION: right 50%; DISPLAY: block; PADDING-LEFT: 0px; BACKGROUND-IMAGE: url(./staticment/images/bg_banner_menu.gif); PADDING-BOTTOM: 0px; PADDING-TOP: 3px; BACKGROUND-REPEAT: no-repeat; HEIGHT: 30px; TEXT-ALIGN: right">
                <A class="wall_show_msg_count" href="javascript:void(0);self.parent.mainFrame.addMenutab(1002,'消息列表','index.php?action=msg&detail=list')"><IMG src="./staticment/images/top/mail.png" align=absMiddle border=0 id="newmsgimg"></A>
                你有新消息
                <A class="wall_show_msg_count" id=HyperLink1 href="javascript:void(0);self.parent.mainFrame.addMenutab(1002,'消息列表','index.php?action=msg&detail=list')">0</A>
                条
                <IMG src="./staticment/images/top/menu_seprator.gif" align=absMiddle>
                <A href='javascript:void(0);self.parent.mainFrame.addMenutab(1000,"修改密码","index.php?action=login&detail=change_psw&frame=show")'>修改密码</A>
                <IMG src="./staticment/images/top/menu_seprator.gif" align=absMiddle>
                <A id=HyperLink3 href="index.php?action=login&amp;detail=logout" target=_top>退出系统</A>
				<div id="wall_msg_count"></div>
            </DIV>
            <DIV style="DISPLAY: block; HEIGHT: 30px">
            </DIV>
            <DIV style="BACKGROUND-IMAGE: url(./staticment/images/bg_nav.gif); BACKGROUND-REPEAT: repeat-x; HEIGHT: 40px">
                <TABLE cellSpacing=0 cellPadding=0 width="100%">
                    <TBODY>
                        <TR>
                            <TD>
                                <DIV>
                                    <IMG src="./staticment/images/top/nav_pre.gif" align=absMiddle>
                                    欢迎
                                    <SPAN id=lblDep>
                                        ：
                                    </SPAN>
                                    <a href="javascript:void(0);self.parent.mainFrame.addMenutab(1001,'个人中心','index.php?action=person_main&detail=main')"
                                    style="text-decoration:none" title="打开个人中心">
                                        <?php echo $chi_name ?>
                                        (
                                        <?php echo $eng_name ?>
                                        )
                                    </a>
                                    <span>
                                        &nbsp; 在线人数：
                                        <a href="javascript:;self.parent.mainFrame.refreshOnline()" title="点击刷新">
                                            <b id="onlinenum">
                                                <?php echo $countonline; ?>
                                            </b>
                                        </a>
                                    </span>
                                </DIV>
                            </TD>
                            <TD align=right width="70%">
                                <div style=" padding-right:50px;height:30px; line-height:30px;">
                                    <ul style="list-style: none; padding:0; margin:0">
                                        <li style="background:url(./staticment/images/top/menu_seprator.gif) no-repeat left center;"
                                        class="top_li_banner">
                                            <SPAN id=clock>
                                            </SPAN>
                                        </li>
                                        <li style="background:url(./staticment/images/top/nav_up.png) no-repeat left center;"
                                        class="top_li_banner">
                                            <a href='javascript:void(0);self.parent.mainFrame.addMenutab(1101,"更新日志","index.php?action=showtxtlog&detail=list")'>
                                                更新日志
                                            </a>
                                        </li>
                                        <li style="background:url(./staticment/images/top/nav_help.png) no-repeat left center;"
                                        class="top_li_banner">
                                            <a href="index.php?action=help&detail=index" target="_blank">
                                                帮助
                                            </a>
                                        </li>
                                        <li style="background:url(./staticment/images/top/nav_forward.png) no-repeat left center;"
                                        class="top_li_banner">
                                            <a href="javascript:history.go(1);">
                                                前进
                                            </a>
                                        </li>
                                        <li style="background:url(./staticment/images/top/nav_back.png) no-repeat left center;"
                                        class="top_li_banner">
                                            <a href="javascript:history.go(-1);">
                                                后退
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </TD>
                        </TR>
                    </TBODY>
                </TABLE>
            </DIV>
        </DIV>
        <SCRIPT type=text/javascript>
            var clock = new Clock();
            clock.display(document.getElementById("clock"));
        </SCRIPT>
    </form>
</BODY>

</HTML>
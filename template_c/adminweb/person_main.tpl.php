<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 10:37:30, compiled from template/adminweb/person_main.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="./staticment/css/base.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="editor/themes/default/default.css" />
<link rel="stylesheet" href="editor/plugins/code/prettify.css" />
</head>
<script type="text/javascript" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script type="text/javascript" src="./staticment/js/new.js"></script>
<script type="text/javascript" src="./staticment/js/person_main.js"></script>
<body>
<!--公司通告-->
<div class="company_announcements">
	<h2><span id="wall_main_flush" style="float:right; padding: 5px; cursor: pointer;" url="index.php?action=announcement&detail=person_center_list"><img src="./staticment/images/wall_flush.png"/></span><span style="float: left;">公司通告</span></h2>
	<div class="wall_content wall_announcement"> <img src="./staticment/images/loading.gif" class="loadimg" /> </div>
</div>
<!--中间3列-->
<div class="company_contents"> 
	<!--中间3列 左边 -->
	<div class="company_contents_left wall_job_alerts" >
		<h2>个人工作提醒</h2>
		<div class="wall_content wall_job_ale">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="20%" align="center">订单数</td>
					<td width="20%" align="center">日期</td>
					<td width="20%" align="center">当前状态</td>
					<td width="20%" align="center">产品数量</td>
					<td width="20%" align="center">目标状态</td>
				</tr>
				<img src="./staticment/images/loading.gif" class="loadimg" />
			</table>
		</div>
	</div>
	<!--中间3列 中间 -->
	<div class="company_contents_body wall_sku_customize">
		<h2><span class="wall_include" url="index.php?action=center_productcustom&detail=select_by_sku"><img src="./staticment/images/searchNode.png"/></span>产品信息定制</h2>
		<div class="wall_content wall_sku_cus">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="34%" align="center">SKU</td>
					<td width="23%" align="center">日期</td>
					<td width="23%" align="center">操作</td>
					<td width="20%" align="center">数量</td>
				</tr>
				<img src="./staticment/images/loading.gif" class="loadimg" />
			</table>
		</div>
	</div>
	<!--中间3列 右边 -->
	<div class="company_contents_body wall_friend_list">
		<h2><span class="wall_include" url="index.php?action=friend_communication&detail=add_friend"><img src="./staticment/images/searchNode.png"/></span>通讯录</h2>
		<div class="wall_content">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="10%" align="center">照片</td>
					<td width="70%" align="center">姓名全称</td>
					<td width="20%" align="center">操作</td>
				</tr>
				<img src="./staticment/images/loading.gif" class="loadimg" />
			</table>
		</div>
	</div>
</div>
<div class="company_view">
	<h2>部门留言</h2>
	<div class="textbox">
		<form method="post" action="index.php?action=person_main&detail=mail">
		<div class="textboxleft">
			<table>
				<tr>
					<td width="100px" style="text-align: right">收件人：</td>
					<td>
					<div class="textboxsendto">	<div></div>						
					</div>
					</td>
				</tr>
				<tr>
					<td width="100px" style="text-align: right">内容：</td>
					<td>
					<div class="textboxleftbox">
						<textarea name="content" style="width:700px;height:200px;visibility:hidden;"></textarea>
					</div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
					<div class="contents_body_height_li_more"><a id="sendedout" href="javascript:void(0);">已发送>></a></div>
					<div class="textboxview"><a id="senddepartmail" href="javascript:void(0);">留言</a><!--<input type="submit" value="留言" />--></div>					
					</td>
				</tr>
			</table>
			
		</div>
		</form>
		<div class="textboxright">
			<h2>联系部门</h2>
			<div class="textboxrightcontent">
				<ul>							
					<?php foreach ($departlist as $val) { ?>
					<li>
						<div class="turnondepart" style="display: inline;">
							<img src="./staticment/images/plus_noLine.gif" width="18" height="18"/>
						</div>
						<div style="display: inline;"><nobr>							
							<input type="checkbox" name="checkbox" class="checkboxdepart" />
							<span class="textboxcheckbox textboxdepart" ><?php echo $val["groupname"] ?></span></nobr>
						</div>
						<ul>
							<?php foreach ($val['user'] as $user) { ?>
							<li title="<?php echo $user["email"] ?>">
								<div class="textboxcheckbox textboxuser"><nobr>
									<input type="checkbox" name="<?php echo $user["eng_name"].'_'.$user["chi_name"] ?>" class="checkbox" value='<?php echo $user["email"] ?>' />
									<?php echo $user["eng_name"].'_'.$user["chi_name"] ?></nobr>
								</div>
							</li>
							<?php } ?>									
						</ul>
					</li>
					<?php } ?>		
				</ul>			
			</div>
		</div>
	</div>
</div>
<div id="popupContact" class="wall_content wall_pop"> <img src="./staticment/images/loading.gif" class="loadimg" /> </div>
<div id="backgroundPopup"></div>
<iframe name='sendmail_frame' id="sendmail_frame" style='display:none'></iframe>
<script charset="utf-8" type="text/javascript" src="./staticment/js/yanue.pop.js"></script>
<?php
   $days = $_SESSION['days'];
   if(!empty($days)){ 
?>
<script type="text/javascript" >
	  //记得加载jquery
	  //作者：yanue
	  //使用参数：1.标题，2.链接地址，3.内容简介
	  window.onload=function(){
		var pop=new Pop("密码修改提醒","index.php?action=login&detail=change_psw","您的帐号于<?php echo $days;?>天后被锁定,请及时修改密码!");
     }
</script>
<?php }?>
<div id="pop" style="display:none;">
	<style type="text/css">
	*{margin:0;padding:0;}
	#pop{background:#fff;width:260px;border:1px solid #e0e0e0;font-size:12px;text-align:left;right:10px;bottom:0px;position: fixed;}
	#popHead{line-height:32px;background:#f6f0f3;border-bottom:1px solid #e0e0e0;position:relative;font-size:12px;padding:0 0 0 10px;}
	#popHead h2{font-size:14px;color:#666;line-height:32px;height:32px;}
	#popHead #popClose{position:absolute;right:10px;top:1px;}
	#popHead a#popClose:hover{color:#f00;cursor:pointer;}
	#popContent{padding:5px 10px;}
	#popTitle a{line-height:24px;font-size:14px;font-family:'微软雅黑';color:#333;font-weight:bold;text-decoration:none;}
	#popTitle a:hover{color:#f60;}
	#popIntro{text-indent:24px;line-height:160%;margin:5px 0;color:#666;}
	#popMore{text-align:right;border-top:1px dotted #ccc;line-height:24px;margin:8px 0 0 0;}
	#popMore a{color:#f60;}
	#popMore a:hover{color:#f00;}
	</style>
	<div id="popHead">
	<a id="popClose" title="关闭" style="cursor:pointer;">关闭</a>
	<h2>温馨提示</h2>
	</div>
	<div id="popContent">
	<dl>
		<dt id="popTitle"><a href="#" target="_blank">这里是参数</a></dt>
		<dd id="popIntro">这里是内容简介</dd>
	</dl>
	<p id="popMore"><a href="#" target="_blank">修改密码 »</a></p>
	</div>
</div>
</body>
</html>

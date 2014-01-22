<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2013-09-02 11:18:44, compiled from template/adminweb/friend_message.tpl */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
body { text-align: center; font-family:"宋体-PUA";margin:0; padding:0;-webkit-text-size-adjust:none; background: #FFF; font-size:12px; color:#000; font-weight:100}
div,form,img,ul,ol,li,dl,dt,dd,p {margin:0; padding:0; border:0;} 
img{ overflow:hidden;vertical-align:bottom;}
li{list-style:none;}
h1,h2,h3,h4,h5,h6 {margin:0; padding:0;}
table,td,tr,th{font-size:12px;}
.clear{ clear: both; font-size:1px; width:1px; height:0; visibility: hidden; }
a{ color:#333; text-decoration:none; blr:expression(this.onFocus=this.blur()); outline: none}
a:hover{ color:#333; text-decoration: underline;}
.message_main{ border:#CCC solid 1px;padding:0; margin:0px; color:#545454; width:100%; display:inline-table;}
.message_main .name{font-size:12px; font-weight:bold; color:#26709A; width:115px; float:left; padding:15px 0 0 15px; line-height:30px; text-align:left; height: 200px;}
.message_main .content{float:left; padding:10px; text-align:left; line-height:25px; display:block; width:80%}
.message_main .content span{color:#A8A8A8}
.message_main .toolbar{float:right; position:relative; text-align:right; height:20px; line-height:20px;}
.message_main .toolbar a{ color:#26709A}
.message_main .message_content{line-height:16px; color:#545454}
.message_main .message_content_in{ line-height:16px; color:#545454;width:500px; text-align:left;  display:inline-table; clear:both; float:left; margin-top:15px; margin-left:20px;}
.message_main .message_content_in li{margin-bottom:2px;background:#F3F3F3;padding:5px 10px 5px 10px; line-height:20px;}
.message_main .message_content_in b{ font-size:12px; font-weight:bold; color:#26709A; padding-right:5px;}
.message_main .message_content_in .time{padding-top:10px; color:#A8A8A8}
.message_main .no_message{margin: 50px 0;text-align: center;}
.message_main .reply_message_input{border-style: solid;border-width: 1px; cursor: text; font-family: Simsun; font-size: 12px; height: 21px; line-height: 23px; margin: 0; overflow: hidden; padding-left: 4px; width: 98%;}
.message_main .reply_message_area{border-style: solid; border-width: 1px; font-size: 12px; height: 38px; margin: 1px; overflow: auto; width: 98%; box-shadow: 0 1px 2px #EAE2C6 inset;}
.textinput, .textarea{background-color: #FFFFFF; border-color: #EAE2C6; color: #A8A8A8;}
</style>
<link rel="stylesheet" href="editor/themes/default/default.css" />
<link rel="stylesheet" href="editor/plugins/code/prettify.css" />
<?php echo $jslink ?>
<script type="text/javascript" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script type="text/javascript" src="./staticment/js/friend_message.js"></script>

</head>

<body>
	<div style="padding: 10px 10px 20px 10px; text-align:left;">
		<h4>发表您的留言</h4>
		<hr  />
		<div class="textboxleftbox" style="padding-bottom: 10px;">
			<textarea name="content" style="width:900px;height:150px;visibility:hidden;"></textarea>
		</div>
		<p><input class="want_to_leave_message" type="button" value="发表"/></p>
	</div>
	<div style="padding: 10px; text-align:left;">	
		<div style="float:right;"><?php echo $page_html ?></div>
		<div><h4>留言（<?php echo $count ?>）</h4></div>
	</div>
<?php if ($datalist) { ?>
	<?php foreach ($datalist as $val) { ?>
	<div class="message_main">
		<div class="name">
			<p>
				<img src="<?php echo empty($val['picurl'])?'./data/users/face_default.png':$val['picurl']; ?>" width="100" height="100" />
			</p>
			<p>
				<?php echo $val['fm_friendname'] ?>
			</p>
		</div>
		<div class="content wall_leave_message_content" a="<?php echo $val['id'] ?>">
			<?php if ($reply) { ?>
			<div class="toolbar">
				<a class="reply_message_a" href="javascript:void(0);">回复	</a>
			</div>
			<?php } ?>
			<p>
				<span><?php echo $val['fm_ctime'] ?> 留言</span>
			</p>			
			<div class="message_content">
				<?php echo $val['fm_content'] ?>
			</div>
			<?php if ($val['child']) { ?>
			<div class="message_content_in">
				<ul>
					<?php foreach ($val['child'] as $child) { ?>
					<li>
						<p>
							<b><?php echo $child['fm_friendname'] ?></b>
							<?php echo $child['fm_content'] ?>
						</p>
						<p class="time">
							<span><?php echo $child['fm_ctime'] ?></span>
						</p>
					</li>
					<?php } ?>	
					<?php if ($reply || $val['reply']) { ?>				
					<li>
						<div class="reply_message_div" style="clear:both">
							<input class="reply_message_input textinput" type="text" value="我也说一句..."/>
<!--							<textarea class="reply_message_area textarea" cols="50">dddddd</textarea>
-->						</div>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>	
<?php } else { ?>
	<div class="message_main">
		<p class="no_message">
			<span class="c_tx3">还没有人发表留言</span>
			<a class="reply_message_for_friend" title="来坐第一个沙发" href="javascript:void(0);" style="color: #26709A;">来坐第一个沙发</a>
		</p>
	</div>
<?php } ?>		
	<div style="padding: 10px; text-align:left;">	
		<div style="float:right;"><?php echo $page_html ?></div>
		<div><a class="reply_message_for_friend" title="来坐第一个沙发" href="javascript:void(0);" style="font-weight: bold; color: #915833;">我要留言</a></div>
	</div>
</body>
</html>

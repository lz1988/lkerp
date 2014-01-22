<style>
body { text-align: center; font-family:Arial, Helvetica, sans-serif;margin:0; padding:0;-webkit-text-size-adjust:none; background: #FFF; font-size:11px; color:#000; font-weight:100}
div,form,img,ul,ol,li,dl,dt,dd,p {margin:0; padding:0; border:0;} 
img{ overflow:hidden;vertical-align:bottom;}
li{list-style:none;}
h1,h2,h3,h4,h5,h6 {margin:0; padding:0;}
.show_announcement_type {	
	border-radius: 5px 5px 0 0;
    height: 35px;
    line-height: 35px;
    position: relative;
	background-color: #FAF7E8;
}
.show_announcement_type h3{
	font-size:14px; 
	font-weight:bold;    
    margin: 0 3px 0 12px;
	padding-left: 8px;
}
.show_announcement_return {	
	line-height:35px;	
    position: absolute;
    right: 10px;
    top: 0;
}

.show_announcement {
	overflow: hidden;
    padding: 20px;
	background: none repeat scroll 0 0 #FCFCFC;
}
.show_announcement_title {
    padding-bottom: 10px;
}
.show_announcement_title strong {
	font-family: 'Helvetica Neue','Helvetica Neue',Helvetica,'Hiragino Sans GB','Microsoft Yahei',tahoma;
    font-size: 20px;
    margin-right: 20px;
}
.show_announcement_info {
	display: inline-block;
    min-width: 100px;
}
.show_announcement_info span {
	font-size: 12px;
	color: #A8A8A8;
}
.show_announcement_content {
	font-size: 14px;
	border: 0 none;
    margin: 0;
    padding: 20px;
}
.show_announcement_page {
	font-size: 14px;
	height: 16px;
	line-height: 16px;
}
.show_announcement_return a,.show_announcement_page a {
	color: #915833;
	font-size: 13px;
	text-decoration: none;
}
</style>
<div style="width: 960px;">
	<div class="show_announcement_type">
		<div style="float:left"><h3><!--{echo $announcement['name'];}--></h3></div>
		<div class="show_announcement_return"><!--{echo $return;}--></div>
	</div>
	<div class="show_announcement">
		<div class="show_announcement_title">
			<strong><span><!--{echo $announcement['title'];}--></span></strong>
			<span class="show_announcement_info"><span><!--{echo $announcement['cuser'];}--></span><span>::</span><span><!--{echo $announcement['cdate'];}--></span></span>
		</div>
		
		<div class="show_announcement_content">
			<!--{echo $announcement['content'];}-->
		</div>
		<div class="show_announcement_page">
			<div style="float: right;margin-top: 6px;">
				<span><!--{echo $prev;}--></span> | 
				<span><!--{echo $next;}--></span>				
			</div>
		</div>        
	</div>
<div>
<script src="./staticment/js/jQuery.js"></script>
<script type="text/javascript">
	var del = <!--{echo $del;}-->; 
	if (del) {
		self.parent.parent.topFrame.readed_msg('wall_announcement_remind', 1);		
	}
</script>
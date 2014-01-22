<link rel="stylesheet" href="editor/themes/default/default.css" />
<link rel="stylesheet" href="editor/plugins/code/prettify.css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script charset="utf-8" src="./staticment/js/commoncheck.js"></script>


<script type="text/javascript">
/*禁止复制资料*/
//document.oncontextmenu=new Function('event.returnValue=false;');
//document.onselectstart=new Function('event.returnValue=false;');
KindEditor.ready(function(K) {
			var editor1 = K.create('textarea[name="content"]', {
				cssPath : 'editor/plugins/code/prettify.css',
				uploadJson : 'editor/php/upload_json.php',
				fileManagerJson : 'editor/php/file_manager_json.php',
				allowFileManager : true,
				afterCreate : function() {
					var self = this;
					K.ctrl(document, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
					K.ctrl(self.edit.doc, 13, function() {
						self.sync();
						K('form[name=example]')[0].submit();
					});
				}
			});
			prettyPrint();
});
</script>
<script charset="utf-8" src="editor/plugins/code/prettify.js"></script>
<style type="text/css">
/* tooltip */
#tooltip{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:2px;
	display:none;
	color:#fff;
}
.tips{color:#c6a8c6; font-size:14px;}
#commomform{border:#FCF solid 1px;border-bottom:none;border-right:none;color: #00004F;font-size:14px;}
#commomform td{border-right:#FCF solid 1px;border-bottom:#FCF  solid 1px; border-left:none; border-top:none;}
#commomform input,#commomform select{width:200px;height:25px;border: double #CCCCFF 1px;}
#commomform #subinput{width:82px; height:30px; border:none; cursor:pointer;}
#commomform span{color: red; font-size: 12px;}
#searchform input,#searchform select{width:140px; height:22px;border: double #CCCCFF 1px; font-size: 12px; margin-top:5px}
#searchform #subre{width:75px; height:21px; border:none; cursor:pointer;}
#searchform { font-size:12px;}
</style>
<DIV>    
<form action="<!--{echo $conform[action]}-->" method="<!--{echo $conform[method]}-->">
  	<table style="border:#FCF solid 1px;" border="1" cellpadding="3" cellspacing="0"  width="<!--{echo$conform[width]}-->px" id="commomform">
		<tr> 
			<td>公告标题：</td>
			<td><input type="text" name="title" class="check_notnull"/></td>
			<td width="120"><span></span>&nbsp;</td>
		</tr> 
		<tr>
			<td>公告类型：</td>
			<td>
				<select name="atid">
					<!--{echo $atid;}-->
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>公告内容：</td>
			<td>
			<textarea name="content" style="width:650px;height:400px;visibility:hidden;"></textarea>
			</td>
			<td><span></span>&nbsp;</td>
		</tr>
		<tr>
			<td>公告置顶：</td>
			<td><input type="checkbox" name="istop" style="width: 50px;"/></td>
			<td>&nbsp;</td>
		</tr>     
  	</table>
	<input type="submit" value="提交" name="sumbit"/> 
</form>

</DIV>





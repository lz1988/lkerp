<link href="./staticment/css/base.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./staticment/js/jquery.js"></script>
<script type="text/javascript" src="./staticment/js/department_message.js"></script>
<!--{echo $js}-->
<div class="mailout_next_previous">	
	<div style="float:right;">
	<!--{echo $prev.' '.$next}-->
	</div>
	<div style="float:left;" >
	<input type="button" value="返回" onclick='window.location.href="index.php?action=department_message&detail=list"'/>
	</div>
</div>
<div class="mail_outbox">
	<div class="outbox_font">
		<span>
			发件人：
		</span>
		<span class="tcolor">
			<b class="name">
				<!--{echo $sendname}-->
			</b>			
		</span>
	</div>
	<div class="outbox_font">
		<span>
			时&nbsp;&nbsp;&nbsp;&nbsp;间：
		</span>
		<span class="tcolor">
			<!--{echo $sendtime}-->
		</span>
	</div>
	<div class="outbox_font">
		<div style="float:left;">
		<span>
			收件人：
		</span>
		</div>
		<div style="float:left; width:80%">			
			<!--{echo $receives}-->
		</div>
	</div>
	<div class="attbg">
		<span>
			发送状态：
			<b>
				<!--{echo $mailstatus}-->
			</b>
			&nbsp;
			<a href="javascript:void(0)" class="clickstu">
				[查看详情]
			</a>			
		</span>
		<div class="mailout_status" style="display: none;">
		<table width="90%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF"
		class="bd mailout_statList">
			<tr>
				<th width="45%">
					收件人
				</th>
				<th width="37%">
					投递状态
				</th>
				<th width="18%" class="end">
					时间
				</th>
			</tr>
			<!--{foreach ($maillist as $val)}-->
			<tr>
				<td>
					<!--{echo $val["mail"]}-->
				</td>
				<td>
					<!--{echo $val["status"]}-->
				</td>
				<td class="end">
					<!--{echo $val["time"]}-->
				</td>
			</tr>
			<!--{/foreach}-->			
		</table>
		</div>
	</div>
</div>
<div class="mailout_body">
	<!--{echo $content}-->
</div>
<div class="mailout_next_previous">
	<div style="float:right;">
	<!--{echo $prev.' '.$next}-->
	</div>
	<div style="float:left;" >
	<input type="button" value="返回" onclick='window.location.href="index.php?action=department_message&detail=list"'/>
	</div>	
</div>

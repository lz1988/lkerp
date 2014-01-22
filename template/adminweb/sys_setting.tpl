<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN">
<HTML><HEAD><TITLE>LK Erp System</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<style  type="text/css">
body{font-family: Arial,Helvetica,sans-serif; font-size:12px}
#commomform{border:#FCF solid 1px;border-bottom:none;border-right:none;color: #00004F;font-size:14px;}
.tips{color:#c6a8c6; font-size:14px;}
#commomform td{border-right:#FCF solid 1px;border-bottom:#FCF  solid 1px; border-left:none; border-top:none;}
#commomform input,#commomform select{width:200px;height:25px;border: double #CCCCFF 1px;}
#commomform #radio{border:none; width:20px;}
#commomform #subinput{width:82px; height:30px; border:none; cursor:pointer;}
.body_title{color:#566984;width:190px; margin-bottom:6px; height:28px; line-height:25px; padding-left:10px; font-size: 12px; background:url('./staticment/images/set_li_bg.gif') no-repeat; font-weight:bold; padding-top:2px;}
.body_bottom{margin-bottom:30px; padding-left:10px; padding-top:6px;}
.ulstyle{list-style: none; padding:0; margin:0}
.ulstyle li label{padding-right:5px; vertical-align:middle !important; line-height:25px;}
input{padding:0; margin:0px;}
.input_mid {vertical-align:middle  !important;}
.tips { color:#999999; font-size:12px}
</style>
<script type="text/javascript" src="./staticment/js/jquery-1.4.1.min.js"></script>
<script type="text/javascript">
$(function(){
	$('.body_title').click(function(){$(this).next().slideToggle();}).css('cursor','pointer');
});

function callback(msg)
{
	var myreload = 0;
	if (msg.msg != '') {
		alert(msg.msg);
	}
	if (msg.isreload == 1 && myreload == 1) {
		$('iframe').html("");
		location.reload();
	}
	if (msg.filename != '') {
		$("img").attr('src', msg.filename);
		$('input[name=newfilename]').val(msg.filename);
		myreload = 1;
	}
}
</script>
<body>

<form id='commitform' action="<!--{echo $commitaction}-->" method="POST" target="hidden_frame">
<input type="hidden" name="newfilename" />

<div class="body_title">
偏好设置
</div>
<div class="body_bottom">
<ul  class="ulstyle">
	<li>双击标签：<label><input type="radio" name="dbclick_tag" value="close_tag" class="input_mid" <!--{if (empty($set_arr['dbclick_tag']) || $set_arr['dbclick_tag'] == 'close_tag')}-->checked<!--{/if}--> >关闭标签</label>
    <label><input type="radio" name="dbclick_tag" value="newopen_tag"  class="input_mid" <!--{if ($set_arr['dbclick_tag'] == 'newopen_tag')}-->checked<!--{/if}--> >在新窗口打开</label>
    <label><input type="radio" name="dbclick_tag" value="return_tag" class="input_mid" <!--{if ($set_arr['dbclick_tag'] == 'return_tag')}-->checked<!--{/if}--> >返回菜单首页</label></li>
</ul>
<span class="tips input_mid" style="margin-left:60PX">此设置需要重新登录生效</span>
</div>

<!--//非偏好设置与Logo设置需要权限-->
<!--{if ($is_conallset)}-->
<p class="tips"><hr></p>
<p class="tips">以下设置是系统设置，需要有"详细信息设置"的权限方可操作。</p>

<div class="body_title">
仓库的相关设置
</div>
<div style=" display:none" class="body_bottom">

	<!--默认仓库设置-->
    <div>
        <ul class='ulstyle'>
            <li><!--{echo $whouse}--></li>
        </ul>
    </div>
	
    <!--设置2-->    
    <div style="margin-top:20px; line-height:20px;">
    	<div>销售下单需要检测库存的发货仓库：<!--{echo $check_stock_whouse_html}--></div>
        <div  style="margin-left:195px"><span class="tips">勾选中的仓库，在销售下单时会进行库存检测</span></div>
    </div>
    
    
    <!--设置3-->
    <div style="margin-top:20px; line-height:20px;">
    	<div>销售下单的匹配订单直接出库中禁止匹配的的仓库：<!--{echo $check_fbden_whouse_html}--></div>
        <div  style="margin-left:195px"><span class="tips">勾选中的仓库，禁止通过导入第三方单号匹配订单产品出库</span></div>
    </div>
</div>



<div class="body_title">
其它设置
</div>
<div style=" display:none" class="body_bottom">

	<!--产品列表成品币种-->
    <div style="margin-top:20px; line-height:20px;">
    	<div>产品列表成本价所属币别：<!--{echo $product_list_cost_html}--></div>
        <div  style="margin-left:145px"><span class="tips">勾选了币别，需要在汇率调整添加该币别的汇率，否则数据可能出错</span></div>
    </div>

    <!--销售下单时估算运费-->
    <div style="margin-top:20px; line-height:20px;">
    	<div>销售下单时估算运费： &nbsp; &nbsp; &nbsp; &nbsp;<!--{echo $selling_countship_html}--></div>
    </div>
    
    <!--系统本位币-->
    <div style="margin-top:20px; line-height:20px;">
    	<div>系统本位币： &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp;  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<!--{echo $system_defaultcoin_html}--></div>
    </div>
    
</div>
<!--{/if}-->
</form>

<!--{if ($is_conallset)}-->
<div class="body_title">
Logo设置
</div>
<div style="height:180px; display:none" class="body_bottom">
	<table  cellpadding="3" cellspacing="0" id="commomform">
		<tr>
			<td>当前Logo：</td>
			<td><img src="<!--{echo $showurl}-->" style="width:<!--{echo $imgwidth}-->px; height:<!--{echo $imgheight}-->px;"/></td>
            <TD width="250">&nbsp;</TD>
		</tr>
		<tr>
			<td>上传Logo：</td>
			<td>
			<form id="uploadform" action="<!--{echo $uploadaction}-->" method="POST" enctype="multipart/form-data" target="hidden_frame">
				<input name="filename" type="file" /><input type="submit" style="width: 50px; margin-left: 20px;" value="上传" />
			</form>
			</td>
            <TD>&nbsp;<span style="color:#c6a8c6">建议上传300*60像素的图片(jpg、gif)</span></TD>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
			</td>
            <TD>&nbsp;</TD>
		</tr>
	</table>
</div>
<!--{/if}-->

<iframe name='hidden_frame' id="hidden_frame" style='display:none'></iframe>
<DIV style="float:left">
<BUTTON onClick="$('#commitform').submit()">提交</BUTTON>
</DIV>
</body>
</HTML>
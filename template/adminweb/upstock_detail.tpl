<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>备货信息</title>
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/stockproduct.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script type="text/javascript">
$(function(){
	var obj_eq = $('input[name=edit_quantity]');
	var obj_sq = $('#edit_quantity');
	
	/*未审核状态并且有审核权限的可修改数量*/
	obj_sq.click(function(){
		$(this).hide();
		obj_eq.show().focus();		
	})
	
	obj_eq.blur(function(){
		CommomAjax('POST','index.php?action=process_upstock&detail=upquantity',{'id':"<!--{echo $backdata['id']}-->",'quantity':$(this).val()},function(msg){
			if(msg == 1){
				obj_eq.hide();
				obj_sq.html(obj_eq.val()).show();
			}
		});
	})
})
</script>

<style type="text/css">
html, body, div, span, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, address, big, cite, code, del, em, font, img, ins, small, strong, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend {margin: 0;padding: 0;}
body 	{	font: 12px/150% Arial,Verdana,"宋体b8b\4f53";color: #333; text-align:center;margin:0;}
#content{	margin:0 auto; width:1000px; text-align:left;}
#info	{	border: 1px solid #DADADA;		padding: 0 5px 10px;		background: #EDEDED;		overflow: visible;	}	
.mt 	{	padding: 0 8px;	height: 30px;	line-height: 30px;	font-size: 14px;}
.mc 	{	padding: 5px 8px;	background: white;	overflow: visible;}
dl 		{	padding:10px 5px;border-top: 1px solid #EDEDED;}
dt 		{	margin-bottom:4px; font-weight:bold;}
dd 		{	display:block;}
ul 		{	list-style:none;}
#footer { 	margin-top:10px; padding-bottom:10px; margin:0 auto;}


#process 			{ margin:0 auto;padding: 10px 0 70px;}
.section4 			{ width: 706px;}
#process .node 		{ width: 13px;}
#process .proce		{ width: 150px;border: solid white;border-width: 0 5px;}
#process .node,#process .proce {float: left;position: relative;height: 13px;background-image: url(./staticment/images/bg_state.jpg);background-repeat: no-repeat;}
.node,.ready 		{ background-position: -150px 0px;}
.proce.wait 		{ background-position: 0 -40px;}
.proce.half 		{ background-position: 0 -20px;}
.proce.ready 		{ background-position: 0 0;}
.node.wait 			{ background-position: -150px -40px;}
#process .proce ul 	{ width: 150px;}
#process .node ul 	{ width: 318px;margin-left: -152px;}
#process ul 		{ position: absolute;margin-top: -38px;text-align: center;}
#process .tx1,#process .tx1 a 		{ height: 36px;margin-bottom: 16px; }
#process .wait .tx2,#process .wait .tx2 a {color: #999;}
a 					{ text-decoration:none; color:#000;}
#process .tx3		{color: #999;line-height: 15px;}

</style>
</head>
<body>
<div id="content">

<div id="process" class="section4">
		   <div class="node <!--{echo $backdata['css_1_n']}-->"><ul><li class="tx1">&nbsp;</li><li class="tx2">提交备货</li><li  class="tx3"><!--{echo $backdata['mod_time1']}--> <br> <!--{echo $backdata['mod_user1']}--></li></ul></div>
           <div class="proce <!--{echo $backdata['css_1_p']}-->"><ul><li class="tx1">&nbsp;</li></ul></div>
           <div class="node <!--{echo $backdata['css_2_n']}-->"><ul><li class="tx1">&nbsp;</li><li class="tx2">审核通过</li><li  class="tx3"><!--{echo $backdata['mod_time2']}--> <br> <!--{echo $backdata['mod_user2']}--></li></ul></div>
           <div class="proce <!--{echo $backdata['css_2_p']}-->"><ul><li class="tx1">&nbsp;</li></ul></div>
           <div class="node <!--{echo $backdata['css_3_n']}-->"><ul><li class="tx1">&nbsp;</li><li class="tx2">采购接收</li><li  class="tx3"><!--{echo $backdata['mod_time3']}--> <br> <!--{echo $backdata['mod_user3']}--></li></ul></div>
           <div class="proce <!--{echo $backdata['css_3_p']}-->"><ul><li class="tx1">&nbsp;</li></ul></div>
           <div class="node <!--{echo $backdata['css_4_n']}-->"><ul><li class="tx1">&nbsp;</li><li class="tx2"><a href="javascript:void(0)" class="showprocess" id="<!--{echo $backdata['id']}-->"  target="detail">采购录单</a></li><li  class="tx3"><!--{echo $backdata['mod_time4']}--> <br> <!--{echo $backdata['mod_user4']}--></li></ul></div>
           <div class="proce <!--{echo $backdata['css_4_p']}-->"><ul><li class="tx1">&nbsp;</li></ul></div>
           <div class="node <!--{echo $backdata['css_5_n']}-->"><ul><li class="tx1">&nbsp;</li><li class="tx2">完成入库</li><li  class="tx3"><!--{echo $backdata['mod_time5']}--> <br> <!--{echo $backdata['mod_user5']}--></li></ul></div>
</div>
                 

<div id="info">
	<div class="mt">
    	<div style="float:left"><strong>备货信息(<!--{echo $backdata['order_id']}-->)</strong></div>
    	<div style="float:right; font-size:12px; line-height:16px; margin-top:3px">
            <!--未审核状态并且拥有权限-->
            <!--{if ($backdata['adut_statu']) }-->
            <input type="hidden" name="checkmod[]" value="<!--{echo $backdata['id']}-->" checked />
            <button onclick=audit("che")>审核通过</button>
            <button onclick=audit("unche")>审核不过</button>
            <!--{/if}-->
        	 &nbsp;&nbsp;
        	<button <!--{echo $backdata['prev_mod']}--> >上一条</button>
            <button <!--{echo $backdata['next_mod']}--> >下一条</button>
        </div>
    </div>
	<div class="mc">
    
    	<!--产品信息-->
        <dl style="border-top:none;">
            <dt>主要信息</dt>
            <dd >
                <ul>
	              <li>备货名称：<!--{echo $backdata['e_stockname']}--></li>
                  <li>产品SKU：<!--{echo $backdata['sku']}--></li>
                  <li>产品名称：<!--{echo $backdata['product_name']}--></li>
                  <li>备货数量：<!--{echo $backdata['e_quantity']}--> pcs</li>
                  <li>审核数量：<span <!--{if ($backdata['adut_statu']) }--> id="edit_quantity"  title="点击修改审核数量"<!--{/if}--> ><!--{echo $backdata['quantity']}--></span><input  name="edit_quantity" style="width:30px; display:none" value="<!--{echo $backdata['quantity']}-->"/> pcs</li>
                  <li>M&nbsp; O&nbsp; Q &nbsp; ：<!--{echo $backdata['e_upc_or_ean']}--> pcs</li>
                  <li>质量问题退货率：<!--{echo $backdata['e_lastbackrate']}--> (过去一个月)</li>
                </ul>    
            </dd>
        </dl>
        
        <!--销售信息-->
        <dl>
            <dt>销售信息</dt>
            <dd >
                <ul>
                  <li>单个总成本：<!--{echo $backdata['price']}--> (USD)</li>
                  <li>销 售 价 格 ：<!--{echo $backdata['e_rprice']}--> (USD)</li>
                  <li>单 个 利 润 ：<!--{echo $backdata['e_aprice']}--> (USD)</li>
                  <li>预计总利润：<!--{echo $backdata['e_sprice']}--> (USD)</li>
                  <li>利 &nbsp; 润 &nbsp; 率 &nbsp;：<!--{echo $backdata['profit']}--> </li>                  
                  <li>销 售 历 史 ：<!--{echo $backdata['e_lastself']}--> pcs(过去两周)</li>
                  <li>销 售 预 估 ：<!--{echo $backdata['e_futureself']}--> pcs(预估未来两周)</li>
                </ul>    
            </dd>
        </dl>
        
        <!--库存与订单信息-->
         <dl>
            <dt>库存与订单信息</dt>
            <dd >
                <ul>
                  <li>可 发 库 存 ：<!--{echo $backdata['e_inware']}--> pcs</li>
                  <li>F B A 在 途 ：<!--{echo $backdata['e_fbainware']}--> pcs</li>
                  <li>已下采购单：<!--{echo $backdata['e_inwareching']}--> pcs</li>
                  <li>已下备货单：<!--{echo $backdata['e_instocking']}--> pcs</li>
                </ul>    
            </dd>
        </dl>
        
        <!--备注-->
        <dl>
            <dt>备注</dt>
            <dd >
                <ul>
                  <li><!--{echo $backdata['comment']}--></li>
                </ul>    
            </dd>
        </dl>
	</div>
</div>
</div>
<div id='footer'>

</div>
</body>
</html>

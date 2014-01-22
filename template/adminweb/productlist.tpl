<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/datalist.js"></script>
<script language="javascript" type="text/javascript" src="./staticment/js/My97DatePicker/WdatePicker.js"></script>
<script src="./staticment/js/process_stock.js" type="text/javascript" charset="UTF-8"></script>
<script type="text/javascript">
$(function(){

	
	/*图片预览*/
	var x = 10;
	var y = 20;
	$("a.tooltip").mouseover(function(e){
		this.myTitle = this.title;
		this.title = "";	
		var tooltip = "<div id='tooltip'><img src='"+ this.href +"' alt='产品预览图'/><\/div>"; //创建 div 元素
		$("body").append(tooltip);	//把它追加到文档中						 
		$("#tooltip")
			.css({
				"top": (e.pageY+y) + "px",
				"left":  (e.pageX+x)  + "px"
			}).show("fast");	  //设置x坐标和y坐标，并且显示
    }).mouseout(function(){
		this.title = this.myTitle;	
		$("#tooltip").remove();	 //移除 
    }).mousemove(function(e){
		$("#tooltip")
			.css({
				"top": (e.pageY+y) + "px",
				"left":  (e.pageX+x)  + "px"
			});
	});
	/*End*/
})
/*AJAX删除 */
function delitem(pid){
	var p = window.confirm("确定删除?");
	if(!p)return;
	CommomAjax('POST','index.php?action=product_list',{'detail':'delete','pid':pid},function(msg){alert(msg);if(msg=='删除成功')window.location.reload();});
}
</script>
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
#searchform { font-size:12px;}
#searchform input,#searchform select{width:140px;border: double #CCCCFF 1px; font-size: 12px; margin-top:5px}
#searchform #subre{width:75px; height:21px; border:none; cursor:pointer;}
.big {font-size: 16px}
.point { cursor:pointer;}
#subinput{width:82px; height:20px; border:none; cursor:pointer;}
#supplier { margin-left:0px; position:absolute; width:300px;}
#selsupplier { margin-left:200px; position:absolute;}
#suplist { font-size:14px;}

<!--固定浮动-->

</style>
<body onmousemove="MouseMoveToResize(event);" onmouseup="MouseUpToResize();" >
<form name="searchform"  action="index.php?action=product_list&detail=list" method="post" id="searchform">
SKU：<input type="text" name="sku" id="sku" value="<!--{echo $sku}-->"/>&nbsp; &nbsp;
产品名称：<input type="text" name="product_name" id="product_name" value="<!--{echo $product_name}-->"/>&nbsp; &nbsp;
添加时间：从 <input type="text" name="startTime"  class="find-T" onClick="WdatePicker()"  value="<!--{echo $startTime}-->"/>
到 <input type="text" name="endTime" class="find-T" onClick="WdatePicker()"  value="<!--{echo $endTime}-->"/><br /><br />
<!--{echo $supplier_str}--><input type="submit" name="search" value="" id="subre" style="background-image:url('./staticment/images/searchan.gif'); margin-left:0px;">

<!--<input type="button" onclick="document.execCommand('print')"  value="打印"/>-->
</form>
<table id="mytable" cellspacing="0" width="1100" id="mytable">
  <tr>
    <th class='list' width="100">产品sku<a href="<!--{echo $t_orderslink;}-->"><!--{echo $t_orderimg}--></a></th>
    <th class='list' width="200">产品名称</th>
    <th class='list' width="80">产品图片</th>
    <th class='list' width="100">类别名称</th>
<!--    <th class='list'>manufacturer</th>
    <th class='list'>brand_name</th>
    <th class='list'>model_number</th>
    <th class='list'>manufacturer_part_number</th>
    <th class='list'>package_quantity</th>
    <!--<th class='list'>upc_or_ean</th>
    <th class='list'>conditionerp</th>-->
    <th class='list' width="100">成本价(USD)</th>
    <!--<th class='list' width="100">产品特性</th>-->
    <!--<th class='list'>product_desc</th>x
    <th class='list'>platinum_keywords</th>
    <th class='list'>related_keywords</th>
    <th class='list'>target_customers</th>-->
    <th class='list' width="100">问题反馈<a href="<!--{echo $q_orderslink;}-->"><!--{echo $q_orderimg}--></a></th>
    <th class='list' width="100">添加时间</th>
    <th class='list' width="50">操作</th>    
  </tr>
  <!--{if($datalist){ $i =0;}-->
  <!--{foreach($datalist as $key=>$r){$i++;}-->
  <tr>
    <td title='点击修改'><b><a href="index.php?action=product_edit&amp;detail=edit&amp;pid=<!--{echo $r['pid']}-->"><!--{echo $r['sku']}--></a>&nbsp;</b></td>
    <td><!--{echo $r['product_name']}-->&nbsp;</td>
    <td align="center"><a href="<!--{echo $r['image_url']}-->" target="_blank" title="点击查看大图" class="tooltip"><img src="<!--{echo $r['image_url']}-->" style='border:solid 1px #828482; width:50px;height:50px;'></a>&nbsp;</td>
    <td><!--{echo $r['cat_name']}-->&nbsp;</td>
    <td><!--{echo $r['cost2']}-->&nbsp;</td>
    <!--<td><!--{echo $r['key_product_features']}-->&nbsp;</td>-->
    <td><a href="index.php?action=product_guestbook&detail=list&pid=<!--{echo $r['pid']}-->&sku=<!--{echo $r['sku']}-->&product_name=<!--{echo $r['product_name']}-->" >查看问题(<font color="red"><!--{echo $r['num']}-->)</font></a>&nbsp;</td>
    <td><!--{echo date('Y-m-d',strtotime($r['cdate']))}-->&nbsp;</td>
    <td><a href="index.php?action=product_edit&amp;detail=edit&amp;pid=<!--{echo $r['pid']}-->" title='修改'><img src="./staticment/images/editbody.gif" border="0"></a>&nbsp;<a href="javascript:void(0);delitem(<!--{echo $r['pid']}-->)" title='删除'><img src="./staticment/images/deletebody.gif" border="0"></a></td>
  </tr>
  <!--{/foreach}-->
  <!--{/if}-->
</table>
<!--{echo $page_html}-->
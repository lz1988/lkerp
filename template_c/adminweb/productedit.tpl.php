<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-21 11:57:12, compiled from template/adminweb/productedit.tpl */ ?>
<link rel="stylesheet" href="editor/themes/default/default.css" />
<link rel="stylesheet" href="editor/plugins/code/prettify.css" />
<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />
<style type="text/css">
body{font-family: Arial,Helvetica,sans-serif;}
#bigform{border:#ececec solid 1px;border-bottom:none;border-right:none;font-size:12px;}
#bigform td{border-right:#ececec solid 1px;border-bottom:#ececec  solid 1px; border-left:none; border-top:none;}
#bigform input,#bigform select,textarea{border-left: 1px solid #C2C2C2;border-right: 1px solid #EAEAEA;border-top: 1px solid #C2C2C2;border-bottom:1px solid #eeeeee;}
#bigform input,select{width:120px;height:25px; font-size:12px;}
.tips		{ color:#c6a8c6; font-size:12px;}
.big 		{ font-size: 12px}
.point,.chs { cursor:pointer;}
.radio 		{ width:12px !important; height:12px !important; border:none !important;}
#subinput	{ width:82px; height:20px; border:none; cursor:pointer;}
#supplier 	{ margin-left:0px; width:300px;}
#selsupplier{ margin-left:200px; position:absolute;}
#suplist 	{ font-size:12px;} 

.box_2			{position:relative;overflow:hidden; bottom:-25px; height:8px;}
.arrow,.arrow_2	{position: absolute;	overflow:hidden;	float:left;}
.arrow 			{left:35px;	color:#ffffff;	z-index:2;	bottom:2px;}	
.arrow_2		{left:35px;	color:#ffc674;	z-index:1;	bottom:1px;}
#tooltip{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:2px;
	display:none;
	color:#fff;
}



</style>
<script src="formValidator4.0.1/jquery-1.4.4.js" type="text/javascript"></script>
<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script charset="utf-8" src="./staticment/js/productmod.js?20130522"></script>   	
<script src="./staticment/js/upImage.js" type="text/javascript" charset="UTF-8"></script>
<script charset="utf-8" src="editor/plugins/code/prettify.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script src="./staticment/js/process_stock.js?version=121212" type="text/javascript" charset="UTF-8"></script>
<script type="text/javascript">
    $(function() {
        $('form').keydown(function(e) {
            if (e.keyCode == '13') {
                return false;
            }
        });
        $('.returnback').click(function() {
            location.href = 'index.php?action=product_list&detail=list';
        });
    });
</script>

<form action="index.php?action=product_list" name="formedit" method="post" id="formedit" onsubmit="return checkform()">
<input type="hidden" name="detail" value="editmod" />
<input type="hidden" name="pid" value="<?php echo $product['pid'] ?>" />
<input type="hidden" name="isimage" value="<?php echo $isimage ?>" />
<input type="hidden" name="iscost" value="<?php echo $iscost ?>" />
<table  cellpadding="3" cellspacing="0"  width="1200px" id="bigform" >
    <tr>
        <td rowspan="8" align="right" width="100"><p>基本信息</p></td>
        <td align="right"><font color="red">*</font>sku</td>
        <td><input type="text" id="sku" name="sku" style="width:120px" value="<?php echo $product['sku'] ?>"/></td>
        <td><div id="skuTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>产品名称</td>
        <td><textarea type="text" id="product_name" name="product_name" style="width:100%; height:40px"/><?php echo $product['product_name'] ?></textarea></td>
        <td><div id="product_nameTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">类别</td>
        <td><?php echo $catstr ?></td>
        <td><div id="cat_idTip" style="width:250px"></div></td>
    </tr>
    <tr height="50">
        <td align="right"><font color="red"><?php echo $supplier_mod ?></font>供应商</td>
        <td><?php  echo $supplier_str ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">品牌名称</td>
        <td><input type="text" id="brand_name" name="brand_name" style="width:120px" value="<?php echo $product['brand_name'] ?>"/></td>
        <td><div id="brand_nameTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">型号</td>
        <td><input type="text" id="model_number" name="model_number" style="width:120px" value="<?php echo $product['model_number'] ?>"/></td>
        <td><div id="model_numberTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">制造商的零件编号</td>
        <td><input type="text" id="manufacturer_part_number" name="manufacturer_part_number" style="width:120px" value="<?php echo     $product['manufacturer_part_number'] ?>"/></td>
        <td><div id="manufacturer_part_numberTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>MOQ</td>
        <td><input type="text" id="upc_or_ean" name="upc_or_ean" style="width:120px" value="<?php echo $product['upc_or_ean'] ?>"/><span class="tips">&nbsp;/pcs</span></td>
        <td><div id="upc_or_eanTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td rowspan="5" align="right">成本</td>
        <td align="right"><font color="red">*</font>状态</td>
        <td>
            <select name="conditionerp">
                <?php echo ($product['conditionerp']=='normal')?' selected':'' ?>>正常<?php echo ($product['conditionerp']=='normal')?' selected':'' ?>>正常<?php echo ($product['conditionerp']=='normal')?' selected':'' ?>>正常<?php echo ($product['conditionerp']=='normal')?' selected':'' ?>>正常<?php echo ($product['conditionerp']=='normal')?' selected':'' ?>>正常<?php echo ($product['conditionerp']=='normal')?' selected':'' ?>>正常<option value='normal' <?php echo ($product['conditionerp']=='normal')?' selected':'' ?>>正常</option>
                <?php echo ($product['conditionerp']=='emptying')?' selected':'' ?>>清库<?php echo ($product['conditionerp']=='emptying')?' selected':'' ?>>清库<?php echo ($product['conditionerp']=='emptying')?' selected':'' ?>>清库<?php echo ($product['conditionerp']=='emptying')?' selected':'' ?>>清库<?php echo ($product['conditionerp']=='emptying')?' selected':'' ?>>清库<?php echo ($product['conditionerp']=='emptying')?' selected':'' ?>>清库<option value='emptying' <?php echo ($product['conditionerp']=='emptying')?' selected':'' ?>>清库</option>
                <?php echo ($product['conditionerp']=='quality')?' selected':'' ?>>停止销售-质量问题<?php echo ($product['conditionerp']=='quality')?' selected':'' ?>>停止销售-质量问题<?php echo ($product['conditionerp']=='quality')?' selected':'' ?>>停止销售-质量问题<?php echo ($product['conditionerp']=='quality')?' selected':'' ?>>停止销售-质量问题<?php echo ($product['conditionerp']=='quality')?' selected':'' ?>>停止销售-质量问题<?php echo ($product['conditionerp']=='quality')?' selected':'' ?>>停止销售-质量问题<option value='quality' <?php echo ($product['conditionerp']=='quality')?' selected':'' ?>>停止销售-质量问题</option>
                <?php echo ($product['conditionerp']=='profit')?' selected':'' ?>>停止销售-低利润<?php echo ($product['conditionerp']=='profit')?' selected':'' ?>>停止销售-低利润<?php echo ($product['conditionerp']=='profit')?' selected':'' ?>>停止销售-低利润<?php echo ($product['conditionerp']=='profit')?' selected':'' ?>>停止销售-低利润<?php echo ($product['conditionerp']=='profit')?' selected':'' ?>>停止销售-低利润<?php echo ($product['conditionerp']=='profit')?' selected':'' ?>>停止销售-低利润<option value='profit' <?php echo ($product['conditionerp']=='profit')?' selected':'' ?>>停止销售-低利润</option>
                <?php echo ($product['conditionerp']=='tort')?' selected':'' ?>>停止销售-侵权<?php echo ($product['conditionerp']=='tort')?' selected':'' ?>>停止销售-侵权<?php echo ($product['conditionerp']=='tort')?' selected':'' ?>>停止销售-侵权<?php echo ($product['conditionerp']=='tort')?' selected':'' ?>>停止销售-侵权<?php echo ($product['conditionerp']=='tort')?' selected':'' ?>>停止销售-侵权<?php echo ($product['conditionerp']=='tort')?' selected':'' ?>>停止销售-侵权<option value='tort' <?php echo ($product['conditionerp']=='tort')?' selected':'' ?>>停止销售-侵权</option>
            </select>        </td>
        <td><div id="conditionTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">原始成本</td>
        <td><?php echo $cost1input ?></td>
        <td><div id="cost1Tip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">销售成本</td>
        <td><input type="text" id="cost2" name="cost2" style="width:120px; background:#EBEBE4" value='<?=$cost2;?>'   readonly="readonly" /></td>
        <td><div id="cost2Tip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">上次采购价</td>
        <td><?php echo $costpre ?><span class="tips">&nbsp;</span></td>
        <td><div id="costpre" style="width:250px"></div></td>            
    </tr>        
    <tr>
        <td align="right"><font color="red">*</font>市场指导价</td>
        <td><input type="text" id="cost3" name="cost3" style="width:120px"  value="<?=$cost3;?>"/><span class="tips">&nbsp;
         <input  class="radio" type="radio" name="coin_code" title="美元" value="USD" onclick="changeprice('USD');" <?php echo $coin_code=='USD'?'checked':'' ?> >USD  <input class="radio" type="radio" name="coin_code" title="人民币" value="CNY" onclick="changeprice('CNY');" <?php echo $coin_code=='CNY'?'checked':'' ?> >CNY <input class="radio" type="radio"  name="coin_code" value="GBP" title="英磅" onclick="changeprice('GBP');" <?php echo $coin_code=='GBP'?'checked':'' ?> >GBP</label>&nbsp; &nbsp;(最多两位小数，超过两位系统自动四舍五入)</span><input type="hidden" id="coin_code" value=<?php echo $coin_code ?> > </td>
        <td></td>
    </tr>
    <tr>
        <td rowspan="2" align="right">图片</td>
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>产品图片</td>
        <td valign="top">
            <table cellspacing='0' cellpadding='0' border='0'>
                <tr>
                    <td>
                        <?php if(!$isshow){ ?>
                        <? include './staticment/dynamic/upFrame.php'?>
                        <?php } ?>                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="width:500px; text-align:left;margin-left:20px;font-size:12px;">已有的图片: <input type="button"   title="下载图片" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px; " name="button" id="dbutton" value="批量下载" />&nbsp;&nbsp;全选<input type="checkbox" name="cheall" id="cheall" style="width:16px;height:16px;"/><br/>
                        <?php echo $backstr ?>
                        </div>                    </td>
                </tr>
            </table>
             <!-- 存储图片临时地址-->
          <!--<div id="aryimg" style="width:500px;"><ul style="list-style-type:none;"></ul></div>-->        </td>
        <td>
            <div id="simagesTip" style="width:250px">&nbsp;</div>        </td>
    </tr>
     <tr>
        <td rowspan="2" align="right">listing图片</td>
    </tr>
    <tr>
        <td colspan="3" >  
            <table cellspacing='0' cellpadding='0' border='0' width="100%" heigh="100%" style="font-size:12px" >
                <tr>
                    <td width="80px" height="150px" align="center">淘宝<br/><br/>全选<input type="checkbox" name="listingcheall" id="listingcheall" iid="taobao" checked='true' style="width:16px;height:16px;"/><br/><br/><input type="button"   title="下载图片" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px; " name="button" onclick="dbuttoncheck('taobao')"  value="批量下载" /></td>
                    <td width="400px"><div style=" position: relative; width:500px; height: 170px;overflow-y:auto; padding: 0; margin: 0; "><?php echo $listing_taobao ?></div></td>
                    <td width="80px" align="center">京东<br/><br/>全选<input type="checkbox" name="listingcheall" id="listingcheall" iid="jd" checked='true' style="width:16px;height:16px;"/><br/><br/><input type="button"   title="下载图片" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px; " name="button" onclick="dbuttoncheck('jd')"  value="批量下载" /></td>
                    <td width="400px"><div style=" position: relative; width:500px; height: 170px;overflow-y:auto; padding: 0; margin: 0; "><?php echo $listing_jd ?></div></td>
                </tr>
                <tr>
                    <td width="80px" height="150px" align="center">一号店<br/><br/>全选<input type="checkbox" name="listingcheall" id="listingcheall" iid="yihaodian" checked='true' style="width:16px;height:16px;"/><br/><br/><input type="button"   title="下载图片" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px; " name="button" onclick="dbuttoncheck('yihaodian')"  value="批量下载" /></td>
                    <td width="400px"><div style=" position: relative; width:500px; height: 170px;overflow-y:auto; padding: 0; margin: 0; "><?php echo $listing_yihaodian ?></div></td>
                    <td align="center">亚马逊<br/><br/>全选<input type="checkbox" name="listingcheall" id="listingcheall" iid="amazon" checked='true' style="width:16px;height:16px;"/><br/><br/><input type="button"   title="下载图片" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px; " name="button" onclick="dbuttoncheck('amazon')"  value="批量下载" /></td>
                    <td width="400px"><div style=" position: relative; width:500px; height: 170px;overflow-y:auto; padding: 0; margin: 0; "><?php echo $listing_amazon ?></div></td>
                </tr>
                <tr>
                    <td  width="80px" height="150px" align="center">官网中文版<br/><br/>全选<input type="checkbox" name="listingcheall" id="listingcheall" iid="cn" checked='true' style="width:16px;height:16px;"/><br/><br/><input type="button"   title="下载图片" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px; " name="button" onclick="dbuttoncheck('cn')"  value="批量下载" /></td>
                    <td width="400px"><div style=" position: relative; width:500px; height: 170px;overflow-y:auto; padding: 0; margin: 0; "><?php echo $listing_cn ?></div></td>
                    <td align="center">官网英文版<br/><br/>全选<input type="checkbox" name="listingcheall" id="listingcheall" iid="us" checked='true' style="width:16px;height:16px;"/><br/><br/><input type="button"   title="下载图片" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px; " name="button" onclick="dbuttoncheck('us')"  value="批量下载" /></td>
                    <td width="400px"><div style=" position: relative; width:500px; height: 170px;overflow-y:auto; padding: 0; margin: 0; "><?php echo $listing_us ?></div></td>
                </tr>
            </table>
             <!-- 存储图片临时地址-->
          <!--<div id="aryimg" style="width:500px;"><ul style="list-style-type:none;"></ul></div>-->        </td> 
    </tr>
    <tr>
        <td rowspan="4" align="right">描述</td>    
    	<td align="right">质检流程</td>
        <td><?php echo $qualityhtml ?></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">海关编码</td>
        <td>
            <table border='0' id="t_k" cellspacing="5"><?php echo $key_product_features ?></table>
              <input type="button" style="background:url('./staticment/images/add_one_row.gif');background-position:-1px -4px;width:18px;height:18px;border:none;cursor:pointer" onclick="addRow_k();"   title="加一行"/>
            <script language="JavaScript"> 
                var t_k = document.getElementById("t_k");  
                function addRow_k(){ 
                    $('#t_k').append($('<tr></tr>').append($('<td></td>').css('border','0').append("<input type='input' name='key_product_features[]' size='30' value=''> <input type='button' value='' onclick='deleteRow_k(this);' title='删除一行' style=\"background:url('./staticment/images/delete_one_row.gif');width:20px;height:20px;background-position:-4px -4px;cursor:pointer;border:none \" />")));
                } 
                function deleteRow_k(btn){ 
                    var tr_k = btn.parentNode.parentNode; 
                    t_k.deleteRow(tr_k.rowIndex); 
                } 
            </script>        </td>
        <td>
            <div id="key_product_featuresTip" style="width:250px"></div>        </td>
    </tr>
    <tr>
        <td align="right">描述抓取</td>
        <td><input type="text" id="collect_url" name="collect_url" style="width:300px" />&nbsp;<a href="javascript:void(0)" id="collect_button" name="collect_button">点击抓取</a>&nbsp;<span id="collect_notice"></span></td>
        <td><div id="product_descTip" style="width:250px"></div></td>
        <script language="javascript">
            $(document).ready(function(){
                $("#collect_button").click(function(){
                    $('#collect_notice').html('数据抓取中，请稍后...');
                    var url = $('#collect_url').val();
                    $.post("index.php?action=product_list&detail=get_description", { url: url },
                        function(data){
                            $("iframe").contents().find("body").first().html(data);
                            $('#collect_notice').html('');
                        }
                    );
                });
            });
        </script>   
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>产品描述</td>
        <td>      
            <!--描述区-->      
            <?php include('template_c/admintag/tag_pdesc.tpl.php'); ?>           </td>
  <td>
            <div id="product_descTip" style="width:250px"></div>        </td>
    </tr>
    <tr>
        <td rowspan="3" align="right">关键词</td>
        <td align="right">重点关键词</td>
        <td>
            <table border='0' id="t_p" cellspacing="5"><?php echo $platinum_keywords ?></table>
              <input type="button" style="background:url('./staticment/images/add_one_row.gif');background-position:-1px -4px;width:18px;height:18px;border:none;cursor:pointer" onclick="addRow_p();"   title="加一行"/>
            <script language="JavaScript"> 
                var t_p = document.getElementById("t_p"); 
                function addRow_p(){ 
                    $('#t_p').append($('<tr></tr>').append($('<td></td>').css('border','0').append("<input type='input' name='platinum_keywords[]' size='30' value=''> <input type='button' value='' onclick='deleteRow_p(this);' title='删除一行' style=\"background:url('./staticment/images/delete_one_row.gif');width:20px;height:20px;background-position:-4px -4px;cursor:pointer;border:none \" />")));
                } 
                function deleteRow_p(btn){ 
                    var tr_p = btn.parentNode.parentNode; 
                    t_p.deleteRow(tr_p.rowIndex); 
                }
            </script>        </td>
        <td>
            <div id="platinum_keywordsTip" style="width:250px"></div>        </td>
    </tr>
    <tr>
        <td align="right">相关关键词</td>
        <td>
            <table border='0' id="t_r" cellspacing="5"><?php echo $related_keywords ?></table>
              <input type="button" style="background:url('./staticment/images/add_one_row.gif');background-position:-1px -4px;width:18px;height:18px;border:none;cursor:pointer" onclick="addRow_r();"   title="加一行"/>
            <script language="JavaScript"> 
                var t_r = document.getElementById("t_r"); 
                function addRow_r(){ 
                    $('#t_r').append($('<tr></tr>').append($('<td></td>').css('border','0').append("<input type='input' name='related_keywords[]' size='30' value=''> <input type='button' value='' onclick='deleteRow_r(this);' title='删除一行' style=\"background:url('./staticment/images/delete_one_row.gif');width:20px;height:20px;background-position:-4px -4px;cursor:pointer;border:none \" />")));

                } 
                function deleteRow_r(btn){ 
                    var tr_r = btn.parentNode.parentNode; 
                    t_r.deleteRow(tr_r.rowIndex); 
                } 
            </script>        </td>
        <td>
            <div id="related_keywordsTip" style="width:250px"></div>        </td>
    </tr>
    <tr>
        <td align="right">面向客户群</td>
        <td><input type="text" id="target_customers" name="target_customers" style="width:120px" value="<?php echo $product['target_customers'] ?>"/></td>
        <td><div id="target_customersTip" style="width:250px"></div></td>
    </tr>
    
    <tr>
        <td rowspan="14" align="right">属性</td>
       
    <td align="right"><span id="ch_inch" class="chs">[inch]</span>产品尺寸(长×宽×高)</td>
       <td><input type="text" onkeyup="getkey('product_size')" name="product_size" value="<?php echo $product['product_size'] ?>"/><span class="tips">&nbsp;/cm&nbsp;</span></td>
       <td>&nbsp;</td>
    </tr>
    <tr>
     <td align="right"><span id="ch_lbs" class="chs">[lbs]</span> <span id="ch_oz" class="chs">[oz]</span>产品净重</td>
       <td><input type="text" name="product_weight" value="<?php echo $product['product_weight'] ?>"/><span class="tips">&nbsp;/kg&nbsp;</span></td>
       <td>&nbsp;</td>
      
     </tr>
     <tr>
       
     <td align="right"><span id="ch_inch" class="chs">[inch]</span> <font color="red">*</font>单个产品包装尺寸(长&times;宽&times;高)</td>
       <td><input type="text" onkeyup="getkey('product_dimensions')"  name="product_dimensions" value="<?php echo $product['product_dimensions'] ?>"/>
         <span class="tips">&nbsp;/cm&nbsp; 每输入完一个按*号自动显示“x”</span></td>
        <td>&nbsp;</td>
     </tr>
    <tr>
        <td align="right"><span id="ch_lbs" class="chs">[lbs]</span> <span id="ch_oz" class="chs">[oz]</span> <font color="red">*</font>发货重量</td>
        <td><input type="text" name="shipping_weight" value="<?php echo $product['shipping_weight'] ?>"/>
          <span class="tips">&nbsp;/kg&nbsp;(单个产品毛重(产品+包装重量))</span></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">长&times;宽&times;高(箱)</td>
        <td><input type="text" onkeyup="getkey('box_product_dimensions')" name="box_product_dimensions" value="<?php echo $product['box_product_dimensions'] ?>"/><span class="tips">&nbsp;/cm&nbsp;(材积重/箱)</span></td>
        <td>&nbsp;</td>
    </tr>
    
     <tr>
        <td align="right">发货重量(箱)</td>
        <td><input type="text" name="box_shipping_weight" value="<?php echo $product['box_shipping_weight'] ?>"/><span class="tips">&nbsp;/kg&nbsp;(产品重/箱)</span></td>
        <td>&nbsp;</td>
    </tr>
    
     
     
    <tr>
        <td align="right">规格</td>
        <td><input type="text" name="style_name" value="<?php echo $product['style_name'] ?>"/></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>颜色</td>
        <td><input type="text" name="color" value="<?php echo $product['color'] ?>"/></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">大小</td>
        <td><input type="text" name="size" value="<?php echo $product['size'] ?>"/></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">包装风格</td>
        <td>
            <select name="box_type">
            <option value="">==选择包装==</option>
            <?php echo ($product['box_type']=='白盒中性包装')?' selected':'' ?> >白盒中性包装<?php echo ($product['box_type']=='白盒中性包装')?' selected':'' ?> >白盒中性包装<?php echo ($product['box_type']=='白盒中性包装')?' selected':'' ?> >白盒中性包装<?php echo ($product['box_type']=='白盒中性包装')?' selected':'' ?> >白盒中性包装<?php echo ($product['box_type']=='白盒中性包装')?' selected':'' ?> >白盒中性包装<?php echo ($product['box_type']=='白盒中性包装')?' selected':'' ?> >白盒中性包装<option value="白盒中性包装" <?php echo ($product['box_type']=='白盒中性包装')?' selected':'' ?> >白盒中性包装</option>
            <?php echo ($product['box_type']=='彩盒中性包装')?' selected':'' ?>>彩盒中性包装<?php echo ($product['box_type']=='彩盒中性包装')?' selected':'' ?>>彩盒中性包装<?php echo ($product['box_type']=='彩盒中性包装')?' selected':'' ?>>彩盒中性包装<?php echo ($product['box_type']=='彩盒中性包装')?' selected':'' ?>>彩盒中性包装<?php echo ($product['box_type']=='彩盒中性包装')?' selected':'' ?>>彩盒中性包装<?php echo ($product['box_type']=='彩盒中性包装')?' selected':'' ?>>彩盒中性包装<option value="彩盒中性包装" <?php echo ($product['box_type']=='彩盒中性包装')?' selected':'' ?>>彩盒中性包装</option>
            <?php echo ($product['box_type']=='品牌包装')?' selected':'' ?>>品牌包装<?php echo ($product['box_type']=='品牌包装')?' selected':'' ?>>品牌包装<?php echo ($product['box_type']=='品牌包装')?' selected':'' ?>>品牌包装<?php echo ($product['box_type']=='品牌包装')?' selected':'' ?>>品牌包装<?php echo ($product['box_type']=='品牌包装')?' selected':'' ?>>品牌包装<?php echo ($product['box_type']=='品牌包装')?' selected':'' ?>>品牌包装<option value="品牌包装" <?php echo ($product['box_type']=='品牌包装')?' selected':'' ?>>品牌包装</option>
            <?php echo ($product['box_type']=='其它包装')?' selected':'' ?>>其它包装<?php echo ($product['box_type']=='其它包装')?' selected':'' ?>>其它包装<?php echo ($product['box_type']=='其它包装')?' selected':'' ?>>其它包装<?php echo ($product['box_type']=='其它包装')?' selected':'' ?>>其它包装<?php echo ($product['box_type']=='其它包装')?' selected':'' ?>>其它包装<?php echo ($product['box_type']=='其它包装')?' selected':'' ?>>其它包装<option value="其它包装" <?php echo ($product['box_type']=='其它包装')?' selected':'' ?>>其它包装</option>
            </select>        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">一箱个数</td>
        <td><input type="text" name="unit_box" value="<?php echo $product['unit_box'] ?>"/><span class="tips">&nbsp;unit/box</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">是否有认证</td>
        <td><input type="text" name="attestation" value="<?php echo $product['attestation'] ?>" style="width:300px"/><span class="tips">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
</table>
<?php if($isshow){ ?>
<input type="button" value="返回" class="returnback" style="background:url('./staticment/images/button_bj.gif') no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px;"/>
<?php } else { ?>   
<input type="submit" value="提交" name="sumbit" style="background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px;"/>
<span id="loading" style="font-size:12px">    
<?php } ?>
</span>
</form>
<!--用于提示DIV-->
<div style="border:#ffc674 solid 1px; width:150px; background-color:#FFFFE5; height:25px; position:absolute; display:none" id="tipsdiv">
        <div class="box_2">
            <div class="arrow">◆</div>
            <div class="arrow_2">◆</div>
        </div>
       <div style="margin-top:-3px; padding-left:5px;font-size:12px;" id="tipcont"></div>
</div>
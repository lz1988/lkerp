<link rel="stylesheet" href="editor/themes/default/default.css" />
<link rel="stylesheet" href="editor/plugins/code/prettify.css" />
<link rel='stylesheet' type='text/css' href='./staticment/css/jquery.autocomplete.css' />
<script src="formValidator4.0.1/jquery-1.4.4.js" type="text/javascript"></script>
<script type='text/javascript' src='./staticment/js/jquery.autocomplete.js'></script>
<script charset="utf-8" src="editor/kindeditor.js"></script>
<script charset="utf-8" src="editor/lang/zh_CN.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/productmod.js?20120301"></script>   	
<script src="./staticment/js/upImage.js" type="text/javascript" charset="UTF-8"></script>
<script charset="utf-8" src="editor/plugins/code/prettify.js"></script>
<script src="./staticment/js/process_stock.js" type="text/javascript" charset="UTF-8"></script>

<style type="text/css">
body{font-family: Arial,Helvetica,sans-serif; font-size:12px}
#bigform{border:#ececec solid 1px;border-bottom:none;border-right:none;font-size:12px;}
#bigform td{border-right:#ececec solid 1px;border-bottom:#ececec  solid 1px; border-left:none; border-top:none;}
#bigform input,#bigform select,textarea{border-left: 1px solid #C2C2C2;border-right: 1px solid #EAEAEA;border-top: 1px solid #C2C2C2;border-bottom:1px solid #eeeeee;}
#bigform input,select{width:120px;height:25px; font-size:12px;}
.tips{ color:#c6a8c6; font-size:12px;}
.big {font-size: 12px}
.point { cursor:pointer;}
.radio { width:12px !important; height:12px !important; border:none !important;}
#subinput{width:82px; height:20px; border:none; cursor:pointer;}
#supplier { margin-left:0px;}
#selsupplier { margin-left:200px; position:absolute;}
#suplist { font-size:12px;}
</style>

<span style="font-size:12px;">当前选择的类别是：<!--{echo $showcat}--></span>
<form action="index.php?action=product_new&detail=newmod" name="formedit" method="post" id="formedit">
<input type="hidden" name="cat_id" value="<!--{echo $cat_id}-->" />
<table  border="1" cellpadding="3" cellspacing="0"  width="1200px" id="bigform">
    <tr>
        <td rowspan="7" align="right" width="100"><p>基本信息</p></td>
        <td width="324" align="right"><font color="red">*</font>sku</td>
      <td width="484"><input type="text" id="sku" name="sku" style="width:120px" class="checksku" /></td>
      <td width="258"><div id="skuTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>产品名称</td>
        <td><textarea type="text" id="product_name" name="product_name" style="width:100%; height:40px" class="checkproduct_name"/></textarea></td>
        <td><div id="product_nameTip" style="width:250px"></div></td>
    </tr>
    <tr height="50">
        <td align="right"><font color="red"><!--{echo $supplier_mod}--></font>供应商</td>
        <td><!--{ echo $supplier_str}--></td>
        <td>&nbsp;</td>            
    </tr>
    <tr>
        <td align="right">品牌名称</td>
        <td><input type="text" id="brand_name" name="brand_name" style="width:120px" /></td>
        <td><div id="brand_nameTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">型号</td>
        <td><input type="text" id="model_number" name="model_number" style="width:120px" /></td>
        <td><div id="model_numberTip" style="width:250px"></div></td>            
    </tr>
    <tr>
        <td align="right">制造商的零件编号</td>
        <td><input type="text" id="manufacturer_part_number" name="manufacturer_part_number" style="width:120px" /></td>
        <td><div id="manufacturer_part_numberTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>MOQ</td>
        <td><input type="text" id="upc_or_ean" name="upc_or_ean" style="width:120px" /><span class="tips">&nbsp;/pcs</span></td>
        <td><div id="upc_or_eanTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td rowspan="5" align="right">成本</td>
        <td align="right"><font color="red">*</font>状态</td>
        <td>
            <select name="conditionerp">
                <option value="normal">正常</option>
                <option value="emptying">清库</option>
                <option value="quality">停止销售-质量问题</option>
                <option value="profit">停止销售-低利润</option>
                <option value="tort">停止销售-侵权</option>
            </select>
        </td>
        <td><div id="conditionTip" style="width:250px"></div></td>            
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>原始成本</td>
        <td><input type="text" id="cost1" name="cost1" style="width:120px" onkeyup="docost()"  /><span class="tips">&nbsp;</span></td>
        <td><div id="cost1Tip" style="width:250px"></div></td>            
    </tr> 
    <tr>
        <td align="right">销售成本</td>
        <td><input type="text" id="cost2" name="cost2" style="width:120px; background:#ececec"   readonly="readonly" /></td>
        <td><div id="cost2Tip" style="width:250px"></div></td>            
    </tr>
    <tr>
        <td align="right">上次采购价</td>
        <td><input type="text" id="costpre" name="costpre" style="width:120px"   disabled="disabled"/><span class="tips">&nbsp;</span></td>
        <td><div id="costpre" style="width:250px"></div></td>            
    </tr>       
    <tr>
        <td align="right"><font color="red">*</font>市场指导价</td>
        <td><input type="text" id="cost3" name="cost3" style="width:120px" /><span class="tips">&nbsp;<label><input  class="radio" type="radio" name="coin_code" title="美元" value="USD" checked="checked" onclick="changeprice('USD');"/>USD </label><label><input  class="radio" type="radio" name="coin_code" title="人民币" value="CNY" onclick="changeprice('CNY');" />CNY </label><label><input class="radio" type="radio" name="coin_code" value="GBP" title="英磅" onclick="changeprice('GBP');"/>GBP </label>&nbsp; &nbsp;(最多两位小数，超过两位系统自动四舍五入)</span><input type="hidden" id="coin_code" value='USD'/></td>
        <td></td>
    </tr>
    <tr>
        <td rowspan="2" align="right">图片</td>  
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>产品图片</td>
        <td><? include './staticment/dynamic/upFrame.php'?></td>
        <td><div id="simagesTip" style="width:250px"></div></td>
    </tr>    
    <tr>
        <td rowspan="4" align="right">描述</td>    
    	<td align="right">质检流程</td>
        <td><!--{echo $qualityhtml}--></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">海关编码</td>
        <td>
            <table border='0' id="t_k" cellspacing="5"> </table>
            &nbsp; <input type="button" style="background:url('./staticment/images/add_one_row.gif');background-position:-1px -4px;width:18px;height:18px;border:none;cursor:pointer" onclick="addRow_k();"   title="加一行"/>
            <script language="JavaScript"> 
                var t_k = document.getElementById("t_k");  
                function addRow_k(){ 
                    $('#t_k').append($('<tr></tr>').append($('<td></td>').css('border','0').append("<input type='input' name='key_product_features[]' size='30' value=''> <input type='button' value='' onclick='deleteRow_k(this);' title='删除一行' style=\"background:url('./staticment/images/delete_one_row.gif');width:20px;height:20px;background-position:-4px -4px;cursor:pointer;border:none \" />")));
                } 
                function deleteRow_k(btn){ 
                    var tr_k = btn.parentNode.parentNode; 
                    t_k.deleteRow(tr_k.rowIndex); 
                } 
            </script>
        </td>
        <td><div id="key_product_featuresTip" style="width:250px"></div></td>
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
        <!--{layout:admintag/tag_pdesc}-->
        </td>
        <td><div id="product_descTip" style="width:250px"></div></td>            
    </tr>
    <tr>
        <td rowspan="3" align="right">关键词</td>  
        <td align="right">重点关键词</td>
        <td>
            <table border='0' id="t_p" cellspacing="5"> </table>
            &nbsp; <input type="button" style="background:url('./staticment/images/add_one_row.gif');background-position:-1px -4px;width:18px;height:18px;border:none;cursor:pointer" onclick="addRow_p();"   title="加一行"/>
            <script language="JavaScript"> 
                var t_p = document.getElementById("t_p"); 
                function addRow_p(){ 
                    $('#t_p').append($('<tr></tr>').append($('<td></td>').css('border','0').append("<input type='input' name='platinum_keywords[]' size='30' value=''> <input type='button' value='' onclick='deleteRow_p(this);' title='删除一行' style=\"background:url('./staticment/images/delete_one_row.gif');width:20px;height:20px;background-position:-4px -4px;cursor:pointer;border:none \" />")));
                } 
                function deleteRow_p(btn){ 
                    var tr_p = btn.parentNode.parentNode; 
                    t_p.deleteRow(tr_p.rowIndex); 
                }
            </script>
        </td>
        <td><div id="platinum_keywordsTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td align="right">相关关键词</td>
        <td>
            <table border='0' id="t_r" cellspacing="5"> </table>
            &nbsp; <input type="button" style="background:url('./staticment/images/add_one_row.gif');background-position:-1px -4px;width:18px;height:18px;border:none;cursor:pointer" onclick="addRow_r();"   title="加一行"/>
            <script language="JavaScript"> 
                var t_r = document.getElementById("t_r"); 
                function addRow_r(){ 
                    $('#t_r').append($('<tr></tr>').append($('<td></td>').css('border','0').append("<input type='input' name='related_keywords[]' size='30' value=''> <input type='button' value='' onclick='deleteRow_r(this);' title='删除一行' style=\"background:url('./staticment/images/delete_one_row.gif');width:20px;height:20px;background-position:-4px -4px;cursor:pointer;border:none \" />")));
                } 
                function deleteRow_r(btn){ 
                    var tr_r = btn.parentNode.parentNode; 
                    t_r.deleteRow(tr_r.rowIndex); 
                } 
            </script>
        </td>
        <td><div id="related_keywordsTip" style="width:250px"></div></td>            
    </tr>
    <tr>
        <td align="right">面向客户群</td>
        <td><input type="text" id="target_customers" name="target_customers" style="width:120px" /></td>
        <td><div id="target_customersTip" style="width:250px"></div></td>
    </tr>
    <tr>
        <td rowspan="14" align="right">属性</td>
       
    <td align="right"><span id="ch_inch" class="chs">[inch]</span>产品尺寸(长×宽×高)</td>
      <td><input type="text"onkeyup="getkey('product_size')" name="product_size"/>
       <span class="tips">&nbsp;/cm&nbsp;</span></td>
       <td>&nbsp;</td>
    </tr>
    <tr>
     <td align="right"><span id="ch_lbs" class="chs">[lbs]</span> <span id="ch_oz" class="chs">[oz]</span>产品净重</td>
      <td><input type="text" name="product_weight"/>
      <span class="tips">&nbsp;/kg&nbsp;</span></td>
       <td>&nbsp;</td>
      
     </tr>
     <tr>
       
     <td align="right"><span id="ch_inch" class="chs">[inch]</span> <font color="red">*</font>单个产品包装尺寸(长&times;宽&times;高)</td>
       <td><input type="text" onkeyup="getkey('product_dimensions')"  name="product_dimensions"/>
         <span class="tips">&nbsp;/cm&nbsp; 每输入完一个按*号自动显示“x”</span></td>
        <td>&nbsp;</td>
     </tr>
    <tr>
        <td align="right"><span id="ch_lbs" class="chs">[lbs]</span> <span id="ch_oz" class="chs">[oz]</span> <font color="red">*</font>发货重量</td>
        <td><input type="text" name="shipping_weight"/>
          <span class="tips">&nbsp;/kg&nbsp;(单个产品毛重(产品+包装重量))</span></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">长&times;宽&times;高(箱)</td>
        <td><input type="text" onkeyup="getkey('box_product_dimensions')" name="box_product_dimensions" /><span class="tips">&nbsp;/cm&nbsp;(材积重/箱)</span></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">发货重量(箱)</td>
        <td><input type="text" name="box_shipping_weight"/><span class="tips">&nbsp;/kg&nbsp;(产品重/箱)</span></td>
        <td>&nbsp;</td>
    </tr>
   
    <tr>
        <td align="right">规格</td>
        <td><input type="text" name="style_name"/></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right"><font color="red">*</font>颜色</td>
        <td><input type="text" name="color"/></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
     	<td align="right">大小</td>
        <td><input type="text" name="size"/></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">包装风格</td>
        <td>
            <select name="box_type">
            <option value="">==选择包装==</option>
            <option value="白盒中性包装">白盒中性包装</option>
            <option value="彩盒中性包装">彩盒中性包装</option>
            <option value="品牌包装">品牌包装</option>
            <option value="其它包装">其它包装</option>
            </select>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">一箱个数</td>
        <td><input type="text" name="unit_box"/><span class="tips">&nbsp;unit/box</td>
        <td>&nbsp;</td>
    </tr>
</table>
<input type="hidden" name="trh" value="<!--{echo $trh}-->" />
<input type="submit" value="Submit" id="savenew" name="sumbit" style="background:url('./staticment/images/button_bj.gif') no-repeat; width:75px; height:22px;border:none;cursor:pointer; margin:2px;" />
<!--<input type="button" id="savenew"  value="保存"/> <span id="loading" style="font-size:12px"></span>-->
</form> 
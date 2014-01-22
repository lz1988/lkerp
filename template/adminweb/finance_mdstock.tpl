<!--此模板应可用于采购付款调用，销售收款调用等-->
<!--{layout:admintag/tag_evdheader}-->
<form action="index.php?action=finance_mdstock&detail=stock_evd" method="post"  name="finance_evd" >
<input type="hidden" name="order_id" value="<!--{echo $order_id}-->" />
<table width="1100" border="0" align="center" cellpadding="0" cellspacing="0" >
  <tr>
    <td style="border:1px #b2b2b2 solid"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center" style="border-bottom:1px #b2b2b2 solid; padding-top:20px"><table width="400" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="40" colspan="4" align="center" class="title">记帐凭证</td>
          </tr>
         <tr class="bz">
            <td width="200" height="39" align="right">日期：<input type="text" name="cdate" value="<!--{echo date('Y-m-d',time())}-->" onClick="WdatePicker()" style="width:100px"/>
            </td>
            <td width="100" align="right">凭证字：</td>
            <td width="100"><select name="eviden_sign" ><option value="记">记</option></select></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table  class="wall_table" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr bgcolor="#CCCCCC" class="ZY">
            <td width="270" height="38">摘要</td>
            <td width="290">科目</td>
            <td width="80">币别</td>
            <td width="80">汇率</td>
            <td width="80">原币金额</td>                        
            <td width="150">借方</td>
            <td width="150">贷方</td>
          </tr>
			<!--{if ($datalist)}-->
            	<!--{foreach ($datalist as $val)}-->
                	<tr>
                    <td height="30"><input class="f_desc" name="comment[]" type="text"  value="<!--{echo $val['desc']}-->" /></td>
                       <td>
<input type="hidden"  name="pro_id[]" class="pro_id" value="<!--{echo $val['pro_id']}-->" />
<input type="hidden"  name="pro_code[]" class="pro_code" value="<!--{echo $val['pro_code']}-->" />
<input type="hidden"  name="pro_name[]" class="pro_name" value="<!--{echo $val['pro_name']}-->" />
<input type="hidden"  name="esse_id[]" class="esse_id"  value="<!--{echo  $val['esse_id']}-->"/><!--实体类型ID-->
<input type="hidden"  name="esse_cat_type[]" class="esse_cat_type" value="<!--{echo $val['esse_cat_type']}-->" /><!--pid or esseid-->
<input type="hidden"  name="esse_cat_id[]" class="esse_cat_id"  value="<!--{echo $val['esse_cat_id']}-->" /><!--实体ID-->
<input type="hidden"  name="esse_cat_name[]" class="esse_cat_name"  value="<!--{echo $val['esse_cat_name']}-->" /><!--实体名称-->
<input type="hidden" class="esse_cat_code"  value="<!--{echo $val['esse_cat_code']}-->" /><!--实体编码-->
<input class="wall_select" type="text" value="<!--{echo $val['show']}-->" />
                       </td>
                       <td><!--{echo $coin_codehtml}--></td>
			           <td><input class="exshow"  type="text" disabled="disabled" /><input class="exshow" type="hidden" name="coin_rate[]" /></td>                       <td><input class="exshow"  type="text" name="s_price[]" /></td> 
                       <td><input class="in_price" type="text" name="in_price[]"  value="<!--{echo $val['in_price']}-->"/></td>                       <td><input class="out_price" type="text" name="out_price[]"  value="<!--{echo $val['out_price']}-->"/></td>   
                    </tr>
            	<!--{/foreach}-->                
			<!--{/if}-->          
        </table></td>
      </tr>
	  <tr>
	  	<td>
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="wall_table_foot">
			 <tr class="bz">
				<td  height="25">&nbsp;</td>
				<td class="total_in_price" width="150"  align="center"><!--{echo $total_price}--></td>
				<td class="total_out_price" width="150"  align="center"><!--{echo $total_price}--></td>
			  </tr>
			</table>
		</td>
	  </tr>
      <tr>
        <td height="35" align="center" class="bhz">        
   		<!--{if ($datalist)}-->
            <!--{foreach ($datalist as $val)}-->
            <!--{if ($val['esse_cat_code'])}-->
               <table id="wall_live_table[]" width="40%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px">              	<tr>
                    <td width="20%" height="28" align="right"><!--{echo $esse_name}-->：</td>
                    <td width="26%" align="center">
                	<input class="esse_cat" type="text" value="<!--{echo $val['esse_cat_code']}-->" style="border:1px #b2b2b2 solid;width:150px; height:20px" />
                	</td>
               		<td width="54%" align="left"></td>
             	 </tr>
            	</table>
           <!--{else}-->
           <table id="wall_live_table[]" width="40%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px">
           </table>           
           <!--{/if}-->
           <!--{/foreach}-->                
		<!--{/if}-->
        </td>
        </tr>
      <tr>
        <td><table width="100%" height="34" border="0" align="center" cellpadding="0" cellspacing="0" class="wall_footer">
          <tr class="bz">
            <td width="25%" height="34">审核：</td>
            <td width="25%">过帐：</td>
            <td width="25%">出纳：</td>
            <td width="25%">制单：<!--{echo $_SESSION['chi_name']}--></td> 
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="1100" align="center">
	<tr><td colspan="3" style="height:10px"></td></tr>
	<tr>
    	<td width="50"><span class="wall_add" style="font-weight:bold; cursor:pointer">&nbsp;+&nbsp;</span></td>
    	<td width="">&nbsp;</td>
    	<td width="70"><input type="image" src="./staticment/images/SaveIcon.gif" /></td>
    </tr>
</table>
</form>
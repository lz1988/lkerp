<!--{layout:admintag/tag_evdheader}-->
<form action="<!--{echo $commit_link}-->" method="post"  name="finance_evd">
<table width="1100" border="0" align="center" cellpadding="0" cellspacing="0" >
  <tr>
    <td style="border:1px #b2b2b2 solid"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center" style="border-bottom:1px #b2b2b2 solid; padding-top:20px"><table width="400" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="40" colspan="4" align="center" class="title">记帐凭证</td>
          </tr>
         <tr class="bz">
            <td width="200" height="39" align="right">日期：
                <!--{if ($cdate)}-->
                    <input type="text" name="cdate" value="<!--{echo $cdate}-->" onClick="WdatePicker()" style="width:100px"/>
                <!--{else}-->
                    <input type="text" name="cdate" value="<!--{echo date('Y-m-d',time())}-->" onClick="WdatePicker()" style="width:100px"/>
                <!--{/if}-->
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
                <td height="30"><input class="f_desc" name="comment[]" type="text" value="<!--{echo $val['comment']}-->"/></td>
                <td>
                    <input type="hidden"  name="detail_id[]" value="<!--{echo $val['id']}-->"/> 
                    <input type="hidden"  name="pro_id[]" class="pro_id" value="<!--{echo $val['proid']}-->"/>
                    <input type="hidden"  name="pro_code[]" class="pro_code" value="<!--{echo $val['pro_code']}-->"/>
                    <input type="hidden"  name="pro_name[]" class="pro_name" value="<!--{echo $val['pro_name']}-->"/>
                    <input type="hidden"  name="esse_id[]" class="esse_id" value="<!--{echo $val['esse_id']}-->" />
                    <input type="hidden"  name="esse_cat_type[]" class="esse_cat_type" value="<!--{echo $val['esse_cat_type']}-->"/>
                    <input type="hidden"  name="esse_cat_id[]" class="esse_cat_id" value="<!--{echo $val['esse_cat_id']}-->" />
                    <input type="hidden"  name="esse_cat_name[]" class="esse_cat_name" value="<!--{echo $val['esse_cat_name']}-->" />
                    <input type="hidden" class="esse_cat_code" value="<!--{echo $val['esse_cat_code']}-->" />
                    <input class="wall_select" type="text" value="<!--{echo $val['pro_code']}-->-<!--{echo $val['pro_name']}--><!--{if ($val['esse_cat_code'])}-->/<!--{echo $val['esse_cat_code']}-->-<!--{echo $val['esse_cat_name']}--><!--{/if}-->" />
                </td>
                <td><!--{echo $val['s_coin_code']}--></td>
                <td>
                    <input class="exshow"  type="text" disabled="disabled" value="<!--{echo $val['coin_rate']}-->" />
                    <input class="exshow"  type="hidden" name="coin_rate[]" value="<!--{echo $val['coin_rate']}-->" />
                </td>
                <td><input class="exshow"  type="text" name="s_price[]" value="<!--{echo $val['s_price']}-->" /></td> 
                <td><input class="in_price" type="text" name="in_price[]" value="<!--{echo $val['in_price']}-->" /></td>  
                        
                <td><input class="out_price" type="text" name="out_price[]" value="<!--{echo $val['out_price']}-->" /></td>            
              </tr>
            <!--{/foreach}-->
          <!--{else}-->
          <tr>
            <td height="30"><input class="f_desc" name="comment[]" type="text" /></td>
            <td>
                <input type="hidden"  name="pro_id[]" class="pro_id"/>
                <input type="hidden"  name="pro_code[]" class="pro_code"/>
                <input type="hidden"  name="pro_name[]" class="pro_name"/>
                <input type="hidden"  name="esse_id[]" class="esse_id" />
                <input type="hidden"  name="esse_cat_type[]" class="esse_cat_type"/>
                <input type="hidden"  name="esse_cat_id[]" class="esse_cat_id" />
                <input type="hidden"  name="esse_cat_name[]" class="esse_cat_name" />
                <input type="hidden" class="esse_cat_code" />
                <input class="wall_select" type="text" />
            </td>
            <td><!--{echo $coin_codehtml}--></td>
            <td>
                <input class="exshow"  type="text" disabled="disabled" />
                <input class="exshow"  type="hidden" name="coin_rate[]" />
            </td>
            <td><input class="exshow"  type="text" name="s_price[]" /></td> 
            <td><input class="in_price" type="text" name="in_price[]" /></td>  
                    
            <td><input class="out_price" type="text" name="out_price[]" /></td>            
          </tr>
          <tr>
            <td height="30"><input class="f_desc" name="comment[]" type="text" /></td>
            <td>
                <input type="hidden"  name="pro_id[]" class="pro_id"/>
                <input type="hidden"  name="pro_code[]" class="pro_code"/>
                <input type="hidden"  name="pro_name[]" class="pro_name"/>
                <input type="hidden"  name="esse_id[]" class="esse_id" />
                <input type="hidden"  name="esse_cat_type[]" class="esse_cat_type"/>
                <input type="hidden"  name="esse_cat_id[]" class="esse_cat_id" />
                <input type="hidden"  name="esse_cat_name[]" class="esse_cat_name" />
                <input type="hidden" class="esse_cat_code" />
                <input class="wall_select" type="text" />
            </td>
            <td><!--{echo $coin_codehtml}--></td>
            <td>
                <input class="exshow"  type="text" disabled="disabled" />
                <input class="exshow"  type="hidden" name="coin_rate[]" />
            </td>
            <td><input class="exshow"  type="text" name="s_price[]" /></td> 
            <td><input class="in_price" type="text" name="in_price[]" /></td>  
                    
            <td><input class="out_price" type="text" name="out_price[]" /></td>            
          </tr>
          <!--{/if}-->
        </table></td>
      </tr>
	  <tr>
	  	<td>
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="wall_table_foot">
			 <tr class="bz">
				<td  height="25">&nbsp;</td>
				<td class="total_in_price" width="150"  align="center"><!--{echo $total_in_price}--></td>
				<td class="total_out_price" width="150"  align="center"><!--{echo $total_out_price}--></td>
			  </tr>
			</table>
		</td>
	  </tr>
      <tr>
        <td height="35" align="center" class="bhz">                
        <!--{if ($datalist)}-->
            <!--{foreach ($datalist as $val)}-->
                <table id="wall_live_table[]" width="40%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px;display: none;">  
                <!--{if ($val['esse_id'] > 0)}-->
                    <tr>
                    <td align="right" width="20%" height="28"><!--{echo $val['cat_name']}-->：</td>
                    <td align="center" width="26%">
                    <input class="esse_cat" type="text" style="border: 1px solid rgb(178, 178, 178); width: 150px; height: 20px;" value="<!--{echo $val['esse_cat_code']}-->">
                    </td>
                    <td align="left" width="54%"></td>
                    </tr>
                <!--{/if}-->
                </table>
            <!--{/foreach}-->
        <!--{else}-->
            <table id="wall_live_table[]" width="40%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px">              
            </table>
            <table id="wall_live_table[]" width="40%" border="0" align="center" cellpadding="0" cellspacing="0" style="font-size:12px">              
            </table>
        <!--{/if}-->
        </td>
        </tr>
      <tr>
        <td><table width="100%" height="34" border="0" align="center" cellpadding="0" cellspacing="0" class="wall_footer">
          <tr class="bz">
            <td width="25%" height="34">审核：</td>
            <td width="25%">过帐：</td>
            <td width="25%">出纳：</td>
            <td width="25%">制单：
                <!--{if ($cuser)}-->
                    <!--{echo $cuser}--><input type="hidden" name="cuser" value="<!--{echo $cuser}-->" />
                <!--{else}-->
                    <!--{echo $_SESSION['chi_name']}-->
                <!--{/if}-->
            </td> 
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
    	<td width="">&nbsp;<input type="hidden" name="id" value="<!--{echo $editid}-->" /></td>
    	<td width="70"><input type="image" src="./staticment/images/SaveIcon.gif" /></td>
    </tr>
</table>
</form>

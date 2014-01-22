<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>备货申请</title>
</head>
<style type="text/css">
.Ptable_left td{padding: 5px 5px; font-size:12px;}
.Ptable_left input,select{padding:3px 3px 5px 5px; width:200px; margin-right:1px; font-size:12px; margin-left:5px; background:#ffffff}
.Ptable{background: none repeat scroll 0 0 #f3e6c6; font-size:12px; color:#b9b9b9; border:#ead9b3 solid 1px;}
.Ptable td { background: none repeat scroll 0 0 #fffcf5;padding: 7px 5px;}
.Ptable input,select{ padding:3px 3px 5px 5px; width:200px; margin-right:3px; font-size:12px;  margin-left:3px; background:#ffffff; color:#666666;}
.Ptable_left input,.Ptable input,select{border-left: 1px solid #C2C2C2;border-right: 1px solid #EAEAEA;border-top: 1px solid #C2C2C2;border-bottom:1px solid #eeeeee;}
#subinput{border: medium none; cursor: pointer; height: 30px; width: 82px;}
input,select{height: 25px;}
.coin_code {height:auto !important; width:auto !important; padding:0px;}
</style>
<!--{echo $jslink}-->
<body>
	<form id="stockform" onsubmit="return checkform()" action="<!--{echo $submit_action}-->" method="post">
    <table width="1000" border="0" cellpadding="0" cellspacing="0" id="commomform">
        <tbody>
            <tr>
                <td width="500" align="left" valign="top">
                	<table width="100%" border="0" cellpadding="0" cellspacing="1" class="Ptable_left">
                		<tbody>
                			<tr>
                				<td align="right"> 产品SKU： </td>
                				<td width="399">
                					<input type="text" onblur="checksku(this.value)" value="<!--{echo $dataresult['sku']}-->" name="sku" />
                					*<span class="tips_产品SKU" style="color: rgb(255, 204, 136);"></span> </td>
               				</tr>
                			<tr>
                				<td align="right"> 备货仓库： </td>
                				<td width="399">
                					<!--{echo $dataresult['stockstr']}-->
               					</td>
               				</tr>
                			<tr>
                				<td width="98" align="right"> 备货名称： </td>
                				<td width="399">
                					<input type="text" value="<!--{echo $dataresult['e_stockname']}-->" name="e_stockname" />
                					* </td>
               				</tr>
                			<tr>
                				<td align="right"> &nbsp;备货数量： </td>
                				<td>
                					<input type="text" onkeyup="get_e_sprice()" name="e_quantity" value="<!--{echo $dataresult['e_quantity']}-->" />
                					*
                                    <span class="tips">pcs</span> </td>
               				</tr>
                			<tr>
                				<td align="right"> 物流方式： </td>
                				<td>
                					<input type="text" name="e_express" value="<!--{echo $dataresult['e_express']}-->" />
               					</td>
               				</tr>
                			<tr>
                				<td align="right"> 单个总成本： </td>
                				<td>
                					<input type="text" class="js_input" obj="price" value="<!--{echo $dataresult['price']}-->" />
                					*
                					<!--{echo $dataresult['coincode']}-->
                					<span class="tips"></span> </td>
               				</tr>
                            <tr>
                				<td align="right"> 销售价格： </td>
                				<td>
                					<input type="text" class="js_input" obj="e_rprice" value="<!--{echo $dataresult['e_rprice']}-->" />
                					*
                					<!--{echo $dataresult['coincode']}-->
                					<span class="tips"></span> </td>
               				</tr>
                			<tr>
                				<td align="right"> 单个利润： </td>
                				<td>
                					<input type="text" name="profit_display" class="js_input" obj="e_aprice" value="<!--{echo $dataresult['e_aprice']}-->" />
                					*
                                    USD
                					<span id="profit_tips"></span>
                                </td>
               				</tr>
                			<tr>
                				<td align="right"> 销售历史： </td>
                				<td>
                					<input type="text" name="e_lastself" value="<!--{echo $dataresult['e_lastself']}-->" />
                					<span class="tips">pcs(过去两周)</span> </td>
               				</tr>
                			<tr>
                				<td align="right"> 销售预估： </td>
                				<td>
                					<input type="text" name="e_futureself" value="<!--{echo $dataresult['e_futureself']}-->" />
                					*<span class="tips">pcs(预估未来两周)</span> </td>
               				</tr>
                			<tr>
                				<td align="right"> 申请备注： </td>
                				<td>
                					<input type="text" name="comment" value="<!--{echo $dataresult['comment']}-->" />
               					</td>
               				</tr>
                            <tr>
                                <td align="right">采购预估：</td>
                              <td><input type="text" name="buytime"  class="find-T twodate" onClick="WdatePicker({minDate:'%y-%M-%d'})"  value="<!--{echo $dataresult['buytime']}-->"/>
                              *(预估采购时间)</td>
                            </tr>
               			</tbody>
               		</table>
                </td>
                <td width="500" align="left" valign="top">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="Ptable">
                        <tbody>
                            <tr>
                                <td width="202" align="left">
									<input type="text" name="product_name" value="<!--{echo $dataresult['product_name']}-->" />
                                </td>
                                <td width="272" align="left">
                                    产品名称
                                </td>
                            </tr>                            
                            <tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['e_inware']}-->" name="e_inware" />
                                </td>
                                <td width="272" align="left">                                   
                                    <span class="tips">
                                         可发库存 pcs
                                    </span>
                                </td>
                            </tr>
							<tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['e_fbainware']}-->" name="e_fbainware" />
                                </td>
                                <td width="272" align="left">                                   
                                    <span class="tips">
                                         FBA在途库存 pcs
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['e_inwareching']}-->" name="e_inwareching" />
                                </td>
                                <td width="272" align="left">
                                    已下采购单 
                                    <span class="tips">
                                        pcs
                                    </span>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['e_instocking']}-->" name="e_instocking" />
                                </td>
                                <td width="272" align="left">
                                    已下备货单
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['cost2']}-->" name="cost2" />
                                </td>
                                <td width="272" align="left">
									单个成本(<span class="tips">USD</span>)
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['e_sprice']}-->" name="e_sprice" />
                                </td>
                                <td width="272" align="left">
                                    预计总利润 (<span class="tips">USD</span>)
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['e_lastbackrate']}-->" name="e_lastbackrate" />
                                </td>
                                <td width="272" align="left">
                                    因质量问题退货率(过去一个月)
                                </td>
                            </tr>
                            <tr>
                                <td align="left">
                                    <input type="text" value="<!--{echo $dataresult['e_upc_or_ean']}-->" name="e_upc_or_ean" />
                                </td>
                                <td width="272" align="left">
                                    MOQ(<span class="tips">pcs</span>&nbsp;)
                                </td>
                            </tr>
                             <tr>
                                <td align="left">
                               <!--{echo $data['id']}-->
                                </td>
                                <td width="272" align="left"><span style="color:#000000;">供货商|账期|价格 *</span></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" height="40" align="center" valign="middle">
                    <input type="submit" id="subinput" style="background-image:url('./staticment/images/sure.gif');"
                    value="">
                    <input type="reset" id="subinput" value="" style="background-image:url('./staticment/images/reset.gif');">
                </td>
            </tr>
        </tbody>
    </table>
	<input type="hidden" name="checkid" value="<!--{echo $dataresult['id']}-->" />
	<input type="hidden" name="pid" value="<!--{echo $dataresult['pid']}-->" />
	<input type="hidden" name="price" value="<!--{echo $dataresult['price']}-->" />
	<input type="hidden" name="e_aprice" value="<!--{echo $dataresult['e_aprice']}-->" />
	<input type="hidden" name="e_rprice" value="<!--{echo $dataresult['e_rprice']}-->" />
	</form>
</body>

</html>

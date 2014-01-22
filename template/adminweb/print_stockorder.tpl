<style type="text/css">
body{ background-color:#fff}
.print{ font-size:12px; color:#06F; margin-bottom:5px; text-align:center;}
.list { font-weight:bold; border-left:none; border-bottom:1px double #06f;border-right:1px double #06f}
#supplier { width:350px; border: 2px  double #06F; padding-left:5px;}
#midcont { border: 2px double #06F; line-height:20px;}
#midcont td{border-right: 1px solid #06F;border-bottom: 1px  solid #06F; border-top:none; border-left:none}
</style>
<!--{for ($mm=1;$mm<=$pageid;$mm++)}-->
<table width="770" border="0" cellpadding="0" cellspacing="0" class="print" style="line-height:30px">
  <tr>
    <td width="25%">&nbsp;</td>
    <td width="50%" align="center"><h3>深圳市米悠文化传播有限公司</h3></td>
    <td width="25%">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="index.php?action=barcode&detail=orderidbarcode&order_id=<!--{echo $sput['order_id']}-->" alt="" /></td>
    <td align="center"><h3>采购订单</h3></td>
    <td>编号：<!--{echo $sput['order_id']}--></td>
  </tr>
  <tr>
    <td>供应商：<!--{echo $sput['provider']}--></td>
    <td align="center">日期：<!--{echo $cdate}--></td>
    <td>币种：人民币</td>
  </tr>
</table>
<table width="770" border="1" cellpadding="0" cellspacing="0" class="print" id="midcont">
  <tr>
    <th class="list" width="70" height="30">产品sku</th>
    <th class="list" width="220">产品名称</th>
    <th class="list" width="60">数量</th>
    <th class="list" width="60" title="列表的采购成本">单价</th>
    <th class="list" width="80" title="含税单价+即付运费">含税单价</th>
    <th class="list" width="80" title="列表的付款金额">价税合计</th>
    <th class="list" width="80">交货日期</th>
    <th class="list" width="100">备注(规格型号)</th>
  </tr>


<!--{for ($mn=($mm-1)*10;$mn<$mm*10;$mn++)}-->
  <tr>
    <td width="" height="50"><!--{echo $datalist[$mn]['sku']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist[$mn]['product_name']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist[$mn]['quantity']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist[$mn]['e_cost']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist[$mn]['e_iprice']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist[$mn]['e_siprice']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist[$mn]['e_recdate']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist[$mn]['comment']}-->&nbsp;</td>
  </tr>
<!--{/for}-->

  <tr>
  	<td colspan="2" style="border-right:none; text-align:left">&nbsp;付款金额（大写）：<!--{echo $big_count_sprice}--></td>
	<td style="border-right:none">总数：<!--{echo $count_nums}--></td>
   	<td  colspan="2" style="border-right:none">即付运费：<!--{echo $shipfare}--></td>
    <td colspan="3">价税合计：<!--{echo $allpay}--></td>
  </tr>
</table>
<table width="770" border="0" cellpadding="2"  cellspacing="0" class="print" style="text-align:left; line-height:16px">
  <tr>
    <td width="53%" valign="top">
    	交（提）货地址：送货至需方深圳指定地方。<br />
        交（提）货日期：____天。<br />
        结算方式及期限：验收货物后支付。<br />
        运输方式及到达站港和费用负担：由供方负责运送到深圳市内<br />
        供方对质量负责的条件和期限：保修____个月。<br />
        合理损耗及计算方法：<br />
        违约责任：<br />
        解决合同纠纷的方式：本着长期友好协作关系，互利互惠，诚<br />
        信第一，双方共同协商解决。<br />
    </td>
    <td width="47%"  valign="top">
    	交（提）货方式：验货收货<br />
        产品协议：<br />
        包装标准、包装物的供应与回收方式：按出厂标准。<br />
        验收标准、方法及提出异议期限：收货同时验货。<br />
        随机备品、配件工具数量及供应办法：随大货发送。<br />
        其他约定事项：供方必须向需方提供增值税专用发票。<br />
    </td>
  </tr>
</table>
<table width="770" border="0" cellpadding="2" cellspacing="0" class="print" style="line-height:20px;">
  <tr>
    <td align="left" valign="top" width="60%">
    <div id="supplier">
    	供方：<!--{echo $sput['provider']}--><br />
        地址：<!--{echo $sput['e_address']}--><br />
        公司盖章：<br />
        联系人：<!--{echo $sput['e_person']}--><br />
        电话：<!--{echo $sput['e_tel']}--><br />
        开户行：<!--{echo $sput['e_bankaddr']}--> &nbsp; &nbsp; 户名：<!--{echo $sput['e_bankuser']}--><br />
        帐号：<!--{echo $sput['e_bankid']}--><br />
    </div>
    </td>
    <td align="left" valign="top" width="40%">
    <div id="supplier">
    	需方：深圳市米悠文化传播有限公司<br />
        仓库地址：南山区沿山路23号胜发大厦B座102库。<br />
        公司盖章：<br />
        收件人: 刘伟波 &nbsp; &nbsp; &nbsp; 收件人电话: 13510574565<br />
        电话：0755-23942895 &nbsp; &nbsp; 传真：0755-83237467<br />
        开户行：平安银行深圳分行营业部<br />
        帐号：6012 1000 44024<br />
    </div>
    </td>
  </tr>
</table>
<table width="770" border="0" class="print">
  <tr>
    <td align="left">审核：<!--{echo $sput['muser']}--></td>
    <td>&nbsp;</td>
    <td align="left">制单：<!--{echo $sput['cuser']}--></td>
  </tr>
  <tr>
    <td align="left">白联：财务</td>
    <td>红联：采购</td>
    <td align="left">黄联：仓库</td>
  </tr>
</table>
<!--{/for}-->



<!--如果存在红单注销单-->
<!--{if (datalist_red)}-->
<!--{for ($mm_red=1;$mm_red<=$pageid_red;$mm_red++)}-->
<table width="770" border="0" cellpadding="0" cellspacing="0" class="print" style="line-height:30px">
  <tr>
    <td width="25%">&nbsp;</td>
    <td width="50%" align="center"><h3>深圳市米悠文化传播有限公司</h3></td>
    <td width="25%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center"><h3>采购订单(红单)</h3></td>
    <td>编号：<!--{echo $sput['order_id']}--></td>
  </tr>
  <tr>
    <td>供应商：<!--{echo $sput['provider']}--></td>
    <td align="center">日期：<!--{echo date('Y-m-d',time())}--></td>
    <td>币种：人民币</td>
  </tr>
</table>
<table width="770" border="1" cellpadding="0" cellspacing="0" class="print" id="midcont">
  <tr>
    <th class="list" width="70" height="30">产品sku</th>
    <th class="list" width="220">产品名称</th>
    <th class="list" width="60">数量</th>
    <th class="list" width="60">单价</th>
    <th class="list" width="80">含税单价</th>
    <th class="list" width="80">价税合计</th>
    <th class="list" width="80">交货日期</th>
    <th class="list" width="100">备注(规格型号)</th>
  </tr>


<!--{for ($mn_red=($mm_red-1)*10;$mn_red<$mm_red*10;$mn_red++)}-->
  <tr>
    <td width="" height="50"><!--{echo $datalist_red[$mn_red]['sku']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist_red[$mn_red]['product_name']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist_red[$mn_red]['quantity']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist_red[$mn_red]['price']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist_red[$mn_red]['e_iprice']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist_red[$mn_red]['e_siprice']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist_red[$mn_red]['e_recdate']}-->&nbsp;</td>
    <td width=""><!--{echo $datalist_red[$mn_red]['comment']}-->&nbsp;</td>
  </tr>
<!--{/for}-->

  <tr>
  	<td colspan="3" style="border-right:none">&nbsp;价税合计（大写）：<!--{echo $big_count_sprice_red}--></td>
   	<td colspan="2" style="border-right:none">&nbsp;合计数量：<!--{echo $count_nums_red}--></td>
   	<td colspan="3">&nbsp;价税合计：<!--{echo $count_sprice_red}--></td>
  </tr>
</table>
<table width="770" border="0" cellpadding="2"  cellspacing="0" class="print" style="text-align:left; line-height:16px">
  <tr>
    <td width="53%" valign="top">
    	交（提）货地址：送货至需方深圳指定地方。<br />
        交（提）货日期：____天。<br />
        结算方式及期限：验收货物后支付。<br />
        运输方式及到达站港和费用负担：由供方负责运送到深圳市内<br />
        供方对质量负责的条件和期限：保修____个月。<br />
        合理损耗及计算方法：<br />
        违约责任：<br />
        解决合同纠纷的方式：本着长期友好协作关系，互利互惠，诚<br />
        信第一，双方共同协商解决。<br />
    </td>
    <td width="47%"  valign="top">
    	交（提）货方式：验货收货<br />
        产品协议：<br />
        包装标准、包装物的供应与回收方式：按出厂标准。<br />
        验收标准、方法及提出异议期限：收货同时验货。<br />
        随机备品、配件工具数量及供应办法：随大货发送。<br />
        其他约定事项：供方必须向需方提供增值税专用发票。<br />
    </td>
  </tr>
</table>
<table width="770" border="0" cellpadding="2" cellspacing="0" class="print" style="line-height:20px;">
  <tr>
    <td align="left" valign="top" width="60%">
    <div id="supplier">
    	供方：<!--{echo $sput['provider']}--><br />
        地址：<!--{echo $sput['e_address']}--><br />
        公司盖章：<br />
        联系人：<!--{echo $sput['e_person']}--><br />
        电话：<!--{echo $sput['e_tel']}--><br />
        开户行：<!--{echo $sput['e_bankaddr']}--> &nbsp; &nbsp; 户名：<!--{echo $sput['e_bankuser']}--><br />
        帐号：<!--{echo $sput['e_bankid']}--><br />
    </div>
    </td>
    <td align="left" valign="top" width="40%">
    <div id="supplier">
    	需方：深圳市米悠文化传播有限公司<br />
        仓库地址：深圳市南山区后海大道19号物资大厦后面仓库101号<br />
        公司盖章：<br />
        收件人: 刘伟波 &nbsp; &nbsp; &nbsp; 收件人电话: 13510574565<br />
        电话：0755-23942895 &nbsp; &nbsp; 传真：0755-83237467<br />
        开户行：平安银行深圳分行营业部<br />
        帐号：6012 1000 44024<br />
    </div>
    </td>
  </tr>
</table>
<table width="770" border="0" class="print">
  <tr>
    <td align="left">审核：<!--{echo $sput['muser']}--></td>
    <td>&nbsp;</td>
    <td align="left">制单：<!--{echo $sput['cuser']}--></td>
  </tr>
  <tr>
    <td align="left">白联：财务</td>
    <td>红联：采购</td>
    <td align="left">黄联：仓库</td>
  </tr>
</table>
<!--{/for}-->
<!--{/if}-->
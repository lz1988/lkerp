<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/commoncheck.js?version=2"></script>
<!--{echo $jslink}-->
<style  type="text/css">
body{font-family: Arial,Helvetica,sans-serif; color:#000;}
.tips{color:#c6a8c6; font-size:12px;}
#commomform{border:#ececec solid 1px;border-bottom:none;border-right:none;font-size:12px;}
#commomform td{border-right:#ececec solid 1px;border-bottom:#ececec  solid 1px; border-left:none; border-top:none;}
#commomform input,#commomform select{width:200px;height:25px;}
#commomform input,#commomform select,textarea{border-left: 1px solid #C2C2C2;border-right: 1px solid #EAEAEA;border-top: 1px solid #C2C2C2;border-bottom:1px solid #eeeeee;}
#commomform #radio{border:none; width:20px;}
#commomform #subinput{background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px; border:none;cursor:pointer; margin:2px;}
</style>
<!--{echo $bannerstr}-->
<form method="<!--{echo $conform['method']}-->" action="<!--{echo $conform['action']}-->" <!--{echo $conform['extra']}--> >
<!--{echo $hidden_input}-->
<table width="<!--{echo $conform['width']}-->" cellpadding="3" cellspacing="0" id="commomform">
<!--{foreach ($disinputarr as $key=>$val)}-->
  <tr>
    <td align="right" width="<!--{echo $colwidth[1]}-->"> <!--{echo $val['showname']}-->： </td>
    <td width="<!--{echo $colwidth[2]}-->"><!--{echo $val['disinput']}--><!--{echo $val['extra']}--></td>
    <td width="<!--{echo $colwidth[3]}-->"><span class="tips_<!--{echo $val['showname']}-->"><!--{echo $val['showtips']}--></span>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>
<!--{/foreach}-->

  <tr>
    <td>&nbsp;</td>
    <td>
        <!--{if ($conform['submit'])}-->
        	<!--{echo $conform['submit']}-->
        <!--{else}-->
        <input  type="submit"  value="确 定"  id="subinput" >&nbsp;<input type="reset" value="重 置" id="subinput">
        <!--{/if}-->
    </td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
<div id="downdiv"></div>
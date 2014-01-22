<?php  if (!defined("IS_INITPHP")) exit("Access Denied!");  /* INITPHP Version 1.0 ,Create on 2014-01-22 16:41:43, compiled from template/adminweb/commom_input.tpl */ ?>
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/commoncheck.js?version=2"></script>
<?php echo $jslink ?>
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
<?php echo $bannerstr ?>
<form method="<?php echo $conform['method'] ?>" action="<?php echo $conform['action'] ?>" <?php echo $conform['extra'] ?> >
<?php echo $hidden_input ?>
<table width="<?php echo $conform['width'] ?>" cellpadding="3" cellspacing="0" id="commomform">
<?php foreach ($disinputarr as $key=>$val) { ?>
  <tr>
    <td align="right" width="<?php echo $colwidth[1] ?>"> <?php echo $val['showname'] ?>： </td>
    <td width="<?php echo $colwidth[2] ?>"><?php echo $val['disinput'] ?><?php echo $val['extra'] ?></td>
    <td width="<?php echo $colwidth[3] ?>"><span class="tips_<?php echo $val['showname'] ?>"><?php echo $val['showtips'] ?></span>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>
<?php } ?>

  <tr>
    <td>&nbsp;</td>
    <td>
        <?php if ($conform['submit']) { ?>
        	<?php echo $conform['submit'] ?>
        <?php } else { ?>
        <input  type="submit"  value="确 定"  id="subinput" >&nbsp;<input type="reset" value="重 置" id="subinput">
        <?php } ?>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
<div id="downdiv"></div>
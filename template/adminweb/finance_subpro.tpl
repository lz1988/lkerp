<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script language="javascript">

/*高度自适应*/
function iFrameHeight() { 
	var ifm= document.getElementById("fright_frame"); 
	var subWeb = document.frames ? document.frames["fright_frame"].document : ifm.contentDocument; 
	if(ifm != null && subWeb != null) { 
		ifm.height = subWeb.body.scrollHeight; 
	} 
} 

/*展开子类别*/
function expand(rowmenu){
	$('#child'+rowmenu).toggle('fast');
	$('#have_add'+rowmenu).toggleClass("have_expand");	
}

/*读出子科目*/
function showsons(id){
	CommomAjax('post','index.php?action=finance_subpro&detail=get_sons',{'pro_id':id},function(msg){
		$('#child'+id).html(msg);
	});
}

/*输出内容到frame窗口*/
function showcontent(cat_id){
		document.getElementById ("fright_frame").src  ="index.php?action=finance_subpro&detail=dataprolist&cat_id="+cat_id;
		window.open(document.all.fright_frame.src,'fright_frame','');	
}
</script>
<style type="text/css">
#finance_left{ width:170px; height:450px;float:left;}
#cctttt{ float:left;}
#le_in{ height:400px; overflow-y:auto; font-size:12px; color:#566984; line-height:20px;}
#intable td{font-size:12px; color:#566984; line-height:20px;}
#le_in table{ cursor:pointer;}
#cat_second {background-image:url(./staticment/images/none_noLine.gif);background-repeat:no-repeat; width:18px; height:18px;}
.have_add {background-image:url(./staticment/images/plus_noLine.gif);background-repeat:no-repeat; width:18px; height:18px;}
.have_expand{background-image:url(./staticment/images/minus_noLine.gif);}

</style>
<div  style="width:1100px">
    <div id='finance_left'>
    <fieldset><legend><font color="#566984" size="-1">科目类别</font></legend>
            <div id="le_in">
            <!--{foreach ($back_data_top as $val)}-->
                <div onclick="expand(<!--{echo $val['id']}-->);<!--{if ($val['have_son'])}-->showsons(<!--{echo $val['id']}-->);<!--{/if}-->">
                <table cellpadding="0" cellspacing="0" id='intable'>
                  <tr>
                    <td><!--{if ($val['have_son'])}-->
                        <div class="have_add" id="have_add<!--{echo $val['id']}-->"></div>
                        <!--{else}-->
                        <div  style="width:18px"></div>
                        <!--{/if}-->
                    </td>
                    <td <!--{if (empty($val['have_son']))}--> onclick=showcontent(<!--{echo $val['id']}-->) <!--{/if}--> >
                        <!--{echo $val['cat_name']}-->
                    </td>
                  </tr>
                </table>
                </div>
                <!--{if ($val['have_son'])}-->
                    <div style="display:none" id="child<!--{echo $val['id']}-->">
                        <div><table cellpadding="0" cellspacing="0" id="intable">
                            <tr><td width="10"></td><td>
                            <div  style="width:18px"></div></td><td><img src="./staticment/images/loading.gif"  border="0"/>
                            </td></tr></table>
                        </div>
                    </div>
                <!--{/if}-->
            <!--{/foreach}-->
        </div>    
    </fieldset>
    <input type="button" value="科目类别" onclick="javascript:window.open('index.php?action=finance_subcat&detail=list','_self','');"/>
    </div>
    <div style="float:left; width:10px; height:450px"></div>
    <div  style="float:left">
            <iframe src="" width="850"  scrolling="no" frameborder="0" id="fright_frame" name="fright_frame" onLoad="iFrameHeight()"></iframe>
    </div>
</div>
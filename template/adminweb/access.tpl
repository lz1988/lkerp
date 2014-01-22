<style type="text/css">
body  table	{ font-size:12px;}
fieldset	{ border:1px solid #ececec;}
legend		{ color:#bdbdbd;}
.group_id label { cursor:pointer;}
.m_name,.u_name{ cursor:pointer; padding:2px;}
.group_id,.menu_id,.right_id { padding-left:2px;}
.disabled	{ color:#bdbdbd;}
.right_list	{ cursor:pointer;}
.overbg		{ background:#86D1B4;}
.nonebg		{ background:#ffffff;}
.selectedbg	{ background:#86D1B4;}
#mod-list	{ float:left;width:210px; }
#mod-rights	{ float:left;width:500px;margin-left:50px; display:none;}
#mod-menu	{ float:left;width:160px; margin-left:50px; display:none;}
</style>

<script src="./staticment/js/jquery.js"></script>
<script src="./staticment/js/new.js"></script>
<script type="text/javascript">
$(function(){
		   
	/*选择了成员菜单变为不可选*/
	$('table[class^=ur_] input').live('click',function(){
		$('input[name=isgroup]').val('');//标记组
	});
	
	/*背景色变换*/
	$('.m_name,.u_name')
		.live('mouseover',function(){$(this).addClass('overbg');})
		.live('mouseout',function(){$(this).removeClass('overbg');});
	
	/*菜单处子菜单跟随父菜单的选择*/
	$('.mr_list input[class^=tr_]').live('click',function(){
		var tcl = $(this).val();
		var sel = $(this).attr('checked');
		var tobj= $('.td_' + tcl);
		if(sel == true){
			tobj.next().removeClass('disabled');
			tobj.attr('disabled',false);
		}else if(sel == false){
			tobj.next().addClass('disabled');//字体变色						
			tobj.attr('disabled',true);//复选框禁止件
		}
		tobj.attr('checked',sel);
	});
	
	/*如果权限已列出，那么切换用户或组的时候，权限相应更新*/
	$(':radio[name="thisid"]').live('click',function(){
		if(thisMenu) {//当菜单已被点击，权限列出
			showRights(thisMenu,'tab');
		}
	});
});


/*点击对显示菜单和菜单权限，若对象是部门同时罗列部门成员*/
function go(objId){

	var isuser	= arguments[1] ? arguments[1]:'';//是否点击成员
	if(!isuser){
		$('input[name=isgroup]').val(objId);//标记组
	}

	CommomAjax('post','index.php?action=access&detail=extends',{'objId':objId, 'menuId':thisMenu, 'isuser':isuser},function(msg){
		msg = eval("(" + msg + ")");

		/*若是点击组，成员显示*/
		if(!isuser){
			$('table[class^=ur_]').remove();
			$('.gr_'+objId).after(msg.userlist);
		}

		/*①显示菜单框与显示菜单权限*/
		$('#mod-menu').show();
		$('.menu_id').html(msg.menulist);

		$('.m_name').die('click');//先去除绑定，否则跨组多次选择成员时造成多次绑定
		$('.m_name').live('click',showRights);//绑定事件点击菜单右边出现权限
		
		/*②显示权限框*/
		$('#mod-rights').show();
	});
}

var tmsg	= '';//权限台头
var thisTit = '';//当前权限头
var thisMenu=0;//当前罗列权限的菜单ID，直接切换组或成员时需要


/*显示权限，①、点击菜单显示某菜单权限。②、若菜单已存在，切换组或成员则是更新权限*/
function showRights(){
	var rado = $(':radio[name="thisid"]:checked');
	var isgu = rado.attr('data');//是否组
	var guid = rado.val();//取得当前选中的组或用户ID

	var mid = arguments[0].length>0 ? arguments[0]:$(this).prev().val();//如果存在menu参数，则直接赋值，否则取点击的菜单ID
	var tab = arguments[1] ? arguments[1]:'';//标识是否成员切换

	tmsg	= $(this).html();
	thisMenu= mid;
	$('input[name=ismenu]').val(mid);//赋值表单
	if(tmsg){
		thisTit = tmsg;
	}
	
	/*非对象切换情况处理，即正常点击菜单，显示菜单权限*/
	var nontips = '<span style="color:red">对象无“'+thisTit+'”的查看权限；<br>若要配置该菜单下的权限，请先勾上菜单前面的复选框让其拥有菜单的查看权限！</span>';
	if(!tab) {
		delrs();//不是对象切换情况需清空权限框内容
		$('.m_name').removeClass('selectedbg');//不是对象切换情况，只是菜单切换，则之前菜单的高亮选中先去掉。
		
		/*判断前面的复选框有没选上，没选上则不查*/
		if($(':checkbox[name="menu_id[]"][value='+mid+']').attr('checked') == true){
			CommomAjax('post','index.php?action=access&detail=rights',{'mid':mid,'guid':guid,'isgu':isgu},function(msg){			
				$('.right_id').html(msg);//显示新权限
				$('#mod-rights').show();//显示权限框				
				$('#title_rs').html('功能权限-> '+thisTit);//权限框标题
			});
		}else{
			$('.right_id').html(nontips);
		}
		$('#menul_'+thisMenu).next().addClass('selectedbg');//有无权限都高亮显示选中
	}
	
	/*切换对象情况处理*/
	else{
		CommomAjax('post','index.php?action=access&detail=rights',{'mid':mid,'guid':guid,'isgu':isgu,'tab':tab},function(msg){
			if(msg == 0){
				$('.right_id').html(nontips);//无该菜单权限
			}else{
				$('.right_id').html(msg);//显示新权限
			}			
		});
	}
}

/*清空权限内容*/
function delrs(){
	$('.right_id').html('');
	$('#title_rs').html('功能权限');
}

/*回调函数*/
function callback(pm){
	var con = $('#cont');
	var css1= {'background':'#ececec url(./staticment/images/sendsuccess.gif) no-repeat right'};
	var css2 = {'background':'#ececec url(./staticment/images/onError.gif) no-repeat right'};
	
	if(pm == '1'){con.css(css1).html('配置成功');}else if(pm == '0'){con.css(css2).html('配置失败');}else if(pm == '2'){con.css(css2).html('请选择配置对象');}
	con.css('visibility','visible');
	setTimeout(function(){con.css('visibility','hidden');},'2000');
}
</script>
<div style=" width:1100px; font-size:12px; color:#000;">
    <div style="visibility: hidden; width:100%; padding-left:250px; margin-bottom:5px; color:#bdbdbd" id="tips"></div>
    <form id="formr" name="formr" method="post" action="index.php?action=access" target="hidden_frame">
    <input type="hidden" name="detail" value="edit" />
    <input type="hidden" name="mod" value="edit" />
    <input type="hidden" name="isgroup" value="" />
    <input type="hidden" name="ismenu" value="" />
    <div id='mod-list'>
        <fieldset>
        <legend> 对象 </legend>
            <div style="height:400px; width:200px; overflow-y:auto;" class="group_id">
            	<table cellpadding="0" cellspacing="0">
                <!--{foreach($group_list as $g){}-->
                    <tr>
                     <td>
                        <label  class="gr_<!--{echo $g['id']}-->">
              				<input type="radio" name="thisid" value ="<!--{echo $g['id']}-->" data="group" onclick=go(<!--{echo $g['id']}-->) ><!--{echo $g['groupname']}-->
                        </label>
                     </td>
                    </tr>
                <!--{/foreach}-->
                </table>
            </div>
        </fieldset>
    </div>
    
    
    <div id="mod-menu">
        <fieldset>
            <legend> 菜单权限 </legend>
            <div style="height:400px; width:160px; overflow-y:auto;" class="menu_id">
 				
            </div>
        </fieldset>
    </div>    

    <div id="mod-rights">
        <fieldset>
        <legend id='title_rs'>功能权限</legend>
            <div style="height:400px; width:500px; overflow-y:auto;" class="right_id">

            </div>
        </fieldset>
    </div>
	<!--{if ($mark == 1)}-->
	<div style="clear:both; padding-top:15px;">	    
    	<input type="submit" value="保存" style="background:url('./staticment/images/button_bj.gif') no-repeat; width:75px; height:22px; border:none;cursor:pointer; margin:2px;"/><span style="background:#ececec; padding:3px 20px 3px 5px; margin-left:2px; visibility:hidden" id="cont"></span>
    </div>
    <!--{/if}-->
</form>
</div>
<iframe name='hidden_frame' id="hidden_frame" style='display:none'></iframe>
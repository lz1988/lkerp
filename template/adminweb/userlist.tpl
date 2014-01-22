<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<link href="./staticment/css/datalist.css" rel="stylesheet" type="text/css" />
<script charset="utf-8" src="./staticment/js/jquery.js"></script>
<script charset="utf-8" src="./staticment/js/new.js"></script>
<script charset="utf-8" src="./staticment/js/ajaxupload.js"></script>
<script charset="utf-8" src="./staticment/js/commoncheck.js?version=2"></script>
<!--{if ($detail =='list') }-->
<script charset="utf-8" src="./staticment/js/datalist.js"></script>
<body onmousemove="MouseMoveToResize(event);" onmouseup="MouseUpToResize();" >
<!--{/if}-->

<script type="text/javascript">
/*禁止复制资料*/
//document.oncontextmenu=new Function('event.returnValue=false;');
//document.onselectstart=new Function('event.returnValue=false;');

$(function(){
/*行变换*/
$("#mytable tr:odd").addClass("oddtrbg");
$("#mytable tr:even").addClass("eventrbg");

/*图片预览*/
	var x = 10;
	var y = 80;
	$("a.tooltip").mouseover(function(e){
									  
		this.myTitle = this.title;
		this.title = "";	
		var tooltip = "<div id='tooltip'><img src='"+ this.href +"' alt='员工形象照' width=100 height=100/><\/div>"; //创建 div 元素
		
		$("body").append(tooltip);	//把它追加到文档中						 

		$("#tooltip")
			.css({
				"top": "200px",
				"left":  (e.pageX+y)  + "px",
				"position":"absolute"
			}).show("fast");	  //设置x坐标和y坐标，并且显示
    }).mouseout(function(){
		this.title = this.myTitle;	
		$("#tooltip").remove();	 //移除 
    }).mousemove(function(e){
		$("#tooltip")
			.css({
				"top": "200px",
				"left": (e.pageX+y)  + "px"
			});
	});
	/*End*/   
})

/*AJAX删除 */
function delitem(uid,mod){
	if(mod == 'del'){
		var p = window.confirm("确定删除?");
		if(!p)return;
		CommomAjax('POST','index.php?action=user_list',{'detail':'delete','uid':uid},function(msg){alert(msg);if(msg=='删除成功')window.location.reload();});
	}
	else if(mod == 'cls'){
		var p = window.confirm("确定关闭该账号？关闭后该帐号将无法登录系统进行任何操作！");
		if(!p)return;
		CommomAjax('POST','index.php?action=user_list',{'detail':'close','uid':uid},function(msg){alert(msg);if(msg=='关闭成功')window.location.reload();});
	}
        else if(mod == 'enable'){
		var p = window.confirm("确定启用该账号？启用后该帐号将恢复正常使用！");
		if(!p)return;
		CommomAjax('POST','index.php?action=user_list',{'detail':'enable','uid':uid},function(msg){alert(msg);if(msg=='启用成功')window.location.reload();});
	}
}

</script>
<style type="text/css">
/* tooltip */

#tooltip{
	position:absolute;
	border:1px solid #ccc;
	background:#333;
	padding:2px;
	display:none;
	color:#fff;
}

.tips{color:#c6a8c6; font-size:12px;}
.grey td{ color:#bdbdbd !important;}
.grey td a{ color:#bdbdbd !important;}
#commomform{border:#ececec solid 1px;border-bottom:none;border-right:none;font-size:12px;}
#commomform td{border-right:#ececec solid 1px;border-bottom:#ececec  solid 1px; border-left:none; border-top:none;}
#commomform input,#commomform select{width:200px;height:25px;}
#commomform input,#commomform select,textarea{border-left: 1px solid #C2C2C2;border-right: 1px solid #EAEAEA;border-top: 1px solid #C2C2C2;border-bottom:1px solid #eeeeee;}
#commomform #radio{border:none; width:20px;}
#commomform #subinput{background:url(./staticment/images/button_bj.gif) no-repeat; width:75px; height:22px; border:none;cursor:pointer; margin:2px;}
</style>
<!--显示列表区Start-->
<!--{if($detail == 'list'){}-->
<form name="searchform"  id="searchform" action="index.php?action=user_list&detail=list" method="post">
名字：<input type="text" name="username" id="sku" value="<!--{echo $username}-->" title="输入中文名或英文名"/>&nbsp; &nbsp; &nbsp;
部门名称：<!--{echo $shou_datagroup_html}-->&nbsp; &nbsp; &nbsp;
部门属性：<!--{echo $backdatacatg_html}-->
	 &nbsp; &nbsp; <input  type="submit"  value="搜 索"  id="subre">
</form>
<button   onClick="window.location='index.php?action=user_list&detail=new'">添加用户</button>
<table id="mytable" cellspacing="0"  width="1100">
  <tr>
    <th class='list'><div class="clearfix">中文名</div></th>
    <th class='list'><div class="clearfix">英文名</div></th>
    <th class='list'><div class="clearfix">电话</div></th>
    <th class='list' width="160"><div class="clearfix">邮箱</div></th>
    <th class='list' width="160"><div class="clearfix">MSN</div></th>
    <th class='list' width="80"><div class="clearfix">角色</div></th>
    <th class='list' width="80"><div class="clearfix">部门名称</div></th>
    <th class='list' width="80"><div class="clearfix">部门属性</div></th>    
    <th class='list' width="80"><div class="clearfix">状态</div></th>        
    <th class='list' width="80"><div class="clearfix">操作</div></th>
  </tr>
  <!--{if($datalist){}-->
  <!--{foreach($datalist as $key=>$r){}-->
  <tr  <!--{echo ($r['status'] == '已关闭')?'class="grey"':''}--> >
    <td><a href="<!--{echo $r['picurl']}-->" target="_blank" class="tooltip"><!--{echo $r['chi_name']}--></a>&nbsp;</td>
    <td><!--{echo $r['eng_name']}-->&nbsp;</td>
    <td><!--{echo $r['telphone']}-->&nbsp;</td>
    <td><!--{echo $r['email']}-->&nbsp;</td>
    <td><!--{echo $r['msn']}-->&nbsp;</td>
    <td><!--{echo $r['role']}-->&nbsp;</td>
    <td><!--{echo $r['groupname']}-->&nbsp;</td>
    <td><!--{echo $r['catg_name']}-->&nbsp;</td>
    <td><!--{echo $r['status']}-->&nbsp;</td>    
    <td><a href="index.php?action=user_list&amp;detail=edit&amp;uid=<!--{echo $r['uid']}-->" title='修改'><img src="./staticment/images/editbody.gif" border="0"></a>&nbsp;<a href="javascript:void(0);delitem(<!--{echo $r['uid']}-->,'del')" title='删除'><img src="./staticment/images/deletebody.gif" border="0"></a>&nbsp;
      <!--{if ($r['isuse']==1)}-->
        <a href="javascript:void(0);delitem(<!--{echo $r['uid']}-->,'cls')" title='关闭此帐号'>
          <img src="./staticment/images/sendfailed.gif" border="0" /></a>
      <!--{else}-->
          <a href="javascript:void(0);delitem(<!--{echo $r['uid']}-->,'enable')" title='启用此帐号'>
          <img src="./staticment/images/sendsuccess.gif" border="0" /></a>
       <!--{/if}--> 
        </td>
  </tr>
  <!--{/foreach}-->
  <!--{/if}-->
</table>
<table width="1000" >
  <tr>
  	<td><!--{echo $page_html}--></td>
  </tr>
</table>
<!--{/if}-->
<!--显示列表区End-->



<!--编辑区Start-->
<!--{if($detail =='edit') {}-->
<DIV>    
<form action="index.php?action=user_list&detail=editmod" method="post">
<input type="hidden" name="uid" value="<!--{echo $uid}-->" />
  <table  border="1" cellpadding="3" cellspacing="0"  width="630px" id="commomform">
    <tr> 
      <td align="right">用户名：</td>
      <td><input type="text" id="username" name="username" value="<!--{echo $data['username']}-->" class="check_notnull" />*</td>
      <td>&nbsp;<span class="tips">登陆用的帐号</span><span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
    </tr> 
  <tr>
    <td align="right">密码：</td>
    <td><input type="password" name="password" disabled="disabled" value="<!--{echo $data['password']}-->" class="check_notnull" />*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span><input type="button" value="重置密码" id="subinput" onClick="resetpass(<!--{echo $uid}-->)"></td>
  </tr>
  <tr>
    <td align="right">英文名：</td>
    <td><input type="text" name="eng_name"  value="<!--{echo $data['eng_name']}-->"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">中文名：</td>
    <td><input type="text" name="chi_name" value="<!--{echo $data['chi_name']}-->" class="check_notnull"  />*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>
    <tr>
    <td align="right">电话：</td>
    <td><input type="text" name="telphone" value="<!--{echo $data['telphone']}-->"/></td>
    <td>&nbsp;</td>
  </tr>
    <tr>
    <td align="right">MSN：</td>
    <td><input type="text" name="msn" value="<!--{echo $data['msn']}-->"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">邮箱：</td>
    <td><input type="text" name="email"  value="<!--{echo $data['email']}-->"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">所属部门：</td>
    <td>
    <select name="groupid" class="check_notnull" >
    <option value="">==选择部门==</option>
    <!--{foreach ($datalist as $key=>$r)}-->
    <option value="<!--{echo $r[id]}-->" <!--{if($r['id']==$data['groupid'])echo 'selected'}--> ><!--{echo $r[groupname]}--></option>
    <!--{/foreach}-->
    </select>*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>
  <tr>
    <td align="right">角色：</td>
    <td>
    <select name="roleid" class="check_notnull" >
    <option value="">==选择角色==</option>
    <!--{foreach ($roleArr as $key=>$r)}-->
    <option value="<!--{echo $key}-->" <!--{if($key==$data['roleid'])echo 'selected'}--> ><!--{echo $r}--></option>
    <!--{/foreach}-->
    </select>*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>  
  <tr>
  	<td align="right">pic：</td>
  	<td align="center"><span id="showpic">
        <img src="<!--{echo $data['picurl']}-->" border=0 width=100 height=100></span>
        <span style="cursor:pointer"><img src="./staticment/images/addNode.png" border="0" title="上传头像" id="upload_button"/></span></td>
    <td><span class="files tips">&nbsp;建议使用100*100的小图</span></td>    
  </tr>
    <tr> 
      <td>&nbsp;</td>
      <td><input  type="submit"  value="确 定"  id="subinput" id="submit">
    <input type="reset"  value="重 置" id="subinput">
    </td>
    <td>&nbsp;</td>
    </tr>
  </table>
</form>
</DIV>
<!--{unset($datalist)}-->
<!--{/if}-->
<!--编辑区End-->



<!--新增区Start-->
<!--{if($detail =='new') {}-->
<DIV>    
<form action="index.php?action=user_list&detail=newmod" method="post">
  <table  border="1" cellpadding="3" cellspacing="0"  width="630px" id="commomform">
    <tr> 
      <td align="right">用户名：</td>
      <td><input type="text" id="username" name="username" class="check_notnull"/>*</td>
      <td>&nbsp;<span class="tips">登陆用的帐号</span><span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
    </tr> 
  <tr>
    <td align="right">密码：</td>
    <td><input type="text" name="password" class="check_notnull"/>*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>
  <tr>
    <td align="right">英文名：</td>
    <td><input type="text" name="eng_name" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">中文名：</td>
    <td><input type="text" name="chi_name"  class="check_notnull" />*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>
     <tr>
    <td align="right">电话：</td>
    <td><input type="text" name="telphone" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">MSN：</td>
    <td><input type="text" name="msn" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">邮箱：</td>
    <td><input type="text" name="email" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">所属部门：</td>
    <td>
    <select name="groupid" class="check_notnull">
    <option value="">==选择部门==</option>    
    <!--{foreach ($datalist as $key=>$r)}-->
    <option value="<!--{echo $r[id]}-->" ><!--{echo $r[groupname]}--></option>
    <!--{/foreach}-->
    </select>*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>
  <tr>
    <td align="right">角色：</td>
    <td>
    <select name="roleid" class="check_notnull">
    <option value="">==选择角色==</option>    
    <!--{foreach ($roleArr as $key=>$r)}-->
    <option value="<!--{echo $key}-->" ><!--{echo $r}--></option>
    <!--{/foreach}-->
    </select>*</td>
    <td>&nbsp;<span style="background:#f8f8f8;font-size:12px;color:#f00"></span></td>
  </tr>  
  <tr>
  	<td align="right">pic：</td>
  	<td align="center"><span id="showpic">
        <img src="./data/users/face_default.png"></span>
        <span style="cursor:pointer"><img src="./staticment/images/addNode.png" border="0" title="上传头像" id="upload_button"/></span></td>
    <td><span class="files tips">&nbsp;建议使用100*100的小图</span></td>    
  </tr>
    <tr> 
        <td>&nbsp;</td>
          <td><input  type="submit"  value="确定"  id="subinput" id="submit">
        	  <input type="reset"  value="重 置" id="subinput">
        </td>
        <td>&nbsp;</td>
    </tr>
  </table>
</form>
</DIV>
<!--{unset($datalist)}-->
<!--{/if}-->
<!--新增区End-->


<!--{if ($detail =='new' || $detail == 'edit') }-->
<script type='text/javascript'>

/*重置密码*/
function resetpass(uid){
	var p = confirm('确定重置密码为 "lk123456" ?');
	if(!p) return; 
	CommomAjax('post','index.php?action=user_list&detail=resetpsw',{'uid':uid},function(msg){
		if(msg == 1){
			alert('已重置');
		}else{
			alert('重置失败')	;
		}
	});
	
}

//上传用户头像
$(document).ready(function(){
    var button = $('#upload_button'), interval;
    var fileType = "pic",fileNum = "one";
    new AjaxUpload(button,{
        action: 'index.php?action=user_list&detail=uploadfile&uid=<!--{echo $uid}-->',
        /*data:{
            'buttoninfo':button.text()
        },*/
        name: 'userfile',
        onSubmit : function(file, ext){
            if(fileType == "pic")
            {
                if (ext && /^(jpg|png|jpeg|gif)$/.test(ext)){
                    this.setData({
                        'info': '文件类型为图片'
                    });
                } else {
                    $('<li></li>').appendTo('#example .files').text('非图片类型文件，请重传');
                    return false;
                }
            }

            //button.text('文件上传中');

            if(fileNum == 'one')
                this.disable();
			/*
            interval = window.setInterval(function(){
                var text = button.text();
                if (text.length < 14){
                    button.text(text + '.');
                } else {
                    button.text('文件上传中');
                }
            }, 200);*/
        },
        onComplete: function(file, response){
			var backarr = response.split(',');
            if(backarr[0] != "success")
                alert(backarr[0]);

			//button.text('文件上传');window.clearInterval(interval);

            this.enable();

            if(backarr[0] == "success");
                $('.files').html('上传成功: '+file+'<input type="hidden" name="picurl" value='+backarr[1]+' />');
				$('#showpic').html('<img src='+backarr[1]+' width=100 height=100 border=0 >');
        }
    });
});
</script>
<!--{/if}-->
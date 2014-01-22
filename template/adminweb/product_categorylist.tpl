<link href="./staticment/css/tablelist.css" rel="stylesheet" type="text/css" />
<script src="formValidator4.0.1/jquery-1.4.4.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="formValidator4.0.1/style/validator.css"></link>
<script src="formValidator4.0.1/formValidator-4.0.1.js" type="text/javascript" charset="UTF-8"></script>
<script type="text/javascript">
$(document).ready(function(){
	$.formValidator.initConfig({formID:"form1",debug:false,submitOnce:true,
		onError:function(msg,obj,errorlist){
			$("#errorlist").empty();
			$.map(errorlist,function(msg){
				$("#errorlist").append("<li>" + msg + "</li>")
			});
			alert(msg);
		},
		submitAfterAjaxPrompt : '有数据正在异步验证，请稍等...'
	});

	$("#cat_name").formValidator({onShow:"请输入cat_name",onFocus:"至少1个长度",onCorrect:"cat_name合法"}).inputValidator({min:1,empty:{leftEmpty:false,rightEmpty:false,emptyError:"cat_name两边不能有空符号"},onError:"cat_name不能为空,请确认"});
	$("#parent_id").formValidator({onShow:"请选择类别",onFocus:"类别必须选择",onCorrect:"谢谢你的配合"}).inputValidator({min:1,onError: "你是不是忘记选择类别了!"}).defaultPassed();
});
</script>
<script type="text/javascript">
$(function(){
/*行变换*/
$("#mytable tr:odd").addClass("oddtrbg");
$("#mytable tr:even").addClass("eventrbg");
})
</script>


<!--显示列表区Start-->
<!--{if($detail =='list') {}-->
<a href="index.php?action=product_category&detail=new">添加类别&raquo;</a>
<table id="mytable" cellspacing="0" width="100%">
  <tr>
    <th class='list'>cat_id</th>
    <th class='list'>cat_name</th>
    <th class='list'>keywords</th>
    <th class='list'>cat_desc</th>
    <th class='list'>parent_id</th>
    <th class='list'>sort_order</th>
    <th class='list'>is_active</th>
  </tr>
  
  <!--{if($datalist){}-->
  <!--{foreach($datalist as $key=>$r){}-->
  <tr> 
    <td><a href="index.php?action=product_category&detail=edit&cat_id=<!--{echo $r['cat_id']}-->" title='点击修改'><!--{echo $r['cat_id']}--></a>&nbsp;</td>
    <td><!--{echo $r['cat_name']}-->&nbsp;</td>
    <td><!--{echo $r['keywords']}-->&nbsp;</td>
    <td><!--{echo $r['cat_desc']}-->&nbsp;</td>
    <td><!--{echo $r['parent_id']}-->&nbsp;</td>
    <td><!--{echo $r['sort_order']}-->&nbsp;</td>
    <td><!--{echo $r['is_active']}-->&nbsp;</td>
  </tr>
  <!--{/foreach}-->
  <!--{/if}-->  
</table>
<!--{echo $page_html}-->
<!--{/if}-->
<!--显示列表区End-->



<!--编辑区Start-->
<!--{if($detail =='edit') {}-->
<DIV>    
<form action="index.php?action=product_category&detail=editmod" method="post" name="form1" id="form1">
<input type="hidden" name="cat_id" value="<!--{echo $data['cat_id']}-->" /> 
  <table style="border:#FCF solid 1px;" border="1" cellpadding="3" cellspacing="0"  width="630px">
    <tr> 
      <td align="right">cat_name</td>
      <td><input type="text" id="cat_name" name="cat_name" style="width:120px" value="<!--{echo $data['cat_name']}-->"/></td>
      <td><div id="cat_nameTip" style="width:250px"></div></td>
    </tr>
  <tr>
    <td align="right">keywords</td>
    <td><input type="text" name="keywords" id="keywords" size="20" value="<!--{echo $data['keywords']}-->"/></td>
    <td>不填或者填默认值</td>
  </tr>  
  <tr>
    <td align="right">cat_desc</td>
    <td><textarea name="cat_desc" id="cat_desc" cols="30" rows="5"><!--{echo $data['cat_desc']}--></textarea></td>
    <td>不填或者填默认值</td>
  </tr>
    <tr> 
      <td align="right">Belong To</td>
      <td> <select name="parent_id" id="parent_id">
          <option value="">－－请选择类别－－</option>
          <option value="0" <!--{echo ($data['parent_id']==0)?'selected':'';}-->>－Root Cagetory－</option>
          <!--{foreach($categorylist as $rr){}-->
<option value="<!--{echo $rr['cat_id']}-->" <!--{echo ($data['parent_id']==$rr['cat_id'])?'selected':'';}-->><!--{echo $rr['cat_name']}--></option>
          <!--{/foreach}-->
        </select> </td>
      <td><div id="parent_idTip" style="width:250px"></div></td>
    </tr>
     <tr> 
      <td align="right">AttributeSet</td>
      <td> <select name="attribute_setid" id="attribute_setid">
          <option value="0">==选择属性集合==</option>
          <!--{foreach($attrdatalist as $attr){}-->
<option value="<!--{echo $attr['attr_set_id']}-->" <!--{echo ($attr['attr_set_id']==$data['attribute_setid'])?'selected':'';}-->>&nbsp;<!--{echo $attr['attr_set_name']}--></option>
          <!--{/foreach}-->
        </select> </td>
      <td><div id="parent_idTip" style="width:250px"></div></td>
    </tr>
    <tr>
    <tr>  
    <td align="right">sort_order</td>
    <td><input type="text" name="sort_order" id="sort_order" size="20" value="<!--{echo $data['sort_order']}-->"/></td>
    <td>不填或者填默认值</td>
  </tr>  
  <tr>
    <td align="right">is_active</td>
    <td><select name="is_active" id="is_active">
          <option value="1">Yes</option>
          <option value="0" <!--{echo ($data['is_active']==0)?' selected':''}-->>No</option>
        </select></td>
    <td></td>
  </tr>
    <tr> 
      <td colspan="3"><input type="submit" name="button" id="button" value="提交" /><input type="button" onclick="history.back(-1)"  value="返回"/>
      <div id="msTip" style="width:250px"></div></td>
    </tr>
  </table>
</form>
</DIV>  
<!--{/if}-->
<!--编辑区End-->


<!--新增区Start-->
<!--{if($detail =='new') {}-->
<DIV>    
<form action="index.php?action=product_category&detail=newmod" method="post" name="form1" id="form1">
<input type="hidden" name="cat_id" value="" /> 
  <table style="border:#FCF solid 1px;" border="1" cellpadding="3" cellspacing="0"  width="630px">
    <tr> 
      <td align="right">cat_name</td>
      <td><input type="text" id="cat_name" name="cat_name" style="width:120px" value=""/></td>
      <td><div id="cat_nameTip" style="width:250px"></div></td>
    </tr>
  <tr>
    <td align="right">keywords</td>
    <td><input type="text" name="keywords" id="keywords" size="20" value=""/></td>
    <td>不填或者填默认值</td>
  </tr>  
  <tr>
    <td align="right">cat_desc</td>
    <td><textarea name="cat_desc" id="cat_desc" cols="30" rows="5"></textarea></td>
    <td>不填或者填默认值</td>
  </tr>
    <tr> 
      <td align="right">Belong To</td>
      <td> <select name="parent_id" id="parent_id">
          <option value="">－－请选择类别－－</option>
          <option value="0">－Root Cagetory－</option>
          <!--{foreach($categorylist as $rr){}-->
			<option value="<!--{echo $rr['cat_id']}-->"><!--{echo $rr['cat_name']}--></option>
          <!--{/foreach}-->
        </select> </td>
      <td><div id="parent_idTip" style="width:250px"></div></td>
    </tr>    
    <tr> 
      <td align="right">AttributeSet</td>
      <td> <select name="attribute_setid" id="attribute_setid">
          <option value="0">==选择属性集合==</option>
          <!--{foreach($attrdatalist as $attr){}-->
			<option value="<!--{echo $attr['attr_set_id']}-->" >&nbsp;<!--{echo $attr['attr_set_name']}--></option>
          <!--{/foreach}-->
        </select> </td>
      <td><div id="parent_idTip" style="width:250px"></div></td>
   </tr>
   <tr>
    <td align="right">sort_order</td>
    <td><input type="text" name="sort_order" id="sort_order" size="20" value=""/></td>
    <td>不填或者填默认值</td>
  </tr>  
  <tr>
    <td align="right">is_active</td>
    <td><select name="is_active" id="is_active">
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select></td>
    <td></td>
  </tr>
    <tr> 
      <td colspan="3"><input type="submit" name="button" id="button" value="提交" /><input type="button" onclick="history.back(-1)"  value="返回"/>
      <div id="msTip" style="width:250px"></div></td>
    </tr>
  </table>
</form>
</DIV>  
<!--{/if}-->
<!--新增区End-->




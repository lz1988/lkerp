<SCRIPT>
function go(loc) {
window.location.href = loc;
}
</script>

<form id="formu" name="formu" method="post" action="index.php?action=user_access">
角色：
  <!--{if($groups){ $i =0;}-->
  <!--{foreach($groups as $key=>$g){$i++;}-->
<input type="radio" name="gid" value="<!--{echo $g['id']}-->" onClick="go('index.php?action=user_access&detail=edit&step=3&gid=<!--{echo $g['id']}-->');" <!--{if($gid==$g['id']){}--> checked<!--{/if}-->><!--{echo $g['groupname']}-->
  <!--{/foreach}-->
  <!--{/if}-->  
</form>

<!--{if($step>=3){}-->
<div style=" width:1100px; font-size:12px">
<form id="formr" name="formr" method="post" action="index.php?action=user_access">
<input type="hidden" name="accesssess_p" value="<!--{echo ${$cur_mode.'sess_c'}}-->" />
<input type="hidden" name="detail" value="edit" /> 
<input type="hidden" name="step" value="4" /> 
<input type="hidden" name="gid" value="<!--{echo $gid}-->" />

    <div style="float:left;width:100px; ">
        <fieldset>
        <legend style="border:#09F 1px solid; color:#09f">&nbsp;用户&nbsp;</legend>
            <div style="height:300; width:100px; overflow-y:auto;">
            <!--{foreach($all_user as $r){}-->
                <input type="radio" name="uid" value ="<!--{echo $r['uid']}-->" onClick="go('index.php?action=user_access&detail=edit&step=3&gid=<!--{echo $gid}-->&uid=<!--{echo $r[uid]}-->');" <!--{if ($uid==$r['uid'])}-->checked<!--{/if}--> ><!--{echo $r['chi_name']}--><br />
            <!--{/foreach}-->
            </div>
        </fieldset>
    </div>

    <div style="float:left;width:300px;margin-left:50px;">
        <fieldset>
        <legend>功能权限</legend>
            <div style="height:300; width:300px; overflow-y:auto;">
            <!--{foreach($all_rights as $key=>$r){}-->
                <input type="checkbox" name="right_ids[]" value ="<!--{$key}-->"  
                <!--{if (in_array($key, $cur_role_rights)) }--> checked<!--{/if}-->
                <!--{if (in_array($key, $cur_user_rights)) }--> checked<!--{/if}-->
                <!--{if (in_array($key, $cur_role_rights) && $uid) }--> checked disabled<!--{/if}--> ><!--{$r}--><br />
            <!--{/foreach}-->
            </div>
        </fieldset>
    </div>
	
    <div style="float:left;width:160px; margin-left:50px;">
        <fieldset>
            <legend>菜单</legend>
            <div style="height:300px; width:160px; overflow-y:auto;">
            <!--{foreach ($all_menu as $key=>$r)}-->
            <!--{if ($r['parent_id'] !='0')}-->
            	<!--{$pre_menu = "&nbsp; &nbsp;";}-->
            <!--{/if}-->
               <!--{echo $pre_menu;}--><input type="checkbox" name="menu_ids[]" value ="<!--{echo $r['id']}-->" <!--{if(in_array($r['id'], $cur_menu_rights)){}--> checked <!--{/if}--><!--{if ($uid)}--> disabled <!--{/if}--> ><!--{echo $r['name']}--><br />
               
            <!--{$pre_menu = "";}-->
            <!--{/foreach}-->
            </div>
        </fieldset>
    </div>
<!--{if ($mark == 1)}-->
<div style="clear:both"><input type="submit" value="提交" /></div>
<!--{/if}--> 
</form>
</div>
<!--{/if}-->
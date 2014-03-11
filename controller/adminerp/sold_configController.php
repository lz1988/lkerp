<?php
/*
 * Created on 2012-8-13
 *
 * 销售渠道，销售帐号，收付款帐号表
 *
 * By:Leon
*/
if($detail == 'main')
{
    $bannerstr	= '<br><button class="six" onclick=window.location="index.php?action=sold_config&detail=sold_way">销售渠道设置</button>';
	$bannerstr .= '<br><br><button class="six" onclick=window.location="index.php?action=sold_config&detail=sold_account">销售帐号设置</button>';
	$bannerstr .= '<br><br><button class="six" onclick=window.location="index.php?action=sold_config&detail=finance_payrec_account">收付款帐号表</button>';
	$bannerstr .= '<br><br><button class="six" onclick=window.location="index.php?action=sold_config&detail=sold_relation_conf">关系设置</button>';
    $bannerstr .= '<br><br><button class="six" onclick=window.location="index.php?action=sold_config&detail=type_account">账号类别设置</button>';
    $bannerstr .= '<br><br><button class="six" onclick=window.location="index.php?action=sold_config&detail=item">项目设置</button>';

    $temp 		= 'pub_list';
	$this->V->mark(array(title=>'销售管理'));
}

//销售渠道列表
elseif ($detail == 'sold_way')
{
    $bannerstr = '<button class="six" onclick=window.location="index.php?action=sold_config&detail=add_sold_way">新增销售渠道</button>';

    $sold_way = $this->S->dao('sold_way');
    $datalist = $sold_way->getSoldWayList();

    $displayarr = array();
	$tablewidth = '400';

    $displayarr['wayname'] = array('showname'=>'销售渠道名称','width'=>'300');
    $displayarr['both'] = array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=sold_config&detail=delete_sold_way&id={id}','url_e'=>'index.php?action=sold_config&detail=edit_sold_way&id={id}');

    $temp = 'pub_list';
    $this->V->mark(array(title=>'销售渠道列表-销售管理(main)'));
}

//销售渠道编辑
elseif ($detail == 'add_sold_way' || $detail == 'edit_sold_way')
{
    if($detail == 'add_sold_way'){
        //权限判断
        if(!$this->C->service('admin_access')->checkResRight('soldconfig_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '添加销售渠道-销售渠道列表(sold_way)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_sold_way&method=insert';
	}elseif($detail == 'edit_sold_way'){
	    if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$sold_way = $this->S->dao('sold_way');
		$data = $sold_way->D->select('wayname','id='.$id);
		$this->V->view['title'] = '编辑销售渠道-销售渠道列表(sold_way)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_sold_way&method=update';
	}

    /*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'500');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'100');

    $disinputarr = array();
    $disinputarr['id']      = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['wayname'] = array('showname'=>'渠道名称','value'=>$data['wayname']);

    $temp = 'pub_edit';
}

//保存销售渠道
elseif ($detail == 'save_sold_way')
{
    if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->ajaxmsg(0);}//权限判断

    $sold_way = $this->S->dao('sold_way');
    if($method == 'insert'){
        $sid = $sold_way->D->insert(array('wayname'=>$wayname));
	    if($sid) $this->C->success('添加成功','index.php?action=sold_config&detail=sold_way');
    } elseif($method == 'update') {
        $sid = $sold_way->D->update_by_field(array('id'=>$id),array('wayname'=>$wayname));
	    if($sid) $this->C->success('修改成功','index.php?action=sold_config&detail=sold_way');
    }
}

//删除渠道编辑
elseif ($detail == 'delete_sold_way')
{
    if(!$this->C->service('admin_access')->checkResRight('soldconfig_del')){$this->C->ajaxmsg(0);}//权限判断

    if($id){if($this->S->dao('sold_way')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

//销售帐号列表
elseif ($detail == 'sold_account')
{
    $bannerstr		= '<button class="six" onclick=window.location="index.php?action=sold_config&detail=add_sold_account">新增销售帐号</button>';
    $sold_account	= $this->S->dao('sold_account');
    $datalist		= $sold_account->getSoldAccountList();

    $displayarr = array();
	$tablewidth = '400';

    $displayarr['account_name'] = array('showname'=>'销售帐号名称','width'=>'280');
    $displayarr['account_code'] = array('showname'=>'代码','width'=>'60');
    $displayarr['both']			= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=sold_config&detail=delete_sold_account&id={id}','url_e'=>'index.php?action=sold_config&detail=edit_sold_account&id={id}');

    $temp = 'pub_list';
    $this->V->mark(array(title=>'销售帐号列表-销售管理(main)'));
}

//销售帐号编辑
elseif ($detail == 'add_sold_account' || $detail == 'edit_sold_account')
{

    if($detail == 'add_sold_account'){
        if(!$this->C->service('admin_access')->checkResRight('soldconfig_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '添加销售帐号-销售帐号列表(sold_account)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_sold_account&method=insert';
	}elseif($detail == 'edit_sold_account'){
	   if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$sold_account = $this->S->dao('sold_account');
		$data = $sold_account->D->select('account_name','id='.$id);
		$this->V->view['title'] = '编辑销售帐号-销售帐号列表(sold_account)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_sold_account&method=update';
	}

    /*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'500');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'100');

    $disinputarr = array();
    $disinputarr['id']      	 = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['account_name'] = array('showname'=>'销售帐号名称','value'=>$data['account_name']);
	$disinputarr['account_code'] = array('showname'=>'帐号代码','value'=>$data['account_code']);

    $temp = 'pub_edit';
}

//保存销售账号
elseif ($detail == 'save_sold_account')
{
    $sold_account = $this->S->dao('sold_account');
    if($method == 'insert'){
        $sid = $sold_account->D->insert(array('account_name'=>$account_name,'account_code'=>$account_code));
	    if($sid) $this->C->success('添加成功','index.php?action=sold_config&detail=sold_account');
    } elseif($method == 'update') {
        $sid = $sold_account->D->update_by_field(array('id'=>$id),array('account_name'=>$account_name,'account_code'=>$account_code));
	    if($sid) $this->C->success('修改成功','index.php?action=sold_config&detail=sold_account');
    }
}

//删除渠道编辑
elseif ($detail == 'delete_sold_account')
{
    if(!$this->C->service('admin_access')->checkResRight('soldconfig_del')){$this->C->ajaxmsg(0);}//权限判断

    if($id){if($this->S->dao('sold_account')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

//收付款帐号列表
elseif ($detail == 'finance_payrec_account')
{
    $bannerstr = '<button class="eight" onclick=window.location="index.php?action=sold_config&detail=add_finance_payrec_account">新增收付款帐号</button>';

    $finance_payrec_account = $this->S->dao('finance_payrec_account');
    $datalist = $finance_payrec_account->getFinancePayrecAccountList();

    $displayarr = array();
	$tablewidth = '400';

    $displayarr['payrec_account'] = array('showname'=>'销售帐号名称','width'=>'300');
    $displayarr['both'] = array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=sold_config&detail=delete_finance_payrec_account&id={id}','url_e'=>'index.php?action=sold_config&detail=edit_finance_payrec_account&id={id}');

    $temp = 'pub_list';
    $this->V->mark(array(title=>'收付款帐号列表-销售管理(main)'));
}

//收付款帐号编辑
elseif ($detail == 'add_finance_payrec_account' || $detail == 'edit_finance_payrec_account')
{

    if($detail == 'add_finance_payrec_account'){
        if(!$this->C->service('admin_access')->checkResRight('soldconfig_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '添加收付款帐号-收付款帐号列表(finance_payrec_account)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_finance_payrec_account&method=insert';
	}elseif($detail == 'edit_finance_payrec_account'){
	    if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$finance_payrec_account = $this->S->dao('finance_payrec_account');
		$data = $finance_payrec_account->D->select('payrec_account','id='.$id);
		$this->V->view['title'] = '编辑收付款帐号-收付款帐号列表(finance_payrec_account)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_finance_payrec_account&method=update';
	}

    /*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'500');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'100');

    $disinputarr = array();
    $disinputarr['id']      = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['payrec_account'] = array('showname'=>'帐号名称','value'=>$data['payrec_account']);

    $temp = 'pub_edit';
}

//保存收付款帐号
elseif ($detail == 'save_finance_payrec_account')
{
    if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->ajaxmsg(0);}//权限判断

    $finance_payrec_account = $this->S->dao('finance_payrec_account');
    if($method == 'insert'){
        $sid = $finance_payrec_account->D->insert(array('payrec_account'=>$payrec_account));
	    if($sid) $this->C->success('添加成功','index.php?action=sold_config&detail=finance_payrec_account');
    } elseif($method == 'update') {
        $sid = $finance_payrec_account->D->update_by_field(array('id'=>$id),array('payrec_account'=>$payrec_account));
	    if($sid) $this->C->success('修改成功','index.php?action=sold_config&detail=finance_payrec_account');
    }
}

//删除收付款帐号
elseif ($detail == 'delete_finance_payrec_account')
{
    if(!$this->C->service('admin_access')->checkResRight('soldconfig_del')){$this->C->ajaxmsg(0);}//权限判断

    if($id){if($this->S->dao('finance_payrec_account')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

//关系列表
elseif ($detail == 'sold_relation_conf')
{
    $bannerstr = '<button onclick=window.location="index.php?action=sold_config&detail=add_sold_relation_conf">新增关系</button>';

    $sold_relation_conf = $this->S->dao('sold_relation_conf');
    $datalist = $sold_relation_conf->getSoldRelationConfList();

    $displayarr = array();
	$tablewidth = '750';

    $displayarr['wayname'] = array('showname'=>'销售渠道','width'=>'250');
    $displayarr['account_name'] = array('showname'=>'销售帐号','width'=>'250');
    $displayarr['payrec_account'] = array('showname'=>'收付款帐号','width'=>'250');
    $displayarr['both'] = array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=sold_config&detail=delete_sold_relation_conf&id={id}','url_e'=>'index.php?action=sold_config&detail=edit_sold_relation_conf&id={id}');

    $temp = 'pub_list';
    $this->V->mark(array(title=>'关系列表-销售管理(main)'));
    $this->V->mark(array(title=>'关系列表-销售管理(main)'));
}

//关系编辑
elseif ($detail == 'add_sold_relation_conf' || $detail == 'edit_sold_relation_conf')
{
    $sold_relation_conf = $this->S->dao('sold_relation_conf');
    $data = $sold_relation_conf->D->select('way_id,account_id,payrec_id','id='.$id);

    //销售渠道，销售帐号，收付款帐号
    $sold_way_data = $this->S->dao('sold_way')->D->get_all('','','','id,wayname');
    $sold_account_data = $this->S->dao('sold_account')->D->get_all('','','','id,account_name');
    $finance_payrec_account_data = $this->S->dao('finance_payrec_account')->D->get_all('','','','id,payrec_account');

    $sold_way_select = "<select name='sold_way_id'>";
    $sold_way_select .= "<option value='0'>==请选择==</option>";
    foreach($sold_way_data as $key=>$val){
        $sel = $data['way_id'] == $val['id'] ? "selected='selected'":'';
        $sold_way_select .= "<option value=".$val['id']." ".$sel.">".$val['wayname']."</option>";
    }
    $sold_way_select .= "</select>";

    $sold_account_select = "<select name='sold_account_id'>";
    $sold_account_select .= "<option value='0'>==请选择==</option>";
    foreach($sold_account_data as $key=>$val){
        $sel = $data['account_id'] == $val['id'] ? "selected='selected'":'';
        $sold_account_select .= "<option value=".$val['id']." ".$sel.">".$val['account_name']."</option>";
    }
    $sold_account_select .= "</select>";

    $finance_payrec_account_select = "<select name='finance_payrec_account_id'>";
    $finance_payrec_account_select .= "<option value='0'>==请选择==</option>";
    foreach($finance_payrec_account_data as $key=>$val){
        $sel = $data['payrec_id'] == $val['id'] ? "selected='selected'":'';
        $finance_payrec_account_select .= "<option value=".$val['id']." ".$sel.">".$val['payrec_account']."</option>";
    }
    $finance_payrec_account_select .= "</select>";
    //销售渠道，销售帐号，收付款帐号

    if($detail == 'add_sold_relation_conf'){
        if(!$this->C->service('admin_access')->checkResRight('soldconfig_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '关系设置-关系列表(sold_relation_conf)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_sold_relation_conf&method=insert';
	}elseif($detail == 'edit_sold_relation_conf'){
	    if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$sold_relation_conf = $this->S->dao('sold_relation_conf');
		$data = $sold_relation_conf->D->select('way_id,account_id,payrec_id','id='.$id);
		$this->V->view['title'] = '关系设置-关系列表(sold_relation_conf)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_sold_relation_conf&method=update';
	}

    /*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'500','extra'=>'id="sold_relation_conf"');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'100');

    $jslink = '
        <script type="text/javascript">
            $(document).ready(function(){
                $("#sold_relation_conf").submit(function(){
                    if($("select[name=\"sold_way_id\"]").val() < 1){
                        alert("请选择销售渠道");
                        return false;
                    }
                    if($("select[name=\"sold_account_id\"]").val() < 1){
                        alert("请选择销售帐号");
                        return false;
                    }
                    if($("select[name=\"finance_payrec_account_id\"]").val() < 1){
                        alert("请选择收付款帐号");
                        return false;
                    }
                });
            });
        </script>
    ';

    $disinputarr = array();
    $disinputarr['id'] = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['sold_way_select'] = array('showname'=>'销售渠道','width'=>'195','datatype'=>'se','datastr'=>$sold_way_select);
    $disinputarr['sold_account_select'] = array('showname'=>'销售帐号','width'=>'195','datatype'=>'se','datastr'=>$sold_account_select);
    $disinputarr['finance_payrec_account_select'] = array('showname'=>'收付款帐号','width'=>'195','datatype'=>'se','datastr'=>$finance_payrec_account_select);

    $temp = 'pub_edit';
}

//保存关系
elseif ($detail == 'save_sold_relation_conf')
{
    if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->ajaxmsg(0);}//权限判断

    $sold_relation_conf = $this->S->dao('sold_relation_conf');
    if($method == 'insert'){
        $sid = $sold_relation_conf->D->insert(array('way_id'=>$sold_way_id,'account_id'=>$sold_account_id,'payrec_id'=>$finance_payrec_account_id));
	    if($sid) $this->C->success('添加成功','index.php?action=sold_config&detail=sold_relation_conf');
    } elseif($method == 'update') {
        $sid = $sold_relation_conf->D->update_by_field(array('id'=>$id),array('way_id'=>$sold_way_id,'account_id'=>$sold_account_id,'payrec_id'=>$finance_payrec_account_id));
	    if($sid) $this->C->success('修改成功','index.php?action=sold_config&detail=sold_relation_conf');
    }
}

//删除关系
elseif ($detail == 'delete_sold_relation_conf')
{
    if(!$this->C->service('admin_access')->checkResRight('soldconfig_del')){$this->C->ajaxmsg(0);}//权限判断

    if($id){if($this->S->dao('sold_relation_conf')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

/**
 * @title 账号类别设置
 * @author Jerry
 * @create on 2014-1-23
 */ 
elseif ($detail == 'type_account'){
    $bannerstr		= '<button class="six" onclick=window.location="index.php?action=sold_config&detail=add_type_account">新增帐号类别</button>';
    $type_account	= $this->S->dao('type_account');
    $datalist		= $type_account->getTypeAccountList();

    $displayarr = array();
	$tablewidth = '400';
    
    $displayarr['brand']        = array('showname'=>'品牌','width'=>'200');
    $displayarr['account_name'] = array('showname'=>'帐号类别名称','width'=>'280');
    $displayarr['account_code'] = array('showname'=>'代码','width'=>'60');
    $displayarr['both']			= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=sold_config&detail=delete_type_account&id={id}','url_e'=>'index.php?action=sold_config&detail=edit_type_account&id={id}');

    $temp = 'pub_list';
    $this->V->mark(array(title=>'账号类别列表-销售管理(main)'));
}

/**
 * @title 账号类别删除
 * @author Jerry
 * @create on 2014-1-23
 */ 
elseif ($detail == 'delete_type_account'){
    if($id){if($this->S->dao('type_account')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

//销售帐号编辑
elseif ($detail == 'add_type_account' || $detail == 'edit_type_account')
{

    if($detail == 'add_type_account'){
        //if(!$this->C->service('admin_access')->checkResRight('soldconfig_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '添加账号类别帐号-帐号类别列表(type_account)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_type_account&method=insert';
	}elseif($detail == 'edit_type_account'){
	   //if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$type_account = $this->S->dao('type_account');
		$data = $type_account->D->select('brand,account_name,account_code','id='.$id);
		$this->V->view['title'] = '编辑帐号类别-帐号类别列表(type_account)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_type_account&method=update';
	}

    /*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'500');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'100');
    
    $brandarr = array(''=>'-请选择-','LOFTEK'=>'LOFTEK','MelodySusie'=>'MelodySusie','Miu Color'=>'Miu Color','中性'=>'中性','前期不用帐号'=>'前期不用帐号','国内MIU'=>'国内MIU','其他收入'=>'其他收入');
    $selstr .= '<select name="brand" id="brand" class="check_notnull" >';
    foreach ($brandarr as $k=>$v){
        $selstr .='<option value="'.$k.'"';
        if($k == $data['brand'])
            $selstr .= 'selected="selected"';
        $selstr .='>'.$v.'</option>';
    }
    $selstr .= '</select>*';
    
    $disinputarr = array();
    $disinputarr['id']      	 = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['brand']        = array('showname'=>'品牌','value'=>$data['brand'],'datatype'=>'se','datastr'=>$selstr);
	$disinputarr['account_name'] = array('showname'=>'帐号类别名称','value'=>$data['account_name']);
	$disinputarr['account_code'] = array('showname'=>'帐号代码','value'=>$data['account_code']);

    $temp = 'pub_edit';
}

//保存账号类别
elseif ($detail == 'save_type_account')
{
    $type_account = $this->S->dao('type_account');
    if($method == 'insert'){
        $sid = $type_account->D->insert(array('account_name'=>$account_name,'account_code'=>$account_code,'brand'=>$brand));
	    if($sid) $this->C->success('添加成功','index.php?action=sold_config&detail=type_account');
    } elseif($method == 'update') {
        $sid = $type_account->D->update_by_field(array('id'=>$id),array('brand'=>$brand,'account_name'=>$account_name,'account_code'=>$account_code));
	    if($sid) $this->C->success('修改成功','index.php?action=sold_config&detail=type_account');
    }
}

/**
 * @title 项目设置
 * @author Jerry
 * @create on 2014-1-23
 */ 
elseif ($detail == 'item'){
    $bannerstr		= '<button class="six" onclick=window.location="index.php?action=sold_config&detail=add_item">新增项目</button>';
    $item	        = $this->S->dao('item');
    $datalist		= $item->D->get_list('','','sort asc','*');

    $displayarr = array();
	$tablewidth = '400';
    
    $displayarr['item_name']    = array('showname'=>'项目名称','width'=>'280');
    $displayarr['sort']         = array('showname'=>'排序','width'=>'60');
    $displayarr['both']			= array('showname'=>'操作','width'=>'60','ajax'=>1,'url_d'=>'index.php?action=sold_config&detail=delete_item&id={id}','url_e'=>'index.php?action=sold_config&detail=edit_item&id={id}');

    $temp = 'pub_list';
    $this->V->mark(array('title'=>'项目列表-销售管理(main)'));
}

/**
 * @title 账号类别删除
 * @author Jerry
 * @create on 2014-1-23
 */ 
elseif ($detail == 'delete_item'){
    if($id){if($this->S->dao('item')->D->delete_by_field(array('id'=>$id))) $this->C->ajaxmsg(1);}
}

//项目编辑
elseif ($detail == 'add_item' || $detail == 'edit_item')
{

    if($detail == 'add_item'){
        //if(!$this->C->service('admin_access')->checkResRight('soldconfig_add')){$this->C->sendmsg();}
		$this->V->view['title'] = '添加项目-项目列表(item)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_item&method=insert';
	}elseif($detail == 'edit_item'){
	   //if(!$this->C->service('admin_access')->checkResRight('soldconfig_edit')){$this->C->sendmsg();}
		if(empty($id))exit('没有ID!');
		$item = $this->S->dao('item');
		$data = $item->D->select('item_name,sort','id='.$id);
		$this->V->view['title'] = '编辑项目-项目列表(item)-销售管理(main)';
		$jump = 'index.php?action=sold_config&detail=save_item&method=update';
	}

    /*表单配置*/
	$conform = array('method'=>'post','action'=>$jump,'width'=>'500');
	$colwidth = array('1'=>'100','2'=>'300','3'=>'100');

    $disinputarr = array();
    $disinputarr['id']      	 = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
	$disinputarr['item_name']    = array('showname'=>'项目名称','value'=>$data['item_name'],'extra'=>'*','inextra'=>'class="check_notnull"');
    $disinputarr['sort']         = array('showname'=>'排序','value'=>$data['sort'],'extra'=>'*','inextra'=>'class="check_notnull Check_isnum_dd2"');

    $temp = 'pub_edit';
}

//保存项目
elseif ($detail == 'save_item')
{
    $item = $this->S->dao('item');
    if($method == 'insert'){
        if (!$item->D->get_count(array('item_name'=>$item_name))){
            $sid = $item->D->insert(array('item_name'=>$item_name,'sort'=>$sort));
            if($sid) $this->C->success('添加成功','index.php?action=sold_config&detail=item');
        }else{
            $this->C->success('该名称添加重复','index.php?action=sold_config&detail=item');
        }
    } elseif($method == 'update') {
        $sid = $item->D->update_by_field(array('id'=>$id),array('item_name'=>$item_name,'sort'=>$sort));
	    if($sid) $this->C->success('修改成功','index.php?action=sold_config&detail=item');
    }
}
//模板定义
if($detail !='save_sold_way' || $detail != 'delete_sold_way' || $detail != 'save_sold_account' || $detail != 'delete_sold_account' || $detail != 'save_type_account' || $detail != 'delete_type_account' || $detail != 'save_finance_payrec_account' || $detail != 'delete_finance_payrec_account' || $detail != 'save_sold_relation_conf' || $detail != 'delete_sold_relation_conf' || $detail != 'delete_item' || $detail != 'save_item')
{
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
}
?>

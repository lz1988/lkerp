<?php
/**
 * @title b2b客户管理
 * @author Jerry
 * @create on 2013-06-17
 */
 
 if ($detail == 'list') {
       $stypemu = array(
        'corpname-s-l' 	    => '公司名：',
		'contactname-s-l'	=> '&nbsp;&nbsp;联系人：',
		'fstcreate-t-t'     => '&nbsp;&nbsp;创建时间：',
    );

    $InitPHP_conf['pageval'] = 15; //分页数
    $datalist 	= $this->S->dao('b2bcorpbsl')->D->get_list($sqlstr,'','id','*');
    $displayarr = array();
    $tablewith  = '900';
    
    $displayarr['corpname']   	 = array('showname'=>'公司名', 'width'=>'100');
   	$displayarr['contactname']   = array('showname'=>'联系人', 'width'=>'100');
    $displayarr['contacttel']    = array('showname'=>'电话', 'width'=>'60');
    $displayarr['contactemail']  = array('showname'=>'邮箱', 'width'=>'60');
    $displayarr['fstcreate']     = array('showname'=>'创建时间', 'width'=>'60');

    $displayarr['both']     = array('showname'=>'操作', 'ajax'=>1, 'width'=>'60', 'url_d'=>'index.php?action=b2bcorpbsl&detail=del&id={id}', 'url_e'=>'index.php?action=b2bcorpbsl&detail=update&id={id}');
    $bannerstr 		= '<button class="six" onclick="window.location=\'index.php?action=b2bcorpbsl&detail=insert\'">新增客户</button>';
    //$bannerstrarr[] = array('url'=>'index.php?action=supplier&detail=outputname', 'value'=>'导出客户', 'class'=>'six');

    $this->V->view['title'] = 'b2b客户列表';
    $temp = 'pub_list';
 }
 /**
  * @title 修改，新增客户
  * @author Jerry
  * @create on 2013-06-17
  */ 
 elseif($detail == 'update' || $detail == 'insert'){

	if($detail == 'update'){
	    if(!$this->C->service('admin_access')->checkResRight('b2bcorpbsl_edit')){$this->C->sendmsg();}//权限判断
		if(empty($id))exit('没有ID!');
		$datalist  = $this->S->dao('b2bcorpbsl')->D->get_one_by_field(array('id'=>$id),'*');
		$this->V->view['title'] = '编辑客户-客户列表(list)';
		$jump = 'index.php?action=b2bcorpbsl&detail=updatemod';
	}elseif($detail == 'insert'){
        if(!$this->C->service('admin_access')->checkResRight('b2bcorpbsl_add')){$this->C->sendmsg();}//权限判断
		$this->V->view['title'] = '添加客户-客户列表(list)';
		$jump 		= 'index.php?action=b2bcorpbsl&detail=insertmod';
	}

	/*表单配置*/
	$conform = Array('method'=>'post','action'=>$jump,'width'=>'490');
	$colwidth = Array('1'=>'100','2'=>'220','3'=>'80');

	$disinputarr = Array();
	$disinputarr['id'] 	           = array('showname'=>'编辑ID','value'=>$id,'datatype'=>'h');
    $disinputarr['corpname'] 	   = array('showname'=>'公司名称','value'=>$datalist['corpname'],'extra'=>'*','inextra'=>'class="check_notnull"');
	$disinputarr['contactname']    = array('showname'=>'联系人','value'=>$datalist['contactname'],'extra'=>'*','inextra'=>'class="check_notnull"');
	$disinputarr['contacttel']     = array('showname'=>'电话','value'=>$datalist['contacttel']);
	$disinputarr['contactemail']   = array('showname'=>'邮箱','value'=>$datalist['contactemail'],'inextra'=>'class="check_notnull"');
	$temp = 'pub_edit';

}
/**
 * @新增客户
 * @author Jerry
 * @create on 2013-06-17
 */

elseif ($detail =='insertmod') {

	if(!$this->C->service('admin_access')->checkResRight('b2bcorpbsl_add')) $this->C->sendmsg();//权限控制
    
	$supplierid = $this->S->dao('b2bcorpbsl')->D->insert(array('corpname'=>$corpname,'contactname'=>$contactname,'contacttel'=>$contacttel,'contactemail'=>$contactemail));
	if($supplierid)$this->C->success('添加成功','index.php?action=b2bcorpbsl&detail=list');

}
/**
 * @客户删除
 * @author by Jerry
 * @create on 2013-06-17
 */

 elseif ($detail =='del') {
    if(!$this->C->service('admin_access')->checkResRight('b2bcorpbsl_del')){$this->C->ajaxmsg(0);}//权限判断

    if($id) {
        if($this->S->dao('b2bcorpbsl')->D->delete_by_field(array('id'=>$id)));
            $this->C->ajaxmsg(1);
    }

 }
/**
 * @编辑客户
 * @author Jerry
 * @create on 2013-06-17
 */
elseif ($detail == 'updatemod') {
	if(!$this->C->service('admin_access')->checkResRight('b2bcorpbsl_edit')){$this->C->sendmsg();}//权限判断
	$sid = $this->S->dao('b2bcorpbsl')->D->update_by_field(array('id'=>$id),array('corpname'=>$corpname,'contactname'=>$contactname,'contacttel'=>$contacttel,'contactemail'=>$contactemail,'lastmodify'=>date('Y-m-d H:i:s')));
	if($sid) $this->C->success('修改成功','index.php?action=b2bcorpbsl&detail=list');
}


 if($detail == 'list' || $detail == 'update' || $detail == 'insert'){
 	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
 }
?>
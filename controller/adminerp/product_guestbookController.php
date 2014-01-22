<?php

$guestbook = $this->S->dao('product_guestbook');

/*产品备注留言列表*/
if($detail == 'list'){

	$InitPHP_conf['pageval'] = 10;
	$datalist = $guestbook->D->get_list('and pid='.$pid,'','id desc');
	$pageshow = array('product_name'=>$product_name,'sku'=>$sku,'pid'=>$pid);
	if($page ==''){$floors = $InitPHP_conf['sums'];}else{$floors = $InitPHP_conf['sums']-($page-1)*$InitPHP_conf['pageval'];}
	$this->V->mark(array('title'=>'产品反馈信息','sku'=>$sku,'product_name'=>$product_name,'pid'=>$pid,'datalist'=>$datalist,'floors'=>$floors));

	$this->V->set_tpl('admintag/tag_header','F');
	$this->V->set_tpl('admintag/tag_footer','L');
	$this->V->set_tpl('adminweb/product_guestbook');

	display();
}


/*增加留言*/
elseif($detail == 'addmod'){
	sleep(1);
	date_default_timezone_set('Asia/Hong_Kong');
	$ctime = date("Y-m-d H:i:s");
	$person = $_SESSION['chi_name'].'('.$_SESSION['eng_name'].')';

	$id = $guestbook->D->insert(array('pid'=>$pid,'msg'=>$msg,person=>$person,ctime=>$ctime));
	if($id){
		echo "留言成功！";
	}
}

/*删除留言*/
elseif($detail == 'deletemsg'){

	if(!$this->C->service('admin_access')->checkResRight('r_p_delmsg')){exit('对不起，你没有删除权限！');	}
	$sid = $guestbook->D->delete_by_field(array('id'=>$id));
	if($sid){echo '删除成功';}
}
?>

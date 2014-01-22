<?php
/**
 * Created on 2012-11-15
 *
 * 模仿YII框架的生成对一个表的列表页、搜索、增删改页面。
 */

if($detail == 'c'){

	if(empty($tablename)) exit('<font size=-1>error：缺少表名参数！</font>');

	/*Dao路径*/
	$rdir 	= 'lib/dao/'.$tablename.'Dao.php';

	/*生成Dao的文本内容*/
	$w_daocont = "<?php \n";
	$w_daocont.= "/**\n * Create on ".date('Y-m-d',time())."\n"." * by ".$_SESSION['eng_name']."\n */\n\n";
	$w_daocont.= 'class '.$tablename."Dao extends D{\n\n}\n";
	$w_daocont.= "?>";

	/*执行生成Dao文件*/
	$this->C->service('global')->makefile($rdir, $w_daocont);


	/*Controller路径*/
	$rdir		= 'controller/adminerp/'.$tablename.'Controller.php';

	$tablecols	= array();//表字段
	$result 	= $this->S->dao($tablename)->D->query('desc '.$tablename);
	while($row = mysql_fetch_assoc($result)){
		$tablecols[] = $row['Field'];
	}

	$displaystr = '';
	$inputstr	= '';
	$insertstr	= 'array(';
	for($i = 0; $i<count($tablecols); $i++){
		$displaystr	.= "\t\$displayarr['".$tablecols[$i]."'] = array('showname'=>'表头".$i."','width'=>'100');\n";
		if($i != 0) {
			$inputstr.= "\t\$disinputarr['".$tablecols[$i]."'] = array('showname'=>'编辑项".$i."','value'=>\$backdata['$tablecols[$i]']);\n";//组装生成编辑页
			$insertstr.= "'$tablecols[$i]'=>$$tablecols[$i],";//组装更新数组
		}
	}
	$insertstr.=')';


	/*生成控制器的文件内容*/
	$w_concont = "<?php \n";
	$w_concont.= "/**\n* Create on ".date('Y-m-d',time())."\n"."* by ".$_SESSION['eng_name']."\n"."* @title ...\n*/\n\n";
	$w_concont.= "/*数据列表*/\n";
	$w_concont.= "if(\$detail == 'list'){\n";

	$w_concont.= "\t\$stypemu = array(\n";
	$w_concont.= "\t\t'".$tablecols[0]."-s-l'=>'搜索1：',\n";
	$w_concont.= "\t\t'".$tablecols[1]."-s-l'=>'搜索2：',\n";
	$w_concont.= "\t);\n";

	$w_concont.= "\t\$datalist = \$this->S->dao('$tablename')->D->get_list(\$sqlstr);\n";
	$w_concont.= "\t\$displayarr = array();\n";
	$w_concont.= "\t\$tablewidth = '900';\n";
	$w_concont.= $displaystr;
	$w_concont.= "\t\$displayarr['both'] = array('showname'=>'操作','width'=>'60','url_e'=>'index.php?action=".$tablename."&detail=edit&".$tablecols['0']."={".$tablecols['0']."}','url_d'=>'index.php?action=".$tablename."&detail=dele&".$tablecols['0']."={".$tablecols['0']."}','ajax'=>'1');\n\n";

	$w_concont.= "\t\$bannerstr = '<button onclick=\"window.location=\\'index.php?action=$tablename&detail=add\\'\">添加记录</button>';\n";
	$w_concont.= "\t\$bannerstr.= '<button>buttonTwo</button>';\n";
	$w_concont.= "\t\$this->V->mark(array('title'=>'列表'));\n";
	$w_concont.= "\n\t\$temp = 'pub_list';\n";
	$w_concont.= "\n}\n\n";

	$w_concont.= "/*新增或编辑页面*/\n";
	$w_concont.= "elseif(\$detail == 'add' || \$detail == 'edit'){\n";
	$w_concont.= "\tif(!\$this->C->service('admin_access')->checkResRight('rights_code...')){\$this->C->sendmsg();}//权限判断\n\n";
	$w_concont.= "\tif(\$detail == 'edit'){\n";
	$w_concont.= "\t\tif(empty($$tablecols[0]))exit('缺少标识参数！');\n";
	$w_concont.= "\t\t\$backdata = \$this->S->dao('$tablename')->D->get_one(array('$tablecols[0]'=>$$tablecols[0]),'*');\n";
	$w_concont.= "\t\t\$showtitle = '编辑';\n";
	$w_concont.= "\t\t\$modurl = 'modedit';\n";
	$w_concont.= "\t}elseif(\$detail == 'add'){\n";
	$w_concont.= "\t\t\$showtitle = '新增';\n";
	$w_concont.= "\t\t\$modurl = 'modadd';\n";
	$w_concont.= "\t}\n";
	$w_concont.= "\t\n";
	$w_concont.= "\t/*表单配置*/\n";
	$w_concont.= "\t\$conform = array('method'=>'post','action'=>'index.php?action=".$tablename."&detail='.\$modurl,'width'=>'500');\n";
	$w_concont.= "\t\$colwidth= array('1'=>'100','2'=>'300','3'=>'100');\n";
	$w_concont.= "\t\n";
	$w_concont.= "\t\$disinputarr = array();\n";
	$w_concont.= "\t\$disinputarr['$tablecols[0]'] = array('showname'=>'编辑ID','value'=>$$tablecols[0],'datatype'=>'h');\n";
	$w_concont.= $inputstr;
	$w_concont.= "\n\t\$this->V->view['title'] = \$showtitle.'-列表(list)';\n";
	$w_concont.= "\t\$temp = 'pub_edit';\n";
	$w_concont.= "}\n\n";
	$w_concont.= "/*保存添加*/\n";
	$w_concont.= "elseif(\$detail == 'modadd'){\n";
	$w_concont.= "\t\$sid = \$this->S->dao('$tablename')->D->insert(".$insertstr.");\n";
	$w_concont.= "\tif(\$sid){\n \t\t\$this->C->success('保存成功','index.php?action=".$tablename."&detail=list');\n\t}else{\n\t\t\$this->C->success('保存失败','index.php?action=".$tablename."&detail=add');\n\t}";
	$w_concont.= "\n}\n\n";
	$w_concont.= "/*保存编辑*/\n";
	$w_concont.= "elseif(\$detail == 'modedit'){\n";
	$w_concont.= "\t\$sid = \$this->S->dao('$tablename')->D->update(array('$tablecols[0]'=>$$tablecols[0]),".$insertstr.");\n";
	$w_concont.= "\tif(\$sid){\n \t\t\$this->C->success('保存成功','index.php?action=".$tablename."&detail=list');\n\t}else{\n\t\t\$this->C->success('保存失败','index.php?action=".$tablename."&detail=edit&$tablecols[0]='.$$tablecols[0]);\n\t}";
	$w_concont.= "\n}\n\n";


	$w_concont.= "/*AJAX删除*/\n";
	$w_concont.= "elseif(\$detail == 'dele'){\n";
	$w_concont.= "\t/*权限判断*/\n";
	$w_concont.= "\tif(!\$this->C->service('admin_access')->checkResRight('rights_code...')) \$this->C->ajaxmsg(0);\n";
	$w_concont.= "\tif(\$this->S->dao('$tablename')->D->delete(array('$tablecols[0]'=>$$tablecols[0]))) {\$this->C->ajaxmsg(1);}else{\$this->C->ajaxmsg('删除失败！');}\n";
	$w_concont.= "}\n\n";

	$w_concont.= "\n/*头尾模板包含*/\n";
	$w_concont.= "if(\$detail == 'list' || \$detail == 'edit' || \$detail == 'add' ){\n";
	$w_concont.= "\t\$this->V->set_tpl('admintag/tag_header','F');\n";
	$w_concont.= "\t\$this->V->set_tpl('admintag/tag_footer','L');\n";
	$w_concont.= "}\n";
	$w_concont.= "?>";

	/*执行生成控制器文件*/
	$this->C->service('global')->makefile($rdir, $w_concont);

}
?>

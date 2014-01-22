<?php
/**
 * Created on 2012-8-29
 * 查看系统更新日志
 * by august
 */

if($detail =='list'){

    function get_log_content($dir,$page){
        $filecount = count(scandir($dir))-2;
        $file_arr = scandir($dir);

        //删除 '.', '..' 赋值给新数组
        $filearr = array();
        foreach($file_arr as $val){
            if($val != '.' && $val != '..'){
                $filearr[] = $val;
            }
        }
        $filearr = array_reverse($filearr);
        //读取指定文件内容
        $handle=opendir($dir);
        $page = $page?intval($page):$filecount;
        $i = $filecount;
        while(($file=readdir($handle)) !== FALSE){
            if($file != '.' && $file != '..'){
                if($i==$page){
                    $file = fopen($dir."/".$file,"r");
                    $content = '';
                    while(!feof($file)){
                        $content .= fgets($file,4096)."<br/>";
                    }
                    return $content;
                    fclose($file);
                    break;
                }
                $i--;
            }
        }
    }

    $dir = "data/systemlog";//目录文件
    $filecount = count(scandir($dir))-2; //文件总数
    $page = empty($page)?'1':$page;

    $page_html = $this->load('pager','l')->pager($filecount,1,"index.php?action=showtxtlog&detail=list");
    $this->V->assign('page_html',$page_html);
    $content = get_log_content($dir,$page);
    $this->V->mark(array('content'=>$content));
    $this->V->set_tpl('adminweb/systemlog');
    display();
}
?>

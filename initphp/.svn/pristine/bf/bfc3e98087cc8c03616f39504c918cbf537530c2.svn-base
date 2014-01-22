<?php
 /**
  * 说明，该类适用于小型的网站的数据库备份，内置MYSQL连接，只需要简单配置数据连接
  * 及存贮备份的位置即可。
  * 类实列化并且连接数据库以后可执行以下操作
 * get_db_table($database)             取得所有数据表
 * export_sql($table,$subsection=0))   生成sql文件,注意生成sql文件只保存到服务器目录，不提供下载
  * import_sql($dir)                    恢复数据只导入服务器目录下的sql文件
  * 该类制作简单，可任意传播，如何您对该类有什么提议，请发送邮件给小虾
  * @author 赵红健[游天小虾]
 * email:328742379@qq.com
 * qq交流群：69574955 聚义堂-网页制作交
 */

class dbbackupInit {
    //public   $data_dir    = "class/";   //备份文件存放的路径
    public   $transfer     ="";         //临时存放sql[切勿不要对该属性赋值，否则会生成错误的sql语句

/**
 *数据库连接
21. *@param string $host 数据库主机名
22. *@param string $user 用户名
23. *@param string $pwd  密码
24. *@param string $db   选择数据库名
25. *@param string $charset 编码方式
26. */
    function connect_db($host,$user,$pwd,$db,$charset='gbk'){
        if(!$conn = mysql_connect($host,$user,$pwd)){
            return false;
        }
        mysql_select_db($db);
        mysql_query("set names $charset");
        return true;
    }

/**
 * 生成sql语句
38. * @param   $table     要备份的表
39. * @return  $tabledump 生成的sql语句
40. */
    public function set_sql($table,$subsection=0,&$tableDom=''){
        $tableDom .= "DROP TABLE IF EXISTS $table;\n";
        $createtable = mysql_query("SHOW CREATE TABLE $table");
        $create = mysql_fetch_row($createtable);
        $create[1] = str_replace("\n","",$create[1]);
        $create[1] = str_replace("/t","",$create[1]);

        $tableDom  .= $create[1].";\n";

        $rows = mysql_query("SELECT * FROM $table");
        $numfields = mysql_num_fields($rows);
        $numrows = mysql_num_rows($rows);
        $n = 1;
        $sqlArry = array();
        while ($row = mysql_fetch_row($rows)){
           $comma = "";
           $tableDom  .= "INSERT INTO $table VALUES(";
           for($i = 0; $i < $numfields; $i++)
           {
                $tableDom  .= $comma."'".mysql_escape_string($row[$i])."'";
                $comma = ",";
           }
          $tableDom  .= ");\n";
           if($subsection != 0 && strlen($this->transfer )>=$subsection*1000){
                $sqlArry[$n]= $tableDom;
                $tableDom = ''; $n++;
           }
        }
        return $sqlArry;
   }

/**
73. *列表数据库中的表
74. *@param  database $database 要操作的数据库名
75. *@return array    $dbArray  所列表的数据库表
76. */
    public function get_db_table($database){
        $result = mysql_list_tables($database);
        while($tmpArry = mysql_fetch_row($result)){
            $dbArry[]  = $tmpArry[0];
        }
        return $dbArry;
    }

/**
86. *验证目录是否有效
87. *@param diretory $dir
88. *@return booln
89. */
    function check_write_dir($dir){
        if(!is_dir($dir)) {@mkdir($dir, 0777);}else{chmod($dir,0777);}
        //if(is_dir($dir)){
            //if($link = opendir($dir)){
                //$fileArry = scandir($dir);
                //for($i=0;$i<count($fileArry);$i++){
                //    if($fileArry[$i]!='.' || $fileArry != '..'){
                //        @unlink($dir.$fileArry[$i]);
                //    }
                //}
            //}
        //}
        return true;
    }
/**
105. *将数据写入到文件中
106. *@param file $fileName 文件名
107. *@param string $str   要写入的信息
108. *@return booln 写入成功则返回true,否则false
109. */
    private function write_sql($fileName,$str){
        $re= true;
        if(!@$fp=fopen($fileName,"w+")) {$re=false; echo iconv('UTF-8','GBK//IGNORE',"在打开文件时遇到错误，备份失败!");}
        if(!@fwrite($fp,$str)) {$re=false; echo iconv('UTF-8','GBK//IGNORE',"在写入信息时遇到错误，备份失败!");}
        if(!@fclose($fp)) {$re=false; echo iconv('UTF-8','GBK//IGNORE',"在关闭文件 时遇到错误，备份失败!");}
        return $re;
    }

/**
119. *生成sql文件
120. *@param string $sql sql    语句
121. *@param number $subsection 分卷大小，以KB为单位，为0表示不分卷
122. */
     public function export_sql($table,$data_dir,$subsection=0){
        if(!$this->check_write_dir($data_dir)){echo '您没有权限操作目录,备份失败';return false;}
        if($subsection == 0){
            if(!is_array($table)){
                $this->set_sql($table,0,$this->transfer);
            }else{
                for($i=0;$i<count($table);$i++){
                    $this->set_sql($table[$i],0,$this->transfer);
                }
            }
            $fileName = $data_dir.date("Y_m_d_His",time()).'_payall.gz';
            if(!$this->write_sql($fileName,$this->transfer)){return false;}
        }else{
            if(!is_array($table)){
                $sqlArry = $this->set_sql($table,$subsection,$this->transfer);
                $sqlArry[] = $this->transfer;
            }else{
                $sqlArry = array();
                for($i=0;$i<count($table);$i++){
                    $tmpArry = $this->set_sql($table[$i],$subsection,$this->transfer);
                    $sqlArry = array_merge($sqlArry,$tmpArry);
                }
                $sqlArry[] = $this->transfer;
            }
            for($i=0;$i<count($sqlArry);$i++){
                $fileName = $data_dir.date("Y_m_d_His",time()).'_part'.$i.'.sql';
                if(!$this->write_sql($fileName,$sqlArry[$i])){return false;}
            }
        }
        return true;
    }
/**
155. *载入sql文件
156. *@param diretory $dir
157. *@return booln
158. *注意:请不在目录下面存放其它文件，或者目录
159. *以节省恢复时间
160. */
    public function import_sql($dir){
        if($link = opendir($dir)){
            $fileArry = scandir($dir);
             $pattern = "_part[0-9]+.sql$|_all.sql$";
            for($i=0;$i<count($fileArry);$i++){
                if(eregi($pattern,$fileArry[$i])){
                    $sqls=file($dir.$fileArry[$i]);
                    foreach($sqls as $sql){
                        str_replace("/r","",$sql);
                        str_replace("\n","",$sql);
                        if(!mysql_query(trim($sql))) return false;
                    }
                }
            }
            return true;
        }
    }

}

//$d = new data();

//连接数据库
//if(!$d->connect_db('localhost','root','','web','gbk')){
//  echo '数据库连接失败';
//}

//查找数据库内所有数据表
//$tableArry = $d->get_db_table('web');
//print_r($tableArry);

//备份并生成sql文件
//if(!$d->export_sql($tableArry)){
//  echo '备份失败';
//}else{
//  echo '备份成功';
//}

//恢复导入sql文件夹
//if($d->import_sql($d->data_dir)){
//  echo '恢复成功';
//}else{
//  echo '恢复失败';
//}
?>
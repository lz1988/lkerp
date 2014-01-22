<?php
/*
 * 常用的基础函数，从面向过程移植
 */
 class basefunsInit {

		// #######====== array warning suppress ======######
		public function getary(&$ary, $field, $notfound = false){
			if(isset($ary[$field]))
				return $ary[$field];
			else
				return $notfound;
		}

		// #######====== session wrapper ======#######
		public function setsession($varname, $value){
			$_SESSION[$varname] = $value;
			return $value;
		}

		public function getsession($varname, $notfound = false){
			return $this->getsessionwithchk($varname, $notfound);
			//return $_SESSION[$varname];
		}

		public function getsessionwithchk($varname, $notfound = false){
			if( isset($_SESSION[$varname]) )
				return $_SESSION[$varname];
			else
				return $notfound;
		}

		public function unsetsession($varname){
			if(isset($_SESSION[$varname]))
				unset($_SESSION[$varname]);
		}

		// #######====== http wrapper ======#######
		function getpost($key, $na = false){
			if(isset($_POST[$key]))
				return ($_POST[$key]);
			else
				return $na;
		}
		function getget($key, $na = false){
			if(isset($_GET[$key]))
				return ($_GET[$key]);
			else
				return $na;
		}
		function getrequest($key, $na = false){
			if(isset($_REQUEST[$key]))
				return ($_REQUEST[$key]);
			else
				return $na;
		}
		// #######====== aspx functions ======#######
		function ispostback(){
			return (count($_POST) > 0);
		}

		// #######====== http request vars ======#######
		function redirect($newurl){
			header("location: " . $newurl); /* redirect browser */
		}

		// #######====== database function ======#######
		function GetDBLink($dbserver, $db, $dbuser, $pass){
			$sqlcon = mysql_pconnect($dbserver, $dbuser, $pass);
			$sqldb = mysql_select_db($db, $sqlcon);
			return $sqlcon;
		}

		function releasedbconn($DBLink){
			// to do - close $DBLink
		}

		function nowildcard($str){
			return str_replace (array("*", "%", "?", "_"), "", $str);
		}

		function sqlstr($str){
			return "'" . mysql_real_escape_string($str) . "'";
		}

		function sqltime($y, $m, $d, $h, $mi, $s){
			return "'$y-$m-$d $h:$mi:$s'";
		}

		function sqldate($y, $m, $d){
			return "'$y-$m-$d 0:0:0'";
		}

		function sqltimets($ts){
			return date("'Y-m-d H:i:s'", $ts);
		}

		function sqldatets($ts){
			return date("'Y-m-d H:i:s'", $ts);
		}

		function today(){
			return date('Y-m-d 0:0:0');
		}

		function now(){
			return date('Y-m-d H:i:s');
		}

		function sqlnow(){
			return "now()";
		}

		function sqltoday(){
			return "'".today()."'";
		}

		function sqlint($i){
			return (int)$i;
		}

		function sqlfloat($f){
			return (float)$f;
		}

		function insert_data($sqlcmd, $DBLink){
			$ret = 0;
			if(edit_data($sqlcmd, $DBLink))
				$ret = last_insert_id($DBLink);
			return $ret;
		}

		function edit_data($sqlcmd, $DBLink){ // require a connection , and execute a update/insert sql
			/*
			$sqltrim = substr($sqlcmd, 0,245);
			mysql_query('insert into _sqllog (stat, exens, exetime) values ('
				.sqlstr($sqltrim).', '.sqlint(0).', '.sqlnow().');', $DBLink);
			*/
			return create_rd($sqlcmd, $DBLink);
			//	return mysql_query($sqlcmd, $DBLink);
		}


		function create_rd($sqlcmd, $DBLink){
			global $_mysql_last_insert_id ;
			$tstart = microtime_float();
			mysql_query("SET NAMES 'utf8'", $DBLink);
			$result = mysql_query($sqlcmd, $DBLink);
			$tend = microtime_float();
			if(!$result){
				$_mysql_last_insert_id[$DBLink] = 0;
				error_log('PHP SQL '.mysql_error().' - '.$sqlcmd);
				echo('<font color=FF0033 size=2><br>'.mysql_error($DBLink).'- (conn='.$DBLink.')<br>'.htmlspecialchars($sqlcmd).'</font><hr>');
			}else{
				$_mysql_last_insert_id[$DBLink] = mysql_insert_id($DBLink);
			}

			$exetime = ceil(($tend - $tstart) * 1000);
			if(($exetime) > 1000){
				$sqltrim = substr($sqlcmd, 0, 245);
				mysql_query('insert into _sqllog (stat, exens, exetime) values ('.sqlstr($sqltrim).', '.sqlint($exetime).', '.sqlnow().');', $DBLink);
			}

			return $result;
		}

		function last_insert_id($DBLink){
			global $_mysql_last_insert_id;
			return 0 + getary($_mysql_last_insert_id, $DBLink);
		}

		function fetch_1row($sql, $DBLink){
			$ret = false;
			if($rst = create_rd($sql, $DBLink)){
				$ret = mysql_fetch_assoc($rst);
				mysql_free_result($rst);
			}
			return $ret;
		}

		function fetch_1($sql, $DBLink){
			$ret = false;
			if($rst = create_rd($sql, $DBLink)){
				if($r = mysql_fetch_row($rst)){
					$ret = $r[0];
				}
				mysql_free_result($rst);
			}
			return $ret;
		}

		function closedbconn(&$DBLink){
			if($DBLink){
				mysql_close($DBLink);
			}
			$DBLink = false;
		}

		// ====== debug functions ======

		function dump_r($key, $ary, $ident=0){
			$retset = "";
			for( $j = 0; $j < $ident; $j++) $retset .= "_";
			$retset .= "$key => ";

			if(is_array($ary)){
				$retset .= "[<br>\n";
				$keyary =  array_keys($ary);
				sort($keyary);
				for ($i = 0; $i < count($keyary); $i++)
					$retset = $retset . dump_r($keyary[$i],  $ary[$keyary[$i]], $ident + 2);
				for( $j = 0; $j < $ident; $j++) $retset .= "_";
				$retset .= "]<br>\n";
			}else{
				$retset .=  "'". $ary . "'<br>\n" ;
			}
			return $retset;
		}

		function microtime_float(){
		    list($usec, $sec) = explode(" ", microtime());
		    return ((float)$usec + (float)$sec);
		}

		function pnum($n){
			return number_format($n, 0, ',', ' ');
		}

		function rndkey(){
			return substr(mt_rand(0, 99999999).'_'. str_replace(' ', '', microtime()), 0, 32);
		}

		function setsession_($varname, $value){
			echo("// <font size=2 color=33AA00> setsession('".$varname."', '".$value."');<hr></font>\n");
			return setsession($varname, $value);
		}

		function fetch_1row_($s,$c){
			echo('// <font size=2 color=33AA00> '.htmlspecialchars($s)."<hr></font>\n");
			if($ret = fetch_1row($s, $c))
			return  $ret;
		}

		function fetch_1_($s,$c){
			echo('// <font size=2 color=33AA00> '.htmlspecialchars($s)."<hr></font>\n");
			return  $ret = fetch_1($s, $c);
		}

		function insert_data_($sqlcmd, $DBLink){
			$ret = 0;
			if(edit_data_($sqlcmd, $DBLink))
				$ret = last_insert_id($DBLink);
			return $ret;
		}

		function edit_data_($s, $c){
			if($ret = edit_data($s, $c))
				echo('// <font size=2 color=33AA00>'.htmlspecialchars($s)."<hr></font>\n");
			return  $ret;
		}

		function create_rd_($s,$c){
			if($ret =create_rd($s, $c))
				echo('// <font size=2 color=33AA00> '.htmlspecialchars($s)."<hr></font>\n");
			return  $ret;
		}

		function redirect_($s){
			echo('// <font size=4 color="#00CC00"> You should be redirected to :<a href="'.
				htmlspecialchars($s).'">'.htmlspecialchars($s)."</a></font>\n");
		}

		function std_class_object_to_array($stdclassobject){
			$_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;

			foreach ($_array as $key => $value) {
				$value = (is_array($value) || is_object($value)) ? std_class_object_to_array($value) : $value;
				$array[$key] = $value;
			}

			return $array;
		}

		function tmpGetJsonStr($rkey){
			$ret = '';
			if($rkey!='false'){
				$rk = json_decode($rkey);
				foreach($rk as $k){
					$ret .= '* '.$k.'<br>';
				}
			}

			return $ret;
		}

		function tmpGetJsonStrP($rkey, $name, $suf){
			$ret = '';
			if($rkey!='false'){
				$rk = json_decode($rkey);
				foreach($rk as $k){
					//$ret .= '<tr id="tr'.$suf.'"><td id="td'.$suf.'"><input type="input" name="'.$name.'[]" size="30" value="'.$k.'"><input type="button" value="Delete" onclick="deleteRow'.$suf.'(this)"></td></tr>';
					$ret .= '<tr><td style="border:none"><input type="input" name='.$name.'[] size="30" value="'.$k.'" > <input type="button" value="" onclick=deleteRow'.$suf.'(this); title="删除一行" style=\'background:url("./staticment/images/delete_one_row.gif");width:20px;height:20px;background-position:-4px -4px;cursor:pointer;border:none\'></td></tr>';
				}
			}
			return $ret;
		}

		/*判断是否处于同一项目目录下，防止session跨域*/
		function check_local(){

		$tag_erp_projectURL = $_SERVER["REQUEST_URI"];
		$tag_erp_projectURL =  substr($tag_erp_projectURL,1,strlen($tag_erp_projectURL));
		$tag_erp_projectURL =  substr($tag_erp_projectURL,0,strpos($tag_erp_projectURL,'/'));
		return $tag_erp_projectURL;
		}
        
        /*
         *@提示信息 
         *@param msg 提示信息
         *@param url 跳转的链接 
         *@author by Jerry
         *@create on 2012-11-22
         */ 
        function Js_msg($msg,$url=-1){
             
    		if($url==-1){
    			echo "<script language='javascript'>alert('$msg');history.back(-1);</script>";exit;
    		}
    		else {
    			echo "<script language='javascript'>alert('$msg');location.href='$url';</script>";
    		}
		}

		/*获取用户IP*/
		function getIp()
		{
			static $realip = NULL;

			if ($realip !== NULL)
			{
				return $realip;
			}

			if (isset($_SERVER))
			{
				if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				{
					$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

					/* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
					foreach ($arr AS $ip)
					{
						$ip = trim($ip);

						if ($ip != 'unknown')
						{
							$realip = $ip;

							break;
						}
					}
				}
				elseif (isset($_SERVER['HTTP_CLIENT_IP']))
				{
					$realip = $_SERVER['HTTP_CLIENT_IP'];
				}
				else
				{
					if (isset($_SERVER['REMOTE_ADDR']))
					{
						$realip = $_SERVER['REMOTE_ADDR'];
					}
					else
					{
						$realip = '0.0.0.0';
					}
				}
			}
			else
			{
				if (getenv('HTTP_X_FORWARDED_FOR'))
				{
					$realip = getenv('HTTP_X_FORWARDED_FOR');
				}
				elseif (getenv('HTTP_CLIENT_IP'))
				{
					$realip = getenv('HTTP_CLIENT_IP');
				}
				else
				{
					$realip = getenv('REMOTE_ADDR');
				}
			}

			preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
			$realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

			return $realip;
		}
 }
?>

<?php
/*
 * Created on 2012-9-21
 *
 * To change the template for this generated file go to
 * 对数组操作的方法
 *
 */
 class arrayInit{

	/*二维数组排序方法1--相对2效率高*/
	function array_sort_each($arr,$keys,$type='asc'){
		$keysvalue = $new_array = array();
		foreach ($arr as $k=>$v){
			$keysvalue[$k] = $v[$keys];
		}
		if($type == 'asc'){
			asort($keysvalue);
		}else{
			arsort($keysvalue);
		}

		reset($keysvalue);

		foreach ($keysvalue as $k=>$v){
			$new_array[$k] = $arr[$k];
		}
		return $new_array;
	}

	/*二维数组排序方法2*/
	function array_sort_for($a,$sort,$d='') {
	    $num=count($a);
	    if(!$d){
	        for($i=0;$i<$num;$i++){
	            for($j=0;$j<$num-1;$j++){
	                if($a[$j][$sort] > $a[$j+1][$sort]){
	                    foreach ($a[$j] as $key=>$temp){
	                        $t=$a[$j+1][$key];
	                        $a[$j+1][$key]=$a[$j][$key];
	                        $a[$j][$key]=$t;
	                    }
	                }
	            }
	        }
	    }
	    else{
	        for($i=0;$i<$num;$i++){
	            for($j=0;$j<$num-1;$j++){
	                if($a[$j][$sort] < $a[$j+1][$sort]){
	                    foreach ($a[$j] as $key=>$temp){
	                        $t=$a[$j+1][$key];
	                        $a[$j+1][$key]=$a[$j][$key];
	                        $a[$j][$key]=$t;
	                    }
	                }
	            }
	        }
	    }
	    return $a;

	}

	public function sortByCol($array, $keyname, $dir = SORT_ASC){
        return self::sortByMultiCols($array, array($keyname => $dir));
    }

    /**
     * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
     *
     * 用法：
     * @code php
     * $rows = Helper_Array::sortByMultiCols($rows, array(
     *     'parent' => SORT_ASC,
     *     'name' => SORT_DESC,
     * ));
     * @endcode
     *
     * @param array $rowset 要排序的数组
     * @param array $args 排序的键
     *
     * @return array 排序后的数组
     */
    static function sortByMultiCols($rowset, $args)
    {
        $sortArray = array();
        $sortRule = '';
        foreach ($args as $sortField => $sortDir)
        {
            foreach ($rowset as $offset => $row)
            {
                $sortArray[$sortField][$offset] = $row[$sortField];
            }
            $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
        }
        if (empty($sortArray) || empty($sortRule)) { return $rowset; }
        eval('array_multisort(' . $sortRule . '$rowset);');
        return $rowset;
    }

 }

?>

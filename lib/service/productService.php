<?php
/*
 * Created on 2013-3-1
 *
 * To change the template for this generated file go to
 * 产品相关的服务层方法
 */

 class productService extends S{


	/*获取质检图片*/
	public function get_qualitycheck($val){

		$qualitycheckdir = './data/images/qualitycheck';
		$qualityhtml	 = '<select name="qualitycheck" onchange="showcheckpic(this.value)"><option value="">=请选择=</option>';
		$fileArr		 = scandir($qualitycheckdir);
		for($i=0; $i<count($fileArr); $i++){
			if($fileArr[$i] != '.' && $fileArr[$i] != '..'){
				$selected	 = ($fileArr[$i] == $val)?'selected':'';
				$qualityhtml.='<option value="'.$fileArr[$i].'" '.$selected.' >'.substr($fileArr[$i],0,strpos($fileArr[$i],'.')).'</option>';
			}
		}

		$qualityhtml	.= '</select><div id="qualitycheck">';
		$qualityhtml	.= ($val?'<a title="点击查看原图" target="_blank" href="'.$qualitycheckdir.'/'.$val.'"><img src="./data/images/qualitycheck/'.$val.'"   style="border:1px solid #ececec" width="117"/></a>':'').'</div>';


		return $qualityhtml;
	}
        
        
        /***
         * 获取质检图片
         */
        public function get_qualitycheck_list(){
            $qualitycheckdir = './data/images/qualitycheck';
            $fileArr = scandir($qualitycheckdir);
            $qualitylist = array();
            if($fileArr){
                for($i=0; $i<count($fileArr); $i++){
                    if($fileArr[$i] != '.' && $fileArr[$i] != '..'){
                        $qualitylist [substr($fileArr[$i],0,strpos($fileArr[$i],'.'))] =$fileArr[$i];   
                    }
                }
                return $qualitylist;
            }
            return false;
        }
 }
?>

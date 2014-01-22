<?php
/*
 * Created on 2012-3-23
 * @title 存放公共的服务函数
 */
 class globalService extends S{

    /*
    *取得最大的实体编码,并+1
    */
    public function get_max_esseid($type){
        $esse  = $this->S->dao('esse');
        $maxid = $esse->D->select('max(esseid) as max','type='.$type);
        return  $maxid['max']+1;
    }

    /*
    *取出实体类别，生成HTM
    */
    public function get_essecat_html($showname,$name,$vval,$depend,$dval){
        $wdata = $this->S->dao('esse_cat')->D->get_allstr('','','cat_code desc');
        $wback = '';
        $wback = '<select name='.$showname.'>';
        $wback.= '<option value=>=核算项目类别=</option>';
        foreach ($wdata as $val){
            if($depend&&$dval){$selected = ($val[$depend]==$dval)?'selected':'';}
            $wback.= '<option value='.$val[$vval].' '.$selected.'>'.$val[$name].'</option>';
        }
        $wback.= '</select>';
        return $wback;
    }

    /*
    *js escape 进行编码转换解决中文问题
    */
    public function js_unescape($str){
        $ret = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i ){
            if ($str[$i] == '%' && $str[$i+1] == 'u'){
                $val = hexdec(substr($str, $i+2, 4));
                if ($val < 0x7f) $ret .= chr($val);
                else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
                else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
                $i = 5;
            }elseif ($str[$i] == '%'){
                $ret .= urldecode(substr($str, $i, 3));
                $i = 2;
            }else $ret .= $str[$i];
        }
        return $ret;
    }

    /*
    * 说明：函数功能是把一个图像裁剪为任意大小的图像
    * 参数说明：输入 需要处理图片的 文件名，生成新图片的保存文件名，生成新图片的宽，生成新图片的高
    * 备注：获得任意大小图像，不足地方拉伸，产生变形，不留下空白
    * create by wall
    * time 2012-04-27
    */
    public function my_image_resize($src_file, $dst_file , $new_width , $new_height){
        if($new_width <1 || $new_height <1) {
            echo "params width or height error !";
            exit();
        }
        if(!file_exists($src_file)) {
            echo $src_file . " is not exists !";
            exit();
        }
        // 图像类型
        $type=split('\.',$src_file);
        $support_type=array('jpg' , 'png' , 'gif');
        if(!in_array($type[count($type)-1], $support_type,true)) {
            echo "this type of image does not support! only support jpg , gif or png";
            exit();
        }
        //Load image
        switch($type[count($type)-1]) {
            case 'jpg' :
            $src_img=imagecreatefromjpeg($src_file);
            break;
            case 'png' :
            $src_img=imagecreatefrompng($src_file);
            break;
            case 'gif' :
            $src_img=imagecreatefromgif($src_file);
            break;
            default:
            echo "Load image error!";
            exit();
        }
        $w=imagesx($src_img);
        $h=imagesy($src_img);
        $white_img=imagecreatetruecolor($new_width, $new_height);
        imagefill($white_img, 0,0, imagecolorallocate($white_img, 255, 255, 255));
        $inter_img=imagecreatetruecolor($w , $h);
        imagefill($inter_img, 0,0, imagecolorallocate($inter_img, 255, 255, 255));
        imagecopy($inter_img, $src_img, 0,0,0,0,$w,$h);
        imagecopyresampled($white_img,$inter_img,0,0,0,0,$new_width,$new_height,$w,$h);
        imagejpeg($white_img, $dst_file,100); // 存储图像
    }

    /*
    * @title 取得配置公共方法;
    * @parse $remer string 简码;
    * @parse $bid string 用户ID或SYS;
    */
    public function sys_settings($remer,$bid,$data = ''){
        $backdata = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>$remer,'bid'=>$bid),'value');

        if(empty($data)){
            return $backdata['value'];//非JSON格式的直接返回值
        }elseif($data == 'json'){
            $json_back = array();
            if(empty($backdata['value'])){return $json_back;}else{return json_decode($backdata['value'],true);}
        }
    }

    /*
    * @title 销售下单，散单判断库存接口
    * @author by hanson
    * @parse $wid 传入仓库id
    * 返回：0不需要判断库存,1 要求库存判断     *
    */
    public function get_needchk_whouse($wid){
        $backdata_check_stock_whouse = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>'check_stock_whouse'),'value,id');
        if(empty($backdata_check_stock_whouse['id'])){
            $default_allcheck = 1;//未配置过，则全部检测库存
            return 1;
        }else{
             $backdata_check_stock_whouse = json_decode($backdata_check_stock_whouse['value'],true);
             if(in_array($wid,$backdata_check_stock_whouse)){
                return 1;
             }else{
                 return 0;
             }
        }
     }

    /*采购订单根据状态判断权限*/
    public function check_right_set($remer,$backdata,$column,$ajax = ''){
        $back_chright    = $this->S->dao('sys_setting')->D->get_one_by_field(array('remer'=>$remer),'value');
        $msg_rights      = '对不起，你没有该权限!<br>可联系管理员开启。';
        $msg_ajax        = strtr($msg_rights,array('<br>'=>''));

        if($back_chright['value'] == 'for_cuser' || empty($back_chright['value']))/*只限创建或更改单本人*/
        {
            if($backdata['0'][$column] != $_SESSION['eng_name']){
                if(empty($ajax)){$this->C->sendmsg($msg_rights);}else{$this->C->ajaxmsg(0,$msg_ajax);}
            }
        }
        elseif($back_chright['value'] == 'for_group')//只限同组人
        {
            $back_chrg = $this->S->dao('user')->D->get_one_by_field(array('eng_name'=>$backdata['0'][$column]),'groupid');
            if($back_chrg['groupid'] != $_SESSION['groupid']){
                if(empty($ajax)){$this->C->sendmsg($msg_rights);}else{$this->C->ajaxmsg(0,$msg_ajax);}
            }
        }

     }

    /*
    *生成发货方式下拉
    *@param string $showname 下拉的name
    *@param string $name 显示的名称，填字段名
    *@param string $vval option的值,填字段名
    *@param string $depend 依据来默认选中的名称
    *@param string $dval 依据来默认选中的值
    */
    public function get_shipping($showname,$name,$vval,$depend,$dval){
        $wdata = $this->S->dao('shipping')->D->get_allstr('','','','id,s_name');
        $wback = '';
        $wback = '<select name='.$showname.'>';
        $wback.= '<option value="">=请选择=</option>';
        foreach ($wdata as $val){
            if($depend&&$dval){$selected = ($val[$depend]==$dval)?'selected':'';}
            $wback.= '<option value="'.$val[$vval].'" '.$selected.'>'.$val[$name].'</option>';
        }
        $wback.= '</select>';
        return $wback;
     }

    /*
    * create on 2012-06-11
    * by wall
    * @param $id 仓库id
    * 返回该仓库是否为其他仓库，true是其他仓库，false不是其他仓库
    */
    public function is_other_warehouse($id){
        $sql = 'select * from info_amazon where ia_houseid='.$id;
        $res = $this->S->dao('info_amazon')->D->get_one_sql($sql);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * create on 2012-07-24
    * by hanson
    */
    public function count_shipping_fare($company,$weight,$target){
        $countarray = array('DHL(只发美国)'=>'DHL','fedex-ie'=>'fedexArea','fedex-ip'=>'fedexArea','ups-express'=>'upsArea','ups-expedited'=>'upsArea');
        $countarray_keys = array_keys($countarray);

        if($company == '香港小包'){
            return number_format($weight*75,2);
        } elseif($company == '香港小包挂号'){
            return number_format($weight*100+13,2);
        } elseif($company == 'e邮宝'){
            return $ems_fare = ($weight <= 0.06)?4.8+7 : 80*$weight + 7;//E邮宝的60克以内(含60g)4.8元,61克以上80元/公斤。+每件7元的操作费
        } elseif(in_array($company,$countarray_keys)){
            $extrafare = array('upsArea'=>1.20,'fedexArea'=>1.19,'DHL'=>1.24);//燃油附加费

            $arecode = $this->S->dao('shipping_code')->D->select($countarray[$company],' country="'.$target.'" or code2="'.$target.'"');//查询国家代码

            $ncompany = explode('-',$company);
            if(count($ncompany) == 1){//DHL运费
                $back_fare = $this->S->dao('shipping_fare')->faresignle($weight,$arecode[$countarray[$company]]);
            } else {
                $back_fare = $this->S->dao('shipping_fare')->faresignle($weight,$arecode[$countarray[$company]],$ncompany['1']);//fedex-ups
            }

            /*结果计算*/
            if($back_fare['cal_type'] == 2){//每KG费用
                 $back_fare['price']*=$weight;
            }

            return $back_fare['price']*$extrafare[$countarray[$company]];//乘以燃油附加费
        } else {
            return 0;
        }
    }

    /*
    * create on 2012-07-26
    * by hanson
    * @title 采购入库通知备货人，采购者(当入库数量少于采购订单数量时)
    */
    public function announce_restock($cidnumArray, $process, $type){
        $friend_message  = $this->S->dao('friend_message');//实例化通知表
        $user            = $this->S->dao('user');
        $time            = date('Y-m-d H:i:s', time());

		/*通知备货人*/
		if($type == 'upstock'){

	        foreach($cidnumArray as $val){
	            $backCdata = $process->D->get_one_by_field(array('id'=>$val['c_id']),'detail_id');//通过采购单取备货单ID
	            $backBdata = $process->D->get_one_by_field(array('id'=>$backCdata['detail_id']),'cuser,order_id,sku,quantity');//取备货单信息
	            $backUdata = $user->D->get_one_by_field(array('eng_name'=>$backBdata['cuser']),'uid');
	            $content   = '你好，你申请备货的SKU '.$backBdata['sku'].' 到货并入库 '.$val['quantity'].'pcs，累计已入库'.$val['countnum'].'pcs，经办人'.$_SESSION['eng_name'].'。(原单号和数量分别是'.$backBdata['order_id'].'，'.$backBdata['quantity'].'pcs)';

	            $friend_message->insert_one_leave_message($backUdata['uid'], '', 'System', $content, 0, $time, '', $time);
	        }
		}

		/*通知采购*/
		elseif($type == 'modstock'){
			foreach($cidnumArray as $val){

				$content	= '你好，采购订单'.$val['order_id'].'的SKU'.$val['sku'].'到货并入库'.$val['quantity'].'pcs，经办人'.$_SESSION['eng_name'].'；累计已入库数量 '.$val['countnum'].'pcs，累计入库数量少于订单数量('.$val['squantity'].'pcs)，特此提醒。(--来自系统)';
				$backUdata	= $user->D->get_one_by_field(array('eng_name'=>$val['cuser']),'uid');

				$did = $friend_message->insert_one_leave_message($backUdata['uid'], '', 'System', $content, 0, $time, '', $time);
			}
		}
    }

    /*
    * create on 2012-08-08
    * by hanson
    * @title 分析statu状态，不同状态开放不同操作按钮
    */
    public function disconnect_modbutton($statumod,$statu){
        $statumod_key = array_keys($statumod);
        for($i = 0; $i<count($statumod_key); $i++){
            $child_key = explode('-',$statumod_key[$i]);
            if(count($child_key) > 1){//一个按钮应用于多种状态
                $statumod[$statumod_key[$i]] = in_array($statu,$child_key)?'':'disabled';
            } else {
                $statumod[$statumod_key[$i]] = ($statu === strval($statumod_key[$i]))?'':'disabled';
            }
        }
    }

    /**
     * @title 备货提醒
     * @author Jerry
     * @create on 2013-3-21
     * @desc 当备货状态为已接收的时候（状态:3），采购预估时间小于当前时间，系统就会自动提醒备货人。
     */
    public function buytimenotice(){
        $friend_message  = $this->S->dao('friend_message');//实例化通知表
        $process    = $this->S->dao('process');
        $user       = $this->S->dao('user');
        $time       = date('Y-m-d H:i:s', time());
        $user_send  = $this->S->dao('user_send');

        $datalist   = $process->D->get_list(' and property = "备货单" and isover="N" and statu="3"  and countnum = ""');
        for($i = 0; $i<count($datalist); $i++){
            $extends                    = json_decode($datalist[$i]['extends'],true);
            $datalist[$i]['buytime']    = $extends['buytime'];

            $backUdata	= $user->D->get_one_by_field(array('eng_name'=>$datalist[$i]['cuser']),'uid');
            $content   = '你好,备货单'.$datalist[$i]['order_id'].'已超过采购预估时间('.$datalist[$i]['buytime'].')，SKU'.$datalist[$i]['sku'].' 数量'.$datalist[$i]['quantity'].'pcs，制单人'.$datalist[$i]['cuser'].',请知会采购下单!';

            if (!empty($datalist[$i]['buytime']) && (strtotime($datalist[$i]['buytime'])<strtotime(date('Y-m-d'))) && $backUdata['uid']){
                //备货制单人设置转接
                $friend_message->insert_one_leave_message($backUdata['uid'], '', 'System', $content, 0, $time, '', $time);

            }
        }
    }

    /**
     * @title 销售，采购，到货提醒
     * @author Jerry
     * @create on 2013-03-22
     * @desc 采购下单--到货日期。
     * 如果到货日期小于当前日期，系统自动会提醒销售，采购。
     * 如果用户设置转接提醒，系统会自动提醒转接设置的用户。
     */
     public function comeproductnotice(){

        $friend_message  = $this->S->dao('friend_message');//实例化通知表
        $process    = $this->S->dao('process');
        $user       = $this->S->dao('user');
        $time       = date('Y-m-d H:i:s', time());
        $user_send  = $this->S->dao('user_send');
        $datalist   = $process->D->get_list(' and property = "采购单" and isover="N" and statu="3"  and countnum = ""');

        for($i = 0; $i<count($datalist); $i++){
            $extends                    = json_decode($datalist[$i]['extends'],true);
            $datalist[$i]['e_recdate']    = $extends['e_recdate'];

            /*获取采购制单人*/
            $purchaseuid	= $user->D->get_one_by_field(array('eng_name'=>$datalist[$i]['cuser']),'uid');

            /*获取备货制单人*/
            $stockdata      = $process->D->get_one_by_field(array('id'=>$datalist[$i]['detail_id']),'cuser,order_id,sku,quantity');
            $stockinguid	= $user->D->get_one_by_field(array('eng_name'=>$stockdata['cuser']),'uid');

            if (!empty($datalist[$i]['e_recdate']) && (strtotime($datalist[$i]['e_recdate'])<strtotime(date('Y-m-d')))){

                /*备货制单人设置转接*/
                if ($stockinguid['uid']) {
                    $stockingcontent_send   = '你好,'.$stockdata['order_id'].'备货单未到货，交货日期('. $datalist[$i]['e_recdate'].'),SKU '.$stockdata['sku'].' 数量'.$stockdata['quantity'].'pcs,制单人'.$stockdata['cuser'].',请知悉！';
                    $friend_message->insert_one_leave_message($stockinguid['uid'], '', 'System', $stockingcontent_send, 0, $time, '', $time);
                }

                 /*采购制单人设置转接*/
                if ($purchaseuid['uid']) {
                    $purchasecontent_send   = '你好,'.$datalist[$i]['order_id'].'采购单未到货,交货日期('. $datalist[$i]['e_recdate'].'),SKU '.$datalist[$i]['sku'].' 数量'.$datalist[$i]['quantity'].'pcs,制单人'.$datalist[$i]['cuser'].',请知悉！';
                    $friend_message->insert_one_leave_message($purchaseuid['uid'], '', 'System', $purchasecontent_send, 0, $time, '', $time);
                }

            }
        }
    }

    /*
    * by hanson
    * @title 返回系统本位币
    */
    public function get_system_defaultcoin(){
        return $this->sys_settings('system_defaultcoin','sys',$data = '');
    }

    /*
    * @title 返回下拉 $type=0用于搜索，$type=1用于生成HTML
    * by hanson 2012-08-21
    */
    public function get_sold_way($type = 0,$tablename,$name,$value,$extra){
        $backsoldway = $this->S->dao(strtr($tablename,array('[]'=>'')))->D->get_allstr();

        if($type == 0){
            $backarr = array(''=>'=请选择=');
            for($i = 0; $i < count($backsoldway); $i++){
                $backarr[$backsoldway[$i]['id']] = $backsoldway[$i][$name];
            }
            return $backarr;
        } elseif($type == 1) {
            $backhtml = '<select name='.$tablename.' '.$extra.'><option value="">=请选择=</option>';
            foreach($backsoldway as $val){
                $selected = ($val['id'] == $value)?'selected':'';
                $backhtml.= '<option value='.$val['id'].' '.$selected.' >'.$val[$name].'</option>';
            }
            $backhtml.= '</select>';
            return $backhtml;
        }
    }
    
    /*
    * @title 返回下拉 $type=0用于搜索，$type=1用于生成HTML
    * by jerry 2013-06-18
    */
    public function get_b2b_customers($type = 0,$tablename,$name,$value,$extra){
        $b2bcustomers = $this->S->dao(strtr($tablename,array('[]'=>'')))->D->get_allstr();
        if($type == 0){
            $backarr = array(''=>'=请选择=');
            for($i = 0; $i < count($b2bcustomers); $i++){
                $backarr[$b2bcustomers[$i]['id']] = $b2bcustomers[$i][$name];
            }
            return $backarr;
        } elseif($type == 1) {
            $backhtml = '<select name='.$tablename.' '.$extra.'><option value="">=请选择=</option>';
            foreach($b2bcustomers as $val){
                $selected = ($val['id'] == $value)?'selected':'';
                $backhtml.= '<option value='.$val['id'].' '.$selected.' >'.$val[$name].'</option>';
            }
            $backhtml.= '</select>';
            return $backhtml;
        }
    }
    
    /*
    * 重量转换
    * 说明：将千克转换为英镑
    */
    public function kg_to_lb($kg_weight){
        $lb_weight = $kg_weight * 2.205;
        return $lb_weight;
    }

    /**
     * @title 生成文件
     * by hanson 2012-11-15
     */
    public function makefile($dir,$cont){

    	$handle = fopen($dir,'w');
		//$cont 	= "\xEF\xBB\xBF".($cont);//带汉字的话，自动生成utf8文件

		echo '<body style="font-size:12px;">';

		if(fwrite($handle,$cont) === FALSE){
			exit('写入失败：'.$dir.'<br>');
		}
		fclose($handle);
		echo '已生成：'.$dir.'<br>';

    }

    /**
     * @title 时间转换,如41308转换成2013-02-03,若能直接取时间，则不转。
     * return 0 or 时间
     * create by hanson 2013-04-11
     */
     public function changetime($value){
        
        date_default_timezone_set('PRC');
		$newtime = date('Y-m-d',strtotime($value));

		if($newtime == '1970-01-01'){//若直接取不到正确时间
			if(strlen($value) == 5){
				$newtime = date('Y-m-d',strtotime("+".($value-40909)." days",strtotime('2012-01-01')));//时间转换
				if($newtime == '1970-01-01'){//若转换后依然取不到
					return 0;
				}else{
					return $newtime;
				}
			}else{
				return 0;
			}
		}else{
			return $newtime;
		}
     }
     
      /**
     * @title 时间转换,如41308转换成2013-02-03,若能直接取时间，则不转。
     * return 0 or 时间
     * create by jerry 2013-04-11 12:12:12
     */
     public function changetime_ymdhis($value){
		$newtime = date('Y-m-d H:i:s',strtotime($value) - 8 * 60 *60);
	//	echo $newtime;die();
		if($newtime == '1970-01-01 00:00:00'){//若直接取不到正确时间
			if(strlen($value) == 5 || strpos($value,'.') != false){
				$newtime = date('Y-m-d H:i:s',($value - 25569) * 24 * 60 *60 - 8 * 60 * 60);
                //$newtime = date('Y-m-d H:i:s',strtotime("+".($value-40909)." days",strtotime('2012-01-01')));//时间转换
				if($newtime == '1970-01-01 00:00:00'){//若转换后依然取不到
					return 0;
				}else{
					return $newtime;
				}
			}else{
				return 0;
			}
		}else{
			return $newtime;
		}
     }
     
    /**
     * @title 检测token值
     * create by hanson 2013-04-11
     */
     public function checktoken($token){
     	return  $this->S->dao('client')->D->get_one(array('token'=>$token),'*');
     }
    
    
}
?>
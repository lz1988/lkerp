<?php
/*
 * Created on 2011-11-16
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class processDao extends D{
    /*备货列表*/
	public function upstock_list($sqlstr,$orders){
		$sql = 'select pc.cost1,pc.coin_code,e.name as receiver_id,o.conditionerp,p.id,p.sku,p.isover,p.fid,p.order_id,p.product_name,p.quantity,p.price,p.cuser,p.cdate,p.muser,p.mdate,p.rdate,p.ruser,p.comment,p.extends,p.statu,sl.esseid  as esseid
                from process p
                left join esse e on p.receiver_id=e.id
                left join product o on o.sku=p.sku
                left join product_cost pc on o.pid=pc.pid
                left join esse AS  sl
                ON sl.id=p.provider_id
                where 1 '.$sqlstr.' '.$orders;
		return $this->D->query_array($sql);
	}

 	/*取得最大单号通用，匹配条件即可*/
 	public function maxorder($sqlstr){
 		$sql = 'select max(order_id) as maxorder from process where 1 '.$sqlstr;
 		$data = $this->D->query_array($sql);
 		return $data['0']['maxorder'];
 	}

 	/*预采购列表,联仓库表,同时用于编辑采购单读取资料*/
 	public function showneedsto($sqlstr,$orders){

		$sql = 'select p.fee,p.fee2,p.fee3,p.coin_code,p.statu,p.provider_id,p.receiver_id,p.isover,p.property,p.detail_id,p.price,price2,p.ispay,p.extends,p.comment,p.id,p.fid,p.order_id,p.product_name,p.sku,p.quantity,p.countnum,p.muser,p.cuser,p.mdate,p.cdate,p.ruser,p.rdate,e.name as whouse_name,e.id as whouse_id,s.name as suppliername from process as p ';
		$sql .= ' left join esse as e on p.receiver_id=e.id ';
		$sql .= ' left join esse as s on p.provider_id=s.id ';
		$sql .= ' where 1 '.$sqlstr.' order by '.$orders.' p.mdate desc,p.order_id desc,p.statu asc,p.id desc';
		return $this->D->query_array($sql);
 	}

    /*采购订单--未审核金蝶数据导出*/
    public function showjingdieoutput($sqlstr){
        $sql = 'select p.order_id,p.cdate,p.statu,p.property,p.fee,p.ispay,p.extends,p.order_id,p.sku,p.quantity,p.muser,p.cuser,p.mdate,p.ruser,p.rdate,e.name as whouse_name,e.id as whouse_id,s.name as suppliername from process as p ';
		$sql .= ' left join esse as e on p.receiver_id=e.id ';
		$sql .= ' left join esse as s on p.provider_id=s.id ';
		$sql .= ' where 1 '.$sqlstr.' order by  p.mdate desc,p.order_id desc,p.statu asc,p.id desc';
        //die($sql);
        return $this->D->query_array($sql);
    }

 	/*预入库列表，联仓库表*/
 	public function showneedrec($sqlstr,$orders,$jd = ''){
 		if(empty($jd)){
	 		$sql = 'select p.comment,p.comment2,e.name as warehouse,p.property,p.protype,p.fid,p.detail_id,p.price,p.quantity,p.countnum,p.sku,p.product_name,p.order_id,p.cuser,p.muser,p.ruser,p.mdate,p.cdate,p.rdate,p.statu from process as p left join esse as e on p.receiver_id=e.id where 1 '.$sqlstr.$orders;
 		}elseif($jd == 1){
 			$sql = 'select ai.sku_code as jin_sku,e.name as warehouse,p.property,p.comment,p.sold_way,p.protype,p.price2,p.comment,p.fid,p.detail_id,p.price,s.name as sname,p.quantity,p.countnum,p.sku,p.product_name,p.order_id,p.cuser,p.muser,p.ruser,p.mdate,p.cdate,p.rdate,p.statu from process as p left join esse as e on p.receiver_id=e.id left join esse as s on p.provider_id = s.id left join  sku_alias as ai on (ai.pid=p.pid and ai.sold_way="金碟") where 1 '.$sqlstr.$orders;
 		}elseif($jd == 2){
            $sql = 'select pc.cost1,ai.sku_code as jin_sku,e.name as warehouse,p.property,p.comment,p.sold_way,p.protype,p.comment,p.price2,p.fid,p.detail_id,p.price,s.name as sname,p.quantity,p.countnum,p.sku,p.product_name,p.order_id,p.cuser,p.muser,p.ruser,p.mdate,p.cdate,p.rdate,p.statu from process as p ';
            $sql .= ' left join esse as e on p.receiver_id=e.id ';
            $sql .= ' left join esse as s on p.provider_id = s.id ';
            $sql .= ' left join  sku_alias as ai on (ai.pid=p.pid and ai.sold_way="金碟") ';
            $sql .= ' left join product_cost as pc on p.pid=pc.pid';
            $sql .= ' where 1 '.$sqlstr.$orders;
        }
 		return $this->D->query_array($sql);
 	}

 	/*出库明细*/
 	public function showoutstocklist($sqlstr){
 		$sql = 'select ai.sku_code as jin_sku,e.name as warehouse,p.fid,p.deal_id,p.receiver_id,p.order_id,p.price2,sold_way.wayname,p.coin_code,s.name as sname,p.sold_way,p.extends,p.price,p.cdate,p.sku,p.product_name,p.quantity,p.rdate,p.mdate,p.muser,p.comment2,p.comment from process as p  left join esse as e on p.provider_id=e.id left join esse as s on p.receiver_id = s.id LEFT JOIN sold_way ON sold_way.id=p.sold_way left join  sku_alias as ai on (ai.pid=p.pid and ai.sold_way="金碟") where output="1" '.$sqlstr.' order by p.mdate desc,p.id desc';
 		return $this->D->query_array($sql);
 	}

    /*出库汇总*/
    public function showsumoutstocklist($sqlstr){
        $sql = 'select ai.sku_code as jin_sku,sold_way.wayname,e.name as warehouse,p.order_id,p.sku,p.product_name,sum(p.quantity) as quantity,p.mdate,p.muser from process as p  left join esse as e on p.provider_id=e.id left join  sku_alias as ai on (ai.pid=p.pid and ai.sold_way="金碟") left join sold_way on sold_way.id=p.sold_way where output="1" '.$sqlstr.'group by p.sku order by p.mdate,p.id desc';
        return $this->D->query_array($sql);
    }

 	/*预出库(转仓)*/
 	public function showtransfer($sqlstr,$orders){
        //如果type=3 分摊反写运费：已出库统计运费反写 实重和体积重比较 大者叠加除以总重等到比例乘以追踪号对应的运费
        /*if ($type == '3'){
            $sql = 'select shipping.sumweight,shipping.weight,temp.total as shipping_fee,e.name as rechouse,e2.name as prohouse,p.statu as statu,p.id,p.fid,p.quantity,p.cuser,p.muser,p.cdate,p.extends,p.sold_way,p.comment2,p.comment,p.sku,p.order_id,p.product_name from process as p left join esse as e on p.receiver_id=e.id left join esse as e2 on p.provider_id=e2.id left join (select shipping,track_no,sum(total) as total from shipping_farerewrite group by track_no)temp on temp.track_no=p.comment2';
            $sql .= ' left join (SELECT process.comment2,SUM(CASE WHEN (product.box_product_dimensions/5000)>box_shipping_weight THEN product.box_product_dimensions/5000 ELSE box_shipping_weight END ) AS sumweight,';
            $sql .= ' (CASE WHEN (product.box_product_dimensions/5000)>box_shipping_weight THEN product.box_product_dimensions/5000 ELSE box_shipping_weight END ) AS weight ';
            $sql .= ' FROM `process` LEFT JOIN product ON product.pid=process.pid  where 1 and process.receiver_id>0 '.$sqlstr.' GROUP BY comment2)  shipping ON shipping.comment2=p.comment2'; 
            $sql .= ' where 1 and p.receiver_id>0 '.$sqlstr.$orders;
        }else{*/
            $sql = 'select temp.total as shipping_fee,e.name as rechouse,e2.name as prohouse,p.statu as statu,p.id,p.fid,p.quantity,p.cuser,p.muser,p.cdate,p.extends,p.sold_way,p.comment2,p.comment,p.sku,p.order_id,p.product_name,shipping_farerewrite,sum_product_size,sum_product_weight from process as p  left join esse as e on p.receiver_id=e.id left join esse as e2 on p.provider_id=e2.id left join (select shipping,track_no,sum(tariff_costs) as total from tariff_rewrite group by track_no)temp on temp.track_no=p.comment2 where 1 and p.receiver_id>0 '.$sqlstr.$orders;
            
        //}    
 		return $this->D->query_array($sql);
 	}
    
    /*分摊反写运费：已出库统计运费反写 实重和体积重比较 大者叠加除以总重等到比例乘以追踪号对应的运费*/
    public function showtransfer_sumfarewritecost($comment2){
        $sql  = " SELECT SUM(CASE WHEN (SELECT SUBSTRING_INDEX(box_product_dimensions,'x',1))*(SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(box_product_dimensions,'x',2),'x',-1))*(SELECT SUBSTRING_INDEX(box_product_dimensions,'x',-1)/5000)>box_shipping_weight"; 
        $sql .= " THEN (SELECT SUBSTRING_INDEX(box_product_dimensions,'x',1))*(SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(box_product_dimensions,'x',2),'x',-1))*(SELECT SUBSTRING_INDEX(box_product_dimensions,'x',-1)/5000)";
        $sql .= " ELSE box_shipping_weight END ) AS sumweight FROM `process` LEFT JOIN product ON product.pid=process.pid";
        $sql .= " WHERE 1 AND process.receiver_id>0 AND statu='3' AND property='转仓单' AND isover='N' and comment2='".$comment2."'  GROUP BY comment2";
        return $this->D->get_one_sql($sql);
        
    }
    
    /*分摊反写运费：已出库统计运费反写 实重和体积重比较 大者叠加除以总重等到比例乘以追踪号对应的运费*/
    public function showtransfer_sumfarewritecost_s($comment2){
        $sql  = " SELECT SUM(CASE WHEN sum_product_weight>sum_product_size THEN sum_product_weight ELSE sum_product_size END) AS sumweight FROM `process`";
        $sql .= " WHERE 1 AND receiver_id>0 AND statu='3' AND property='转仓单' AND isover='N' and comment2='".$comment2."'  GROUP BY comment2";
        return $this->D->get_one_sql($sql);
        
    }

 	/*预出库(销售下单)*/
 	public function get_shipment_orders($sqlstr, $mysold = '', $stars){
 		if(empty($mysold))
 		{
	 		$sql = 'select w.wayname,p.id,p.deal_id,order_id,fid,sku,product_name,quantity,p.price,p.statu,p.isover,p.cuser,muser,sold_way,buyer_id,p.cdate,p.rdate,p.comment,comment2,comment3,p.extends,e.name as warename from process as p left join esse as e on e.id=p.provider_id left join `sold_way` as w on p.sold_way=w.id where 1 '.$sqlstr.' order by order_id desc ';
 		}elseif($mysold == 'mysold'){
 			$minsqlstr = ($stars == 1)?' and temp.os_id is NULL ':(($stars == 2)?' and temp.os_id!=""':'');
 			$sql = 'select temp.* from(';
			$sql.= 'select s.wayname  soldway,c.account_name  soldid,f.payrec_account payrecid,o.*,p.coin_code,p.id,order_id,fid,p.deal_id,sku,product_name,quantity,p.price,p.statu,p.isover,p.cuser,muser,sold_way,buyer_id,p.cdate,p.rdate,p.comment,comment2,comment3,p.extends,e.name as warename from process as p left join esse as e on e.id=p.provider_id left join order_signed as o on o.os_id=p.id left join sold_way s on s.id=p.sold_way left join sold_account c on c.id=p.sold_id left join finance_payrec_account f on f.id=p.payrec_id where 1 '.$sqlstr.' order by order_id desc ';
			$sql.= ')temp where 1 '.$minsqlstr;
 		}
 		return $this->D->query_array($sql);
 	}

    /*出库订单导出最终表*/
    public function outputfinalorder($sqlstr){
        $sqls   = 'select market_price,w.wayname,finance_payrec_account.payrec_account,sold_account.account_name,p.id,p.deal_id,order_id,fid,sku,product_name,quantity,p.price,p.statu,p.pid,p.coin_code,p.isover,p.cuser,muser,p.sold_way,buyer_id,p.cdate,p.coin_code as coincode,p.rdate,p.comment,comment2,comment3,p.extends,e.name as warename,product_cost.cost3 from process as p left join esse as e on e.id=p.provider_id  left join product_cost on product_cost.pid=p.pid left join finance_payrec_account on finance_payrec_account.id=p.payrec_id left join sold_account on sold_account.id=p.sold_id left join `sold_way` as w on p.sold_way=w.id where 1 '.$sqlstr.' order by order_id desc ';
        $sql    = 'SELECT market_price,payrec_account,account_name,wayname,id,deal_id,order_id,fid,sku,product_name,quantity,statu,coincode,isover,cuser,muser,sold_way,buyer_id,cdate,rdate,`comment`,comment2,comment3,extends, warename,cost3,(SELECT case when count(*) = 1 then price else a.market_price/SUM(market_price)*SUM(price) end FROM (';
        $sql   .= $sqls.') AS t';
        $sql   .= ' WHERE t.order_id=a.order_id GROUP BY t.order_id) AS price FROM (';
        $sql   .= $sqls.') a;';
        //die($sql);
        return $this->D->query_array($sql);
    }
    
 	/*退货明细*/
 	public function get_backprolist($sqlstr){
 		$sql = 'select s.wayname,o.account_name,p.input,p.order_id,p.comment2,p.fee,p.id,p.statu,p.fid,p.sku,p.product_name,p.quantity,p.buyer_id,p.cuser,p.cdate,p.comment,p.comment3 from process as p left join sold_account as o on p.sold_id=o.id left join `sold_way` as s on p.sold_way=s.id where 1 '.$sqlstr.' order by p.id desc';
		return $this->D->query_array($sql);

 	}

 	/*退款明细*/
 	public function get_backorlist($sqlstr){
 		$sql = 'select s.wayname,o.account_name,p.sold_id,p.coin_code,p.muser,p.order_id,p.extends,p.comment2,p.id,p.statu,p.fid,p.sku,p.product_name,p.price,p.sold_way,p.cuser,p.cdate,p.comment,p.comment3 from process as p left join sold_account as o on p.sold_id=o.id left join `sold_way` as s on p.sold_way=s.id where 1 '.$sqlstr.' order by p.id desc';
 		return $this->D->query_array($sql);
 	}

 	/*批量采购下单时，需要显示仓库，所以联表*/
 	public function gostockorder($strid){
 		$sql = 'select p.id,p.sku,p.product_name,p.quantity,p.countnum,e.name as whouse_name from process as p left join esse as e on p.receiver_id=e.id where p.id in('.$strid.') and quantity>countnum';
 		return $this->D->query_array($sql);
 	}

 	/*入库时，需要读取该订单所有产品信息*/
 	public function gorecstockorder($order_id){
 		$sql = 'select e.name,p.id,p.comment,sku,product_name,quantity,countnum,statu from process as p left join esse as e on p.receiver_id=e.id where order_id="'.$order_id.'" and quantity>countnum order by p.id asc';
 		return $this->D->query_array($sql);
 	}

 	/*采购入库时，需要读取该订单信息*/
 	public function gorecstockcigou($order_id){
	 	$sql = 'select temp.coin_code,temp.name,temp.id,temp.comment,temp.sku,temp.product_name,temp.quantity,temp.countnum,temp.statu from (select e.name,p.coin_code,p.id,p.comment,sku,product_name,sum(quantity) as quantity,sum(countnum) as countnum,statu from process as p left join esse as e on p.receiver_id=e.id where order_id="'.$order_id.'"  group by fid order by p.id asc)temp  where temp.quantity>temp.countnum order by temp.id asc';
	 	return $this->D->query_array($sql);
 	}

 	/*根据采购订单号获得订单产品信息，供应商(联实体表)信息,审核人与制单人(联有户表)信息*/
 	public function print_getmsg($sqlstr){
 		$sql = 'select um.chi_name as mname,uc.chi_name as cname,e.name,e.extends as e_extends,p.fee,p.sku,p.product_name,p.quantity,p.price,p.extends as p_extends,p.comment,p.cdate as cdate from process as p left join esse as e on p.provider_id=e.id left join user as um on um.eng_name=p.muser  left join user as uc on uc.eng_name=p.cuser where 1 '.$sqlstr.' group by p.id';
 		return $this->D->query_array($sql,fetch_assoc);

 	}

 	/*获得该采购单（批量采购）的最大备货单，水单只保存在此记录的扩展内容中*/
 	public function get_maxfid($order_id){
 		$sql = 'select id,extends from process where id =(select max(id) from process where order_id="'.$order_id.'") and property="采购单"';
 		return $this->D->query_array($sql);
 	}


	/*数量显示-查某仓库，某SKU的已下备货申请数量*/
	public function get_upstock_bysku($sku,$houseid){
		$sql = 'select sum(quantity) as inupstocknums from process where property="备货单" and isover="N" and statu!="2" and sku="'.$sku.'" and receiver_id="'.$houseid.'"';
		$back= $this->D->get_one_sql($sql);
		if(empty($back['inupstocknums'])) $back['inupstocknums']=0;
		return $back['inupstocknums'];
	}



	/*列表显示-查某仓库，某SKU，采购与转仓在途、退货在途的批次*/
	public function get_allinware($sku,$houseid){
		$sql = 'select order_id,sums,statu,property from(';
		$sql.= ' select order_id,(quantity-countnum) as sums,statu,property from process where property="采购单" and isover="N" and sku="'.$sku.'" and receiver_id="'.$houseid.'"';
		$sql.= ' union all';
		$sql.= ' select order_id,quantity as sums,statu,property from process where active="1" and input="0" and sku="'.$sku.'" and receiver_id='.$houseid;
		$sql.= ' )temp';
		return $this->D->query_array($sql);
	}


	/*数量显示--查看某仓库,某SKU的不良品调拨情况*/
	public function get_badinhouse($sku,$houseid){

		$sql = 'select sum(quantity) as badsums,comment2 from process where property="转仓单" and input="1" and output="1" and provider_id='.$houseid.' and receiver_id="" and sku="'.$sku.'" group by comment2';
		return $this->D->query_array($sql);

	}


	/*数量显示-查某仓库，某SKU的预出库数(包括销售下单与调拨)*/
	public function get_havebook_bysku($sku,$houseid){
		$sql = 'select sum(quantity) as quantity from process where sku="'.$sku.'" and provider_id='.$houseid.' and output="0" and isover="N" and (property="出仓单" or property="转仓单")';
		return $this->D->get_one_sql($sql);
	}

	/*列表显示-某仓库，某SKU，三个月内的销售数(包含了重发的)*/
	public function get_havesold_nearthree($sku,$houseid){
		$sql = 'select sum(quantity) as soldsums,cdate from process where sku="'.$sku.'" and provider_id='.$houseid.' and property="出仓单" and (protype="售出" or protype="重发") and output="1" and active="1"  group by EXTRACT(YEAR FROM cdate),extract(month from cdate) order by cdate desc limit 3';
		return $this->D->query_array($sql);
	}

	/*
	 * update on 2012-04-23
	 * by wall
	 * 个人中心使用 查询产品定制最新一条记录(正常单isover="N")
	 * $pid pid订单表中的产品编号
	 * return 返回最新一条PID的订单信息
	 * */
	public function get_one_by_pid_desc_mdate($pid) {
		//$sql = 'select sku,mdate,quantity,property,statu from process where isover="N" and pid='.$pid.' order by mdate desc ';
		//update on 2012-04-23
		$sql = 'select sku,mdate,count(quantity) as quantity,property,statu from process where isover="N" and pid='.$pid.' group by mdate,statu,property  order by mdate desc ';
		return $this->D->get_one_sql($sql);
	}

	/*报表统计--SKU为$sku的订单总数、卖出产品总数、卖出产品销售总额(不包括重发)*/
	public function get_total_process($sqlstr, $group, $orders) {
		$sql = 'select p.cdate as cdate,sold_way,sku,pc.coin_code as coin_code,count(sku) as totalprocess,sum(quantity) as totalquantity,sum(price) as totalprice,cost2 as cost ';
		$sql .= ' from process p left join product_cost pc on p.pid=pc.pid ';
		$sql .= 'where active="1" and output="1"  and protype= "售出" '.$sqlstr;
		$sql .= ' group by '.$group.' order by '.$orders.'cdate desc,p.pid,sold_way desc';
		return $this->D->query_array($sql);
	}

	/*报表统计--SKU为$sku的退货产品总数*/
	public function get_sum_product_by_return_process($sqlstr, $group) {
		$sql = 'select sum(quantity) as total from process where protype="退货" '.$sqlstr.' group by '.$group;
		return $this->D->query_array($sql);
	}
	/*报表统计--SKU为$sku的退款*/
	public function get_sum_price_by_return_process($sqlstr, $group) {
		$sql = 'select sum(price) as returnprice from process where ispay="1" and property="退款单" '.$sqlstr.' group by '.$group;
		return $this->D->query_array($sql);
	}

	/*财务需求列表-采购已审核列表，供勾勒出应付帐款*/
	public function get_needpay_stock($sqlstr){
		$sql = 'select e.name,order_id,sku,product_name,quantity,p.comment,p.extends,p.mdate,price from process as p left join esse as e on e.id=p.provider_id where property="采购单" and (statu="1" or statu="3") and ispay="0" '.$sqlstr.' and p.id not in(select detail_id from finance_need) order by order_id asc';
		return $this->D->query_array($sql);
	}


	/**
	 * 核心语句-查所有仓库所有SKU的库存可发数(不包括不良品与已下订单(出仓单与转仓单)的)
	 * @param $unquick=1,快速查询，只返回数量
	 **/
	public function get_allw_allsku($sqlstr,$unquick=1){

	  if($unquick == 2){

		$sql = 'SELECT temp.sku,temp.wid,p.pid,p.product_name,e.name as warename,sum(quantity) as sums,sum(in_waresums) as in_waresums,sum(in_soldsums) as in_soldsums,sum(tensoldsums) as tensoldsums from (';
		$sql.= ' select sku,receiver_id as wid,quantity,0 as in_waresums,0 as in_soldsums,0 as tensoldsums from process where active="1" and input="1" and receiver_id>0 ';	//查进仓，要加receiver_id>0，否则不良品调拨也调出来
		$sql.= ' union all';
		$sql.= ' select sku,provider_id as wid,-quantity as quantity,0 as in_waresums,0 as in_soldsums,0 as tensoldsums from process where active="1" and output="1" and provider_id>0'; 		//查出仓
 		$sql.= ' union all';
 		$sql.= ' select sku,provider_id as wid,-quantity as quantity,0 as in_waresums,0 as in_soldsums,0 as tensoldsums from process where output="0" and isover="N" and (property="出仓单" or property="转仓单")';//再减去已被book的
 		$sql.= ' union all';
 		$sql.= ' select sku,receiver_id as wid,0 as quantity,quantity as in_waresums,0 as in_soldsums,0 as tensoldsums from process where active="1" and input="0" and receiver_id>0'; //转入与退货在途,要加receiver_id>0，否则售出的也读出来
		$sql.= ' union all';
		$sql.= ' select sku,receiver_id as wid,0 as quantity,(quantity-countnum) as in_waresums,0 as in_soldsums,0 as tensoldsums from process where property="采购单" and isover="N"';//采购在途
		$sql.= ' union all';
		$sql.= ' select sku,provider_id as wid,0 as quantity,0 as in_waresums,quantity as in_soldsums,0 as tensoldsums from process where output="1" and active="1" and (protype="售出" or protype="重发")';//销售历史
 		$sql.= ' union all';
 		$sql.= ' select sku,provider_id as wid,0 as quantity,0 as in_waresums,0 as in_soldsums,quantity as tensoldsums from process where output="1" and active="1" and (protype="售出" or protype="重发") and date_sub(curdate(),INTERVAL 10 DAY) < date(mdate)';//近十天销售历史,用于计算库存系数
 		$sql.= ' )temp';
 		$sql.= ' left join product as p on p.sku=temp.sku  left join esse as e on e.id=temp.wid '; 							//JOIN产品表与实体表查产品名称与仓库名称
 		$sql.= ' where 1=1'.$sqlstr.' group by temp.sku,temp.wid';
        //die($sql);

	  }elseif($unquick == 1){

	  	$sql = 'SELECT sum(quantity) as sums from (';
		$sql.= ' select sku,receiver_id as wid,pid,quantity from process where active="1" and input="1" and receiver_id>0 ';   //查进仓数量
		$sql.= ' union all';
		$sql.= ' select sku,provider_id as wid,pid,-quantity from process where output="0" and isover="N" and (property="出仓单" or property="转仓单") '; //减去被BOOK的
		$sql.= ' union all';
		$sql.= ' select sku,provider_id as wid,pid,-quantity as quantity from process where active="1" and output="1"  and provider_id>0';		//查出仓数量
 		$sql.= ' ) temp';
 		$sql.= ' where 1=1'.$sqlstr.' group by temp.sku,temp.wid';

	  }
     //echo $sql;
		return $this->D->query_array($sql);

	}

	/**
	 * 数量显示，查在途库存(采购在途、转入在途、退货在途)
	 * $in_type=1 采购在途-已下采购单但未入库的
	 * $in_type=2 转入在途-有下单，不管是不是已确认都算在途
	 * $in_type=3 退单在途-已下退单
	 */
	public function get_all_incoming_byskuhouse($sku,$houseid,$in_type){
		switch($in_type){
			case 1:$sql = 'select sum(quantity-countnum) as sumsnum from process where  sku="'.$sku.'" and receiver_id='.$houseid. ' and property="采购单" and isover="N" ';break;
			case 2:$sql = 'select sum(quantity) as sumsnum from process where  sku="'.$sku.'" and receiver_id='.$houseid.' and property="转仓单" and input="0" ';break;
			case 3:$sql = 'select sum(quantity) as sumsnum from process where  sku="'.$sku.'" and receiver_id='.$houseid.' and protype="退货" and input="0" ';break;
			case 4:$sql = 'select sum(quantity) as sumsnum from process where  sku="'.$sku.'" and provider_id='.$houseid.' and output="1" and active="1" and date_sub(curdate(),INTERVAL 10 DAY) < date(cdate) and property="出仓单" and (protype="售出" or protype="重发") ';
		}

		$back= $this->D->get_one_sql($sql);
		if(empty($back['sumsnum'])) $back['sumsnum']=0;
		return $back['sumsnum'];
	}

	/*带成本价的实际库存*/
	public function get_allw_allsku_withp($sqlstr){

		$sql = 'SELECT temp.sku,temp.wid,temp.pid,p.product_name,pc.cost1 as costp,pc.coin_code,e.name as warename,sum(quantity) as sums from (';
		$sql.= ' select sku,receiver_id as wid,pid,quantity from process where active="1" and input="1" and receiver_id>0 ';
		$sql.= ' union all';
		$sql.= ' select sku,provider_id as wid,pid,-quantity as quantity from process where active="1" and output="1"  and provider_id>0) temp';
 		$sql.= ' left join product as p on p.sku=temp.sku  left join esse as e on e.id=temp.wid  left join product_cost as pc on pc.pid=temp.pid';
 		$sql.= ' where 1=1'.$sqlstr.' group by temp.sku,temp.wid';
		return $this->D->query_array($sql);
	}

	/*查实际库存，可按时间段查(注意转仓单)*/
	public function get_allw_allsku_withp_time($sqlstr,$timesqlstr){

		$sql = 'SELECT c.cost2,c.coin_code,ai.sku_code as jin_sku,temp.sku,temp.wid,temp.pid,p.product_name,e.name as warename,sum(quantity) as sums,sum(inquantity) as insums from (';
		$sql.= ' select sku,receiver_id as wid,pid,quantity,quantity as inquantity from process where active="1" and input="1" and receiver_id>0 '.$timesqlstr;   //查进仓数量
		$sql.= ' union all';
		$sql.= ' select sku,provider_id as wid,pid,-quantity as quantity,0 from process where active="1" and output="1"  and provider_id>0'.$timesqlstr;
 		$sql.= ' ) temp left join product as p on p.sku=temp.sku ';
 		$sql.= ' left join sku_alias as ai on (ai.pid=temp.pid and ai.sold_way="金碟")  left join esse as e on e.id=temp.wid';
 		$sql.= ' left join product_cost c on c.pid=temp.pid';
 		$sql.= ' where 1=1'.$sqlstr.' group by temp.sku,temp.wid';
		return $this->D->query_array($sql);
	}

	/* create on 2012-04-23
	 * by wall
	 * 产品定制中要求按日期，状态分组
	 * @param $sqlstr 条件
	 * */
	public function get_list_groub_by_day($sqlstr) {
		$sql = 'select order_id,mdate,statu,sum(quantity) as quantity,property from process where 1 '.$sqlstr.' group by date(mdate),statu,property order by mdate desc';
		return $this->D->query_array($sql);
	}

 	/*
 	 * create on 2012-05-07
 	 * update on 2012-06-01
 	 * by wall
 	 * 报表统计--时间段销售报表*/
	public function get_total_process_by_date($sold_way,$sqlstr) {
		$sql = 'select cdate,max(sold_way) as sold_way,sum(totalprocess) as totalprocess,sum(totalquantity) as totalquantity,sum(totalprice) as totalprice,sum(returnprice) as returnprice from(';
		$sql .= ' select cdate,0 as sold_way,0 as totalprocess,0 as totalquantity,0 as totalprice,sum(price) as returnprice from process where ispay="1" and property="退款单" '.$sold_way.' group by TO_DAYS(cdate) ';
		$sql .= ' union all';
		$sql .= ' select cdate,sold_way,count( distinct order_id) as totalprocess,sum(quantity) as totalquantity,sum(price) as totalprice,0 as returnprice from process where active="1" and output="1" and protype= "售出" '.$sold_way.' group by TO_DAYS(cdate) ';
		$sql .= ') as p where 1 '.$sqlstr.' group by TO_DAYS(cdate) order by cdate desc';
		return $this->D->query_array($sql);
	}

	/*
	 * create on 2012-05-22
	 * by wall
	 * DHL待发货订单
	 * */
	public function get_dhl_list() {
		$sql = ' select distinct id,order_id,fid,pd.sku,pd.product_name,quantity,price,statu,isover,sold_way,buyer_id,comment,comment2,comment3,shipping_weight,product_dimensions,p.extends from process as p';
		$sql .= ' left join product as pd on p.pid=pd.pid '	;
		$sql .= ' where extends like "%e_shipping\":%DHL%" and (protype="售出" or protype="重发") and isover="N" and statu="2" order by order_id desc';
		return $this->D->query_array($sql);
	}

	/*
	 * create on 2012-05-23
	 * by wall
	 * 退货操作时，获取目标订单号所有订单信息
	 * */
	public function get_all_list_by_order_id($order_id) {
		$sql = ' select id,sku,sum(quantity) as quantity from (';
		$sql .= ' select id,sku,quantity from process where order_id="'.$order_id.'" ';
		$sql .= ' union all ';
		$sql .= ' select p1.id,p1.sku,-sum(p2.quantity) as quantity from process as p1 ';
		$sql .= ' left join process as p2 on p1.pid=p2.pid where p1.order_id="'.$order_id.'" ';
		$sql .= ' and p2.property="进仓单" and p2.protype="退货" and p1.fid=p2.fid and p1.pid=p2.pid group by p1.id';
		$sql .= ') as p group by id ';
		return $this->D->query_array($sql);
	}

	/*
	 * create on 2012-05-24
	 * by wall
	 * 退款操作时，获取目标订单号所有退款价格信息
	 * */
	public function get_all_price_by_order_id($order_id) {
		$sql = 'select id,sku,price,extends,sum(out_price) as out_price from (';
		$sql .= ' SELECT a.id,sku,(a.price/b.rate*100) as price,extends,0 as out_price FROM process as a ';
		$sql .= ' left join exchange_rate as b on a.coin_code=b.code ';
		$sql .= ' where a.stage_rate=b.stage_rate  and order_id="'.$order_id;
		$sql .= '" union all ';
		$sql .= ' select p1.id,0,0,0,sum(p2.price/er.rate*100) as out_price FROM process as p1 ';
		$sql .= ' left join process as p2 on p1.id=p2.detail_id ';
		$sql .= ' left join exchange_rate as er on p2.coin_code=er.code';
		$sql .= ' where er.stage_rate=p2.stage_rate and p1.order_id="'.$order_id;
		$sql .= '" and p2.property = "退款单" group by p2.detail_id) as p group by p.id';
		return $this->D->query_array($sql);
	}

	/**
	 *@time 	create on 2012-06-14
	 *@author 	hanson
	 *@title	异常订单列表
	 **/
	 public function get_badorder($sqlstr){
	 	$sql = 'select s.wayname,p.id,p.quantity,p.provider_id,p.cdate,p.order_id,p.fid,p.sku,p.buyer_id,p.product_name,p.comment3,p.cuser,e.name from process p left join esse e on p.provider_id=e.id left join `sold_way` as s on p.sold_way=s.id where 1 '.$sqlstr.' order by p.comment3 asc,p.id desc';
	 	return $this->D->query_array($sql);
	 }


	 /**
	  *@time 	2012-06-27
	  *@author	hanson
	  *@title	E邮宝的发货列表
	  */

	  public function get_emsorder($sqlstr){

	  	$sql = 'select p.id,p.order_id,p.fid,p.sku,p.product_name,p.quantity,p.cuser,p.extends,e.name as warehouse from process as p left join esse as e on e.id=p.provider_id where 1 '.$sqlstr.' order by order_id desc';
		return $this->D->query_array($sql);

	  }

	/*
	 * create on 2012-07-26
	 * by wall
	 * @param $id 事务表id
	 * 返回指定事务记录的sku、receiver_id、quantity（备货操作使用）
	 * */
	public function get_info_by_id($id) {
		$sql = 'select sku,receiver_id,quantity from process where id='.$id;
		return $this->D->get_one_sql($sql);
	}

 	/*
	 * create on 2012-08-10
	 * by wall
	 * @param $id 事务表id
	 * 返回指定事务记录的扩展信息
	 * */
	public function get_extends_by_id($id) {
		$sql = 'select pid,extends from process where id='.$id;
		return $this->D->get_one_sql($sql);
	}

	/*
	 * create on 2012-07-25
	 * by wall
	 * @param $sqlstr 查询条件
	 * 采购订单序时簿查询（财务用）
	 * */
	public function financial_get_purchase_order($sqlstr) {
		$sql = 'select a.order_id as order_no,a.id,name,mdate,sku,product_name,quantity,coin_code,stage_rate,price,a.extends from process as a ';
		$sql .= ' left join esse as b on a.provider_id=b.id ';
		$sql .= ' where b.type=3 and (statu="3" or statu="4") and ispay="1" and property="采购单" '.$sqlstr.' order by a.order_id desc';
		return $this->D->query_array($sql);
	}

	/*
	 * create on 2012-07-25
	 * by wall
	 * @param $sqlstr 查询条件
	 * 采购入库单序时簿查询（财务用）
	 * */
	public function financial_get_purchase_storage($sqlstr) {
		$sql = 'select a.order_id as order_no,a.id,name,a.mdate,a.sku,a.product_name,b.quantity as real_quantity,a.quantity,a.coin_code,a.stage_rate,a.price,b.order_id from process as a ';
		$sql .= ' left join process as b on a.detail_id=b.id ';
		$sql .= ' left join esse as c on a.receiver_id=c.id ';
		$sql .= ' where c.type=2 and a.property="进仓单" and a.protype="采购" and a.active="1" and a.input="1" '.$sqlstr.' order by a.order_id desc';
		return $this->D->query_array($sql);
	}

 	/*
	 * create on 2012-07-26
	 * by wall
	 * @param $sqlstr 查询条件
	 * 销售出库单序时簿查询（财务用）
	 * */
	public function financial_get_sales_of_library($sqlstr) {
		$sql = 'select a.order_id as order_no,a.mdate,sold_way,sku,product_name,name,quantity,a.coin_code,a.stage_rate,"CNY" as coin_code1,price2 as cost1, price as sumprice,a.extends,order_id from process as a ';
		$sql .= ' left join esse as b on a.provider_id=b.id ';
		$sql .= ' where b.type=2 and statu="3" and (protype="售出" or protype="重发") and isover="N" '.$sqlstr.' order by a.order_id desc';
		return $this->D->query_array($sql);
	}

	/*
	 * create on 2012-07-26
	 * by wall
	 * @param $sqlstr 查询条件
	 * 销售订单序时簿查询（财务用）
	 * */
	public function financial_get_sales_order($sqlstr) {
		$sql = 'select a.order_id as order_no,a.cdate,sold_way,sku,product_name,name,quantity,coin_code,stage_rate, price as sumprice,a.extends,order_id from process as a ';
		$sql .= ' left join esse as b on a.provider_id=b.id ';
		$sql .= ' where b.type=2 and statu="0" and (protype="售出" or protype="重发") and isover="N" '.$sqlstr.' order by a.order_id desc';
		return $this->D->query_array($sql);
	}

 	/*
	 * create on 2012-07-30
	 * by wall
	 * @param $sqlstr 查询条件
	 * 调仓单序时簿查询（财务用）
	 * */
	public function financial_get_material_allocation($sqlstr) {
		$sql = ' select a.order_id as order_no,a.mdate,sku,product_name,b.name as outname,c.name as inname,quantity,"CNY" as coin_code1,price2 as cost1,comment2 from process as a ';
		$sql .= ' left join esse as b on a.provider_id=b.id ';
		$sql .= ' left join esse as c on a.receiver_id=c.id ';
		$sql .= ' where b.type=2 and c.type=2 and statu="3" and property="转仓单" and isover="N" '.$sqlstr.' order by a.order_id desc';
		return $this->D->query_array($sql);
	}

 	/*
	 * create on 2012-07-30
	 * by wall
	 * @param $sqlstr 查询条件
	 * 其他出库序时簿查询（财务用）
	 * */
	public function financial_get_other_libraries($sqlstr) {
		$sql = ' select a.order_id as order_no,a.mdate,sku,product_name,name,quantity,"CNY" as coin_code1,price2 as cost1,a.comment from process as a ';
		$sql .= ' left join esse as b on a.provider_id=b.id ';
		$sql .= ' where b.type=2 and property="出仓单" and protype="其它" and output="1" '.$sqlstr.' order by a.order_id desc';
		return $this->D->query_array($sql);
	}

 	/*
	 * create on 2012-07-30
	 * by wall
	 * @param $sqlstr 查询条件
	 * 其他入库序时簿查询（财务用）
	 * */
	public function financial_get_other_storage($sqlstr) {
		$sql = ' select a.order_id as order_no,a.mdate,sku,product_name,name,quantity,"CNY" as coin_code1,price2 as cost1,a.comment from process as a ';
		$sql .= ' left join esse as b on a.receiver_id=b.id ';
		$sql .= ' where b.type=2 and property="进仓单" and protype="其它" and active="1" and input="1" '.$sqlstr.' order by a.order_id desc';
		return $this->D->query_array($sql);
	}
    /**
	 * create on 2012-08-22
	 * by wall
	 * @param $sqlstr 报表过滤条件
	 * @param $order 排序条件
	 * sku销售报表用
	 * */
	public  function select_sku_sold_list($sqlstr, $order) {
		$sql = 'select sku,sold_id,count(sku) as totalprocess,sum(quantity) as totalquantity,sum(price*rate/100) as totalprice,sum(price2*quantity) as cost,sum(quantity2) as returnsum,sum(price3*rate3/100) as returnprice from (';
		$sql .= ' select p1.pid,sku,quantity,price,price2,coin_code,rate,sold_id,0 as quantity2,0 as price3,0 as coin_code3,0 as rate3 from process as p1 ';
		$sql .= ' left join exchange_rate as er on p1.coin_code=er.code ';
		$sql .= ' where active="1" and output="1" and protype= "售出" and p1.stage_rate=er.stage_rate '.$sqlstr;
		$sql .= ' union all ';
		$sql .= ' select p1.pid,p1.sku,0 as quantity,0 as price,0 as price2,0 as coin_code,0 as rate,0 as sold_id,p2.quantity as quantity2,0 as price3,0 as coin_code3,0 as rate3 from process as p1 ';
		$sql .= ' left join process as p2 on p1.id=p2.detail_id  ';
		$sql .= ' where p1.active="1" and p1.output="1" and p1.protype= "售出" and p2.protype="退货" '.$sqlstr;
		$sql .= ' union all ';
		$sql .= ' select p1.pid,p1.sku,0 as quantity,0 as price,0 as price2,0 as coin_code,0 as rate,0 as sold_id,0 as quantity2,p2.price,p2.coin_code,rate as rate3 from process as p2 ';
		$sql .= ' left join process as p1 on p1.id=p2.detail_id ';
		$sql .= ' left join exchange_rate as er on p2.coin_code=er.code  ';
		$sql .= ' where p1.active="1" and p1.output="1" and p1.protype= "售出" and p2.property="退款单" and er.stage_rate=p2.stage_rate '.$sqlstr;
		$sql .= ') temp group by sku order by '.$order.'sku asc';
		return $this->D->query_array($sql);
	}
    
    /**
	 * create on 2014-02-14
	 * by jerry
	 * @param $sqlstr 报表过滤条件 ,如果选择销售账号，销售账号必须分组，在统计单个账号产品收入时，必须得分组统计，留到后期改动
	 * @param $order 排序条件
	 * sku销售报表用
	 * */
	public  function select_sku_sold_list_slod_id($sqlstr, $order) {
		$sql = 'select sku,sold_id,count(sku) as totalprocess,sum(quantity) as totalquantity,sum(price*rate/100) as totalprice,sum(price2*quantity) as cost,sum(quantity2) as returnsum,sum(price3*rate3/100) as returnprice from (';
		$sql .= ' select p1.pid,sku,quantity,price,price2,coin_code,rate,sold_id,0 as quantity2,0 as price3,0 as coin_code3,0 as rate3 from process as p1 ';
		$sql .= ' left join exchange_rate as er on p1.coin_code=er.code ';
		$sql .= ' where active="1" and output="1" and protype= "售出" and p1.stage_rate=er.stage_rate '.$sqlstr;
		$sql .= ' union all ';
		$sql .= ' select p1.pid,p1.sku,0 as quantity,0 as price,0 as price2,0 as coin_code,0 as rate,0 as sold_id,p2.quantity as quantity2,0 as price3,0 as coin_code3,0 as rate3 from process as p1 ';
		$sql .= ' left join process as p2 on p1.id=p2.detail_id  ';
		$sql .= ' where p1.active="1" and p1.output="1" and p1.protype= "售出" and p2.protype="退货" '.$sqlstr;
		$sql .= ' union all ';
		$sql .= ' select p1.pid,p1.sku,0 as quantity,0 as price,0 as price2,0 as coin_code,0 as rate,0 as sold_id,0 as quantity2,p2.price,p2.coin_code,rate as rate3 from process as p2 ';
		$sql .= ' left join process as p1 on p1.id=p2.detail_id ';
		$sql .= ' left join exchange_rate as er on p2.coin_code=er.code  ';
		$sql .= ' where p1.active="1" and p1.output="1" and p1.protype= "售出" and p2.property="退款单" and er.stage_rate=p2.stage_rate '.$sqlstr;
		$sql .= ') temp where sold_id <>0 group by sku,sold_id order by '.$order.'sku asc';
		return $this->D->query_array($sql);
	}


	/**
	 * create on 2013-12-31
	 * by jerry
	 * @param $sqlstr 报表过滤条件
	 * @param $order 排序条件
	 * 供应商sku销售报表用
	 * */
	public  function select_skusupplier_sold_list($sqlstr, $order) {
		$sql = 'select sku,sold_id,count(sku) as totalprocess,sum(quantity) as totalquantity,sum(price*rate/100) as totalprice,sum(price2*quantity) as cost,sum(quantity2) as returnsum,sum(price3*rate3/100) as returnprice from (';
		$sql .= ' select p1.pid,sku,quantity,price,price2,coin_code,rate,sold_id,0 as quantity2,0 as price3,0 as coin_code3,0 as rate3 from process as p1 ';
		$sql .= ' left join exchange_rate as er on p1.coin_code=er.code JOIN prc_esse ON prc_esse.pid=p1.pid JOIN esse ON esse.id=eid ';
		$sql .= ' where active="1" and output="1" and protype= "售出" and p1.stage_rate=er.stage_rate '.$sqlstr;
		$sql .= ' union all ';
		$sql .= ' select p1.pid,p1.sku,0 as quantity,0 as price,0 as price2,0 as coin_code,0 as rate,0 as sold_id,p2.quantity as quantity2,0 as price3,0 as coin_code3,0 as rate3 from process as p1 ';
		$sql .= ' left join process as p2 on p1.id=p2.detail_id JOIN prc_esse ON prc_esse.pid=p1.pid JOIN esse ON esse.id=eid ';
		$sql .= ' where p1.active="1" and p1.output="1" and p1.protype= "售出" and p2.protype="退货" '.$sqlstr;
		$sql .= ' union all ';
		$sql .= ' select p1.pid,p1.sku,0 as quantity,0 as price,0 as price2,0 as coin_code,0 as rate,0 as sold_id,0 as quantity2,p2.price,p2.coin_code,rate as rate3 from process as p2 ';
		$sql .= ' left join process as p1 on p1.id=p2.detail_id ';
		$sql .= ' left join exchange_rate as er on p2.coin_code=er.code JOIN prc_esse ON prc_esse.pid=p1.pid JOIN esse ON esse.id=eid ';
		$sql .= ' where p1.active="1" and p1.output="1" and p1.protype= "售出" and p2.property="退款单" and er.stage_rate=p2.stage_rate '.$sqlstr;
		$sql .= ') temp group by sku order by '.$order.'sku asc';
		return $this->D->query_array($sql);
	}

	public function get_moddetail($sqlstr){

		$sql = 'SELECT p1.isover,p2.id,p2.quantity, p2.statu FROM process AS p1 LEFT JOIN process p2 ON p1.id = p2.detail_id where 1 '.$sqlstr;
		return $this->D->query_array($sql);
	}

	/**
	*
	* 发货明细表
	*/
	public function get_outbondlist($sqlstr){

	 	$sql = 'SELECT s.wayname AS sold_way,p.rdate,p.cdate,p.order_id,p.sold_id,p.fid,p.deal_id,p.sku,p.product_name,p.quantity,p.price,p.extends,
                p.cuser,p.comment2,p.buyer_id,p.pid,p.provider_id FROM `process` p LEFT JOIN sold_way s ON p.sold_way=s.id
                WHERE active="1" AND output="1" AND (protype= "售出" OR protype="重发") '.$sqlstr.' order by order_id desc';
		return $this->D->query_array($sql);

	 }

    /**
	* @author Jerry
	* 出库订单售出
    * @creat on 2013-10-22
	*/
	public function get_soldlist($sqlstr){

	 	$sql = 'select s.wayname as sold_way,p.rdate,p.cdate,p.order_id,p.sold_id,p.fid,p.deal_id,p.sku,p.product_name,p.quantity,p.price,p.extends,p.cuser,p.comment2,p.buyer_id,p.pid,p.provider_id from `process` p left join sold_way s on p.sold_way=s.id where active="1" and output="1"  and protype= "售出" '.$sqlstr.' order by order_id desc';
		return $this->D->query_array($sql);

	 }
     
     /**
	* @author Jerry
	* 出库订单售出统计
    * @creat on 2013-10-22
	*/
	public function get_countsold($sqlstr){

	 	$sql = 'select COUNT(distinct order_id)as order_id,SUM(quantity) as quantity from process p left join sold_way s on p.sold_way=s.id where active="1" and output="1"  and protype= "售出" '.$sqlstr.'';
		return $this->D->query_array($sql);

	 }
	/**
	 * by hanson 库龄查询
	 * create on 2012-10-10
	 */
	public function get_storge_age_bystocks($sqlstr, $combine){

		$commomstr	= ' AND active="1" AND input="1" AND receiver_id>0';
		$onetime	= date('Y-m-d H:i:s',strtotime("-30days"));
		$twotime	= date('Y-m-d H:i:s',strtotime("-60days"));
		$thrtime	= date('Y-m-d H:i:s',strtotime("-90days"));

		/*调拨单不继承库龄*/
		if($combine == '1'){
			$sql = 'SELECT c.cost2, c.coin_code, sum(one) as one,sum(two) as two,sum(three) as three,sum(four) as four,sum(outs) as outs,temp.sku, e.name as warehouse,temp.wid FROM (';
			$sql.= ' SELECT pid,quantity as one , 0 as two, 0 as three, 0 as four ,0 as outs, sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate >= "'.$onetime.'"';//3个月内进仓数量
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,quantity as two,0 as three,0 as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate>="'.$twotime.'" and rdate<="'.$onetime.'"';//查3-6月
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, quantity as three ,0 as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate>="'.$thrtime.'" and rdate<="'.$twotime.'"';//查6-9月
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, 0 as three, quantity as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate<="'.$thrtime.'"';//查9月以前
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, 0 as three, 0 as four, quantity as outs, sku, provider_id as wid FROM process WHERE active="1" and output="1"  and provider_id>0 ';//查总出仓数量
			$sql.= ')temp ';
			$sql.= ' left join product_cost c on c.pid=temp.pid ';
			$sql.= ' left join esse e on e.id=temp.wid ';
			$sql.= ' WHERE 1 '.$sqlstr.'  group by temp.sku,temp.wid';
		}
        /*库龄分析汇总*/
        elseif($combine == '2'){
			$sql = 'SELECT c.cost2, c.coin_code, sum(one) as one,sum(two) as two,sum(three) as three,sum(four) as four,sum(outs) as outs,temp.sku, e.name as warehouse,temp.wid FROM (';
			$sql.= ' SELECT pid,quantity as one , 0 as two, 0 as three, 0 as four ,0 as outs, sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate >= "'.$onetime.'"';//3个月内进仓数量
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,quantity as two,0 as three,0 as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate>="'.$twotime.'" and rdate<="'.$onetime.'"';//查3-6月
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, quantity as three ,0 as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate>="'.$thrtime.'" and rdate<="'.$twotime.'"';//查6-9月
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, 0 as three, quantity as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate<="'.$thrtime.'"';//查9月以前
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, 0 as three, 0 as four, quantity as outs, sku, provider_id as wid FROM process WHERE active="1" and output="1"  and provider_id>0 ';//查总出仓数量
			$sql.= ')temp ';
			$sql.= ' left join product_cost c on c.pid=temp.pid ';
			$sql.= ' left join esse e on e.id=temp.wid ';
			$sql.= ' WHERE 1 '.$sqlstr.'  group by temp.wid,temp.sku';
		}

		/*调拨单继承库龄查总仓*/
		elseif(empty($combine)){
			$commomstr.= ' AND property!="转仓单" ';
			$sql = 'SELECT c.cost2, c.coin_code, sum(one) as one,sum(two) as two,sum(three) as three,sum(four) as four,sum(outs) as outs,temp.sku, e.name as warehouse,temp.wid FROM (';
			$sql.= ' SELECT pid,quantity as one , 0 as two, 0 as three, 0 as four ,0 as outs, sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate >= "'.$onetime.'"';//3个月内进仓数量
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,quantity as two,0 as three,0 as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate>="'.$twotime.'" and rdate<="'.$onetime.'"';//查3-6月
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, quantity as three ,0 as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate>="'.$thrtime.'" and rdate<="'.$twotime.'"';//查6-9月
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, 0 as three, quantity as four,0 as outs,sku,receiver_id as wid FROM process WHERE 1 '.$commomstr.' AND rdate<="'.$thrtime.'"';//查9月以前
			$sql.= ' union all';
			$sql.= ' SELECT pid,0 as one,0 as two, 0 as three, 0 as four, quantity as outs, sku, provider_id as wid FROM process WHERE active="1" and output="1"  and provider_id>0 AND property!="转仓单" ';//查总出仓数量
			$sql.= ')temp ';
			$sql.= ' left join product_cost c on c.pid=temp.pid ';
			$sql.= ' left join esse e on e.id=temp.wid ';
			$sql.= ' WHERE 1 '.$sqlstr.'  group by temp.sku';
		}

		return $this->D->query_array($sql);

	}

	/**
	 * by hanson 2012-10-11 查某SKU在某仓库的进出明细
	 */
	public function get_process_detail($sqlstr){

		$sql = 'select p.ruser,p.rdate,ep.name as pname,er.name as rname,provider_id,receiver_id,sku,quantity,order_id,output,input,property,protype from process p';
		$sql.= ' left join esse as ep on ep.id=p.provider_id ';
		$sql.= ' left join esse as er on er.id=p.receiver_id ';
		$sql.= ' where 1 '.$sqlstr;

		return $this->D->query_array($sql);
	}

	/**
	 * by hanson 2012-10-16 不良品调拨明细
	 */
	public function get_badlist($sqlstr){

		$sql = 'select p1.id,p1.order_id,p2.order_id as preorder_id,p1.sku,p1.product_name,e.name as warehouse,p1.quantity,p1.comment2,p1.ruser,p1.rdate from process p1 ';
		$sql.= ' left join process p2 on p1.detail_id = p2.id ';
		$sql.= ' left join esse e on e.id = p1.provider_id ';
		$sql.= ' where p1.property="转仓单" and p1.receiver_id="" '.$sqlstr;
		$sql.= ' order by p1.rdate desc';

		return $this->D->query_array($sql);
	}


	/**
	 * by hanson 2012-10-17 不良品库存查询
	 */
	public function get_badstatu($sqlstr){

		$sql = 'select p.sku,d.product_name,e.name,sum(p.quantity) as sums , provider_id from process p ';
		$sql.= ' left join product d on d.pid=p.pid';
		$sql.= ' left join esse e on e.id=p.provider_id';
		$sql.= ' where property="转仓单"  and receiver_id="" '.$sqlstr;
		$sql.= 'group by p.sku,p.provider_id';

		return $this->D->query_array($sql);
	}

	/**
	 * by hanson 2012-10-19 发货汇总表显示金碟SKU
	 */
	public function get_sumsells($order_id){

		$sql = 'select sku, s.sku_code, sum(quantity) as num, product_name from process p left join sku_alias as s on (p.pid=s.pid and s.sold_way="金碟") where order_id in('.$order_id.') group by sku order by sku asc';
		return $this->D->query_array($sql);
	}
    
    /**
     * @title 亚马逊出库单,组合的sku分解成单个sku做单
     * @author Jerry
     * @create on 2013-4-1
     */ 
    public function output_child_sku($sqlstr){
        $sql = 'select p.sku,p.product_name,p.pid,product_cost.cost1,product_cost.cost3,s.quantity,s.child_pid from product as p left join  sku_assembly as s on p.pid=s.child_pid left join product_cost on product_cost.pid=p.pid where 1 '.$sqlstr;
        //echo $sql;
        return $this->D->query_array($sql);
    }
    public function get_childsku_cost3($pid){
        $sql    = 'SELECT SUM(cost3) as cost3 FROM product AS p LEFT JOIN  sku_assembly AS s ON p.pid=s.child_pid LEFT JOIN product_cost ON product_cost.pid=p.pid WHERE s.pid='.$pid;
        $sql   .=' GROUP BY s.pid';
        //die($sql);
        return $this->D->query_array($sql);
    }
    
    /**
     *@title b2b客户报表 
     *@author Jerry
     *@create on 2013-06-19 
     */
     public function get_b2bcorpbslinfo($sqlstr){
        $sql = 'select sku,order_id,rdate,contactname,contactemail,contacttel,corpname from process as p  join b2bcorpbsl as b on p.b2b_customers = b.id where 1  and (protype="售出" or protype="重发") and isover="N" '.$sqlstr.'';
        return $this->D->query_array($sql);
     }
     
     /**
      * @title 采购入库，反入库，判断库存
      * @author Jerry
      * @create on 2013-12-11
      */
     public function get_quantity($sqlstr)
     {
        $sql = 'SELECT temp.sku,temp.wid,p.product_name,e.name AS warename,SUM(quantity) AS sums
        FROM ( SELECT sku,receiver_id AS wid,quantity FROM PROCESS WHERE active="1" AND input="1" AND receiver_id>0 
        UNION ALL SELECT sku,provider_id AS wid,-quantity AS quantity FROM PROCESS WHERE active="1" AND output="1" 
        AND provider_id>0 UNION ALL SELECT sku,provider_id AS wid,-quantity AS quantity FROM PROCESS WHERE output="0" 
        AND isover="N" AND (property="出仓单" OR property="转仓单") UNION ALL SELECT sku,receiver_id AS wid,
        0 AS quantity FROM PROCESS WHERE active="1"
        AND input="0" AND receiver_id>0 UNION ALL SELECT sku,receiver_id AS wid,0 AS quantity FROM PROCESS 
        WHERE property="采购单" AND isover="N" UNION ALL SELECT sku,provider_id AS wid,0 AS quantity FROM PROCESS WHERE output="1" AND active="1" 
        AND (protype="售出" OR protype="重发") UNION ALL SELECT sku,provider_id AS wid,0 AS quantity FROM PROCESS WHERE output="1" AND active="1" AND (protype="售出" OR protype="重发")
        AND DATE_SUB(CURDATE(),INTERVAL 10 DAY) < DATE(mdate) )temp 
        LEFT JOIN product AS p ON p.sku=temp.sku LEFT JOIN esse AS e ON e.id=temp.wid
        WHERE 1=1   '.$sqlstr.' GROUP BY temp.sku,temp.wid';
        return $this->D->query_array($sql);
     }
     
     /**
      * @title 物料调拨运费反写
      * @author Jerry
      * @create on 2014-1-17
      */ 
     public function get_shipping_farerewrite(){
        $sql = 'select temp.total as shipping_fee,p.id,p.comment2,p.sum_product_size,p.sum_product_weight from process as p  left join esse as e on p.receiver_id=e.id left join esse as e2 on p.provider_id=e2.id left join (select shipping,track_no,sum(total) as total from shipping_farerewrite group by track_no)temp on temp.track_no=p.comment2 where 1 and p.receiver_id>0 and statu="3" and property="转仓单" and isover="N" ';
        return $this->D->get_all_sql($sql);
     }
     
     /**
      * @title 获取sku在每个仓库运费
      * @author Jerry
      * @create on 2014-1-17
      */
     public function get_sku_house_shipping(){
        $sql = ' SELECT pid,e.id,SUM(shipping_farerewrite)/COUNT(sku) AS shipping FROM PROCESS 
                LEFT JOIN esse AS e ON process.receiver_id = e.id 
                WHERE 1 AND receiver_id > 0 AND statu = "3"
                AND property = "转仓单" AND isover = "N" AND process.shipping_farerewrite<>""
                GROUP BY sku,receiver_id ORDER BY sku DESC';
        return $this->D->get_all_sql($sql);
     }
     
    /*
     * @title 当不选择仓库时候，列出sku所有仓库对应的运费
     * @author Jerry
     * @create on 2014-1-20
     */ 
    public function get_sku_all_house_shipping($sqlstr){
        $sql = 'SELECT sku,`name`,shipping FROM product LEFT JOIN sku_shipping ON product.pid=sku_shipping.pid  LEFT JOIN esse ON esse.id=sku_shipping.eid WHERE 1 AND NAME<>"" AND shipping<>"" and  '.$sqlstr.' ORDER BY name';
        return $this->D->get_all_sql($sql);
    }
    
    //获取市场指导价的总计
    public function get_total_market_price($sqlstr)
    {
        $sql ='SELECT sum(market_price*quantity) as total_market_price FROM process '.$sqlstr;
        
        return $this->D->get_one_sql($sql);
    }
}
?>
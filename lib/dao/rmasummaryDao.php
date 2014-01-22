<?php

/**
 *@title RMA汇总  
 */
 
 class rmasummaryDao extends D{
    
    /**
     * @RMA汇总列表
     * @author Jerry
     * @create on 2012-11-30
     */ 
    function rmasummarylist($strsql){
        $sql = 'SELECT  p.order_id,p.product_name,p.sold_id,p.sold_way,tmp.cdate,tmp.ruser,tmp.rdate,tmp.comment3,tmp.sku,SUM(returns) as returns,SUM(replaces) as replaces ,SUM(tmp.price) as price,tmp.coin_code,tmp.stage_rate FROM (
        SELECT  sku,detail_id,0  AS returns,0 AS replaces,price,coin_code,stage_rate,cdate,rdate,comment3,ruser FROM  `process`  WHERE  property="退款单"
        UNION ALL
        SELECT  sku,detail_id,quantity AS  returns,0 AS replaces ,0 AS price,coin_code,0 as stage_rate,cdate,rdate,comment3,ruser  FROM  `process`  WHERE  protype="退货"
        UNION ALL 
        SELECT  sku,detail_id,0 AS returns,quantity AS replaces,0 AS price ,coin_code,0 as stage_rate,cdate,rdate,comment3,ruser FROM  `process` WHERE  protype="重发"
        )tmp 
        JOIN `process`  p ON p.id=tmp.detail_id   '.$strsql.'  GROUP BY tmp.detail_id  ORDER BY p.order_id DESC ';        
        return $this->D->query_array($sql);         
    }
    
    /**
     * @RMA汇总列表统计订单数
     * @author Jerry
     * @create on 2013-10-23
     */ 
    function rmasummarylistcount($strsql){
        $sql = 'SELECT  p.order_id FROM (
        SELECT  sku,detail_id,0  AS returns,0 AS replaces,price,coin_code,stage_rate,cdate,rdate,comment3,ruser FROM  `process`  WHERE  property="退款单"
        UNION ALL
        SELECT  sku,detail_id,quantity AS  returns,0 AS replaces ,0 AS price,coin_code,0 as stage_rate,cdate,rdate,comment3,ruser  FROM  `process`  WHERE  protype="退货"
        UNION ALL 
        SELECT  sku,detail_id,0 AS returns,quantity AS replaces,0 AS price ,coin_code,0 as stage_rate,cdate,rdate,comment3,ruser FROM  `process` WHERE  protype="重发"
        )tmp 
        JOIN `process`  p ON p.id=tmp.detail_id   '.$strsql.'  GROUP BY tmp.detail_id ';  
        //echo $sql;     
        return $this->D->query_array($sql);         
    }
    
    /**
     * @RMA明细列表
     * @author Jerry
     * @create on 2012-11-30
     */ 
    function rmadetailslist($strsql){
        $sql  = 'select p.order_id,p.sold_way,p.sold_id,p.protype,p.property,p.sku,p.quantity,p.price,p.cuser,p.cdate,p.fid,p.comment,p.comment3,p.ruser,p.rdate,p.ispay,p.input,esse.`name`,p.coin_code from `process` as p  left join esse on esse.id=p.receiver_id   where 1 '.$strsql.' and (p.protype="退货" or p.property="退款单") ';
        $sql .= ' order by cdate desc';
        return $this->D->query_array($sql);
    }
    
     /**
     * @出库单-RMA明细列表
     * @author Jerry
     * @create on 2014-1-2
     */ 
    function process_shipment_rmadetailslist($strsql){
        $sql  = 'select p.order_id,p.sold_way,p.sold_id,p.protype,p.property,p.sku,p.quantity,p.price,p.cuser,p.cdate,p.fid,p.comment,p.comment3,p.ruser,p.rdate,p.ispay,p.input,esse.`name`,p.coin_code from `process` as p  left join esse on esse.id=p.receiver_id   where 1 '.$strsql.' and (p.protype="退货" or p.property="退款单") ';
        $sql .= ' order by cdate desc';
        return $this->D->get_all_sql($sql);
    }
    
    
 }
 
?>
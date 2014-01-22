<?php
/**
 *@title 仓库运费 
 */
class warehouseshippingDao extends D{
    
    /**
     * @title 仓库运费
     * 
     */ 
    public function get_list($sqlstr)
    {
        $sql = 'SELECT w.id,e.name,w.warehouse,w.shipping,w.checktime FROM esse e  JOIN warehouseshipping  w ON e.id=w.warehouse AND e.type=2 where 1 '.$sqlstr;
        $sql .= ' order by w.id,w.checktime desc ';
        return $this->D->query_array($sql);
    }
}
?>
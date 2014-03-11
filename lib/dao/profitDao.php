<?php
class profitDao extends D{
    /**
     * @title 利润报表(日期)
     * 
     */ 
    public function get_list($sqlstr,$sql)
    {
        $sql = 'SELECT item_name,account_code,'.$sql.'
                FROM (SELECT  item_name,profit_date,costs,account_code,sort,brand,type_account.id FROM profit  
                LEFT JOIN type_account ON profit.account_id=type_account.id 
                LEFT JOIN item         ON item.id=profit.item_id 
                WHERE 1 '.$sqlstr.') AS t GROUP BY item_name ORDER BY sort asc';
        return $this->D->query_array($sql);
    }
    
    /**
     * @title 利润报表（账号）
     * @author jerry
     * @create on 2014-2-17
     */ 
    public function get_report_profit_by_account($sqlstr,$sql){
        $sql = 'SELECT item_name,sort,SUM(costs) as sumcosts,'.$sql.'
        FROM (SELECT item_name,profit_date,costs,sort,account_code,type_account.id FROM profit LEFT JOIN type_account ON profit.account_id=type_account.id 
        LEFT JOIN item ON item.id=profit.item_id WHERE 1  '.$sqlstr.') AS t GROUP BY sort ORDER BY sort asc';
        return $this->D->query_array($sql);
    }
    
    /**
     * @title 获取利润明细报表
     * @author Jerry
     * @create on 2014-01-27
     */ 
    public function get_detail_list($sqlstr)
    {
        $sql = 'SELECT profit.id,item_name,account_code,profit_date,sort,costs,profit.fstcreate FROM profit LEFT JOIN type_account ON profit.account_id=type_account.id LEFT JOIN item ON item.id=profit.item_id  where 1  '.$sqlstr.'';
        $sql .= 'order by sort,account_code asc ';
        return $this->D->query_array($sql);
    }
    
}
?>
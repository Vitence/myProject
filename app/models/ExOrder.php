<?php
class ExOrder extends ModelBase{
    
    public function getSource()
    {
        return 'ex_order';
    }
    
    public static function itemsByUserId($userId){
        if($userId <= 0 ){
            return false;
        }
        $where['user_id'] = $userId;
        $items = parent::select($where);
        
        return $items ? $items->toArray() : [];
    }
    
    public static function getMaxOrderPrice($currencyId){
        $where['currency_id'] = $currencyId;
        $where['type'] = 1;
        $order = 'pay_at desc';
        $item = parent::findRow($where,null,$order);
        return $item;
    }
}
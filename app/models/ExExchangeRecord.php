<?php

class ExExchangeRecord extends ModelBase{

    public function getSource()
    {
        return 'ex_exchange_record';
    }
    
    public static function itemsByUserId($userId){
        if($userId <= 0 ){
            return false;
        }
        $where['user_id'] = $userId;
        
        $items = parent::select($where);
        
        return $items ? $items->toArray() : [];
    }
    
    public static function itemsByUserIdAndType($userId,$currencyId){
        if($userId <= 0 ){
            return false;
        }
        $where['user_id'] = $userId;
        $where['currency_id'] = $currencyId;
        
        $items = parent::findRow($where);
        
        return $items ? $items->toArray() : [];
    }
}
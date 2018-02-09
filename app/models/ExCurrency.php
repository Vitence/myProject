<?php
class ExCurrency extends ModelBase{

    public static function itemsByIdArr($idArr = []){
        if(empty($idArr)){
            return [];
        }
        
        $where['id'] = array('IN',$idArr);
        
        $items = parent::select($where);
        
        return $items ? $items->toArray() : [];
    }
}
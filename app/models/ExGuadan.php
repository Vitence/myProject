<?php
class ExGuadan extends ModelBase{
    
    public function getSource()
    {
        return 'ex_guadan';
    }
    
    public static function itemByPrice($price,$handle,$type){
       
        $where['price'] = $price;
        $where['type']  = $handle;
        $where['currency_id']  = $type;
        $where['status']  = 0;
        $item = parent::findRow($where);
        
        return $item ? $item->toArray() : [];
    }
    
    
    public static function updateById($id,$data){
        $where['id'] = $id;
        $data['update_at'] = \Util\common::getDataTime();
        return parent::updataData($where,$data);
    }
}
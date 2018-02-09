<?php
use Util\common;
class ExUsers extends ModelBase{

    const NORMAL = 1;
    const LOCK   = 2;
    const DELETE = 3;
    
    public function getSource()
    {
        return 'ex_users';
    }
    
    public static function addUser($data){
        if(empty($data)){
            return false;
        }
        $data['register_at'] = common::getDataTime();
        
        $obj = new ExUsers();
        
        $add = parent::addData($obj,$data);
        return $add;
    }
    
    public static function saveLastLogin($id){
        if((int)$id <= 0){
            return false;
        }
        
        $where['id'] = $id;
        
        $data['last_login_at'] = common::getDataTime();
        
        $save = parent::updataData($where,$data);
        
        return $save;
    }
    
    
    public static function saveUserPassword($id,$password){
        if((int)$id <= 0){
            return false;
        }
        
        $where['id'] = $id;
        
        $data['password'] = md5(md5($password));
        
        $save = parent::updataData($where,$data);
        
        return $save;
    }
    
    
    public static function saveTradingPassword($id,$password){
        if((int)$id <= 0){
            return false;
        }
        
        $where['id'] = $id;
        
        $data['trading_password'] = md5(md5($password));
        
        $save = parent::updataData($where,$data);
        
        return $save;
    }
    
    public static function itemByEmail($email){
        $where['email']  = $email;
        $where['status'] = ['!=',self::DELETE];
    
        $item = parent::findRow($where);
    
        return $item ? $item->toArray() : [];
    }
    
    public static function itemById($id){
        if((int)$id <= 0){
            return false;
        }
    
        $where['id'] = $id;
        $where['status'] = ['!=',self::DELETE];
    
        $item = parent::findRow($where);
    
        return $item ? $item->toArray() : [];
    }
    
    public static function saveDataById($id,$data){
        if((int)$id <= 0){
            return false;
        }
        
        $where['id'] = $id;
        
        $save = parent::updataData($where,$data);
        
        return $save;
    }
}
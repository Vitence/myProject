<?php
class UserLogic{

    public static function checkPassword($userInfo, $password){
        if(empty($userInfo)){
            return false;
        }
        
        $user = ExUsers::itemById($userInfo['id']);
        
        if(empty($user)){
            return false;
        }
        
        if($user['password'] != md5(md5($password))){
            return false;
        }
        
        return true;
    }
    
    public static function checkTradingPassword($userInfo, $password){
        if(empty($userInfo)){
            return false;
        }
        
        $user = ExUsers::itemById($userInfo['id']);
        
        if(empty($user)){
            return false;
        }

        //第一次没密码 ，待定 先通过
        if($user['trading_password'] == "" || $user['trading_password'] == null){
            return true;
        }
        
        if($user['trading_password'] != md5(md5($password))){
            return false;
        }
        
        return true;
    }
}
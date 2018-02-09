<?php
/**
 * Created by PhpStorm.
 * User: huangshucai
 * Date: 2017/12/18
 * Time: 15:53
 */
namespace Util;
class Check{
    
    /**
     * 验证密码（目前密码通过sha传输所以暂时步验证）
     * @param $password
     * @return bool
     * @throws \Exception
     */
    public static function verifyPassword($password){
        $preg = "/((?=.*\d)(?=.*\D)|(?=.*[a-zA-Z])(?=.*[^a-zA-Z]))^[^\s]{8,16}$/";
        if (!preg_match( $preg, $password)){
            throw new \Exception(ErrorCode::getMsgName(ErrorCode::PASSWORD_FORMAT_ERROR),ErrorCode::PASSWORD_FORMAT_ERROR);
        }
        return true;
    }
    

    
    /**
     * 邮箱有限格式
     * @param $email
     * @return bool
     * @throws \Exception
     */
    public static function VerifyEmail($email){
        if (!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email)) {
            return false;
        }
        return true;
    }
    
}
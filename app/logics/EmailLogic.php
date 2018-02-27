<?php
class EmailLogic{
    
    const REGISTER = 1;
    const RESET_PASSWORD = 2;
    const SIGN = 'FLIDJ12ASLDJD2ASD01';
    public static function sendMail($data,$type = self::REGISTER){
        $mailServer = \Phalcon\Di::getDefault()->getShared('mailer');
        if($type == self::REGISTER){
            $mailServer->Subject = '注册激活';
        }else{
            $mailServer->Subject = '找回密码';
        }
        $content = self::getEmailContent($data,$type);
        $mailServer->Body = $content;
        $mailServer->CharSet='utf-8';
        $mailServer->AddAddress($data['email'], '亲爱的网站会员');
        $mailServer->Send();
        if(!empty($mailServer->{'ErrorInfo'})){
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * 邮件内容
     * @param     $data
     * @param int $type
     * @return string
     */
    public static function getEmailContent($data, $type = self::REGISTER){
        $data['sign'] = self::SIGN;
        $dataBase = base64_encode(json_encode($data));
        $content = '';
        switch ($type){
            case self::REGISTER:
                $content = '<a href="'.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/user/active?d='.$dataBase.'">点击激活</a>，此邮件有效期24小时';
                break;
            case self::RESET_PASSWORD:
                $content = '请复制此链接到浏览器修改密码  '.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/password/set?d='.$dataBase.'，此邮件有效期24小时';
                break;
            default:
                break;
        }
        
        return $content;
    }
}
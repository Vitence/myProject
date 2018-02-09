<?php
namespace Util;
use Service\Verify\CaptchaBuilder;
class common{
    /**
     * @return array  验证码二进制码  验证码图片的值
     * code    图片验证码☞
     * codeUrl 图片验证面二进制地址
     * 获取验证码
     */
    public static function getCaptcha(){
        $codeNumber = rand(1000,9999);
        $builder = new CaptchaBuilder((string)$codeNumber);
        $builder->setBackgroundColor(255,255,255); //背景色
        $builder->setFontColor(100,100,100); //自己添加的方法 字体颜色
        $builder->setIgnoreAllEffects(true); //是否去出干扰
        $builder->build(100,50); //验证码大小
        $imgSrc = $builder->inline();  //验证码图片二进制
        return array('code' => $codeNumber,'codeUrl' => $imgSrc);
    }
    
    /**
     * 当前日期
     * @return false|string
     */
    public static function getDataTime(){
        return date("Y-m-d H:i:s",time());
    }
}
<?php
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Di\FactoryDefault;
use PHPMailer\Mailer;
$di = new FactoryDefault();
$di->setShared('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

$di->setShared('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        '.html' => 'Phalcon\Mvc\View\Engine\Php'
    ));
    return $view;
});


$di->setShared('db', function () use ($config) {
    $dbConfig = $config->database->toArray();
    $adapter = $dbConfig['adapter'];
    unset($dbConfig['adapter']);
    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;
    $connection = new $class($dbConfig);
    return $connection;
});


$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

$di->set('flash', function () {
    return new Flash(array(
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
});

$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});


$di->setShared('debugConfig',function(){
    $config =  include_once APP_PATH . '/app/config/debugConfig.php';
    return $config;
});


$di->setShared('logger', function () use($config){
    if($config->log->LOG_RECORD){
        if(!file_exists($config->log->LOG_DIR)){
            mkdir($config->log->LOG_DIR,'0777',true);
        }
        $logger = '';
        switch ($config->log->LOG_TYPE){
            case 'File':
                $logname = date('Y-m-d').'.log';
                $logger =  new FileAdapter($config->log->LOG_DIR.$logname);
                break;
            default :  
        }
        return $logger;
    }
});


$di->set('mailer', function(){
    $config = include_once APP_PATH . '/app/config/email.php';
    $emailConfig = $config['emailConfig'];
    $mailer = new Mailer();                             //实例化
    $mailer->IsSMTP();                                  // 启用SMTP
    $mailer->Host=$emailConfig['mail_host'];              //smtp服务器的名称                      例:smtp.163.com
    $mailer->SMTPAuth = $emailConfig['mail_smtpauth'];    //设置SMTP是否需要密码验证,true表示需要 例:true
    $mailer->Port = $emailConfig['mail_port'];            //设置SMTP是否需要密码验证,true表示需要 例:true
    $mailer->Username = $emailConfig['mail_username'];    //邮箱用户名                               例:sun_rain_0533@163.com
    $mailer->Password = $emailConfig['mail_password'];    //邮箱密码                                例:SRQQ12369
    $mailer->From = $emailConfig['mail_from'];            //发件人邮箱地址                         例:sun_rain_0533@163.com
    $mailer->FromName = $emailConfig['mail_fromname'];    //发件人姓名                               例:sun_rain_0533@163.com
    $mailer->CharSet=$emailConfig['mail_charset'];        //设置邮件编码                          例:utf-8
    $mailer->AltBody = $emailConfig['mail_altbody'];      //邮件正文不支持HTML的备用显示            例:测试邮件,请勿回复
    $mailer->IsHTML($emailConfig['mail_ishtml']);         //是否HTML格式邮件                      例:true
    $mailer->WordWrap = $emailConfig['word_wrap'];
    return $mailer;
});
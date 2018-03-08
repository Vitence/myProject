<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => 'root',
        'password'    => '123456',
        'dbname'      => 'project',
        'charset'     => 'utf8',
    ),
    
    'log'=>array(
        'LOG_RECORD' => true, // 开启日志记录
        'LOG_TYPE'   => 'File', // 日志记录类型 默认为文件方式
        'LOG_DIR'   => APP_PATH.'/app/runtime/logs/',
        'LOG_PROFILER' =>true
    ),
    'application' => array(
        'controllersDir' => APP_PATH . '/app/controllers/',
        'modelsDir'      => APP_PATH . '/app/models/',
        'viewsDir'       => APP_PATH . '/app/views/',
        'libraryDir'     => APP_PATH . '/app/library/',
        'utilDir'        => APP_PATH . '/app/library/util',
        'cacheDir'       => APP_PATH . '/app/cache/',
        'logicsDir'      => APP_PATH . '/app/logics/',
        'messageDir'     => APP_PATH . '/app/library/message',
        'serviceDir'     => APP_PATH . '/app/library/service',
        'stateDir'       => APP_PATH . '/app/models/Dbstate',
        'mailerDir'      => APP_PATH . '/app/library/service/PHPMailer/',
        'baseUri'        => '/',
        
    ),
));

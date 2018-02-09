<?php
return new Phalcon\Config(array(
	'emailConfig'=>array(
        'mail_host'    => 'smtp.163.com',
		'mail_smtpauth'=> TRUE,
		'mail_port'    => 25,
		'mail_username'=> 'xinhuodata@163.com',
		'mail_from'    => 'xinhuodata@163.com',
		'mail_fromname'=> 'XinHuo',
		'mail_password'=> 'XHdata201801',
		'mail_charset' => 'utf-8',
		'mail_ishtml'  => TRUE,
		'mail_altbody' => 'XinHuo',
		'word_wrap'    => 70,
	),
));
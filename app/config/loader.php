<?php

$loader = new \Phalcon\Loader();

$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->logicsDir,
        $config->application->libraryDir,
    )
)->register();

$loader->registerNamespaces(
    array(
        'Util'               => $config->application->utilDir,
        'Message'            => $config->application->messageDir,
        'Service'            => $config->application->serviceDir,
        'State'              => $config->application->stateDir,
        'PHPMailer'          => $config->application->mailerDir,
    )
);

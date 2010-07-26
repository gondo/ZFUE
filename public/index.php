<?php

require_once '../application/configs/global.php';

try {
    require_once 'Zend/Application.php';
    $application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
    $application->bootstrap()->run();
}
catch (Exception $exception) {
    echo '<pre>';
    var_dump($exception);
}
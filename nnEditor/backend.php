<?php

try {
    require_once "config.php";

    $options = array(
        'group' => 'backend'
    );
    $app = nnEditor\Core\Controller::getInstance($options);
     
    $app->start();
    
    $app->terminate();
} catch(Exception $exp) {
    echo $exp->getMessage();
    exit;
}


<?php

try {
    require_once "config.php";

    $app = nnEditor\Core\Controller::getInstance();
     
    $app->start();
    
    $app->terminate();
} catch(Exception $exp) {
    echo $exp->getMessage();
    exit;
}


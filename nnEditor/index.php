<?php

require_once "config.php";

$app = nnEditor\Core\Controller::getInstance();
 
$app->start();

$app->terminate();

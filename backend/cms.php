<?php
error_reporting(E_ALL);

// Добавлять в отчет все ошибки PHP
error_reporting(-1);

// То же, что и error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

session_start();

require_once "config.php";
if (!class_exists('nilContentParser')) {
    include_once dirname(__FILE__)."/core/nilContentParser.php";
}

$frontend = new nilContentParser($_REQUEST);

$frontend->displayPage();


//$_SESSION['token'] = 1;


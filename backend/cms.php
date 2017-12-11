<?php
session_start();

require_once "config.php";
if (!class_exists('nilContentParser')) {
    include_once dirname(__FILE__)."/core/nilContentParser.php";
}

$frontend = new nilContentParser($_REQUEST);

$frontend->displayPage();


//$_SESSION['token'] = 1;


<?php

if (!class_exists('nilBackedController')) {
    include_once dirname(__FILE__)."/core/nilBackedController.php";
}
$backend = new nilBackendController();

$auth = $backend->auth('admin', 'admin');
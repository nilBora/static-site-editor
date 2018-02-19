<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("FS_ROOT", __DIR__.'/');
define("FS_PROJECT", realpath(FS_ROOT.'../')."/");
define("FS_BACKUP", FS_ROOT.'history/');
define("FS_TEMPLATES", FS_ROOT.'static/template/');
define("FS_CORE", FS_ROOT.'core/');
define("FS_CORE_CONFIG", FS_CORE.'config/');
define('FS_TEMPLATES_BACKEND', FS_TEMPLATES.'backend/');
define('FS_TEMPLATES_FRONTEND', FS_TEMPLATES.'frontend/');


$GLOBALS['http_base'] = '/nneditor/';

include_once 'core/Dispatcher.php';

//Helpers
include_once 'core/helpers/HelperFacade.php';
/*
include_once 'core/helpers/Request.php';
include_once 'core/helpers/Route.php';
include_once 'core/helpers/Profiler.php';
include_once 'core/helpers/Curl.php';
*/

include_once 'core/entities/Entity.php';
include_once 'core/entities/UserEntity.php';

include_once 'core/Controller.php';
//include_once 'core/DomEditor.php';
include_once 'core/Display.php';
include_once 'core/Response.php';
include_once 'core/Backend.php';
include_once 'core/Frontend.php';
include_once 'core/Auth.php';
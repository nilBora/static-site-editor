<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("FS_ROOT", __DIR__.'/');
define("FS_PROJECT", realpath(FS_ROOT.'../')."/");
define("FS_HISTORY", FS_ROOT.'history/');

include_once 'core/Controller.php';
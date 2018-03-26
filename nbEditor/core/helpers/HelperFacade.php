<?php

namespace nnEditor\Core\Helpers;

class HelperFacade
{
    private static $_instance = null;
    private static $_helpers = array();
    private static $_dir;
    
    public static function factory($dir = false)
    {
        if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
    }
    
    private function __construct()
    {
        if (isset(self::$_instance)) {
			$msg = 'Instance already defined use HelperFacade::factory';
			throw new Exception($msg);
		}
        static::$_dir = __DIR__; 
        
        static::_autoload(); 
    }
    
    private static function _autoload()
    {
        include_once 'Request.php';
        include_once 'Route.php';
        include_once 'Profiler.php';
        include_once 'Curl.php';
        include_once 'StaticFiles.php';
        include_once 'Api.php';
        include_once 'DomEditor.php';
    }
    
    public function &get($name, $construct = false)
    {
        if (!isset(static::$_helpers[$name])) {
            $name = "\\nnEditor\Core\Helpers\\".$name;
            if (!class_exists($name)) {
                throw new HelperFacadeException(sprintf('Class Not Found. %s', $name));
            }
			static::$_helpers[$name] = new $name($construct);
		}

		return static::$_helpers[$name];
    }
    
    public function has($name)
    {
        
    }
}

class HelperFacadeException extends \Exception
{
}
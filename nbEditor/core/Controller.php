<?php
namespace nnEditor\Core;

use nnEditor\Core\Frontend;
use nnEditor\Core\Backend;
use nnEditor\Core\Response;
use nnEditor\Core\Helpers\HelperFacade;
use nnEditor\Core\Auth;


class Controller extends \nnEditor\Core\Dispatcher
{
    private static $_instance = null;
    private static $_bundles = null;
    
    private $_config;
    private $_options = array();
    
    public function __construct($options = array())
    {
        if (isset(self::$_instance)) {
			$msg = 'Instance already defined use Controller::getInstance';
			throw new SystemException($msg);
		}
        
        parent::__construct();
        
        $this->_setOptions($options);
        
        $this->onInit();
    }
    
    public static function &getInstance($options = array())
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($options);
		}
		return self::$_instance;
	}
	
    private function _setOptions($options)
    {
         $this->_options = $options;
    }
    
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->_options)) {
            throw new Exception(sptintf('Key %s Not Found', $key));
        }
        
        return $this->_options[$key];
    }
	
	protected function onInit()
	{
    	$this->_setConfigs();
        
        $this->_initSession();
        
        $this->_route = $this->getHelper('Route', $this->_config);

        $this->setTemplatePath();
	}
    
    private function _initSession()
    {
        //unset($_SESSION['sessionData']);
        $this->_sessionData = ['auth' => ''];
        
        if (array_key_exists('sessionData', $_SESSION)) {
           $this->_sessionData = $_SESSION['sessionData'];
           $this->setUser($_SESSION['sessionData']['auth']);
        }

        return true;
    }
    
    public function terminate()
    {
        if ($this->getHelper('Request')->has('profiler')) {
            define('MODULES_DIR', FS_ROOT);
            define('HELPERS_DIR', FS_CORE.'helpers');
            
            $profiler = $this->getHelper('Profiler');//new \nnEditor\Core\Helpers\Profiler();
            echo "<pre>";
            print_r($profiler->getMessages());
            echo "</pre>";
        }
        
    }
    
    public function start()
    {
        if ($this->_isBackend()) {
            $this->_onInitBackend();
            
            return true;
        }
        
        $this->_onInitFrontend();
        
        return true;
    }
    
    private function _isBackend()
    {
        return $this->getOption('group') == 'backend';
    }
    
    private function _onInitBackend()
    {
        $currentRouteConfig =  $this->_route->pareseUrl();
       
        $rules = $this->_route->getRules();
        
		if ($this->_hasExistMethodControllerByConfig($currentRouteConfig)) {
			if ($this->_isAuthRoute($currentRouteConfig)) {
                $this->call(new Auth(), 'onDisplayLogin');
				return true;
			}
            
            if ($this->getUserID()) {
                $this->_doCheckRoleRules($currentRouteConfig['role'], $rules);
            }
            
            $this->_doCallInstanceByRouts($currentRouteConfig); 
            
			return true;
		}
		
		throw new NotFoundException();
    }
    
    private function _doCallInstanceByRouts($currentRouteConfig)
    {
        $controllerName = $currentRouteConfig['controller'];
            
        $controllerName = $currentRouteConfig['controller'];
        if (array_key_exists('namespace', $currentRouteConfig)) {
            $controllerName = $currentRouteConfig['namespace'].'\\'.$controllerName;
        }

		$method = $currentRouteConfig['method'];
		
		if (!isset(static::$_bundles[$controllerName])) {
			static::$_bundles[$controllerName] = new $controllerName();
		}
		
		$instance = static::$_bundles[$controllerName];
		
        $this->call($instance, $method, $currentRouteConfig['matches']);
        
        return true;
    }
    
    public function getBundles()
    {
        return static::$_bundles;
    }
    
    private function _onInitFrontend()
    {
        $currentRouteConfig =  $this->_route->pareseUrl();
        
        if ($currentRouteConfig) {
            
            $this->_doCallInstanceByRouts($currentRouteConfig);            
            
            return true;
        }
        $frontend = new Frontend();
        $frontend->init();
        
        return true;
    }
    
    public function call($controller, $method, $params = [], $option = [])
    {
        $response = new Response();
        $args = [];
        $args[] = &$response;
        $params = array_merge($params, $args);

        call_user_func_array(
            array($controller, $method),
            $params
        );
        
/*
        $this->_doPrepareResponseByAnnotationss(
            $response,
            $controller,
            $method
        );
*/
        
        $response->send($controller);
        return true;
    }
    
    private function _doPrepareResponseByAnnotationss(
        $response, $controller, $method
    )
    {
        $annotations = $this->getClassAnnotations($controller, $method);
        if (!$annotations) {
            return false;
        }
        
        foreach ($annotations as $annotation) {
            $params = explode(" ", $annotation);
            if ($params[0] == 'before') {
               //
            }
        }
        
        return true;
    }
    // TODO: move to helper Annotations Controller
    public function getClassAnnotations($class, $method)
    {
        $r = new \ReflectionMethod($class, $method);
       
        $doc = $r->getDocComment();
        
        $allow = ['Response', 'before'];
        
        $regExp = '#@('.implode("|", $allow).'.*?)\n#s';
        
        preg_match_all($regExp, $doc, $annotations);

        if (empty($annotations[1])) {
            return false;
        }
        
        return $annotations[1];
    }
        
    private function _hasExistMethodControllerByConfig($currentRouteConfig)
	{
        if (!array_key_exists('controller', $currentRouteConfig)) {
            throw new NotFoundException();
        }
       
        $controller = $currentRouteConfig['controller'];
        if (!empty($currentRouteConfig['namespace'])) {
            $controller = $currentRouteConfig['namespace'].'\\'.$controller;
        }
        
		return $currentRouteConfig &&
		 	   method_exists(
				   $controller,
				   $currentRouteConfig['method']
			   );
	}
	
	private function _isAuthRoute($currentRouteConfig)
	{
		return $currentRouteConfig['auth'] && !$this->_isAuthInSessionData();
	}
    
    private function _isAuthInSessionData()
	{
		return array_key_exists('auth', $this->_sessionData)
			   && $this->_sessionData['auth'];
	}
	
    private function _doCheckRoleRules($role, $rules)
    {
        if (!$role) {
            return true;
        }
        
        $user = $this->getUser();
        
        if (!array_key_exists($role, $rules)) {
            return true;    
        }
        
        $rule = $rules[$role];
        
        if (in_array($user->getRole(), $rule)) {
            return true;        
        }
        
        throw new PermissionException();
    }
	
	public function setTemplatePath()
	{    	
        $this->templatePath = FS_TEMPLATES_FRONTEND;
        
        if ($this->_route->isBackend()) {
            $this->templatePath = FS_TEMPLATES_BACKEND;
        }
        return true;
	}
    
    public function getTeplatePah()
	{
    	return $this->templatePath;
	}
	
    private function _setConfigs()
    {
        $this->_config = $GLOBALS;
    }
    public function getConfig($key)
    {
        if (!array_key_exists($key, $this->_config)) {
            throw new SystemException('Not found config with key: '.$key);
        }
        
        return $this->_config[$key];
    }
    
    public function getConfigs()
    {
        return $this->_config;
    }
    
    public function getStaticPath()
    {
        return '/nbEditor/static/backend/';
    }
}

class SystemException extends \Exception
{
}

class PermissionException extends \Exception
{
    protected $message = 'Permission Error';
}

class NotFoundException extends \Exception
{
    protected $message = 'NotFoundException';
}
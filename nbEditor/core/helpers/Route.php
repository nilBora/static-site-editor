<?php

namespace nnEditor\Core\Helpers;

class Route
{
    private $_requestUri;
    private static $_routes = [];
    private $_rules = [];
    private $_config;
    private $_groups = [];
    
    public function __construct($config = false)
    {
        $this->_config = $config;
        $this->_requestUri = $_SERVER['REQUEST_URI'];
    }
    
    public function pareseUrl()
    {   
        $requestUriArray = explode('?', $this->_requestUri);
        
        $currentUri = $requestUriArray[0];
        
        if (preg_match("#url=(.*)#mis", $this->_requestUri, $matches)) {
            if (!empty($matches[1])) {
                $currentUri = $matches[1];
            }
        }
        
        $result = [];
        
        $routes = $this->getRoutes();
        
        foreach ($routes as $uri => $config) {
            
            $prefix = '';
            if (array_key_exists('group', $config) && !empty($this->_groups[$config['group']])) {
                $prefix = $this->_groups[$config['group']]['prefix'];
            }

            $uri  = '#^'.$prefix.$uri.'$#';
            //XXX: Fix This
            $uri = str_replace('//', '/', $uri);
            
            if (preg_match($uri, $currentUri, $matches)) {
                
                array_shift($matches);
                $use = explode('@', $config['use']);
                
                if (array_key_exists('auth', $config) && $config['auth']) {
                    $auth = true;
                }
                
                if (array_key_exists('role', $config) && $config['role']) {
                    $role = $config['role'];
                }
                
                if (array_key_exists('namespace', $config) && $config['namespace']) {
                    $namespace = $config['namespace'];
                }
                
                $result = [
                    'uri'        => $uri,
                    'matches'    => $matches,
                    'controller' => $use[0],
                    'method'     => $use[1],
                    'auth'       => !empty($auth) ? $auth : false,
                    'role'       => !empty($role) ? $role : false,
                    'namespace'  => !empty($namespace) ? $namespace : false,
                ];
            }
        }
        return $result;
    }
    public static function get($url, $params = [])
    {
        if (array_key_exists($url, static::$_routes)) {
            throw new \Exception('Route is Exists: '.$url);
        }
        static::$_routes[$url] = $params;
    }
    public function getRoutes()
    {
        $routes = [];
        require FS_CORE_CONFIG.'routes.php';
        
        $routesByConfig = [];//$this->_getRoutesByConfig();
        
        static::$_routes = array_merge(static::$_routes, $data['routes'], $routesByConfig);
       
        $this->_rules = $data['rules'];
        $this->_groups = $data['groups'];
        
        return static::$_routes;
    }
    
    private function _getRoutesByConfig()
    {
        //$configs = App::getInstance()->getConfig('modules');
    
        $routes = [];
       
        if (!$configs) {
            return $routes;
        }
        
        foreach ($configs as $config) {
            if (array_key_exists('routes', $config) && is_array($config['routes'])) {
                foreach ($config['routes'] as $route => $value) {
                    $routes[$route] = $value;
                }
            }
        }
        
        return $routes;
    }
    
    public function getRules()
    {
        return $this->_rules;
    }
    
    public function isBackend($httpBase = '/')
    {
        $httpBase = $this->_config['http_base'];
        
        return preg_match('#'.$httpBase.'#Umis', $this->_requestUri);
    }
}
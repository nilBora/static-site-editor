<?php
namespace nnEditor\Core;

use nnEditor\Core\Controller;

class Container
{
    public static function show($container)
    {
        $content = '';
        
        $settings = static::_getSettings();

        if (!array_key_exists($container, $settings)) {
            return true;
        }
        $modules = $settings[$container];
        
        foreach ($modules as $name => $values) {
            $params = [];

            if (!array_key_exists('method', $values)) {
                continue;
            }
            $method = (string) $values['method'];
            if (array_key_exists('params', $values)) {
                $params = $values['params'];
            }
            
            $bundles = Controller::getInstance()->getBundles();//
            
            $controller = $values['use'];
            
            if (array_key_exists($controller, $bundles)) {
                $content .= static::call($bundles[$controller], $method, $params);
            } 
        }
        
        return $content;
    }
    
    protected static function call($controller, $method, $params = [])
    {
        $response = new \nnEditor\Core\Response();
        $args = [];
        $args[] = &$response;
        $params = array_merge($args, $params);

        call_user_func_array(
            array($controller, $method),
            $params
        );
        
        return $response->content;
    }
    
    private static function _getSettings()
    {
        include FS_CORE_CONFIG.'container.php';
        
        return $settings;
    }
}
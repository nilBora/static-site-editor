<?php
namespace nnEditor\Core\tmpengine;

use nnEditor\Core\Controller;

class tmpEngine
{
    public function __construct($config = false)
    {
    }
    
    public function parse($template)
    {
        $template = $this->_parseMethods($template);
        
        $template = $this->_parseContainer($template);
        
        return $template;
    }
    
    private function _parseMethods($template)
    {
        $regExp = "#{{(.+)\((.*)\) ?}}#Umis";
        preg_match_all($regExp, $template, $matches);
        if (!$matches) {
            return $template;
        }
        
        $matches = array_reverse($matches);
        
        list($params, $methods, $search) = $matches;
        
        foreach ($methods as $key => $method) {
            $method = (string) trim($method);
            
            if (is_callable(array($this, $method))) {
                $result = call_user_func_array(
                    array($this, $method),
                    array($params[$key])
                );
                
                $template = str_replace($search[$key], $result, $template);
            }
        }
        
        return $template;
    }
    
    public function _parseContainer($template)
    {
        
        $regExp = "#{{ ?\W.container.show\((.+)\) ?}}#m";
        preg_match_all($regExp, $template, $matches);
        
        if (!$matches) {
            return $template;
        }
        list($search, $containers) = $matches;
        
       
        foreach ($containers as $key => $container) {
            $container = str_replace(array('\'', '"'), '', $container);
            $result = \nnEditor\Core\Container::show($container);
            
            $template = str_replace($search[$key], $result, $template);
        }
        
        return $template;
    }
    
    public function staticUrl($string)
    {
        $string = str_replace(array('\'', '"'), '', $string);
        
        return Controller::getInstance()->getStaticPath().$string;
    }
    
    public function siteUrl($url)
    {
        $url = str_replace(array('\'', '"'), '', $url);
        
        $base = Controller::getInstance()->getConfig('http_base');
        $url = $base.$url;
        $url = str_replace('//', '/', $url);
        
        return $url;
    }
}
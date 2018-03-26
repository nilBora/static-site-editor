<?php

namespace nnEditor\Core;

use nnEditor\Core\Dispatcher;
use nnEditor\Core\tmpengine\tmpEngine;

class Display extends Dispatcher
{
    protected $__path;
    
    public $fragment = false;
    
    public function __construct($path = false)
    {
        parent::__construct();

        //$pathTeplate = $this->getTeplatePah();
        if (!$path) {
            $path = \nnEditor\Core\Controller::getInstance()->getTeplatePah();
        }
        
        $this->__path = $path;
    }    

    public function getPath()
    {
        return $this->__path;
    }
    
    public function setTemplatePath($path)
    {
        $this->__path = $path;
    }

    public function assign($key, $value = null)
    {
        if (!is_array($key)) {
            $this->$key = $value;
            return true;
        }

        foreach ($key as $varName => $varValue) {
             $this->$varName = $varValue;
        }

        return true;
    }

    public function isTemplateExists($file)
    {
        $templatePath = $this->__path.$file;

        return file_exists($templatePath);
    }

    public function getTemplateFilePath($file)
    {
        return $this->getPath().$file;
    }
    
    public function fetch($file, $localVars = false, $templatePath = false)
    {
        $vars = $this->getArrayCopy();

        if ($templatePath) {
            $templatePath .= $file;
        } else {
            $templatePath = $this->getTemplateFilePath($file);
        }

        if (!file_exists($templatePath)) {
            throw new \Exception('Template file not found '.$templatePath);
        }

        extract($vars);
        if ($localVars) {
            extract($localVars);
        }

        ob_start();

        include($templatePath);

        $result = ob_get_contents();
        ob_end_clean();
        
        $tmpEngine = new tmpEngine();
        
        $result = $tmpEngine->parse($result);
        
        return $result;
    }
    
    public function display($content, $layout = 'main.phtml')
    {
        if ($this->fragment) {
            echo $content;
            
            return true;
        }
        $staticHelper = $this->getHelper('StaticFiles');
        
        $vars['content'] = $content;
        $vars['infoPage'] = array(
            'js'         => $staticHelper->getJs(),
            'static_url' => Controller::getInstance()->getStaticPath()
        );
        $vars['user'] = $this->getUser();
        
        $content = $this->fetch($layout, $vars);
        
        echo $content;
        
        return true;
    }
}
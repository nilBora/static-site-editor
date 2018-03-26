<?php
namespace nnEditor\Core\Helpers;

class StaticFiles
{
    private static $js = array();
    private static $css = array();
    
    public function includeJS($path)
    {
        static::$js[] = $path;   
    }
    
    public function getJs()
    {
        return static::$js;
    }
    
    public function includeCss($path)
    {
         static::$css[] = $path;
    }
    
    public function getCss()
    {
        return static::$css;
    }

}
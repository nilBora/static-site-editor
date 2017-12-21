<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("FS_ROOT", __DIR__.'/');
define("FS_PROJECT", realpath(FS_ROOT.'../')."/");
define("FS_HISTORY", FS_ROOT.'history/');

class Controller
{
    private $_request;
    
    public function __construct()
    {
        $this->_request = $_REQUEST;
    }
    
    public function init()
    {
        if (!empty($this->_request['save'])) {
            $this->_doSaveContent();
            
            return true;
        }
        $url = $this->_request['url'];
        
        
        //XXX: Вынести в метод
        if (!file_exists(FS_HISTORY.$url)) {
            if (file_exists(FS_PROJECT.$url)) {
                $content = file_get_contents(FS_PROJECT.$url);
            }
        }  else {
            $content = file_get_contents(FS_HISTORY.$url);
        }
        
        
        
        $content = $this->_getPreparedContent($content);
        
        //XXX: save history file
        file_put_contents(FS_HISTORY.$url, $content);
        
        $tagsJs = '<script></script>';
        
        $js = '<script type="text/javascript" src="/nnEditor/static/js/app.js" ></script>';
        $content = $content.$js;
        
        echo $content;
    }
    
    private function _getPreparedContent($content)
    {
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        
        $dom = $this->_getPreparedDomContent($dom);

        return $dom->saveHTML();
    }
    
    private function _getPreparedDomContent($dom)
    {
        $allowTags = $this->_getAllowedTags();
        $uniqID = 0;
        foreach ($allowTags as $tag) {
            
            $attributes = $dom->getElementsByTagName($tag);
            
            foreach ($attributes as $node) {
                $uniqID++;
                $node->setAttribute('data-nneditor', $uniqID);
            }
        }
        
        $url = $this->_request['url'];
        
        $bodyAttr = $dom->getElementsByTagName('body');
        foreach ($bodyAttr as $nod) {
            $nod->setAttribute('data-nneditor-url', $url);
        }
        
        return $dom; 
    }
    
    private function _getAllowedTags()
    {
        return ['h1', 'p', 'li'];
    }
    
    private function _doSaveContent()
    {
        
        $urlPost = $_POST['url'];
        $contentPost = $_POST['content'];
        $contentPost = json_decode($contentPost, true);

        $contentHtml = file_get_contents(FS_HISTORY.$urlPost);

        $dom = new DOMDocument;
        $dom->loadHTML($contentHtml);
        $xpath = new DomXPath($dom);

        foreach ($contentPost as $ident => $content) {
            $div = $xpath->query('//*[@data-nneditor="'.$ident.'"]')->item(0);
            $div->nodeValue = $content;   
        }
        $content = $dom->saveHTML();
       
        file_put_contents(FS_HISTORY.$urlPost, $content);
    }
}

$controller = new Controller();
$controller->init();

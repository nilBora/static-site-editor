<?php

namespace nnEditor\Core;

class Controller
{
    private static $_instance;
    
    private $_request;
    
    
    public function __construct()
    {
        if (isset(self::$_instance)) {
			$msg = 'Instance already defined use Controller::getInstance';
			throw new Exception($msg);
		}

        $this->_request = $_REQUEST;
    }
    
    public static function &getInstance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    public function terminate()
    {
        
    }
    
    public function start()
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

        
        echo $content;
    }
    
    private function _getPreparedContent($content)
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
        
        $dom = $this->_getPreparedDomContent($dom);
        
        $content = $dom->saveHTML();

        //XXX: save history file
        file_put_contents(FS_HISTORY.$this->_request['url'], $content);
        
        
        $content = $this->_getContentWithJS($dom);
        
        return $content;
    }
    
    private function _getContentWithJS($dom)
    {
        /*
        $js = '<script type="text/javascript" src="/nnEditor/static/js/app.js" ></script>';
        $content = $content.$js;
        */        
        $hd = $dom->getElementsByTagName('body');
        $hd = $hd->item(0);

        $script = $dom->createElement('script');
        $scriptAttr = $dom->createAttribute('src');
        $scriptAttr->value= '/nnEditor/static/js/app.js';
        $script->appendChild($scriptAttr);
        $hd->appendChild($script);
        
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
        
        $dom = new \DOMDocument;
        $dom->loadHTML($contentHtml);
        $xpath = new \DomXPath($dom);

        foreach ($contentPost as $ident => $content) {
            $div = $xpath->query('//*[@data-nneditor="'.$ident.'"]')->item(0);
            $div->nodeValue = $content;
           
        }
        $content = $dom->saveXML();

        file_put_contents(FS_HISTORY.$urlPost, $content);
    }
}
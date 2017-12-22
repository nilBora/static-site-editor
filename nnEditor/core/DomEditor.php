<?php
    
namespace nnEditor\Core;

class DomEditor
{
    private $_dom;
    
    public function __construct($content = false)
    {
        $this->_dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        if ($content) {
            $this->_dom->loadHTML($content);    
        }
        return true;
    }
    
    public function getPreparedDomContent()
    {
        $allowTags = Controller::getInstance()->getAllowedTags();

        $uniqID = 0;
        foreach ($allowTags as $tag) {
            
            $attributes = $this->_dom->getElementsByTagName($tag);
            
            foreach ($attributes as $node) {
                $uniqID++;
                $node->setAttribute('data-nneditor', $uniqID);
            }
        }
        //XXX: FIx this
        $url = $_REQUEST['url'];
        
        $bodyAttr = $this->_dom->getElementsByTagName('body');
        foreach ($bodyAttr as $nod) {
            $nod->setAttribute('data-nneditor-url', $url);
        }
        
        $content = $this->_dom->saveHTML();
        
        return $content;
    }
    
    public function getContentWithJS()
    {
        $hd = $this->_dom->getElementsByTagName('body');
        $hd = $hd->item(0);
        
        $script = $this->_dom->createElement('script');
        $tagsJs = $this->_fetchAllowedTagsByJs();
        
        $script->nodeValue = $tagsJs;
        
        $hd->appendChild($script);

        $script = $this->_dom->createElement('script');
        $scriptAttr = $this->_dom->createAttribute('src');
        $scriptAttr->value= '/nnEditor/static/js/app.js';
        $script->appendChild($scriptAttr);
        $hd->appendChild($script);
        
        return $this->_dom->saveHTML();
    }
    
    private function _fetchAllowedTagsByJs()
    {
        $tags = Controller::getInstance()->getAllowedTags();
        $tagsJs = "var ALLOW_TAGS = [";
        foreach ($tags as $tag) {
            $tagsJs .= "'".$tag."',";
        }
        $tagsJs .= "];";
        
        return $tagsJs;
    }
    
    public function doSaveContent($postContent)
    {
        $xpath = new \DomXPath($this->_dom);

        foreach ($postContent as $ident => $content) {
            $div = $xpath->query('//*[@data-nneditor="'.$ident.'"]')->item(0);
            $div->nodeValue = $content;
           
        }
        
        return $this->_dom->saveHtml();
    }
    
    public function getDom()
    {
        return $this->_dom;
    }
    
    
}
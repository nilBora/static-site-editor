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
        
        $content = $this->saveHtmlDom();
        
        return $content;
    }
    
    public function getContentWithAdminPanel()
    {
        $this->_displayPanel();
        
        
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
        
        return $this->saveHtmlDom();
    }
    
    private function _displayPanel()
    {
        $this->_includeCss('panel.css');
         
        $hd = $this->_dom->getElementsByTagName('body');
        $hd = $hd->item(0);
        
        $div = $this->_dom->createElement('div');
        $idAttr = $this->_dom->createAttribute('id');
        $idAttr->value = "nn_system_info";
        $div->appendChild($idAttr);
        $div->nodeValue = $this->_fetchPanelHtml();
        
        $hd->appendChild($div);
    }
    
    private function _includeCss($cssName)
    {
        $hd = $this->_dom->getElementsByTagName('head');
        $hd = $hd->item(0);
        
        $style = $this->_dom->createElement('link');
        
        $styleAttr = $this->_dom->createAttribute('type');
        $styleAttr->value= 'text/css';
        $style->appendChild($styleAttr);
        
        $styleAttr = $this->_dom->createAttribute('rel');
        $styleAttr->value= 'stylesheet';
        $style->appendChild($styleAttr);
        
        $styleAttr = $this->_dom->createAttribute('href');
        $styleAttr->value= '/nnEditor/static/css/'.$cssName;
        $style->appendChild($styleAttr);
        
        $hd->appendChild($style);
    }
    
    private function _fetchPanelHtml()
    {
        return Controller::getInstance()->fetch('panel.phtml');
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
    
    public function doDiffContent($postContent)
    {
        $xpath = new \DomXPath($this->_dom);

        foreach ($postContent as $ident => $content) {
            $div = $xpath->query('//*[@data-nneditor="'.$ident.'"]')->item(0);
            $div->nodeValue = $content;
        }
        
        return $this->saveHtmlDom();
    }
    
    public function &getDom()
    {
        return $this->_dom;
    }
    
    public function saveHtmlDom()
    {
        return html_entity_decode($this->_dom->saveHtml()); 
    }
}
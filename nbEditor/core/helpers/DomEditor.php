<?php
    
namespace nnEditor\Core\Helpers;

class DomEditor
{
    private $_dom;
    private $_adapter = null;
    
    public function __construct(&$adapter = false)
    {
        $this->_adapter = $adapter;
        
        $this->_dom = new \DOMDocument;
        libxml_use_internal_errors(true);
    }
    
    public function load($content)
    {
        if ($content) {
            $this->_dom->loadHTML($content);    
        }
        return true;
    }
    
    public function getPreparedDomContent()
    {
        $allowTags = $this->_adapter->getAllowedTags();

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
        $hd = $this->_dom->getElementsByTagName('body');
        $hd = $hd->item(0); 
        
        $this->_createDivContainer($hd);
        
        
        $this->_createCoreScript($hd);
        
        
        $this->_createInitCoreScript($hd);
        
        return $this->saveHtmlDom();
    }
    
    /*
     * <div id="NB-Eitor" class="nn-editor-content" style="position: fixed; bottom: 5%; z-index: 99999; opacity: 0.9; width: 100%;"></div>
    */
    private function _createDivContainer($hd)
    {
        $div = $this->_dom->createElement('div');
        
        $idAttr = $this->_dom->createAttribute('id');
        $idAttr->value = "NB-Eitor";
        $div->appendChild($idAttr);
        
        $classAttr = $this->_dom->createAttribute('class');
        $classAttr->value = "nn-editor-content";
        $div->appendChild($classAttr);
                
        $styleAttr = $this->_dom->createAttribute('style');
        $styleAttr->value = "position: fixed; bottom: 5%; 
                             z-index: 99999; opacity: 0.9; width: 100%;";
        $div->appendChild($styleAttr);
        
        $hd->appendChild($div);
        
        return true;
    }
    
    /*
     *   <script type="text/javascript" src="/nnEditor/static/js/app.js"></script>    
    */
    private function _createCoreScript($hd)
    {
        $script = $this->_dom->createElement('script');
        
        $typeAttr = $this->_dom->createAttribute('type');
        $typeAttr->value = "text/javascript";
        $script->appendChild($typeAttr);
        
        $scrAttr = $this->_dom->createAttribute('src');
        $scrAttr->value = "/nbEditor/static/frontend/js/app.js";
        $script->appendChild($scrAttr);
        
        $hd->appendChild($script);
        
        return true;
    }
    
    /*
     * <script>nnCore.initJS()</script>
    */
    private function _createInitCoreScript($hd)
    {
        $scriptCore = $this->_dom->createElement('script');
        $scriptCore->nodeValue = "nnCore.initJS()";
        
        $hd->appendChild($scriptCore);
        
        return true;
    }
    
    public function _fetchPanelHtml()
    {
        $display = new \nnEditor\Core\Display();

        echo $display->fetch('panel.phtml');
    }
    
    private function _fetchAllowedTagsByJs()
    {
        $tags = $this->_adapter->getAllowedTags();
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
    
    public function doSaveBodyContent($content)
    {
        $content = $this->_getPrepareCleanContent($content);
        
        $body = $this->_dom->getElementsByTagName('body');
        
        $bodyItem = $body->item(0);
        $bodyItem->nodeValue = $content;

        return $this->saveHtmlDom();
    }
    
    private function _getPrepareCleanContent($content)
    {
        return $content;
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
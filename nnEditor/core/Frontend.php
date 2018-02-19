<?php
    
namespace nnEditor\Core;

//use nnEditor\Core\DomEditor;
use nnEditor\Core\Display;

class Frontend extends Display
{
    private $_request = null;
    private $_dom;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->_request = $this->getHelper('Request');
    }
    
    public function init()
    {
        $this->_dom = $this->getHelper('DomEditor', $this);
        
        if ($this->_request->has('clear') && $this->_request->get('clear') == 1) {
            unlink(FS_BACKUP.$this->_request->get('url'));
        }
        
        if (!empty($this->_request->has('save'))) {
            $this->_doSaveContent();
            
            return true;
        }
        $url = $this->_request->get('url');//$this->_request['url'];
        
        //XXX: Вынести в метод
        if (!file_exists(FS_BACKUP.$url)) {
            if (file_exists(FS_PROJECT.$url)) {
                $content = file_get_contents(FS_PROJECT.$url);
            }
        }  else {
            $content = file_get_contents(FS_BACKUP.$url);
        }
        
        if ($this->_isAdmin()) {
            $content = $this->_getPreparedContent($content);
        }

        
        echo $content;
    }
    
    private function _getPreparedContent($content)
    {
       
        //$domEditor = new DomEditor($this);
        $this->_dom->load($content);
        $content = $this->_dom->getPreparedDomContent();
        
        //XXX: save history file
        $url = $this->_request->get('url');
        file_put_contents(FS_BACKUP.$url, $content);
        
        $content = $this->_dom->getContentWithAdminPanel();
        
        return $content;
    }
    
    private function _isAdmin()
    {
        return array_key_exists('admin', $_GET) && $_GET['admin'] == 1;
    }
    
    private function _doSaveContent()
    {
        $urlPost = $_POST['url'];
        $contentPost = $_POST['content'];
        $contentBody = $_POST['body'];
        
        $contentPost = json_decode($contentPost, true);

        $contentHtml = file_get_contents(FS_BACKUP.$urlPost);
        
        //$domEditor = new DomEditor($this);
        $this->_dom->load($contentHtml);
        $content = $this->_dom->doSaveBodyContent($contentBody);
        
        //$content = $domEditor->doDiffContent($contentPost);

        if (!file_put_contents(FS_BACKUP.$urlPost, $content)) {
            throw new Exception('File Not Save');
        }
        
        return true;
    }
    
    public function getAllowedTags()
    {
        /* 
            $allowedTags = ['a', 'abbr', 'address', 'area', 'b', 'basefont', 
                            'bdo', 'blockquote', 'body', 'button', 'caption',
                            'cite', 'code', 'col', 'colgroup', 'dd', 'del',
                            'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'fieldset', 
                            'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 
                            'iframe', 'input', 'ins', 'kbd', 'label', 'legend', 
                            'li', 'menu', 'ol', 'option', 'p', 'pre', 'q', 
                            'samp', 'select', 'span', 'strong', 'sub', 'sup', 
                            'table', 'tbody', 'td', 'textarea', 'tfoot', 
                            'th', 'thead', 'tr', 'ul', 'var'] 
        */
        return ['h1', 'p', 'ul', 'img'];
    }
}
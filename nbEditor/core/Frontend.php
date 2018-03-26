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
        parent::__construct(FS_TEMPLATES_FRONTEND);
        
        $this->_request = $this->getHelper('Request');
    }
    
    public function init()
    {
        $this->_dom = $this->getHelper('DomEditor', $this);
        
        if ($this->_hasClearInRequest()) {
            unlink(FS_BACKUP.$this->_request->get('url'));
        }
        
        $this->_onDisplayContent();
        
        return true;
    }
    
    private function _hasClearInRequest()
    {
        return $this->_request->has('clear') && 
               $this->_request->get('clear') == 1;
    }
    
    private function _onDisplayContent()
    {
        $url = $this->_request->get('url');

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
    
    public function onAjaxSaveContent(Response &$response)
    {
        $urlPost = $_POST['url'];

        $contentBody = $_POST['body'];

        $contentHtml = file_get_contents(FS_BACKUP.$urlPost);
        $dom= $this->getHelper('DomEditor', $this);
        
        $dom->load($contentHtml);
        $content = $dom->doSaveBodyContent($contentBody);

        if (!file_put_contents(FS_BACKUP.$urlPost, $content)) {
            throw new Exception('File Not Save');
        }
        
        $response->content = array('save' => 'ok');
        $response->setType(Response::TYPE_JSON);
        
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
        return ['h1', 'p', 'img'];
    }
    
    public function fetchPanel(Response &$response)
    {
        $tagDecorations = array(
            array(
                'name' => 'formatBlock',
                'caption' => '1',
                'param' => '<h1>',
                'ident' => 'h1'
            ),
            array(
                'name' => 'formatBlock',
                'caption' => '2',
                'param' => '<h2>',
                'ident' => 'h2'
            ),
            array(
                'name' => 'insertUnorderedList',
                'caption' => '',
                'param' => null,
                'ident' => 'ul'
            ),
            array(
                'name' => 'insertOrderedList',
                'caption' => '',
                'param' => null,
                'ident' => 'ol'
            ),
             array(
                'name' => 'formatBlock',
                'caption' => '',
                'param' => '<p>',
                'ident' => 'paragraph'
            ),
            array(
                'name' => 'bold',
                'caption' => '',
                'param' => null,
                'ident' => 'strong'
            ),
            array(
                'name' => 'italic',
                'caption' => '',
                'param' => null,
                'ident' => 'italic'
            ),
            array(
                'name' => 'underline',
                'caption' => '',
                'param'   => null,
                'ident' => 'underline',
            ),
            array(
                'name' => 'justifyCenter',
                'caption' => '',
                'param' => null,
                'ident' => 'justify-center'
            ),
            array(
                'name' => 'justifyFull',
                'caption' => '',
                'param' => null,
                'ident' => 'justify-full'
            ),
            array(
                'name' => 'justifyLeft',
                'caption' => '',
                'param' => null,
                'ident' => 'justify-left'
            ),
            array(
                'name' => 'justifyRight',
                'caption' => '',
                'param' => null,
                'ident' => 'justify-right'
            ),
            array(
                'name' => 'removeFormat',
                'caption' => '',
                'param' => null,
                'ident' => 'remove-format'
            ),
            array(
                'name' => 'undo',
                'caption' => '',
                'param' => null,
                'ident' => 'undo'
            ),
            array(
                'name' => 'foreColor',
                'caption' => 'Change Color',
                'param' => 'FF0000',
                'ident' => 'foreColor',
            )
            
        ); 
        
        $vars = array(
            'tagDecorations' => $tagDecorations
        );
        $response->setFragment();
        $response->content = $this->fetch('panel.phtml', $vars);

        return true;
    }
}
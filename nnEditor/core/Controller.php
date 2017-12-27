<?php

namespace nnEditor\Core;

use nnEditor\Core\DomEditor;

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
        if (!file_exists(FS_BACKUP.$url)) {
            if (file_exists(FS_PROJECT.$url)) {
                $content = file_get_contents(FS_PROJECT.$url);
            }
        }  else {
            $content = file_get_contents(FS_BACKUP.$url);
        }
        
        $content = $this->_getPreparedContent($content);

        
        echo $content;
    }
    
    private function _getPreparedContent($content)
    {
        $domEditor = new DomEditor($content);

        $content = $domEditor->getPreparedDomContent();
        
        //XXX: save history file
        file_put_contents(FS_BACKUP.$this->_request['url'], $content);
        
        $content = $domEditor->getContentWithAdminPanel();
        
        return $content;
    }
        
    public function getAllowedTags()
    {
        return ['h1', 'p', 'li', 'img'];
    }
    
    private function _doSaveContent()
    {
        $urlPost = $_POST['url'];
        $contentPost = $_POST['content'];
        $contentPost = json_decode($contentPost, true);

        $contentHtml = file_get_contents(FS_BACKUP.$urlPost);
        
        $domEditor = new DomEditor($contentHtml);
        
        $content = $domEditor->doDiffContent($contentPost);

        if (!file_put_contents(FS_BACKUP.$urlPost, $content)) {
            throw new Exception('File Not Save');
        }
        
        return true;
    }
    
    public function fetch($template)
    {
        $templatePath = FS_ROOT.'static/template/'.$template;

        if (!file_exists($templatePath)) {
            $msg = sprintf('Template Not Found: %s', $templatePath);
            throw new Exception($msg);
        }
        
        ob_start();
        
        include $templatePath;
        
        $content = ob_get_clean();
        
        return $content;
    }
    
    
    private function _getCurrentTemplate($fileName)
    {
        if ($content = file_get_contents(FS_BACKUP.$fileName) !== false) {
            return $content;
        }
        
        throw new Exception('File not Found');
    }
}
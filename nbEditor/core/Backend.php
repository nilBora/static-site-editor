<?php

namespace nnEditor\Core;

use nnEditor\Core\Display;

class Backend extends Display
{
    public function onDisplayDefault(Response &$response)
    {
/*
        $api = $this->getHelper('Api');
        echo "<pre>";
        print_r($api->send('http://api.develop-nil.com/api/users'));
*/
        $response->content = $this->fetch('index.phtml');
    }
    
    public function onDisplayFileManager(Response &$response)
    {   
        $currentDir = FS_PROJECT;
/*
        unset($_SESSION['currentPath']);
        exit;
*/
        if (!empty($_SESSION['currentPath'])) {
            $currentDir = $_SESSION['currentPath'];
        }

        $result = $this->_getPreparedFilesData($currentDir);
        
        $vars = array(
            'files' => $result
        );
        
        $response->content = $this->fetch('filemanager.phtml', $vars);
        
        return true;
    }
    
    public function onDisplayHistory(Response &$response)
    {
        $currentDir = FS_BACKUP;
        
        if (!empty($_SESSION['currentBackupPath'])) {
            $currentDir = $_SESSION['currentBackupPath'];
        }
        
        $result = $this->_getPreparedFilesData($currentDir);
        
        $vars = array(
            'files' => $result
        );
        
        $response->content = $this->fetch('history.phtml', $vars);
        
        return true;
    }
    
    private function _getPreparedFilesData($currentDir)
    {
        $allow = array('.', '.git', 'nnEditor');
        
        $result = array();
        
        $files = scandir($currentDir);
          
        foreach ($files as $key => $file) {
            if (in_array($file, $allow)) {
                continue;
            }
            
            if ($file == '.' || $file == '..') {
                $result[$file]['type'] = 'dir';
                $result[$file]['path'] = $file;
            }
            
            if (is_dir(FS_PROJECT.$file)) {
                $result[$file]['type'] = 'dir';
                $result[$file]['path'] = $currentDir.$file.'/';
            } else {
                $result[$file]['type'] = 'file';
                $result[$file]['path'] = $currentDir;
            }   
        }
        
        return $result;
    }
    
    /**
     * @before test 1
     */
    public function onDisplayEdit(Response &$response)
    {
        if (!empty($_SESSION['currentEditFile'])) {
            $path = $_SESSION['currentEditFile'];
            
            $extension = pathinfo( basename($path), PATHINFO_EXTENSION);
    
            $extensionData = array(
                'js'       => 'javascript',
                'htaccess' => 'html',
                'html'     => 'html',
                'phtml'    => 'php',
                'css'      => 'css',
                'json'     => 'json'
            );
            
            $currentMode = 'php';
            
            if (array_key_exists($extension, $extensionData)) {
                $currentMode = $extensionData[$extension];
            }
           
            
            $pathMode = '/nbEditor/static/backend/plugins/ace/src/mode-'.$currentMode.'.js';
            if (!file_exists(FS_PROJECT.$pathMode)) {
                $pathMode = '/nbEditor/static/backend/plugins/ace/src/mode-php.js'; 
            }
            
            $staticHelper = $this->getHelper('StaticFiles');
            //$staticHelper->includeJS('asdasda');
            
            $staticHelper->includeJS('/nbEditor/static/backend/plugins/ace/src/ace.js');
            $staticHelper->includeJS('/nbEditor/static/backend/plugins/ace/src/theme-twilight.js');
            $staticHelper->includeJs($pathMode);
            
            $vars = array(
                'fileName'    => basename($path),
                'fileContent' => file_get_contents($path),
                'currentMode' => $currentMode
            );
            
            $response->content = $this->fetch('edit.phtml', $vars);
            
            return true;
        }
        
        throw new \Exception('Erorr Display Edit File');
    }
    
    public function onAjaxSaveFilePath(Response &$response)
    {
        $path = $_POST['path'];
        $name = $_POST['name'];
        
        $filePath = $path.$name;
        
        $_SESSION['currentEditFile'] = $filePath;
        
        $response->setType(Response::TYPE_JSON);
        $response->content = 'ok';
        
        return true;
    }
    
    public function onAjaxFileManager(Response &$response)
    {
        $path = realpath($_POST['path'])."/";
        
        if (strcmp($path, FS_PROJECT) < 0) {       
            echo json_encode(array('erorr' => 1));
            exit;
        }
        $_SESSION['currentPath'] = $path;
        
        if ($_POST['name'] == '..') {
            
            $_SESSION['currentPath'] =  $path;//str_replace('/../', '/', $_POST['path']);

        }
        if (!empty($_SESSION['currentPath'])) {
            $_SESSION['prevPath'] = $_SESSION['currentPath'];
        }
        
        echo json_encode(array('ok' => 1));
        exit;
    }

    public function onAjaxHistory(Response &$response)
    {
        $_SESSION['currentBackupPath'] = $_POST['path'];

        if ($_POST['name'] == '..') {
            $path = realpath($_POST['path'])."/";
            $_SESSION['currentBackupPath'] = $path;//str_replace('/../', '/', $_POST['path']);
        }
        if (!empty($_SESSION['currentBackupPath'])) {
            $_SESSION['prevBackupPath'] = $_SESSION['currentBackupPath'];
        }
        
        echo json_encode(array('ok' => 1));
        exit;
    }
    
    public function onDisplayLeftMenu(Response &$response)
    {
        $response->content = $this->fetch('container_left_menu.phtml');
    }
}
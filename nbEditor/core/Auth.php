<?php
namespace nnEditor\Core;

use nnEditor\Core\Display;

class Auth extends Display
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_request = $this->getHelper('Request');
    }
    
    public function onDisplayLogin(Response &$response)
    {
        $response->content = $this->display(false, 'login.phtml');
    }
    
    public function onAuth(Response &$response)
    {
        $username = $this->_request->post(
            'email',
            $this->_request::TYPE_FIELD_EMAIL,
            $error
        );
            
        $password = $this->_request->post('password');
        
        $data = array(
            'username' => $username,
            'password' => $password
        );
        
        $api = $this->getHelper('Api');
        $result = $api->getBasicAuth($data);   
        
        if (!$result) {
            throw new AuthException('API Service Not Found');
        }
        
        $resultData = json_decode($result, true);
        
        if (empty($resultData['access_token'])) {
            throw new AuthException('Access Token Not Found');
        }
        
        $_SESSION['sessionData']['auth'] = $resultData;
        
        $response->setAction(Response::ACTION_REDIRECT);
        $response->url = $GLOBALS['http_base'];
        
        return true;
    }
    
    public function onLogout(Response &$response)
    {
        unset($_SESSION['sessionData']);
        $response->setAction(Response::ACTION_REDIRECT);
        $response->url = $GLOBALS['http_base'];
        
        return true;
    }
}

class AuthException extends \Exception
{
}
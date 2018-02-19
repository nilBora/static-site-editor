<?php
namespace nnEditor\Core;

use nnEditor\Core\Display;

class Auth extends Display
{
    public function onDisplayLogin(Response &$response)
    {
        $response->content = $this->display('', 'login.phtml');
    }
    
    public function onAuth(Response &$response)
    {
        $username = $_POST['email'];
        $password = $_POST['password'];
        
        $curl = $this->getHelper('Curl');//new \nnEditor\Core\Helpers\Curl();
        $url = "http://api.develop-nil.com/api/login";
        $userData = "$username:$password";
        $result = $curl->getBasicAuth($url, $userData);     
        
        if (!$result) {
            throw new \Exception('API Service Not Found');
        }
        
        $resultData = json_decode($result, true);
        
        if (empty($resultData['access_token'])) {
            throw new \Exception('Access Token Not Found');
        }
        
        $_SESSION['sessionData']['auth'] = $resultData;
        
        header('Location: /nneditor/');
        exit;
        
    }

}
<?php   
namespace nnEditor\Core\Helpers;

use nnEditor\Core\Helpers\HelperFacade;

class Api
{
    const REQUEST_METHOD_GET = "GET";
    const REQUEST_METHOD_POST = "POST";
    const REQUEST_METHOD_PUT = "PUT";
    const REQUEST_METHOD_DELETE = "DELETE";       
    
    private static $_token = null;
    private static $_tokenName = 'Access-Token';
    
    private $_host = 'http://api.develop-nil.com/';
    private $_urlAuth = 'api/login';
    
    public function setToken($token)
    {
        static::$_token = $token;
    }
    
    public function getToken()
    {
        if (!static::_hasToken()) {
            throw new ApiException('Access Token Not Found');
        }
        
        return static::$_token;
    }
    
    private function _hasToken()
    {
        return static::$_token != null;
    }
    
    public static function sendGet($url, $headers = false)
    {
        return static::send($url, false, false, $headers);
    }
    
    public function sendPost($url, $postData = false, $headers = false)
    {
        return static::send($url, $postData, static::REQUEST_METHOD_POST);
    }
    
    public function sendPut($url, $postData = false, $headers = false)
    {
        return static::send($url, $postData, static::REQUEST_METHOD_PUT);
    }
    
    public function sendDelete($url, $headers = false)
    {
        return static::send($url, false, static::REQUEST_METHOD_DELETE);
    }
    
    public function send(
        $url, $data = false, $requestMethod = false, $headers = false
    )
    {   
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTPHEADER => array(
                'Content-type: application/json',
                'Access-Token: '.$this->getToken()
            )
        );

        //$options[CURLOPT_HTTPHEADER] = $headers;

        if ($requestMethod) {
            $options[CURLOPT_CUSTOMREQUEST] = $requestMethod;
        }
        
        $facade = HelperFacade::factory();
        $curl = $facade->get('Curl');

        $result = $curl->getUrl($url, $options);
      
        if (!$result) {
            $msg = sprintf(
                'Api Service Not Found. Url: %s. Data: %s',
                $url,
                json_encode($data)
            );
            throw new ApiException($msg);
        }
        
        return json_decode($result, true);
    }
    
    private function _setUserToken($token)
    {
        static::setToken($token);
    }
    
    public function getTokenName()
    {
        return static::$_tokenName;
    }
    
    public function setTokenName($name)
    {
        static::$_tokenName = $name;
    }
    
    public function getBasicAuth($data)
    {
        $facade = HelperFacade::factory();
        $curl = $facade->get('Curl');
        $userData = sprintf("%s:%s", $data['username'], $data['password']);
        
        return $curl->getBasicAuth($this->_getUrlAuth(), $userData);
    }
    
    private function _getUrlAuth()
    {
        return $this->_host.$this->_urlAuth;
    }
}

class ApiException extends \Exception
{
}

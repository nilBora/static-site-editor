<?php
namespace nnEditor\Core\Helpers;

class Request
{
    private $_post;
    private $_get;
    private $_request;
    
    const TYPE_REQUEST = 'request';
    const TYPE_POST    = 'post';
    const TYPE_GET     = 'get';
    
    const TYPE_FIELD_STRING = 'string';
    const TYPE_FIELD_EMAIL  = 'email';
    
    public function __construct()
    {
        $this->_request = $_REQUEST;
        $this->_post = $_POST;
        $this->_get = $_GET;
    }
    
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new RequestException();
        }
        
        return $this->_request[$key];
    }
    
    public function has($key, $type = self::TYPE_REQUEST)
    {
        if ($type == static::TYPE_POST) {
            return array_key_exists($key, $this->_post);
        }
        
        if ($type == static::TYPE_GET) {
            return array_key_exists($key, $this->_get);
        }
        
        return array_key_exists($key, $this->_request);
    }
       
    public function post($key = false, $type = self::TYPE_FIELD_STRING, &$error = array())
    {   
        if ($key && $this->has($key, static::TYPE_POST)) {
            switch ($type) {
                case self::TYPE_FIELD_STRING:
                    $value = filter_var($this->_post[$key], FILTER_SANITIZE_STRING);
                    if (!$value) {
                        $error[] = 'String Error';
                    }
                    break;
                case self::TYPE_FIELD_EMAIL:
                    $value = filter_var($this->_post[$key], FILTER_VALIDATE_EMAIL);
                    if (!$value) {
                        $error[] = 'Email Error';
                    }
                    break;
            }
            return $value;
        }
        
        return $this->_post;
    }
    
    public function getParam($key = false)
    {   
        if ($key && $this->has($key, static::TYPE_GET)) {
            return $this->_get[$key];
        }
        
        return $this->_get;
    }
    
    public function all()
    {   
        return $this->_request;
    }
}

class RequestException extends \Exception
{
    protected $message = 'Bad Request';
}
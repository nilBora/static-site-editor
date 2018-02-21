<?php

namespace nnEditor\Core;
use nnEditor\Core\Helpers\HelperFacade;
use nnEditor\Core\Entities\UserEntity;

use ArrayObject;

class Dispatcher extends ArrayObject
{
    private $_helpersFacade = null;
    
    public static $_user;
    
    protected $templatePath;
    
    public function __construct()
    {
        parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
        
        $this->_helpersFacade = HelperFacade::factory();
        
    }
    
    public function getHelper($name, $construct = false)
    {
        return $this->_helpersFacade->get($name, $construct);
    }
    
    protected function setUser($data)
    {  
        static::$_user = new UserEntity($data); 
        $this->_setUserToken();
    }
    
    private function _setUserToken()
    {
        $token = static::$_user->getAccessToken();
        $api = $this->_helpersFacade->get('Api');
        $api->setToken($token);
    }
    
    protected function getUser()
    {
        return static::$_user;
    }
    
    protected function getUserID()
    {
        if (is_object(static::$_user)) {
            return static::$_user->getID();    
        }
        return false;
    }
    
    protected function getPreparedData($data, $conditions = array())
    {
        if (!is_array($data)) {
            throw new \Exception('Data Not Found');
        }
        
        foreach ($data as $key => $item) {
            //
        }
        
        return $data;
    }
}
<?php

namespace nnEditor\Core\Helpers;

class Curl
{
    private $_curl = null;
    private $_curlInfo = null;
    
    public function __construct()
    {
        $this->_curl = curl_init();
    }
    
    public function getUrl($url = false, $options = array())
    {
        if (!$url) {
            throw new Exception("Url Not Found");
        }
        
        $defaultOptions = $this->_getDefaultOptions();
        
        foreach ($defaultOptions as $index => $value) {
            if (isset($options[$index])) {
                continue;
            }
            $options[$index] = $value;
        }

        $options[CURLOPT_URL] = $url;
        
        curl_setopt_array($this->_curl, $options);
        
        $output = curl_exec($this->_curl);
        $this->_curlInfo = curl_getinfo($this->_curl);
        
        curl_close($this->_curl);
        
        return $output;
    }
    
    public function getBasicAuth($url = false, $userData = false)
    {
        $options = array(
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_USERPWD        => $userData,
           CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
           CURLOPT_POST           => 1
           
        );
        
        return $this->getUrl($url, $options);
    }
    
    private function _getDefaultOptions()
    {
        return array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTPHEADER => array('Content-type: application/json', 'Accept: application/json')
        );
    }
    
    
}
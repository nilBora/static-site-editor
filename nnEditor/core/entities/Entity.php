<?php
namespace nnEditor\Core\Entities;

abstract class Entity
{
    private $_values;

    public function __construct($data)
    {
        $this->_values = $data;
    }

    protected function get($key)
    {
        if ($this->has($key)) {
            return $this->_values[$key];
        }

        throw new \Exception("Not Fount Key In Values: ".$key);
    }

    protected function has($key)
    {
        return array_key_exists($key, $this->_values);
    }

    public function toJson()
    {
        return json_encode($this->_values);
    }
    
    protected function set($key, $value)
    {
        $this->_values[$key] = $value;
    }
    
    public function getValues()
    {
        return $this->_values;
    }
}
<?php

namespace Sunhill\Storage;

class StorageException extends \Exception {}

class storage_base  {
    
    protected $inheritance;
    
    protected $caller;
    
    protected $entities = [];
    
    public function __construct($caller) {
        $this->caller = $caller;    
    }
    
    public function set_inheritance($inheritance) {
        $this->inheritance = $inheritance;
    }
    
    public function get_entity(string $name) {
        if (!isset($this->entities[$name])) {
            return null;
        } else {
            return $this->entities[$name];
        }
    }
    
    public function __get(string $name) {
        return $this->get_entity($name);
    }
    
    public function set_entity(string $name,$value) {
        $this->entities[$name] = $value;
    }
    
    public function __set(string $name,$value) {
        return $this->set_entity($name,$value);
    }    
    
}

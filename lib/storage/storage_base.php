<?php

namespace Sunhill\Storage;

class StorageException extends \Exception {}

class storage_base  {
    
    protected $inheritance;
    
    protected $caller;
    
    public function __construct($caller) {
        $this->caller = $caller;    
    }
    
    public function set_inheritance($inheritance) {
        $this->inheritance = $inheritance;
    }
}

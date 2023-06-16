<?php

namespace Sunhill\ORM\Storage;

/**
 * Helper structure for a storage entry
 * 
 * @author klaus
 *
 */
class StorageElement
{
    
    protected $name;
    
    protected $type;
    
    protected $value;
    
    protected $shadow;
    
    protected $storage_id;
    
    public function setName(string $name): StorageElement
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName(): string
    {
        return $this->name;    
    }
    
    public function setType(string $type): StorageElement
    {
        $this->type = $type;
        return $this;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function setValue($value): StorageElement
    {
        $this->value = $value;
        return $this;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setShadow($shadow): StorageElement
    {
        $this->shadow = $shadow;
        return $this;
    }
    
    public function getShadow()
    {
        return $this->shadow;
    }
    
    public function setStorageID(string $storage_id): StorageElement
    {
        $this->storage_id = $storage_id;
        return $this;
    }
    
    public function getStorageID(): string
    {
        return $this->storage_id;
    }
        
}
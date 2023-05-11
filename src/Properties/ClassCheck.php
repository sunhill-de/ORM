<?php
/**
 * @file ClassCheck.php
 * Defines the trait that checks if an given object is allowed for this object field
 * Lang en
 * Reviewstatus: 2023-05-08
 * Localization: complete
 * Documentation: complete
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Storage\StorageBase;

trait ClassCheck 
{

    protected $allowed_objects;
    
    public function setAllowedObjects($object)
    {
        if (is_string($object)) {
            $this->allowed_objects = [$object];
        } else if (is_array($object)) {
            $this->allowed_objects = $object;
        }
        return $this;
    }
    
    public function getAllowedObjects(): array
    {
        return $this->allowed_objects;
    }
    
    protected function isAllowedObject($test): bool
    {
        return true;
    }
    
}
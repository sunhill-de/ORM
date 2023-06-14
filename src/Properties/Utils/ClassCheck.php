<?php
/**
 * @file ClassCheck.php
 * Defines the trait that checks if an given object is allowed for this object field
 * Lang en
 * Reviewstatus: 2023-06-14
 * Localization: complete
 * Documentation: complete
 */

namespace Sunhill\ORM\Properties\Utils;

use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\ORMObject;

trait ClassCheck 
{

    protected $allowed_classes;
    
    public function setAllowedClasses($object)
    {
        if (is_string($object)) {
            $this->allowed_objects = [$object];
        } else if (is_array($object)) {
            $this->allowed_objects = $object;
        }
        return $this;
    }
    
    public function getAllowedClasses(): array
    {
        return $this->allowed_objects;
    }
    
    protected function isAllowedObject($test): bool
    {
        if (is_int($test)) {
            $test = Objects::getClassNamespaceOf($test);
        }
        if (empty($this->allowed_objects)) {
            return is_a($test, ORMObject::class);
        }
        foreach ($this->allowed_objects as $object) {
            if (Classes::isA($test, $object)) {
                return true;
            }
        }
        return false;
    }
    
}
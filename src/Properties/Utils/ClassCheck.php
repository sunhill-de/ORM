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
use Sunhill\ORM\Properties\Exceptions\InvalidParameterException;

trait ClassCheck 
{

    protected $allowed_classes;
    
    public function setAllowedClasses($object)
    {
        if (is_string($object)) {
            $this->allowed_classes = [$object];
        } else if (is_array($object)) {
            $this->allowed_classes = $object;
        }
        return $this;
    }
    
    public function setAllowedClass($class)
    {
        $this->allowed_classes = $class;
        return $this;
    }
    
    public function getAllowedClasses()
    {
        if (empty($this->allowed_classes)) {
            throw new InvalidParameterException("The allowed classes for ".static::getInfo('name')." are not set.");
        }
        return $this->allowed_classes;
    }
    
    public function getAllowedCollection()
    {
        if (empty($this->allowed_classes)) {
            throw new InvalidParameterException("The allowed collection for ".static::getInfo('name')." is not set.");
        }
        return $this->allowed_classes;
    }
    
    protected function isAllowedObject($test): bool
    {
        if (is_int($test)) {
            $test = Objects::getClassNamespaceOf($test);
        }
        if (empty($this->allowed_classes)) {
            return is_a($test, ORMObject::class);
        }
        foreach ($this->allowed_classes as $object) {
            if (Classes::isA($test, $object)) {
                return true;
            }
        }
        return false;
    }
    
    protected function isAllowedCollection($test): bool
    {
        return is_a($test, $this->allowed_classes, true);    
    }
    
    protected function checkForObjectConversion($input)
    {
        if (is_numeric($input)) {
            return Objects::load($input);
        }
        return $input;        
    }
}
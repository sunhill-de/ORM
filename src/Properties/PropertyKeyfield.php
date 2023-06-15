<?php

/**
 * @file PropertyKeyfield.php
 * Provides a keyfield property
 * Lang en
 * Reviewstatus: 2023-06-14
 * Localization: none
 * Documentation: incomplete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: completed
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Properties\Exceptions\WriteToReadonlyException;

class PropertyKeyfield extends AtomarProperty 
{
	
	protected static $type = 'none';
	
	protected $build_rule = '';
	
    public function setBuildRule(string $rule): PropertyKeyfield
    {
        $this->build_rule = $rule;
        return $this;
    }

    /**
     * Raises an exception when called (keyfields mustn't be written to)
     */
    protected function doSetValue($value)
    {
        throw new WriteToReadonlyException("Tried to write to a key field ".$this->getName());
    }
 
    protected function recalculate()
    {
        $this->value = preg_replace_callback("|:([a-zA-Z0-9_\->]*)|",function($found){
            $owner = $this->getActualPropertiesCollection();
            $key = $found[1];
            if (strpos($key, "->") !== false) {
                list($field,$key) = explode("->",$key);
                $object = $owner->$field;
                if (empty($object)) {
                    return "";
                }
                return $object->$key;
            } else {
                return $owner->$key;
            }
        }, $this->build_rule);   
    }
    
    protected function initializeValue(): bool
    {
        $this->recalculate();
        return true;
    }
    
}
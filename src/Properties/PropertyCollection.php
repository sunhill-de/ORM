<?php
/**
 * @file PropertyObject.php
 * Provides an access to a object field
 * Lang de,en
 * Reviewstatus: 2023-06-14
 * Localization: none
 * Documentation: incomplete
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: incomplete
 * Dependencies: LazyIDLoading
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Storage\StorageBase;
use Sunhill\ORM\Properties\Utils\ClassCheck;

class PropertyCollection extends AtomarProperty
{
	
    use ClassCheck;
    
	protected static $type = 'integer';
		
	protected $initialized = true;
	
	public function isValid($input): bool
	{
	    return $this->isAllowedObject($input);
	}
	
	public function convertValue($input)
	{
	    if (is_numeric($input)) {
	        return Objects::load($input);	        
	    }
	    return $input;
	}
	
	public function loadFromStorage(StorageBase $storage) 
	{
        $name = $this->getName();
	    $object_id = $storage->$name;	    
	    if (!empty($object_id)) {
	        $this->doSetValue($this->convertValue($object_id));
	    }
	}
	
	public function insertIntoStorage(StorageBase $storage) 
	{
        $storage->setEntity($this->getName(), $this->value->getID());
	}

	public function updateToStorage(StorageBase $storage)
	{
	    if ($this->isDirty()) {
	    }
	}
}

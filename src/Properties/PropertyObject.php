<?php
/**
 * @file PropertyObject.php
 * Provides an access to a object field
 * Lang de,en
 * Reviewstatus: 2021-10-14
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

class PropertyObject extends AtomarProperty
{
	
    use ClassCheck;
    
    /**
     * Indicates the internal storage type
     * @var string
     */
	protected static $type = 'integer';

	/**
	 * Objects are assumed as initialized (default null)
	 * @var boolean
	 */
	protected $initialized = true;
	
	/**
	 * Checks if the given value is an object of an allowed class
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\AtomarProperty::isValid()
	 */
	public function isValid($input): bool
	{
	    return $this->isAllowedObject($input);
	}
	
	/**
	 * When an object field 
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\AtomarProperty::convertValue()
	 */
	public function convertValue($input)
	{
        return $this->checkForObjectConversion($input);
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

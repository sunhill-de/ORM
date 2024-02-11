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
use Sunhill\ORM\Objects\Collection;
use Sunhill\ORM\Facades\Collections;

class PropertyCollection extends AtomarProperty
{
	
	protected static $type = 'integer';
		
	protected $initialized = true;
	
	protected $allowed_collection = '';
	
	public function isValid($input): bool
	{
        $namepace = Collections::searchCollection($this->allowed_collection);
	    return is_numeric($input) || is_a($input, Collections::searchCollection($this->allowed_collection));
	}

	public function setAllowedCollection(string $allowed_collection): PropertyCollection
	{
	   $this->allowed_collection = $allowed_collection;
	   return $this;
	}
	
	public function setAllowedClasses($allowed_collection): PropertyCollection
	{
	   return $this->setAllowedCollection($allowed_collection);    
	}
	
	public function convertValue($input)
	{
	    return $this->checkForCollectionConversion($input);
	}
	
	public function getAllowedCollection(): string
	{
	   return $this->allowed_collection;    
	}
	
	protected function checkForCollectionConversion($input)
	{
	    if (is_numeric($input)) {
	        return Collections::loadCollection($this->allowed_collection,$input);
	    }
	    return $input;
	}
	
}

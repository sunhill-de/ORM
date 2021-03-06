<?php
/**
 * @file PropertyCalculated.php
 * Provides the property for calcualted fields
 * Lang en
 * Reviewstatus: 2021-10-14
 * Localization: complete
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, ObjectException, base
 * PSR-State: in progress
 */
namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ObjectException;
use Sunhill\ORM\Storage\StorageBase;

/**
 * The property class for calculated fields
 */
class PropertyCalculated extends PropertyField 
{
	
	protected $type = 'calculated';

	protected $features = ['complex','calculated'];
	
	protected $read_only = true;
	
//	protected $initialized = true;
	
	/**
	 * Raises an exception when called (property fields mustn't be written to)
	 */
	protected function doSetValue($value) 
	{
	    throw new PropertyException(__("Tried to write to a calculate field"));
	}
	
	/**
	 * Lets this property recalculate it self
	 */
	public function recalculate() 
	{
	    $method_name = 'calculate_'.$this->name;
	    $newvalue = $this->owner->$method_name();
	    if ($this->value !== $newvalue) { // Was there a change at all?
	        if (!$this->getDirty()) {
	            $this->shadow = $this->value;
	            $this->setDirty(true);
	            $this->initialized = true;
	        }
	        $this->value = $newvalue;
	    }
	}
	
	/**
	 * A calculated field is never uninitialized, if it is marked a so, do recalculate
	 */
	protected function initializeValue(): bool 
	{
	    $this->recalculate();
	    return true;
	}
	
	/**
	 * Inserts its value into the storage. if not already initialized, do this first
	 */
	protected function doInsert(StorageBase $storage, string $name) 
	{
	    if (!$this->initialized) {
	        $this->recalculate();
	    }
	    parent::doInsert($storage,$name);
	}
		
}

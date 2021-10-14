<?php
/**
 * @file PropertyCalculated.php
 * Provides the property for calcualted fields
 * Lang en (complete)
 * Reviewstatus: 2021-04-07
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * Dependencies: Objects, ObjectException, base
 * PSR-State: in progress
 */
namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ObjectException;
use Sunhill\ORM\Storage\StorageBase;

class PropertyCalculated extends PropertyField 
{
	
	protected $type = 'calculated';

	protected $features = ['complex','calculated'];
	
	protected $read_only = true;
	
//	protected $initialized = true;
	
	protected function doSetValue($value) 
	{
	    throw new ObjectException("Tried to write to a calculate field");
	}
	
	/**
	 * Fordert das Property auf, sich neu zu berechnen (lassen)
	 */
	public function recalculate() {
	    $method_name = 'calculate_'.$this->name;
	    $newvalue = $this->owner->$method_name();
	    if ($this->value !== $newvalue) { // Was there a change at all?
	        if (!$this->get_dirty()) {
	            $this->shadow = $this->value;
	            $this->set_dirty(true);
	            $this->initialized = true;
	        }
	        $this->value = $newvalue;
	    }
	}
	
	protected function initializeValue() {
	    $this->recalculate();
	    return true;
	}
	
	protected function do_insert(StorageBase $storage, string $name) {
	    if (!$this->initialized) {
	        $this->recalculate();
	    }
	    parent::doInsert($storage,$name);
	}
		
}

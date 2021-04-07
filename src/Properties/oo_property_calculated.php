<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ObjectException;

class oo_property_calculated extends oo_property_field {
	
	protected $type = 'calculated';

	protected $features = ['complex','calculated'];
	
	protected $read_only = true;
	
//	protected $initialized = true;
	
	protected function do_set_value($value) {
	    throw new ObjectException("Tried to write to a calculate field");
	}
	
	/**
	 * Fordert das Property auf, sich neu zu berechnen (lassen)
	 */
	public function recalculate() {
	    $method_name = 'calculate_'.$this->name;
	    $newvalue = $this->owner->$method_name();
	    if ($this->value !== $newvalue) { // Gab es überhaupt eine Änderung
	        if (!$this->get_dirty()) {
	            $this->shadow = $this->value;
	            $this->set_dirty(true);
	            $this->initialized = true;
	        }
	        $this->value = $newvalue;
	    }
	}
	
	protected function initialize_value() {
	    $this->recalculate();
	    return true;
	}
	
	protected function do_insert(\Sunhill\ORM\Storage\storage_base $storage,string $name) {
	    if (!$this->initialized) {
	        $this->recalculate();
	    }
	    parent::do_insert($storage,$name);
	}
		
}
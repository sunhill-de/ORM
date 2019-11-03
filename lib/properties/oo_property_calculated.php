<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_calculated extends oo_property_field {
	
	protected $type = 'calculated';

	protected $features = ['complex','calculated'];
	
	protected $read_only = true;
	
	protected $initialized = true;
	
	protected function do_set_value($value) {
	    throw new \Sunhill\Objects\ObjectException("Versuch ein Calulate-Field zu beschreiben");
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
	        }
	        $this->value = $newvalue;
	    }
	}
	
	protected function initialize_value() {
	    $this->recalculate();
	    return true;
	}
	
	protected function do_insert(\Sunhill\Storage\storage_base $storage,string $name) {
	    if (!$this->initialized) {
	        $this->recalculate();
	    }
	    parent::do_insert($storage,$name);
	}
	
	public function get_table_name($relation,$where) {
	    return 'caching';
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "on a.id = $letter.object_id";
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    if ($relation == 'begins with') {
	        return "value like '$value%'";
	    } else if ($relation == 'ends with') {
	        return "value like '%$value'";
	    } else if ($relation == 'consists') {
	        return "value like '%$value%'";
	    } else {
	        return "value ".$relation."'".$value."'";
	    }
	}
	
	protected function is_allowed_relation(string $relation,$value) {
	    switch ($relation) {
	        case '=':
	        case '<':
	        case '>':
	        case '>=':
	        case '<=':
	        case '<>':
	        case 'begins with':
	        case 'ends with':
	        case 'consists':
	            return is_scalar($value); break;
	        case 'in':
	            return is_array($value); break;
	        default:
	            return false;
	    }
	}
	
}
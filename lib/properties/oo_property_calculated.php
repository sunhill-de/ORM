<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_calculated extends oo_property_field {
	
	protected $type = 'calculated';

	protected $features = ['complex','calculated'];
	
	protected $readonly = true;
	
	protected $initialized = true;
	
	public function get_dirty() {
	    return true;
	}
	
	protected function do_set_value($value) {
	    throw new \Sunhill\Objects\ObjectException("Versuch ein Calulate-Field zu beschreiben");
	}
	
	protected function &do_get_value() {
	// @todo: Ein sehr schmutziger Hack, damit die Tests durchlaufen
	//    if (!$this->initialized) {
	    if (true) {
	        $method_name = 'calculate_'.$this->name;
	        $this->value = $this->owner->$method_name();
	        //$this->initialized = true;
	    }
        return $this->value;
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
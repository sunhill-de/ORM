<?php

namespace Sunhill\Properties;

class oo_property_varchar extends oo_property_field {
	
	protected $type = 'varchar';
	
	protected $features = ['object','simple'];

	protected $maxlen=255;
	
	public function get_maxlen() {
	    return $this->maxlen;
	}
	
	public function set_maxlen(int $value) {
	    $this->maxlen = $value;
	    return $this;
	}

	protected function get_individual_where($relation,$value,$letter) {
	    if ($relation == 'begins with') {
	        return $letter.'.'.$this->get_name()." like '$value%'";
	    } else if ($relation == 'ends with') {
	        return $letter.'.'.$this->get_name()." like '%$value'";
	    } else if ($relation == 'consists') {
	        return $letter.'.'.$this->get_name()." like '%$value%'";
	    } else {
	        return parent::get_individual_where($relation,$value,$letter);
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
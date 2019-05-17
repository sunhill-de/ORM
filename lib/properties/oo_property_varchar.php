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

	public function get_where($relation,$value) {
	    if ($relation == 'begins with') {
	        return $this->get_name()." like '$value%'";
	    } else if ($relation == 'ends with') {
	        return $this->get_name()." like '%$value'";
	    } else if ($relation == 'consists') {
	        return $this->get_name()." like '%$value%'";
	    } else {
	        return parent::get_where($relation,$value);
	    }
	}

}
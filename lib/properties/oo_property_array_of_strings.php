<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_array_of_strings extends oo_property_arraybase {
	
	protected $type = 'array_of_strings';
	
	protected $features = ['object','complex','array','strings'];
	
	public function set_type($type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
		
}
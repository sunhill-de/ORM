<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;

class PropertyArrayOfStrings extends PropertyArrayBase {
	
	protected $type = 'arrayOfStrings';
	
	protected $features = ['object','complex','array','strings'];
	
	public function set_type($type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
		
}
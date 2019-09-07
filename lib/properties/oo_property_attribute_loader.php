<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_attribute_loader extends oo_property {
	
	protected $type = 'attribute_loader';
	
	protected $features = ['loader'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	public function load(\Sunhill\Storage\storage_load $loader) {
	    $values = $loader->get_entity('attributes');
	    foreach ($values as $name => $value) {
	        $this->owner->$name = $value;
	    }	    
	}

}
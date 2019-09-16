<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_attribute_loader extends oo_property {
	
	protected $type = 'attribute_loader';
	
	protected $features = ['loader'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	protected function do_load(\Sunhill\Storage\storage_load $loader,$name) {
	    $values = $loader->get_entity('attributes');
	    foreach ($values as $name => $value) {
	        $this->owner->$name = $value;
	    }	    
	}

	protected function do_insert(\Sunhill\Storage\storage_insert $storage,string $tablename,string $name) {
//	    $storage->set_subvalue('xx_attributes', 'tags', $this->value);
	}
	
}
<?php

namespace Sunhill\Properties;

class oo_property_varchar extends oo_property_field {
	
	protected $type = 'varchar';
	
	protected $features = ['object','simple'];
	
	public function get_maxlen() {
	    return '255';
	}
	
	public function set_maxlen(int $value) {
	    return $this;
	}
}
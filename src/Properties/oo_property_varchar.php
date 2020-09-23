<?php

namespace Sunhill\ORM\Properties;

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

}
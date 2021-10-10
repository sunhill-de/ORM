<?php

namespace Sunhill\ORM\Properties;


class oo_property_field extends Property {
		
	protected $type;
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function get_type() {
		return $this->type;
	}
	
}
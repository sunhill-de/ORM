<?php

namespace Sunhill\Properties;


class oo_property_field extends oo_property {
		
	protected $type;
	
	protected $model_name;
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function set_model($name) {
	    return $this;
	}
	
	public function get_model() {
		return $this->model_name;
	}
	
	public function get_where($relation,$value) {
        return $this->get_name().$relation."'".$value."'";
	}
}
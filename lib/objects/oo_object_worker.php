<?php

namespace Sunhill\Objects;

abstract class oo_object_worker extends \Sunhill\base {
	
	protected $object;
	
	public function __construct($object) {
		$this->object = $object;
	}
	
	protected function walk_simple_fields($callback) {
	    $fields = $this->object->get_simple_fields();
	    foreach ($fields as $model_name=>$fields) {
	        if (!empty($model_name)) {
	            $model_name = $this->object->default_ns.'\\'.$model_name;
	            $model = new $model_name;
	            $this->$callback($model_name,$model,$fields);
	        }
	    }
	}
	
	protected function walk_complex_fields($callback) {
	    $fields = $this->object->get_complex_fields();
	    foreach ($fields as $fieldname) {
	        $property = $this->object->get_property($fieldname);
	        $this->$callback($fieldname,$property->type);
	    }
	}
	
	protected function work() {
	    $this->prepare_work();
	    $this->work_simple_fields();
	    $this->work_complex_fields();
	    $this->work_tags();
	    $this->work_attributes();
	    $this->finish_work();
	}
	
	abstract protected function work_simple_fields();
	abstract protected function work_complex_fields();
	abstract protected function work_tags();
	
	protected function work_attributes() {
	    
	}
	protected function prepare_work() {
	    // Macht nix
	}
	
	protected function finish_work() {
	    // macht nix
	}
}
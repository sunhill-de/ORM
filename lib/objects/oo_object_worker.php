<?php

namespace Sunhill\Objects;

class oo_object_worker extends \Sunhill\base {
	
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
	
}
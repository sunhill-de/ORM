<?php

namespace Sunhill\Objects;

class oo_object_loader extends oo_object_worker {
	
	public function load($id) {
		$this->object->set_id($id);
		$this->load_simple_fields();
		$this->load_complex_fields();
		$this->load_tags($id);
		return $this;		
	}
	
	private function load_simple_fields() {
		$fields = $this->object->get_simple_fields();
		foreach ($fields as $model=>$fields) {
    		    if (!empty($model)) {
    		         $model_name = $this->object->default_ns.'\\'.$model;			
    			     $model = $model_name::where('id','=',$this->object->get_id())->first();
    			     foreach ($fields as $field) {
    				        $this->object->$field = $model->$field;
    			     }
		          }
		}
		
	}
	
	private function load_complex_fields() {
		
	}
	
	private function load_tags($id) {
		$assigns = \App\tagobjectassign::where('object_id','=',$id)->get();
		foreach ($assigns as $assign) {
			$tag = new \Sunhill\Objects\oo_tag($assign->tag_id);
			$this->object->add_tag($tag);
		}
	}
		
}
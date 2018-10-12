<?php

namespace Sunhill\Objects;

class oo_object_loader extends oo_object_worker {
	
    /**
     * Der Wrapper um die Worker-Methode
     * @param int $id: Id des zu ladenden Objektes
     * @return \Sunhill\Objects\oo_object
     */
	public function load($id) {
	    $this->object->set_id($id);
	    $this->work();
		return $this->object;		
	}
	
	protected function work_simple_fields() {
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
	
	protected function work_complex_fields() {
		$this->load_object_fields();
		$this->load_string_fields();
	}
	
	private function load_object_fields() {
	     $references = \App\objectobjectassign::where('container_id','=',$this->object->get_id())->get();
	     foreach ($references as $reference) {
	        $fieldname = $reference->field;
            $property = $this->object->get_property($fieldname);
            
            // Load Object
            $object = $this->object::load_object_of($reference->element_id);
            
            if ($property->is_array()) {
                $this->object->$fieldname[$reference->index] = $object; 
            } else {
                $this->object->$fieldname = $object;                
            }
	     }
	}
	
	private function load_string_fields() {
	    $references = \App\stringobjectassign::where('container_id','=',$this->object->get_id())->get();
	    foreach ($references as $reference) {
	        $fieldname = $reference->field;
	        $this->object->$fieldname[$reference->index] = $reference->element_id;
	    }
	}
	
	protected function work_tags() {
		$assigns = \App\tagobjectassign::where('container_id','=',$this->object->get_id())->get();
		foreach ($assigns as $assign) {
			$tag = new \Sunhill\Objects\oo_tag($assign->tag_id);
			$this->object->add_tag($tag);
		}
	}
		
}
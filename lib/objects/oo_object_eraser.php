<?php

namespace Sunhill\Objects;

use Illuminate\Support\Facades\DB;

class oo_object_eraser extends oo_object_worker {
	
    /**
     * Der Wrapper um die Worker-Methode
     * @param int $id: Id des zu ladenden Objektes
     * @return \Sunhill\Objects\oo_object
     */
	public function erase() {
	    $this->work();
		return $this->object;		
	}
	
	protected function work_simple_fields() {
		$fields = $this->object->get_simple_fields();
		foreach ($fields as $model=>$fields) {
		    if (!empty($model)) {
    		         $model_name = $this->object->default_ns.'\\'.$model;			
    		         $model_name::destroy($this->object->get_id());
		    }
		}
	}
	
	protected function work_complex_fields() {
	    $this->erase_object_fields();
	    $this->erase_string_fields();
	}
	
	private function erase_object_fields() {
	    DB::statement("delete from objectobjectassigns where container_id = ".$this->object->get_id());
	    DB::statement("delete from objectobjectassigns where element_id = ".$this->object->get_id());
	}
	
	private function erase_string_fields() {
	    DB::statement("delete from stringobjectassigns where container_id = ".$this->object->get_id());
	}
	
	protected function work_tags() {
	    DB::statement("delete from tagobjectassigns where container_id = ".$this->object->get_id());
	}
		
}
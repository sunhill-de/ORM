<?php

namespace Sunhill\Objects;
use Illuminate\Support\Facades\DB;

class oo_object_creator extends oo_object_storage {
	
    
	public function store() {
		$this->work();
		return $this->object->get_id();
	}
	
	protected function store_simple_callback($model_name,$model,$fields) {
	    foreach ($fields as $field) {
	        $model->$field = $this->object->$field;
	    }
	    if ($model_name == $this->object->default_ns."\coreobject") {
	        $model->classname = get_class($this->object);
	        $model->save(); 
	        $this->object->set_id($model->id); 
	        $this->object->updated_at = $model->updated_at;
	        $this->object->created_at = $model->created_at;
	    } else { 
	        $model->id = $this->object->get_id();
	        $model->save();
	    }	    
	}
	
	protected function work_simple_fields() {
		$this->walk_simple_fields('store_simple_callback');
	}
	
	protected function work_complex_fields() {
	    $this->store_references();
	}
	
	protected function work_tags() {
		for ($i=0;$i<$this->object->get_tag_count();$i++)
		{
			$tag = $this->object->get_tag($i);
			$this->store_tag($tag,$this->object->get_id());
		}
	}

	/**
	 * Speichert eine einzelne Referenz eines Tags in der Datenbank ab
	 * @todo Hier besteht Optimierungpotential für zusammengefassten Datenbankanfragen
	 * @param oo_tag $tag
	 */
	private function store_tag(oo_tag $tag,$id) {
	    $tagid = $tag->get_id();
	    DB::statement("insert ignore into tagobjectassigns (container_id,tag_id) values ($id,$tagid)");	    
	}
	
	
}
<?php

namespace Sunhill\Objects;

class oo_object_creator extends oo_object_worker {
	
    protected $id;
    
	public function store() {
		$this->store_simple_fields();
		$this->store_complex_fields($this->id);
		$this->store_tags($this->id);
		return $this->id;
	}
	
	protected function store_simple_callback($model_name,$model,$fields) {
	    foreach ($fields as $field) {
	        $model->$field = $this->object->$field;
	    }
	    if ($model_name == $this->object->default_ns."\coreobject") {
	        $model->save();
	        $this->id = $model->id;
	    } else {
	        $model->id = $this->id;
	        $model->save();
	    }	    
	}
	
	private function store_simple_fields() {
		$this->walk_simple_fields('store_simple_callback');
	}
	
	private function store_complex_fields() {
		
	}
	
	private function store_tags($id) {
		for ($i=0;$i<$this->object->get_tag_count();$i++)
		{
			$tag = $this->object->get_tag($i);
			$this->store_tag($tag,$id);
		}
	}

	/**
	 * Speichert eine einzelne Referenz eines Tags in der Datenbank ab
	 * @todo Hier besteht Optimierungpotential für zusammengefassten Datenbankanfragen
	 * @param oo_tag $tag
	 */
	private function store_tag(oo_tag $tag,$id) {
		$test = \App\tagobjectassign::firstOrCreate(['object_id'=>$id,
				'tag_id'=>$tag->get_id()]);
	}
	
	
}
<?php

namespace Sunhill\Objects;

class oo_object_creator extends oo_object_worker {
	
	public function store() {
		$id = $this->store_simple_fields();
		$this->store_complex_fields($id);
		$this->store_tags($id);
		return $id;
	}
	
	private function store_simple_fields() {
		$fields = $this->object->get_simple_fields();
		foreach ($fields as $model_name=>$fields) {
			$model_name = $this->object->default_ns.'\\'.$model_name;
			$model = new $model_name;
			foreach ($fields as $field) {
				$model->$field = $this->object->$field;
			}
			if ($model_name == $this->object->default_ns."\object") {
				$model->save();
				$result = $model->id;
			} else {
				$model->id = $result;
				$model->save();
			}			
		}
		return $result;
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
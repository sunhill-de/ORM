<?php

namespace Sunhill\Objects;

class oo_object_updater extends oo_object_worker {
	
	public function update() {
		$this->update_simple_fields();
		$this->update_complex_fields();
		$this->update_tags();
	}
	
	private function update_simple_fields() {
		$fields = $this->object->get_changed_fields();
		foreach ($fields as $model_name=>$fields) {
			$model_name = $this->object->default_ns.'\\'.$model_name;
			$model = $model_name::where('id','=',$this->object->get_id())->first();
			$model_changed = false;
			foreach ($fields as $field) {
				if ($this->object->get_property($field)->is_simple()) {
					$model->$field = $this->object->$field;
					$model_changed = true;
				}
			}
			if ($model_changed) {
				$model->save();
			}
		}
	}
	
	private function update_complex_fields() {
		
	}
	
	private function update_tags() {
	}

	/**
	 * Speichert eine einzelne Referenz eines Tags in der Datenbank ab
	 * @todo Hier besteht Optimierungpotential für zusammengefassten Datenbankanfragen
	 * @param oo_tag $tag
	 */
	private function store_tag(oo_tag $tag) {
		$test = \App\tagobjectassign::firstOrCreate(['object_id'=>$this->object->get_id(),
				'tag_id'=>$tag->get_id()]);
	}
	
	
}
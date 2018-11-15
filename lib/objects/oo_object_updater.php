<?php

namespace Sunhill\Objects;

use Illuminate\Support\Facades\DB;


class oo_object_updater extends oo_object_storage {
	
	public function update() {
        $this->work();
        return $this;
	}
	
	protected function work_simple_fields() {
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
	
	protected function work_complex_fields() {
	    //$this->object->update_children();
	    $this->delete_references(); 
	    $this->store_references();
	}
	
	protected function work_tags() {
        $change = $this->object->get_changed_tags(); 
        foreach ($change['added'] as $added) {
            $this->store_tag($added);
            $this->object->tag_added($added);
        }
        foreach ($change['deleted'] as $deleted) {
            $this->remove_tag($deleted);
            $this->object->tag_deleted($deleted);
        }
	}

	/**
	 * Speichert eine einzelne Referenz eines Tags in der Datenbank ab
	 * @todo Hier besteht Optimierungpotential für zusammengefassten Datenbankanfragen
	 * @param oo_tag $tag
	 */
	private function store_tag(oo_tag $tag) {
	    $tagid = $tag->get_id();
	    $id = $this->object->get_id();
	    DB::statement("insert ignore into tagobjectassigns (container_id,tag_id) values ($id,$tagid)");
	}
	
	/**
	 * Speichert eine einzelne Referenz eines Tags in der Datenbank ab
	 * @todo Hier besteht Optimierungpotential für zusammengefassten Datenbankanfragen
	 * @param oo_tag $tag
	 */
	private function remove_tag(oo_tag $tag) {
	    $tagid = $tag->get_id();
	    $id = $this->object->get_id();
	    DB::statement("delete from tagobjectassigns where container_id = $id and tag_id = $tagid");
	}
	
	
}
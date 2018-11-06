<?php

namespace Sunhill\Objects;

use Illuminate\Support\Facades\DB;

abstract class oo_object_storage extends oo_object_worker {
	

    protected function delete_references() {
       //DB::table('objectobjectassigns')->where('container_id','=',$this->object->get_id())->delete();
       // DB::table('stringobjectassigns')->where('container_id','=',$this->object->get_id())->delete();
    }
    
    protected function store_references() {
        $this->walk_complex_fields('store_complex_field');        
    }
    
    protected function store_complex_field($fieldname,$type) {
        switch ($type) {
            case 'object':
                $this->store_object_field($fieldname);
                break;
            case 'array_of_objects':
                $this->store_oarray_field($fieldname);
                break;
            case 'array_of_strings':
                $this->store_sarray_field($fieldname);
                break;
        }
    }
    
    private function store_object_reference($fieldname,$element,$index) {
        DB::table('objectobjectassigns')->where([['container_id','=',$this->object->get_id()],
                                                 ['field','=',$fieldname],
                                                 ['index','=',$index]])->delete();
        $model = new \App\objectobjectassign();
        $model->container_id = $this->object->get_id();
        $model->element_id = $element;
        $model->field = $fieldname;
        $model->index = $index;
        $model->save();
    }
    
    private function store_string_reference($fieldname,$element,$index) {
        DB::table('stringobjectassigns')->where([['container_id','=',$this->object->get_id()],
        ['field','=',$fieldname],
        ['index','=',$index]])->delete();
        $model = new \App\stringobjectassign();
        $model->container_id = $this->object->get_id();
        $model->element_id = $element;
        $model->field = $fieldname;
        $model->index = $index;
        $model->save();
    }
    
    private function store_object_field($fieldname) {
        if (!is_null($this->object->$fieldname)) {
//            $this->object->$fieldname->commit();
            $reference = $this->object->$fieldname;
            if (!is_null($reference)) {
                if (!$reference->get_id()) {
                    $reference->commit();
                }
                if ($reference->get_id()) {
                    $this->store_object_reference($fieldname, $reference->get_id(), 0);
                } else {
                    var_dump($reference);
                }
            } 
        }
    }
    
    private function store_oarray_field($fieldname) {
        for ($i=0;$i<count($this->object->$fieldname);$i++) {
            if (!$this->object->$fieldname[$i]->get_id()) {
                $this->object->$fieldname[$i]->commit();
            }
            //            $this->object->$fieldname[$i]->commit();
            $this->store_object_reference($fieldname, $this->object->$fieldname[$i]->get_id(), $i);
        }
    }
    
    private function store_sarray_field($fieldname) {
        for ($i=0;$i<count($this->object->$fieldname);$i++) {
            $this->store_string_reference($fieldname, $this->object->$fieldname[$i], $i);
        }
    }
    
    
}
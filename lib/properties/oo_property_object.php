<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_object extends oo_property_field {
	
	protected $type = 'object';
	
	protected $features = ['object','complex'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	public function set_allowed_objects($object) {
	    $this->validator->set_allowed_objects($object);
	    return $this;
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geladen wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::load()
	 */
	public function load(int $id) {
	    $reference = \App\objectobjectassign::where('container_id','=',$id)
	               ->where('field','=',$this->get_name())->first();
	    if (!empty($reference)) {
    	    $object = \Sunhill\Objects\oo_object::load_object_of($reference->element_id);
    	    $this->value = $object;
    	    $this->initialized = true;
	    }
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	    if (empty($this->value)) {
	        // Falls es einen Eintrag gab, löschen
	        DB::table('stringobjectassigns')->where([['container_id','=',$id],
	            ['field','=',$this->get_name()],
	            ['index','=',0]])->delete();
	    } else {
    	    $this->value->commit();
	        $model = \App\objectobjectassign::where('container_id','=',$id)
    	    ->where('field','=',$this->get_name())->first();
    	    if (empty($model)) {
    	        $model = new \App\objectobjectassign();
    	    }
    	    $model->container_id = $id;
    	    $model->element_id = $this->value->get_id();
    	    $model->field = $this->get_name();
    	    $model->index = 0;
    	    $model->save();
	    }
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	    if (!empty($this->value)) {
	        $this->value->commit();
	        $model = new \App\objectobjectassign();
    	    $model->container_id = $id;
    	    $model->element_id = $this->value->get_id();
    	    $model->field = $this->get_name();
    	    $model->index = 0;
    	    $model->save();
	    }
	}
}
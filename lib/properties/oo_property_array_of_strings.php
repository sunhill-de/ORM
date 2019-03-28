<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_array_of_strings extends oo_property_arraybase {
	
	protected $type = 'array_of_strings';
	
	protected $model_name;
	
	protected $features = ['object','complex','array'];
	
	protected function initialize() {
		$this->initialized = true;
	}
	
	public function set_type($type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
	
	public function set_model($name) {
	    if (strpos($name,'\\') === false) {
	        $this->model_name = $this->owner->default_ns.'\\'.$name;
	    } else {
	        $this->model_name = $name;
	    }
	    return $this;
	}
	
	public function get_model() {
	    return $this->model_name;
	}
	
	public function load($id) {
	    $references = \App\stringobjectassign::where('container_id','=',$id)
	                  ->where('field','=',$this->get_name())->get();
	    foreach ($references as $reference) {
	        $this->value[$reference->index] = $reference->element_id;
	    }	    
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	    $this->set_dirty(false);
	    DB::table('stringobjectassigns')->where([['container_id','=',$id],
	        ['field','=',$this->get_name()]])->delete();
	        $this->inserted($id);
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	    foreach ($this->value as $index => $value) {
	        $model = new \App\stringobjectassign();
	        $model->container_id = $id;
	        $model->element_id = $value;
	        $model->field = $this->get_name();
	        $model->index = $index;
	        $model->save();
	    }
	}
	
	
}
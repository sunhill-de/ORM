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
	
	public function updating(int $id) {
	    if (!empty($this->value)) {
	        $this->value->commit();	        
	    }
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	    DB::table('objectobjectassigns')->where([['container_id','=',$id],
	        ['field','=',$this->get_name()],
	        ['index','=',0]])->delete();
	    if (!empty($this->value)) {
	        $this->value->commit();
    	    DB::table('objectobjectassigns')->insert(
    	            ['container_id'=>$id,
    	             'element_id'=>$this->value->get_id(),
    	             'field'=>$this->get_name(),
    	             'index'=>0]); 
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
	
	protected function value_changed($from,$to) {
	    foreach ($this->hooks as $hook) {
	        $to->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target']);
	    }
	}

	public function get_table_name($relation,$where) {
        return 'objectobjectassigns';
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "on a.id = $letter.container_id";
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    switch ($relation) {
	        case '=':
	            if (is_null($value)) {
	               $value = 'NULL'; 
	            } else if (!is_int($value)) {
	                $value = $value->get_id();
	            }
	            return "$letter.element_id = $value"; break;
	        case 'in':
	            $result = "$letter.element_id in (";
	            $first = true;
	            foreach ($value as $single_value) {
	                if (!is_int($single_value)) {
	                    $single_value = $single_value->get_id();
	                }
	                if (!$first) {
	                    $result .= ',';
	                }
	                $result .= $single_value;
	                $first = false;
	            }
	            return $result.')'; 
	            break;
	    }
	}
	
	protected function is_allowed_relation(string $relation,$value) {
	    switch ($relation) {
	        case '=':
	        case 'in':
                return true;
	        default:
	            return false;
	    }
	}
	
}
<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_array_of_objects extends oo_property_arraybase {

	protected $type = 'array_of_objects';
		
	protected $model_name;
	
	protected $features = ['object','complex','array'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	protected function initialize() {
		$this->initialized = true;	
	}
	
	public function set_allowed_objects($object) {
	    $this->validator->set_allowed_objects($object);
	    return $this;
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
	
	public function load(int $id) {
	    $references = \App\objectobjectassign::where('container_id','=',$id)
	                                           ->where('field','=',$this->get_name())->get();
	    foreach ($references as $reference) {
	        $object = \Sunhill\Objects\oo_object::load_object_of($reference->element_id);
	        $this->value[$reference->index] = $object;
	    }
	    $this->set_dirty(false);
	    $this->initialized = true;
	    $this->shadow = $this->value;
	    
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt geupdated wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::updated()
	 */
	public function updated(int $id) {
	    $this->set_dirty(false);
	    DB::table('objectobjectassigns')->where([['container_id','=',$id],
	        ['field','=',$this->get_name()]])->delete();
	    $this->inserted($id);
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::inserted()
	 */
	public function inserted(int $id) {
	    $this->set_dirty(false);
	    foreach ($this->value as $index => $object) {
    	        $object->commit();
	            $model = new \App\objectobjectassign();
    	        $model->container_id = $id;
    	        $model->element_id = $object->get_id();
    	        $model->field = $this->get_name();
    	        $model->index = $index;
    	        $model->save();
	    }
	}
	protected function value_added($value) {
	    foreach ($this->hooks as $hook) {
	        $value->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target']);
	    }	    
	}

	public function get_table_name($relation,$where) {
	    return "";
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "";
	}
	
	public function get_special_join($letter) {
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    switch ($relation) {
	        case 'has':
	            return "a.id in (select x.container_id from objectobjectassigns as x where x.element_id = ".
	   	            $this->escape($value)." and x.field = '".$this->get_name()."')";
	        case 'has not':
	            return "a.id not in (select x.container_id from objectobjectassigns as x where x.element_id = ".
	   	            $this->escape($value)." and x.field = '".$this->get_name()."')";
	        case 'one of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' or ';
	                }
	                $first = false;
	                $result .= "x.element_id = $single_value";
	            }
	            return "a.id in (select x.container_id from objectobjectassigns as x where (".$result.")".
	   	            " and x.field = '".$this->get_name()."')";
	        case 'all of':
	            $result = '';
	            $first = true;
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' and ';
	                }
	                $first = false;
	                $result .= "a.id in (select xx.container_id from objectobjectassigns as xx ".
	   	                "where xx.element_id = $single_value and xx.field = '".$this->get_name()."')";
	            }
	            return $result; break;
	        case 'none of':
	            $first = true;
	            $result = '';
	            foreach ($value as $single_value) {
	                $single_value = $this->escape($single_value);
	                if (!$first) {
	                    $result .= ' or ';
	                }
	                $first = false;
	                $result .= "x.element_id = $single_value";
	            }
	            return "a.id not in (select x.container_id from objectobjectassigns as x where (".$result.")".
	   	            " and x.field = '".$this->get_name()."')";
	        case 'empty':
	            return "a.id not in (select xx.container_id from objectobjectassigns as xx where ".
	   	            "xx.field = '".$this->get_name()."')";
	    }
	}
	
}
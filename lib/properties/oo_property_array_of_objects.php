<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_array_of_objects extends oo_property_arraybase {

	protected $type = 'array_of_objects';
		
	protected $features = ['object','complex','array','objectid'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
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
	
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name) {
	    $references = $loader->$name;
	    if (empty($references)) {
	        return;
	    }
	    foreach ($references as $index => $reference) {
	       $this->value[$index] = $reference;
	    }
	}
	
	protected function &do_get_indexed_value($index) {
	    if (is_int($this->value[$index])) {
	        $this->value[$index] = \Sunhill\Objects\oo_object::load_object_of($value[$index]);
	    }
	    return $this->value[$index];
	}
	
	protected function do_insert(\Sunhill\Storage\storage_base $storage,string $name) {
	    $result = [];
	    foreach ($this->value as $index => $value) {
	        if (is_int($value)) {
	            $result[$index] = $value;
	        } else {
	            $result[$index] = $value->get_id();
	        }
	    }
	    $storage->set_entity($name,$result);
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
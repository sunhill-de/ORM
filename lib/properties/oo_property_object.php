<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class oo_property_object extends oo_property_field {
	
	protected $type = 'object';
	
	protected $features = ['object','complex','objectid'];
	
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
	protected function do_load(\Sunhill\Storage\storage_base $storage,$name) {
        $reference = $storage->$name;
	    if (!empty($reference)) {
	        $this->do_set_value($reference);
	    }
	}
	
	/**
	 * Überschriebene Methode von oo_property. Prüft, ob die Objekt-ID bisher nur als Nummer gespeichert war. Wenn ja, wird das
	 * Objekt lazy geladen.
	 * {@inheritDoc}
	 * @see \Sunhill\Properties\oo_property::do_get_value()
	 */
	protected function &do_get_value() {
	    if (is_int($this->value)) {
	        $this->value = \Sunhill\Objects\oo_object::load_object_of($this->value);
	    }
        return $this->value;	    
	}
	
	protected function do_insert(\Sunhill\Storage\storage_base $storage,string $name) {
	    if (is_int($this->value)) {
	        $storage->set_entity($name,$this->value);
	    } else if (is_object($this->value)){
	        $storage->set_entity($name,$this->value->get_id());
	    }
	}
	
	public function inserting(\Sunhill\Storage\storage_base $storage) {
	    if (!empty($this->value) && !(is_int($this->value))) {
	        $this->value->commit();
	    }
	}
	
	protected function do_update(\Sunhill\Storage\storage_base $storage,string $name) {
	   $this->do_insert($storage,$name);
	}
	
	public function updating(\Sunhill\Storage\storage_base $storage) {
        $this->inserting($storage);
	}
	
	protected function value_changed($from,$to) {
	    foreach ($this->hooks as $hook) {
	        $to->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target']);
	    }
	}

	public function get_table_name($relation,$where) {
        return '';
	}
	
	public function get_table_join($relation,$where,$letter) {
	    return "";
	}
	
	protected function get_individual_where($relation,$value,$letter) {
	    switch ($relation) {
	        case '=':
	            if (is_null($value)) {
                    return "a.id not in (select zz.container_id from objectobjectassigns as zz where zz.field = '".$this->get_name()."')";
	            } else {
	                if (!is_int($value)) {
	                    $value = $value->get_id();
	                }
	                return "a.id in (select zz.container_id from objectobjectassigns as zz where zz.field = '".$this->get_name().
	                       "' and zz.element_id = ".$this->escape($value).")"; break;
	            }
	        case 'in':
	            $result = "a.id in (select zz.container_id from objectobjectassigns as zz where zz.field = '".$this->get_name().
	                      "' and zz.element_id in (";
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
	            return $result.'))'; 
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
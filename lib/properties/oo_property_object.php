<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

require_once('property_traits.php');

class oo_property_object extends oo_property_field {
	
    use LazyIDLoading;
    
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
	
	public function reinsert(\Sunhill\Storage\storage_base $storage) {
	    $this->commit_child_if_loaded($this->value);
	    $this->do_update($storage,$this->get_name());	   
	}
	
	protected function do_insert(\Sunhill\Storage\storage_base $storage,string $name) {
	    if (is_int($this->value)) {
	        $storage->set_entity($name,$this->value);
	    } else if (is_object($this->value)){
	        $id = $this->value->get_id();
	        if (is_null($id)) {
	            // Wir haben zirkuläre Referenzen, d.h. ein Objekt bezieht sich auf ein anderes Objekt, 
	            // Welches noch nicht eingefügt wurde ($id = null)
	            $this->owner->set_needs_recommit(); // Hier ist ein Recommit-Fällig
	           // D.h. wir können die Objektreferenzen erst einfügen, wenn alle abhängigen Objekte
	           // eingefügt wurden und eine ID haben.
	        } else {
	           $storage->set_entity($name,$this->value->get_id());
	        }
	    }
	}
	
	public function inserting(\Sunhill\Storage\storage_base $storage) {
	    $this->commit_child_if_loaded($this->value);
	}
	
	/**
	 * Erzeugt ein Diff-Array.
	 * d.h. es wird ein Array mit (mindestens) zwei Elementen zurückgebene:
	 * FROM ist der alte Wert
	 * TO ist der neue Wert
	 * @param int $type Soll bei Objekten nur die ID oder das gesamte Objekt zurückgegeben werden
	 * @return void[]|\Sunhill\Properties\oo_property[]
	 */
	public function get_diff_array(int $type=PD_VALUE) {
	    $diff = parent::get_diff_array($type);
	    if ($type == PD_ID) {
	        return [
	            'FROM'=>$this->get_local_id($this->shadow),
	            'TO'=>$this->get_local_id($this->value)
	        ];
	    } else {
	        return $diff;
	    }
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
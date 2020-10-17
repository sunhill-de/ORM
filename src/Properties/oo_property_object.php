<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Storage\storage_base;

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
	 * @see \Sunhill\ORM\Properties\oo_property::load()
	 */
	protected function do_load(storage_base $storage,$name) {
        $reference = $storage->$name;
	    if (!empty($reference)) {
	        $this->do_set_value($reference);
	    }
	}
	
	/**
	 * Überschriebene Methode von oo_property. Prüft, ob die Objekt-ID bisher nur als Nummer gespeichert war. Wenn ja, wird das
	 * Objekt lazy geladen.
	 * {@inheritDoc}
	 * @see \Sunhill\ORM\Properties\oo_property::do_get_value()
	 */
	protected function &do_get_value() {
	    if (is_int($this->value)) {
	        $this->value = Objects::load($this->value);
	    }
        return $this->value;	    
	}
	
	protected function do_insert(\Sunhill\ORM\Storage\storage_base $storage,string $name) {
	    if (is_int($this->value)) {
	        $storage->set_entity($name,$this->value);
	    } else if (is_object($this->value)){
	        $storage->set_entity($name,$this->value->get_id());
	    }
	}
	
	public function inserting(\Sunhill\ORM\Storage\storage_base $storage) {
	    $this->commit_child_if_loaded($this->value);
	}

	public function inserted(\Sunhill\ORM\Storage\storage_base $storage) {
	    $this->commit_child_if_loaded($this->value);	    
	}
	
	/**
	 * Erzeugt ein Diff-Array.
	 * d.h. es wird ein Array mit (mindestens) zwei Elementen zurückgebene:
	 * FROM ist der alte Wert
	 * TO ist der neue Wert
	 * @param int $type Soll bei Objekten nur die ID oder das gesamte Objekt zurückgegeben werden
	 * @return void[]|\Sunhill\ORM\Properties\oo_property[]
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
	
	public function updating(\Sunhill\ORM\Storage\storage_base $storage) {
        $this->inserting($storage);
	}
	
	public function updated(\Sunhill\ORM\Storage\storage_base $storage) {
	    $this->updating($storage);
	}
	
	protected function value_changed($from,$to) {
	    foreach ($this->hooks as $hook) {
	        $to->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target']);
	    }
	}
	
}
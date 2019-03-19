<?php

namespace Sunhill;

class PropertyException extends \Exception {}

class propertieshaving extends hookable {
	
    private $state = 'normal';
    
    private $readonly = false;
    
    protected $properties;
    
    /**
     * Konstruktur, ruft nur zusätzlich setup_properties auf
     */
	public function __construct() {
		parent::__construct();
		$this->setup_properties();
	}
	
	/**
	 * Setzt einen neuen Wert für Readonly
	 * @param bool $value
	 * @return \Sunhill\propertieshaving
	 */
	protected function set_readonly(bool $value) {
	    $this->readonly = $value;
	    return $this;
	}
	
	/**
	 * Liefert den Wert für Readonly zurück
	 * @return boolean|\Sunhill\bool
	 */
	protected function get_readonly() {
	    return $this->readonly;
	}
	
// ============================== State-Handling ===========================================	
	protected function set_state(string $state) {
	    $this->state = $state;
	    return $this;
	}

	protected function get_state() {
	    return $this->state;
	}
	
	protected function is_committing() {
	    return ($this->get_state() == 'committing');
	}
	
	protected function is_invalid() {
	    return $this->get_state() == 'invalid';
	}
	
// ===================================== Property-Handling ========================================	
	/**
	 * Wird vom Constructor aufgerufen, um die Properties zu initialisieren.
	 * Abgeleitete Objekte müssen immer die Elternmethoden mit aufrufen.
	 */
	protected function setup_properties() {
	    $this->properties = array();
	}

	public function __get($name) {
	    $this->check_for_hook('GET',$name,array(
	        'value'=>$this->properties[$name]->get_value()));
	    if (isset($this->properties[$name])) {
	        return $this->properties[$name]->get_value();
	    } else {
	        return parent::__get($name);
	    }
	}
	
	public function __set($name,$value) {
	    if (isset($this->properties[$name])) {
	        if ($this->get_readonly()) {
	            throw new \Exception("Property '$name' in der Readonly Phase verändert.");
	        } else {
	            $this->properties[$name]->set_value($value);
	            $this->check_for_hook('SET',$name,array(
	                'from'=>$this->properties[$name]->get_old_value(),
	                'to'=>$value));
	            if (!$this->properties[$name]->is_simple()) {
	                $this->check_for_hook('EXTERNAL',$name,array('to'=>$value,'from'=>$this->properties[$name]->get_old_value()));
	            }
	            if ($this->properties[$name]->get_dirty()) {
	                $this->check_for_hook('FIELDCHANGE',$name,array(
	                    'from'=>$this->properties[$name]->get_old_value(),
	                    'to'=>$this->properties[$name]->get_value()));
	            }
	        }
	    } else {
	        return parent::__set($name,$value);
	    }
	}
	
	/**
	 * Liefert das Property-Objekt der Property $name zurück
	 * @param string $name Name der Property
	 * @return oo_property
	 */
	public function get_property($name) {
	    if (!isset($this->properties[$name])) {
	        throw new UnknownPropertyException("Unbekannter Property '$property'");
	    }
	    return $this->properties[$name];
	}
	
	protected function add_property($name,$type) {
	    $property_name = '\Sunhill\Properties\oo_property_'.$type;
	    $property = new $property_name($this);
	    $property->set_name($name);
	    $property->set_type($type);
	    $this->properties[$name] = $property;
	    return $property;
	}
	
	/**
	 * Liefert alle Properties zurück, die ein bestimmtes Feature haben
	 * @param string $feature
	 * @return unknown[]
	 */
	protected function get_properties_with_feature(string $feature) {
	    $result = array();
	    foreach ($this->properties as $name => $property) {
	        if ($property->has_feature($feature)) {
	            $result[$name] = $property;
	        }
	    }
	    return $result;
	}
}
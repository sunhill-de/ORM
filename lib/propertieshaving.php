<?php

namespace Sunhill;

class propertieshaving extends hookable {
	
    private $state = 'normal';
    
    private $readonly = false;
    
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
		
}
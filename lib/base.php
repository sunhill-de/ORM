<?php

namespace Sunhill;

/**
 * Macht zur Zeit erstmal nichts, dient nur als gemeinsamer Vorfahre für die weiteren crawler-Klassen
 * @author klaus
 *
 */
class base {
	
    public function __construct() {
    }
    
    public function __get($varname) {
		$method = "get_$varname";
		if (method_exists($this,$method)) {
			return $this->$method();
		} else {
			throw new \Exception("Variable '$varname' nicht gefunden.");
		}
	}
	
	
	public function __set($varname,$value) {
		$method = "set_$varname";
		if (method_exists($this,$method)) {
			return $this->$method($value);
		} else {
			throw new \Exception("Variable '$varname' nicht gefunden.");
		}
	}
	
}
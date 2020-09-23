<?php
/**
 * @file base.php
 * Beinhaltet die Basisklasse und Basisexception für das Sunhill-Framework
 */

namespace Sunhill\ORM;


/**
 * Basisklasse für Exceptions im Sunhill-Framework
 * @author lokal
 *
 */
class SunhillException extends \Exception {}

/**
 * Basisklasse für alle Klassen innhalb des sunhill Frameworks 
 * Macht zur Zeit erstmal nichts, dient nur als gemeinsamer Vorfahre für die weiteren sunhill-Klassen
 * @author klaus
 *
 */
class base {
	
    /**
     * Leerer Konstruktor. Wird nur definiert, damit parent::__construct() immer funktioniert
     */
    public function __construct() {        
    }
    
    /**
     * Catchall für unbekannte Variablen, versucht immer zunächst eine getter-Methode zu finden
     * @param unknown $varname Name der Variablen
     * @throws \Exception Wird geworfen, wenn es keinen Getter gibt
     * @return unknown Wert der Variablen
     */
    public function __get($varname) {
		$method = "get_$varname";
		if (method_exists($this,$method)) {
			return $this->$method();
		} else {
			throw new SunhillException("Variable '$varname' nicht gefunden.");
		}
	}
	
    /**
     * Set-Catchall für unbekannte Variablen. Versucht immer zunächst eine Setter-Methode zu finden
     * @param unknown $varname Name der Variablen
     * @param unknown $value Wert der Variablen
     * @throws \Exception Wird geworfen, wenn es keinen Setter gibt
     * @return unknown
     */
	public function __set($varname,$value) {
		$method = "set_$varname";
		if (method_exists($this,$method)) {
			return $this->$method($value);
		} else {
			throw new SunhillException("Variable '$varname' nicht gefunden.");
		}
	}
	
}
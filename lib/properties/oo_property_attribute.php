<?php

namespace Sunhill\Properties;

use Illuminate\Support\Facades\DB;

class AttributeException extends \Exception {}

class oo_property_attribute extends oo_property {
	
	protected $type = 'attribute';
	
	protected $features = ['attribute','complex'];
	
	protected $allowed_objects;
	
	protected $property;
	
    protected $attribute_id;
    
    protected $value_id;
    
    protected $attribute_name;

    protected $attribute_type;
    
	public function initialize() {
		$this->initialized = true;
	}

// ===================================== Laden ===========================================	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name) {	    
	    $this->attribute_name = $name;
	    $this->attribute_id = $loader->entities['attributes'][$name]['attribute_id'];	    
	    $this->value = $this->extract_value($loader);
	    $this->allowed_objects = $loader->entities['attributes'][$name]['allowedobjects'];
	    $this->attribute_type = $loader->entities['attributes'][$name]['type'];
	    $this->property = $loader->entities['attributes'][$name]['property'];
	    $this->value_id = $loader->entities['attributes'][$name]['value_id'];
	}
	
	/**
	 * Ermittelt den Wert des Attributs aus dem Storage. Defaultmäßig ist dies value, muss von Textattributen
	 * überschrieben werden.
	 * @param \Sunhill\Storage\storage_base $loader
	 */
	protected function extract_value(\Sunhill\Storage\storage_base $loader) {
	    return $this->value = $loader->entities['attributes'][$this->attribute_name]['value'];    
	}

// ============================ Einfügen ========================================	
	protected function do_insert(\Sunhill\Storage\storage_base $loader,$name) {
	    $loader->entities['attributes'][$name] = 
	       ['value'=>$this->value];    
	}
	
	/**
	 * Wird aufgerufen, nachdem das Elternobjekt eingefügt wurde
	 */
	public function inserted(\Sunhill\Storage\storage_base $storage) {
	}
	
	// ============================ Statische Funktionen ===========================
	static public function search($name) {
	    $property = DB::table('attributes')->where('name','=',$name)->first();
	    if (empty($property)) {
	        return false;
	    } else {
	        return $property;
	    }
	}
}
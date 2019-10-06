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
    
	public function initialize() {
		$this->initialized = true;
	}
	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name) {
	    $this->value = $loader->entities['attributes'][$name]['value'];
	    $this->allowed_objects = $loader->entities['attributes'][$name]['allowedobjects'];
	    $this->property = $loader->entities['attributes'][$name]['property'];
	    $this->attribute_id = $loader->entities['attributes'][$name]['attribute_id'];
	}
	
	protected function do_insert(\Sunhill\Storage\storage_base $loader,$name) {
	    $loader->entities['attributes'][$name] = 
	       ['value'=>$this->value];    
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
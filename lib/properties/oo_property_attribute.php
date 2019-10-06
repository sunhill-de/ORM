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
		$attribute = self::search($this->type);
	    $this->allowed_objects = $attribute->allowed_objects;
	    $this->property = $attribute->property;
	    $this->attribute_id = $attribute->id;
		$this->initialized = true;
	}
	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function do_load(\Sunhill\Storage\storage_base $loader,$name) {
	    $this->value = $loader->entities['attributes'][$name]['value'];
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
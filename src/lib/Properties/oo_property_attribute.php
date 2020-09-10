<?php

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;

class AttributeException extends \Exception {}

class oo_property_attribute extends oo_property {
	
	protected $type = 'attribute';
	
	protected $features = ['attribute','complex'];
	
	protected $allowed_objects;
	
	protected $property;
	
    protected $attribute_id;
    
    protected $attribute_name;

    protected $attribute_type;
    
	public function initialize() {
		$this->initialized = true;
	}

	public function set_allowed_objects(string $allowed_objects) {
	    $this->allowed_objects = $allowed_objects;
	    return $this;
	}
	
	public function set_attribute_id(int $id) {
	    $this->attribute_id = $id;
	    return $this;
	}
	
	public function set_attribute_name(string $name) {
	    $this->attribute_name = $name;
	    return $this;
	}
	
	public function set_attribute_type(string $type) {
	    $this->attribute_type = $type;
	    return $this;
	}
	
	public function set_attribute_property(string $property) {
	    $this->attribute_property = $property;
	    return $this;
	}
	
// ===================================== Laden ===========================================	
	/**
	 * Individuell überschreibbare Methode, die dem Property erlaub, besondere Lademethoden zu verwenden
	 * @param \Sunhill\ORM\Storage\storage_load $loader
	 * @param unknown $name
	 */
	protected function do_load(\Sunhill\ORM\Storage\storage_base $loader,$name) {	    
	    $this->attribute_name = $name;
	    $this->attribute_id = $loader->entities['attributes'][$name]['attribute_id'];	    
	    $this->value = $this->extract_value($loader);
	    $this->allowed_objects = $loader->entities['attributes'][$name]['allowedobjects'];
	    $this->attribute_type = $loader->entities['attributes'][$name]['type'];
	    $this->property = $loader->entities['attributes'][$name]['property'];
	}
	
	/**
	 * Ermittelt den Wert des Attributs aus dem Storage. Defaultmäßig ist dies value, muss von Textattributen
	 * überschrieben werden.
	 * @param \Sunhill\ORM\Storage\storage_base $loader
	 */
	protected function extract_value(\Sunhill\ORM\Storage\storage_base $loader) {
	    return $this->value = $loader->entities['attributes'][$this->attribute_name]['value'];    
	}

// ============================ Einfügen ========================================	
	protected function do_insert(\Sunhill\ORM\Storage\storage_base $storage,$name) {
        $storage->entities['attributes'][$name] = [
            'name'=>$this->attribute_name,
            'attribute_id'=>$this->attribute_id,
            'allowed_objects'=>$this->allowed_objects,
            'type'=>$this->attribute_type,
            'property'=>$this->property
        ];
        $this->insert_value($storage);        
	}
	
	protected function insert_value(\Sunhill\ORM\Storage\storage_base $storage) {
	   $storage->entities['attributes'][$this->attribute_name]['value'] = $this->value;    
	   $storage->entities['attributes'][$this->attribute_name]['textvalue'] = '';
	}
	
// ================================= Update =========================================
	/**
	 * Erzeugt ein Diff-Array.
	 * d.h. es wird ein Array mit (mindestens) zwei Elementen zurückgebene:
	 * FROM ist der alte Wert
	 * TO ist der neue Wert
	 * @param int $type Soll bei Objekten nur die ID oder das gesamte Objekt zurückgegeben werden
	 * @return void[]|\Sunhill\ORM\Properties\oo_property[]
	 */
	public function get_diff_array(int $type=PD_VALUE) {
        $result = [
            'attribute_id'=>$this->attribute_id,
            'object_id'=>$this->owner->get_id(),
            'name'=>'general_attribute',
            'allowedobjects'=>"\\Sunhill\\Objects\\oo_object",
            'type'=>'int',
            'property'=>''            
        ];
        if ($this->attribute_type == 'text') {
            $result['textvalue']=['FROM'=>$this->shadow,'TO'=>$this->value];
            $result['value']=['FROM'=>'','TO'=>is_null($this->value)?null:''];            
        } else {
            $result['value']=['FROM'=>$this->shadow,'TO'=>$this->value];
            $result['textvalue']=['FROM'=>'','TO'=>is_null($this->value)?null:''];            
        }
        return $result;
	}
	
	public function do_update($storage, $name) {
	    $diff = $this->get_diff_array(PD_ID);
	    $storage->entities['attributes'][$this->attribute_name] = $diff;
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
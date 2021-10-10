<?php
/**
 * @file PropertyArrayOfObjects.php
 * A propertx that represents an array of objects
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-11
 * Localization: none
 * Documentation: complete
 * Tests: unknown
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Objects\ORMObject;

class PropertyArrayOfObjects extends PropertyArrayBase {

	protected $type = 'arrayOfObject';
		
	protected $features = ['object','complex','array','objectid'];
	
	protected $initialized = true;
	
	protected $validator_name = 'object_validator';
	
	public function set_allowed_objects($object) {
	    $this->validator->set_allowed_objects($object);
	    return $this;
	}

	public function set_type($type) {
	    $this->type = $type;
	    return $this;
	}
	
	public function get_type() {
	    return $this->type;
	}
	
	protected function NormalizeValue($value) {
	    if (is_int($value)) {
	        return $value;
	    } else if (is_a($value,ORMObject::class)) {
	        return $value->get_id();
	    }
	}
	
	protected function do_load(\Sunhill\ORM\Storage\storage_base $loader,$name) {
	    $references = $loader->$name;
	    if (empty($references)) {
	        return;
	    }
	    foreach ($references as $index => $reference) {
	       $this->value[$index] = $reference;
	    }
	}
	
	protected function &do_get_indexed_value($index) {
	    if (is_int($this->value[$index])) {
	        $this->value[$index] = Objects::load($this->value[$index]);
	    }
	    return $this->value[$index];
	}
	
	protected function do_insert(\Sunhill\ORM\Storage\storage_base $storage,string $name) {
	    $result = [];
	    foreach ($this->value as $index => $value) {
	        if (is_int($value)) {
	            $result[$index] = $value;
	        } else {
	            $result[$index] = $value->get_id();
	        }
	    }
	    $storage->set_entity($name,$result);
	}
	
	public function inserting(\Sunhill\ORM\Storage\storage_base $storage) {
	    if (!empty($this->value)) {
	        foreach ($this->value as $index=>$element) {
	            if (!is_int($element)) {
	                $element->commit();
	            } else if (Objects::isCached($element)) {
	                // Wenn es im Cache ist, kann es per seiteneffekt manipuliert worden sein
	                $this->value[$index] = Objects::load($element);	
	                $this->value[$index]->commit();
	            }
	        }
	    }
	}
	
	private function get_local_id($test) {
	    if (is_null($test)) {
	        return null;
	    } else if (is_int($test)) {
	        return $test;
	    } else {
	        return $test->get_id();
	    }
	}
	
	/**
	 * Erzeugt ein Diff-Array.
	 * d.h. es wird ein Array mit (mindestens) zwei Elementen zurückgebene:
	 * FROM ist der alte Wert
	 * TO ist der neue Wert
	 * @param int $type Soll bei Objekten nur die ID oder das gesamte Objekt zurückgegeben werden
	 * @return void[]|\Sunhill\ORM\Properties\Property[]
	 */
	public function get_diff_array(int $type=PD_VALUE) {
	    $diff = parent::get_diff_array($type);
	    if ($type == PD_ID) {
	        $result = ['FROM'=>[],'TO'=>[],'ADD'=>[],'DELETE'=>[],'NEW'=>[],'REMOVED'=>[]];
	        foreach ($diff as $name=>$item) {
	            if (empty($item)) {
	                continue;
	            }
	            foreach ($item as $index=>$entry) {
	                $result[$name][$index] = $this->get_local_id($entry);
	            }
	        }
	        return $result;
	    } else {
	        return $diff;
	    }
	}
	
	public function updating(\Sunhill\ORM\Storage\storage_base $storage) {
	    $this->inserting($storage);
	}
	
	protected function value_added($value) {
	    foreach ($this->hooks as $hook) {
	        $value->add_hook($hook['action'],$hook['hook'],$hook['subaction'],$hook['target']);
	    }	    
	}
	
}
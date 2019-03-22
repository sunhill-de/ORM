<?php

namespace Sunhill\Properties;

class oo_property_arraybase extends oo_property implements \ArrayAccess,\Countable {

	protected $initialized = true;
	
	protected function initialize() {
		$this->initialized = true;	
	}

	private function check_array() {
	    if (!$this->is_array()) {
	        throw new \Exception('Die Property "'.$this->name.'" wurde mit array Funktionen aufgerufen obwohl vom Typ "'.$this->type.'"');
	    }
	}
	public function offsetExists($offset) {
	    $this->check_array();
	    return isset($this->value[$offset]);
	}
	
	public function offsetGet($offset) {
	    $this->check_array();
	    return $this->value[$offset];
	}
	
	public function offsetSet($offset, $value) {
	    $this->check_array();
	    if (!$this->dirty) {
	        $this->shadow = $this->value;
	        $this->dirty = true;
	    }
	    if (isset($offset)) {
	        $this->value[$offset] = $this->validate($value);
	    } else {
	        $this->value[] = $this->validate($value);
	    }
	}
	
	public function offsetUnset($offset) {
	    $this->check_array();
	    if (!$this->dirty) {
	        $this->shadow = $this->value;
	        $this->dirty = true;
	    }
	    unset($this->value[$offset]);
	}
	
	public function count() {
	    $this->check_array();
	    return count($this->value);
	}
	
	private function array_search($needle,$haystack) {
	    foreach ($haystack as $entry) {
	        if ($needle === $entry) {
	            return true;
	        }
	    }
	    return false;
	}
	
	public function get_array_diff() {
	    $this->check_array();
	    $result = ['NEW'=>array(),'REMOVED'=>array()];
	    if (isset($this->shadow)) {
    	    foreach ($this->shadow as $oldentry) {
    	        if ($this->array_search($oldentry,$this->value)===false) {
    	            $result['REMOVED'][] = $oldentry;
    	        }
    	    }
	    }
	    foreach ($this->value as $newentry) {
	        if ($this->array_search($newentry,$this->shadow)===false) {
	            $result['NEW'][] = $newentry;
	        }
	    }
	    return $result;
	}
	
	
}
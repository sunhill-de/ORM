<?php

namespace Sunhill\ORM\Properties;

class oo_property_arraybase extends oo_property implements \ArrayAccess,\Countable {

	protected $initialized = true;
	
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
	    return $this->do_get_indexed_value($offset);
	}
	
	public function offsetSet($offset, $value) {
	    $this->check_array();
	    if (!$this->dirty) {
	        $this->shadow = $this->value;
	        $this->dirty = true;
	        $this->initialized = true;
	    }
	    $value = $this->validate($value);
	    if (isset($offset)) {
	        $this->value[$offset] = $value;
	    } else {
	        $this->value[] = $value;
	    }
	    $this->value_added($value);
	    if (!empty($this->owner)) {
	       $this->owner->array_field_new_entry($this->get_name(),$offset,$value);
	    }
	}
	
	public function offsetUnset($offset) {
	    $this->check_array();
	    if (!$this->dirty) {
	        $this->shadow = $this->value;
	        $this->dirty = true;
	    }
	    if (!empty($this->owner)) {
	        $this->owner->array_field_removed_entry($this->get_name(),$offset,$this->value[$offset]);
	    }
	    $this->value_removed($this->value[$offset]);
	    unset($this->value[$offset]);
	    $this->value = array_values($this->value); // Reindex
	}
	
	protected function value_added($value) {
	       
	}
	
	protected function value_removed($value) {
	    
	}
	
	public function count() {
	    $this->check_array();
	    return count($this->value);
	}
	
	private function array_search($needle,$haystack) {
	    if (!is_array($haystack)) {
	        return false;
	    }
	    foreach ($haystack as $entry) {
	        if ($needle === $entry) {
	            return true;
	        }
	    }
	    return false;
	}
		
	/**
	 * Überschreibt die geerbte Methode und ergänzt das Resultat noch um die Einträge ADD,DELETE, NEW und REMOVED
	 * @return array[]
	 */
	public function get_diff_array(int $type=PD_VALUE) {
	    $this->check_array();
	    $result = ['FROM'=>[],'TO'=>[],'ADD'=>[],'DELETE'=>[],'NEW'=>[],'REMOVED'=>[]];
	    if (isset($this->shadow)) {
	        foreach ($this->shadow as $index=>$oldentry) {
	            $result['FROM'][$index] = $this->get_diff_entry($oldentry, $type);
	            if ($this->array_search($oldentry,$this->value)===false) {
	                $result['DELETE'][$index] = $this->get_diff_entry($oldentry,$type);
	                $result['REMOVED'][] = $this->get_diff_entry($oldentry,$type);
	            }
	        }
	    }
	    if (isset($this->value)) {
    	    foreach ($this->value as $index=>$newentry) {
    	        $result['TO'][$index] = $this->get_diff_entry($newentry,$type);
    	        if ($this->array_search($newentry,$this->shadow)===false) {
    	            $result['ADD'][$index] = $this->get_diff_entry($newentry,$type);
    	            $result['NEW'][] = $this->get_diff_entry($newentry,$type);
    	        }
    	    }
	    }
    	return $result;
	}
	
	protected function is_allowed_relation(string $relation,$value) {
	    switch ($relation) {
	        case 'has':
	        case 'has not':
	            return is_scalar($value); break;	            
	        case 'one of':
	        case 'none of':
	        case 'all of':
	            return is_array($value); break;
	        case 'empty':
	            return true; break;
	        default:
	            return false;
	    }
	}
	
}
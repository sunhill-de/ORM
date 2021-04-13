<?php

namespace Sunhill\ORM\Properties;

class oo_property_arraybase extends oo_property implements \ArrayAccess,\Countable,\Iterator {
    
	protected $initialized = true;
	
	protected $pointer = 0;

	/**
	 * Checks if the property exports the array feature
	 * @throws \Exception
	 */
	private function check_array() {
	    if (!$this->is_array()) {
	        throw new \Exception('The property "'.$this->name.'" if of type "'.$this->type.'" and doesnt have the array feature');
	    }
	}

	/**
	 * Returns the current element of the foreach loop
	 * @return mixed
	 */
	public function current (  ) {
	    return $this->value[$this->pointer];
	}
	
	/**
	 * Returns the current key of the foreach loop
	 * @return unknown
	 */
	public function key (  ) {
	    return $this->pointer;
	}
	
	/**
	 * Sets the pointer to the next element
	 */
	public function next (  ) {
	    $this->pointer++;
	}
	
	/**
	 * Rewinds the pointer
	 */
	public function rewind (  ) {
	    $this->pointer = 0;
	}
	
	/**
	 * Checks if the pointer points to a valid element
	 * @return boolean
	 */
	public function valid (  ) {
	    return (($this->pointer >= 0) && ($this->pointer < count($this->value)));
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
	
	/**
	 * Returns the number of entries in this array
	 * {@inheritDoc}
	 * @see Countable::count()
	 */
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
	
	/**
	 * This method normalizes the given value $value so that arrays with for example lazy loading could
	 * use objects and ids
	 * @param unknown $value
	 * @return unknown
	 */
	protected function NormalizeValue($value) {
	    return $value;
	}
	
	/**
	 * Tests if the element $value is in this array
	 * @param unknown $value
	 * @return boolean true if its in otherwise false
	 */
	public function IsElementIn($value) {
	   $value = $this->NormalizeValue($value);
	   foreach ($this->value as $test) {
	       if ($this->NormalizeValue($test) == $value) {
	           return true;
	       }
	   }
	   return false;
	}
	
}
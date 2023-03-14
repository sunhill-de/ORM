<?php

/**
 * @file PropertyArrayBase.php
 * The base class for array like properties
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-04-16
 * Localization: no localization
 * Documentation: complete
 * Tests: Unit/Properties/ArrayPropertyTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties;

class PropertyArrayBase extends Property implements \ArrayAccess,\Countable,\Iterator 
{
    
	protected $initialized = true;
	
	protected $pointer = 0;

	public function __construct()
	{
	    parent::__construct();
	    $this->value = [];
	}
	
	/**
	 * Checks if the property exports the array feature
	 * @throws \Exception
	 */
	private function checkArray() 
	{
	    if (!$this->is_array()) {
	        throw new \Exception('The property "'.$this->name.'" if of type "'.$this->type.'" and doesnt have the array feature');
	    }
	}

	/**
	 * Returns the current element of the foreach loop
	 * @return mixed
	 */
	public function current () 
	{
	    return $this->value[$this->pointer];
	}
	
	/**
	 * Returns the current key of the foreach loop
	 * @return unknown
	 */
	public function key () 
	{
	    return $this->pointer;
	}
	
	/**
	 * Sets the pointer to the next element
	 */
	public function next () 
	{
	    $this->pointer++;
	}
	
	/**
	 * Rewinds the pointer
	 */
	public function rewind () 
	{
	    $this->pointer = 0;
	}
	
	/**
	 * Checks if the pointer points to a valid element
	 * @return boolean
	 */
	public function valid () 
	{
	    return (($this->pointer >= 0) && ($this->pointer < count($this->value)));
	}
	
	public function offsetExists($offset) 
	{
	    $this->checkArray();
	    return isset($this->value[$offset]);
	}
	
	public function offsetGet($offset) 
	{
	    $this->checkArray();
	    return $this->doGetIndexedValue($offset);
	}
	
	public function offsetSet($offset, $value) 
	{
	    $this->checkArray();
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
	    $this->valueAdded($value);
	    if (!empty($this->owner)) {
	       $this->owner->arrayFieldNewEntry($this->getName(),$offset,$value);
	    }
	}
	
	public function offsetUnset($offset) 
	{
	    $this->checkArray();
	    if (!$this->dirty) {
	        $this->shadow = $this->value;
	        $this->dirty = true;
	    }
	    if (!empty($this->owner)) {
	        $this->owner->arrayFieldRemovedEntry($this->getName(),$offset,$this->value[$offset]);
	    }
	    $this->valueRemoved($this->value[$offset]);
	    unset($this->value[$offset]);
	    $this->value = array_values($this->value); // Reindex
	}
	
	protected function valueAdded($value) 
	{
	       
	}
	
	protected function valueRemoved($value) 
	{
	    
	}
	
	/**
	 * Returns the number of entries in this array
	 * {@inheritDoc}
	 * @see Countable::count()
	 */
	public function count()
	{
	    $this->checkArray();
	    if (is_null($this->value)) {
	        return 0;
	    } else {
	        return count($this->value);
	    }
	}
	
	private function objectsEqual($obj1, $obj2)
	{
	    return ($obj1 === $obj2);
	}
	
	private function arraySearch($needle, $haystack) 
	{
	    if (!is_array($haystack)) {
	        return false;
	    }
	    foreach ($haystack as $entry) {
	        if ($this->objectsEqual($needle,$entry)) {
	            return true;
	        }
	    }
	    return false;
	}
		
	/**
	 * Überschreibt die geerbte Methode und ergänzt das Resultat noch um die Einträge ADD,DELETE, NEW und REMOVED
	 * @return array[]
	 */
	public function getDiffArray(int $type = PD_VALUE) 
	{
	    $this->checkArray();
	    $result = ['FROM'=>[],'TO'=>[],'ADD'=>[],'DELETE'=>[],'NEW'=>[],'REMOVED'=>[]];
	    if (isset($this->shadow)) {
	        foreach ($this->shadow as $index=>$oldentry) {
	            $result['FROM'][$index] = $this->getDiffEntry($oldentry, $type);
	            if ($this->arraySearch($oldentry,$this->value)===false) {
	                $result['DELETE'][$index] = $this->getDiffEntry($oldentry,$type);
	                $result['REMOVED'][] = $this->getDiffEntry($oldentry,$type);
	            }
	        }
	    }
	    if (isset($this->value)) {
    	    foreach ($this->value as $index=>$newentry) {
    	        $result['TO'][$index] = $this->getDiffEntry($newentry,$type);
    	        if ($this->arraySearch($newentry,$this->shadow)===false) {
    	            $result['ADD'][$index] = $this->getDiffEntry($newentry,$type);
    	            $result['NEW'][] = $this->getDiffEntry($newentry,$type);
    	        }
    	    }
	    }
    	return $result;
	}
	
	protected function isAllowedRelation(string $relation, $value) 
	{
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
	protected function NormalizeValue($value) 
	{
	    return $value;
	}
	
	/**
	 * Tests if the element $value is in this array
	 * @param unknown $value
	 * @return boolean true if its in otherwise false
	 */
	public function IsElementIn($value) 
	{
	   $value = $this->NormalizeValue($value);
	   foreach ($this->value as $test) {
	       if ($this->NormalizeValue($test) == $value) {
	           return true;
	       }
	   }
	   return false;
	}
	
	/**
	 * Tests if this array property is empty (has no entries)
	 * @return true if empty otherwise false
	 */
	public function empty() 
	{
	    return (empty($this->value));
	}
	
	/**
	 * Clears the array. Removes all entries
	 */
	public function clear() 
	{
	    $this->value = [];
	}
}
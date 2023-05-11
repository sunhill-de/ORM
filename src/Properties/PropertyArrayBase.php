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

use Sunhill\ORM\Storage\StorageBase;

class PropertyArrayBase extends AtomarProperty implements \ArrayAccess,\Countable,\Iterator 
{
    
	protected $initialized = true;
	
	protected $pointer = 0;

	protected $element_type;
	
	public function __construct()
	{
	    parent::__construct();
	    $this->value = [];
	}
	
	public function setElementType(string $type): PropertyArrayBase
	{
	    $this->element_type = $type;
	    return $this;
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
	
	/**
	 * Returns the number of entries in this array
	 * {@inheritDoc}
	 * @see Countable::count()
	 */
	public function count()
	{
	    if (is_null($this->value)) {
	        return 0;
	    } else {
	        return count($this->value);
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
	
	protected function handleArrayKey($key)
	{
	   return $key;    
	}
	
	protected function handleArrayValue($value)
	{
	   $element_class = $this->element_type;
	   $element_property = new $element_class();
	   $element_property->setValue($value);
       return $element_property->getValue();    
	}
	
	protected function setElement($key, $value)
	{
	   $key = $this->handleArrayKey($key);
	   $value = $this->handleArrayValue($value);
	   
	   $this->value[$key] = $value;
	}
	
	protected function getElement($key)
	{
	   $key = $this->handleArrayKey($key);
	   
	   return $this->value[$key];
	}
	
	public function loadFromStorage(StorageBase $storage)
	{
        $this->clear();
        $name = $this->getName();
        
        foreach ($storage->$name as $key => $value) {
            $this->setElement($key, $value);
        }
	}
	
}
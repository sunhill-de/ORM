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
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Properties\Exceptions\InvalidParameterException;

class PropertyArrayBase extends AtomarProperty implements \ArrayAccess,\Countable,\Iterator 
{
    
	protected $initialized = true;
	
	protected $pointer = 0;

	protected $element_type;
	
	protected $allowed_classes;
	
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
	
	public function getElementType(): string
	{
	   return $this->element_type;    
	}
	
	protected function checkForObject(string $class)
	{
/*	    if (!Classes::isA(Classes::getNamespaceOfClass($class), 'object')) {
	        throw new InvalidParameterException("Parameter for setAllowedClasses is not an ORMObject.");	        
	    } */
	}
	
	protected function checkForCollection(string $class)
	{
/*	Sorry, removed because of too many side effects @todo fixme    
 * if (!is_a($class, Collection::class, true)) {
	        throw new InvalidParameterException("Parameter for setAllowedClass is not a Collection.");
	    } */ 
	}
	
	public function setAllowedClasses($allowed_classes)
	{
	    if ((PropertyObject::class !== $this->element_type) && !empty($this->element_type)) {
	        throw new InvalidParameterException("setAllowedClasses only makes sense with object maps/arrays");
	    }
	    if (is_string($allowed_classes)) {
	        $this->checkForObject($allowed_classes);
	        $this->allowed_classes = [$allowed_classes];
	    } else if (is_array($allowed_classes)) {
	        foreach ($allowed_classes as $class) {
	            $this->checkForObject($class);
	        }
	        $this->allowed_classes = $allowed_classes;
	    } else {
	        throw new InvalidParameterException("setAllowedClasses was passed an invalid value.");
	    }
	    return $this;
	}
	
	public function setAllowedClass($allowed_class)
	{
	    if ((PropertyCollection::class !== $this->element_type)) {
	        throw new InvalidParameterException("setAllowedClass only makes sense with collection maps/arrays");
	    }
	    if (is_string($allowed_class)) {
	        $this->checkForCollection($allowed_class);
	        $this->allowed_classes = $allowed_class;	        
	    } else {
	        throw new InvalidParameterException("setAllowedClass was passed an invalid value.");	        
	    }
	    return $this;	    
	}
	
	/**
	 * Returns the current element of the foreach loop
	 * 
	 * @return mixed
	 * 
	 * Test: tests/Unit/Properties/PropertyArrayTest::testForeach()
	 */
	public function current () 
	{
	    return $this->value[$this->pointer];
	}
	
	/**
	 * Returns the current key of the foreach loop
	 * 
	 * @return unknown
	 * 
	 * Test: tests/Unit/Properties/PropertyArrayTest::testForeach()
	 */
	public function key () 
	{
	    return $this->pointer;
	}
	
	/**
	 * Sets the pointer to the next element
	 * 
	 * Test: tests/Unit/Properties/PropertyArrayTest::testForeach()
	 */
	public function next () 
	{
	    $this->pointer++;
	}
	
	/**
	 * Rewinds the pointer
	 * 
	 * Test: tests/Unit/Properties/PropertyArrayTest::testForeach()
	 */
	public function rewind () 
	{
	    $this->pointer = 0;
	}
	
	/**
	 * Checks if the pointer points to a valid element
	 * @return boolean
	 * 
	 * Test: tests/Unit/Properties/PropertyArrayTest::testForeach()
	 */
	public function valid () 
	{
	    return (($this->pointer >= 0) && ($this->pointer < count($this->value)));
	}
	
	public function offsetExists($offset) 
	{
	    return isset($this->value[$offset]);
	}
	
	/**
	 * Sets the element with the given offset
	 * 
	 * @param unknown $offset
	 * @throws PropertyException
	 * 
	 * Test: tests/Unit/Properties/PropertyArrayTest::testArrayCount()
	 * Test: tests/Unit/Properties/PropertyArrayTest::testWrongIndex()
	 */
	public function offsetGet($offset) 
	{
	    if (!isset($this->value[$offset])) {
	        throw new PropertyException("Index $offset out of range");
	    }
	        
	    return $this->value[$offset];
	}

	protected function &doGetValue()
	{
	    return $this;
	}
	
	public function offsetSet($offset, $value) 
	{
	    if (!$this->dirty) {
	        $this->shadow = $this->value;
	        $this->dirty = true;
	        $this->initialized = true;
	    }
	    $value = $this->handleArrayValue($value);
	    if (isset($offset)) {
	        $offset = $this->handleArrayKey($offset);
	        $this->value[$offset] = $value;
	    } else {
	        $this->value[] = $value;
	    }
	}
	
	public function offsetUnset($offset) 
	{
	    if (!$this->dirty) {
	        $this->shadow = $this->value;
	        $this->dirty = true;
	    }
	    unset($this->value[$offset]);
	    $this->value = array_values($this->value); // Reindex
	}
	
	/**
	 * Returns the number of entries in this array
	 * {@inheritDoc}
	 * @see Countable::count()
     *
	 * Test test/Unit/Properties/PropertyArrayTest::testArrayCount
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
	 * 
	 * @param unknown $value
	 * @return boolean true if its in otherwise false
	 * 
	 * Test test/Unit/Properties/PropertyArrayTest::testIsElementIn()
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
	 * 
	 * @return true if empty otherwise false
	 * 
	 * Test test/Unit/Properties/\PropertyArrayTest::testEmpty()
	 */
	public function empty() 
	{
	    return (empty($this->value));
	}
	
	/**
	 * Clears the array. Removes all entries
	 * 
	 * Test test/Unit/Properties/\PropertyArrayTest::testEmpty()
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
	   foreach ($this->getAttributes() as $key => $attr_value) {
	       $method = 'set'.ucfirst($key);
	       if (($key !== 'value') && ($key !== 'shadow')) {
	           $element_property->$method($attr_value);
	       }
	   }
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
        if (!$storage->hasEntity($name) || is_null($storage->$name)) {
            return;
        }
            
        foreach ($storage->$name as $key => $value) {
            $this->setElement($key, $value);
        }
	}

	/**
	 * Returns
	 * @param unknown $input
	 * @return bool
	 */
	public function isValid($input): bool
	{
	    $class = $this->element_type;
	    $test = new $class();
	    if (!empty($this->allowed_classes)) {
            $test->setAllowedClasses($this->allowed_classes);
	    }
	    return $test->isValid($input);
	}
	
}
<?php

namespace Sunhill\ORM\Traits;

use Sunhill\ORM\Utils\descriptor;

/**
 * Implements the trait "TraversableArray" which provides a combination of the Interfaces countable, array_access and Iterator
 * @author lokal
 *
 */
trait TraversableArray {
    
    protected $fields = [];
    
    protected $pointer = 0;
    
    /**
     * Returns the current element of the foreach loop
     * @return mixed
     */
    public function current (  ) {
        return $this->get_fields()[array_keys($this->get_fields())[$this->pointer]];
    }
    
    /**
     * Returns the current key of the foreach loop
     * @return unknown
     */
    public function key (  ) {
        return array_keys($this->get_fields())[$this->pointer];
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
        return (($this->pointer >= 0) && ($this->pointer < count($this->get_fields())));
    }
    
    public function offsetExists($offset) {
        return isset($this->get_fields()[$offset]);
    }
    
    public function offsetGet($offset) {
        return $this->get_fields()[$offset];
    }
    
    public function offsetSet($offset, $value) {
        // Do we change a previously set elemnt?
        if (isset($offset) && $this->offsetExists($offset)) {
            // Yes, is the a change at all?
            if ($value !== $this->offsetGet($offset)) {
                // Yes, do we need to fire a trigger?
                if (method_exists($this,'element_changing')) {
                    // Do we have a changing trigger?
                    $oldvalue = $this->offsetGet($offset);
                    $this->element_changing($oldvalue,$value,$offset);
                }
                $this->element_change($offset,$value);
                if (method_exists($this,'element_changed')) {
                    // Do we need to fire a changed trigger?
                    $this->element_changed($oldvalue,$value,$offset);
                }
            }
        } else {
            // No, it's a previously unset element
            if (method_exists($this,'element_changing')) {
                $this->element_changing(null,$value,$offset);
            }            
            $this->element_append($value);
            if (method_exists($this,'element_changed')) {
                // Do we need to fire a changed trigger?
                $this->element_changed(null,$value,$offset);
            }
        }
    }
    
    /**
     * Deletes the given element from the array
     * @param unknown $offset
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            $oldvalue = $this->offsetGet($offset);
            if (method_exists($this,'element_changing')) {
                $this->element_changing($oldvalue,null,$offset);
            }
            $this->element_unset($offset);
            if (method_exists($this,'element_changed')) {
                $this->element_changing($oldvalue,null,$offset);
            }
        }
    }
    
    /**
     * Returns the count of elements
     * @return unknown
     */
    public function count() {
        return count($this->get_fields());
    }
    
    /**
     * The follwing methods could be overwritten in a class that implements this trait
     */  
    
    /**
     * Return the traversable array field. By default this is the protected property $fields but it
     * could be overwritten by anything else
     * @return array
     */
     protected function get_fields() {
         return $this->fields;
     }
     
     /**
     * Performs the change of the value. By default it changes the given values property
     * This method should be overwritten by classes that uses this trait and use a different array
     * @param unknown $offset
     * @param unknown $value
     */
    protected function element_change($offset,$value) {
        $this->fields[$offset] = $value;
    }
    
    /**
     * Performs the adding of a new element to the array. By default it appends the given values property
     * This method should be overwritten by classes that uses this trait and use a different array
     * @param unknown $value
     */
    protected function element_append($value) {
        $this->fields[] = $value;
    }
    
    /**
     * Performs the unsetting of an element. By defaults it uses the given values property
     * This method should be overwritten by classes that uses this trait and use a different array
     * @param unknown $offset
     */
    protected function element_unset($offset) {
        unset($this->fields[$offset]);
    }
    
}

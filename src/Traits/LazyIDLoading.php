<?php
/**
 * @file LazyIDLoading.php
 * A trait for handling lazy id loading
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-08-20
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\ORM\Traits;

use Sunhill\ORM\Facades\Objects;

/** 
 * This trait is used for property_object and property_array_of_object. It provided the methods
 * for lazy id treatment. This is used for object references that are only loaded if they are read. 
 */
trait LazyIDLoading 
{
    
    /**
     * Calls ->commit() for a child
     * @param unknown $child
     */
    protected function commitChild($child) 
    {
        $child->commit($this);
    }
    
    /**
     * Calles for the given child ->commit() when it is loaded or not in the cache
     * @param unknown $child
     */
    protected function commitChildIfLoaded($child) 
    {
        if (!empty($child)) {
            if (is_numeric($child)) {
                if (Objects::isCached($child)) {
                    // When it is in cache it could be manipulated via side effects
                    $child = Objects::load($child);
                } else {
                    return; // Not loaded nor in cache
                }
            } 
            $this->commitChild($child);
        }
    }
 
    /**
     * Tries to get the ID for the given object $test
     * @param unknown $test
     * @return NULL|unknown
     */
    protected function getLocalID($test) 
    {
        if (is_null($test)) {
            return null;
        } else if (is_int($test)) {
            return $test;
        } else {
            return $test->getID();
        }
    }   
    
}

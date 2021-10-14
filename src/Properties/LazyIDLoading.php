<?php
/**
 * @file LazyIDLoading.php
 * Provides the trait LazyIDLoading
 * Lang de,en
 * Reviewstatus: 2021-10-14
 * Localization: incomplete
 * Documentation: incomplete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: in progress
 */

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Facades\Objects;

/**
 * Provides some routines for handling of object fields (PropertyObject and PropertyArrayOfObjects)
 * @author lokal
 *
 */
trait LazyIDLoading 
{
    
    /**
     * Calls commit for the child
     * @param unknown $child
     */
    protected function commitChild($child) 
    {
        $child->commit($this);
    }
    
    /**
     * Calls commit for the child if it is loaded or already in the cache
     * @param unknown $child
     */
    protected function commitChildIfLoaded($child) 
    {
        if (!empty($child)) {
            if (is_numeric($child)) {
                if (Objects::isCached($child)) {
                    // If it's in the cache it could be manipulated 
                    $child = Objects::load($child);
                } else {
                    return; // Weder geladen noch im Cache
                }
            } 
            $this->commitChild($child);
        }
    }
 
    /**
     * Tries to get the id for the give test object
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

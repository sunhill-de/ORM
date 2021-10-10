<?php

namespace Sunhill\ORM\Properties;

use Sunhill\ORM\Facades\Objects;

/**
 * Kapselt für property_object und property_array_of_objects die Methoden, die für die Lazy-ID Behandlung
 * benötigt werden, um doppelte Programmierarbeit zu sparen
 * @author lokal
 *
 */
trait LazyIDLoading {
    
    /**
     * Ruft für das Kind commit() auf
     * @param unknown $child
     */
    protected function commit_child($child) {
        $child->commit($this);
    }
    
    /**
     * Ruft für das übergebene Kind commit auf, wenn es geladen wurde oder nicht im cache befindet
     * @param unknown $child
     */
    protected function commit_child_if_loaded($child) {
        if (!empty($child)) {
            if (is_numeric($child)) {
                if (Objects::isCached($child)) {
                    // Wenn es im Cache ist, kann es per seiteneffekt manipuliert worden sein
                    $child = Objects::load($child);
                } else {
                    return; // Weder geladen noch im Cache
                }
            } 
            $this->commit_child($child);
        }
    }
 
    /**
     * Versucht für das übergebene Object $test die ID zu ermitteln
     * @param unknown $test
     * @return NULL|unknown
     */
    protected function get_local_id($test) {
        if (is_null($test)) {
            return null;
        } else if (is_int($test)) {
            return $test;
        } else {
            return $test->get_id();
        }
    }   
    
}

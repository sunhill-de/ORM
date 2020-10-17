<?php

namespace Sunhill\ORM\Traits;

use Sunhill\ORM\SunhillException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\oo_object;

/**
 * A trait that tests if a given object is a allowed class that is defined by a list of allowed classes
 * @author klaus
 *
 */
trait TestObject {

    /**
     * Returns true, is $test is a valid object defined by $allowed_objects
     * @param oo_object $test
     * @param array of oo_object|oo_object $allowed_objects
     * @throws SunhillException
     * @return boolean|unknown
     */
    protected function is_valid_object(oo_object $test,$allowed_objects) {
        if (is_array($allowed_objects)) {
            foreach ($allowed_objects as $object) {
                if (Classes::is_a($test,$object)) {
                    return true;
                }
            }
            return false;
        } else if (is_string($allowed_objects)) {
            return Classes::is_a($test,$allowed_objects);
        } else {
            throw new SunhillException("is_valid_object: Inavlid type passed to allowed_objects.");
        }
    }
}

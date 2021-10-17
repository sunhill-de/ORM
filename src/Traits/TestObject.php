<?php
/**
 * @file TestObject.php
 * A trait for testing if a given object is a allowed class
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-08-20
 * Localization: none
 * Documentation: complete
 * Tests: tests/Unit/TraitTestObjectTest.php
 * Coverage: unknown
 */

namespace Sunhill\ORM\Traits;

use Sunhill\ORM\ORMException;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Objects\ORMObject;

/**
 * A trait that tests if a given object is a allowed class that is defined by a list of allowed classes
 * @author klaus
 *
 */
trait TestObject {

    /**
     * Returns true, is $test is a valid object defined by $allowed_objects
     * @param ORMObject $test
     * @param array of ORMObject|ORMObject $allowed_objects
     * @throws ORMException
     * @return boolean|unknown
     */
    protected function isValidObject(ORMObject $test,$allowed_objects) {
        if (is_array($allowed_objects)) {
            foreach ($allowed_objects as $object) {
                if (Classes::isA($test,$object)) {
                    return true;
                }
            }
            return false;
        } else if (is_string($allowed_objects)) {
            return Classes::isA($test,$allowed_objects);
        } else {
            throw new ORMException("is_valid_object: Inavlid type passed to allowed_objects.");
        }
    }
}

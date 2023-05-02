<?php
/**
 * @file PropertyUtils.php
 * A trait for utils dealing with properties
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
trait PropertyUtils 
{

    protected function getAllProperties($caller, bool $only_own_table = false): array
    {
        $properties = $caller::staticGetProperties()->get();
        
        if (!$only_own_table) {
            return $properties;
        }
        $result = [];
        foreach ($properties as $key => $info) {
            if ($info->getClass() == $caller::getInfo('name')) {
                $result[$key] = $info;
            }
        }
        return $result;
    }
    
}

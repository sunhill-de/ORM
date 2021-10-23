<?php
/**
 * @file ClassOperatorBase.php
 * Provides the ClassOperatorBase class as a base class for Operators that work on classes
 * Lang en
 * Reviewstatus: 2021-03-26
 * Localization: none
 * Documentation: complete
 * Tests: tests/Unit/ClassOperatorTest
 * Coverage: unknown
 */

namespace Sunhill\ORM\Operators;

use \Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\Operators\OperatorBase;

/**
 * Base class for operators that work on classes. As an addition to the OperatorBase it
 * checks for a given object in the Descriptor 
 * @author klaus
 *
 */
abstract class ClassOperatorBase extends OperatorBase 
{
    protected $target_class;
    
    protected function cond_class(Descriptor $Descriptor) 
    {
        if (is_null($this->target_class)) {
            return true;
        } else {
            return (is_a($Descriptor->object,$this->target_class));
        }
    }
    
}

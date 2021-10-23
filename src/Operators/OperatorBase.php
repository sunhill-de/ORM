<?php
/**
 * @file OperatorBase.php
 * Provides the OperatorBase class as a base class for Operators
 * Lang en
 * Reviewstatus: 2021-03-14
 * Localization: none
 * Documentation: complete
 * Tests: tests/Unit/OperatorTest
 * Coverage: unknown
 */

namespace Sunhill\ORM\Operators;

use Sunhill\Basic\Utils\Descriptor;
use Sunhill\ORM\ORMObject;

/**
 * Base class for operators. An operator is a class that performs a certain action on
 * an object if the conditions matches. A derrived class can use methods with the prefix
 * 'cond_' to implement on condition checks. These methods (and the parent method check) 
 * are passed a Descriptor object with some input and output variables. This Descriptor
 * must implement as least the command field, that defines the action that should be
 * performed on this object.
 * @author klaus
 *
 */
abstract class OperatorBase 
{
    /**
     * A derrived operator must implement at least one command here (otherwise the cond_command 
     * check always returns false
     * @var array
     */
    protected $commands = [];
    
    protected $prio = 50;
    
    public function getPrio(): int 
    {
        return $this->prio;
    }
    
    /**
     * The public check method which in turn calls all methods with the prefix cond_ to
     * check if the condition matches
     * @param Descriptor $Descriptor
     * @return boolean
     */
    public function check(Descriptor $descriptor) 
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (substr($method,0,5) === 'cond_') {
                if (!$this->$method($descriptor)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Checks if the command matches
     * @param Descriptor $Descriptor
     * @return boolean
     */
    protected function cond_command(Descriptor $descriptor) 
    {
        return (in_array($descriptor->command, $this->commands));
    }
    
    /**
     * Performs the action on the object (which is a field of $Descriptor)
     * @param Descriptor $Descriptor
     * @return unknown
     */
    public function execute(Descriptor $descriptor) 
    {
        return $this->doExecute($descriptor);
    }
    
    /**
     * The abstract execution method
     * @param Descriptor $Descriptor
     */
    abstract protected function doExecute(Descriptor $descriptor);
}

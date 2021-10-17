<?php
/**
 * @file OperatorManager.php
 * Provides the OperatorManager class for managing the handling of operators on objects
 * @author Klaus Dimde
 * --------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-10-10
 * Localization: none
 * Documentation: compleze
 * Tests: Unit/Operators/OperatorManagerTest.php
 * Coverage: unknown
 * PSR-State: complete
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\ORMException;
use Sunhill\Basic\Utils\Descriptor;

/**
 The operator manager provides access to the operator subsystem. An operator is a piece of code that works on a Sunhill\Basic\Utils\Descriptor object if 
 certain conditions meet.
 */
class OperatorManager 
{
 
    protected $operators = null; /**< Saves the loaded operators */
    
    protected $operator_classes = []; /**< Saves the class names of the operators */
    
    /**
     * Adds a new operator class to the manager
     * @param string $class
     */
    public function addOperator(string $class): OperatorManager 
    {
        $this->operator_classes[] = $class;
        return $this;
    }
    
    /**
     * Returns the number of registered operators
     * @return number
     */
    public function getOperatorCount(): int 
    {
        return count($this->operator_classes);
    }
    
    /**
     * Clears the caches
     */
    public function flush() 
    {
        $this->operators = null;
        $this->operator_classes = [];
    }
    
    /**
    * Executes all operators that meet the conditions. At least a command has to be passed. If no Descriptor is passed
    * one is created. If no object is passed an empty Descriptor is used. 
    * @param $command string The current command that is executed
    * @param $object ORMObject|null The objects that should be used for the operators (or null, if none)
    * @param $Descriptor Descriptor|null The Descriptor that should be used for the operators. If null, an empty Descriptor is created
    */
    public function ExecuteOperators(string $command = '', $object = null, &$Descriptor = null) 
    {
        if (is_null($this->operators)) {
            $this->loadOperators();
        }
        
        if (is_null($Descriptor)) {
            $Descriptor = new Descriptor();
        }
        if (!is_null($object))  {
            $Descriptor->object = $object;
        }
        if (!empty($command)) {
            $Descriptor->command = $command;
        }        
        
        foreach ($this->operators as $operator) {
            if ($operator->check($Descriptor)) {
                $operator->execute($Descriptor);
            }
        }
    }
    
    private function loadOperators() 
    {
        $this->operators = [];
        foreach ($this->operator_classes as $class) {
            $this->operators[] = new $class();
        }
        usort($this->operators,function($x,$y) {
            if ($x->getPrio() == $y->getPrio()) {
                return 0;
            }
            return ($x->getPrio() < $y->getPrio())? -1:1;
        });
    }
}

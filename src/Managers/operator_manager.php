<?php
/**
 * @file operator_manager.php
 * Provides the operator_manager class for managing the handling of operators on objects
 * @author Klaus Dimde
 * --------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2021-03-14
 * Localization: unknown
 * Documentation: unknown
 * Tests: Unit/Operators/OperatorManagerTest.php
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\ORMException;
use Sunhill\Basic\Utils\descriptor;

/**
 The operator manager provides access to the operator subsystem. An operator is a piece of code that works on a Sunhill\Basic\Utils\descriptor object if 
 certain conditions meet.
 */
class operator_manager {
 
    protected $operators = null; /**< Saves the loaded operators */
    
    protected $operator_classes = []; /**< Saves the class names of the operators */
    
    /**
     * Adds a new operator class to the manager
     * @param string $class
     */
    public function add_operator(string $class) {
        $this->operator_classes[] = $class;
        return $this;
    }
    
    /**
     * Returns the number of registered operators
     * @return number
     */
    public function get_operator_count() {
        return count($this->operator_classes);
    }
    
    /**
     * Clears the caches
     */
    public function flush() {
        $this->operators = null;
        $this->operator_classes = [];
    }
    
    /**
    * Executes all operators that meet the conditions. At least a command has to be passed. If no descriptor is passed
    * one is created. If no object is passed an empty descriptor is used. 
    * @param $command string The current command that is executed
    * @param $object oo_object|null The objects that should be used for the operators (or null, if none)
    * @param $descriptor descriptor|null The descriptor that should be used for the operators. If null, an empty descriptor is created
    */
    public function ExecuteOperators(string $command='',$object=null,&$descriptor=null) {
        if (is_null($this->operators)) {
            $this->loadOperators();
        }
        
        if (is_null($descriptor)) {
            $descriptor = new descriptor();
        }
        if (!is_null($object))  {
            $descriptor->object = $object;
        }
        if (!empty($command)) {
            $descriptor->command = $command;
        }        
        
        foreach ($this->operators as $operator) {
            if ($operator->check($descriptor)) {
                $operator->execute($descriptor);
            }
        }
    }
    
    private function loadOperators() {
        $this->operators = [];
        foreach ($this->operator_classes as $class) {
            $this->operators[] = new $class();
        }
        usort($this->operators,function($x,$y) {
            if ($x->get_prio() == $y->get_prio()) {
                return 0;
            }
            return ($x->get_prio() < $y->get_prio())? -1:1;
        });
    }
}

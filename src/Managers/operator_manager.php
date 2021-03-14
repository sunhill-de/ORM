<?php
/**
 * @file operator_manager.php
 * Provides the operator_manager class for managing the handling of operators on objects
 * Lang en
 * Reviewstatus: 2021-03-14
 * Localization: unknown
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 */
namespace Sunhill\ORM\Managers;

use Sunhill\ORM\ORMException;
use Sunhill\Basic\Utils\descriptor;

class operator_manager {
 
    protected $operators = null;
    
    protected $operator_classes = [];
    
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
    
    public function flush() {
        $this->operators = null;
        $this->operator_classes = [];
    }
}
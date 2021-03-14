<?php
namespace Sunhill\ORM\Operators;

use \Sunhill\Basic\Utils\descriptor;

abstract class OperatorBase 
{
    protected $commands = [];
    
    public function check(descriptor $descriptor) {
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
    
    protected function cond_command(descriptor $descriptor) {
        return (in_array($descriptor->command,$this->commands));
    }
    
    public function execute(descriptor $descriptor) {
        return $this->do_execute($descriptor);
    }
    
    abstract protected function do_execute(descriptor $descriptor);
}

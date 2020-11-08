<?php

/**
 * @file descriptor.php
 * Provides a class that bundles information for easier access
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/UtilDescriptorTest.php
 * Coverage: unknown
 */
namespace Sunhill\ORM\Utils;

use Sunhill\ORM\ORMException;

class DescriptorException extends ORMException {}

/**
 * A class that bundles informations in a class like style
 *
 * @author Klaus
 *        
 */
class descriptor implements \Iterator
{

    private $fields = [];

    private $error = false;

    private $pointer = 0;

    protected $autoadd = true;
    
    protected $disable_triggers = false;
    
    public function __construct() {
        $save_autoadd = $this->autoadd;
        $save_triggers = $this->disable_triggers;
        $this->autoadd = true;
        $this->disable_triggers = true;
        $this->setup_fields();
        $this->autoadd = $save_autoadd;
        $this->disable_triggers = $save_triggers;
    }
    
    protected function setup_fields() {
        
    }
    
    /**
     * Catch all for setting a value
     *
     * @param unknown $name
     * @param unknown $value
     */
    public function __set($name, $value)
    {
        $this->check_autoadd($name,$value);
        if (!isset($this->fields[$name])) {
            $oldvalue = null;   
        } else {
            $oldvalue = $this->fields[$name];
        }
        if ($oldvalue !== $value) {
            if (!$this->check_changing_trigger($name,$oldvalue,$value)) {
                throw new DescriptorException("Valuechange forbidden by trigger.");
            }
            $this->fields[$name] = $value;
            $this->check_changed_trigger($name,$oldvalue,$value);            
        }
    }

    private function check_autoadd($name,$value) {
        if (!isset($this->fields[$name]) && !$this->autoadd) {
            throw new DescriptorException("Autoadd forbidden.");
        }        
    }
    
    private function check_changing_trigger($name,$from,$to) {
        if ($this->disable_triggers) {
            return true;
        }
        $method_name = $name.'_changing';
        if (method_exists($this,$method_name)) {
            $diff = new descriptor();
            $diff->from = $from;
            $diff->to = $to;
            return $this->$method_name($diff);
        }
        return true;
    }
    
    private function check_changed_trigger($name,$from,$to) {
        if ($this->disable_triggers) {
            return true;
        }
        $method_name = $name.'_changed';
        if (method_exists($this,$method_name)) {
            $diff = new descriptor();
            $diff->from = $from;
            $diff->to = $to;
            $this->$method_name($diff);
        }        
    }
    
    /**
     * Catch all for getting a value
     *
     * @param unknown $name
     * @return mixed|NULL
     */
    public function &__get($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            $this->fields[$name] = new descriptor();
            return $this->fields[$name];
        }
    }

    /**
     * Checks, if the descriptor has the field with the name $name
     * @param string $name
     */
    public function is_defined(string $name) {
        return isset($this->fields[$name]);
    }
    
    /**
     * Catch all for method so we can implement set_xxx, get_xxx
     */
    public function &__call(string $name, array $params)
    {
        if (substr($name, 0, 4) == 'get_') {
            $name = substr($name, 4);
            return $this->$name;
        } else if (substr($name, 0, 4) == 'set_') {
            $name = substr($name, 4);
            $this->$name = $params[0];
            return $this;
        }
        throw new DescriptorException("Unknown method '$name'");
    }

    /**
     * Returns true, if the descriptor is empty
     *
     * @return bool
     */
    public function empty()
    {
        return empty($this->fields);
    }

    /**
     * Returns false, if there was no error otherwise its error message
     *
     * @return boolean|\Manager\Utils\string
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Sets an error message and therefore an error condition
     *
     * @param string $message
     */
    public function set_error(string $message)
    {
        $this->error = $message;
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->fields[array_keys($this->fields)[$this->pointer]];
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::key()
     */
    public function key()
    {
        return array_keys($this->fields)[$this->pointer];
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::next()
     */
    public function next()
    {
        $this->pointer ++;
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::valid()
     */
    public function valid()
    {
        return (($this->pointer >= 0) && ($this->pointer < count($this->fields)));
    }
}
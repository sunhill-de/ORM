<?php

/**
 * @file TypeNumeric.php
 * Defines a non instantiable type for numeric types (int and float)
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

use Sunhill\ORM\Properties\Exceptions\InvalidParameterException;
use Sunhill\ORM\Properties\AbstractSimpleProperty;

abstract class TypeNumeric extends AbstractSimpleProperty
{
   
    /**
     * The minimal value this property must have
     * When null there is no lower limit
     * 
     * @var integer
     */
    protected $minimum = null;
    
    /**
     * The minimal value this property must have
     * Wenn null there is no upper limit
     * 
     * @var integer
     */
    protected $maximum = null;
    
    /**
     * Indicates what should happen, when the given value is outside the mimimum-maximum-intervall
     * Could be:
     * - set = set the value to the nex bound
     *   invalid = declare this value as invalid
     *   
     * @var string
     */
    protected $out_of_bound_policy = 'invalid';
    
    /**
     * Setter for maximum
     * 
     * @param int $maximum
     * @return \Sunhill\ORM\Properties\Types\TypeVarchar
     */
    public function setMaximum(int $maximum)
    {
        $this->maximum = $maximum;
        
        return $this;
    }
    
    /**
     * Getter for maximum
     * 
     * @return int
     */
    public function getMaximum(): int
    {
        return $this->maximum;    
    }
    
    /**
     * Setter for Minimum
     *
     * @param int $Minimum
     * @return \Sunhill\ORM\Properties\Types\TypeVarchar
     */
    public function setMinimum(int $minimum)
    {
        $this->minimum = $minimum;
        
        return $this;
    }
    
    /**
     * Getter for Minimum
     *
     * @return int
     */
    public function getMinimum(): int
    {
        return $this->minimum;
    }
    
    /**
     * Setter for out_of_bound_policy
     * 
     * @param string $policy
     * @return \Sunhill\ORM\Properties\Types\TypeVarchar
     * @throws InvalidParameterException when $policy is not cur or invalid
     */
    public function setOutOfBoundsPolicy(string $policy)
    {
        if (($policy <> 'set') && ($policy <> 'invalid')) {
           throw new InvalidParameterException("OutOfBoundsPolicy may only be 'set' or 'invalid'"); 
        }
        
        $this->out_of_bound_policy = $policy;
        
        return $this;
    }
    
    /**
     * Getter for out_of_bounds_policy
     * 
     * @return string
     */
    public function getOutOfBoundsPolicy(): string
    {
        return $this->out_of_bound_policy;        
    }
    
    abstract protected function isNumericType($input): bool;
    
    /**
     * First check if the given value is an ingteger at all all. afterwards check the boundaries
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        if (!$this->isNumericType($input)) {
            return false;
        }
        if (!is_null($this->minimum) && ($input < $this->minimum)) {
            if ($this->out_of_bound_policy == 'invalid') {
                return false;
            }
        }
        if (!is_null($this->maximum) && ($input > $this->maximum)) {
            if ($this->out_of_bound_policy == 'invalid') {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Cuts the input string to a maximum length
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::doConvertToInput()
     */
    protected function doConvertToInput($input)
    {
        if (!is_null($this->minimum) && ($input < $this->minimum)) {
            return $this->mimnimum;
        }
        if (!is_null($this->maximum) && ($input > $this->maximum)) {
            return $this->maximum;
        }
        
        return $input;
    }
}
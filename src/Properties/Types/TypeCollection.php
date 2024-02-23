<?php

/**
 * @file TypeCollection.php
 * Defines a type for collections
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\ORM\Properties\Types;

use Sunhill\ORM\Properties\Exceptions\InvalidParameterException;
use Sunhill\ORM\Facades\Collections;
use Sunhill\ORM\Properties\AbstractSimpleProperty;

class TypeCollection extends AbstractSimpleProperty
{

    /**
     * Stores what kind of collection is allowed for this field
     * @var string
     */
    protected $allowed_collection = '';

    /**
     * Setter for allowed_collection
     * 
     * @param string $allowed_collection
     * @return TypeCollection
     */
    public function setAllowedCollection(string $allowed_collection): TypeCollection
    {
        $this->allowed_collection = $allowed_collection;
        return $this;
    }
    
    /**
     * Getter for allowed_collection 
     * 
     * @return string
     */
    public function getAllowedCollection(): string
    {
        return $this->allowed_collection;
    }
    
    /**
     * Alias for setAllowedCollection
     * 
     * @deprecated Use setAllowedCollection instead
     * @param unknown $allowed_collection
     * @return TypeCollection
     */
    public function setAllowedClasses($allowed_collection): TypeCollection
    {
        return $this->setAllowedCollection($allowed_collection);
    }
    
    protected function checkAllowedCollectionSet()
    {
        if (empty($this->allowed_collection)) {
            throw new InvalidParameterException("Allowed collection not set.");
        }
    }
    
    protected function checkForCollectionConversion($input)
    {
        if (is_numeric($input)) {
            return Collections::loadCollection($this->allowed_collection,$input);
        }
        return $input;
    }
    
    
    /**
     * Tests if input can be solved to an boolean (always true, because everything can be solved to
     * a boolean
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        $this->checkAllowedCollectionSet();
        return is_numeric($input) || is_a($input, Collections::searchCollection($this->allowed_collection));
    }
    
    /**
     * Translates the given input to 1 or 0
     * 
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\ValidatorBase::doConvertToInput()
     */
    protected function doConvertToInput($input)
    {
        $this->checkAllowedCollectionSet();
        return $input;
    }
    
    public function getAccessType(): string
    {
        return 'record';
    }
        
}
<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\Properties\AtomarProperty;
use Sunhill\ORM\InfoMarket\Exceptions\InvalidItemException;

abstract class BaseObjectItem extends AtomarProperty
{
    
    static protected $type = 'object';
    
    /**
     * Always assume a array item as initialized
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\AtomarProperty::initializeValue()
     */
    protected function initializeValue(): bool
    {
        return true;
    }
        
    abstract protected function getObjectOffering(): array;
    abstract protected function getElement(string $name);
    
    protected function &doGetValue()
    {
        $offer = $this->getObjectOffering();
        return $offer;
    }
    
    protected function requestTerminalItem(string $name)
    {
        return $this->getElement($name);
    }
    
}
<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\Properties\AtomarProperty;
use Sunhill\ORM\InfoMarket\Exceptions\InvalidItemException;

abstract class BaseArrayItem extends AtomarProperty
{
    
    static protected $type = 'array';
    
    /**
     * Always assume a array item as initialized
     * {@inheritDoc}
     * @see \Sunhill\ORM\Properties\AtomarProperty::initializeValue()
     */
    protected function initializeValue(): bool
    {
        return true;
    }
        
    abstract protected function getArrayOffering(): array;
    abstract protected function getEntryCount(): int;
    abstract protected function getIndexedElement(int $index);
    
    protected function &doGetValue()
    {
        $offer = array_merge(['count'],$this->getArrayOffering());
        return $offer;
    }
    
    protected function getCount()
    {
        $result = $this->createResponseFromValue($this->getEntryCount());
        return $result->OK()->type('int')->unit('None')->semantic('Count')->readable()->writeable(false)->update('asap');
    }
        
    protected function requestTerminalItem(string $name)
    {
        if ($name == 'count') {
            return $this->getCount();
        }
        if (is_numeric($name)) {
            return $this->getIndexedElement(intval($name));
        }
        return $this->handleUnknownItem($name);
    }
    
    protected function handleUnknownItem(string $item)
    {
        throw new InvalidItemException("Can't process item '$item'");    
    }
    
}
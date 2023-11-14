<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\Properties\AtomarProperty;

abstract class ArrayInfoMarketItem extends AtomarProperty
{

    static protected $type = 'array';
    
    static protected $named_array = false;
    
    protected function initializeValue(): bool
    {
        return true;        
    }
    
    protected function &doGetValue()
    {
        $result = ['count'=>'count'];
        
        if (static::$named_array) {
            $result = array_merge($result, $this->getNamedEntries());
        } else {
            $result = array_merge($result, $this->getNumberedEntries());
        }
        return $result;        
    }
    
    protected function getNamedEntries()
    {
        return [];
    }
    
    protected function getNumberedEntries()
    {
        $result = [];
        for ($i=0;$i<$this->getCountValue();$i++) {
            $result[] = $i;
        }
        
        return $result;
    }
    
    /**
     * Should return the element count of values of this array item
     * @return int
     */
    abstract protected function getCountValue(): int;
    
    /**
     * Should return the $index-th element of this array item
     * @param int $index
     */
    abstract protected function getIndexedElement(int $index);
    
    protected function getNamedElement(string $name)
    {
    }
    
    protected function getCount()
    {
        $result = $this->createResponseFromValue($this->getCountValue());
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
        return $this->getNamedElement($name);
    }
    
}

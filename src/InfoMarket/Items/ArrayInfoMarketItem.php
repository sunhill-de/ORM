<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\Properties\AtomarProperty;

abstract class ArrayInfoMarketItem extends AtomarProperty
{

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
    
    protected function getCount()
    {
        $result = $this->createResponseFromValue($this->getCountValue());
        return $result->OK()->type('int')->unit('none')->semantic('count')->readable()->writeable(false)->update('asap');        
    }
    
    protected function requestTerminalItem(string $name)
    {
        if ($name == 'count') {
           return $this->getCount();
        }
        if (is_numeric($name)) {
            return $this->getIndexedElement(intval($name));
        }
    }
    
}

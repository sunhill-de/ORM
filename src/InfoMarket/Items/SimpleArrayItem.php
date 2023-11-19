<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\InfoMarket\Exceptions\CantLoadItemsException;
use Sunhill\ORM\InfoMarket\Exceptions\InvalidIndexException;

class SimpleArrayItem extends BaseArrayItem
{
    
    protected $items;
    
    public function addEntry($key, $entry)
    {
        $this->items[$key] = $entry;
    }
    
    protected function getArrayOffering(): array
    {
        return array_keys($this->items);
    }
    
    protected function getEntryCount(): int
    {
        return count($this->items);
    }
    
    protected function getIndexedElement(int $index)
    {
        if (isset($this->items[$index])) {
            return $this->items[$index];
        }
        
        if (($index < 0) || ($index >= count($this->items))) {
            throw new InvalidIndexException("The index '$index' is invalid.");
        }
        
        foreach ($this->items as $key => $value) {
            if (!$index--) {
                return $value;
            }
        }
    }
    
    protected function handleUnknownItem(string $item)
    {
        if (isset($this->items[$item])) {
            return $this->items[$item];
        }
        
        parent::handleUnknownItem($item);
    }
    
}

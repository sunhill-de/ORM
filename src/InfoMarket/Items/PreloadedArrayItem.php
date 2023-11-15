<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\InfoMarket\Exceptions\CantLoadItemsException;
use Sunhill\ORM\InfoMarket\Exceptions\InvalidIndexException;

abstract class PreloadedArrayItem extends BaseArrayItem
{
    
    protected $items;
    
    abstract protected function loadItems(): array;
    
    protected function checkPreload()
    {
        if (is_null($this->items)) {
            $this->items = $this->loadItems();
            if (is_null($this->items)) {
                throw new CantLoadItemsException("Can't load the items for ".static::class);
            }
        }
    }
    
    protected function getArrayOffering(): array
    {
        $this->checkPreload();
        
        return array_keys($this->items);
    }
    
    protected function getEntryCount(): int
    {
        $this->checkPreload();
        
        return count($this->items);
    }
    
    protected function getIndexedElement(int $index)
    {
        $this->checkPreload();
        
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
        $this->checkPreload();
        
        if (isset($this->items[$item])) {
            return $this->items[$item];
        }
        
        parent::handleUnknownItem($item);
    }
    
}

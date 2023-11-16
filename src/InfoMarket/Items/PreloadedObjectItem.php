<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\InfoMarket\Exceptions\CantLoadItemsException;
use Sunhill\ORM\InfoMarket\Exceptions\InvalidIndexException;
use Sunhill\ORM\InfoMarket\Exceptions\InvalidItemException;

abstract class PreloadedObjectItem extends BaseObjectItem
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
    
    protected function getObjectOffering(): array
    {
        $this->checkPreload();
        
        return $this->items;
    }
    
    protected function getElement(string $name)
    {
        $this->checkPreload();
        
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }
        throw new InvalidItemException("Can't process item '$name'");        
    }
 
}

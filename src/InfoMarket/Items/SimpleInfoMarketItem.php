<?php

namespace Sunhill\ORM\InfoMarket\Items;

use Sunhill\ORM\Properties\AtomarProperty;
use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotWriteableException;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotReadableException;

class SimpleInfoMarketItem extends AtomarProperty
{
    
    protected static $item_semantic = Name::class;
    
    protected static $item_unit = None::class;
    
    protected static $item_readable = true;
    
    protected static $item_writeable = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->initialized = true; // InfoMarketItems are always initialized
        $this->setUnit(static::$item_unit);
        $this->setSemantic(static::$item_semantic);
    }
    
    protected function doSetValue($value)
    {
        if (!static::$item_writeable) {
            throw new ItemNotWriteableException("The item '".$this->getName()."' is not writeable.");
        }
        $this->writeItem($value);
    }
    
    protected function &doGetValue()
    {
        if (!static::$item_readable) {
            throw new ItemNotReadableException("The item '".$this->getName()."' is not readable.");
        }
        $value = $this->readItem();
        return $value;
    }
    
    protected function writeItem($value)
    {
        return false;
    }
    
    protected function readItem()
    {
        return null;
    }
}

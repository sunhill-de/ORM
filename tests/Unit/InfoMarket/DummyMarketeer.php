<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\InfoMarket\Marketeer;
use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;

class Item1 extends SimpleInfoMarketItem
{
    
    protected static $type = 'string';
    
    protected function readItem()
    {
        return 'This is item 1';
    }    
    
}

class Item2 extends SimpleInfoMarketItem
{
    protected static $type = 'string';
    
    
    protected function readItem()
    {
        return 'This is item 2';
    }
    
}

class Item3 extends SimpleInfoMarketItem
{
    protected static $type = 'string';
    
    protected function readItem()
    {
        return 'This is item 3';
    }
    
}

class Item4 extends SimpleInfoMarketItem
{
    protected static $type = 'string';
    
    protected static $item_semantic = 'Temperature';
    
    protected static $item_unit = 'Degreecelsius';
    
    protected static $item_readable = true;
    
    protected static $item_writeable = true;
    
    protected $value = 4;
    
    protected function readItem()
    {
        return $this->value;
    }
    
    protected function writeItem($value)
    {
        $this->value = $value;
        return $this;
    }
    
}

class DummyMarketeer extends Marketeer
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setName('dummy');
        $this->addEntry('item1', Item1::class);
        $this->addEntry('item2', Item2::class);
        $this->addEntry('item3', Item3::class);
        $this->addEntry('item4', Item4::class);
    }
    
}

class CachedDummyMarketeer extends Marketeer
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setCachePhilosophy('group');
        
        $this->setName('dummy');
        $this->addEntry('item1', Item1::class);
        $this->addEntry('item2', Item2::class);
        $this->addEntry('item3', Item3::class);
        $this->addEntry('item4', Item4::class);
    }
    
}

class TopLevelMarketeer extends Marketeer
{
    public function __construct()
    {
        parent::__construct();
        
        $this->setName('top');
        $this->addEntry('dummy', CachedDummyMarketeer::class);
    }
    
}
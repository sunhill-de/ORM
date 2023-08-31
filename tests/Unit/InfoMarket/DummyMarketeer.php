<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\InfoMarket\Marketeer;
use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;

class Item1 extends SimpleInfoMarketItem
{
    
    protected function readItem()
    {
        return 'This is item 1';
    }    
    
}

class Item2 extends SimpleInfoMarketItem
{
    protected function readItem()
    {
        return 'This is item 2';
    }
    
}

class Item3 extends SimpleInfoMarketItem
{
    protected function readItem()
    {
        return 'This is item 3';
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
    }
    
}
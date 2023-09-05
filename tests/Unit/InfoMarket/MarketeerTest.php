<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Marketeer;

class ParentMarketeer extends Marketeer
{
    
    public function __construct()
    {
        parent::__construct();
        $this->addEntry('child', DummyMarketeer::class);
    }
    
}

class MarketeerTest extends TestCase
{
    
    public function testGetItem()
    {
        $test = new DummyMarketeer();
        
        $item = $test->requestItem(['item1']);
        $this->assertTrue(is_a($item, Item1::class));
        $this->assertEquals('This is item 1', $item->getValue());
    }
    
    public function testNestedGetItem()
    {
        $test = new ParentMarketeer();
        
        $item = $test->requestItem(['child','item1']);
        $this->assertTrue(is_a($item, Item1::class));
        $this->assertEquals('This is item 1', $item->getValue());
    }
    
    public function testGetOffer()
    {
        $test = new ParentMarketeer();
        $this->assertEquals(['child'], $test->requestOffer([]));
        $this->assertEquals(['item1','item2','item3','item4'],$test->requestOffer(['child']));
        $this->assertFalse($test->requestOffer(['child','item1']));
    }
}
<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Marketeer;
use Sunhill\ORM\InfoMarket\OnDemandMarketeer;

class OnDemandParentMarketeer extends OnDemandMarketeer
{

    public $initialized = false;
    
    protected function initializeMarketeer()
    {
        $this->initialized = true;
        $this->addEntry('child', DummyMarketeer::class);
    }
    
}

class OnDemandMarketeerTest extends TestCase
{
    
    public function testNestedGetItem()
    {
        $test = new OnDemandParentMarketeer();
        
        $this->assertFalse($test->initialized);
        $item = $test->requestItem(['child','item1']);
        $this->assertTrue($test->initialized);
        $this->assertTrue(is_a($item, Item1::class));
        $this->assertEquals('This is item 1', $item->getValue());
    }
    
    public function testGetOffer()
    {
        $test = new OnDemandParentMarketeer();
        $this->assertFalse($test->initialized);
        $this->assertEquals(['child'], $test->requestOffer([]));
        $this->assertTrue($test->initialized);
        $this->assertEquals(['item1','item2','item3','item4'],$test->requestOffer(['child']));
        $this->assertFalse($test->requestOffer(['child','item1']));
    }
}
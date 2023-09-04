<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Market;
use Sunhill\ORM\InfoMarket\Marketeer;

class MarketTest extends TestCase
{
    
    public function testRequestItem()
    {        
        $test = new Market();
        $test->installMarketeer('dummy',DummyMarketeer::class);
        
        $item = $test->requestItem(['dummy','item1']);
        $this->assertEquals('This is item 1', $item->getValue());
    }
    
    public function testGetItem()
    {
        $test = new Market();
        $test->installMarketeer('dummy',DummyMarketeer::class);
        
        $info = $test->getItem('dummy.item1','anybody','stdclass');
        $this->assertEquals('This is item 1',$info->value);
    }
}
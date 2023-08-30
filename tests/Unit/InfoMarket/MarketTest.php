<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Market;
use Sunhill\ORM\InfoMarket\Marketeer;

class TestMarketeer extends Marketeer
{
    
}

class MarketTest extends TestCase
{
    
    public function testProvidesMarketeer()
    {
        $marketeer = new TestMarketeer();
        $marketeer->name = 'test';
        
        $test = new Market();
        $test->installMarketeer($marketeer);
        
        $this->assertEquals($marketeer, $test->getProperty('test'));
    }
}
<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\InfoMarket\Market;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MarketWithCacheTest extends DatabaseTestCase
{
    
    public function testGetItem()
    {
        $test = new Market();
        $test->setCacheEnabled(true);
        
        $test->installMarketeer('dummy',DummyMarketeer::class);
        
        Cache::shouldReceive('add')->once();
        Cache::shouldReceive('has')->once()->with('dummy.item1');
        $info = $test->getItem('dummy.item1','anybody','stdclass');
        $this->assertEquals('This is item 1',$info->value);        
    }
    
    public function testGetItem2()
    {
        $test = new Market();
        $test->setCacheEnabled(true);
        
        $test->installMarketeer('dummy',DummyMarketeer::class);
        
        $info = $test->getItem('dummy.item1','anybody','stdclass');
        $this->assertTrue(Cache::has('dummy.item1'));
        $this->assertFalse(Cache::has('dummy.item2'));
    }
    
    public function testGetItemFromCache()
    {
        $test = new Market();
        $test->setCacheEnabled(true);
        
        $test->installMarketeer('dummy',DummyMarketeer::class);
        Cache::shouldReceive('has')->with('dummy.item1')->andReturn(true);
        Cache::shouldReceive('get')->with('dummy.item1')->andReturn('{"key": "ABC"}');
        
        
        $info = $test->getItem('dummy.item1','anybody','stdclass');        
        $this->assertEquals('ABC', $info->key);
        
        $info = $test->getItem('dummy.item1','anybody','json');
        $this->assertEquals('{"key": "ABC"}', $info);

        $info = $test->getItem('dummy.item1','anybody','array');
        $this->assertEquals('ABC', $info['key']);        
    }
    
    public function testGetItemWithGroup()
    {
        $test = new Market();
        $test->setCacheEnabled(true);
        $test->installMarketeer('toplevel', TopLevelMarketeer::class);

        $this->assertFalse(Cache::has('toplevel.dummy.item1'));
        $this->assertEquals('This is item 1',$test->getItem('toplevel.dummy.item1','anybody','stdclass')->value);
        $this->assertTrue(Cache::has('toplevel.dummy.item1'));
        $this->assertTrue(Cache::has('toplevel.dummy.item2'));
    }
}
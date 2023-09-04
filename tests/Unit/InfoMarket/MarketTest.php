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
        $this->assertEquals('dummy.item1', $info->request);
        $this->assertEquals('', $info->unit);
        $this->assertEquals('None', $info->unit_name);
        $this->assertEquals('Name', $info->semantic);
        $this->assertEquals('asap', $info->update);
        $this->assertEquals('OK', $info->result);
        $this->assertEquals('anybody', $info->credentials);
    }
    
    public function testGetItemWithUnit()
    {
        $test = new Market();
        $test->installMarketeer('dummy',DummyMarketeer::class);
        
        $info = $test->getItem('dummy.item4','anybody','stdclass');
        $this->assertEquals(4,$info->value);
        $this->assertEquals('4 °C',$info->human_readable_value);
        $this->assertEquals('dummy.item4', $info->request);
        $this->assertEquals('°C', $info->unit);
        $this->assertEquals('Degreecelsius', $info->unit_name);
        $this->assertEquals('Temperature', $info->semantic);
        $this->assertEquals('asap', $info->update);
        $this->assertEquals('OK', $info->result);
        $this->assertEquals('anybody', $info->credentials);        
    }
}
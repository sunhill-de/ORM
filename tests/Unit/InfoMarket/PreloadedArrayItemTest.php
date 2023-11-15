<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;
use Sunhill\ORM\Semantic\Duration;
use Sunhill\ORM\Units\Second;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotWriteableException;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotReadableException;
use Sunhill\ORM\InfoMarket\Items\BaseArrayItem;
use Sunhill\ORM\InfoMarket\Items\PreloadedArrayItem;

class DummyPreloadedArrayItem extends PreloadedArrayItem
{
    
    protected function loadItems(): array
    {
       return [
           $this->createResponseFromValue('AAA')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap'),
           $this->createResponseFromValue('BBB')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap'),
           $this->createResponseFromValue('CCC')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap')
       ]; 
    }
    
}

class PreloadedArrayItemTest extends TestCase
{

    public function testGetOffering()
    {
        $test = new DummyPreloadedArrayItem();
        $this->assertEquals(['count',0,1,2], $test->getValue());
    }
    
    public function testGetCount()
    {
        $test = new DummyPreloadedArrayItem();
        $this->assertEquals(3,$test->requestItem(['count'])->getElement('value'));
    }
    
    public function testGetElement()
    {
        $test = new DummyPreloadedArrayItem();
        $this->assertEquals('BBB', $test->requestItem(['1'])->getElement('value'));
    }
}
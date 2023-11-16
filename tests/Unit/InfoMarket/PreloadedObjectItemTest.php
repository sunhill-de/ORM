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

class DummyPreloadedObjectItem extends PreloadedArrayItem
{
    
    protected function loadItems(): array
    {
        return [
            'A'=>$this->createResponseFromValue('AAA')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap'),
            'B'=>$this->createResponseFromValue('BBB')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap'),
            'C'=>$this->createResponseFromValue('CCC')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap')
        ];
    }
    
}

class PreloadedObjectItemTest extends TestCase
{

    public function testGetOffering()
    {
        $test = new DummyPreloadedObjectItem();
        $this->assertEquals(['count','A','B','C'], array_keys($test->getValue()));
    }
    
    public function testGetElement()
    {
        $test = new DummyPreloadedObjectItem();
        $this->assertEquals('BBB', $test->requestItem(['B'])->getElement('value'));
    }
    
}
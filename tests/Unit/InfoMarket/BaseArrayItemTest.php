<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;
use Sunhill\ORM\Semantic\Duration;
use Sunhill\ORM\Units\Second;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotWriteableException;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotReadableException;
use Sunhill\ORM\InfoMarket\Items\BaseArrayItem;

class DummyBaseArrayItem extends BaseArrayItem
{
    
    protected function getArrayOffering(): array
    {
        return [0,1,2];        
    }
    
    protected function getEntryCount(): int
    {
        return 3;
    }
    
    protected function getIndexedElement(int $index)
    {
        switch ($index) {
            case 0:
                return $this->createResponseFromValue('AAA')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap');
            case 1:
                return $this->createResponseFromValue('BBB')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap');
            case 2:
                return $this->createResponseFromValue('CCC')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap');            
        }
    }
    
}

class BaseArrayItemTest extends TestCase
{

    public function testGetOffering()
    {
        $test = new DummyBaseArrayItem();
        $this->assertEquals(['count',0,1,2], array_keys($test->getValue()));
    }
    
    public function testGetCount()
    {
        $test = new DummyBaseArrayItem();
        $this->assertEquals(3,$test->requestItem(['count'])->getElement('value'));
    }
    
    public function testGetElement()
    {
        $test = new DummyBaseArrayItem();
        $this->assertEquals('BBB', $test->requestItem(['1'])->getElement('value'));
    }
}
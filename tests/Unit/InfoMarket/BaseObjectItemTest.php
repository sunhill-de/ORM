<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;
use Sunhill\ORM\Semantic\Duration;
use Sunhill\ORM\Units\Second;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotWriteableException;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotReadableException;
use Sunhill\ORM\InfoMarket\Items\BaseArrayItem;
use Sunhill\ORM\InfoMarket\Items\BaseObjectItem;

class DummyBaseObjectItem extends BaseObjectItem
{
    
    protected function getObjectOffering(): array
    {
        return ['A'=>'A','B'=>'B','C'=>'C'];        
    }
    
    protected function getElement(string $name)
    {
        switch ($name) {
            case 'A':
                return $this->createResponseFromValue('AAA')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap');
            case 'B':
                return $this->createResponseFromValue('BBB')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap');
            case 'C':
                return $this->createResponseFromValue('CCC')->OK()->type('string')->unit('None')->semantic('Name')->readable()->writeable(false)->update('asap');            
        }
    }
    
}

class BaseObjectItemTest extends TestCase
{

    public function testGetOffering()
    {
        $test = new DummyBaseObjectItem();
        $this->assertEquals(['A','B','C'], array_keys($test->getValue()));
    }
    
    public function testGetElement()
    {
        $test = new DummyBaseObjectItem();
        $this->assertEquals('BBB', $test->requestItem(['B'])->getElement('value'));
    }
}
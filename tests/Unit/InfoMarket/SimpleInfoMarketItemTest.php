<?php

namespace Sunhill\ORM\Tests\Unit\InfoMarket;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\InfoMarket\Items\SimpleInfoMarketItem;
use Sunhill\ORM\Semantic\Duration;
use Sunhill\ORM\Units\Second;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotWriteableException;
use Sunhill\ORM\InfoMarket\Exceptions\ItemNotReadableException;

class DummySimpleInfoMarketItem extends SimpleInfoMarketItem
{
    
    public static function setStaticValue(string $name, $value)
    {
        static::$$name = $value;    
    }
    
    protected function writeItem($value)
    {
        return true;
    }
    
    protected function readItem()
    {
        return 'test';
    }
    
}

class SimpleInfoMarketItemTest extends TestCase
{
    
    public function testConstructor()
    {
        DummySimpleInfoMarketItem::setStaticValue('item_semantic', Duration::class);
        DummySimpleInfoMarketItem::setStaticValue('item_unit', Second::class);
        
        $test = new DummySimpleInfoMarketItem();
        $this->assertEquals(Duration::class, $test->getSemantic());
        $this->assertEquals(Second::class, $test->getUnit());
    }
    
    public function testReadOnly()
    {
        DummySimpleInfoMarketItem::setStaticValue('item_readable', true);
        DummySimpleInfoMarketItem::setStaticValue('item_writeable', false);
        
        $test = new DummySimpleInfoMarketItem();
        $this->assertEquals('test', $test->getValue());
        $this->expectException(ItemNotWriteableException::class);
        $test->setValue('notpossible');
    }
    
    public function testWriteOnly()
    {
        DummySimpleInfoMarketItem::setStaticValue('item_readable', false);
        DummySimpleInfoMarketItem::setStaticValue('item_writeable', true);
        
        $test = new DummySimpleInfoMarketItem();
        $test->setValue('notpossible');
        $this->expectException(ItemNotReadableException::class);
        $hilf = $test->getValue();
    }
    
    public function testGetOffer()
    {
        $test = new DummySimpleInfoMarketItem();
        $this->assertFalse($test->requestOffer([]));
    }
}
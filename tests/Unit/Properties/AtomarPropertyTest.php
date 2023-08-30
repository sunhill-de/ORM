<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Utils\DefaultNull;
use Sunhill\ORM\Properties\AtomarProperty;
use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Properties\Exceptions\WriteToReadonlyException;
use Sunhill\ORM\Properties\Exceptions\InvalidValueException;

class DummyAtomarProperty extends AtomarProperty
{
    
    public $child;
    
    protected function requestTerminalItem(string $name)
    {
        if ($name == 'terminal') {
            return 'terminal';
        }
    }
    
    protected function passItemRequest(string $name, array $path)
    {
        if ($name == 'child') {
            return $this->child->requestItem($path);
        }
    }
    
}

class AtomarPropertyTest extends TestCase
{
 
    /**
     * @dataProvider StandardGettersProvider
     */
    public function testStandardGetters($setter, $getter, $value, $default)
    {
        $test = new AtomarProperty();
        $this->assertEquals($default, $test->$getter());
        $this->assertEquals($test,$test->$setter($value));
        $this->assertEquals($value, $test->$getter());
    }
    
    public static function StandardGettersProvider()
    {
        return [
            ['setNullable','getNullable',false,true],
            ['Nullable','getNullable',false,true],
        ];
    }
    
    public function testDefault()
    {
        $test = new AtomarProperty();
        $this->assertNull($test->getDefault());
        $this->assertFalse($test->getDefaultsNull());
        
        $test->default(5);
        $this->assertEquals(5, $test->getDefault());
        $this->assertFalse($test->getDefaultsNull());
        
        $test->default(null);
        $this->assertEquals(DefaultNull::class, $test->getDefault());
        $this->assertTrue($test->getDefaultsNull());
    }
    
    public function testSetValue()
    {
        $test = new AtomarProperty();
        $this->assertFalse($test->getDirty());
        $test->setValue('ABC');
        
        $this->assertEquals('ABC', $test->getValue());
        $this->assertTrue($test->getDirty());
    }
    
    public function testWriteReadonly()
    {
        $this->expectException(WriteToReadonlyException::class);
        
        $test = new AtomarProperty();
        $test->readonly();
        $test->setValue('ABC');
    }
    
    public function testSetNull()
    {
        $test = new AtomarProperty();
        $test->nullable();
        $this->assertFalse($test->getInitialized());
        $test->setValue(null);
        
        $this->assertTrue($test->getDirty());
        $this->assertTrue($test->getInitialized());
    }
    
    public function testSetNullNotAllowed()
    {
        $this->expectException(InvalidValueException::class);
        $test = new AtomarProperty();
        $test->notNullable();
        $test->setValue(null);
    }
    
    public function testCommit()
    {
        $test = new AtomarProperty();
        $test->setValue(5);
        $this->assertTrue($test->getDirty());
        $test->commit();
        $this->assertFalse($test->getDirty());
        $test->setValue(7);
        $this->assertTrue($test->getDirty());
        $test->rollback();
        $this->assertFalse($test->getDirty());
        $this->assertEquals(5,$test->getValue());
    }
        
    public function testDoubleChange()
    {
        $test = new AtomarProperty();
        $test->setValue(5);        
        $test->commit();
        $test->setValue(7);
        $test->setValue(9);
        $test->rollback();
        $this->assertEquals(5,$test->getValue());
    }
    
    public function testNoChange()
    {
        $test = new AtomarProperty();
        $test->setValue(5);
        $test->commit();
        $test->setValue(5);
        $this->assertFalse($test->getDirty());
        $this->assertEquals(5,$test->getValue());
    }
    
    public function testChangeToSameValue()
    {
        $test = new AtomarProperty();
        $test->setValue(5);
        $this->assertTrue($test->getDirty());
        $test->commit();
        $this->assertFalse($test->getDirty());
        $test->setValue(5);
        $this->assertFalse($test->getDirty());
    }
    
    public function testRequestItem()
    {
        $test = new AtomarProperty();
        $test->setName('test');
        
        $this->assertEquals($test, $test->requestItem([]));
    }
    
    public function testRequestItemNotEmpty()
    {
        $test = new AtomarProperty();
        $test->setName('test');
        
        $this->assertNull($test->requestItem(['something']));        
    }
    
    public function testRequestTerminal()
    {
        $test = new DummyAtomarProperty();
        $test->setName('test');
        
        $this->assertEquals('terminal', $test->requestItem(['terminal']));
    }
    
    public function testRequestChild()
    {
        $child = new DummyAtomarProperty();
        $child->setName('child');
        
        $test = new DummyAtomarProperty();
        $test->setName('test');        
        $test->child = $child;
        
        $this->assertEquals($child, $test->requestItem(['child']));
        $this->assertEquals('terminal', $test->requestItem(['child','terminal']));
    }
}
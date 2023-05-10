<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\DefaultNull;
use Sunhill\ORM\Properties\AtomarProperty;
use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Units\None;

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
    
    public function StandardGettersProvider()
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
        $this->expectException(PropertyException::class);
        
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
        $this->expectException(PropertyException::class);
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
    
}
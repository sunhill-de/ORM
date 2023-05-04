<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\DefaultNull;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Units\None;

class PropertyTest extends TestCase
{
 
    /**
     * @dataProvider StandardGettersProvider
     */
    public function testStandardGetters($setter, $getter, $value, $default)
    {
        $test = new Property();
        $this->assertEquals($default, $test->$getter());
        $this->assertEquals($test,$test->$setter($value));
        $this->assertEquals($value, $test->$getter());
    }
    
    public function StandardGettersProvider()
    {
        return [
            ['setName','getName','test', ''],
            ['name','getName','test', ''],
            ['setUnit','getUnit','abc',None::class],
            ['unit','getUnit','abc',None::class],
            ['setSemantic','getSemantic','abc', Name::class],
            ['semantic','getSemantic','abc', Name::class],
            ['setType','getType','abc', ''],
            ['type','getType','abc', ''],
            ['setClass','getClass','abc', null],
            ['setReadonly','getReadonly',true, false],
            ['readonly','getReadonly',true, false],
            ['setSearchable','getSearchable',true, false],
            ['searchable','getSearchable',true, false],
            ['setNullable','getNullable',false,true],
            ['Nullable','getNullable',false,true],
        ];
    }
    
    public function testOwner()
    {
        $test = new Property();
        $test->setOwner($test);
        $this->assertEquals($test, $test->getOwner());        
    }
    
    /**
     * @dataProvider AdditionalGetterProvider
     * @param unknown $item
     * @param unknown $value
     */
    public function testAdditionalGetter($item, $value)
    {
        $test = new Property();
        $method = 'set'.$item;
        $test->$method($value);
        $method = 'get'.$item;
        $this->assertEquals($value, $test->$method());
    }
    
    public function AdditionalGetterProvider()
    {
        return [
            ['test','TEST'],
            ['Test','TEST'],
            ['_Test','TEST']
        ];
    }
    
    public function testUnknownMethod()
    {
        $this->expectException(PropertyException::class);
        
        $test = new Property();
        $test->unknownMethod();
    }
    
    public function testDefault()
    {
        $test = new Property();
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
        $test = new Property();
        $this->assertFalse($test->getDirty());
        $test->setValue('ABC');
        
        $this->assertEquals('ABC', $test->getValue());
        $this->assertTrue($test->getDirty());
    }
    
    public function testWriteReadonly()
    {
        $this->expectException(PropertyException::class);
        
        $test = new Property();
        $test->readonly();
        $test->setValue('ABC');
    }
    
    public function testSetNull()
    {
        $test = new Property();
        $test->nullable();
        $this->assertFalse($test->getInitialized());
        $test->setValue(null);
        
        $this->assertTrue($test->getDirty());
        $this->assertTrue($test->getInitialized());
    }
    
    public function testSetNullNotAllowed()
    {
        $this->expectException(PropertyException::class);
        $test = new Property();
        $test->notNullable();
        $test->setValue(null);
    }
    
}
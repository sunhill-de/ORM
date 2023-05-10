<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\DefaultNull;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyException;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Properties\NonAtomarProperty;

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
            ['setClass','getClass','abc', null],
            ['setReadonly','getReadonly',true, false],
            ['readonly','getReadonly',true, false],
            ['setSearchable','getSearchable',true, false],
            ['searchable','getSearchable',true, false],
        ];
    }
    
    public function testOwner()
    {
        $test = new NonAtomarProperty();
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
        
}
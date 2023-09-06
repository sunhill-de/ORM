<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Exceptions\InvalidNameException;

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
    
    public static function StandardGettersProvider()
    {
        return [
            ['setName','getName','test', ''],
            ['name','getName','test', ''],
            ['setUnit','getUnit','abc',None::class],
            ['unit','getUnit','abc',None::class],
            ['setSemantic','getSemantic','abc', Name::class],
            ['semantic','getSemantic','abc', Name::class],
//            ['setClass','getClass','abc', null],
            ['setReadonly','getReadonly',true, false],
            ['readonly','getReadonly',true, false],
            ['setSearchable','getSearchable',true, false],
            ['searchable','getSearchable',true, false],
        ];
    }
    
    /**
     * @dataProvider NamesProvider
     * @param unknown $test
     * @param bool $forbidden
     */
    public function testNames($name, bool $forbidden)
    {
        if ($forbidden) {
            $this->expectException(InvalidNameException::class);
        }
        $test = new Property();
        
        $test->setName($name);
        
        $this->assertTrue(true);
    }
    
    public static function NamesProvider()
    {
        return [
            ['_forbidden', true],
            ['string', true],
            ['object', true],
            ['float', true],
            ['integer', true],
            ['boolean', true],
            ['collection', true],
            ['name_with_underscores', false],
            ['namewith1digit', false],
        ];    
    }
    
    public function testOwner()
    {
        $test = new ORMObject();
        $test->setOwner(ORMObject::class);
        $this->assertEquals(ORMObject::class, $test->getOwner());        
    }
    
    /**
     * @dataProvider AdditionalGetterProvider
     * @param unknown $item
     * @param unknown $value
     */
    public function testAdditionalGetter($item, $value)
    {
        $test = new Property();
        $method = 'set_'.$item;
        $test->$method($value);
        $method = 'get_'.$item;
        $this->assertEquals($value, $test->$method());
    }
    
    public static function AdditionalGetterProvider()
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
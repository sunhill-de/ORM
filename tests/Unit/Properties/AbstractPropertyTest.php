<?php

use Sunhill\ORM\Semantic\Name;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Units\None;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Properties\Exceptions\InvalidNameException;
use Sunhill\ORM\Properties\AbstractProperty;
use Sunhill\ORM\Tests\TestSupport\TestAbstractIDStorage;

class NonAbstractProperty extends AbstractProperty
{
    
    public function __construct()
    {
        $this->setName('test_int');
    }
    
    public function getAccessType(): string
    {
        return 'integer';    
    }
}

class AbstractPropertyTest extends TestCase
{
     
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
        $test = new NonAbstractProperty();
        
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
        
    /**
     * @dataProvider AdditionalGetterProvider
     * @param unknown $item
     * @param unknown $value
     */
    public function testAdditionalGetter($item, $value)
    {
        $test = new NonAbstractProperty();
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
        
        $test = new NonAbstractProperty();
        $test->unknownMethod();
    }
    
    public function testSetName()
    {
        $test = new NonAbstractProperty();
        $this->assertEquals('test_int', $test->getName());
        $test->setName('another');
        $this->assertEquals('another', $test->getName());        
    }
    
    public function testSetStorage()
    {
        $storage = new TestAbstractIDStorage();
        $storage->setID(1);
        
        $test = new NonAbstractProperty();
        $test->setStorage($storage);
        $this->assertEquals($storage, $test->getStorage());
        $this->assertEquals(345, $test->getValue());
    }
}
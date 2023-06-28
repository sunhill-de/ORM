<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyMap;

class PropertyMapTest extends TestCase
{
 
    public function testMapCount()
    {
        $test = new PropertyMap();
        $test->setName('test');
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()['A'] = 1;
        $test->getValue()['B'] = 2;
        $test->getValue()['C'] = 3;
        
        $this->assertEquals(3,count($test->getValue()));
        $this->assertEquals(2,$test->getValue()['B']);
    }
    
    public function testWrongIndex()
    {
        $this->expectException(PropertyException::class);

        $test = new PropertyMap();
        $test->setName('test');
        
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()['A'] = 1;
        $test->getValue()['B'] = 2;
        $test->getValue()['C'] = 3;
        
        $a = $test->getValue()['D'];
    }
    
    public function testEmpty()
    {
        $test = new PropertyMap();
        $test->setName('test');
        
        $test->setElementType(PropertyInteger::class);
        
        $this->assertTrue($test->empty());

        $test->getValue()['A'] = 1;
        $test->getValue()['B'] = 2;
        $test->getValue()['C'] = 3;
        
        $this->assertFalse($test->empty());
        
        $test->clear();
        
        $this->assertTrue($test->empty());
    }
    
    public function testIsElementIn()
    {
        $test = new PropertyMap();
        $test->setName('test');
        
        $test->setElementType(PropertyInteger::class);
        
        $this->assertTrue($test->empty());
        
        $test->getValue()['A'] = 1;
        $test->getValue()['B'] = 2;
        $test->getValue()['C'] = 3;
        
        $this->assertTrue($test->isElementIn(2));
        $this->assertFalse($test->isElementIn(5));
    }
    
    public function testValidator()
    {
        $this->expectException(PropertyException::class);
        
        $test = new PropertyMap();
        $test->setName('test');
        
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()['A'] = 'ABC';        
    }
    
    public function testForeach()
    {
        $test = new PropertyMap();
        $test->setName('test');
        
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()['A'] = 1;
        $test->getValue()['B'] = 2;
        $test->getValue()['C'] = 3;
        
        $key_result = '';
        $element_result = '';
        foreach ($test->getValue() as $key => $element) {
            $key_result .= $key;
            $element_result .= $element;
        }
        $this->assertEquals("123",$element_result);
        $this->assertEquals("ABC",$key_result);
    }
}
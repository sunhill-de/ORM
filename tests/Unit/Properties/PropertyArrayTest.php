<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\Exceptions\PropertyException;
use Sunhill\ORM\Properties\PropertyInteger;

class PropertyArrayTest extends TestCase
{
 
    public function testArrayCount()
    {
        $test = new PropertyArray();
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()[] = 1;
        $test->getValue()[] = 2;
        $test->getValue()[] = 3;
        
        $this->assertEquals(3,count($test->getValue()));
        $this->assertEquals(2,$test->getValue()[1]);
    }
    
    public function testWrongIndex()
    {
        $this->expectException(PropertyException::class);

        $test = new PropertyArray();
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()[] = 1;
        $test->getValue()[] = 2;
        $test->getValue()[] = 3;
        
        $a = $test->getValue()[4];
    }
    
    public function testEmpty()
    {
        $test = new PropertyArray();
        $test->setElementType(PropertyInteger::class);
        
        $this->assertTrue($test->empty());

        $test->getValue()[] = 1;
        $test->getValue()[] = 2;
        $test->getValue()[] = 3;
        
        $this->assertFalse($test->empty());
        
        $test->clear();
        
        $this->assertTrue($test->empty());
    }
    
    public function testIsElementIn()
    {
        $test = new PropertyArray();
        $test->setElementType(PropertyInteger::class);
        
        $this->assertTrue($test->empty());
        
        $test->getValue()[] = 1;
        $test->getValue()[] = 2;
        $test->getValue()[] = 3;
        
        $this->assertTrue($test->isElementIn(2));
        $this->assertFalse($test->isElementIn(5));
    }
    
    public function testValidator()
    {
        $this->expectException(PropertyException::class);
        
        $test = new PropertyArray();
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()[] = 'ABC';        
    }
    
    public function testForeach()
    {
        $test = new PropertyArray();
        $test->setElementType(PropertyInteger::class);
        
        $test->getValue()[] = 1;
        $test->getValue()[] = 2;
        $test->getValue()[] = 3;

        $key_result = '';
        $element_result = '';
        foreach ($test->getValue() as $key => $element) {
            $key_result .= $key;
            $element_result .= $element;
        }
        $this->assertEquals("123",$element_result);
        $this->assertEquals("012",$key_result);
    }
}
<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyDatetime;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyCollection;
use Sunhill\ORM\Properties\PropertyText;
use Sunhill\ORM\Properties\PropertyTime;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Properties\Exceptions\InvalidValueException;
use Sunhill\ORM\Properties\PropertyMap;
use Sunhill\ORM\Tests\Testobjects\TestChild;

class PropertyValueTest extends TestCase
{
 
    /**
     * @dataProvider SetValueScalarProvider
     * 
     */
    public function testSetValueScalar($property_class, $postfix, $value, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        
        $property = new $property_class();
        if (is_callable($postfix)) {
            $postfix($property);
        }

        if (class_exists($expect)) {
            $this->expectException($expect);
        }
        if (is_callable($value)) {
            $property->setValue($value());
        } else {
            $property->setValue($value);
        }
        $this->assertEquals($expect, $property->getValue());
    }
        
    public static function SetValueScalarProvider()
    {
        return [
            // ========================== Boolean ===============================
            [PropertyBoolean::class, null, 'Y', true],
            [PropertyBoolean::class, null, 'N', false],
            [PropertyBoolean::class, null, '+', true],
            [PropertyBoolean::class, null, '-', false],
            [PropertyBoolean::class, null, 'true', true],
            [PropertyBoolean::class, null, 'false', false],
            [PropertyBoolean::class, null, true, true],
            [PropertyBoolean::class, null, false, false],
            [PropertyBoolean::class, null, 'ABC', InvalidValueException::class],
            [PropertyBoolean::class, null, 1, true],
            [PropertyBoolean::class, null, 0, false],
            [PropertyBoolean::class, null, 10, true],
            
            // =========================== Date ================================
            [PropertyDate::class, null, '01.02.2018', '2018-02-01'],
            [PropertyDate::class, null, '2018-02-02', '2018-02-02'],
            [PropertyDate::class, null, '1.2.2018', '2018-02-01'],
            [PropertyDate::class, null, '2018-2-1', '2018-02-01'],
            [PropertyDate::class, null, 1686778521.3, '2023-06-14'],
            [PropertyDate::class, null, '2018-2', '2018-02-00'],
            [PropertyDate::class, null, '2.3.', '0000-03-02'],
            [PropertyDate::class, null, '2018', '2018-00-00'],
            [PropertyDate::class, null, false, InvalidValueException::class],
            [PropertyDate::class, null, 'ABC', InvalidValueException::class],
            [PropertyDate::class, null, '', InvalidValueException::class],
            [PropertyDate::class, null, 1686778521, '2023-06-14'],
            
            // ========================= DateTime ===============================
            [PropertyDatetime::class, null, '2018-02-01 11:11:11', true],
            [PropertyDatetime::class, null, 1686778521, true],
            
            // =========================== Enum =================================
            [PropertyEnum::class, function(&$property) {
                $property->setEnumValues(['TestA','TestB']);
            }, 'TestA', true],
            [PropertyEnum::class, function(&$property) {
                $property->setEnumValues(['TestA','TestB']);                
            }, 'NonExisting', InvalidValueException::class],
            
            // ========================== Float ===============================
            [PropertyFloat::class, null, 1, true, 1.0],
            [PropertyFloat::class, null, 1.1, true, 1.1],
            [PropertyFloat::class, null, "1", true, 1],
            [PropertyFloat::class, null, "1.1", true, 1.1],
            [PropertyFloat::class, null, "A", InvalidValueException::class],
            [PropertyFloat::class, null, "1.1.1", InvalidValueException::class],
            
            // ========================== Integer ===============================
            [PropertyInteger::class, null, 1, true, 1],
            [PropertyInteger::class, null, 1.1, InvalidValueException::class],
            [PropertyInteger::class, null, 'A', InvalidValueException::class],
            [PropertyInteger::class, null, '1', true, 1],
            
            // =========================== Text =================================
            [PropertyText::class, null, 'Lorem ipsum', 'Lorem ipsum'],
                
            // =========================== Time =================================
            [PropertyTime::class, null, '11:11:11', true, '11:11:11'],
            [PropertyTime::class, null, '11:11', true, '11:11:00'],
            [PropertyTime::class, null, '1:1', true, '01:01:00'],
                
            // ========================== Varchar ===============================
            [PropertyVarchar::class, null, 1, true, "1"],
            [PropertyVarchar::class, null, 1.1, true, "1.1"],
            [PropertyVarchar::class, null, "1", true, "1"],
         ];
    }

    /**
     * @dataProvider SetValueObjectProvider
     *
     */
    public function testSetValueObject($property_class, $postfix, $value, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        
        $property = new $property_class();
        if (is_callable($postfix)) {
            $postfix($property);
        }
        
        if (class_exists($expect)) {
            $this->expectException($expect);
        }
        if (is_callable($value)) {
            $property->setValue($value());
        } else {
            $property->setValue($value);
        }
        $object = $property->getValue();
        $this->assertTrue(is_a($object, Dummy::class));
    }
    
    public static function SetValueObjectProvider()
    {
        return [
            // ========================== Object ===============================
            [
                PropertyObject::class,
                function(&$property) {
                    $property->setAllowedClasses('dummy');
                },
                function() {
                    return new Dummy();
                }, true
            ],
            [
                PropertyObject::class,
                function(&$property) {
                    $property->setAllowedClasses('dummy');
                },
                function() {
                    return new TestParent();
                }, InvalidValueException::class
            ],
            [
                PropertyObject::class,
                function(&$property) {
                    $property->setAllowedClasses('dummy');
                },                
                function() {
                    return new DummyChild();
                }, true
            ],
            [
                PropertyObject::class,
                function(&$property) {
                    $property->setAllowedClasses('dummychild');
                 },
                 function() {
                    return new Dummy();
                 }, InvalidValueException::class
            ],
            [
                PropertyObject::class,
                function(&$property) {
                    $property->setAllowedClasses('dummy','testparent');
                },
                function() {
                    return new Dummy();
                }, true
            ],
            [
                PropertyObject::class,
                function(&$property) {
                    $property->setAllowedClasses('dummychild','testparent');
                },
                function() {
                    return new Dummy();
                }, InvalidValueException::class
            ],
        ];
    }
   
    /**
     * @dataProvider SetValueArrayProvider
     *
     */
    public function testSetValueArray($property_class, $postfix, $value, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        
        $property = new $property_class();
        $property->setName('test');
        if (is_callable($postfix)) {
            $postfix($property);
        }
        
        if (class_exists($expect) && is_a($expect, \Exception::class, true)) {
            $this->expectException($expect);
        }
        if (is_callable($value)) {
            $property[] = $value();
        } else {
            $property[] = $value;
        }
        $element = $property[0];
        
        if (is_object($element)) {
            $this->assertTrue(is_a($element, $expect));
        } else {
            $this->assertEquals($expect, $element);
        }
    }
    
    public static function SetValueArrayProvider()
    {
        return [
            // =========================== Array ================================
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyInteger::class);
            }, 44, true],
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyInteger::class);
            }, 'ABC', InvalidValueException::class],
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyObject::class)->setAllowedClasses('dummy');
            }, function() {
                return new Dummy();
            }, Dummy::class],
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyObject::class)->setAllowedClasses('dummy');
            }, function() {
                return new TestParent();
            }, InvalidValueException::class],
        ];            
    }
    
    /**
     * @dataProvider SetValueMapProvider
     *
     */
    public function testSetValueMap($postfix, $index, $value, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        
        $property = new PropertyMap();
        $property->setName('test');
        
        if (is_callable($postfix)) {
            $postfix($property);
        }
        
        if (class_exists($expect) && is_a($expect, \Exception::class, true)) {
            $this->expectException($expect);
        }
        if (is_callable($value)) {
            $property[$index] = $value();
        } else {
            $property[$index] = $value;
        }
        $element = $property[$index];
        
        if (is_object($element)) {
            $this->assertTrue(is_a($element, $expect));
        } else {
            $this->assertEquals($expect, $element);
        }
    }
    
    public static function SetValueMapProvider()
    {
        return [
            // =========================== Array ================================
            [function(&$property) {
                $property->setElementType(PropertyInteger::class);
            }, 'KeyA', 44, true],
            [function(&$property) {
                $property->setElementType(PropertyInteger::class);
            }, 'KeyA', 'ABC', InvalidValueException::class],
            [function(&$property) {
                $property->setElementType(PropertyObject::class)->setAllowedClasses('dummy');
            }, 'KeyA', function() {
                return new Dummy();
            }, Dummy::class],
            [function(&$property) {
                $property->setElementType(PropertyObject::class)->setAllowedClasses('dummy');
            }, 'KeyA', function() {
                return new TestParent();
            }, InvalidValueException::class],
            ];
    }
    
    public function testKeyfield()
    {
        $test = new TestParent();
        $test->parentchar = 'ABC';
        $test->parentint = 123;
        $this->assertEquals('ABC (123)', $test->parentkeyfield);
    }
    
    public function testKeyfieldWithObject()
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        
        $dummy = new Dummy();
        $dummy->dummyint = 123;
        $test = new TestChild();
        $test->childobject = $dummy;
        $test->parentint = 321;
        $this->assertEquals('123 (321)', $test->childkeyfield);
    }
    
    public function testKeyfieldWithEmptyObject()
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        
        $test = new TestChild();
        $test->parentint = 321;
        $this->assertEquals(' (321)', $test->childkeyfield);
    }
    
    public function testCalcfield()
    {
        $test = new TestParent();
        $test->parentint = 122;
        $this->assertEquals('122A', $test->parentcalc);
    }
    
}
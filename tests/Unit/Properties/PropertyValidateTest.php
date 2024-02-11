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

class PropertyValidateTest extends TestCase
{
 
    /**
     * @dataProvider ValidateProvider
     * 
     * @param unknown $property_class
     * @param unknown $postfix
     * @param unknown $value
     * @param unknown $expect
     * 
     * @tests Almost any Property type for validity and data conveersion
     */
    public function testValidate($property_class, $postfix, $value, $expect, $convert = null)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        
        $property = new $property_class();
        if (is_callable($postfix)) {
            $postfix($property);
        }
        if (is_callable($value)) {
            $this->assertEquals($expect, $property->isValid($value()));
        } else {
            $this->assertEquals($expect, $property->isValid($value));            
        }
        if ($expect && !is_null($convert)) {
            $this->assertEquals($convert, $property->convertValue($value));
        }
    }
    
    public static function ValidateProvider()
    {
        return [
            // =========================== Array ================================
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyInteger::class);
            }, 44, true],
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyInteger::class);
            }, 'ABC', false],
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyObject::class)->setAllowedClasses(Dummy::class);
            }, function() {
                return new Dummy();
            }, true],
            [PropertyArray::class, function(&$property) {
                $property->setElementType(PropertyObject::class)->setAllowedClasses(Dummy::class);
            }, function() {
                return new TestParent();
            }, false],
            
            
            
            // ========================= Collection =============================
            [PropertyCollection::class, function(&$property) {
                $property->setAllowedCollection(DummyCollection::class);                
            }, function() {
                return new DummyCollection();
            }, true],
            [PropertyCollection::class, function(&$property) {
                $property->setAllowedCollection(DummyCollection::class);
            }, function() {
                return new ComplexCollection();
            }, false],
                        
                        
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
                }, false
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
                }, false
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
                }, false
            ],
         ];
    }
    
}
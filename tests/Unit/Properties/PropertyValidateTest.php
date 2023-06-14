<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Property;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyFloat;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Tests\Testobjects\TestParent;

class PropertyValidateTest extends TestCase
{
 
    /**
     * @dataProvider ValidateProvider
     * 
     * @param unknown $property_class
     * @param unknown $postfix
     * @param unknown $value
     * @param unknown $expect
     */
    public function testValidate($property_class, $postfix, $value, $expect)
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
    }
    
    public function ValidateProvider()
    {
        return [
           // ========================== Integer ===============================
            [PropertyInteger::class, null, 1, true],
            [PropertyInteger::class, null, 1.1, false],
            [PropertyInteger::class, null, 'A', false],
            [PropertyInteger::class, null, '1', true],

            // ========================== Float ===============================
            [PropertyFloat::class, null, 1, true],
            [PropertyFloat::class, null, 1.1, true],
            [PropertyFloat::class, null, "1", true],
            [PropertyFloat::class, null, "1.1", true],
            [PropertyFloat::class, null, "A", false],
            [PropertyFloat::class, null, "1.1.1", false],

            // ========================== Varchar ===============================
            [PropertyVarchar::class, null, 1, true],
            [PropertyVarchar::class, null, 1.1, true],
            [PropertyVarchar::class, null, "1", true],
            
            // ========================== Boolean ===============================
            [PropertyBoolean::class, null, 'Y', true],
            [PropertyBoolean::class, null, 'N', true],
            [PropertyBoolean::class, null, '+', true],
            [PropertyBoolean::class, null, '-', true],
            [PropertyBoolean::class, null, 'true', true],
            [PropertyBoolean::class, null, 'false', true],
            [PropertyBoolean::class, null, true, true],
            [PropertyBoolean::class, null, false, true],
            [PropertyBoolean::class, null, 'ABC', false],
            [PropertyBoolean::class, null, 1, true],
            [PropertyBoolean::class, null, 0, true],
            [PropertyBoolean::class, null, 10, true],
            
            // =========================== Date ================================
            [PropertyDate::class, null, '1.2.2018', true],
            [PropertyDate::class, null, '2018-2-1', true],
            [PropertyDate::class, null, 23.3, true],
            [PropertyDate::class, null, '2018-2', true],
            [PropertyDate::class, null, '2.3.', true],
            [PropertyDate::class, null, '2018', true],
            [PropertyDate::class, null, false, false],            
            [PropertyDate::class, null, 'ABC', false],
            [PropertyDate::class, null, '', false],
            
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
    
    /**
     * @dataProvider ConvertValueProvider
     */
    public function testConvertValue($property_class, $postfix, $value, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(TestParent::class);
        
        $property = new $property_class();
        if (is_callable($postfix)) {
            $postfix($property);
        }
        if (is_callable($value)) {
            $this->assertEquals($expect, $property->convertValue($value()));
        } else {
            $this->assertEquals($expect, $property->convertValue($value));
        }        
    }
    
    public function ConvertValueProvider()
    {
        return [
            [PropertyInteger::class, null, 1, 1],            
            [PropertyFloat::class, null, 1.1, 1.1],
            [PropertyVarchar::class, null, "A", "A"],
            [PropertyVarchar::class, function($property) { $property->setMaxLen(2); }, "ABCD", "AB"],
        ];
    }
}
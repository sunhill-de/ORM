<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Types\TypeVarchar;
use Sunhill\ORM\Properties\Exceptions\InvalidValueException;
use Sunhill\ORM\Properties\Types\TypeInteger;
use Sunhill\ORM\Properties\Types\TypeFloat;
use Sunhill\ORM\Properties\Types\TypeBoolean;
use Sunhill\ORM\Properties\Types\TypeDateTime;
use Sunhill\ORM\Tests\ReadonlyDatabaseTestCase;
use Sunhill\ORM\Properties\Types\TypeDate;
use Sunhill\ORM\Properties\Types\TypeTime;
use Sunhill\ORM\Properties\Types\TypeText;
use Sunhill\ORM\Properties\Types\TypeEnum;
use Sunhill\ORM\Properties\Types\TypeCollection;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Testobjects\AnotherDummyCollection;

class TypesTest extends TestCase
{

    protected function getTestType($type, $setters)
    {
        $test = new $type();
        foreach ($setters as $name => $value) {
            $method = 'set'.$name;
            $test->$method($value);
        }
        
        return $test;
    }
    
    /**
     * @dataProvider validateProvider
     */
    public function testValidateType($type, $setters, $test_input, $expect)
    {
        $test = $this->getTestType($type, $setters);
        
        if (is_callable($test_input)) {
            $this->assertEquals($expect, $test->isValid($test_input()));            
        } else {
            $this->assertEquals($expect, $test->isValid($test_input));            
        }
    }
    
    static public function validateProvider()
    {
        return [
            [TypeBoolean::class, [], 'Y', true],
            [TypeBoolean::class, [], 'N', true],
            [TypeBoolean::class, [], '+', true],
            [TypeBoolean::class, [], '-', true],
            [TypeBoolean::class, [], 'true', true],
            [TypeBoolean::class, [], 'false', true],
            [TypeBoolean::class, [], true, true],
            [TypeBoolean::class, [], true, true],
            [TypeBoolean::class, [], 'ABC', true],
            [TypeBoolean::class, [], 1, true],
            [TypeBoolean::class, [], 0, true],
            [TypeBoolean::class, [], 10, true],

            [
                TypeCollection::class, 
                ['AllowedCollection'=>DummyCollection::class], 
                function() { return new DummyCollection(); }, 
                true
            ],
            [
                TypeCollection::class, 
                ['AllowedCollection'=>DummyCollection::class],
                function() { return new AnotherDummyCollection(); }, 
                false
            ],
            [
                TypeCollection::class,
                ['AllowedCollection'=>DummyCollection::class],
                1,
                true
            ],
                
            [TypeDatetime::class, [], '2018-02-01 11:11:11', true],
            [TypeDatetime::class, [], '2018-02-32 11:11:11', false],
            [TypeDatetime::class, [], '01.02.2018 11:11:11', true],
            [TypeDateTime::class, [], 1686778521, true],
            [TypeDateTime::class, [], "1686778521", true],
            [TypeDateTime::class, [], "@1686778521", true],
            [TypeDateTime::class, [], 'ABC', false],
            [TypeDateTime::class, [], 1686778521.123, true],            
                        
            [TypeDate::class, [], '01.02.2018', true],
            [TypeDate::class, [], '2018-02-02', true],
            [TypeDate::class, [], '1.2.2018', true],
            [TypeDate::class, [], '2018-2-1', true],
            [TypeDate::class, [], 1686778521.3, true],
            [TypeDate::class, [], '2018-2', true],
            [TypeDate::class, [], '2.3.', true],
            [TypeDate::class, [], '2018', true],
            [TypeDate::class, [], false, false],
            [TypeDate::class, [], 'ABC', false],
            [TypeDate::class, [], '', false],
            [TypeDate::class, [], 1686778521, true],
            
            [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', true],
            [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'NonExisting', false],
            
            [TypeFloat::class, [], 1, true],
            [TypeFloat::class, [], 1.1, true],
            [TypeFloat::class, [], "1", true],
            [TypeFloat::class, [], "1.1", true],
            [TypeFloat::class, [], "A", false],
            [TypeFloat::class, [], "1.1.1", false],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],6,true],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],5,true],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],10,true],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],11,false],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10],1,false],            
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],6,true],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],5,true],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],10,true],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],11,true],
            [TypeFloat::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],1,true],
            
            [TypeInteger::class,[],1,true],
            [TypeInteger::class,[],"1",true],
            [TypeInteger::class,[],'A',false],
            [TypeInteger::class,[],1.1,false],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],6,true],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],5,true],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],10,true],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],11,false],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10],1,false],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],6,true],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],5,true],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],10,true],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],11,true],
            [TypeInteger::class,['Minimum'=>5,'Maximum'=>10,'OutOfBoundsPolicy'=>'set'],1,true],
            
            [TypeText::class, [], 'Lorem ipsum', true],
            [TypeText::class, [], function() { return new \StdClass(); }, false],
            
            [TypeTime::class, [], '11:11:11', true],
            [TypeTime::class, [], '11:11', true],
            [TypeTime::class, [], '1:1', true],
            
            [TypeVarchar::class,[],'Test',true],
            [TypeVarchar::class,['MaxLen'=>2],'Test',true],
            [TypeVarchar::class,['MaxLen'=>2,'LengthExceedPolicy'=>'invalid'],'Test',false],
            [TypeVarchar::class, [], function() { return new \StdClass(); }, false],
            
        ];
    }
    
    /**
     * @dataProvider convertProvider
     * @group convert
     */
    public function testConvertToInput($type, $setters, $test_input, $expect, $expect_mod = null)
    {
        $test = $this->getTestType($type, $setters);
        
        if ($expect == 'except') {
            $this->expectException(InvalidValueException::class);
        }
        if (is_callable($expect_mod)) {
            $this->assertEquals($expect, $expect_mod($this->callProtectedMethod($test, 'formatForStorage',[$test_input])));
        } else {
            $this->assertEquals($expect, $this->callProtectedMethod($test, 'formatForStorage',[$test_input]));            
        }
    }
    
    static public function convertProvider()
    {
        return [
 /*           [TypeBoolean::class, [], 'Y', 1],
            [TypeBoolean::class, [], 'N', 0],
            [TypeBoolean::class, [], '+', 1],
            [TypeBoolean::class, [], '-', 0],
            [TypeBoolean::class, [], 'true', 1],
            [TypeBoolean::class, [], 'false', 0],
            [TypeBoolean::class, [], true, 1],
            [TypeBoolean::class, [], false, 0],
            [TypeBoolean::class, [], 'ABC', 0],
            [TypeBoolean::class, [], 1, 1],
            [TypeBoolean::class, [], 0, 0],
            [TypeBoolean::class, [], 10, 1],
  */          
            [TypeDate::class, [], '01.02.2018', '2018-02-01', function($input) { return $input->format('Y-m-d'); }],
            [TypeDate::class, [], '2018-02-02', '2018-02-02', function($input) { return $input->format('Y-m-d'); }],
            [TypeDate::class, [], '1.2.2018', '2018-02-01', function($input) { return $input->format('Y-m-d'); }],
            [TypeDate::class, [], '2018-2-1', '2018-02-01', function($input) { return $input->format('Y-m-d'); }],
            [TypeDate::class, [], 1686778521.3, '2023-06-14', function($input) { return $input->format('Y-m-d'); }],
            [TypeDate::class, [], '2018-2', '2018-02-01', function($input) { return $input->format('Y-m-d'); }],
            [TypeDate::class, [], false, 'except'],
            [TypeDate::class, [], 'ABC', 'except'],
            [TypeDate::class, [], '', 'except'],
            [TypeDate::class, [], 1686778521, '2023-06-14', function($input) { return $input->format('Y-m-d'); }],
            
            [TypeDatetime::class, [], '2018-02-01 11:11:11', '2018-02-01 11:11:11', function($input) { return $input->format('Y-m-d H:i:s'); }],
            [TypeDatetime::class, [], '1.2.2018 11:11:11', '2018-02-01 11:11:11', function($input) { return $input->format('Y-m-d H:i:s'); }],
            [TypeDateTime::class, [], 1686778521, '2023-06-14 21:35:21', function($input) { return $input->format('Y-m-d H:i:s'); }],
            
            [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', 'TestA'],
            
            [TypeFloat::class, [], 1, 1.0],
            [TypeFloat::class, [], 1.1, 1.1],
            [TypeFloat::class, [], "1", 1],
            [TypeFloat::class, [], "1.1", 1.1],
            [TypeFloat::class, [], "A", 'except'],
            [TypeFloat::class, [], "1.1.1", 'except'],
            
            [TypeInteger::class, [], 1, 1],
            [TypeInteger::class, [], 1.1, 'except'],
            [TypeInteger::class, [], 'A', 'except'],
            [TypeInteger::class, [], '1', 1],
            
            [TypeText::class, [], 'Lorem ipsum', 'Lorem ipsum'],
            [TypeText::class, [], function() { return new \StdClass(); }, 'except'],
            
            [TypeTime::class, [], '11:11:11', '11:11:11', function($input) { return $input->format('H:i:s'); }],
            [TypeTime::class, [], '11:11', '11:11:00', function($input) { return $input->format('H:i:s'); }],
            [TypeTime::class, [], '1:1', '01:01:00', function($input) { return $input->format('H:i:s'); }],
            
            [TypeVarchar::class,[],'Test','Test'],
            [TypeVarchar::class,['MaxLen'=>2],'Test','Te'],            
            [TypeVarchar::class,['MaxLen'=>2,'LengthExceedPolicy'=>'invalid'],'Test','except']
        ];
    }
}
<?php

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Properties\Types\TypeVarchar;
use Sunhill\ORM\Properties\Exceptions\InvalidValueException;
use Sunhill\ORM\Properties\Types\TypeInteger;
use Sunhill\ORM\Properties\Types\TypeFloat;
use Sunhill\ORM\Properties\Types\TypeBoolean;
use Sunhill\ORM\Properties\Types\TypeDateTime;

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
        
        $this->assertEquals($expect, $test->isValid($test_input));
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

            [TypeDatetime::class, [], '2018-02-01 11:11:11', true],
            [TypeDatetime::class, [], '2018-02-32 11:11:11', false],
            [TypeDatetime::class, [], '01.02.2018 11:11:11', true],
            [TypeDateTime::class, [], 1686778521, true],
            [TypeDateTime::class, [], "1686778521", true],
            [TypeDateTime::class, [], "@1686778521", true],
            [TypeDateTime::class, [], 'ABC', false],
            [TypeDateTime::class, [], 1686778521.123, true],
            
            /*            
            [TypeDate::class, null, '01.02.2018', true, '2018-02-01'],
            [TypeDate::class, null, '2018-02-02', true, '2018-02-02'],
            [TypeDate::class, null, '1.2.2018', true, '2018-02-01'],
            [TypeDate::class, null, '2018-2-1', true, '2018-02-01'],
            [TypeDate::class, null, 1686778521.3, true, '2023-06-14'],
            [TypeDate::class, null, '2018-2', true, '2018-02-00'],
            [TypeDate::class, null, '2.3.', true,'0000-03-02'],
            [TypeDate::class, null, '2018', true,'2018-00-00'],
            [TypeDate::class, null, false, false],
            [TypeDate::class, null, 'ABC', false],
            [TypeDate::class, null, '', false],
            [TypeDate::class, null, 1686778521, true, '2023-06-14'],
*/            
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
            
            [TypeVarchar::class,[],'Test',true],
            [TypeVarchar::class,['MaxLen'=>2],'Test',true],
            [TypeVarchar::class,['MaxLen'=>2,'LengthExceedPolicy'=>'invalid'],'Test',false],
            
        ];
    }
    
    /**
     * @dataProvider convertProvider
     */
    public function testConvertToInput($type, $setters, $test_input, $expect)
    {
        $test = $this->getTestType($type, $setters);
        
        if ($expect == 'except') {
            $this->expectException(InvalidValueException::class);
        }
        $this->assertEquals($expect, $test->convertToInput($test_input));        
    }
    
    static public function convertProvider()
    {
        return [
            [TypeBoolean::class, [], 'Y', 1],
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
            
            [TypeVarchar::class,[],'Test','Test'],
            [TypeVarchar::class,['MaxLen'=>2],'Test','Te'],            
            [TypeVarchar::class,['MaxLen'=>2,'LengthExceedPolicy'=>'invalid'],'Test','except']
        ];
    }
}
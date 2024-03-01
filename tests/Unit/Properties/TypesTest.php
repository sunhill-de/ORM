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
use Sunhill\ORM\Tests\TestSupport\TestAbstractStorage;

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
     * @dataProvider readStorageProvider
     * @group read
     */
    public function testReadStorage($type, $setters, $test_value, $expect)
    {
        $test = $this->getTestType($type, $setters);
        $test->setName('test');
        
        $storage = new TestAbstractStorage();
        $storage->values['test'] = $test_value;
        $test->setStorage($storage);
        
        if (is_callable($expect)) {
            $this->assertTrue($expect($test->getValue()));
        } else {
            $this->assertEquals($expect, $test->getValue());
        }
    }
    
    static public function readStorageProvider()
    {
        return [
            [TypeBoolean::class, [], 1, 1],
            [TypeBoolean::class, [], 0, 0],
            
            [TypeDate::class, [], '2023-12-24', function($date) { return $date->format('Y-m-d') == '2023-12-24'; }],

            [TypeDateTime::class, [], '2023-12-24 12:13:14', function($date) { return $date->format('Y-m-d H:i:s') == '2023-12-24 12:13:14'; }],
            
            [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', 'TestA' ],
            
            [TypeFloat::class, [], 3.14, 3.14],            
            [TypeFloat::class, ['Precision'=>1], 3.14, 3.14],
            
            [TypeInteger::class, [], 3, 3],
            
            [TypeText::class, [], 'Lorem ipsum','Lorem ipsum'],
            
            [TypeTime::class, [], '11:12:13', function($date) { return $date->format('H:i:s') == '11:12:13'; }],

            [TypeVarchar::class, [], 'Lorem ipsum', 'Lorem ipsum'],
        ];    
    }
    
    /**
     * @dataProvider readStorageHumanProvider
     * @group readHuman
     */
    public function testReadStorageHuman($type, $setters, $test_value, $expect)
    {
        $test = $this->getTestType($type, $setters);
        $test->setName('test');
        
        $storage = new TestAbstractStorage();
        $storage->values['test'] = $test_value;
        $test->setStorage($storage);
        
        if (is_callable($expect)) {
            $this->assertTrue($expect($test->getHumanValue()));
        } else {
            $this->assertEquals($expect, $test->getHumanValue());
        }
    }
    
    static public function readStorageHumanProvider()
    {
        return [
            [TypeBoolean::class, [], 1, 'true'],
            [TypeBoolean::class, [], 0, 'false'],

            [TypeDate::class, [], '2023-01-02', '2.1.2023'],
            [TypeDate::class, [], '2023-01-02', '2.1.2023'],
            
            [TypeDateTime::class, [], '2023-01-02 11:12:13', '2.1.2023 11:12:13'],
            
            [TypeEnum::class, ['EnumValues'=>['TestA','TestB']], 'TestA', 'TestA' ],

            [TypeFloat::class, [], 3.14, 3.14],
            [TypeFloat::class, ['Precision'=>1], 3.14, 3.1],
            [TypeFloat::class, ['Precision'=>1], 3.15, 3.2],

            [TypeInteger::class, [], 3, 3],
            
            [TypeText::class, [], 'Lorem ipsum','Lorem ipsum'],

            [TypeTime::class, [], '11:12:13', '11:12:13'],

            [TypeVarchar::class, [], 'Lorem ipsum', 'Lorem ipsum'],
            
        ];
    }
    
    /**
     * @dataProvider writeStorageProvider
     * @group write
     */
    public function testWriteStorage($type, $setters, $test_input, $expect)
    {
        $test = $this->getTestType($type, $setters);
        $test->setName('test');
        
        $storage = new TestAbstractStorage();
        $test->setStorage($storage);
        if ($expect == 'except') {
            $this->expectException(InvalidValueException::class);
        }
       
        if (is_callable($test_input)) {
            $test->setValue($test_input());
        } else {
            $test->setValue($test_input);
        }
        $this->assertEquals($expect, $storage->values['test']);
    }
    
    static public function writeStorageProvider()
    {
        return [
/*            [TypeBoolean::class, [], 'Y', 1],
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
            [TypeDate::class, [], '01.02.2018', '2018-02-01'],
            [TypeDate::class, [], '2018-02-02', '2018-02-02'],
            [TypeDate::class, [], '1.2.2018', '2018-02-01'],
            [TypeDate::class, [], '2018-2-1', '2018-02-01'],
            [TypeDate::class, [], 1686778521.3, '2023-06-14'],
            [TypeDate::class, [], '2018-2', '2018-02-01'],
            [TypeDate::class, [], false, 'except'],
            [TypeDate::class, [], 'ABC', 'except'],
            [TypeDate::class, [], '', 'except'],
            [TypeDate::class, [], 1686778521, '2023-06-14'],
            
            [TypeDatetime::class, [], '2018-02-01 11:11:11', '2018-02-01 11:11:11'],
            [TypeDatetime::class, [], '1.2.2018 11:11:11', '2018-02-01 11:11:11'],
            [TypeDateTime::class, [], 1686778521, '2023-06-14 21:35:21'],
            
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
<?php

namespace Sunhill\ORM\Tests\Unit\Properties;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyArrayOfObjects;
use Sunhill\ORM\Properties\PropertyArrayOfStrings;
use Sunhill\ORM\Properties\PropertyEnum;
use Sunhill\ORM\Validators\ValidatorException;
use Sunhill\ORM\Tests\Objects\ts_testparent;
use Sunhill\ORM\Tests\Objects\ts_testchild;
use Sunhill\ORM\Facades\Classes;

class PropertyValidateTest extends TestCase
{
    
    public function setUp() : void {
        parent::setUp();
        Classes::flushClasses();
        Classes::registerClass(ts_testchild::class);
        Classes::registerClass(ts_testparent::class);
    }
    
    /**
     * A basic test example.
     *
     * @dataProvider ValidateProvider
     */
    public function testValidate($property,$testvalue,$exception,$expected)
    {
		$property_name = '\Sunhill\ORM\Properties\oo_property_'.$property;
    	$property_class = new $property_name(null);
    	$result = 0;
		try {
			$property_class->setValue($testvalue);
			$newvalue = $property_class->getValue();
		} catch (\Exception $e) {
			$result = 1;
		}
		$this->assertEquals($exception,$result);
		if (!$result) { $this->assertEquals($expected,$newvalue); }
    }
    
    public function ValidateProvider() {
    	return [['integer',1,false,1],
    			['integer','1',false,1],
    			['integer','A',true,null],
    			['integer',1.1,true,null],
    			['integer',null,false,null],
    			
    			['float',1,false,1],
    			['float','1.1',false,1.1],
    			['float','A',true,null],
    			['float',1.1,false,1.1],
    			['float',null,false,null],

    			['varchar',1,false,'1'],
    			['varchar','A',false,'A'],
    			['varchar',null,false,null],

    			['date','1.2.2018',false,'2018-02-01'],
    			['date','2018-2-1',false,'2018-02-01'],
    			['date',23.3,true,null],
    			['date','2018-2',false,'2018-02-00'],
    			['date','2.3.',false,'0000-03-02'],
    	        ['date','2018',false,'2018-00-00'],
    	        ['date',null,false,null],
    			
    			['time','20:33:43',false,'20:33:43'],
    			['time','20:33',false,'20:33:00'],
    			['time','2:3:4',false,'02:03:04'],
    			['time','2:3',false,'02:03:00'],
    			['time','13:',true,null],
    			['time','3.2',true,null],
    			['time','abc',true,null],
    			['time',null,false,null],
    			
    			['datetime','2018-01-01 01:01:01',false,'2018-01-01 01:01:01'],
    			['datetime','2018-1-1 1:1:1',false,'2018-01-01 01:01:01'],
    	        ['datetime','2018-01-01',true,null],
    	        ['datetime','2018-01 01:01:01',true,null],
    			['datetime','abc',true,null],
    			['datetime',1530791223,false,'2018-07-05 11:47:03'],
    			['datetime',null,false,null]
    	];
    }
    
    public function testObjectsPropertyPass() {
    	$test = new PropertyObject(null);
    	$test->setAllowedObjects(['testparent']);
    	$object = new ts_testparent();
    	$object->parentint = 22;
    	$test->setValue($object);
    	$this->assertEquals(22,$test->getValue()->parentint);
    }
    
    public function testObjectsPropertyPassWithChild() {
    	$test = new PropertyObject(null);
    	$test->setAllowedObjects(['testparent']);
    	$object = new ts_testchild();
    	$test->setValue($object);
    	$this->assertEquals($object,$test->getValue());
    }
    
    public function testObjectsFail() {
        $this->expectException(ValidatorException::class);
        $test = new PropertyObject(null);
    	$test->setAllowedObjects(['testchild']);
    	$object = new ts_testparent();
    	$test->setValue($object);
    }
    
    public function testArrayOfObjectsPropertyPass() {
    	$test = new PropertyArrayOfObjects(null);
    	$test->setAllowedObjects(['testparent']);
    	$object = new ts_testparent();
    	$object->parentint = 22;
    	$test->getValue()[] = $object;
    	$this->assertEquals(22,$test->getValue()[0]->parentint);
    }
    
    public function testArrayOfObjectsPropertyPassWithChild() {
    	$test = new PropertyArrayOfObjects(null);
    	$test->setAllowedObjects(['testparent']);
    	$object = new ts_testchild();
    	$object->parentint = 23;
    	$test->getValue()[] = $object;
    	$this->assertEquals(23,$test->getValue()[0]->parentint);
    }
    /**
     * @group reindex
     */
    public function testArrayOfObjectReindex() {
        $test = new PropertyArrayOfObjects(null);
        $test->setAllowedObjects(['testparent']);
        $object1 = new ts_testchild();
        $object1->parentint = 23;
        $test->getValue()[] = $object1;
        $object2 = new ts_testchild();
        $object2->parentint = 34;
        $test->getValue()[] = $object2;
        unset($test->getValue()[0]);
        $this->assertEquals(34,$test->getValue()[0]->parentint);
        
    }
    
    public function testArrayOfObjectsFail() {
    	$this->expectException(ValidatorException::class);
        $test = new PropertyArrayOfObjects(null);
    	$test->setAllowedObjects(['testchild']);
    	$object = new ts_testparent();
    	$test->getValue()[] = $object;
    }
    
    public function testArrayOfString() {
    	$test = new PropertyArrayOfStrings(null);
    	$test->getValue()[] = 'ABC';
    	$test->getValue()[] = 'DEF';
    	$hilf = $test->getValue();
    	$this->assertEquals('DEF',$test->getValue()[1]);
    }
    
    /**
     * @dataProvider EnumProvider
     * @param unknown $test
     * @param unknown $raise_exception
     */
    public function testEnum($test_value,$exception) {
    	$test = new PropertyEnum(null);
    	$test->setEnumValues(['A','B']);
    	$result = 0;
    	try {
    		$test->setValue($test_value);
    	} catch (\Exception $e) {
    		$result = 1;
    	}
    	$this->assertEquals($exception,$result);
    }
    
    public function EnumProvider() {
    	return [['A',0],
    			['B',0],
    			['C',1],
    			[1,1]
    	];
    }
}

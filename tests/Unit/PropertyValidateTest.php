<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Objects\oo_property_object;

class PropertyValidateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @dataProvider ValidateProvider
     */
    public function testValidate($property,$testvalue,$exception,$expected)
    {
		$property_name = '\Sunhill\Objects\oo_property_'.$property;
    	$property_class = new $property_name(null);
    	$result = 0;
		try {
			$property_class->set_value($testvalue);
			$newvalue = $property_class->get_value();
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
    			['date','2018-2',true,null],
    			['date','2.3.',true,null],
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
    			['datetime','abc',true,null],
    			['datetime',1530791223,false,'2018-07-05 11:47:03'],
    			['datetime',null,false,null]
    	];
    }
    
    public function testObjectsPropertyPass() {
    	$test = new oo_property_object(null);
    	$test->set_allowed_objects(['\\Sunhill\\Test\\ts_testparent']);
    	$object = new \Sunhill\Test\ts_testparent();
    	$object->parentint = 22;
    	$test->set_value($object);
    	$this->assertEquals(22,$test->get_value()->parentint);
    }
    
    public function testObjectsPropertyPassWithChild() {
    	$test = new oo_property_object(null);
    	$test->set_allowed_objects(['\\Sunhill\\Test\\ts_testparent']);
    	$object = new \Sunhill\Test\ts_testchild();
    	$test->set_value($object);
    	$this->assertEquals($object,$test->get_value());
    }
    
    /**
     * @expectedException \Sunhill\Objects\InvalidValueException
     */
    public function testObjectsFail() {
    	$test = new oo_property_object(null);
    	$test->set_allowed_objects(['\\Sunhill\\Test\\ts_testchild']);
    	$object = new \Sunhill\Test\ts_testparent();
    	$test->set_value($object);
    }
    
    public function testArrayOfObjectsPropertyPass() {
    	$test = new \Sunhill\Objects\oo_property_array_of_objects(null);
    	$test->set_allowed_objects(['\\Sunhill\\Test\\ts_testparent']);
    	$object = new \Sunhill\Test\ts_testparent();
    	$object->parentint = 22;
    	$test->get_value()[] = $object;
    	$this->assertEquals(22,$test->get_value()[0]->parentint);
    }
    
    public function testArrayOfObjectsPropertyPassWithChild() {
    	$test = new \Sunhill\Objects\oo_property_array_of_objects(null);
    	$test->set_allowed_objects(['\\Sunhill\\Test\\ts_testparent']);
    	$object = new \Sunhill\Test\ts_testchild();
    	$object->parentint = 23;
    	$test->get_value()[] = $object;
    	$this->assertEquals(23,$test->get_value()[0]->parentint);
    }
    
    /**
     * @expectedException \Sunhill\Objects\InvalidValueException
     */
    public function testArrayOfObjectsFail() {
    	$test = new \Sunhill\Objects\oo_property_array_of_objects(null);
    	$test->set_allowed_objects(['\\Sunhill\\Test\\ts_testchild']);
    	$object = new \Sunhill\Test\ts_testparent();
    	$test->get_value()[] = $object;
    }
    
    public function testArrayOfString() {
    	$test = new \Sunhill\Objects\oo_property_array_of_strings(null);
    	$test->get_value()[] = 'ABC';
    	$test->get_value()[] = 'DEF';
    	$hilf = $test->get_value();
    	$this->assertEquals('DEF',$test->get_value()[1]);
    }
    
    /**
     * @dataProvider EnumProvider
     * @param unknown $test
     * @param unknown $raise_exception
     */
    public function testEnum($test_value,$exception) {
    	$test = new \Sunhill\Objects\oo_property_enum(null);
    	$test->set_enum_values(['A','B']);
    	$result = 0;
    	try {
    		$test->set_value($test_value);
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
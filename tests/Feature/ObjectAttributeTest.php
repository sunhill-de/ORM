<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class ObjectAttributeTest extends ObjectCommon
{
    
    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_write_scenario();
    }
    
    /**
     * @dataProvider AttributeProvider
     * @param unknown $attributename
     * @param unknown $init
     * @param unknown $change
     */
    public function testSimpleAttribute($attributename,$init,$change,$exception) {
        $this->prepare_tables();
        try {
            $test = new \Sunhill\Test\ts_dummy();
            $test->$attributename = $init;
            $this->assertEquals($init,$test->$attributename);
            $test->dummyint = 123;
            $test->commit();
            
            \Sunhill\Objects\oo_object::flush_cache();
            $read = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
            $this->assertEquals($init,$read->$attributename);
            $read->$attributename = $change;
            $read->commit();
            
            \Sunhill\Objects\oo_object::flush_cache();
            $reread = \Sunhill\Objects\oo_object::load_object_of($test->get_id());
            $this->assertEquals($change,$reread->$attributename);
        } catch (\Exception $e) {
            if ($exception) {
                $this->assertTrue(true);
            } else {
               throw $e;
            }
        }
    }
    
    public function AttributeProvider() {
        return [
            ['int_attribute',1,2,false]
        ];
    }
    
    /**
     * @expectedException \Sunhill\Properties\AttributeException
     */
    public function testInvalidAttribute() {
        $this->prepare_tables();
        $test = new \Sunhill\Test\ts_dummy();
        $test->attribute1 = 2;
    }
    
}

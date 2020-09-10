<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Test\ts_dummy;

class ObjectAttributeTest extends DBTestCase
{
    
    /**
     * @dataProvider AttributeProvider
     * @param unknown $attributename
     * @param unknown $init
     * @param unknown $change
     */
    public function testSimpleAttribute($attributename,$init,$change,$exception) {
        try {
            $test = new ts_dummy();
            $test->$attributename = $init;
            $this->assertEquals($init,$test->$attributename);
            $test->dummyint = 123;
            $test->commit();
            
            oo_object::flush_cache();
            $read = oo_object::load_object_of($test->get_id());
            $this->assertEquals($init,$read->$attributename);
            $read->$attributename = $change;
            $read->commit();
            
            oo_object::flush_cache();
            $reread = oo_object::load_object_of($test->get_id());
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
            ['int_attribute',1,2,false],
            ['char_attribute','ABC','DEF',false],
            ['float_attribute',1.3,2.5,false],
            ['text_attribute','Lorem ipsum','lari fari',false]
        ];
    }
    
    public function testInvalidAttribute() {
        $this->expectException(\Sunhill\ORM\Properties\AttributeException::class);
        $test = new ts_dummy();
        $test->attribute1 = 2;
    }
    
}

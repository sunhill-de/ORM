<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class ObjectAttributeTest extends ObjectCommon
{
    protected function prepare_attributes() {
        DB::statement("truncate attributes");
        DB::statement("truncate attributevalues");
        $entry = new \App\attribute();
        $entry->id = 1;
        $entry->name = 'int_attribute';
        $entry->type = 'int';
        $entry->allowedobjects = "\\Sunhill\\Test\\ts_dummy";
        $entry->save();
        $entry = new \App\attribute();
        $entry->id = 2;
        $entry->name = 'attribute1';
        $entry->type = 'int';
        $entry->allowedobjects = "\\Sunhill\\Test\\ts_testparent";
        $entry->save();        
    }

    /**
     * @dataProvider AttributeProvider
     * @param unknown $attributename
     * @param unknown $init
     * @param unknown $change
     */
    public function testSimpleAttribute($attributename,$init,$change,$exception) {
        $this->prepare_attributes();
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
        $this->prepare_attributes();
        $test = new \Sunhill\Test\ts_dummy();
        $test->attribute1 = 2;
    }
    
}

<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Properties\AttributeException;

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
            
            Objects::flushCache();
            $read = Objects::load($test->get_id());
            $this->assertEquals($init,$read->$attributename);
            $read->$attributename = $change;
            $read->commit();
            
            Objects::flushCache();
            $reread = Objects::load($test->get_id());
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
        $this->expectException(AttributeException::class);
        $test = new ts_dummy();
        $test->attribute1 = 2;
    }
    
}

<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Properties\Exceptions\AttributeException;

class ObjectAttributeTest extends DatabaseTestCase
{
    
    /**
     * @dataProvider AttributeProvider
     * @param unknown $attributename
     * @param unknown $init
     * @param unknown $change
     */
    public function testSimpleAttribute($attributename,$init,$change,$exception) {
        try {
            $test = new Dummy();
            $test->$attributename = $init;
            $this->assertEquals($init,$test->$attributename);
            $test->dummyint = 123;
            $test->commit();
            
            Objects::flushCache();
            $read = Objects::load($test->getID());
            $this->assertEquals($init,$read->$attributename);
            $read->$attributename = $change;
            $read->commit();
            
            Objects::flushCache();
            $reread = Objects::load($test->getID());
            $this->assertEquals($change,$reread->$attributename);
        } catch (\Exception $e) {
            if ($exception) {
                $this->assertTrue(true);
            } else {
               throw $e;
            }
        }
    }
    
    public static function AttributeProvider() {
        return [
            ['int_attribute',1,2,false],
            ['char_attribute','ABC','DEF',false],
            ['float_attribute',1.3,2.5,false],
            ['text_attribute','Lorem ipsum','lari fari',false]
        ];
    }
    
    public function testInvalidAttribute() {
        $this->expectException(AttributeException::class);
        $test = new Dummy();
        $test->attribute1 = 2;
    }
    
}

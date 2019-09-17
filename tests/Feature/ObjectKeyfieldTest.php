<?php

namespace Tests\Feature;

use Tests\searchtestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class keyfieldA extends \Sunhill\Objects\oo_object {
   
    public static $table_name = 'keyfieldA';
    
    protected static $has_keyfield = true;
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('Aint')->searchable();
    }
    
    public function get_keyfield() {
        return "Key:".$this->Aint;
    }
}

class ObjectKeyfieldTest extends ObjectCommon
{
    
    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_table('keyfieldA',['Aint int']);
        $this->insert_into('objects',['id','classname','created_at','updated_at'],
            [
                [1,"\\Tests\\Feature\\keyfieldA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('keyfieldA',['id','Aint'],
            [
                [1,111]
            ]);
        $this->insert_into('caching',['id','object_id','fieldname','value'],
            [
                [1,1,'keyfield','Key:111'], 
            ]);
    }
    
    public function testSearchForKeyfield() {
        $this->prepare_tables();
        $result = keyfieldA::search()->where('keyfield','=','Key:111')->get();
        $this->assertEquals(1,$result);
    }
 
    public function testWriteKeyfield() {
        $this->prepare_tables();
        $object = new keyfieldA();
        $object->Aint = 234;
        $object->commit();
        $result = keyfieldA::search()->where('keyfield','=','Key:234')->get();
        $this->assertFalse(is_null($result));
    }
    
    public function testDuplicateKeyfield() {
        $this->prepare_tables();
        $object = new keyfieldA();
        $object->Aint = 234;
        $object->commit();
        $object2 = new keyfieldA();
        $object2->Aint = 234;
        $object2->commit();
        $this->assertEquals('Key:2341',$object2->keyfield);
    }
}

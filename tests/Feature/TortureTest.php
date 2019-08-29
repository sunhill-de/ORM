<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;
use Sunhill\Objects\oo_object;
use Sunhill;

define('TORTURE_DEPTH',5);
define('TORTURE_LETTER','F');

class TortureObject extends \Sunhill\Objects\oo_object {
 
    public static $table_name = 'tortures';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::object('parent')->set_allowed_objects(['\\Tests\\Feature\\TortureObject'])->set_default('null');
        self::varchar('keychar');
        self::calculated('completekey')->searchable();
    }
    
    public function calculate_completekey() {
        $parent = $this->parent;
        if (is_null($parent)) {
            return $this->keychar;
        } else {
            return $this->parent->completekey.$this->keychar;
        }
    }
    
}

class ObjectCalculatedTest extends ObjectCommon
{
    
    static protected $times = [];
    
    private function microtime_float() {
        list($usec,$sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
    
    protected function setup_torturetables() {
        DB::statement("drop table if exists tortures");
        DB::statement("create table tortures (id int primary key,keychar varchar(2))");        
    }
    
    protected function create_objects($depth,$parent) {
        if (!$depth) {
            return;
        }
        for ($i='A';$i<=TORTURE_LETTER;$i++) {
            $object = new TortureObject();
            $object->keychar = $i;
            $object->parent = $parent;
            $object->commit();
            $this->create_objects($depth-1,$object);
        }
    }
    
    public function testCreateObjects() {
        $this->setup_torturetables();
        $time = $this->microtime_float();
        $this->create_objects(TORTURE_DEPTH,null);
        self::$times['init'] = $this->microtime_float()-$time;
        $this->assertTrue(true);
    }
    
    protected function load_objects($depth,$parent) {
        if (!$depth) {
            return;
        }
        for ($i='A';$i<=TORTURE_LETTER;$i++) {
            $object_id = TortureObject::search()->where('completekey','=',$parent.$i)->first();
            var_dump($object_id);
            $object = \Sunhill\Objects\oo_object::load_object_of($object_id);
            $this->load_objects($depth-1,$object->completekey);
        }
    }
    
    /**
     * @depends testCreateObjects
     */
    public function testLoadObjects() {
        $time = $this->microtime_float();
        $this->load_objects(TORTURE_DEPTH,'');
        self::$times['load'] = $this->microtime_float()-$time;
        $this->assertTrue(true);
    }
    
    protected function modify_objects($depth,$parent) {
        
    }
    
    /**
     * @depends testLoadObjects
     */
    public function testModifyObjects() {
        $time = $this->microtime_float();
        $this->modify_objects(TORTURE_DEPTH,'');
        self::$times['modify'] = $this->microtime_float()-$time;
        $this->assertTrue(true);        
    }
    
    protected function delete_objects($depth,$parent) {
        
    }
    
    /**
     * @depends testModifybjects
     */
    public function testDeleteObjects() {
        $time = $this->microtime_float();
        $this->delete_objects(TORTURE_DEPTH,'');
        self::$times['delete'] = $this->microtime_float()-$time;
        $this->assertTrue(true);
    }
    
    /**
     * @depends testDeleteObjects
     */
    public function testFinal() {
        $timestr = self::$times['init'].'|'.self::$times['load'].'|'.self::$times['modify'].'|'.self::$times['delete'];
        exec("echo $timestr >> times.log");
    }
}
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
        self::object('parent')->set_allowed_objects(['\\Tests\\Feature\\TortureObject'])->set_default(null);
        self::varchar('keychar');
        self::varchar('payload')->set_default('A');
        self::calculated('completekey')->searchable();
    }
    
    public function calculate_completekey() {
        $parent = $this->parent;
        if (is_null($parent) || $parent == 'null') {
            return $this->keychar;
        } else {
            return $this->parent->completekey.$this->keychar;
        }
    }
    
}

class ObjectCalculatedTest extends ObjectCommon
{
    
    static protected $times = [];
    
    static protected $ids = [];
    
    private function microtime_float() {
        list($usec,$sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }
    
    protected function setup_torturetables() {
        DB::statement("drop table if exists tortures");
        DB::statement("create table tortures (id int primary key,keychar varchar(2),payload varchar(2))");        
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
            self::$ids[] = $object->get_id();
            $this->create_objects($depth-1,$object);
        }
    }
    
    /**
     * @large
     */
    public function testTorture() {
        $this->setup_torturetables();
        $time = $this->microtime_float();
        $this->create_objects(TORTURE_DEPTH,null);
        self::$times['init'] = $this->microtime_float()-$time;
        
        \Sunhill\Objects\oo_object::flush_cache();
        $time = $this->microtime_float();
        foreach (self::$ids as $id) {
            $object = \Sunhill\Objects\oo_object::load_object_of($id);
            if (is_null($object)) {
                $this->fail();
            }
            if ($object->payload !== 'A') {
                $this->fail();
            }
        }
        self::$times['load'] = $this->microtime_float()-$time;
        
        \Sunhill\Objects\oo_object::flush_cache();
        $time = $this->microtime_float();
        foreach (self::$ids as $id) {
            $object = \Sunhill\Objects\oo_object::load_object_of($id);
            if (is_null($object) || $object == 'null') {
                $this->fail();
            }
            $object->payload = 'B';
            $object->commit();
        }
        self::$times['modify'] = $this->microtime_float()-$time;
        
        \Sunhill\Objects\oo_object::flush_cache();
        $time = $this->microtime_float();
        foreach (self::$ids as $id) {
            $object = \Sunhill\Objects\oo_object::load_object_of($id);
            if (is_null($object)) {
                $this->fail();
            }
            $object->delete();
        }
        self::$times['delete'] = $this->microtime_float()-$time;
        
        $timestr = self::$times['init'].'|'.self::$times['load'].'|'.self::$times['modify'].'|'.self::$times['delete'];
        exec("echo '$timestr' >> storage/logs/times.log");
        
        $this->assertTrue(true);
    }
    
}
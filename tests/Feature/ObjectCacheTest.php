<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class ObjectCacheTest extends ObjectCommon
{

    protected function fill_test_database() {
        $classname = '\\Sunhill\\Test\\ts_dummy';
        DB::table('objects')->insert(['id'=>1,'classname'=>$classname]);
        DB::table('dummies')->insert(['id'=>1,'dummyint'=>1]);
    }
    
    public function testGetClassOf() {
        $this->fill_test_database();
        $this->assertEquals('\\Sunhill\\Test\\ts_dummy',\Sunhill\Objects\oo_object::get_class_name_of(1));    
    }
    
    public function testIsNotCached() {
        $this->fill_test_database();
        $this->assertFalse(\Sunhill\Objects\oo_object::is_cached(1));
    }
    
    /**
     * @depends testIsNotCached
     */
    public function testIsCached() {
        $this->fill_test_database();
        $first  = \Sunhill\Objects\oo_object::load_object_of(1);
        if (!$first) {
            $this->fail('Objekt nicht gefunden.');
        }
        $this->assertTrue(\Sunhill\Objects\oo_object::is_cached(1));        
    }
    
    /**
     * @depends testIsCached
     */
    public function testFlushCache() {
        $this->fill_test_database();
        \Sunhill\Objects\oo_object::flush_cache();
        $this->assertFalse(\Sunhill\Objects\oo_object::is_cached(1));        
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadFromCache() {
        $this->fill_test_database();
        $first  = \Sunhill\Objects\oo_object::load_object_of(1);
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testLoadFromCache
     */
    public function testLoadMethod() {
        $this->fill_test_database();
        \Sunhill\Objects\oo_object::flush_cache();
        $first  = \Sunhill\Objects\oo_object::load_object_of(1);
        $second = new \Sunhill\Test\ts_dummy();
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadMethodInsertCache() {
        $this->fill_test_database();
        \Sunhill\Objects\oo_object::flush_cache();
        $second = new \Sunhill\Test\ts_dummy();
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $this->assertTrue(\Sunhill\Objects\oo_object::is_cached(1));
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange() {
        $this->fill_test_database();
        \Sunhill\Objects\oo_object::flush_cache();
        $first = new \Sunhill\Test\ts_dummy();
        $first = \Sunhill\Objects\oo_object::load_object_of(1);
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange2() {
        $this->fill_test_database();
        \Sunhill\Objects\oo_object::flush_cache();
        $first = new \Sunhill\Test\ts_dummy();
        $first = \Sunhill\Objects\oo_object::load_object_of(1);
        $second = new \Sunhill\Test\ts_dummy();
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
   
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ObjectCacheTest extends ObjectCommon
{

    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->insert_into('objects', ['id','classname'], [[1,"\\Sunhill\\Test\\ts_dummy"]]);
        $this->insert_into('dummies', ['id','dummyint'],[[1,1]]);
    }
    
    public function testGetClassOf() {
        $this->prepare_tables();
        $this->assertEquals('\\Sunhill\\Test\\ts_dummy',\Sunhill\Objects\oo_object::get_class_name_of(1));    
    }
    
    public function testIsNotCached() {
        $this->prepare_tables();
        $this->assertFalse(\Sunhill\Objects\oo_object::is_cached(1));
    }
    
    /**
     * @depends testIsNotCached
     */
    public function testIsCached() {
        $this->prepare_tables();
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
        $this->prepare_tables();
        \Sunhill\Objects\oo_object::flush_cache();
        $this->assertFalse(\Sunhill\Objects\oo_object::is_cached(1));        
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadFromCache() {
        $this->prepare_tables();
        $first  = \Sunhill\Objects\oo_object::load_object_of(1);
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testLoadFromCache
     */
    public function testLoadMethod() {
        $this->prepare_tables();
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
        $this->prepare_tables();
        \Sunhill\Objects\oo_object::flush_cache();
        $second = new \Sunhill\Test\ts_dummy();
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $this->assertTrue(\Sunhill\Objects\oo_object::is_cached(1));
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange() {
        $this->prepare_tables();
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
        $this->prepare_tables();
        \Sunhill\Objects\oo_object::flush_cache();
        $first = new \Sunhill\Test\ts_dummy();
        $first = \Sunhill\Objects\oo_object::load_object_of(1);
        $second = new \Sunhill\Test\ts_dummy();
        $second = \Sunhill\Objects\oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
   
}

<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;

class ObjectCacheTest extends DBTestCase
{
    
    public function testGetClassOf() {
        $this->assertEquals('dummy',oo_object::get_class_name_of(1));    
    }
    
    public function testIsNotCached() {
        $this->assertFalse(oo_object::is_cached(1));
    }
    
    /**
     * @depends testIsNotCached
     */
    public function testIsCached() {
        $first  = oo_object::load_object_of(1);
        if (!$first) {
            $this->fail('Objekt nicht gefunden.');
        }
        $this->assertTrue(oo_object::is_cached(1));        
    }
    
    /**
     * @depends testIsCached
     */
    public function testFlushCache() {
        oo_object::flush_cache();
        $this->assertFalse(oo_object::is_cached(1));        
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadFromCache() {
        $first  = oo_object::load_object_of(1);
        $second = oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testLoadFromCache
     */
    public function testLoadMethod() {
        oo_object::flush_cache();
        $first  = oo_object::load_object_of(1);
        $second = new \Sunhill\ORM\Test\ts_dummy();
        $second = oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadMethodInsertCache() {
        oo_object::flush_cache();
        $second = new \Sunhill\ORM\Test\ts_dummy();
        $second = oo_object::load_object_of(1);
        $this->assertTrue(oo_object::is_cached(1));
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange() {
        oo_object::flush_cache();
        $first = new \Sunhill\ORM\Test\ts_dummy();
        $first = oo_object::load_object_of(1);
        $second = oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange2() {
        oo_object::flush_cache();
        $first = new \Sunhill\ORM\Test\ts_dummy();
        $first = oo_object::load_object_of(1);
        $second = new \Sunhill\ORM\Test\ts_dummy();
        $second = oo_object::load_object_of(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
   
}

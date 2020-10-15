<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Facades\Objects;

class ObjectCacheTest extends DBTestCase
{
    
    public function testGetClassOf() {
        $this->assertEquals('dummy',Objects::get_class_name_of(1));    
    }
    
    public function testIsNotCached() {
        $this->assertFalse(Objects::is_cached(1));
    }
    
    /**
     * @depends testIsNotCached
     */
    public function testIsCached() {
        $first  = Objects::load(1);
        if (!$first) {
            $this->fail('Objekt nicht gefunden.');
        }
        $this->assertTrue(Objects::is_cached(1));        
    }
    
    /**
     * @depends testIsCached
     */
    public function testFlushCache() {
        Objects::flush_cache();
        $this->assertFalse(Objects::is_cached(1));        
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadFromCache() {
        $first  = Objects::load(1);
        $second = Objects::load(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testLoadFromCache
     */
    public function testLoadMethod() {
        Objects::flush_cache();
        $first  = Objects::load(1);
        $second = new \Sunhill\ORM\Test\ts_dummy();
        $second = Objects::load(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadMethodInsertCache() {
        Objects::flush_cache();
        $second = new \Sunhill\ORM\Test\ts_dummy();
        $second = Objects::load(1);
        $this->assertTrue(Objects::is_cached(1));
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange() {
        Objects::flush_cache();
        $first = new \Sunhill\ORM\Test\ts_dummy();
        $first = Objects::load(1);
        $second = Objects::load(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange2() {
        Objects::flush_cache();
        $first = new \Sunhill\ORM\Test\ts_dummy();
        $first = Objects::load(1);
        $second = new \Sunhill\ORM\Test\ts_dummy();
        $second = Objects::load(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
   
}

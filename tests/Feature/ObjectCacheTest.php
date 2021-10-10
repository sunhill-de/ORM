<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Tests\Objects\ts_dummy;

class ObjectCacheTest extends DBTestCase
{
    
    public function testGetClassOf() {
        $this->assertEquals('dummy',Objects::getClassNameOf(1));    
    }
    
    public function testIsNotCached() {
        $this->assertFalse(Objects::isCached(1));
    }
    
    /**
     * @depends testIsNotCached
     */
    public function testIsCached() {
        $first  = Objects::load(1);
        if (!$first) {
            $this->fail('Objekt nicht gefunden.');
        }
        $this->assertTrue(Objects::isCached(1));        
    }
    
    /**
     * @depends testIsCached
     */
    public function testFlushCache() {
        Objects::flushCache();
        $this->assertFalse(Objects::isCached(1));        
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
        Objects::flushCache();
        $first  = Objects::load(1);
        $second = new ts_dummy();
        $second = Objects::load(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
    
    /**
     * @depends testFlushCache
     */
    public function testLoadMethodInsertCache() {
        Objects::flushCache();
        $second = new ts_dummy();
        $second = Objects::load(1);
        $this->assertTrue(Objects::isCached(1));
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange() {
        Objects::flushCache();
        $first = new ts_dummy();
        $first = Objects::load(1);
        $second = Objects::load(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }

    /**
     * @depends testFlushCache
     */
    public function testLoadMethodChange2() {
        Objects::flushCache();
        $first = new ts_dummy();
        $first = Objects::load(1);
        $second = new ts_dummy();
        $second = Objects::load(1);
        $second->dummyint = 2;
        $this->assertEquals($first->dummyint,$second->dummyint);
    }
   
}

<?php

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Managers\class_manager;

define('CLASS_COUNT',8);

class ManagerClassesTest extends DBTestCase
{
    public function testFlushCache() {
        $test = new class_manager();
        $test->flush_cache();
        $this->assertFalse(file_exists(base_path('bootstrap/cache/sunhill_classes.php')));
        return $test;
    }
    
    /**
     * @depends testFlushCache
     */
    public function testCreateCache($test) {
        $test->create_cache(dirname(__FILE__).'/../objects');
        $this->assertTrue(file_exists(base_path('bootstrap/cache/sunhill_classes.php')));
        return $test;
    }
    
    /**
     * @depends testFlushCache
     */
    public function testNumberOfClasses($test) {
        $this->assertEquals(CLASS_COUNT,$test->get_class_count());
        return $test;
    }
    
    
}

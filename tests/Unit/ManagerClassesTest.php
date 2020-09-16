<?php

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Managers\class_manager;
use Sunhill\ORM\Fascades\Classes;

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
    
    /**
     * @depends testFlushCache
     */
    public function testNumberOfClassesViaApp($test) {
        $manager = app('\Sunhill\ORM\Managers\class_manager');
        $this->assertEquals(CLASS_COUNT,$manager->get_class_count());
        return $test;
    }
    
    /**
     * @depends testFlushCache
     */
    public function testNumberOfClassesViaAlias($test) {
        $manager = app('classes');
        $this->assertEquals(CLASS_COUNT,$manager->get_class_count());
        return $test;
    }
    
    /**
     * @depends testFlushCache
     */
    public function testNumberOfClassesViaFascade($test) {
        $this->assertEquals(CLASS_COUNT,Classes::get_class_count());
        return $test;
    }
    
    
}

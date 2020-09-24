<?php

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Managers\class_manager;
use Sunhill\ORM\Facades\Classes;
use \Sunhill\ORM\SunhillException;

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
        $this->assertTrue(CLASS_COUNT<=($count=$test->get_class_count()));
        return $count;
    }
    
    /**
     * @depends testNumberOfClasses
     */
    public function testNumberOfClassesViaApp($count) {
        $manager = app('\Sunhill\ORM\Managers\class_manager');
        $this->assertEquals($count,$manager->get_class_count());
        return $count;
    }
    
    /**
     * @depends testNumberOfClasses
     */
    public function testNumberOfClassesViaAlias($count) {
        $manager = app('classes');
        $this->assertEquals($count,$manager->get_class_count());
        return $count;
    }
    
    /**
     * @depends testNumberOfClasses
     */
    public function testNumberOfClassesViaFascade($count) {
        $this->assertEquals($count,$count = Classes::get_class_count());
        return $count;
    }
    
    public function testSearchClassNoField() {
        $this->assertEquals('dummies',Classes::get_class('dummy')->table);
    }
    
    public function testSearchClassField() {
        $this->assertEquals('dummies',Classes::get_class('dummy','table'));
    }
    
    public function testSearchClassNotExists() {
        $this->expectException(SunhillException::class);
        Classes::get_class('notexistsing');
    }
    
    public function testSearchClassNotExistingField() {
        $this->expectException(SunhillException::class);
        Classes::get_class('dummy','nonexisting');
    }

    /**
     * @dataProvider SearchClassProvider
     * @param unknown $search
     */
    public function testSearchClassViaName($expect,$search) {
        $this->assertEquals($expect,Classes::search_class($search));
    }
    
    public function SearchClassProvider() {
        return [
            ['dummy','dummy'],
            ['dummy','\\Sunhill\\ORM\\Test\\ts_dummy'],
            ['dummy','Sunhill\\ORM\\Test\\ts_dummy'],
            [null,'notexisting'],
            [null,'\\Sunhill\\ORM\\Test\\nonexisting'],
            [null,'Sunhill\\ORM\\Test\\nonexisting'],
        ];
    }
/**    
    public function testSearchClassWithTranslation() {
        $this->assertEquals('dummies',Classes::get_class('dummy','name_p'));
    }
*/    
}

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
    
    /**
     * @dataProvider GetClassProvider
     * @param unknown $class
     * @param unknown $subfield
     * @param unknown $field
     * @param unknown $expect
     */
    public function testGetClass($class,$subfield,$field,$expect) {
        if ($expect == 'except') {
            try {
                $this->get_field(Classes::get_class($class,$field),$subfield);
            } catch (\Exception $e) {
                $this->assertTrue(true);
                return;
            }
            $this->fail("Expected exception not raised");
        } else {
            $this->assertEquals($expect,$this->get_field(Classes::get_class($class,$field),$subfield));
        }
    }
    
    public function GetClassProvider() {
        return [
            ['dummy','table',null,'dummies'],       // Get Field indirect
            ['dummy',null,'table','dummies'],       // Get Field direct
            ['notexisting',null,null,'except'],     // Class not existing
            ['dummy',null,'notexisting','except'],  // Field not exported
            [-1,null,'table','except'],             // Invalid Index
            [1000,null,'table','except'],           // Invalid Index
            [0,null,'table','dummies'],             // Get table by index direct
            [0,'table',null,'dummies'],             // Get table by index indirect
            [1,null,'table','objectunits'],         // Get table by index direct
            [1,'table',null,'objectunits'],         // Get table by index indirect
        ];    
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

<?php

namespace Sunhill\ORM\Tests\Unit;

use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Managers\class_manager;
use Sunhill\ORM\Facades\Classes;
use \Sunhill\ORM\SunhillException;

define('CLASS_COUNT',8);

class ManagerClassesTest extends DBTestCase
{
   
    protected function checkArrays($expect,$test) {
        foreach ($expect as $key => $value) {
            if (!array_key_exists($key, $test)) {
                return false;
            }
            if (is_array($value)) {
                if (!$this->checkArrays($value,$test[$key])) {
                    return false;
                }
            }
        }
        return true;
    }
    
    protected function assertArrayContains($expect,$test) {
        if (!$this->checkArrays($expect,$test)) {
            $this->fail("The expected array is not contained in the passed one");
            return;
        }
        $this->assertTrue(true);
    }
    
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
        ];    
    }
    
    public function testDummyTable() {
        $classes = Classes::get_all_classes();
        $i=0;
        foreach ($classes as $class=>$info) {
            if ($class === 'dummy') {
                $dummyid = $i;
            }
            $i++;
        }
        $this->assertEquals('dummies',Classes::get_class($dummyid,'table'));        
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
     * @dataProvider ClassParentProvider
     * @param unknown $test_class
     * @param unknown $expect
     */
    public function testClassParent($test_class,$expect) {
        $this->assertEquals($expect,Classes::get_parent_of_class($test_class));
    }
    
    public function ClassParentProvider() {
        return [
            ['dummy','object'],
            ['testparent','object'],
            ['testchild','testparent']
        ];
    }
    
    /**
     * @dataProvider GetChildrenOfClassProvider
     * @param unknown $test_class
     * @param unknown $expect
     */
    public function testGetChildrenOfClass($test_class,$level,$expect) {
        $this->assertEquals($expect,Classes::get_children_of_class($test_class,$level));    
    }
    
    public function GetChildrenOfClassProvider() {
        return [
                ['referenceonly',-1,[]],
                ['secondlevelchild',-1,['thirdlevelchild'=>[]]],
                ['testparent',-1,['passthru'=>['secondlevelchild'=>['thirdlevelchild'=>[]]],'testchild'=>[]]],
                ['testparent',1,['passthru'=>[],'testchild'=>[]]],
       ];
    }
    
    /**
     * @dataProvider GetClassTreeProvider
     */
    public function testGetClassTree($test_class,$expect) {
        if (is_null($test_class)) {
            $this->assertArrayContains($expect,Classes::get_class_tree());
        } else {
            $this->assertArrayContains($expect,Classes::get_class_tree($test_class));
        }
    }
    
    public function GetClassTreeProvider() {
        return [
            [null,
                ['object'=>
                    [
                        'dummy'=>[],
                        'objectunit'=>[],
                        'referenceonly'=>[],
                        'testparent'=>[
                            'passthru'=>[
                                'secondlevelchild'=>[
                                    'thirdlevelchild'=>[]
                                ]
                            ],
                            'testchild'=>[]
                        ]
                    ]    
                ]                
            ],
            ['testparent',
                        ['testparent'=>[
                            'passthru'=>[
                                'secondlevelchild'=>[
                                    'thirdlevelchild'=>[]
                                ]
                            ],
                            'testchild'=>[]
                        ]]
            ],
            ['dummy',['dummy'=>[]]],
            
        ];
    }

    public function testGetClassProperties() {
        $this->assertEquals('dummyint',Classes::get_properties_of_class('dummy')->dummyint->name);
    }
    
    public function testGetClassProperty() {
        $this->assertEquals('dummyint',Classes::get_property_of_class('dummy','dummyint')->name);
    }

    public function testGetClassTreeRoot() {
        $this->assertArrayContains(
            ['object'=>[
                'dummy'=>[],
                'objectunit'=>[],
                'referenceonly'=>[],
                'testparent'=>[
                    'testchild'=>[],
                    'passthru'=>[
                        'secondlevelchild'=>[
                            'thirdlevelchild'=>[]
                        ]
                    ]
                ]
            ]],Classes::get_class_tree());    
    }
    
    public function testGetClassTreeClass() {
        $this->assertEquals([
            'testparent'=>[
                'testchild'=>[],
                'passthru'=>[
                    'secondlevelchild'=>[
                        'thirdlevelchild'=>[]
                    ]
                ]
            ]
       ],Classes::get_class_tree('testparent'));
    }
    
/**    
    public function testSearchClassWithTranslation() {
        $this->assertEquals('dummies',Classes::get_class('dummy','name_p'));
    }
*/    
}

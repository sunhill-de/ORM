<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Managers\ClassManager;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\TestChild;
use Sunhill\ORM\Tests\Objects\TestParent;
use Sunhill\ORM\Tests\Objects\Passthru;
use Sunhill\ORM\Tests\Objects\SecondLevelChild;
use Sunhill\ORM\Tests\Objects\ThirdLevelChild;
use Sunhill\ORM\Tests\Objects\ReferenceOnly;
use Sunhill\ORM\Tests\Objects\ObjectUnit;

define('CLASS_COUNT',8);

class ManagerClassesTest extends TestCase
{
 
    protected function setupClasses() : void {
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        Classes::registerClass(ReferenceOnly::class);
        Classes::registerClass(Passthru::class);
        Classes::registerClass(SecondLevelChild::class);
        Classes::registerClass(ThirdLevelChild::class);
        Classes::registerClass(ObjectUnit::class);
    }
    
    public function testFlushClasses() {
        $test = new ClassManager();
        $this->setProtectedProperty($test,'classes',['test']);
        $this->assertFalse(empty($this->getProtectedProperty($test,'classes')));

        $this->callProtectedMethod($test,'flushClasses',[]);

        $this->assertEquals(1,count($this->getProtectedProperty($test,'classes')));
    }
    
    public function testGetClassEntry() {
        $test = new ClassManager();
        
        $result = [];
        $this->callProtectedMethod($test,'getClassEntry',[&$result,Dummy::class]);
        
        $this->assertEquals('Sunhill\\ORM\\Tests\\Objects\\Dummy',$result['class']);
    }
    
    public function testGetClassInformationEntries() {
        $test = new ClassManager();
    
        $result = [];
        $this->callProtectedMethod($test,'getClassInformationEntries',[&$result,Dummy::class]);
        
        $this->assertEquals('dummy',$result['name']);
    }
    
    public function testGetClassParentEntry() {
        $test = new ClassManager();
        
        $result = [];
        $this->callProtectedMethod($test,'getClassParentEntry',[&$result,Dummy::class]);
        
        $this->assertEquals('object',$result['parent']);
    }
    
    public function testGetClassPropertyEntries() {
        $test = new ClassManager();
        
        $result = [];
        $this->callProtectedMethod($test,'getClassPropertyEntries',[&$result,Dummy::class]);
        
        $this->assertEquals('Integer',$result['properties']['dummyint']['type']);
    }
    
    public function testBuildClassInformation() {
        $test = new ClassManager();
    
        $result = $this->callProtectedMethod($test,'buildClassInformation',[Dummy::class]);
        
        $this->assertEquals('Sunhill\\ORM\\Tests\\Objects\\Dummy',$result['class']);
        $this->assertEquals('dummy',$result['name']);
        $this->assertEquals('object',$result['parent']);
        $this->assertEquals('Integer',$result['properties']['dummyint']['type']);
    }
    
    public function testRegisterClass() {
        $test = new ClassManager();
        
        $this->callProtectedMethod($test,'registerClass',[Dummy::class]);
        
        $result = $this->getProtectedProperty($test,'classes');
        
        $this->assertEquals('Sunhill\\ORM\\Tests\\Objects\\Dummy',$result['dummy']['class']);
        $this->assertEquals('dummy',$result['dummy']['name']);
        $this->assertEquals('object',$result['dummy']['parent']);
        $this->assertEquals('Integer',$result['dummy']['properties']['dummyint']['type']);
        
    }
    
    public function testNumberOfClasses() {
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        $this->assertEquals(2,$test->getClassCount());
    }
    
    public function testNumberOfClassesViaApp() {
        $manager = app('\Sunhill\ORM\Managers\ClassManager');
        $manager->flushClasses();
        $manager->registerClass(Dummy::class);
        $this->assertEquals(2,$manager->getClassCount());
    }
    
     public function testNumberOfClassesViaAlias() {
        $manager = app('classes');
        $manager->flushClasses();
        $manager->registerClass(Dummy::class);
        $this->assertEquals(2,$manager->getClassCount());
    }
    
    public function testNumberOfClassesViaFacade() {
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        $this->assertEquals(2,Classes::getClassCount());
    }
    
    /**
     * @dataProvider GetClassnameProvider
     * @param unknown $test
     * @param unknown $expect
     */
    public function testGetClassname($test,$expect) {
        $this->setupClasses();
        if (is_callable($test)) {
            $test = $test();
        } 
        if ($expect == 'except') {
            try {
                Classes::getClassName($test);
            } catch (\Exception $e) {
                $this->assertTrue(true);
                return;
            }
            $this->fail("Expected exception not raised");
        } else {
            $this->assertEquals($expect,Classes::getClassName($test));
        }        
    }
    
    public function GetClassnameProvider() {
        return [
            ['dummy','dummy'],
            ['Sunhill\ORM\Tests\Objects\Dummy','dummy'],
            [-1,'except'],
            [1000,'except'],
            [function() { return new Dummy(); },'dummy'],
            [function() { return new \stdClass(); },'except'],
        ];
    }
    
    /**
     * @dataProvider GetClassProvider
     * @param unknown $class
     * @param unknown $subfield
     * @param unknown $field
     * @param unknown $expect
     */
    public function testGetClass($class,$subfield,$field,$expect) {
        $this->setupClasses();
        if ($expect == 'except') {
            try {
                $this->getField(Classes::getClass($class,$field),$subfield);
            } catch (\Exception $e) {
                $this->assertTrue(true);
                return;
            }
            $this->fail("Expected exception not raised");
        } else {
            $class = Classes::getClass($class,$field);
            if (is_null($subfield)) {
                $this->assertEquals($expect,$class);                
            } else {
                $this->assertEquals($expect,$class[$subfield]);
            }
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
    
    public function testGetClassWithObject() {
        $this->setupClasses();
        $test = new Dummy();
        $this->assertEquals('dummy',Classes::getClass($test,'name'));
    }
    
    public function testGetClassWithObjectFail() {
        $this->setupClasses();
        $this->expectException(ORMException::class);
        $test = new \stdClass();
        Classes::getClass($test,'name');
    }
    
    public function testDummyTable() {
        $this->setupClasses();
        $classes = Classes::getAllClasses();
        $i=0;
        foreach ($classes as $class=>$info) {
            if ($class === 'dummy') {
                $dummyid = $i;
            }
            $i++;
        }
        $this->assertEquals('dummies',Classes::getClass($dummyid,'table'));        
    }
    /**
     * @dataProvider SearchClassProvider
     * @param unknown $search
     */
    public function testSearchClassViaName($expect,$search) {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::searchClass($search));
    }
    
    public function SearchClassProvider() {
        return [
            ['dummy','dummy'],
            ['dummy','\\Sunhill\\ORM\\Tests\\Objects\\Dummy'],
            ['dummy','Sunhill\\ORM\\Tests\\Objects\\Dummy'],
            [null,'notexisting'],
            [null,'\\Sunhill\\ORM\\Tests\\Objects\\nonexisting'],
            [null,'Sunhill\\ORM\\Tests\\Objects\\nonexisting'],
        ];
    }
    
    /**
     * @dataProvider ClassParentProvider
     * @param unknown $test_class
     * @param unknown $expect
     */
    public function testClassParent($test_class,$expect) {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getParentOfClass($test_class));
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
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getChildrenOfClass($test_class,$level));    
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
        $this->setupClasses();
        if (is_null($test_class)) {
            $this->assertArrayContains($expect,Classes::getClassTree());
        } else {
            $this->assertArrayContains($expect,Classes::getClassTree($test_class));
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
        $this->setupClasses();
        $this->assertEquals('dummyint',Classes::getPropertiesOfClass('dummy')['dummyint']['name']);
    }
    
    public function testGetClassProperty() {
        $this->setupClasses();
        $this->assertEquals('dummyint',Classes::getPropertyOfClass('dummy','dummyint')['name']);
    }

    public function testGetClassTreeRoot() {
        $this->setupClasses();
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
            ]],Classes::getClassTree());    
    }
    
    public function testGetClassTreeClass() {
        $this->setupClasses();
        $this->assertEquals([
            'testparent'=>[
                'testchild'=>[],
                'passthru'=>[
                    'secondlevelchild'=>[
                        'thirdlevelchild'=>[]
                    ]
                ]
            ]
       ],Classes::getClassTree('testparent'));
    }
    
    public function testCreateObjectViaName() {
        $this->setupClasses();
        $test = Classes::createObject('testparent');
        $this->assertTrue(is_a($test,'Sunhill\ORM\Tests\Objects\TestParent'));
    }
    
    public function testCreateObjectViaNamespace() {
        $this->setupClasses();
        $test = Classes::createObject('Sunhill\ORM\Tests\Objects\TestParent');
        $this->assertTrue(is_a($test,'Sunhill\ORM\Tests\Objects\TestParent'));
    }
    
    /**
     * @dataProvider IsAProvider
     * @group IsA
     */
    public function testIsA($test,$param,$expect) {
        $this->setupClasses();
        $test = new $test();
        $this->assertEquals($expect,Classes::isA($test,$param));
    }
        
    public function IsAProvider() {
        return [
            ['Sunhill\ORM\Tests\Objects\TestParent','testparent',true],
            ['Sunhill\ORM\Tests\Objects\TestParent','Sunhill\ORM\Tests\Objects\TestParent',true],
            ['Sunhill\ORM\Tests\Objects\TestChild','testparent',true],
            ['Sunhill\ORM\Tests\Objects\TestChild','Sunhill\ORM\Tests\Objects\TestParent',true],
            ['Sunhill\ORM\Tests\Objects\TestParent','testchild',false],
            ['Sunhill\ORM\Tests\Objects\TestParent','Sunhill\ORM\Tests\Objects\TestChild',false],
            ['Sunhill\ORM\Tests\Objects\Dummy','testparent',false],
            ['Sunhill\ORM\Tests\Objects\Dummy','Sunhill\ORM\Tests\Objects\TestParent',false],
        ];
    }
    
    /**
     * @dataProvider IsAClassProvider
     * @group IsA
     */
    public function testIsAClass($test,$param,$expect) {
        $this->setupClasses();
        $test = new $test();
        $this->assertEquals($expect,Classes::isAClass($test,$param));
    }
    
    public function IsAClassProvider() {
        return [
            ['Sunhill\ORM\Tests\Objects\TestParent','testparent',true],
            ['Sunhill\ORM\Tests\Objects\TestParent','Sunhill\ORM\Tests\Objects\TestParent',true],
            ['Sunhill\ORM\Tests\Objects\TestChild','testparent',false],
            ['Sunhill\ORM\Tests\Objects\TestChild','Sunhill\ORM\Tests\Objects\TestParent',false],
            ['Sunhill\ORM\Tests\Objects\TestParent','testchild',false],
            ['Sunhill\ORM\Tests\Objects\TestParent','Sunhill\ORM\Tests\Objects\TestChild',false],
            ['Sunhill\ORM\Tests\Objects\Dummy','testparent',false],
            ['Sunhill\ORM\Tests\Objects\Dummy','Sunhill\ORM\Tests\Objects\TestParent',false],
        ];
    }
    
    /**
     * @dataProvider IsSubclassOfProvider
     * @group IsA
     */
    public function testIsSubclassOf($test,$param,$expect) {
        $this->setupClasses();
        $test = new $test();
        $this->assertEquals($expect,Classes::isSubclassOf($test,$param));
    }
        
    public function IsSubclassOfProvider() {
        return [
            ['Sunhill\ORM\Tests\Objects\TestParent','testparent',false],
            ['Sunhill\ORM\Tests\Objects\TestParent','Sunhill\ORM\Tests\Objects\TestParent',false],
            ['Sunhill\ORM\Tests\Objects\TestParent','testchild',false],
            ['Sunhill\ORM\Tests\Objects\TestParent','Sunhill\ORM\Tests\Objects\TestChild',false],
            ['Sunhill\ORM\Tests\Objects\TestChild','testparent',true],
            ['Sunhill\ORM\Tests\Objects\TestChild','Sunhill\ORM\Tests\Objects\TestParent',true],
            ['Sunhill\ORM\Tests\Objects\Dummy','testparent',false],
            ['Sunhill\ORM\Tests\Objects\Dummy','Sunhill\ORM\Tests\Objects\TestParent',false],
        ];
    }
    
    /**
     * @dataProvider GetInheritanceProvider
     * @param unknown $test
     * @param unknown $include_self
     * @param unknown $expect
     */
    public function testGetInheritance($test,$include_self,$expect) {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getInheritanceOfClass($test,$include_self));    
    }
    
    public function GetInheritanceProvider() {
        return [
            ['testparent',false,['object']],
            ['testparent',true,['testparent','object']],
            ['testchild',true,['testchild','testparent','object']]
        ];
    }
    
    /**
     * @dataProvider GetUsedTablesProvider
     * @param unknown $test
     * @param unknown $expect
     */
    public function testGetUsedTables($test,$expect) 
    {
        $this->setupClasses();
        $list = Classes::getUsedTablesOfClass($test);
        sort($list);
        $this->assertEquals($expect,$list);
    }
    
    public function GetUsedTablesProvider() 
    {
        return [
          ['testparent',['objects','testparents']],
          ['testchild',['objects','testchildren','testparents']]
        ];
    }
    
/**    
    public function testSearchClassWithTranslation() {
        $this->assertEquals('dummies',Classes::getClass('dummy','name_p'));
    }
*/    
}

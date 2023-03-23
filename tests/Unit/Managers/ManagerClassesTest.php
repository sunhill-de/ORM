<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Managers\ClassManager;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\ORMException;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;

class ManagerClassesTest extends TestCase
{
 
    /**
     * Tests: ClassManager::getClassEntry
     */
    public function testGetClassEntry() 
    {
        $test = new ClassManager();
        
        $result = [];
        $this->callProtectedMethod($test,'getClassEntry',[&$result,Dummy::class]);
        
        $this->assertEquals(Dummy::class,$result['class']);
    }
    
    /**
     * Tests: Classmanager::getClassInformationEntries
     */
    public function testGetClassInformationEntries() 
    {
        $test = new ClassManager();
        
        $result = [];
        $this->callProtectedMethod($test,'getClassInformationEntries',[&$result,Dummy::class]);
        
        $this->assertEquals('dummy',$result['name']);
    }
    
    /**
     * Tests: Classmanager::getClassParentEntry
     */
    public function testGetClassParentEntry() 
    {
        $test = new ClassManager();
        
        $result = [];
        $this->callProtectedMethod($test,'getClassParentEntry',[&$result,Dummy::class]);
        
        $this->assertEquals('object',$result['parent']);
    }
    
    /**
     * Tests: Classmanager::getClassProperties
     */
    public function testGetClassProperties()
    {
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        
        $result = $this->callProtectedMethod($test,'getClassProperties',[Dummy::class]);
        $this->assertArrayHasKey('dummyint', $result);
    }
    
    /**
     * Tests: Classmanager::getClassPropertyEntries
     */
    public function testGetClassPropertyEntries() 
    {
        $test = new ClassManager();
        
        $result = [];
        $this->callProtectedMethod($test,'getClassPropertyEntries',[&$result,Dummy::class]);
        
        $this->assertEquals('integer',$result['properties']['dummyint']['type']);
    }
    
    /**
     * Tests: Classmanager::buildClassInformation
     */
    public function testBuildClassInformation() 
    {
        $test = new ClassManager();
        
        $result = $this->callProtectedMethod($test,'buildClassInformation',[Dummy::class]);
        
        $this->assertEquals(Dummy::class,$result['class']);
        $this->assertEquals('dummy',$result['name']);
        $this->assertEquals('object',$result['parent']);
        $this->assertEquals('integer',$result['properties']['dummyint']['type']);
    }
    
    /**
     * Tests: Classmanager::registerClass
     */
    public function testRegisterClass() 
    {
        $test = new ClassManager();
        
        $this->callProtectedMethod($test,'registerClass',[Dummy::class]);
        
        $result = $this->getProtectedProperty($test,'classes');
        
        $this->assertEquals(Dummy::class,$result['dummy']['class']);
        $this->assertEquals('dummy',$result['dummy']['name']);
        $this->assertEquals('object',$result['dummy']['parent']);
        $this->assertEquals('integer',$result['dummy']['properties']['dummyint']['type']);
        
    }
    
    /**
     * Tests: Classmanager::registerClass
     */
    public function testRegisterClass_noclass() 
    {
        $this->expectException(ORMException::class);
        
        $test = new ClassManager();
        
        $this->callProtectedMethod($test,'registerClass',['notaclass']);        
    }
    
    /**
     * Tests: Classmanager::registerClass
     */
    public function testRegisterClass_duplicate()
    {
        $this->expectException(ORMException::class);
        
        $test = new ClassManager();

        $this->callProtectedMethod($test,'registerClass',[Dummy::class]);
        $this->callProtectedMethod($test,'registerClass',[Dummy::class]);        
    }
    
    /**
     * Tests: ClassManager::flushClasses
     */
    public function testFlushClasses() {
        $test = new ClassManager();
        $this->setProtectedProperty($test,'classes',['test']);
        $this->assertFalse(empty($this->getProtectedProperty($test,'classes')));
        
        $test->flushClasses();
        
        $this->assertEquals(1,count($this->getProtectedProperty($test,'classes')));
    }
    
    /**
     * Tests: ClassManager::getClassCount
     */
    public function testNumberOfClasses() {
        $test = new ClassManager();
        
        $test->registerClass(Dummy::class);
        
        $this->assertEquals(2,$test->getClassCount());
    }
    
    /**
     * Tests: ClassManager::getClassCount
     */
    public function testNumberOfClassesViaFacade() {
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        
        $this->assertEquals(2,Classes::getClassCount());
    }
    
    /**
     * Tests: ClassManager::getAllClasses
     */
    public function testGetAllClasses()
    {
        $test = new ClassManager();
        
        $test->registerClass(Dummy::class);
        
        $result = $test->getAllClasses();
        
        $this->assertEquals(2, count($result));
        $this->assertEquals('dummy',$result['dummy']['name']);
    }
    
    /**
     * Tests: ClassManager::getClassTree
     */
    public function testGetClassTree_root()
    {
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        $test->registerClass(DummyChild::class);
        $test->registerClass(TestParent::class);
        
        $result = $test->getClassTree();
        $expect = [
            'object'=>['dummy'=>['dummychild'=>[]],'testparent'=>[]]
        ];
        
        $this->assertEquals($expect, $result);
    }
    
    /**
     * Tests: ClassManager::getClassTree
     */
    public function testGetClassTree_nonroot()
    {
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        $test->registerClass(DummyChild::class);
        $test->registerClass(TestParent::class);
        
        $result = $test->getClassTree('dummy');
        $expect = [
            'dummy'=>['dummychild'=>[]]
        ];
        
        $this->assertEquals($expect, $result);
    }
    
    /**
     * @dataProvider NormalizeNamespaceProvider
     * @param unknown $namespace 
     * @param unknown $expect
     * Tests: ClassManager::normalizeNamespace
     */
    public function testNormalizeNamespace($namespace, $expect)
    {
        $test = new ClassManager();
        
        $this->assertEquals($expect, $test->normalizeNamespace($namespace));    
    }
    
    public function NormalizeNamespaceProvider()
    {
        return [
            ['this\is\a\namespace','this\is\a\namespace'],
            ['\this\\is\a\namespace','this\is\a\namespace'],
            ['\this\is\a\namespace','this\is\a\namespace'],
            ['this\\\is\a\\namespace','this\is\a\namespace'],
        ];    
    }
    
    protected function setupClasses() : void 
    {
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        Classes::registerClass(DummyChild::class);        
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        Classes::registerClass(ReferenceOnly::class);
        Classes::registerClass(TestSimpleChild::class);
        Classes::registerClass(SecondLevelChild::class);
        Classes::registerClass(ThirdLevelChild::class);
    }

    /**
     * Tests: checkForObject
     */
    public function testCheckForObject_pass()
    {
        $test = \Mockery::mock(ClassManager::class);
        $test->shouldReceive('searchClass')->with(Dummy::class)->andReturn('dummy');
        
        $this->assertEquals('dummy', $this->callProtectedMethod($test, 'checkForObject', [new Dummy()]));
    }
    
    /**
     * Tests: checkForObject
     */
    public function testCheckForObject_fail1()
    {
        $test = new ClassManager();
        
        $this->assertEquals(null, $this->callProtectedMethod($test, 'checkForObject', [new \StdClass()]));
    }
    
    /**
     * Tests: checkForObject
     */
    public function testCheckForObject_fail2()
    {
        $test = new ClassManager();
        
        $this->assertEquals(null, $this->callProtectedMethod($test, 'checkForObject', ['test']));
    }
    
    /**
     * @dataProvider CheckForNamespaceProvider
     * @param unknown $needle
     * @param unknown $expect
     * 
     * Tests: checkForNamespace
     */
    public function testCheckForNamespace($needle, $expect)
    {
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        $test->registerClass(DummyChild::class);
        $test->registerClass(TestParent::class);
        
        $this->assertEquals($expect, $this->callProtectedMethod($test, 'checkForNamespace', [$needle]));
    }

    public function CheckForNamespaceProvider()
    {
        return [
            [Dummy::class,'dummy'],
            [DummyChild::class,'dummychild'],
            ['somethingelse',null]
        ];
    }
    
    /**
     * @dataProvider CheckForNamespaceProvider
     * @param unknown $needle
     * @param unknown $expect
     * 
     * tests: checkForClassname
     */
    public function testCheckForClassname($needle, $expect)
    {
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        $test->registerClass(DummyChild::class);
        $test->registerClass(TestParent::class);
        
        $this->assertEquals($expect, $this->callProtectedMethod($test, 'checkForNamespace', [$needle]));
    }
    
    public function CheckForClassnameProvider()
    {
        return [
            ['dummy','dummy'],
            ['dummychild','dummychild'],
            ['somethingelse',null],
            ['object','object']
        ];
    }
    
    /**
     * @dataProvider CheckForStringProvider
     * @param unknown $needle
     * @param unknown $expect
     * 
     * Tests: checkForString
     */
    public function testCheckForString($needle, $expect)
    {
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        $test->registerClass(DummyChild::class);
        $test->registerClass(TestParent::class);
        
        $this->assertEquals($expect, $this->callProtectedMethod($test, 'checkForString', [$needle]));
    }
    
    public function CheckForStringProvider()
    {
        return [
            [Dummy::class,'dummy'],
            [DummyChild::class,'dummychild'],
            ['somethingelse',null],
            ['dummy','dummy'],
            ['dummychild','dummychild'],
            ['somethingelse',null],
            ['object','object']
        ];
    }

    /**
     * Tests: checkForInt
     * I don't get this to work
    public function testCheckForInt_pass()
    {
        $test = \Mockery::mock(ClassManager::class);
        $test->shouldAllowMockingProtectedMethods();
        
        $test->shouldReceive('getClassnameWihIndex')->with(1)->andReturn('dummy');
        
        $this->assertEquals('dummy', $this->callProtectedMethod($test, 'checkForInt', [1]));
    }
     */
    
    /**
     * Tests: checkForInt
     */
    public function testCheckForInt_fail()
    {
        $test = new ClassManager();
        $this->assertNull($this->callProtectedMethod($test, 'checkForInt', ['abc']));        
    }
    
    /**
     * @dataProvider SearchClassProvider
     * @param unknown $search
     * 
     * Tests: ClassManager::searchClass
     */
    public function testSearchClass($expect,$search) {
        if ($search == 'takeclass') {
            $search = new Dummy();
        }
        $this->setupClasses();
        $this->assertEquals($expect,Classes::searchClass($search));
    }
    
    public function SearchClassProvider() {
        return [
            ['dummy','dummy'],
            ['dummy',Dummy::class],
            ['dummy','takeclass'],
            ['dummy',1],
            [null,'notexisting'],
            [null,'\\Sunhill\\ORM\\Tests\\Objects\\nonexisting'],
            [null,new \StdClass()],
        ];
    }
    
    /**
     * @dataProvider SearchClassProvider
     * @param unknown $search
     *
     * Tests: ClassManager::getClassName
     */
    public function testGetClassName($expect,$search) {
        if ($search == 'takeclass') {
            $search = new Dummy();
        }
        $this->setupClasses();
        try {
            $this->assertEquals($expect,Classes::getClassName($search));
        } catch (ORMException $e) {
            if (is_null($expect)) {
                $this->assertTrue(true);
            } else {
                throw $e;
            }
        }
    }
    
    public function getClassNameProvider() {
        return [
            ['dummy','dummy'],
            ['dummy',Dummy::class],
            ['dummy','takeclass'],
            [null,'notexisting'],
            [null,'\\Sunhill\\ORM\\Tests\\Objects\\nonexisting'],
            [null,new \StdClass()],
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
    public function testGetChildrenOfClass($test_class,$level,$expect) 
    {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getChildrenOfClass($test_class,$level));    
    }
    
    public function GetChildrenOfClassProvider() 
    {
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
        $this->assertTrue(is_a($test,'Sunhill\ORM\Tests\Testobjects\TestParent'));
    }
    
    public function testCreateObjectViaNamespace() {
        $this->setupClasses();
        $test = Classes::createObject('Sunhill\ORM\Tests\Testobjects\TestParent');
        $this->assertTrue(is_a($test,'Sunhill\ORM\Tests\Testobjects\TestParent'));
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
            ['Sunhill\ORM\Tests\Testobjects\TestParent','testparent',true],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','Sunhill\ORM\Tests\Testobjects\TestParent',true],
            ['Sunhill\ORM\Tests\Testobjects\TestChild','testparent',true],
            ['Sunhill\ORM\Tests\Testobjects\TestChild','Sunhill\ORM\Tests\Testobjects\TestParent',true],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','testchild',false],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','Sunhill\ORM\Tests\Testobjects\TestChild',false],
            ['Sunhill\ORM\Tests\Testobjects\Dummy','testparent',false],
            ['Sunhill\ORM\Tests\Testobjects\Dummy','Sunhill\ORM\Tests\Testobjects\TestParent',false],
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
            ['Sunhill\ORM\Tests\Testobjects\TestParent','testparent',true],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','Sunhill\ORM\Tests\Testobjects\TestParent',true],
            ['Sunhill\ORM\Tests\Testobjects\TestChild','testparent',false],
            ['Sunhill\ORM\Tests\Testobjects\TestChild','Sunhill\ORM\Tests\Testobjects\TestParent',false],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','testchild',false],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','Sunhill\ORM\Tests\Testobjects\TestChild',false],
            ['Sunhill\ORM\Tests\Testobjects\Dummy','testparent',false],
            ['Sunhill\ORM\Tests\Testobjects\Dummy','Sunhill\ORM\Tests\Testobjects\TestParent',false],
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
            ['Sunhill\ORM\Tests\Testobjects\TestParent','testparent',false],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','Sunhill\ORM\Tests\Testobjects\TestParent',false],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','testchild',false],
            ['Sunhill\ORM\Tests\Testobjects\TestParent','Sunhill\ORM\Tests\Testobjects\TestChild',false],
            ['Sunhill\ORM\Tests\Testobjects\TestChild','testparent',true],
            ['Sunhill\ORM\Tests\Testobjects\TestChild','Sunhill\ORM\Tests\Testobjects\TestParent',true],
            ['Sunhill\ORM\Tests\Testobjects\Dummy','testparent',false],
            ['Sunhill\ORM\Tests\Testobjects\Dummy','Sunhill\ORM\Tests\Testobjects\TestParent',false],
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

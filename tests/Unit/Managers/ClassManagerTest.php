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
use Sunhill\ORM\Managers\Exceptions\ClassNotORMException;
use Sunhill\ORM\Managers\Exceptions\ClassNotAccessibleException;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Managers\Exceptions\ClassNameForbiddenException;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;
use Sunhill\ORM\Properties\PropertyBoolean;
use Sunhill\ORM\Properties\PropertyDate;
use Sunhill\ORM\Properties\PropertyObject;
use Sunhill\ORM\Properties\PropertyArray;
use Sunhill\ORM\Managers\Exceptions\DuplicateEntryException;

class BadClass1 extends ORMObject
{
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'Class');
    }
}

class BadClass2 extends ORMObject
{
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'attr_');
    }
}

class ClassManagerTest extends TestCase
{
 
    /**
     * Tests: Classmanager::buildClassInformation
     */
    public function testBuildClassInformation() 
    {
        $test = new ClassManager();
        
        $result = $this->callProtectedMethod($test,'buildClassInformation',[Dummy::class]);
        
        $this->assertEquals(Dummy::class,$result->class);
        $this->assertEquals('dummy',$result->name);
        $this->assertEquals('object',$result->parent);
        $this->assertEquals('integer',$result->properties['dummyint']['type']);
    }
    
    /**
     * Tests: Classmanager::registerClass
     */
    public function testRegisterClass() 
    {
        $test = new ClassManager();
        
        $this->callProtectedMethod($test,'registerClass',[Dummy::class]);
        
        $result = $this->getProtectedProperty($test,'registered_items');
        
        $this->assertEquals(Dummy::class,$result['dummy']->class);
        $this->assertEquals('dummy',$result['dummy']->name);
        $this->assertEquals('object',$result['dummy']->parent);
        $this->assertEquals('integer',$result['dummy']->properties['dummyint']['type']);
        
    }
    
    /**
     * @dataProvider RegisterClassProvider
     * @param unknown $class
     * @param unknown $expected_exception
     */
    public function testRegisterClassException($class, $expected_exception)
    {
        $this->expectException($expected_exception);
        
        $test = new ClassManager();
        
        $test->registerClass($class);
    }

    public static function RegisterClassProvider()
    {
        return [
            [static::class, ClassNotORMException::class],
            ['abc',ClassNotAccessibleException::class],
            [BadClass1::class,ClassNameForbiddenException::class],
            [BadClass2::class,ClassNameForbiddenException::class]
        ];    
    }
    
    /**
     * Tests: Classmanager::registerClass
     */
    public function testRegisterClass_duplicate()
    {
        $this->expectException(DuplicateEntryException::class);
        
        $test = new ClassManager();

        $this->callProtectedMethod($test,'registerClass',[Dummy::class]);
        $this->callProtectedMethod($test,'registerClass',[Dummy::class]);        
    }
    
    /**
     * Tests: ClassManager::flushClasses
     */
    public function testFlushClasses() {
        $test = new ClassManager();
        $this->setProtectedProperty($test,'registered_items',['test']);
        $this->assertFalse(empty($this->getProtectedProperty($test,'registered_items')));
        
        $test->flush();
        
        $this->assertEquals(1,count($this->getProtectedProperty($test,'registered_items')));
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
        Classes::flush();
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
        $this->assertEquals('dummy',$result['dummy']->name);
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
    
    public static function NormalizeNamespaceProvider()
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
        Classes::flush();
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

    public static function CheckForNamespaceProvider()
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
    
    public static function CheckForStringProvider()
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
    
    public static function SearchClassProvider() {
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
        } catch (ClassNotAccessibleException $e) {
            if (is_null($expect)) {
                $this->assertTrue(true);
            } else {
                throw $e;
            }
        }
    }
    
    public static function getClassNameProvider() {
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
     * @dataProvider CheckClassProvider
     * 
     * Tests: ClassManager::checkClass
     */
    public function testCheckClass($expect, $search)
    {
        if ($search == 'takeclass') {
            $search = new Dummy();
        }
        $test = new ClassManager();
        $test->registerClass(Dummy::class);
        $test->registerClass(DummyChild::class);
        $test->registerClass(TestParent::class);
        
        try {
            $this->assertEquals($expect,$this->callProtectedMethod($test, 'checkClass', [$search]));
        } catch (ClassNotAccessibleException $e) {
            if (is_null($expect)) {
                $this->assertTrue(true);
            } else {
                throw $e;
            }
        }
    }
    
    public static function CheckClassProvider()
    {
        return [
            ['dummy','dummy'],
            ['dummy',Dummy::class],
            ['dummy','takeclass'],
            [null,'notexisting'],
            [null,'\\Sunhill\\ORM\\Tests\\Objects\\nonexisting'],
            [null,new \StdClass()],
            [null,null]
        ];
    }
    /**
     * @dataProvider GetClassProvider
     * 
     * Tests: ClassManager::getClass()
     */
    public function testGetClass($class, $field, $expect) {
        $this->setupClasses();
        if ($class = 'makeobject') {
            $class = new Dummy();
        }
        try {
            $result = Classes::getClass($class, $field);
            if ($expect == 'array') {
                $this->assertEquals('dummies', $result->table);
            } else {
                $this->assertEquals($expect, $result);
            }
        } catch (\Exception $e) {
            if ($expect == 'except') {
                $this->assertTrue(true);
            } else {
                throw $e;
            }            
        }
    }
    
    public static function GetClassProvider() {
        return [
            ['dummy', null, 'array'],
            [Dummy::class, null, 'array'],
            [1, null, 'array'],
            ['makeobject', null, 'array'],
            
            ['nonexisting', null, 'except'],
            [-1, null, 'except'],
            [1000, null, 'except'],
            [new \StdClass(), null, 'except'],
            
            ['dummy', 'table', 'dummies'],
            [Dummy::class, 'table', 'dummies'],
            [1, 'table', 'dummies'],
            ['makeobject', 'table', 'dummies'],
            ['dummy', 'name_p', 'dummies'],
            ['dummy', 'parent', 'object']
        ];    
    }
    
    /**
     * @dataProvider ClassTableProvider
     *
     * Tests: ClassManager::getTableOfClass
     */
    public function testClassTable($test_class,$expect) {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getTableOfClass($test_class));
    }
    
    public static function ClassTableProvider() {
        return [
            ['dummy','dummies'],
            ['testparent','testparents'],
            ['testchild','testchildren'],
            [Dummy::class,'dummies']
        ];
    }
    
    /**
     * @dataProvider ClassParentProvider
     * 
     * Tests: ClassManager::getParentOfClass
     */
    public function testClassParent($test_class,$expect) {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getParentOfClass($test_class));
    }
    
    public static function ClassParentProvider() {
        return [
            ['dummy','object'],
            ['testparent','object'],
            ['testchild','testparent'],
            [Dummy::class,'object']
        ];
    }
    
    /**
     * @dataProvider GetInheritanceProvider
     * 
     * Tests: ClassManager::getInheritanceOfClass
     */
    public function testGetInheritance($test,$include_self,$expect) {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getInheritanceOfClass($test,$include_self));
    }
    
    public static function GetInheritanceProvider() {
        return [
            ['testparent',false,['object']],
            ['testparent',true,['testparent','object']],
            ['testchild',true,['testchild','testparent','object']],
            [Dummy::class,false,['object']]
        ];
    }
    
    /**
     * @dataProvider GetChildrenOfClassProvider
     * 
     * Tests: getChildrenOfClass
     */
    public function testGetChildrenOfClass($test_class,$level,$expect) 
    {
        $this->setupClasses();
        $this->assertEquals($expect,Classes::getChildrenOfClass($test_class,$level));    
    }
    
    public static function GetChildrenOfClassProvider() 
    {
        return [
                ['referenceonly',-1,['secondlevelchild'=>['thirdlevelchild'=>[]]]],
                ['secondlevelchild',-1,['thirdlevelchild'=>[]]],
                ['testparent',-1,['testchild'=>[],'testsimplechild'=>[]]],
                ['referenceonly',1,['secondlevelchild'=>[]]],
                [Dummy::class,-1,['dummychild'=>[]]]
       ];
    }
    
    /**
     * @dataProvider GetPropertiesOfClassProvider
     * 
     * Tests: getPropertiesOfClass
     */
    public function testGetPropertiesOfClass($test_class, $expect_property)
    {
        $this->setupClasses();
        $result = Classes::getPropertiesOfClass($test_class);
        $this->assertTrue(isset($result[$expect_property]));
    }
    
    public static function GetPropertiesOfClassProvider()
    {
        return [
            ['dummy','dummyint'],
            ['testparent', 'parentint'],
            ['testchild', 'childint'],
            ['testchild', 'parentint'],
            [DummyChild::class, 'dummyint']
        ];
    }
    
    /**
     * @dataProvider GetPropertyOfClassProvider
     *
     * Tests: getPropertyOfClass
     */
    public function testGetPropertyOfClass($test_class, $expect_property)
    {
        $this->setupClasses();
        $result = Classes::getPropertyOfClass($test_class, $expect_property);
        $this->assertEquals($result['name'],$expect_property);
    }
    
    public static function GetPropertyOfClassProvider()
    {
        return [
            ['dummy','dummyint'],
            ['testparent', 'parentint'],
            ['testchild', 'childint'],
            ['testchild', 'parentint'],
            [DummyChild::class, 'dummyint']
        ];
    }
    
    /**
     * @dataProvider GetNamespaceOfClassProvider
     *
     * Tests: getPropertyOfClass
     */
    public function testGetNamespaceOfClass($test_class, $expect)
    {
        $this->setupClasses();
        $this->assertEquals($expect, Classes::getNamespaceOfClass($test_class));
    }
    
    public static function GetNamespaceOfClassProvider()
    {
        return [
            ['dummy',Dummy::class],
            ['testparent', TestParent::class],
            [DummyChild::class, DummyChild::class]
        ];
    }
    
    /**
     * @dataProvider GetUsedTablesProvider
     * 
     * Tests: getUsedTablesOfClass
     */
    public function testGetUsedTables($test,$expect)
    {
        $this->setupClasses();
        $list = Classes::getUsedTablesOfClass($test);
        sort($list);
        $this->assertEquals($expect,$list);
    }
    
    public static function GetUsedTablesProvider()
    {
        return [
            ['testparent',['objects','testparents']],
            ['testchild',['objects','testchildren','testparents']]
        ];
    }
    
    /**
     * Tests: createObject
     */
    public function testCreateObjectViaName() {
        $this->setupClasses();
        
        $test = Classes::createObject('testparent');
        
        $this->assertTrue(is_a($test,TestParent::class));
    }
    
    /**
     * Tests: createObject
     */
    public function testCreateObjectViaNamespace() {
        $this->setupClasses();
        
        $test = Classes::createObject(TestParent::class);
        
        $this->assertTrue(is_a($test,TestParent::class));
    }
    
    /**
     * @dataProvider IsAProvider
     * @group IsA
     * 
     * Tests: isA
     */
    public function testIsA($test,$param,$expect) {
        $this->setupClasses();
        $test = new $test();
        $this->assertEquals($expect,Classes::isA($test,$param));
    }
    
    public static function IsAProvider() {
        return [
            [TestParent::class, 'testparent', true],
            [TestParent::class, TestParent::class, true],
            [TestChild::class, 'testparent', true],
            [TestChild::class, TestParent::class, true],
            [TestParent::class, 'testchild',false],
            [TestParent::class, TestChild::class,false],
            [Dummy::class, 'testparent',false],
            [Dummy::class, TestParent::class,false],
        ];
    }
    
    /**
     * @dataProvider IsAClassProvider
     * @group IsA
     * 
     * Test: isAClass
     */
    public function testIsAClass($test,$param,$expect) {
        $this->setupClasses();
        $test = new $test();
        $this->assertEquals($expect,Classes::isAClass($test,$param));
    }
    
    public static function IsAClassProvider() {
        return [
            [TestParent::class,'testparent',true],
            [TestParent::class,TestParent::class,true],
            [TestChild::class,'testparent',false],
            [TestChild::class,TestParent::class,false],
            [TestParent::class,'testchild',false],
            [TestParent::class,TestChild::class,false],
            [Dummy::class,'testparent',false],
            [Dummy::class,TestParent::class,false],
        ];
    }
    
    /**
     * @dataProvider IsSubclassOfProvider
     * @group IsA
     * 
     * Tests: isSubclassOf
     */
    public function testIsSubclassOf($test,$param,$expect) {
        $this->setupClasses();
        $test = new $test();
        $this->assertEquals($expect,Classes::isSubclassOf($test,$param));
    }
    
    public static function IsSubclassOfProvider() {
        return [
            [TestParent::class,'testparent',false],
            [TestParent::class,TestParent::class,false],
            [TestParent::class,'testchild',false],
            [TestParent::class,TestChild::class,false],
            [TestChild::class,'testparent',true],
            [TestChild::class,TestParent::class,true],
            [Dummy::class,'testparent',false],
            [Dummy::class,TestParent::class,false],
        ];
    }
    
    /**
     * @dataProvider GetClassTreeProvider
     * @group tree
     * 
     * Tests: getClassTree
     */
    public function testGetClassTree($test_class,$expect) {
        $this->setupClasses();
        if (is_null($test_class)) {
            $this->assertArrayContains($expect,Classes::getClassTree());
        } else {
            $this->assertArrayContains($expect,Classes::getClassTree($test_class));
        }
    }
    
    public static function GetClassTreeProvider() {
        return [
            [null,
                ['object'=>
                    [
                        'dummy'=>['dummychild'=>[]],
                        'referenceonly'=>['secondlevelchild'=>['thirdlevelchild'=>[]]],
                        'testparent'=>[
                            'testsimplechild'=>[],
                            'testchild'=>[]
                        ]
                    ]    
                ]                
            ],
            ['testparent',['testparent'=>['testsimplechild'=>[],'testchild'=>[]]]],
            ['dummy',['dummy'=>['dummychild'=>[]]]],
            
        ];
    }

    /**
     * @dataProvider QueryProvider
     * @group query
     */
    public function testQuery($callback, $modifier, $expect)
    {
        Classes::registerClass(Dummy::class);
        Classes::registerClass(TestParent::class);
        Classes::registerClass(TestChild::class);
        Classes::registerClass(DummyChild::class);
        Classes::registerClass(ReferenceOnly::class);
        Classes::registerClass(SecondLevelChild::class);
        Classes::registerClass(ThirdLevelChild::class);
        
        $query = Classes::query();
        $result = $callback($query);
        
        if (is_callable($modifier)) {
            $result = $modifier($result);
        }
        $this->assertEquals($expect, $result);
    }
    
    public static function QueryProvider()
    {
        return [
            [function($query) { return $query->count(); }, null, 8],
            [function($query) { return $query->first(); }, function($value) { return $value->name; }, 'object'],
            [function($query) { return $query->orderBy('name')->first(); }, function($value) { return $value->name; }, 'dummy'],
            [function($query) { return $query->where('name','dummy')->first(); }, function($value) { return $value->name; }, 'dummy'],
            [function($query) { return $query->where('name','like','test%')->count(); }, null, 2],
            [function($query) { return $query->where('name','like','%child')->count(); }, null, 4],
            [function($query) { return $query->where('name','like','dummy%')->count(); }, null, 2],            
           [function($query) { return $query->whereHasPropertyOfType(PropertyArray::class)->count(); }, null, 5],
            [function($query) { return $query->whereHasPropertyOfType(PropertyDate::class)->count(); }, null, 2],
            [function($query) { return $query->whereHasPropertyOfType(PropertyBoolean::class)->count(); }, null, 2],
            [function($query) { return $query->whereHasPropertyOfType(PropertyBoolean::class, true)->count(); }, null, 1],            
            [function($query) { return $query->whereHasPropertyOfName('dummyint')->count(); }, null, 2],
            [function($query) { return $query->whereHasPropertyOfName('dummyint', true)->count(); }, null, 1],
            [function($query) { return $query->whereHasPropertyOfName('parent%', true)->count(); }, null, 1],
            [function($query) { return $query->whereHasPropertyOfName('notexisting%', true)->count(); }, null, 0],

            [function($query) { return $query->whereHasParent('dummy')->count(); }, null, 1],
            [function($query) { return $query->whereHasParent('dummy')->first(); }, function($value) { return $value->name; }, 'dummychild'],            
            [function($query) { return $query->whereHasParent('dummychild')->count(); }, null, 0],            
            [function($query) { return $query->whereHasParent('referenceonly')->count(); }, null, 2],
            [function($query) { return $query->whereHasParent('referenceonly')->first(); }, function($value) { return $value->name; }, 'secondlevelchild'],            
            [function($query) { return $query->whereHasParent('referenceonly', true)->first(); }, function($value) { return $value->name; }, 'secondlevelchild'],
            [function($query) { return $query->whereHasParent('referenceonly', true)->count(); }, null, 1],            
            [function($query) { return $query->whereHasParent('testparent')->count(); }, null, 1],

            [function($query) { return $query->whereIsParentOf('dummy')->count(); }, null, 1],
            [function($query) { return $query->whereIsParentOf('dummy')->first(); }, function($value) { return $value->name; }, 'object'],
            [function($query) { return $query->whereIsParentOf('dummychild', true)->first(); }, function($value) { return $value->name; }, 'dummy'],
            [function($query) { return $query->whereIsParentOf('thirdlevelchild')->count(); }, null, 3],
            [function($query) { return $query->whereIsParentOf('thirdlevelchild', true)->count(); }, null, 1],
            [function($query) { return $query->whereIsParentOf('thirdlevelchild', true)->first(); }, function($value) { return $value->name; }, 'secondlevelchild'],
            
            [function($query) { return $query->where('table','dummies')->first(); }, function($value) { return $value->name; }, 'dummy'],
            [function($query) { return $query->where('special',true)->first(); }, function($value) { return $value->name; }, 'dummy'],
            ];
    }
}

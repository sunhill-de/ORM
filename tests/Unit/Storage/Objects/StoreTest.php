<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Unit\Storage\Utils\CollectionsAndObjects;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;
use Sunhill\ORM\Properties\PropertyInteger;
use Sunhill\ORM\Properties\PropertyVarchar;

class StoreTest extends DatabaseTestCase
{
    
    use CollectionsAndObjects;
    
    protected function fillObject($object)
    {
        $object->_created_at = '2023-07-06 13:35:00';    
        $object->_updated_at = '2023-07-06 13:35:00';
        $object->_uuid = 'abc-def-ghi';
        $object->_owner = 0;
        $object->_group = 0;
        $object->_read = 7;
        $object->_edit = 7;
        $object->_delete = 7;
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testDummy()
    {
        $object = new Dummy();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->dummyint = 5;
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('objects',['id'=>$id,'classname'=>'dummy','_owner'=>0,'_group'=>0,'_read'=>7,'_edit'=>7,'_delete'=>7]);
        $this->assertDatabaseHas('dummies',['id'=>$id,'dummyint'=>5]);        
    }

    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testDummyWithTags()
    {
        $object = new Dummy();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->dummyint = 5;
        $object->tags[] = $this->getTag(3);

        $test->dispatch('store');
        $id = $object->getID();
        
        $query = DB::table('tagobjectassigns')->get();
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>$id,'tag_id'=>3]);
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testDummyWithAttributes()
    {
        $object = new Dummy();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $attr = $object->dynamicAddProperty('int_attribute', 'integer');
        $attr->setAttributeID(1);
        $attr = $object->dynamicAddProperty('char_attribute', 'string');
        $attr->setAttributeID(3);
        $object->int_attribute = 1509;
        $object->char_attribute = 'LOREM';
        $object->dummyint = 5;

        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>1,'object_id'=>$id]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>3,'object_id'=>$id]);
        $this->assertDatabaseHas('attr_int_attribute',['object_id'=>$id,'value'=>1509]);
        $this->assertDatabaseHas('attr_char_attribute',['object_id'=>$id,'value'=>'LOREM']);
    }
        
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testTestParent()
    {
        $object = new TestParent();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->parentint = 101; 
        $object->parentchar = 'BAB'; 
        $object->parentfloat = 1.01; 
        $object->parenttext = 'The ice is really cold;  the streetlight really old'; 
        $object->parentenum = 'testA'; 
        $object->parentdate = '2023-04-28'; 
        $object->parenttime = '10:07:00'; 
        $object->parentdatetime = '2023-04-28 10:07:00';
        $object->parentbool = true;
        $object->parentsarray[] = 'ABC'; 
        $object->parentsarray[] = 'DEF';
        $object->parentsarray[] = 'GHI';        
        $object->parentoarray[] = $this->getObject(2);
        $object->parentoarray[] = $this->getObject(3);
        $object->parentoarray[] = $this->getObject(4);  
        $object->parentmap['KeyA'] = 'ValueA';
        $object->parentmap['KeyB'] = 'ValueB';
        $object->nosearch = 100;        
        $object->parentobject = $this->getObject(1);
        $object->parentcollection = $this->getCollection(1);

        $test->dispatch('store');
        $id = $object->getID();
                
        $this->assertDatabaseHas('testparents',[
            'id'=>$id,
            'parentint'=>101,
            'parentchar'=>'BAB',
            'parentfloat'=>1.01,
            'parenttext'=>'The ice is really cold;  the streetlight really old',
            'parentenum'=>'testA',
            'parentdate'=>'2023-04-28',
            'parenttime'=>'10:07:00',
            'parentdatetime'=>'2023-04-28 10:07:00',
            'parentcalc'=>'101A',
            'nosearch'=>100,
            'parentobject'=>1,
            'parentcollection'=>1,
            'parentbool'=>1
        ]);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id, 'value'=>2, 'index'=>0]);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id, 'value'=>3, 'index'=>1]);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id, 'value'=>4, 'index'=>2]);        
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id, 'value'=>'ABC', 'index'=>0]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id, 'value'=>'DEF', 'index'=>1]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id, 'value'=>'GHI', 'index'=>2]);        
        $this->assertDatabaseHas('testparents_parentmap',['id'=>$id, 'value'=>'ValueA', 'index'=>'KeyA']);
        $this->assertDatabaseHas('testparents_parentmap',['id'=>$id, 'value'=>'ValueB', 'index'=>'KeyB']);
        
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>1]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>2]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>3]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>4]);
    }
   
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testTestParentWithNullField()
    {
        $object = new TestParent();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->parentint = 101;
        $object->parentchar = null;
        $object->parentfloat = 1.01;
        $object->parenttext = 'The ice is really cold;  the streetlight really old';
        $object->parentenum = 'testA';
        $object->parentdate = '2023-04-28';
        $object->parenttime = '10:07:00';
        $object->parentdatetime = '2023-04-28 10:07:00';
        $object->parentbool = true;
        $object->nosearch = 100;
        
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('testparents',['id'=>$id,'parentchar'=>null]);
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testTestParentWithNullFieldAndDefaultNull()
    {
        $object = new TestParent();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->parentint = 101;
        $object->parentfloat = 1.01;
        $object->parenttext = 'The ice is really cold;  the streetlight really old';
        $object->parentenum = 'testA';
        $object->parentdate = '2023-04-28';
        $object->parenttime = '10:07:00';
        $object->parentdatetime = '2023-04-28 10:07:00';
        $object->parentbool = true;
        $object->nosearch = 100;
        
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('testparents',['id'=>$id,'parentchar'=>null]);
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testTestParentWithDefaultField()
    {
        $object = new TestParent();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->parentint = 101;
        $object->parentchar = 'ABC';
        $object->parentfloat = 1.01;
        $object->parenttext = 'The ice is really cold;  the streetlight really old';
        $object->parentenum = 'testA';
        $object->parentdate = '2023-04-28';
        $object->parenttime = '10:07:00';
        $object->parentdatetime = '2023-04-28 10:07:00';
        $object->parentbool = true;
        
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('testparents',['id'=>$id,'nosearch'=>1]);
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->parentint = 101;
        $object->parentchar = 'BAB';
        $object->parentfloat = 1.01;
        $object->parenttext = 'The ice is really cold, the streetlight really old';
        $object->parentenum = 'testA';
        $object->parentdate = '2023-04-28';
        $object->parenttime = '10:07:01';
        $object->parentbool = true;
        $object->parentdatetime = '2023-04-28 10:07:01';
        $object->parentsarray[] = 'ABC';
        $object->parentsarray[] = 'DEF';
        $object->parentsarray[] = 'GHI';
        $object->parentobject = $this->getObject(1);
        $object->parentcollection = $this->getCollection(2);
        $object->parentoarray[] = $this->getObject(2);
        $object->parentoarray[] = $this->getObject(3);
        $object->nosearch = 100;
        $object->parentmap['KeyA'] = 'ValueA';
        $object->parentmap['KeyB'] = 'ValueB';
        
        $object->childint = 202;
        $object->childchar = 'CBC';
        $object->childfloat = 2.02;
        $object->childtext = 'Her childs all alone as she melts into her own';
        $object->childenum = 'testB';
        $object->childdate = '2022-04-28';
        $object->childtime = '10:00:02';
        $object->childdatetime = '2022-04-28 10:00:02';
        $object->childsarray[] = 'JKL';
        $object->childsarray[] = 'MNO';
        $object->childsarray[] = 'PQR';
        $object->childoarray[] = $this->getObject(4);
        $object->childoarray[] = $this->getObject(5);
        $object->childobject = $this->getObject(2);
        $object->childcollection = $this->getComplexCollection(4);
        $object->childmap['Key0A'] = $this->getObject(1);
        $object->childmap['Key0B'] = $this->getObject(2);
        
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('testparents',[
            'id'=>$id,
            'parentint'=>101,
            'parentchar'=>'BAB',
            'parentfloat'=>1.01,
            'parenttext'=>'The ice is really cold, the streetlight really old',
            'parentenum'=>'testA',
            'parentdate'=>'2023-04-28',
            'parenttime'=>'10:07:01',
            'parentdatetime'=>'2023-04-28 10:07:01',
            'parentobject'=>1,
            'parentcollection'=>2,
            'nosearch'=>100,
            'parentbool'=>1,
        ]);
        $this->assertDatabaseHas('testchildren',[
            'id'=>$id,
            'childint'=>202,
            'childchar'=>'CBC',
            'childfloat'=>2.02,
            'childtext'=>'Her childs all alone as she melts into her own',
            'childenum'=>'testB',
            'childdate'=>'2022-04-28',
            'childtime'=>'10:00:02',
            'childdatetime'=>'2022-04-28 10:00:02',
            'childobject'=>2,
            'childcollection'=>4,
        ]);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id,'index'=>0,'value'=>2]);    
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id,'index'=>1,'value'=>3]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id,'index'=>0,'value'=>'ABC']);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id,'index'=>1,'value'=>'DEF']);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id,'index'=>2,'value'=>'GHI']);
        $this->assertDatabaseHas('testparents_parentmap',['id'=>$id,'index'=>'KeyA','value'=>'ValueA']);
        $this->assertDatabaseHas('testparents_parentmap',['id'=>$id,'index'=>'KeyB','value'=>'ValueB']);
        
        $this->assertDatabaseHas('testchildren_childoarray',['id'=>$id,'index'=>0,'value'=>4]);
        $this->assertDatabaseHas('testchildren_childoarray',['id'=>$id,'index'=>1,'value'=>5]);
        $this->assertDatabaseHas('testchildren_childsarray',['id'=>$id,'index'=>0,'value'=>'JKL']);
        $this->assertDatabaseHas('testchildren_childsarray',['id'=>$id,'index'=>1,'value'=>'MNO']);
        $this->assertDatabaseHas('testchildren_childsarray',['id'=>$id,'index'=>2,'value'=>'PQR']);
        $this->assertDatabaseHas('testchildren_childmap',['id'=>$id,'index'=>'Key0A','value'=>1]);
        $this->assertDatabaseHas('testchildren_childmap',['id'=>$id,'index'=>'Key0B','value'=>2]);

        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>1]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>2]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>3]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>4]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>5]);
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testReferenceOnly()
    {
        $object = new ReferenceOnly();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->testsarray[] = 'ABC';
        $object->testsarray[] = 'DEF';
        $object->testoarray[] = $this->getObject(1);
        $object->testoarray[] = $this->getObject(2);
    
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('referenceonlies',['id'=>$id]);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>$id,'index'=>0,'value'=>'ABC']);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>$id,'index'=>1,'value'=>'DEF']);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>$id,'index'=>0,'value'=>1]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>$id,'index'=>1,'value'=>2]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>1]);
        $this->assertDatabaseHas('objectobjectassigns', ['container_id'=>$id,'target_id'=>2]);
    }

    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testReferenceOnly_noreferences()
    {
        $object = new ReferenceOnly();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('referenceonlies',['id'=>$id]);
        $this->assertDatabaseMissing('objectobjectassigns', ['container_id'=>$id]);
        
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testSecondLevelChild()
    {
        $object = new SecondLevelChild();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->childint = 123;
        
        $test->dispatch('store');
        $id = $object->getID();

        $this->assertDatabaseHas('referenceonlies',['id'=>$id]);
        $this->assertDatabaseHas('secondlevelchildren',['id'=>$id,'childint'=>123]);        
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testThirdLevelChild()
    {
        $object = new ThirdLevelChild();
        $test = new MysqlStorage();
        $test->setCollection($object);

        $this->fillObject($object);
        $object->childint = 234;
        $object->childchildint = 456;
        $object->childchildchar = 'ABCD';
        $object->thirdlevelobject = $this->getObject(4);
        $object->thirdlevelsarray[] = 'AB';
        $object->thirdlevelsarray[] = 'CD';
        
        $test->dispatch('store');
        $id = $object->getID();
        
        $this->assertDatabaseHas('referenceonlies',['id'=>$id]);
        $this->assertDatabaseHas('secondlevelchildren',['id'=>$id,'childint'=>234]);
        $this->assertDatabaseHas('thirdlevelchildren',[
            'id'=>$id,
            'childchildint'=>456,
            'childchildchar'=>'ABCD',
            'thirdlevelobject'=>4,            
        ]);
        $this->assertDatabaseHas('thirdlevelchildren_thirdlevelsarray',['id'=>$id,'index'=>0,'value'=>'AB']);
        $this->assertDatabaseHas('thirdlevelchildren_thirdlevelsarray',['id'=>$id,'index'=>1,'value'=>'CD']);
    }
    
    /**
     * @group storeobject
     * @group object
     * @group store
     */
    public function testSimpleChild()
    {
        $object = new TestSimpleChild();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->fillObject($object);
        $object->parentint = 101;
        $object->parentchar = 'BAB';
        $object->parentfloat = 1.01;
        $object->parenttext = 'The ice is really cold;  the streetlight really old';
        $object->parentenum = 'testA';
        $object->parentdate = '2023-04-28';
        $object->parenttime = '10:07:00';
        $object->parentdatetime = '2023-04-28 10:07:00';
        $object->parentsarray[] = 'ABC';
        $object->parentsarray[] = 'DEF';
        $object->parentsarray[] = 'GHI';
        $object->parentoarray[] = $this->getObject(2);
        $object->parentoarray[] = $this->getObject(3);
        $object->parentoarray[] = $this->getObject(4);
        $object->parentmap['KeyA'] = 'ValueA';
        $object->parentmap['KeyB'] = 'ValueB';
        $object->nosearch = 100;
        $object->parentobject = $this->getObject(1);
        $object->parentcollection = $this->getCollection(1);
        $object->parentbool = false;
        
        $test->dispatch('store');
        $id = $object->getID();
 
        $this->assertDatabaseHas('testparents',[
            'id'=>$id,
            'parentint'=>101,
            'parentchar'=>'BAB',
            'parentfloat'=>1.01,
            'parenttext'=>'The ice is really cold;  the streetlight really old',
            'parentenum'=>'testA',
            'parentdate'=>'2023-04-28',
            'parenttime'=>'10:07:00',
            'parentdatetime'=>'2023-04-28 10:07:00',
            'parentcalc'=>'101A',
            'nosearch'=>100,
            'parentobject'=>1,
            'parentcollection'=>1,
            'parentbool'=>false,
        ]);
        $this->assertDatabaseHas('testsimplechildren',['id'=>$id]);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id, 'value'=>2, 'index'=>0]);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id, 'value'=>3, 'index'=>1]);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>$id, 'value'=>4, 'index'=>2]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id, 'value'=>'ABC', 'index'=>0]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id, 'value'=>'DEF', 'index'=>1]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>$id, 'value'=>'GHI', 'index'=>2]);
        $this->assertDatabaseHas('testparents_parentmap',['id'=>$id, 'value'=>'ValueA', 'index'=>'KeyA']);
        $this->assertDatabaseHas('testparents_parentmap',['id'=>$id, 'value'=>'ValueB', 'index'=>'KeyB']);        
    }
    
}
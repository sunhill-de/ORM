<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;
use Sunhill\ORM\Objects\Tag;
use Sunhill\ORM\Tests\Testobjects\DummyCollection;
use Sunhill\ORM\Tests\Testobjects\ComplexCollection;
use Sunhill\ORM\Tests\Testobjects\Circular;

class UpdateTest extends DatabaseTestCase
{
    
    protected function getTag($id)
    {
        $tag = new Tag();
        $tag->load($id);
        return $tag;
    }
    
    protected function getObject($id)
    {
        $object = new Dummy();
        $object->load($id);
        return $object;
    }
    
    protected function getCollection($id, $class = DummyCollection::class)
    {
        $object = new $class();
        $object->load($id);
        return $object;
    }

    protected function getDummy1()
    {
        $object = new Dummy();
        $object->setID(1);
        
        $property = $object->getProperty('dummyint');
        $property->loadValue(123);
        $property = $object->getProperty('tags');
        $property->loadValue([$this->getTag(1),$this->getTag(2),$this->getTag(4)]);
        
        return $object;
    }
    
    protected function getDummy2()
    {
        $object = new Dummy();
        $object->setID(2);
        
        $property = $object->getProperty('dummyint');
        $property->loadValue(234);
        
        return $object;
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testDummy()
    {
        $object = $this->getDummy1();
        $test = new MysqlStorage();
        $test->setCollection($object);        

        $object->dummyint = 321;
        
        $test->dispatch('update',1);        
        
        $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>321]);
    }

    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testTagUntagged()
    {
        $object = $this->getDummy2();
        $test = new MysqlStorage();
        $test->setCollection($object);

        $object->tags[] = $this->getTag(1);
        
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>2,'tag_id'=>1]);
        
        $test->dispatch('update',2);

        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>2,'tag_id'=>1]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testRemoveTags()
    {
        $object = $this->getDummy1();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
        $object->tags->clear();        
        
        $test->dispatch('update',1);
        
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testAddTags()
    {
        $object = $this->getDummy1();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>4]);
        
        $object->tags[] = $this->getTag(3);
        $test->dispatch('update',1);
        
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testDeleteTags()
    {
        $object = $this->getDummy1();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>4]);
        
        unset($object->tags[2]);
        $test->dispatch('update',1);
        
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1,'tag_id'=>4]);
    }
    
    protected function setProperty($object,string $name, $value)
    {
        $property = $object->getProperty($name);
        $property->loadValue($value);
    }
    
    protected function getTestParent9()
    {
        $object = new TestParent();
        $object->setID(9);
        $this->setProperty($object, 'parentint', 111);
        $this->setProperty($object, 'parentchar','ABC');
        $this->setProperty($object, 'parentbool',true);
        $this->setProperty($object, 'parentfloat',1.11);
        $this->setProperty($object, 'parenttext','Lorem ipsum');
        $this->setProperty($object, 'parentdatetime','1974-09-15 17:45:00');
        $this->setProperty($object, 'parentdate','1974-09-15');
        $this->setProperty($object, 'parenttime','17:45:00');
        $this->setProperty($object, 'parentenum','testC');
        $this->setProperty($object, 'parentobject',$this->getObject(1));
        $this->setProperty($object, 'parentcalc','111A');
        $this->setProperty($object, 'parentcollection',$this->getCollection(7));
        $this->setProperty($object, 'parentinformation','some.path.to9');        
        $this->setProperty($object, 'tags', [$this->getTag(3),$this->getTag(4),$this->getTag(5)]);
        $this->setProperty($object, 'parentsarray', ['String A','String B']);
        $this->setProperty($object, 'parentoarray', [$this->getObject(2),$this->getObject(3)]);
        $this->setProperty($object, 'parentmap', ['KeyA'=>'ValueA','KeyB'=>'ValueB']);
        
        $attr = $object->dynamicAddProperty('attribute1', 'integer');
        $attr->set_AttributeID(2);
        $attr->loadValue(123);
        
        $attr = $object->dynamicAddProperty('attribute2', 'integer');
        $attr->set_AttributeID(3);
        $attr->loadValue(222);
        
        return $object;        
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testChangeAllAttributes()
    {
        $object = $this->getTestParent9();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $object->attribute1 = 234;
        $object->attribute2 = 333;
        
        $test->dispatch('update', 9);
        
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>2,'object_id'=>9]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>3,'object_id'=>9]);
        $this->assertDatabaseHas('attr_attribute1',['object_id'=>9,'value'=>234]);
        $this->assertDatabaseHas('attr_attribute2',['object_id'=>9,'value'=>333]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testChangeOneAttributes()
    {
        $object = $this->getTestParent9();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $object->attribute1 = 234;
        
        $test->dispatch('update', 9);
        
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>2,'object_id'=>9]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>3,'object_id'=>9]);
        $this->assertDatabaseHas('attr_attribute1',['object_id'=>9,'value'=>234]);
        $this->assertDatabaseHas('attr_attribute2',['object_id'=>9,'value'=>222]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testAddAttributes()
    {
        $object = $this->getTestParent9();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $attr = $object->dynamicAddProperty('general_attribute', 'integer');
        $attr->set_AttributeID(4);
        $attr->loadValue(222);
        
        $test->dispatch('update', 9);
        
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>4,'object_id'=>9]);
        $this->assertDatabaseHas('attr_general_attribute',['object_id'=>9,'value'=>222]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testRemoveAttributes()
    {
        $object = $this->getTestParent9();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $object->attribute1 = null;
        $test->dispatch('update', 9);
        
        $this->assertDatabaseMissing('attributeobjectassigns',['attribute_id'=>2,'object_id'=>9]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>3,'object_id'=>9]);
        $this->assertDatabaseMissing('attr_attribute1',['object_id'=>9,'value'=>234]);
        $this->assertDatabaseHas('attr_attribute2',['object_id'=>9,'value'=>222]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testDummyChild()
    {
        $object = new DummyChild();
        $object->setID(8);
        $this->setProperty($object, 'dummyint', 789);
        $this->setProperty($object, 'dummychildint', 999);
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $object->dummyint = 321;
        $object->dummychildint = 888;
        
        
        $test->dispatch('update', 8);
        
        $this->assertDatabaseHas('dummies',['id'=>8,'dummyint'=>321]);
        $this->assertDatabaseHas('dummychildren',['id'=>8,'dummychildint'=>888]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testTestParent()
    {
        $object = $this->getTestParent9();
        $test = new MysqlStorage();
        $test->setCollection($object);

        $object->parentint = 222;
        $object->parentchar = 'DEF';
        $object->parentfloat = 2.22;
        $object->parenttext = 'All that we are';        
        $object->parentdatetime = '1989-11-09 20:00:00';
        $object->parentdate = '1989-11-09';
        $object->parenttime = '20:00:00';        
        $object->parentenum = 'testA';
        $object->parentobject = $this->getObject(2);
        $object->parentcollection = $this->getCollection(2);
        $object->parentsarray[] = 'String C';
        $object->parentoarray[] = $this->getObject(4);
        
        $test->dispatch('update', 9);
        
        $this->assertDatabaseHas('testparents',[
            'id'=>9,
            'parentint'=>222,
            'parentchar'=>'DEF',
            'parentfloat'=>2.22,
            'parenttext'=>'All that we are',
            'parentdatetime'=>'1989-11-09 20:00:00',
            'parentdate'=>'1989-11-09',
            'parenttime'=>'20:00:00',
            'parentenum'=>'testA',
            'parentobject'=>2,
            'parentcalc'=>'222A'
        ]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>9,'value'=>'String C']);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>9,'value'=>4]);
        $this->assertDatabaseHas('objectobjectassigns',['container_id'=>9,'target_id'=>4]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     * @group child
     */
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage();
        $test->setCollection($object);
        $this->setProperty($object, 'parentint', 800);
        
        $this->setProperty($object,'parentchar','DEF');
        $this->setProperty($object,'parentfloat',8);
        $this->setProperty($object,'parenttext','no sea takimata sanctus');
        $this->setProperty($object,'parentdatetime','1974-09-15 17:45:00');
        $this->setProperty($object,'parentdate','1974-09-15');
        $this->setProperty($object,'parenttime','17:45:00');
        $this->setProperty($object,'parentenum','testB');
        
        $this->setProperty($object,'parentobject',$this->getObject(4));        
        $this->setProperty($object,'parentsarray',['Something','Something else','Another something'],['String B','String C']);
        $this->setProperty($object,'parentoarray',[$this->getObject(3),$this->getObject(2),$this->getObject(1)]);
        
        $this->setProperty($object,'childint',801);
        $this->setProperty($object,'childchar','DEF');
        $this->setProperty($object,'childfloat',8);
        $this->setProperty($object,'childtext','no sea takimata sanctus');
        $this->setProperty($object,'childdatetime','1974-09-15 17:45:00');
        $this->setProperty($object,'childdate','1974-09-15');
        $this->setProperty($object,'childtime','17:45:00');
        $this->setProperty($object,'childenum','testB');
        
        $this->setProperty($object,'childobject',$this->getObject(4));
        $this->setProperty($object,'childcollection',$this->getCollection(9,ComplexCollection::class));
        $this->setProperty($object,'childsarray',['Yea','Yupp']);
        $this->setProperty($object,'childoarray',[$this->getObject(5),$this->getObject(6),$this->getObject(7)]);
        
        
        $object->parentint = 222;
        $object->parentchar = 'DEF';
        $object->parentfloat = 2.22;
        $object->parenttext = 'All that we are';
        $object->parentdatetime = '1989-11-09 20:00:00';
        $object->parentdate = '1989-11-09';
        $object->parenttime = '20:00:00';
        $object->parentenum = 'testA';
        $object->parentobject = $this->getObject(2);
        $object->parentcollection = $this->getCollection(3);        
        $object->parentsarray[] = 'Another something';
        unset($object->parentoarray[2]);
        
        $object->childint = 333;
        $object->childchar = 'REF';
        $object->childfloat = 3.33;
        $object->childtext = 'Panic attack';
        $object->childdatetime = '1990-11-09 20:00:00';
        $object->childdate = '1990-11-09';
        $object->childtime = '20:00:10';
        $object->childenum = 'testC';
        $object->childobject = $this->getObject(5);
        $object->childcollection = $this->getCollection(10, ComplexCollection::class);
        $object->childsarray[] = 'YO';
        $object->childoarray[2] = $this->getObject(8);
        
        $test->dispatch('update', 18);
        
        $this->assertDatabaseHas('testparents',[
            'id'=>18,
            'parentint'=>222,
            'parentchar'=>'DEF',
            'parentfloat'=>2.22,
            'parenttext'=>'All that we are',
            'parentdatetime'=>'1989-11-09 20:00:00',
            'parentdate'=>'1989-11-09',
            'parenttime'=>'20:00:00',
            'parentenum'=>'testA',
            'parentobject'=>2,
            'parentcalc'=>'222A'            
        ]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>18,'value'=>'Another something']);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>18,'value'=>2]);
        $this->assertDatabaseMissing('testparents_parentoarray',['id'=>18,'value'=>1]);
        
        $this->assertDatabaseHas('testchildren',[
            'id'=>18,
            'childint'=>333,
            'childchar'=>'REF',
            'childfloat'=>3.33,
            'childtext'=>'Panic attack',
            'childdatetime'=>'1990-11-09 20:00:00',
            'childdate'=>'1990-11-09',
            'childtime'=>'20:00:10',
            'childenum'=>'testC',
            'childobject'=>5,
            'childcalc'=>'333B'            
        ]);
        $this->assertDatabaseHas('testchildren_childsarray',['id'=>18,'value'=>'YO']);
        $this->assertDatabaseHas('testchildren_childoarray',['id'=>18,'value'=>8]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testArrayClear()
    {
        $object = $this->getReferenceOnly27();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $object->testsarray->clear();
        $object->testoarray->clear();
        
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27]);
        $this->assertDatabaseHas('objectobjectassigns',['container_id'=>27]);
        
        $test->dispatch('update', 27);
        
        $this->assertDatabaseMissing('referenceonlies_testsarray',['id'=>27]);
        $this->assertDatabaseMissing('referenceonlies_testoarray',['id'=>27]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>27]);
    }
    
    protected function getReferenceOnly27()
    {
        $object = new ReferenceOnly();
        $object->setID(27);
        $this->setProperty($object,'testsarray',['Test A','Test B']);
        $this->setProperty($object,'testoarray',[$this->getObject(2),$this->getObject(3)]);
        return $object;
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testArrayNew()
    {
        $object = new ReferenceOnly();
        $object->setID(29);
        $test = new MysqlStorage();
        $test->setCollection($object);
                
        $this->assertDatabaseMissing('referenceonlies_testsarray',['id'=>29]);
        $this->assertDatabaseMissing('referenceonlies_testoarray',['id'=>29]);
        $this->assertDatabaseMissing('objectobjectassigns',['container_id'=>29]);
        
        $object->testsarray[] = 'New A';
        $object->testsarray[] = 'New B';
        $object->testoarray[] = $this->getObject(1);
        $object->testoarray[] = $this->getObject(2);
        
        $test->dispatch('update', 29);
        
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>29,'value'=>'New A','index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>29,'value'=>'New B','index'=>1]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>29,'value'=>1,'index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>29,'value'=>2,'index'=>1]);
        $this->assertDatabaseHas('objectobjectassigns',['container_id'=>29,'target_id'=>1]);
        $this->assertDatabaseHas('objectobjectassigns',['container_id'=>29,'target_id'=>2]);
    }
    
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testArrayEntryRemovedAndReindexed()
    {
        $object = $this->getReferenceOnly27();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        unset($object->testsarray[0]);
        unset($object->testoarray[0]);
        
        $entries = DB::table('referenceonlies_testsarray')->get();
        
        $test->dispatch('update', 27);
        
        $entries = DB::table('referenceonlies_testsarray')->get();
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test B','index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>3,'index'=>0]);        
        $this->assertDatabaseHas('objectobjectassigns',['container_id'=>27,'target_id'=>3]);
    }
        
    /**
     * @group updateobject
     * @group object
     * @group update
     */
    public function testArrayEntryAddedAndReindexed()
    {
        $object = $this->getReferenceOnly27();
        $test = new MysqlStorage();
        $test->setCollection($object);

        unset($object->testsarray[1]);
        $object->testsarray[] = 'Test C'; 
        $object->testsarray[] = 'Test B';

        unset($object->testoarray[1]);
        $object->testoarray[] = $this->getObject(1);
        $object->testoarray[] = $this->getObject(3);
        
        $test->dispatch('update', 27);
        
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test A','index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test C','index'=>1]);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test B','index'=>2]);
        
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>2,'index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>1,'index'=>1]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>3,'index'=>2]);
    }

    /**
     * @group circular
     */
    public function testCircular()
    {
        $test1 = new Circular();
        $test1->load(34);
        
        $test2 = new Circular();
        $test2->load(36);
        $test2->child = $test1;
        $test2->commit();
        
        $this->assertDatabaseHas('circulars',['id'=>36,'payload'=>333,'child'=>34]);
    }
    
    /**
     * @group uuid
     */
    public function testChangeUUID()
    {
        $object = $this->getDummy1();
        $test = new MysqlStorage();
        $test->setCollection($object);
        
        $object->dummyint = 321;
        $object->_uuid = 'ABC';
        
        $test->dispatch('update',1);
        
        $this->assertDatabaseHas('objects', ['id'=>1,'_uuid'=>'ABC']);
        $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>321]);
    }
}
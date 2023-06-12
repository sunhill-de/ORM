<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;

class UpdateTest extends DatabaseTestCase
{
    
    protected function makeChange($old, $new): \StdClass
    {
        $result = new \StdClass();
        
        $result->value = $new;
        $result->shadow = $old;
        
        return $result;
    }
    
    public function testDummy()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        
        $test->setEntity('dummyint', $this->makeChange(123,321));
        $this->assertDatabaseHas('objects',['id'=>1,'updated_at'=>'2019-05-15 10:00:00']);
        
        $test->update(1);
        
        $this->assertDatabaseHas('dummies',['id'=>1,'dummyint'=>321]);
        $this->assertDatabaseMissing('objects',['id'=>1,'updated_at'=>'2019-05-15 10:00:00']);
    }

    public function testTagUntagged()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        
        $test->setEntity('tags', $this->makeChange([],[1,2]));
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>2,'tag_id'=>1]);
        
        $test->update(2);

        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>2,'tag_id'=>1]);
    }
    
    public function testRemoveTags()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        
        $test->setEntity('tags', $this->makeChange([1,2,4],[]));
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
        
        $test->update(1);
        
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1]);
    }
    
    public function testAddTags()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        
        $test->setEntity('tags', $this->makeChange([1,2,4],[1,2,3,4]));
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>4]);
        
        $test->update(1);
        
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>3]);
    }
    
    public function testDeleteTags()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        
        $test->setEntity('tags', $this->makeChange([1,2,4],[1,2]));
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>4]);
        
        $test->update(1);
        
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>1,'tag_id'=>1]);
        $this->assertDatabaseMissing('tagobjectassigns',['container_id'=>1,'tag_id'=>4]);
    }
    
    public function testChangeAllAttributes()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        $attribute1 = new \StdClass();
        $attribute1->name = 'attribute1';
        $attribute1->attribute_id = 2;
        $attribute1->type = 'integer';
        $attribute1->value = 234;
        $attribute1->shadow = 123;
        $attribute2 = new \StdClass();
        $attribute2->name = 'attribute2';
        $attribute2->attribute_id = 3;
        $attribute2->type = 'integer';
        $attribute2->value = 333;
        $attribute2->shadow = 222;
        $test->setEntity('attributes',[$attribute1,$attribute2]);
        
        $test->update(9);
        
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>2,'object_id'=>9]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>3,'object_id'=>9]);
        $this->assertDatabaseHas('attr_attribute1',['object_id'=>9,'value'=>234]);
        $this->assertDatabaseHas('attr_attribute2',['object_id'=>9,'value'=>333]);
    }
    
    public function testChangeOneAttributes()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        $attribute1 = new \StdClass();
        $attribute1->name = 'attribute1';
        $attribute1->attribute_id = 2;
        $attribute1->type = 'integer';
        $attribute1->value = 234;
        $attribute1->shadow = 123;
        $test->setEntity('attributes',[$attribute1]);
        
        $test->update(9);
        
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>2,'object_id'=>9]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>3,'object_id'=>9]);
        $this->assertDatabaseHas('attr_attribute1',['object_id'=>9,'value'=>234]);
        $this->assertDatabaseHas('attr_attribute2',['object_id'=>9,'value'=>222]);
    }
    
    public function testAddAttributes()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        $attribute1 = new \StdClass();
        $attribute1->attribute_id = 5;
        $attribute1->name = 'char_attribute';
        $attribute1->type = 'string';
        $attribute1->value = 'DEF';
        $attribute1->shadow = null;
        $test->setEntity('attributes',[$attribute1]);
        
        $test->update(1);
        
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>5,'object_id'=>1]);
        $this->assertDatabaseHas('attr_char_attribute',['object_id'=>1,'value'=>'DEF']);
    }
    
    public function testRemoveAttributes()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        $attribute1 = new \StdClass();
        $attribute1->name = 'attribute1';
        $attribute1->attribute_id = 2;
        $attribute1->type = 'integer';
        $attribute1->value = null;
        $attribute1->shadow = 123;
        $test->setEntity('attributes',[$attribute1]);
        
        $test->update(9);
        
        $this->assertDatabaseMissing('attributeobjectassigns',['attribute_id'=>2,'object_id'=>9]);
        $this->assertDatabaseHas('attributeobjectassigns',['attribute_id'=>3,'object_id'=>9]);
        $this->assertDatabaseMissing('attr_attribute1',['object_id'=>9,'value'=>234]);
        $this->assertDatabaseHas('attr_attribute2',['object_id'=>9,'value'=>222]);
    }
    
    public function testDummyChild()
    {
        $object = new DummyChild();
        $test = new MysqlStorage($object);
        $test->setEntity('dummyint',$this->makeChange(789,321));
        $test->setEntity('dummychildint',$this->makeChange(999,888));
        
        $test->update(8);
        
        $this->assertDatabaseHas('dummies',['id'=>8,'dummyint'=>321]);
        $this->assertDatabaseHas('dummychildren',['id'=>8,'dummychildint'=>888]);
    }
    
    public function testTestParent()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        
        $test->setEntity('parentint',$this->makeChange(111,222));
        $test->setEntity('parentchar',$this->makeChange('ABC','DEF'));
        $test->setEntity('parentfloat',$this->makeChange(1.11,2.22));
        $test->setEntity('parenttext',$this->makeChange('Lorem ipsum','All that we are'));
        $test->setEntity('parentdatetime',$this->makeChange('1974-09-15 17:45:00','1989-11-09 20:00:00'));
        $test->setEntity('parentdate',$this->makeChange('1974-09-15','1989-11-09'));
        $test->setEntity('parenttime',$this->makeChange('17:45:00','20:00:00'));
        $test->setEntity('parentenum',$this->makeChange('testC','testA'));
        $test->setEntity('parentobject',$this->makeChange(1,2));
        $test->setEntity('parentsarray',$this->makeChange(['String A','String B'],['String B','String C']));
        $test->setEntity('parentoarray',$this->makeChange([2,3],[3,4]));
        $test->setEntity('parentcalc',$this->makeChange('111A','222A'));
        
        $test->update(9);
        
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
            'parentobject'=>2,
            'parentcalc'=>'222A'
        ]);
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>9,'value'=>'String C']);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>9,'value'=>4]);
    }
    
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage($object);
        
        $test->setEntity('parentint',$this->makeChange(800,222));
        $test->setEntity('parentchar',$this->makeChange('DEF','DEF'));
        $test->setEntity('parentfloat',$this->makeChange(8,2.22));
        $test->setEntity('parenttext',$this->makeChange('no sea takimata sanctus','All that we are'));
        $test->setEntity('parentdatetime',$this->makeChange('1974-09-15 17:45:00','1989-11-09 20:00:00'));
        $test->setEntity('parentdate',$this->makeChange('1974-09-15','1989-11-09'));
        $test->setEntity('parenttime',$this->makeChange('17:45:00','20:00:00'));
        $test->setEntity('parentenum',$this->makeChange('testB','testA'));
        $test->setEntity('parentobject',$this->makeChange(4,2));
        $test->setEntity('parentsarray',$this->makeChange(['Something','Something else','Another something'],['String B','String C']));
        $test->setEntity('parentoarray',$this->makeChange([3,2,1],[3,4]));
        $test->setEntity('parentcalc',$this->makeChange('800A','222A'));
        
        $test->setEntity('childint',$this->makeChange(801,333));
        $test->setEntity('childchar',$this->makeChange('DEF','REF'));
        $test->setEntity('childfloat',$this->makeChange(8,3.33));
        $test->setEntity('childtext',$this->makeChange('no sea takimata sanctus','Panic attack'));
        $test->setEntity('childdatetime',$this->makeChange('1974-09-15 17:45:00','1990-11-09 20:00:00'));
        $test->setEntity('childdate',$this->makeChange('1974-09-15','1990-11-09'));
        $test->setEntity('childtime',$this->makeChange('17:45:00','20:00:10'));
        $test->setEntity('childenum',$this->makeChange('testB','testC'));
        $test->setEntity('childobject',$this->makeChange(4,3));
        $test->setEntity('childsarray',$this->makeChange(['Yea','Yupp'],['Yea','Yupp','YO']));
        $test->setEntity('childoarray',$this->makeChange([5,6,7],[5,6,8]));
        $test->setEntity('childcalc',$this->makeChange('801B','333B'));
        
        $test->update(18);

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
        $this->assertDatabaseHas('testparents_parentsarray',['id'=>18,'value'=>'String C']);
        $this->assertDatabaseHas('testparents_parentoarray',['id'=>18,'value'=>4]);
        
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
            'childobject'=>3,
            'childcalc'=>'333B'            
        ]);
        $this->assertDatabaseHas('testchildren_childsarray',['id'=>18,'value'=>'YO']);
        $this->assertDatabaseHas('testchildren_childoarray',['id'=>18,'value'=>8]);
    }
    
    public function testArrayClear()
    {
        $object = new ReferenceOnly();
        $test = new MysqlStorage($object);
        
        $test->setEntity('testsarray',$this->makeChange(['Test A','Test B'],[]));
        $test->setEntity('testoarray',$this->makeChange([2,3],[]));
        
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27]);
        
        $test->update(27);
        
        $this->assertDatabaseMissing('referenceonlies_testsarray',['id'=>27]);
        $this->assertDatabaseMissing('referenceonlies_testoarray',['id'=>27]);
    }
    
    public function testArrayNew()
    {
        $object = new ReferenceOnly();
        $test = new MysqlStorage($object);
        
        $test->setEntity('testsarray',$this->makeChange([],['New A','New B']));
        $test->setEntity('testoarray',$this->makeChange([],[1,2]));
        
        $this->assertDatabaseMissing('referenceonlies_testsarray',['id'=>29]);
        $this->assertDatabaseMissing('referenceonlies_testoarray',['id'=>29]);
        
        $test->update(29);
        
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>29,'value'=>'New A','index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>29,'value'=>'New B','index'=>1]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>29,'value'=>1,'index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>29,'value'=>2,'index'=>1]);
    }
    
    public function testArrayEntryRemovedAndReindexed()
    {
        $object = new ReferenceOnly();
        $test = new MysqlStorage($object);
        
        $test->setEntity('testsarray',$this->makeChange(['Test A','Test B'],['Test B']));
        $test->setEntity('testoarray',$this->makeChange([2,3],[3]));
        
        $test->update(27);

        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test B','index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>3,'index'=>0]);        
    }
        
    public function testArrayEntryAddedAndReindexed()
    {
        $object = new ReferenceOnly();
        $test = new MysqlStorage($object);
        
        $test->setEntity('testsarray',$this->makeChange(['Test A','Test B'],['Test A','Test C','Test B']));
        $test->setEntity('testoarray',$this->makeChange([2,3],[2,1,3]));
        
        $test->update(27);
        
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test A','index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test C','index'=>1]);
        $this->assertDatabaseHas('referenceonlies_testsarray',['id'=>27,'value'=>'Test B','index'=>2]);
        
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>2,'index'=>0]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>1,'index'=>1]);
        $this->assertDatabaseHas('referenceonlies_testoarray',['id'=>27,'value'=>3,'index'=>2]);
    }
    
    
}
<?php

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;

class MysqlStorageStoreTest extends DatabaseTestCase
{
    
    public function testDummy()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        $test->setEntity('dummyint', 5);
        $id = $test->Store();
        
        $entry = DB::table('objects')->where('id',$id)->first();
        $this->assertEquals($id,$entry->id);
        $this->assertEquals('dummy',$entry->classname);
        $this->assertFalse(empty($entry->uuid));
        $this->assertEquals(0,$entry->obj_owner);
        $this->assertEquals(0,$entry->obj_group);
        $this->assertEquals(7,$entry->obj_read);
        $this->assertEquals(7,$entry->obj_edit);
        $this->assertEquals(7,$entry->obj_delete);
        $entry = DB::table('dummies')->where('id',$id)->first();
        $this->assertEquals(5,$entry->dummyint);
    }

    public function testDummyWithTags()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        $test->setEntity('dummyint', 5);
        $test->setEntity('tags',[1,2,3]);
        $id = $test->Store();
        
        $this->assertDatabaseHas('tagobjectassigns',['container_id'=>$id,'tag_id'=>3]);
    }
    
    public function testDummyWithAttributes()
    {
        $object = new Dummy();
        $test = new MysqlStorage($object);
        $attribute1 = new \StdClass();
        $attribute1->name = 'int_attribute';
        $attribute1->attribute_id = 1;
        $attribute1->value = 1509;
        $attribute1->type = 'int';
        $attribute2 = new \StdClass();
        $attribute2->name = 'char_attribute';
        $attribute2->attribute_id = 5;
        $attribute2->value = 'LOREM';
        $attribute2->type = 'char';
        $test->setEntity('dummyint', 5);
        $test->setEntity('attributes',[$attribute1,$attribute2]);
        $id = $test->Store();
        
        $this->assertDatabaseHas('attributevalues',['attribute_id'=>1,'object_id'=>$id,'value'=>'1509']);
        $this->assertDatabaseHas('attributevalues',['attribute_id'=>5,'object_id'=>$id,'value'=>'LOREM']);
    }
        
    public function testTestParent()
    {
        $object = new TestParent();
        $test = new MysqlStorage($object);
        $input_data = [
            'parentint'=>101,
            'parentchar'=>'BAB',
            'parentfloat'=>1.01,
            'parenttext'=>'The ice is really cold, the streetlight really old',
            'parentenum'=>'testA',
            'parentdate'=>'2023-04-28',
            'parenttime'=>'10:07',
            'parentdatetime'=>'2023-04-28 10:07',
            'parentsarray'=>['ABC','DEF','GHI'],
            'parentoarray'=>[1,2,3,4],
            'parentcalc'=>'101A',
            'nosearch'=>100,
            'parentobject'=>1,
            ];
        foreach ($input_data as $key => $value) {
            $test->setEntity($key,$value);
        }
        $id = $test->Store();
        $simple_data = [
            'id'=>$id,
            'parentint'=>$input_data['parentint'],
            'parentchar'=>$input_data['parentchar'],
            'parentfloat'=>$input_data['parentfloat'],
            'parenttext'=>$input_data['parenttext'],
            'parentenum'=>$input_data['parentenum'],
            'parentdate'=>$input_data['parentdate'],
            'parenttime'=>$input_data['parenttime'],
            'parentdatetime'=>$input_data['parentdatetime'],
            'nosearch'=>$input_data['nosearch'],
            'parentobject'=>$input_data['parentobject']
        ];
        
        $this->assertDatabaseHas('testparents',$simple_data);
        $this->assertDatabaseHas('testparents_array_parentoarray',['id'=>$id,'target'=>$input_data['parentoarray'][0],'index'=>0]);
        $this->assertDatabaseHas('testparents_array_parentoarray',['id'=>$id,'target'=>$input_data['parentoarray'][3],'index'=>3]);
        $this->assertDatabaseHas('testparents_array_parentsarray',['id'=>$id,'target'=>$input_data['parentsarray'][0],'index'=>0]);
        $this->assertDatabaseHas('testparents_array_parentsarray',['id'=>$id,'target'=>$input_data['parentsarray'][2],'index'=>2]);

        $this->assertDatabaseHas('testparents_calc_parentcalc',['id'=>$id,'value'=>'101A']);
    }
    
    public function testTestChild()
    {
        $object = new TestChild();
        $test = new MysqlStorage($object);
        $input_data = [
            'parentint'=>101,
            'parentchar'=>'BAB',
            'parentfloat'=>1.01,
            'parenttext'=>'The ice is really cold, the streetlight really old',
            'parentenum'=>'testA',
            'parentdate'=>'2023-04-28',
            'parenttime'=>'10:07',
            'parentdatetime'=>'2023-04-28 10:07',
            'parentsarray'=>['ABC','DEF','GHI'],
            'parentoarray'=>[1,2,3,4],
            'parentcalc'=>'101A',
            'nosearch'=>100,
            'parentobject'=>1,

            'childint'=>202,
            'childchar'=>'CBC',
            'childfloat'=>2.02,
            'childtext'=>'Her childs all alone as she melts into her own',
            'childenum'=>'testB',
            'childdate'=>'2022-04-28',
            'childtime'=>'10:00',
            'childdatetime'=>'2022-04-28 10:00',
            'childsarray'=>['JKL','MNO','PQR'],
            'childoarray'=>[5,6,7,8],
            'childcalc'=>'202B',
            'childobject'=>2,            
        ];
        foreach ($input_data as $key => $value) {
            $test->setEntity($key,$value);
        }
        $id = $test->Store();
        $simple_data_parent = [
            'id'=>$id,
            'parentint'=>$input_data['parentint'],
            'parentchar'=>$input_data['parentchar'],
            'parentfloat'=>$input_data['parentfloat'],
            'parenttext'=>$input_data['parenttext'],
            'parentenum'=>$input_data['parentenum'],
            'parentdate'=>$input_data['parentdate'],
            'parenttime'=>$input_data['parenttime'],
            'parentdatetime'=>$input_data['parentdatetime'],
            'nosearch'=>$input_data['nosearch'],
            'parentobject'=>$input_data['parentobject'],
        ];
        $simple_data_child = [
            'id'=>$id,
            'childint'=>$input_data['childint'],
            'childchar'=>$input_data['childchar'],
            'childfloat'=>$input_data['childfloat'],
            'childtext'=>$input_data['childtext'],
            'childenum'=>$input_data['childenum'],
            'childdate'=>$input_data['childdate'],
            'childtime'=>$input_data['childtime'],
            'childdatetime'=>$input_data['childdatetime'],
            'childobject'=>$input_data['childobject']
        ];
        $data = DB::table('testchildren')->where('id',$id)->get();
        $this->assertDatabaseHas('testparents',$simple_data_parent);
        $this->assertDatabaseHas('testchildren',$simple_data_child);
        
        $this->assertDatabaseHas('testparents_array_parentoarray',['id'=>$id,'target'=>$input_data['parentoarray'][0],'index'=>0]);
        $this->assertDatabaseHas('testparents_array_parentoarray',['id'=>$id,'target'=>$input_data['parentoarray'][3],'index'=>3]);
        $this->assertDatabaseHas('testparents_array_parentsarray',['id'=>$id,'target'=>$input_data['parentsarray'][0],'index'=>0]);
        $this->assertDatabaseHas('testparents_array_parentsarray',['id'=>$id,'target'=>$input_data['parentsarray'][2],'index'=>2]);
        
        $this->assertDatabaseHas('testchildren_array_childoarray',['id'=>$id,'target'=>$input_data['childoarray'][0],'index'=>0]);
        $this->assertDatabaseHas('testchildren_array_childoarray',['id'=>$id,'target'=>$input_data['childoarray'][3],'index'=>3]);
        $this->assertDatabaseHas('testchildren_array_childsarray',['id'=>$id,'target'=>$input_data['childsarray'][0],'index'=>0]);
        $this->assertDatabaseHas('testchildren_array_childsarray',['id'=>$id,'target'=>$input_data['childsarray'][2],'index'=>2]);
        
        $this->assertDatabaseHas('testparents_calc_parentcalc',['id'=>$id,'value'=>'101A']);
        $this->assertDatabaseHas('testchildren_calc_childcalc',['id'=>$id,'value'=>'202B']);
    }
    
}
<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\Objects\ts_dummy;

class StorageInsertTest extends StorageBase {

    
    /**
     * @dataProvider InsertProvider
     * @group insert
     */
    public function testInsert($class,$init_callback,$fieldname,$expected) {
        $this->prepare_write();
        $object = new $class();
        $storage = new \Sunhill\ORM\Storage\storage_mysql($object);
        $init_callback($storage);
        $id = $storage->insert_object();        
        
        $readobject = new $class();
        $loader = new \Sunhill\ORM\Storage\storage_mysql($readobject);
        $loader->load_object($id);
        $this->assertEquals($expected,$this->get_field($loader,$fieldname));
        
    }
    
    public function InsertProvider() {
        return [
            ['Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($object) { $object->dummyint = 123; },'dummyint',123], // Einfacher Test mit simple Fields
            ['Sunhill\\ORM\\Tests\\Objects\\ts_testparent',function($object) { // Komplexere Simplefields
                $object->parentint = 234;
                $object->parentchar = 'ABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1974-09-15';
                $object->parenttime = '11:11:11';
                $object->parentdatetime = '2013-11-24 01:11:00';
                $object->parenttext = 'Lorem Ipsum';
            },'parentint',234],
            ['Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($object) { // Simplefields mit Vererbung
                $object->parentint = 1234;
                $object->parentchar = 'ZABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1975-09-15';
                $object->parenttime = '12:11:11';
                $object->parentdatetime = '2014-11-24 01:11:00';
                $object->parenttext = 'asLorem Ipsum';
            
                $object->childint = 2345;
                $object->childchar = 'ABCDEF';
                $object->childenum = 'TestB';
                $object->childfloat = 2.34;
                $object->childdate = '1974-09-16';
                $object->childtime = '11:11:12';
                $object->childdatetime = '2019-11-24 01:11:00';
                $object->childtext = 'Lorems Ipsums';
            },'parentint',1234],
            ['Sunhill\\ORM\\Tests\\Objects\\ts_testchild',function($object) { // Simplefields mit Vererbung
                $object->parentint = 1234;
                $object->parentchar = 'ZABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1975-09-15';
                $object->parenttime = '12:11:11';
                $object->parentdatetime = '2014-11-24 01:11:00';
                $object->parenttext = 'asLorem Ipsum';
                
                $object->childint = 2345;
                $object->childchar = 'ABCDEF';
                $object->childenum = 'TestB';
                $object->childfloat = 2.34;
                $object->childdate = '1974-09-16';
                $object->childtime = '11:11:12';
                $object->childdatetime = '2019-11-24 01:11:00';
                $object->childtext = 'Lorems Ipsums';
            },'childint',2345],
 /*           ['Sunhill\\ORM\\Tests\\Objects\\ts_passthru',function($object) { // Simplefields mit Objekt ohne Simplefields am Ende
                $object->parentint = 234;
                $object->parentchar = 'ABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1974-09-15';
                $object->parenttime = '11:11:11';
                $object->parentdatetime = '2013-11-24 01:11:00';
                $object->parenttext = 'Lorem Ipsum';
            },'parentint',234],
            ['Sunhill\\ORM\\Tests\\Objects\\ts_referenceonly',function($object) {
                $object->testobject = 234;
            },'testobject',234],
            ['Sunhill\\ORM\\Tests\\Objects\\ts_referenceonly',function($object) {
                $object->testoarray = [123,234,345];
            },'testoarray[1]',234], */
            ['Sunhill\\ORM\\Tests\\Objects\\ts_testparent',function($object) { // Komplexere Simplefields
                $object->parentint = 234;
                $object->parentchar = 'ABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1974-09-15';
                $object->parenttime = '11:11:11';
                $object->parentdatetime = '2013-11-24 01:11:00';
                $object->parenttext = 'Lorem Ipsum';
                $object->parentsarray = ['ABC','BCE','DEF'];
            },'parentsarray[1]','BCE'],
            ['Sunhill\\ORM\\Tests\\Objects\\ts_testparent',function($object) { // Komplexere Simplefields
                $object->parentint = 234;
                $object->parentchar = 'ABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1974-09-15';
                $object->parenttime = '11:11:11';
                $object->parentdatetime = '2013-11-24 01:11:00';
                $object->parenttext = 'Lorem Ipsum';
                $object->parentcalc = 'ABC1';
            },'parentcalc','ABC1'],
            ['Sunhill\\ORM\\Tests\\Objects\\ts_testparent',function($object) { // Komplexere Simplefields
                $object->parentint = 234;
                $object->parentchar = 'ABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1974-09-15';
                $object->parenttime = '11:11:11';
                $object->parentdatetime = '2013-11-24 01:11:00';
                $object->parenttext = 'Lorem Ipsum';
                $object->tags = [1,2,3];
            },'tags',[1,2,3]],
            ['Sunhill\\ORM\\Tests\\Objects\\ts_dummy',function($object) { 
                $object->dummyint = 123; 
                $object->attributes = ['int_attribute' =>['name'=>'int_attribute','type'=>'int','property'=>'','attribute_id'=>1,'value'=>999,'textvalue'=>'']];
            },'attributes[int_attribute][value]',999], // Einfacher Test mit simple Fields            
          ];
    }
    
    /**
     * @group externalhooks
     */
    public function testInsertHooks() {
        $this->prepare_write();
        $object = new ts_dummy();
        $storage = new \Sunhill\ORM\Storage\storage_mysql($object);
        $storage->dummyint = 123;
        $storage->set_entity('externalhooks', 
         [['target_id'=>2,'action'=>'PROPERTY_UPDATED','subaction'=>'dummyint','hook'=>'dummychanged','payload'=>'']]);
        $id = $storage->insert_object();
        
        $readobject = new ts_dummy();
        $loader = new \Sunhill\ORM\Storage\storage_mysql($readobject);
        $loader->load_object($id);
        $this->assertEquals('dummyint',$this->get_field($loader,'externalhooks[0][subaction]'));
    }
}

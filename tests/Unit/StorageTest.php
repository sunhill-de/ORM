<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class StorageTest extends sunhill_testcase_db
{

    static $is_prepared = false;
    
    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_special_table('passthrus');
        $this->create_special_table('testparents');
        $this->create_special_table('testchildren');
        $this->create_special_table('referenceonlies');        
    }
    
    protected function prepare_read() {
        $this->prepare_tables();
        $this->create_load_scenario();
    }
    
    protected function prepare_write() {
        $this->prepare_tables(); 
        $this->create_write_scenario();
    }
    
    /**
     * 
     * @param unknown $id
     * @param unknown $fieldname
     * @param unknown $expected
     * @dataProvider LoadProvider
     * @group load
     */
    public function testLoad($id,$class,$fieldname,$expected) {
        if (!self::$is_prepared) { // Bei Lesetests muss nicht immer neu initialisiert werden
            $this->prepare_read();
            self::$is_prepared = true;
        }
        $object = new $class();
        $loader = new \Sunhill\Storage\storage_mysql($object);
        $loader->load_object($id);
        $this->assertEquals($expected,$this->get_field($loader,$fieldname));
    }
    
    public function LoadProvider() {
        return [
            [1,'Sunhill\\Test\\ts_dummy','dummyint',123],                       // Wird ein einfaches Feld gelesen?
            [2,'Sunhill\\Test\\ts_dummy','dummyint',234],                       // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'Sunhill\\Test\\ts_dummy','tags',[1,2]],                         // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy','created_at','2019-05-15 10:00:00'],   // Werden die Felder aus oo_object ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy','tags[0]',1],                          // Werden Felder richtig indiziert
            [1,'Sunhill\\Test\\ts_dummy','attributes[int_attribute][value]',111],      // Werden Attribute richtig ausgelesen
            
            [5,'Sunhill\\Test\\ts_testparent','parentchar','ABC'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentint',123],                 // Werden intfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentfloat',1.23],              // Werden Floatfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parenttext','Lorem ipsum'],      // Werden Textfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentdatetime','1974-09-15 17:45:00'],              // Werden Zeitstempel gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentdate','1978-06-05'],                 // Werden Datums gelesen
            [5,'Sunhill\\Test\\ts_testparent','parenttime','01:11:00'],         // Werden Zeit gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentenum','testC'],            // Werden Enum gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentcalc','123A'],             // Werden calculierte Felder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentobject',1],                // Werden Objektfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentoarray',[2,3]],            // Werden Objektarrays gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentsarray',['ObjectString0','ObjectString1']],      // Werden StringArrays gelesen
            [5,'Sunhill\\Test\\ts_testparent','attributes[attribute1][value]',121],    // Werden Attribute gelesen
            
            
            [6,'Sunhill\\Test\\ts_testchild','parentchar','DEF'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentint',234],                 // Werden intfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentfloat',2.34],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parenttext','Upsala Dupsala'],      // Werden Textfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentdatetime','1970-09-11 18:00:00'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentdate','2013-11-24'],                 // Werden intfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parenttime','16:00:00'],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentenum','testB'],      // Werden Textfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childchar','GHI'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childint',345],                 // Werden intfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childfloat',3.45],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childtext','Norem Torem'],      // Werden Textfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childdatetime','1973-01-24 18:00:00'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childdate','2016-06-17'],                 // Werden intfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childtime','18:00:00'],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childenum','testA'],      // Werden Textfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentcalc','234A'],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentobject',3],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','parentoarray',[1,2]],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childobject',2],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childoarray',[3,4,1]],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','childsarray',['Child0','Child1','Child2']],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','attributes[attribute1][value]',232],              // Werden Floatfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','attributes[attribute2][value]',666],              // Werden Floatfelder gelesen
            
            [7,'Sunhill\\Test\\ts_passthru','id',7]                         // Werden Objekte ohne Simple-Fields geladen
        ];
    }
    
    /**
     * @dataProvider InsertProvider
     * @group insert
     */
    public function testInsert($class,$init_callback,$fieldname,$expected) {
        $this->prepare_write();
        $object = new $class();
        $storage = new \Sunhill\Storage\storage_mysql($object);
        $init_callback($storage);
        $id = $storage->insert_object();        
        
        $readobject = new $class();
        $loader = new \Sunhill\Storage\storage_mysql($readobject);
        $loader->load_object($id);
        $this->assertEquals($expected,$this->get_field($loader,$fieldname));
        
    }
    
    public function InsertProvider() {
        return [
            ['Sunhill\\Test\\ts_dummy',function($object) { $object->dummyint = 123; },'dummyint',123], // Einfacher Test mit simple Fields
            ['Sunhill\\Test\\ts_testparent',function($object) { // Komplexere Simplefields
                $object->parentint = 234;
                $object->parentchar = 'ABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1974-09-15';
                $object->parenttime = '11:11:11';
                $object->parentdatetime = '2013-11-24 01:11:00';
                $object->parenttext = 'Lorem Ipsum';
            },'parentint',234],
            ['Sunhill\\Test\\ts_testchild',function($object) { // Simplefields mit Vererbung
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
            ['Sunhill\\Test\\ts_testchild',function($object) { // Simplefields mit Vererbung
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
            ['Sunhill\\Test\\ts_passthru',function($object) { // Simplefields mit Objekt ohne Simplefields am Ende
                $object->parentint = 234;
                $object->parentchar = 'ABC';
                $object->parentenum = 'TestA';
                $object->parentfloat = 1.23;
                $object->parentdate = '1974-09-15';
                $object->parenttime = '11:11:11';
                $object->parentdatetime = '2013-11-24 01:11:00';
                $object->parenttext = 'Lorem Ipsum';
            },'parentint',234],
            ['Sunhill\\Test\\ts_referenceonly',function($object) {
                $object->testobject = 234;
            },'testobject',234],
            ['Sunhill\\Test\\ts_referenceonly',function($object) {
                $object->testoarray = [123,234,345];
            },'testoarray[1]',234],
            ['Sunhill\\Test\\ts_testparent',function($object) { // Komplexere Simplefields
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
            ['Sunhill\\Test\\ts_testparent',function($object) { // Komplexere Simplefields
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
            ['Sunhill\\Test\\ts_testparent',function($object) { // Komplexere Simplefields
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
            ['Sunhill\\Test\\ts_dummy',function($object) { 
                $object->dummyint = 123; 
                $object->attributes = ['int_attribute' =>['attribute_id'=>1,'value'=>999,'textvalue'=>'']];
            },'attributes[int_attribute][value]',999], // Einfacher Test mit simple Fields
            ];
    }
    
    /**
     * @group update
     * @dataProvider UpdateProvider
     */
    public function testUpdate($id,$class,$change_callback,$fieldname,$expected) {
        $this->prepare_read();
        $object = new $class();
        $changer = new \Sunhill\Storage\storage_mysql($object);
        $change_callback($changer);
        $changer->update_object($id);

        $readobject = new $class();
        $loader = new \Sunhill\Storage\storage_mysql($readobject);
        $loader->load_object($id);
        $this->assertEquals($expected,$this->get_field($loader,$fieldname));
    }
    
    public function UpdateProvider() {
        return [
            [1,'Sunhill\\Test\\ts_dummy',function($storage) {
                $storage->dummyint = 321;
            },'dummyint',321],                       // Wird ein einfaches Feld geändert?
            [5,'Sunhill\\Test\\ts_testparent',function($storage) {
                $storage->parentint = 321;
            },'parentint',321],                       // Wird ein einfaches Feld geändert?
            [5,'Sunhill\\Test\\ts_testparent',function($storage) {
                $storage->parentchar = 'OOO';
            },'parentchar','OOO'],                       // Wird ein einfaches Feld geändert?
            [5,'Sunhill\\Test\\ts_testparent',function($storage) {
                $storage->parentint = '999';
                $storage->parentchar = 'OOO';
            },'parentchar','OOO'],  
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentint = '999';
                $storage->parentchar = 'OOO';
                $storage->childint = '888';
                $storage->childchar = 'PPP';
            },'parentchar','OOO'],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentint = '999';
                $storage->parentchar = 'OOO';
                $storage->childint = '888';
                $storage->childchar = 'PPP';
            },'childchar','PPP'],
            [7,'Sunhill\\Test\\ts_passthru',function($storage) {
                $storage->parentint = '999';
                $storage->parentchar = 'OOO';
            },'parentchar','OOO'],
            [1,'Sunhill\\Test\\ts_dummy',function($storage) {
                $storage->attributes = ['int_attribute' => ['id'=>1,'attribute_id'=>1,'value'=>999,'textvalue'=>'']];
            },'attributes[int_attribute][value]',999],
            [1,'Sunhill\\Test\\ts_dummy',function($storage) {
                $storage->tags = [1,2,3];
            },'tags',[1,2,3]],                         // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy',function($storage) {
                $storage->tags = [1];
            },'tags',[1]],                         // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy',function($storage) {
                $storage->tags = [];
            },'tags',[]],                         // Werden die Tags vernünftig ausgelesen?
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentoarray = [1,2,3];
            },'parentoarray',[1,2,3]],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentoarray = [1];
            },'parentoarray',[1]],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentoarray = [];
            },'parentoarray',null],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentchar = 'ABCDEF';
            },'parentoarray',[1,2]],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentobject = 12;
            },'parentobject',12],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentsarray = ['TESTA','TESTB','TESTC'];
            },'parentsarray',['TESTA','TESTB','TESTC']],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentsarray = ['TESTA'];
            },'parentsarray',['TESTA']],
            [6,'Sunhill\\Test\\ts_testchild',function($storage) {
                $storage->parentsarray = [];
            },'parentsarray',null],
            ];
    }
    
    /**
     * @dataProvider DeleteProvider
     * @group delete
     * @param unknown $id
     * @param unknown $class
     * @param unknown $table
     * @param unknown $field
     */
    public function testDelete($id,$class,$table,$field) {
        $this->prepare_read();
        $object = new $class();
        $changer = new \Sunhill\Storage\storage_mysql($object);
        $before = empty(DB::table($table)->where($field,$id)->first());
        $changer->delete_object($id);        
        $after = empty(DB::table($table)->where($field,$id)->first());
        $this->assertEquals($before,!$after);
    }
    
    public function DeleteProvider() {
        return [
            [1,'Sunhill\\Test\\ts_dummy','dummies','id'],                       // Wird ein einfaches Feld gelesen?
            [1,'Sunhill\\Test\\ts_dummy','objects','id'],                       // Wird ein einfaches Feld gelesen?
            [2,'Sunhill\\Test\\ts_dummy','dummies','id'],                       // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'Sunhill\\Test\\ts_dummy','tagobjectassigns','container_id'],                         // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy','attributevalues','object_id'],      // Werden Attribute richtig ausgelesen
            
            [5,'Sunhill\\Test\\ts_testparent','testparents','id'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','caching','object_id'],             // Werden calculierte Felder gelesen
            [5,'Sunhill\\Test\\ts_testparent','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','objects','id'],            // Werden Objektarrays gelesen
            [5,'Sunhill\\Test\\ts_testparent','attributevalues','object_id'],    // Werden Attribute gelesen
            
            
            [6,'Sunhill\\Test\\ts_testchild','testchildren','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','testparents','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','objects','id'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','objectobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','stringobjectassigns','container_id'],                // Werden Objektfelder gelesen
            [6,'Sunhill\\Test\\ts_testchild','objects','id'],            // Werden Objektarrays gelesen
            [6,'Sunhill\\Test\\ts_testchild','attributevalues','object_id'],    // Werden Attribute gelesen
                        
            [7,'Sunhill\\Test\\ts_passthru','passthrus','id']                         // Werden Objekte ohne Simple-Fields geladen
        ];        
    }
}

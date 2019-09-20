<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;

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
        $this->insert_into('objects',['id','classname','created_at','updated_at'],
            [[1,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [2,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [3,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [4,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [5,"\\Sunhill\\Test\\ts_testparent",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [6,"\\Sunhill\\Test\\ts_testchild",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            [7,"\\Sunhill\\Test\\ts_referenceonly",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
         ]);
        $this->insert_into('dummies',['id','dummyint'],[[1,123],[2,234],[3,345],[4,456]]);
        $this->insert_into('testparents',['id','parentint','parentchar','parentfloat','parenttext','parentdatetime',
                                          'parentdate','parenttime','parentenum'],
            [[5,123,'ABC',1.23,'Lorem ipsum','1974-09-15 17:45:00','1978-06-05','01:11:00','testC'],
             [6,234,'DEF',2.34,'Upsala Dupsala','1970-09-11 18:00:00','2013-11-24','16:00:00','testB']
            ]);
        $this->insert_into('testchildren',['id','childint','childchar','childfloat','childtext','childdatetime',
            'childdate','childtime','childenum'],
            [
                [6,345,'GHI',3.45,'Norem Torem','1973-01-24 18:00:00','2016-06-17','18:00:00','testA']
            ]);
        $this->insert_into('tags',['id','created_at','updated_at','name','options','parent_id'],
            [
                [1,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagA',0,0],
                [2,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagB',0,0],
                [3,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagC',0,2],
                [4,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagD',0,0],
                [5,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagE',0,0],
                [6,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagF',0,0],
            ]);
        $this->insert_into('tagcache',['id','name','tag_id','created_at','updated_at'],
            [
                [1,'TagA',1,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,'TagB',2,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,'TagC',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,'TagC.TagB',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [5,'TagD',4,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [6,'TagE',5,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [7,'TagF',6,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('tagobjectassigns',['container_id','tag_id'],
            [
                [1,1],[1,2]
            ]);
    }
    
    /**
     * 
     * @param unknown $id
     * @param unknown $fieldname
     * @param unknown $expected
     * @dataProvider SimpleProvider
     */
    public function testSimple($id,$class,$fieldname,$expected) {
        if (!self::$is_prepared) { // Bei Lesetests muss nicht immer neu initialisiert werden
            $this->prepare_read();
            self::$is_prepared = true;
        }
        $object = new $class();
        $loader = new \Sunhill\Storage\storage_load($object);
        $loader->set_inheritance($object->get_inheritance(true));
        $loader->load_object($id);
        if (preg_match('/(?P<name>\w+)\[(?P<index>\d+)\]/',$fieldname,$match)){
            $name = $match['name'];
            $index = $match['index'];
            $this->assertEquals($expected,$loader->$name[$index]);
        } else if (preg_match('/(?P<name>\w+)->(?P<subfield>\w+)/',$fieldname,$match)) {
            $name = $match['name'];
            $subfield = $match['subfield'];
            $this->assertEquals($expected,$loader->$name->$subfield);
        } else {
            $this->assertEquals($expected,$loader->$fieldname);
        }
    }
    
    public function SimpleProvider() {
        return [
            [1,'Sunhill\\Test\\ts_dummy','dummyint',123],                       // Wird ein einfaches Feld gelesen?
            [2,'Sunhill\\Test\\ts_dummy','dummyint',234],                       // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'Sunhill\\Test\\ts_dummy','tags',[1,2]],                         // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy','created_at','2019-05-15 10:00:00'],   // Werden die Felder aus oo_object ausgelesen?
            [1,'Sunhill\\Test\\ts_dummy','tags[0]',1],                          // Werden Felder richtig indiziert
            [5,'Sunhill\\Test\\ts_testparent','parentchar','ABC'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentint',123],                 // Werden intfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentfloat',1.23],              // Werden Floatfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parenttext','Lorem ipsum'],      // Werden Textfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentdatetime','1974-09-15 17:45:00'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentdate','1978-06-05'],                 // Werden intfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parenttime','01:11:00'],              // Werden Floatfelder gelesen
            [5,'Sunhill\\Test\\ts_testparent','parentenum','testC'],      // Werden Textfelder gelesen

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
            
        ];
    }
}

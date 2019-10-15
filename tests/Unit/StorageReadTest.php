<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class StorageReadTest extends StorageBase
{

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
    
}

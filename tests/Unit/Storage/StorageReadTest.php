<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Sunhill\ORM\Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class StorageReadTest extends StorageBase
{

    protected function prepare_tables() {
        parent::prepare_tables();
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
        $loader = new \Sunhill\ORM\Storage\StorageMySQL($object);
        $loader->loadObject($id);
        $this->assertEquals($expected,$this->getField($loader,$fieldname));
    }
    
    public function LoadProvider() {
        return [
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','dummyint',123],                       // Wird ein einfaches Feld gelesen?
            [2,'Sunhill\\ORM\\Tests\\Objects\\Dummy','dummyint',234],                       // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','tags',[1,2]],                         // Werden die Tags vernünftig ausgelesen?
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','created_at','2019-05-15 10:00:00'],   // Werden die Felder aus ORMObject ausgelesen?
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','tags[0]',1],                          // Werden Felder richtig indiziert
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','attributes[int_attribute][value]',111],      // Werden Attribute richtig ausgelesen
            [1,'Sunhill\\ORM\\Tests\\Objects\\Dummy','externalhooks[0][hook]','dummyint_updated'],
            [2,'Sunhill\\ORM\\Tests\\Objects\\Dummy','externalhooks[0][hook]','dummyint2_updated'],
            
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentchar','ABC'],              // Werden Varcharfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentint',123],                 // Werden intfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentfloat',1.23],              // Werden Floatfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parenttext','Lorem ipsum'],      // Werden Textfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentdatetime','1974-09-15 17:45:00'],              // Werden Zeitstempel gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentdate','1978-06-05'],                 // Werden Datums gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parenttime','01:11:00'],         // Werden Zeit gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentenum','testC'],            // Werden Enum gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentcalc','123A'],             // Werden calculierte Felder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentobject',1],                // Werden Objektfelder gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentoarray',[2,3]],            // Werden Objektarrays gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','parentsarray',['ObjectString0','ObjectString1']],      // Werden StringArrays gelesen
            [5,'Sunhill\\ORM\\Tests\\Objects\\TestParent','attributes[attribute1][value]',121],    // Werden Attribute gelesen
            
            
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentchar','DEF'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentint',234],                 // Werden intfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentfloat',2.34],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parenttext','Upsala Dupsala'],      // Werden Textfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentdatetime','1970-09-11 18:00:00'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentdate','2013-11-24'],                 // Werden intfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parenttime','16:00:00'],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentenum','testB'],      // Werden Textfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childchar','GHI'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childint',345],                 // Werden intfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childfloat',3.45],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childtext','Norem Torem'],      // Werden Textfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childdatetime','1973-01-24 18:00:00'],              // Werden Varcharfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childdate','2016-06-17'],                 // Werden intfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childtime','18:00:00'],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childenum','testA'],      // Werden Textfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentcalc','234A'],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentobject',3],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','parentoarray',[1,2]],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childobject',2],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childoarray',[3,4,1]],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','childsarray',['Child0','Child1','Child2']],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','attributes[attribute1][value]',232],              // Werden Floatfelder gelesen
            [6,'Sunhill\\ORM\\Tests\\Objects\\TestChild','attributes[attribute2][value]',666],              // Werden Floatfelder gelesen
            
            [7,'Sunhill\\ORM\\Tests\\Objects\\Passthru','id',7]                         // Werden Objekte ohne Simple-Fields geladen
        ];
    }
    
}

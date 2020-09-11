<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;

class ObjectReadTest extends DBTestCase
{
    
    /**
     * @dataProvider ReadProvider
     * @group load
     * @param int $id
     * @param string $variable
     * @param unknown $expected_value
     */
    public function testRead(int $id,string $variable,$expected_value) {
        $object = \Sunhill\ORM\Objects\oo_object::load_object_of($id);
        $this->assertEquals($expected_value,$this->get_field($object,$variable));
    }
    
    public function ReadProvider() {
        return [
            [1,'dummyint',123],                       // Wird ein einfaches Feld gelesen?
            [2,'dummyint',234],                       // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,'created_at','2019-05-15 10:00:00'],   // Werden die Felder aus oo_object ausgelesen?
            [1,'tags[0]','TagA'],                     // Werden Felder richtig indiziert
            [1,'int_attribute',111],                  // Werden Attribute richtig ausgelesen
            
            [5,'parentchar','ABC'],              // Werden Varcharfelder gelesen
            [5,'parentint',123],                 // Werden intfelder gelesen
            [5,'parentfloat',1.23],              // Werden Floatfelder gelesen
            [5,'parenttext','Lorem ipsum'],      // Werden Textfelder gelesen
            [5,'parentdatetime','1974-09-15 17:45:00'],              // Werden Zeitstempel gelesen
            [5,'parentdate','1978-06-05'],                 // Werden Datums gelesen
            [5,'parenttime','01:11:00'],         // Werden Zeit gelesen
            [5,'parentenum','testC'],            // Werden Enum gelesen
            [5,'parentcalc','123A'],             // Werden calculierte Felder gelesen
            [5,'parentobject->dummyint',123],                // Werden Objektfelder gelesen
            [5,'parentoarray[1]->dummyint',345],            // Werden Objektarrays gelesen
            [5,'parentsarray[0]','ObjectString0'],      // Werden StringArrays gelesen
            [5,'attribute1',121],    // Werden Attribute gelesen
            
            
            [6,'parentchar','DEF'],              // Werden Varcharfelder gelesen
            [6,'parentint',234],                 // Werden intfelder gelesen
            [6,'parentfloat',2.34],              // Werden Floatfelder gelesen
            [6,'parenttext','Upsala Dupsala'],      // Werden Textfelder gelesen
            [6,'parentdatetime','1970-09-11 18:00:00'],              // Werden Varcharfelder gelesen
            [6,'parentdate','2013-11-24'],                 // Werden intfelder gelesen
            [6,'parenttime','16:00:00'],              // Werden Floatfelder gelesen
            [6,'parentenum','testB'],      // Werden Textfelder gelesen
            [6,'childchar','GHI'],              // Werden Varcharfelder gelesen
            [6,'childint',345],                 // Werden intfelder gelesen
            [6,'childfloat',3.45],              // Werden Floatfelder gelesen
            [6,'childtext','Norem Torem'],      // Werden Textfelder gelesen
            [6,'childdatetime','1973-01-24 18:00:00'],              // Werden Varcharfelder gelesen
            [6,'childdate','2016-06-17'],                 // Werden intfelder gelesen
            [6,'childtime','18:00:00'],              // Werden Floatfelder gelesen
            [6,'childenum','testA'],      // Werden Textfelder gelesen
            [6,'parentcalc','234A'],              // Werden Floatfelder gelesen
            [6,'parentobject->dummyint',345],              // Werden Floatfelder gelesen
            [6,'parentoarray[0]->dummyint',123],              // Werden Floatfelder gelesen
            [6,'childobject->dummyint',234],              // Werden Floatfelder gelesen
            [6,'childoarray[1]->dummyint',456],              // Werden Floatfelder gelesen
            [6,'childsarray[2]','Child2'],              // Werden Floatfelder gelesen
            [6,'attribute1',232],              // Werden Floatfelder gelesen
            [6,'attribute2',666],              // Werden Floatfelder gelesen
            
            [7,'parentint',321]                         // Werden Objekte ohne Simple-Fields geladen
        ];        
    }
}

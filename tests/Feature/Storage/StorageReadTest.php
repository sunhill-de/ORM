<?php

namespace Sunhill\ORM\Tests\Feature\Storage;

use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;


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
            [1,Dummy::class,'dummyint',123],                       // Wird ein einfaches Feld gelesen?
            [2,Dummy::class,'dummyint',234],                       // Wird ein einfaches Feld mit höherem Index gelesen?
            [1,Dummy::class,'tags',[1,2,4]],                         // Werden die Tags vernünftig ausgelesen?
            [1,Dummy::class,'created_at','2019-05-15 10:00:00'],   // Werden die Felder aus ORMObject ausgelesen?
            [1,Dummy::class,'tags[0]',1],                          // Werden Felder richtig indiziert
            [4,Dummy::class,'attributes[int_attribute][value]',5],      // Werden Attribute richtig ausgelesen
            
            [9,TestParent::class,'parentchar','ABC'],              // Werden Varcharfelder gelesen
            [9,TestParent::class,'parentint',111],                 // Werden intfelder gelesen
            [9,TestParent::class,'parentfloat',1.11],              // Werden Floatfelder gelesen
            [9,TestParent::class,'parenttext','Lorem ipsum'],      // Werden Textfelder gelesen
            [9,TestParent::class,'parentdatetime','1974-09-15 17:45:00'],              // Werden Zeitstempel gelesen
            [9,TestParent::class,'parentdate','1974-09-15'],                 // Werden Datums gelesen
            [9,TestParent::class,'parenttime','17:45:00'],         // Werden Zeit gelesen
            [9,TestParent::class,'parentenum','testC'],            // Werden Enum gelesen
            [9,TestParent::class,'parentcalc','111A'],             // Werden calculierte Felder gelesen
            [9,TestParent::class,'parentobject',1],                // Werden Objektfelder gelesen
            [9,TestParent::class,'parentoarray',[2,3]],            // Werden Objektarrays gelesen
            [9,TestParent::class,'parentsarray',['String A','String B']],      // Werden StringArrays gelesen
            [9,TestParent::class,'attributes[attribute1][value]',123],    // Werden Attribute gelesen
                        
            [17,TestChild::class,'parentchar','RRR'],              // Werden Varcharfelder gelesen
            [17,TestChild::class,'parentint',123],                 // Werden intfelder gelesen
            [17,TestChild::class,'parentfloat',1.23],              // Werden Floatfelder gelesen
            [17,TestChild::class,'parenttext','amet. Lorem ipsum dolo'],      // Werden Textfelder gelesen
            [17,TestChild::class,'parentdatetime','1978-06-05 11:45:00'],              // Werden Varcharfelder gelesen
            [17,TestChild::class,'parentdate','1978-06-05'],                 // Werden intfelder gelesen
            [17,TestChild::class,'parenttime','11:45:00'],              // Werden Floatfelder gelesen
            [17,TestChild::class,'parentenum','testC'],      // Werden Textfelder gelesen
            [17,TestChild::class,'childchar','WWW'],              // Werden Varcharfelder gelesen
            [17,TestChild::class,'childint',777],                 // Werden intfelder gelesen
            [17,TestChild::class,'childfloat',1.23],              // Werden Floatfelder gelesen
            [17,TestChild::class,'childtext','amet. Lorem ipsum dolo'],      // Werden Textfelder gelesen
            [17,TestChild::class,'childdatetime','1978-06-05 11:45:00'],              // Werden Varcharfelder gelesen
            [17,TestChild::class,'childdate','1978-06-05'],                 // Werden intfelder gelesen
            [17,TestChild::class,'childtime','11:45:00'],              // Werden Floatfelder gelesen
            [17,TestChild::class,'childenum','testC'],      // Werden Textfelder gelesen
            [17,TestChild::class,'parentcalc','123A'],              // Werden Floatfelder gelesen
            [17,TestChild::class,'parentobject',3],              // Werden Floatfelder gelesen
            [17,TestChild::class,'parentoarray',[4,5]],              // Werden Floatfelder gelesen
            [17,TestChild::class,'childobject',3],              // Werden Floatfelder gelesen
            [17,TestChild::class,'childoarray',[4,5]],              // Werden Floatfelder gelesen
            [17,TestChild::class,'childsarray',['OPQRSTU', 'VXYZABC']],              // Werden Floatfelder gelesen
            [17,TestChild::class,'attributes[attribute2][value]',543],              // Werden Floatfelder gelesen
            [17,TestChild::class,'attributes[attribute1][value]',654],              // Werden Floatfelder gelesen
            
            [25,TestSimpleChild::class,'id',25]                         // Werden Objekte ohne Simple-Fields geladen
        ];
    }
    
}

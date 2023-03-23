<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Facades\Objects;

class ObjectReadTest extends DatabaseTestCase
{
    
    /**
     * @dataProvider ReadProvider
     * @group load
     * @param int $id
     * @param string $variable
     * @param unknown $expected_value
     */
    public function testRead(int $id,string $variable,$expected_value) {
        $object = Objects::load($id);
        $this->assertEquals($expected_value,$this->getField($object,$variable));
    }
    
    public function ReadProvider() {
        return [
            [1,'dummyint',123],                         // Reading of a simple field
            [2,'dummyint',234],                         // Reading of a simple field with higher object id
            [1,'created_at','2019-05-15 10:00:00'],     // Are the timestamps read
            [1,'tags[0]','TagA'],                       // Are the indices right
            [4,'int_attribute',5],                      // Are attributes read correctly
            
            [9,'parentchar','ABC'],                     // Reading of varchar
            [9,'parentint',111],                        // Reading of integer
            [9,'parentfloat',1.11],                     // Reading of float
            [9,'parenttext','Lorem ipsum'],             // Reading of text
            [9,'parentdatetime','1974-09-15 17:45:00'], // Reading of datetime
            [9,'parentdate','1974-09-15'],              // Reading of dates
            [9,'parenttime','17:45:00'],                // Reading of time
            [9,'parentenum','testC'],                   // Reading of enum
            [9,'parentcalc','111A'],                    // Reading of calculated fields
            [9,'parentobject->dummyint',123],           // Reading of object fields
            [9,'parentoarray[1]->dummyint',123],        // Reading of object arrays
            [9,'parentsarray[0]','String A'],           // Reading of string arrays
            [9,'attribute1',123],                       // Reading of attributes
            
            
            [17,'parentchar','RRR'],              
            [17,'parentint',123],                 
            [17,'parentfloat',1.23],              
            [17,'parenttext','amet. Lorem ipsum dolo'],      
            [17,'parentdatetime','1978-06-05 11:45:00'],     
            [17,'parentdate','1978-06-05'],                 
            [17,'parenttime','11:45:00'],              
            [17,'parentenum','testC'],      
            [17,'childchar','WWW'],              
            [17,'childint',777],                 
            [17,'childfloat',1.23],              
            [17,'childtext','amet. Lorem ipsum dolo'],      
            [17,'childdatetime','1978-06-05 11:45:00'],     
            [17,'childdate','1978-06-05'],                 
            [17,'childtime','11:45:00'],              
            [17,'childenum','testC'],      
            [17,'parentcalc','123A'],      
            [17,'parentobject->dummyint',123],              
            [17,'parentoarray[0]->dummyint',456],           
            [17,'childobject->dummyint',123],              
            [17,'childoarray[1]->dummyint',123],           
            [17,'childsarray[1]','VXYZABC'],             
            [17,'attribute1',654],              
            [17,'attribute2',543],              
            
            [25,'parentint',999]                         
        ];        
    }
}

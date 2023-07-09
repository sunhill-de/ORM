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
    public function testRead(int $id,$expected_values) {
        $object = Objects::load($id);
        foreach ($expected_values as $variable => $expected_value) {
            $this->assertEquals($expected_value,$this->getField($object,$variable),"With field '$variable' the expected value doesn't match.");
        }
    }
    
    public function ReadProvider() {
        return [
            [1,['dummyint'=>123,'_created_at'=>'2019-05-15 10:00:00','tags[0]->name'=>'TagA']],                         // Reading of a simple field
            [2,['dummyint'=>234]],                         // Reading of a simple field with higher object id
            [4,['int_attribute'=>5]],                      // Are attributes read correctly
            
            [9,[
                'parentchar'=>'ABC',
                'parentint'=>111,
                'parentfloat'=>1.11,
                'parenttext'=>'Lorem ipsum',
                'parentdatetime'=>'1974-09-15 17:45:00', // Reading of datetime
                'parentdate'=>'1974-09-15',              // Reading of dates
                'parenttime'=>'17:45:00',                // Reading of time
                'parentenum'=>'testC',                   // Reading of enum
                'parentcalc'=>'111A',                    // Reading of calculated fields
                'parentobject->dummyint'=>123,           // Reading of object fields
                'parentoarray[1]->dummyint'=>123,        // Reading of object arrays
                'parentsarray[0]'=>'String A',           // Reading of string arrays
                'attribute1'=>123,                       // Reading of attributes
               ]
            ],
            
            [17,[
                'parentchar'=>'RRR',              
                'parentint'=>123,                 
                'parentfloat'=>1.23,              
                'parenttext'=>'amet. Lorem ipsum dolo',      
                'parentdatetime'=>'1978-06-05 11:45:00',     
                'parentdate'=>'1978-06-05',                 
                'parenttime'=>'11:45:00',              
                'parentenum'=>'testC',      
                'childchar'=>'WWW',              
                'childint'=>777,                 
                'childfloat'=>1.23,              
                'childtext'=>'amet. Lorem ipsum dolo',      
                'childdatetime'=>'1978-06-05 11:45:00',     
                'childdate'=>'1978-06-05',                 
                'childtime'=>'11:45:00',              
                'childenum'=>'testC',      
                'parentcalc'=>'123A',      
                'parentobject->dummyint'=>123,              
                'parentoarray[0]->dummyint'=>456,           
                'childobject->dummyint'=>123,              
                'childoarray[1]->dummyint'=>456,           
                'childsarray[1]'=>'VXYZABC',             
                'attribute1'=>654,              
                'attribute2'=>543,  
                ]
            ],
            [25,['parentint'=>999]]                         
        ];        
    }
}

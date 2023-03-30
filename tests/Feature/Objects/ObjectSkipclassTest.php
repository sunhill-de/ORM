<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Facades\Objects;

class ObjectSkipclassTest extends DatabaseTestCase
{
    
     public function testSkipclass() {
        $init_object = new TestSimpleChild();
        $init_object->parentchar ='ABC';
        $init_object->parentint = 123;
        $init_object->parentfloat = 1.23;
        $init_object->parenttext = 'ABC DEF';
        $init_object->parentdatetime = '2001-01-01 01:01:01';
        $init_object->parentdate = '2011-01-01';
        $init_object->parenttime = '11:11:11';
        $init_object->parentenum = 'testA';
        $init_object->nosearch = 1;
        $init_object->commit();

        Objects::flushCache();
        $read_object = Objects::load($init_object->getID());
        $this->assertEquals(123,$read_object->parentint);
        $read_object->parentint = 4312;
        $read_object->commit();
        
        Objects::flushCache();
        $reread_object = Objects::load($init_object->getID());
        $this->assertEquals(4312,$reread_object->parentint);
        
	}
	
}

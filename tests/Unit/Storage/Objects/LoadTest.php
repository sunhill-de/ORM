<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;

class LoadTest extends DatabaseTestCase
{
    
    /**
     * @group loadobject
     * @group object
     * @group load
     */
    public function testDummy()
    {
        $test = new Dummy();
        
        $test->load(1);
        
        $this->assertEquals(123,$test->dummyint);
        $this->assertEquals('a123',$test->_uuid);
        $this->assertEquals('2019-05-15 10:00:00',$test->_created_at);
        $this->assertEquals(444,$test->general_attribute);
        $this->assertEquals(3,count($test->tags));
        $this->assertEquals(4,$test->tags[2]->getID());
    }

    /**
     * @group loadobject
     * @group object
     * @group load
     */
    public function testTestParent()
    {
        $test = new TestParent();
        
        $test->load(9);
        
        $this->assertEquals(111,$test->parentint);
        $this->assertEquals('ABC',$test->parentchar);
        $this->assertEquals(1.11,$test->parentfloat);
        $this->assertEquals('Lorem ipsum',$test->parenttext);
        $this->assertEquals('1974-09-15 17:45:00',$test->parentdatetime);
        $this->assertEquals('1974-09-15',$test->parentdate);
        $this->assertEquals('17:45:00',$test->parenttime);
        $this->assertEquals('testC',$test->parentenum);
        $this->assertEquals('111A',$test->parentcalc);
        $this->assertEquals(1,$test->parentobject->getID());
        $this->assertEquals(7,$test->parentcollection->getID());
        $this->assertEquals(123,$test->attribute1);
        $this->assertEquals(222,$test->attribute2);
        $this->assertEquals(2,count($test->parentsarray));
        $this->assertEquals(2,count($test->parentoarray));
        $this->assertEquals(2,count($test->parentmap));
        $this->assertEquals(3,count($test->tags));        
        $this->assertEquals('some.path.to9',$test->getProperty('parentinformation')->getPath());
    }
    
    /**
     * @group loadobject
     * @group object
     * @group load
     */
    public function testTestChild()
    {
        $test = new TestChild();
        
        $test->load(17);

        $this->assertEquals(123,$test->parentint);
        $this->assertEquals('RRR',$test->parentchar);
        $this->assertEquals(true,$test->parentbool);
        $this->assertEquals(1.23,$test->parentfloat);
        $this->assertEquals('amet. Lorem ipsum dolo',$test->parenttext);
        $this->assertEquals('1978-06-05 11:45:00',$test->parentdatetime);
        $this->assertEquals('1978-06-05',$test->parentdate);
        $this->assertEquals('11:45:00',$test->parenttime);
        $this->assertEquals('testC',$test->parentenum);        
        $this->assertEquals('123A',$test->parentcalc);
        $this->assertEquals(777,$test->childint);
        $this->assertEquals('WWW',$test->childchar);
        $this->assertEquals(1.23,$test->childfloat);
        $this->assertEquals('amet. Lorem ipsum dolo',$test->childtext);        
        $this->assertEquals('1978-06-05 11:45:00',$test->childdatetime);
        $this->assertEquals('1978-06-05',$test->childdate);
        $this->assertEquals('11:45:00',$test->childtime);
        $this->assertEquals('testC',$test->childenum);
        $this->assertEquals('777B',$test->childcalc);            
        $this->assertEquals(3,$test->childobject->getID());
        $this->assertEquals(9,$test->childcollection->getID());
        $this->assertEquals(4,$test->parentcollection->getID());
        $this->assertEquals(3,$test->parentobject->getID());               
        $this->assertEquals('some.path.to17',$test->getProperty('parentinformation')->getPath());
        $this->assertEquals('path.to.child17',$test->getProperty('childinformation')->getPath());
        $this->assertEquals('HIJKLMN',$test->parentsarray[1]);
        $this->assertEquals(5,$test->parentoarray[1]->getID());
        $this->assertEquals(4,$test->childoarray[1]->getID());
        $this->assertEquals('DEF',$test->parentmap['KeyC']);
        $this->assertEquals(2,$test->tags[1]->getID());
        $this->assertEquals(543,$test->attribute2);
    }
    
    /**
     * @group loadobject
     * @group object
     * @group load
     */
    public function testSimpleChild()
    {
        $test = new TestSimpleChild();
        
        $test->load(25);
        
        $this->assertEquals(999, $test->parentint);
    }

    /**
     * @group loadobject
     * @group object
     * @group load
     */
    public function testReferenceOnly()
    {
        $test = new ReferenceOnly();
        
        $test->load(27);
        
        $this->assertEquals('Test B', $test->testsarray[1]);
    }
    
}
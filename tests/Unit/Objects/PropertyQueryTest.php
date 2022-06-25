<?php
/**
 *
 * @file PropertyQueryTest.php
 * Tests the functions of PropertyQuery
 * Lang en
 * Reviewstate: 2020-08-12
 */

namespace Sunhill\ORM\Tests\Unit\Objects;

use Illuminate\Foundation\Testing\WithFaker;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\PropertyQuery\PropertyQuery;
use Sunhill\ORM\Properties\Property;

class PropertyQueryTest extends DBTestCase
{
    
      // Tests a simple get on a PropertyQuery
      public function testGetAllProperties()
      {
          $prop1 = new Property();
          $prop1->setName('prop1');
          $prop2 = new Property();
          $prop2->setName('prop2');
        
          $test = new PropertyQuery([$prop1,$prop2]);
          $result = $test->get();
          $this->assertEquals(2,count($result));
          $this->assertEquals('prop1',$result[0]->getName());
          $this->assertEquals('prop2',$result[1]->getName());
      }

      // Tests a filtered query
      public function testGetFilteredProperties()
      {
          $prop1 = new Property();
          $prop1->setName('prop1');
          $prop2 = new Property();
          $prop2->setName('prop2');
        
          $test = new PropertyQuery([$prop1,$prop2]);
          $result = $test->where('name','prop2')->get();
          $this->assertEquals(1,count($result));
          $this->assertEquals('prop2',$result[0]->getName());
      }  
  
      // Tests a combined filtered query
      public function testGetCombinedFilteredProperties()
      {
          $prop1 = new Property();
          $prop1->setName('prop1');
          $prop2 = new Property();
          $prop2->setName('prop2');
          $prop3 = new Property();
          $prop3->setName('prop3')->setReadonly(true);
        
          $test = new PropertyQuery([$prop1,$prop2]);
          $result = $test->whereNot('name','prop2')->where('readonly',false)->get();
          $this->assertEquals(1,count($result));
          $this->assertEquals('prop1',$result[0]->getName());
      }  
  
      // Tests a combined filtered query
      public function testGroupByProperties()
      {
          $prop1 = new Property();
          $prop1->setName('prop1');
          $prop2 = new Property();
          $prop2->setName('prop2');
          $prop3 = new Property();
          $prop3->setName('prop3')->setReadonly(true);
        
          $test = new PropertyQuery([$prop1,$prop2,$prop3]);
          $result = $test->groupBy('readonly')->get();
          $this->assertEquals(2,count($result));
          $this->assertEquals('prop1',$result[false][0]->getName());
          $this->assertEquals('prop2',$result[false][1]->getName());
          $this->assertEquals('prop3',$result[true][0]->getName());
      }  
  
  
}

<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Search\QueryException;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Utils\ObjectList;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\ORMException;

use Sunhill\ORM\Tests\Testobjects\CalcClass;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\SecondLevelChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestSimpleChild;
use Sunhill\ORM\Tests\Testobjects\ThirdLevelChild;

class ObjectSearchTest extends DatabaseTestCase
{

    protected function simplify_result(Collection $result) {
        $return = [];
        foreach ($result as $obj) {
            $return[] = $obj->getID();
        }
        return $return;
    }
    
    
    
    public function SimpleProvider() {
        return [
          // Test of integer fields
           
        ];
    }
    
    /**
     * @group object
     */
    public function testPassObject() {
        $test = Objects::load(1);
        $result = $this->simplify_result(TestParent::search()->where('parentobject','=',$test)->get());
        $this->assertEquals([9],$result);
        
    }
    
    /**
     * @group Focus
     */
    public function testGetFirst() {
         $result = TestParent::search()->where('parentchar','=','DEF')->first();
        $this->assertEquals(10,$result);        
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithOneResult() {
        $result = TestParent::search()->where('parentint','=','111')->first();
        $this->assertEquals(9,$result);
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithNoResult() {
        $result = TestParent::search()->where('parentint','=','776')->first();
        $this->assertEquals(null,$result);
    }
    
    /**
     * @dataProvider ComplexProvider
     * @group complex
     */
    public function testComplexSearchIDs($searchclass,$field1,$relation1,$value1,$field2,$relation2,$value2,$expect) {
        
        $classname = "\\Sunhill\\ORM\\Tests\\Testobjects\\".$searchclass;
         $result = $this->simplify_result($classname::search()->where($field1,$relation1,$value1)->where($field2,$relation2,$value2)->get());
        $this->assertEquals($expect,$result);
        
        
    }
    
    public function ComplexProvider() {
        return [
            ["TestParent",'parentint','<',200,'parentint', '<>','123',[9]],
            ["TestParent",'parentint','=',123,'parentchar','=', 'DEF',[10]],            
            ["TestChild", 'parentint','>',300,'childint',  '=', '777',[24]],             
            ["TestParent",'tags','has','TagD','parentint','<',200,[9,17]],            
            ["TestParent",'tags','has','TagA','tags','has','TagB',[17]],            
            ["TestParent",'parentcalc','=','123A','tags','has','TagF.TagG.TagE',[10,12]],            
            ["TestParent",'parentobject','=',2,'parentint','<','700',[20]],            
            ["TestChild","childoarray","empty",null,'parentsarray','has not','ABCD',[19,21,22,23]],
            ["TestParent",'parentsarray','has','HALLO','parentsarray','has','HELLO',[19]],
        ];
    }
    
}

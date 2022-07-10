<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Sunhill\ORM\Objects\ORMObject;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Utils\ObjectList;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\ORMException;
use Database\Seeders\SearchSeeder;

use Sunhill\ORM\Tests\Objects\Dummy;

use Sunhill\ORM\Tests\Objects\SearchtestA;
use Sunhill\ORM\Tests\Objects\SearchtestB;
use Sunhill\ORM\Tests\Objects\SearchtestC;

class ObjectSearchTest extends DBTestCase
{
    protected function do_seeding() {
        $this->seed(SearchSeeder::class);
    }
    
    protected function simplify_result(ObjectList $result) {
        $return = [];
        for($i=0;$i<count($result);$i++) {
            $return[] = $result[$i]->getID($i);
        }
        return $return;
    }
    
    public function testSearchWithNoConditionSingleResult() {
        $result = $this->simplify_result(\Sunhill\ORM\Tests\Objects\SearchtestC::search()->get());
        $this->assertEquals([15],$result);
    }
    
    public function testSearchWithNoConditionMultipleResult() {
        $result = $this->simplify_result(\Sunhill\ORM\Tests\Objects\SearchtestB::search()->get());
        $this->assertEquals([10,11,12,13,14,15],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithNoConditionOrder() {
        $result = $this->simplify_result(\Sunhill\ORM\Tests\Objects\SearchtestB::search()->orderBy('Bchar')->get());
        $this->assertEquals([10,14,11,12,13,15],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithConditionOrder() {
        $result = $this->simplify_result(\Sunhill\ORM\Tests\Objects\SearchtestB::search()->where('Bint','<',602)->orderBy('Bchar','desc')->get());
        $this->assertEquals([11,10],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithCombinedConditionOrder() {
        $result = $this->simplify_result(\Sunhill\ORM\Tests\Objects\SearchtestB::search()->where('Bint','<',603)->where('Aint','<',502)->orderBy('Bchar',false)->get());
        $this->assertEquals([11,10],$result);
    }
    
    /**
     * @group limit
     */
    public function testSearchWithLimit() {
        $result = $this->simplify_result(\Sunhill\ORM\Tests\Objects\SearchtestB::search()->limit(2,2)->get());
        $this->assertEquals([12,13],$result);
    }
    
    /**
     * @group count
     */
    public function testCountSingleResult() {
        $result = \Sunhill\ORM\Tests\Objects\SearchtestC::search()->count();
        $this->assertEquals(1,$result);
    }
    
    /**
     * @group bug
     * @group count
     */
    public function testCountWithObjectCondition() {
        $result = \Sunhill\ORM\Tests\Objects\SearchtestA::search()->where('Aobject','=',1)->count();
        $this->assertEquals(2,$result);
    }
    
    /**
     * @group count
     */   
    public function testCountMultipleResult() {
        $result = \Sunhill\ORM\Tests\Objects\SearchtestB::search()->count();
        $this->assertEquals(6,$result);
    }
    
    public function testFailSearch() {
        $this->expectException(\Sunhill\ORM\Search\QueryException::class);
        SearchtestA::search()->where('Anosearch','=',1)->get();
    }
  
    /**
     * @dataProvider SimpleProvider
     * @group simple
     */
    public function testSimpleSearchIDs($searchclass,$field,$relation,$value,$expect) {
        $classname = "\\Sunhill\\ORM\\Tests\\Objects\\".$searchclass;
        $result = $this->simplify_result($classname::search()->where($field,$relation,$value)->get());
        $this->assertEquals($expect,$result);
    }
    
    public function SimpleProvider() {
        return [
            ["SearchtestA",'Aint','=',111,[5]],
            ["SearchtestA",'Aint','=',5,[]],
            ["SearchtestA",'Aint','<',300,[5,6]],
            ["SearchtestA",'Aint','>',900,[8,9]],
            ["SearchtestB",'Bint','<>',602,[10,11,14,15]],
            ["SearchtestB",'Bint','!=',602,[10,11,14,15]],
            ["SearchtestA",'Aint','<',502,[5,6,7,10,11]],
            ["SearchtestC",'Bint','=',603,[15]],
            ["SearchtestA",'Aint','in',[111,222],[5,6]],
            
            ["SearchtestA",'Achar','=','ADE',[6]],
            ["SearchtestA",'Achar','=','ABC',[5,11]],
            ["SearchtestB",'Achar','=','ABC',[11]],
            ["SearchtestA",'Achar','=','NÜX',[]],
            ["SearchtestA",'Achar','<','B',[5,6,11]],
            ["SearchtestA",'Achar','>','X',[8,9]],
            ["SearchtestB",'Bchar','<>','CCC',[10,11,13,14,15]],
            ["SearchtestA",'Achar','<','GGH',[5,6,7,10,11,15]],
            ["SearchtestC",'Achar','=','GGG',[15]],
            ["SearchtestA",'Achar','in',['GGT','GGZ'],[12,13]],
            
            ["SearchtestA",'Achar','begins with','A',[5,6,11]],
            ["SearchtestA",'Achar','begins with','B',[7]],
            ["SearchtestA",'Achar','begins with','2',[]],
            ["SearchtestA",'Achar','ends with','Z',[8,13]],
            ["SearchtestA",'Achar','ends with','T',[12]],
            ["SearchtestA",'Achar','ends with','2',[]],
            
            ["SearchtestB",'Bchar','consists','D',[13,14]],
            ["SearchtestB",'Bchar','consists','C',[10,12,13]],
            ["SearchtestB",'Bchar','consists','G',[15]],
            ["SearchtestB",'Bchar','consists','2',[]],
            
            ["SearchtestA",'Acalc','=','222=ADE',[6]],
            ["SearchtestA",'Acalc','=','666=RRR',[]],
            ["SearchtestA",'Acalc','begins with','503',[14,15]],
            ["SearchtestA",'Acalc','begins with','666',[]],
            ["SearchtestA",'Acalc','begins with','222',[6]],
            ["SearchtestA",'Acalc','ends with','ADE',[6]], 
             
            ["SearchtestA",'tags','has','TagA',[5,6]],
            ["SearchtestA",'tags','has','TagB.TagC',[6]],
            ["SearchtestA",'tags','has','TagD',[]],
            ["SearchtestA",'tags','has not','TagA',[7,8,9,10,11,12,13,14,15]],
            ["SearchtestA",'tags','one of',['TagE','TagF'],[5,6]],
            ["SearchtestA",'tags','none of',['TagE'],[6,7,8,9,10,11,12,13,14,15]],
            ["SearchtestA",'tags','all of',['TagA','TagE'],[5]], 

            ["SearchtestA",'Asarray','has','testA',[7,8]],
            ["SearchtestA",'Asarray','has','testC',[8,13]],
            ["SearchtestA",'Asarray','has','testC',[8,13]],
            ["SearchtestB",'Asarray','has','testC',[13]],
            ["SearchtestA",'Asarray','has','testD',[]],
            ["SearchtestA",'Asarray','has not','testA',[5,6,9,10,11,12,13,14,15]],
            ["SearchtestA",'Asarray','one of',['testB','testC'],[7,8,13]],
            ["SearchtestA",'Asarray','none of',['testC','testA'],[5,6,9,10,11,12,14,15]],
            ["SearchtestA",'Asarray','all of',['testC','testA'],[8]], 
            ["SearchtestA",'Asarray','empty',null,[5,6,9,10,11,12,14,15]],
            
            ["SearchtestA",'Aobject','=',1,[7,13]],
            ["SearchtestA","Aobject",'=',2,[8]],
            ["SearchtestB","Aobject","=",1,[13]],
            ["SearchtestA","Aobject","in",[1,2],[7,8,13]],
            ["SearchtestA","Aobject","=",null,[5,6,9,10,11,12,14,15]],
            
            ["SearchtestA","Aoarray","has",3,[9]],
            ["SearchtestA","Aoarray","has",1,[]],
            ["SearchtestA","Aoarray","one of",[3,1],[9]],
            ["SearchtestA","Aoarray","all of",[3,4],[9]],
            ["SearchtestA","Aoarray","none of",[3,4],[5,6,7,8,10,11,12,13,14,15]],
            ["SearchtestB","Boarray","empty",null,[10,11,12,14,15]],
        ];
    }
    
    /**
     * @group object
     */
    public function testPassObject() {
        $test = Objects::load(1);
        $result = $this->simplify_result(\Sunhill\ORM\Tests\Objects\SearchtestA::search()->where('Aobject','=',$test)->get());
        $this->assertEquals([7,13],$result);
        
    }
    
    public function testGetFirst() {
         $result = \Sunhill\ORM\Tests\Objects\SearchtestA::search()->where('Achar','=','ABC')->first();
        $this->assertEquals(5,$result);        
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithOneResult() {
        $result = \Sunhill\ORM\Tests\Objects\SearchtestA::search()->where('Aint','=','111')->first();
        $this->assertEquals(5,$result);
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithNoResult() {
        $result = \Sunhill\ORM\Tests\Objects\SearchtestA::search()->where('Aint','=','666')->first();
        $this->assertEquals(null,$result);
    }
    
    /**
     * @dataProvider ComplexProvider
     * @group complex
     */
    public function testComplexSearchIDs($searchclass,$field1,$relation1,$value1,$field2,$relation2,$value2,$expect) {
         $classname = "\\Sunhill\\ORM\\Tests\\Objects\\".$searchclass;
         $result = $this->simplify_result($classname::search()->where($field1,$relation1,$value1)->where($field2,$relation2,$value2)->get());
        $this->assertEquals($expect,$result);
    }
    
    public function ComplexProvider() {
        return [
            ["SearchtestA",'Aint','<',300,'Aint','<>','222',[5]],
            ["SearchtestA",'Aint','<',300,'Achar','=','ABC',[5]],
            ["SearchtestB",'Aint','>',300,'Bint','=','602',[12,13]], 
            ["SearchtestA",'tags','has','TagA','Aint','<>',222,[5]],
            ["SearchtestA",'tags','has','TagA','tags','has','TagC',[6]],
            ["SearchtestA",'Acalc','<>','111=ABC','tags','has','TagA',[6]],
            ["SearchtestA",'Aobject','=',1,'Aint','<','502',[7]],
            ["SearchtestB","Boarray","empty",null,'Asarray','has','testC',[]],
            ["SearchtestA",'Asarray','has','testA','Asarray','has','testC',[8]],
        ];
    }
    
    /**
     * @group regression
     */
    public function testSearcRegression() {
        $test = new SearchtestA();
        $test->unify();
        $this->assertTrue(true);
    }
    
    /**
     * @dataProvider SearchFieldProvider
     */
    public function testSearchKeyfield($class,$keyfield,$expect) {
        $classname = 'Sunhill\\ORM\\Tests\\Objects\\'.$class;
        $search = $classname::SearchKeyfield($keyfield);
        if ($expect == 0) {
            $this->assertNull($search);
        } else {
            $this->assertEquals($search->getID(),$expect);
        }
    }
    
    public function SearchFieldProvider() {
        return [
            ['SearchtestA','111 ABC',5],
            ['SearchtestA','502 GGT',12],
            ['SearchtestA','999 ZZZ',0],
            ['SearchtestB','601 BBB',11],
            ['SearchtestB','111 ABC',10],
            ['SearchtestB','603 ADD',14],
            ['SearchtestB','502 GGT',0],
        ];
    }
    
    public function testSearchkeyfieldException() {
        $this->expectException(ORMException::class);
        Dummy::SearchKeyfield('Keyfield');
    }
}

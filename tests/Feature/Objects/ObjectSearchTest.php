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
    
    public function testSearchWithNoCondition_oneresult()
    {
        $this->assertEquals([31], $this->simplify_result(CalcClass::search()->get()));    
    }
    
    public function testSearchWithNoCondition_moreresults() {
       $this->assertEquals([25,26],$this->simplify_result(TestSimpleChild::search()->get()));
    }

    public function testSearchWithNoCondition_noresult()
    {
        $this->assertEquals([],$this->simplify_result(ThirdLevelChild::search()->get()));        
    }
    /**
     * @group order
     */
    public function testSearchWithNoConditionOrder() {
        $result = $this->simplify_result(Dummy::search()->orderBy('dummyint')->get());
        $this->assertEquals([1,3,5,2,4,6,7,8],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithConditionOrder() {
        $result = $this->simplify_result(Dummy::search()->where('dummyint','>',500)->orderBy('dummyint','desc')->get());
        $this->assertEquals([8,7,6],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithCombinedConditionOrder() {
        $result = $this->simplify_result(TestParent::search()->where('parentint','<',600)->where('parentchar','<','EEE')->orderBy('parentchar')->get());
        $this->assertEquals([9,23,10,13,21],$result);
    }
    
    /**
     * @group limit
     */
    public function testSearchWithLimit() {
        $result = $this->simplify_result(TestParent::search()->limit(2,2)->get());
        $this->assertEquals([11,12],$result);
    }
    
    /**
     * @group count
     */
    public function testCount_oneresult() {
        $this->assertEquals(1,CalcClass::search()->count());
    }
    
    /**
     * @group count
     */
    public function testCount_noresult() {
        $this->assertEquals(0,ThirdLevelChild::search()->count());
    }
    
    /**
     * @group count
     */
    public function testCount_multipleresult() {
        $this->assertEquals(2,SecondLevelChild::search()->count());
    }
    
    /**
     * @group bug
     * @group count
     */
    public function testCountWithObjectCondition() 
    {
        $this->assertEquals(5,TestParent::search()->where('parentchar','=','DEF')->count());
    }
    
    public function testFailSearch() {
        $this->expectException(QueryException::class);
        TestParent::search()->where('nosearch','=',1)->get();
    }
  
    /**
     * @dataProvider SimpleProvider
     * @group simple
     */
    public function testSimpleSearchIDs($searchclass,$field,$relation,$value,$expect) {
        $classname = "\\Sunhill\\ORM\\Tests\\Testobjects\\".$searchclass;
        $result = $this->simplify_result($classname::search()->where($field,$relation,$value)->get());
        $this->assertEquals($expect,$result);
    }
    
    public function SimpleProvider() {
        return [
          // Test of integer fields
            ["TestParent",'parentint','=',123,[10,12,17,26]],
            ["TestParent",'parentint','=',111,[9]],
            ["TestParent",'parentint','=',5,[]],
            ["TestParent",'parentint','<',234,[9,10,11,12,17,26]],
            ["TestParent",'parentint','<=',234,[9,10,11,12,13,17,26]],
            ["TestParent",'parentint','>',800,[19,25]],
            ["TestParent",'parentint','>=',800,[18,19,25]],
            ["TestParent","parentint", "<>", 123, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            ["TestParent","parentint", "in", [111,123],[9,10,12,17,26]],            
            ["TestChild", 'parentint','=',123,[17]],
            ["TestChild", 'parentint','=',5,[]],
            ["TestChild", 'parentint','<',400,[17,23]],
            ["TestChild", 'parentint','>',700,[18,19,24]],
            ["TestChild", "parentint", "!=", 123, [18,19,20,21,22,23,24]],
            ["TestChild", "parentint", "<>", 123, [18,19,20,21,22,23,24]],
            ["TestChild", "parentint", "in", [800,123],[17,18]],            
            ["TestChild", 'childint','=',801,[18]],
            ["TestChild", 'childint','=',5,[]],
            ["TestChild", 'childint','<',350,[21,22,23]],
            ["TestChild", 'childint','>',800,[18,19]],
            ["TestChild", "childint", "<>", 112, [17,18,19,20,22,23,24]],
            ["TestChild", "childint", "in", [112,321],[21,22]],
         // Test of float fields            
            ["TestParent",'parentfloat','=',1.23,[10,12,17,26]],
            ["TestParent",'parentfloat','=',1.11,[9]],
            ["TestParent",'parentfloat','=',5,[]],
            ["TestParent",'parentfloat','<',2.34,[9,10,11,12,17,26]],
            ["TestParent",'parentfloat','<=',2.34,[9,10,11,12,13,17,26]],
            ["TestParent",'parentfloat','>',8,[19,25]],
            ["TestParent",'parentfloat','>=',8,[18,19,25]],
            ["TestParent","parentfloat", "<>", 1.23, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            ["TestParent","parentfloat", "in", [1.11,1.23],[9,10,12,17,26]],
            ["TestChild", 'parentfloat','=',1.23,[17]],
            ["TestChild", 'parentfloat','=',5,[]],
            ["TestChild", 'parentfloat','<',4,[17,23]],
            ["TestChild", 'parentfloat','>',7,[18,19,24]],
            ["TestChild", "parentfloat", "<>", 1.23, [18,19,20,21,22,23,24]],
            ["TestChild", "parentfloat", "in", [8,1.23],[17,18]],
            ["TestChild", 'childfloat','=',1.23,[17]],
            ["TestChild", 'childfloat','=',5,[]],
            ["TestChild", 'childfloat','<',3.50,[17,23]],
            ["TestChild", 'childfloat','>',7,[18,19,24]],
            ["TestChild", "childfloat", "<>", 1.23, [18,19,20,21,22,23,24]],
            ["TestChild", "childfloat", "in", [1.23,6.66],[17,20]],
         // Test of varchar fields   
            ["TestParent",'parentchar','=',"DEF",[10,13,18,21,25]],
            ["TestParent",'parentchar','=',"EEE",[12]],
            ["TestParent",'parentchar','=',"WWW",[]],
            ["TestParent",'parentchar','<',"DEF",[9,23]],
            ["TestParent",'parentchar','<=',"DEF",[9,10,13,18,21,23,25]],
            ["TestParent",'parentchar','>',"XZT",[19,20]],
            ["TestParent",'parentchar','>=',"XZT",[15,19,20]],
            ["TestParent","parentchar", "<>", "DEF", [9,11,12,14,15,17,19,20,22,23]],
            ["TestParent","parentchar", "!=", "DEF", [9,11,12,14,15,17,19,20,22,23]],
            ["TestParent","parentchar", "in", ["DEF","ABC"],[9,10,13,18,21,25]],
            ["TestParent","parentchar", "begins with", "A",[9,23]],
            ["TestParent","parentchar", "begins with", "AB",[9]],
            ["TestParent","parentchar", "begins with", "K",[]],
            ["TestParent","parentchar", "ends with", "T",[14,15]],
            ["TestParent","parentchar", "ends with", "C",[9]],
            ["TestParent","parentchar", "ends with", "K",[]],            
            ["TestParent","parentchar", "consists", "B",[9]],
            ["TestParent","parentchar", "consists", "R",[17,22,23]],
            ["TestParent","parentchar", "consists", "K",[]],
            ["TestChild",'parentchar','=',"DEF",[18,21]],
            ["TestChild",'parentchar','=',"ZZZ",[19]],
            ["TestChild",'parentchar','=',"WWW",[]],
            ["TestChild",'parentchar','<',"CCC",[23]],
            ["TestChild",'parentchar','>',"YYY",[19,20]],
            ["TestChild","parentchar", "<>", "DEF", [17,19,20,22,23]],
            ["TestChild","parentchar", "in", ["DEF","ABC"],[18,21]],
            ["TestChild","parentchar", "begins with", "D",[18,21]],
            ["TestChild","parentchar", "begins with", "A",[23]],
            ["TestChild","parentchar", "begins with", "K",[]],
            ["TestChild","parentchar", "ends with", "F",[18,21]],
            ["TestChild","parentchar", "ends with", "O",[20]],
            ["TestChild","parentchar", "ends with", "K",[]],
            ["TestChild","parentchar", "consists", "O",[20]],
            ["TestChild","parentchar", "consists", "R",[17,22,23]],
            ["TestChild","parentchar", "consists", "K",[]],            
            ["TestChild",'childchar','=',"DEF",[18,21]],
            ["TestChild",'childchar','=',"WWW",[17]],
            ["TestChild",'childchar','=',"QQQ",[]],
            ["TestChild",'childchar','<',"EEE",[18,21]],
            ["TestChild",'childchar','>',"QQQ",[17,19,20,22,23]],
            ["TestChild","childchar", "<>", "DEF", [17,19,20,22,23]],
            ["TestChild","childchar", "in", ["DEF","WWW"],[17,18,21]],
            ["TestChild","childchar", "begins with", "D",[18,21]],
            ["TestChild","childchar", "begins with", "Q",[23]],
            ["TestChild","childchar", "begins with", "K",[]],
            ["TestChild","childchar", "ends with", "F",[18,21]],
            ["TestChild","childchar", "ends with", "O",[20]],
            ["TestChild","childchar", "ends with", "K",[]],
            ["TestChild","childchar", "consists", "O",[20]],
            ["TestChild","childchar", "consists", "W",[17,22,23]],
            ["TestChild","childchar", "consists", "K",[]],            
            // enum 
            ["TestParent", "parentenum", "=", "testA", [12]],
            ["TestParent", "parentenum", "=", "testB", [10,15,18,22,26]],
            ["TestChild", "parentenum", "=", "testB", [18,22]],
            ["TestChild", "childenum", "=", "testA", []],
            ["TestChild", "childenum", "=", "testB", [18,22]],
            // Calculated fields
            ["TestParent",'parentcalc','=',"123A",[10,12,17,26]],
            ["TestParent",'parentcalc','=',"111A",[9]],
            ["TestParent",'parentcalc','=',"5A",[]],            
            ["TestParent",'parentcalc','<',"300A",[9,10,11,12,13,17,26]],
            ["TestParent",'parentcalc','>',"800A",[19,25]],                        
            ["TestParent",'parentcalc','<=',"123A",[9,10,12,17,26]],
            ["TestParent",'parentcalc','>=',"800A",[18,19,25]],            
            ["TestParent","parentcalc", "<>", "123A", [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            ["TestParent","parentcalc", "!=", "123A", [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],            
            ["TestParent","parentcalc", "in", ["111A","123A"],[9,10,12,17,26]],
            ["TestParent","parentcalc", "begins with", "2",[11,13]],
            ["TestParent","parentcalc", "ends with", "3A",[10,12,17,24,26]],
            ["TestParent","parentcalc", "contains", "5",[14,21,23]],            
            ["TestChild",'parentcalc','=',"123A",[17]],
            ["TestChild",'parentcalc','=',"432A",[22]],
            ["TestChild",'parentcalc','=',"5A",[]],
            ["TestChild",'parentcalc','<',"300A",[17]],
            ["TestChild",'parentcalc','>',"800A",[19]],
            ["TestChild",'parentcalc','<=',"345A",[17,23]],
            ["TestChild",'parentcalc','>=',"800A",[18,19]],
            ["TestChild","parentcalc", "<>", "123A", [18,19,20,21,22,23,24]],
            ["TestChild","parentcalc", "!=", "123A", [18,19,20,21,22,23,24]],
            ["TestChild","parentcalc", "in", ["800A","123A"],[17,18]],
            ["TestChild","parentcalc", "begins with", "5",[21]],
            ["TestChild","parentcalc", "ends with", "3A",[17,24]],
            ["TestChild","parentcalc", "contains", "8",[18,21]],           
            ["TestChild",'childcalc','=',"777B",[17,24]],
            ["TestChild",'childcalc','=',"900B",[19]],
            ["TestChild",'childcalc','=',"123A",[]],            
            ["TestChild",'childcalc','<',"340B",[21,22]],
            ["TestChild",'childcalc','>',"800B",[18,19]],
            ["TestChild",'childcalc','<=',"321B",[21,22]],
            ["TestChild",'childcalc','>=',"801B",[18,19]],
            ["TestChild","childcalc", "<>", "777B", [18,19,20,21,22,23]],
            ["TestChild","childcalc", "!=", "777B", [18,19,20,21,22,23]],
            ["TestChild","childcalc", "in", ["801B","777B"],[17,18,24]],
            ["TestChild","childcalc", "begins with", "8",[18]],
            ["TestChild","childcalc", "ends with", "1B",[18,22]],
            ["TestChild","childcalc", "contains", "2",[21,22]],
            // tags
            ["TestParent",'tags','has','TagA',[12,17,22]],
            ["TestParent",'tags','has','TagB.TagC',[9,12,19,22]],
            ["TestParent",'tags','has','TagZ',[]],            
            ["TestParent",'tags','has not','TagB.TagC',[10,11,13,14,15,16,17,18,20,21,23,24,25,26]],            
            ["TestParent",'tags','one of',['TagE','TagF'],[9,10,11,12,19,20,21,22]],            
            ["TestParent",'tags','none of',['TagF','TagE'],[13,14,15,16,17,18,23,24,25,26]],
            ["TestParent",'tags','all of',['TagA','TagB'],[17]], 
            ["TestChild",'tags','has','TagD',[17,19]],
            ["TestChild",'tags','one of',['TagE','TagF'],[19,20,21,22]],
            ["TestChild",'tags','none of',['TagF','TagE'],[17,18,23,24]],
            ["TestChild",'tags','all of',['TagA','TagB'],[17]], 
            
            ["TestParent", "parentsarray", "has", "DEFG", [10,14]],
            ["TestParent", "parentsarray", "has", "Non existing", []],
            ["TestParent", "parentsarray", "has", "Muse", [22]],            
            ["TestParent", "parentsarray", "has not", "ABCD", [9,11,12,14,15,16,17,18,19,20,21,22,23,24,25,26]],
            ["TestParent", "parentsarray", "one of", ["ABCD", "DEFG"], [10,13,14]],
            ["TestParent", "parentsarray", "none of", ["ABCD", "DEFG","ZZZZ"], [9,11,12,15,16,17,18,19,20,21,23,24,25,26]],
            ["TestParent", "parentsarray", "all of", ["ABCD", "DEFG"], [10]],
            ["TestParent", "parentsarray", "empty", null, [12,15,16,20,21,23,24,25,26]],            
            ["TestChild", "parentsarray", "has", "DEFG", []],
            ["TestChild", "parentsarray", "has", "Muse", [22]],            
            ["TestChild", "parentsarray", "has not", "ABCD", [17,18,19,20,21,22,23,24]],
            ["TestChild", "parentsarray", "one of", ["HELLO","Iron Maiden"], [19,22]],
            ["TestChild", "parentsarray", "none of", ["HALLO","ZZZZ"], [17,18,20,21,23,24]],
            ["TestChild", "parentsarray", "all of", ["HALLO","HOLA"], [19]],
            ["TestChild", "parentsarray", "empty", null, [20,21,23,24]],            
            ["TestChild", "childsarray", "has", "ABCD", [20]],
            ["TestChild", "childsarray", "has", "Non existing", []],
            ["TestChild", "childsarray", "has", "Muse", []],
            ["TestChild", "childsarray", "has not", "ABCD", [17,18,19,21,22,23,24]],
            ["TestChild", "childsarray", "one of", ["ABCD","Yea"], [18,20]],
            ["TestChild", "childsarray", "none of", ["ABCD","Yea","OPQRSTU"], [19,21,22,23,24]],
            ["TestChild", "childsarray", "all of", ["Yea","Yupp"], [18]],
            ["TestChild", "childsarray", "empty", null, [19,21,22,23]],
            // Object fields                        
            ["TestParent",'parentobject','=',1,[9]],
            ["TestParent","parentobject","=",3,[17]],
            ["TestParent","parentobject",'=',4,[10,12,18]],
            ["TestParent","parentobject",'=',null,[13,14,15,16,21,22,23,24,26]],
            ["TestParent","parentobject","!=",null,[9,10,11,12,17,18,19,20,25]],
            ["TestParent","parentobject","in",[1,3,4],[9,10,12,17,18]],            
            ["TestChild","parentobject","=",3,[17]],
            ["TestChild","parentobject",'=',4,[18]],
            ["TestChild","parentobject",'=',null,[21,22,23,24]],
            ["TestChild","parentobject","!=",null,[17,18,19,20]],
            ["TestChild","parentobject","in",[1,3,4],[17,18]],            
            ["TestChild","childobject","=",3,[17,19]],
            ["TestChild","childobject",'=',4,[18]],
            ["TestChild","childobject",'=',null,[20,21,22,23,24]],
            ["TestChild","childobject","!=",null,[17,18,19]],
            ["TestChild","childobject","in",[3,4],[17,18,19]],
            // array of objects
            ["TestParent", "parentoarray","has",3,[9,10,13,18]],
            ["TestParent", "parentoarray","has",8,[]], 
            ["TestParent", "parentoarray","has",7,[11,19]],
            ["TestParent", "parentoarray","one of",[1,2],[9,10,11,13,14,18,19]],            
            ["TestParent", "parentoarray","all of",[1,2],[10,13,18]],
            ["TestParent", "parentoarray","all of",[1,7],[]],
            ["TestParent", "parentoarray","none of",[1,2,3],[12,15,16,17,20,21,22,23,24,25,26]],
            ["TestParent", "parentoarray","empty",null,     [12,15,16,20,21,23,24,25,26]],
            
            ["TestChild", "parentoarray","has",3,[18]],
            ["TestChild", "parentoarray","has",8,[]],
            ["TestChild", "parentoarray","one of",[1,2],[18,19]],
            ["TestChild", "parentoarray","all of",[1,2],[18]],
            ["TestChild", "parentoarray","all of",[1,7],[]],
            ["TestChild", "parentoarray","none of",[1,2,3],[17,20,21,22,23,24]],
            ["TestChild", "parentoarray","empty",null,     [20,21,23,24]],
            
            ["TestChild", "childoarray","has",1,[20,24]],
            ["TestChild", "childoarray","has",8,[]],
            ["TestChild", "childoarray","one of",[3,4],[17,20]],
            ["TestChild", "childoarray","all of",[4,5],[17]],
            ["TestChild", "childoarray","all of",[1,7],[]],
    //@todo no expected result        ["TestChild", "childoarray","none of",[1,2,3],[17,18,19,21,22,23,25]],
            ["TestChild", "childoarray","empty",null,     [19,21,22,23]],
            
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

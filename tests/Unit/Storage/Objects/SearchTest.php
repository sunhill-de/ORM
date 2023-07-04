<?php

namespace Sunhill\ORM\Tests\Unit\Storage\Objects;

use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Tests\Testobjects\DummyChild;
use Sunhill\ORM\Tests\Testobjects\TestParent;
use Sunhill\ORM\Tests\Testobjects\TestChild;
use Sunhill\ORM\Storage\Mysql\MysqlStorage;
use Sunhill\ORM\Tests\Testobjects\ReferenceOnly;

class SearchTest extends DatabaseTestCase
{
    
    protected function getQuery($class)
    {
        $object = new $class();
        $storage = new MysqlStorage();
        $storage->setCollection($object);
        
        return $storage->dispatch('query');
    }
    
    public function testNoCondition()
    {
        $query = $this->getQuery(Dummy::class)->get();
        $this->assertEquls(8, count($query));
        $this->assertEquals([1,2,3,4,5,6,7,8], $query->toArray());        
    }
    
    public function testOffsetAndLimit()
    {
        $query = $this->getQuery(Dummy::class)->offset(3)->limit(3)->get();
        $this->assertEquls(3, count($query));
        $this->assertEquals([3,4,5], $query->toArray());        
    }

    public function testFirst()
    {
        $query = $this->getQuery(Dummy::class)->first();
        $this->assertEquals(1, $query);
    }
    
    public function testGetObjects()
    {
        $query = $this->getQuery(Dummy::class)->getObjects();
        $this->assertEquals(8, count($query));
        $this->assertEquals(1, $query[0]->getID());
    }
    
    public function testFirstObjects()
    {
        $query = $this->getQuery(Dummy::class)->firstObject();
        $this->assertEquals(1, $query->getID());
    }

    public function testCount()
    {
        $query = $this->getQuery(Dummy::class)->count();
        $this->assertEquals(8, $query);
    }
    
    public function testOrderAsc()
    {
        $query = $this->getQuery(Dummy::class)->order('dummyint')->get();
        $this->assertEquls(8, count($query));
        $this->assertEquals([1,3,5,2,4,6,7,8], $query->toArray());        
    }
    
    public function testOrderDesc()
    {
        $query = $this->getQuery(Dummy::class)->order('dummyint')->get();
        $this->assertEquls(8, count($query));
        $this->assertEquals([8,7,6,4,2,5,3,1], $query->toArray());
    }
    
    /**
     * @dataProvider SearchSimpleProvider
     */    
    public function testSearchSimple($class, $field, $relation, $condition, $expect)
    {
        $query = $this->getQuery($class)->where($field, $relation, $condition)->get();
        $this->assertEquals($expect, $query->toArray());
    }
    
    public function SearchSimpleProvider()
    {
        return [
            [Dummy::class, 'dummyint', '=', 234, [2]],
            [Dummy::class, 'dummyint', '=', 123, [1,3,5]],
            [Dummy::class, 'dummyint', '=', 999, []],            

            // Test of integer fields
            [TestParent::class,'parentint','=',123,[10,12,17,26]],
            [TestParent::class,'parentint','=',111,[9]],
            [TestParent::class,'parentint','=',5,[]],
            [TestParent::class,'parentint','<',234,[9,10,11,12,17,26]],
            [TestParent::class,'parentint','<=',234,[9,10,11,12,13,17,26]],
            [TestParent::class,'parentint','>',800,[19,25]],
            [TestParent::class,'parentint','>=',800,[18,19,25]],
            [TestParent::class,"parentint", "<>", 123, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class,"parentint", "in", [111,123],[9,10,12,17,26]],
            [TestChild::class, 'parentint','=',123,[17]],
            [TestChild::class, 'parentint','=',5,[]],
            [TestChild::class, 'parentint','<',400,[17,23]],
            [TestChild::class, 'parentint','>',700,[18,19,24]],
            [TestChild::class, "parentint", "!=", 123, [18,19,20,21,22,23,24]],
            [TestChild::class, "parentint", "<>", 123, [18,19,20,21,22,23,24]],
            [TestChild::class, "parentint", "in", [800,123],[17,18]],
            [TestChild::class, 'childint','=',801,[18]],
            [TestChild::class, 'childint','=',5,[]],
            [TestChild::class, 'childint','<',350,[21,22,23]],
            [TestChild::class, 'childint','>',800,[18,19]],
            [TestChild::class, "childint", "<>", 112, [17,18,19,20,22,23,24]],
            [TestChild::class, "childint", "in", [112,321],[21,22]],
            
            // Test of float fields
            [TestParent::class,'parentfloat','=',1.23,[10,12,17,26]],
            [TestParent::class,'parentfloat','=',1.11,[9]],
            [TestParent::class,'parentfloat','=',5,[]],
            [TestParent::class,'parentfloat','<',2.34,[9,10,11,12,17,26]],
            [TestParent::class,'parentfloat','<=',2.34,[9,10,11,12,13,17,26]],
            [TestParent::class,'parentfloat','>',8,[19,25]],
            [TestParent::class,'parentfloat','>=',8,[18,19,25]],
            [TestParent::class,"parentfloat", "<>", 1.23, [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class,"parentfloat", "in", [1.11,1.23],[9,10,12,17,26]],
            [TestChild::class, 'parentfloat','=',1.23,[17]],
            [TestChild::class, 'parentfloat','=',5,[]],
            [TestChild::class, 'parentfloat','<',4,[17,23]],
            [TestChild::class, 'parentfloat','>',7,[18,19,24]],
            [TestChild::class, "parentfloat", "<>", 1.23, [18,19,20,21,22,23,24]],
            [TestChild::class, "parentfloat", "in", [8,1.23],[17,18]],
            [TestChild::class, 'childfloat','=',1.23,[17]],
            [TestChild::class, 'childfloat','=',5,[]],
            [TestChild::class, 'childfloat','<',3.50,[17,23]],
            [TestChild::class, 'childfloat','>',7,[18,19,24]],
            [TestChild::class, "childfloat", "<>", 1.23, [18,19,20,21,22,23,24]],
            [TestChild::class, "childfloat", "in", [1.23,6.66],[17,20]],
            // Test of varchar fields
            [TestParent::class,'parentchar','=',"DEF",[10,13,18,21,25]],
            [TestParent::class,'parentchar','=',"EEE",[12]],
            [TestParent::class,'parentchar','=',"WWW",[]],
            [TestParent::class,'parentchar','<',"DEF",[9,23]],
            [TestParent::class,'parentchar','<=',"DEF",[9,10,13,18,21,23,25]],
            [TestParent::class,'parentchar','>',"XZT",[19,20]],
            [TestParent::class,'parentchar','>=',"XZT",[15,19,20]],
            [TestParent::class,"parentchar", "<>", "DEF", [9,11,12,14,15,17,19,20,22,23]],
            [TestParent::class,"parentchar", "!=", "DEF", [9,11,12,14,15,17,19,20,22,23]],
            [TestParent::class,"parentchar", "in", ["DEF","ABC"],[9,10,13,18,21,25]],
            [TestParent::class,"parentchar", "begins with", "A",[9,23]],
            [TestParent::class,"parentchar", "begins with", "AB",[9]],
            [TestParent::class,"parentchar", "begins with", "K",[]],
            [TestParent::class,"parentchar", "ends with", "T",[14,15]],
            [TestParent::class,"parentchar", "ends with", "C",[9]],
            [TestParent::class,"parentchar", "ends with", "K",[]],
            [TestParent::class,"parentchar", "consists", "B",[9]],
            [TestParent::class,"parentchar", "consists", "R",[17,22,23]],
            [TestParent::class,"parentchar", "consists", "K",[]],
            [TestChild::class,'parentchar','=',"DEF",[18,21]],
            [TestChild::class,'parentchar','=',"ZZZ",[19]],
            [TestChild::class,'parentchar','=',"WWW",[]],
            [TestChild::class,'parentchar','<',"CCC",[23]],
            [TestChild::class,'parentchar','>',"YYY",[19,20]],
            [TestChild::class,"parentchar", "<>", "DEF", [17,19,20,22,23]],
            [TestChild::class,"parentchar", "in", ["DEF","ABC"],[18,21]],
            [TestChild::class,"parentchar", "begins with", "D",[18,21]],
            [TestChild::class,"parentchar", "begins with", "A",[23]],
            [TestChild::class,"parentchar", "begins with", "K",[]],
            [TestChild::class,"parentchar", "ends with", "F",[18,21]],
            [TestChild::class,"parentchar", "ends with", "O",[20]],
            [TestChild::class,"parentchar", "ends with", "K",[]],
            [TestChild::class,"parentchar", "consists", "O",[20]],
            [TestChild::class,"parentchar", "consists", "R",[17,22,23]],
            [TestChild::class,"parentchar", "consists", "K",[]],
            [TestChild::class,'childchar','=',"DEF",[18,21]],
            [TestChild::class,'childchar','=',"WWW",[17]],
            [TestChild::class,'childchar','=',"QQQ",[]],
            [TestChild::class,'childchar','<',"EEE",[18,21]],
            [TestChild::class,'childchar','>',"QQQ",[17,19,20,22,23]],
            [TestChild::class,"childchar", "<>", "DEF", [17,19,20,22,23]],
            [TestChild::class,"childchar", "in", ["DEF","WWW"],[17,18,21]],
            [TestChild::class,"childchar", "begins with", "D",[18,21]],
            [TestChild::class,"childchar", "begins with", "Q",[23]],
            [TestChild::class,"childchar", "begins with", "K",[]],
            [TestChild::class,"childchar", "ends with", "F",[18,21]],
            [TestChild::class,"childchar", "ends with", "O",[20]],
            [TestChild::class,"childchar", "ends with", "K",[]],
            [TestChild::class,"childchar", "consists", "O",[20]],
            [TestChild::class,"childchar", "consists", "W",[17,22,23]],
            [TestChild::class,"childchar", "consists", "K",[]],
            // enum
            [TestParent::class, "parentenum", "=", "testA", [12]],
            [TestParent::class, "parentenum", "=", "testB", [10,15,18,22,26]],
            [TestChild::class, "parentenum", "=", "testB", [18,22]],
            [TestChild::class, "childenum", "=", "testA", []],
            [TestChild::class, "childenum", "=", "testB", [18,22]],
            // Calculated fields
            [TestParent::class,'parentcalc','=',"123A",[10,12,17,26]],
            [TestParent::class,'parentcalc','=',"111A",[9]],
            [TestParent::class,'parentcalc','=',"5A",[]],
            [TestParent::class,'parentcalc','<',"300A",[9,10,11,12,13,17,26]],
            [TestParent::class,'parentcalc','>',"800A",[19,25]],
            [TestParent::class,'parentcalc','<=',"123A",[9,10,12,17,26]],
            [TestParent::class,'parentcalc','>=',"800A",[18,19,25]],
            [TestParent::class,"parentcalc", "<>", "123A", [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class,"parentcalc", "!=", "123A", [9,11,13,14,15,16,18,19,20,21,22,23,24,25]],
            [TestParent::class,"parentcalc", "in", ["111A","123A"],[9,10,12,17,26]],
            [TestParent::class,"parentcalc", "begins with", "2",[11,13]],
            [TestParent::class,"parentcalc", "ends with", "3A",[10,12,17,24,26]],
            [TestParent::class,"parentcalc", "contains", "5",[14,21,23]],
            [TestChild::class,'parentcalc','=',"123A",[17]],
            [TestChild::class,'parentcalc','=',"432A",[22]],
            [TestChild::class,'parentcalc','=',"5A",[]],
            [TestChild::class,'parentcalc','<',"300A",[17]],
            [TestChild::class,'parentcalc','>',"800A",[19]],
            [TestChild::class,'parentcalc','<=',"345A",[17,23]],
            [TestChild::class,'parentcalc','>=',"800A",[18,19]],
            [TestChild::class,"parentcalc", "<>", "123A", [18,19,20,21,22,23,24]],
            [TestChild::class,"parentcalc", "!=", "123A", [18,19,20,21,22,23,24]],
            [TestChild::class,"parentcalc", "in", ["800A","123A"],[17,18]],
            [TestChild::class,"parentcalc", "begins with", "5",[21]],
            [TestChild::class,"parentcalc", "ends with", "3A",[17,24]],
            [TestChild::class,"parentcalc", "contains", "8",[18,21]],
            [TestChild::class,'childcalc','=',"777B",[17,24]],
            [TestChild::class,'childcalc','=',"900B",[19]],
            [TestChild::class,'childcalc','=',"123A",[]],
            [TestChild::class,'childcalc','<',"340B",[21,22]],
            [TestChild::class,'childcalc','>',"800B",[18,19]],
            [TestChild::class,'childcalc','<=',"321B",[21,22]],
            [TestChild::class,'childcalc','>=',"801B",[18,19]],
            [TestChild::class,"childcalc", "<>", "777B", [18,19,20,21,22,23]],
            [TestChild::class,"childcalc", "!=", "777B", [18,19,20,21,22,23]],
            [TestChild::class,"childcalc", "in", ["801B","777B"],[17,18,24]],
            [TestChild::class,"childcalc", "begins with", "8",[18]],
            [TestChild::class,"childcalc", "ends with", "1B",[18,22]],
            [TestChild::class,"childcalc", "contains", "2",[21,22]],
            // tags
            [TestParent::class,'tags','has','TagA',[12,17,22]],
            [TestParent::class,'tags','has','TagB.TagC',[9,12,19,22]],
            [TestParent::class,'tags','has','TagZ',[]],
            [TestParent::class,'tags','has not','TagB.TagC',[10,11,13,14,15,16,17,18,20,21,23,24,25,26]],
            [TestParent::class,'tags','one of',['TagE','TagF'],[9,10,11,12,19,20,21,22]],
            [TestParent::class,'tags','none of',['TagF','TagE'],[13,14,15,16,17,18,23,24,25,26]],
            [TestParent::class,'tags','all of',['TagA','TagB'],[17]],
            [TestChild::class,'tags','has','TagD',[17,19]],
            [TestChild::class,'tags','one of',['TagE','TagF'],[19,20,21,22]],
            [TestChild::class,'tags','none of',['TagF','TagE'],[17,18,23,24]],
            [TestChild::class,'tags','all of',['TagA','TagB'],[17]],
            
            [TestParent::class, "parentsarray", "has", "DEFG", [10,14]],
            [TestParent::class, "parentsarray", "has", "Non existing", []],
            [TestParent::class, "parentsarray", "has", "Muse", [22]],
            [TestParent::class, "parentsarray", "has not", "ABCD", [9,11,12,14,15,16,17,18,19,20,21,22,23,24,25,26]],
            [TestParent::class, "parentsarray", "one of", ["ABCD", "DEFG"], [10,13,14]],
            [TestParent::class, "parentsarray", "none of", ["ABCD", "DEFG","ZZZZ"], [9,11,12,15,16,17,18,19,20,21,23,24,25,26]],
            [TestParent::class, "parentsarray", "all of", ["ABCD", "DEFG"], [10]],
            [TestParent::class, "parentsarray", "empty", null, [12,15,16,20,21,23,24,25,26]],
            [TestChild::class, "parentsarray", "has", "DEFG", []],
            [TestChild::class, "parentsarray", "has", "Muse", [22]],
            [TestChild::class, "parentsarray", "has not", "ABCD", [17,18,19,20,21,22,23,24]],
            [TestChild::class, "parentsarray", "one of", ["HELLO","Iron Maiden"], [19,22]],
            [TestChild::class, "parentsarray", "none of", ["HALLO","ZZZZ"], [17,18,20,21,23,24]],
            [TestChild::class, "parentsarray", "all of", ["HALLO","HOLA"], [19]],
            [TestChild::class, "parentsarray", "empty", null, [20,21,23,24]],
            [TestChild::class, "childsarray", "has", "ABCD", [20]],
            [TestChild::class, "childsarray", "has", "Non existing", []],
            [TestChild::class, "childsarray", "has", "Muse", []],
            [TestChild::class, "childsarray", "has not", "ABCD", [17,18,19,21,22,23,24]],
            [TestChild::class, "childsarray", "one of", ["ABCD","Yea"], [18,20]],
            [TestChild::class, "childsarray", "none of", ["ABCD","Yea","OPQRSTU"], [19,21,22,23,24]],
            [TestChild::class, "childsarray", "all of", ["Yea","Yupp"], [18]],
            [TestChild::class, "childsarray", "empty", null, [19,21,22,23]],
            // Object fields
            [TestParent::class,'parentobject','=',1,[9]],
            [TestParent::class,"parentobject","=",3,[17]],
            [TestParent::class,"parentobject",'=',4,[10,12,18]],
            [TestParent::class,"parentobject",'=',null,[13,14,15,16,21,22,23,24,26]],
            [TestParent::class,"parentobject","!=",null,[9,10,11,12,17,18,19,20,25]],
            [TestParent::class,"parentobject","in",[1,3,4],[9,10,12,17,18]],
            [TestChild::class,"parentobject","=",3,[17]],
            [TestChild::class,"parentobject",'=',4,[18]],
            [TestChild::class,"parentobject",'=',null,[21,22,23,24]],
            [TestChild::class,"parentobject","!=",null,[17,18,19,20]],
            [TestChild::class,"parentobject","in",[1,3,4],[17,18]],
            [TestChild::class,"childobject","=",3,[17,19]],
            [TestChild::class,"childobject",'=',4,[18]],
            [TestChild::class,"childobject",'=',null,[20,21,22,23,24]],
            [TestChild::class,"childobject","!=",null,[17,18,19]],
            [TestChild::class,"childobject","in",[3,4],[17,18,19]],
            // array of objects
            [TestParent::class, "parentoarray","has",3,[9,10,13,18]],
            [TestParent::class, "parentoarray","has",8,[]],
            [TestParent::class, "parentoarray","has",7,[11,19]],
            [TestParent::class, "parentoarray","one of",[1,2],[9,10,11,13,14,18,19]],
            [TestParent::class, "parentoarray","all of",[1,2],[10,13,18]],
            [TestParent::class, "parentoarray","all of",[1,7],[]],
            [TestParent::class, "parentoarray","none of",[1,2,3],[12,15,16,17,20,21,22,23,24,25,26]],
            [TestParent::class, "parentoarray","empty",null,     [12,15,16,20,21,23,24,25,26]],
            
            [TestChild::class, "parentoarray","has",3,[18]],
            [TestChild::class, "parentoarray","has",8,[]],
            [TestChild::class, "parentoarray","one of",[1,2],[18,19]],
            [TestChild::class, "parentoarray","all of",[1,2],[18]],
            [TestChild::class, "parentoarray","all of",[1,7],[]],
            [TestChild::class, "parentoarray","none of",[1,2,3],[17,20,21,22,23,24]],
            [TestChild::class, "parentoarray","empty",null,     [20,21,23,24]],
            
            [TestChild::class, "childoarray","has",1,[20,24]],
            [TestChild::class, "childoarray","has",8,[]],
            [TestChild::class, "childoarray","one of",[3,4],[17,20]],
            [TestChild::class, "childoarray","all of",[4,5],[17]],
            [TestChild::class, "childoarray","all of",[1,7],[]],
            //@todo no expected result        [TestChild::class, "childoarray","none of",[1,2,3],[17,18,19,21,22,23,25]],
            [TestChild::class, "childoarray","empty",null,     [19,21,22,23]],
            
            
        ];
    }
}
<?php

namespace Tests\Feature;

use Tests\searchtestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class searchtestA extends \Sunhill\Objects\oo_object {
   
    public static $table_name = 'searchtestA';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('Aint')->searchable();
        self::integer('Anosearch');
        self::varchar('Achar')->searchable();
        self::calculated('Acalc')->searchable();
        self::object('Aobject')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
        self::arrayofobjects('Aoarray')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
        self::arrayofstrings('Asarray')->searchable();
    }
    
    public function calculate_Acalc() {
        return $this->Aint."=".$this->Achar;
    }
    
    public function unify() {
        $id = searchtestA::search()->where('Acalc','=','ABC')->first();
        
    }
}

class searchtestB extends searchtestA {

    public static $table_name = 'searchtestB';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('Bint')->searchable();
        self::varchar('Bchar')->searchable();
        self::calculated('Bcalc')->searchable();
        self::object('Bobject')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
        self::arrayofstrings('Bsarray')->searchable();
        self::arrayofobjects('Boarray')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
    }
    
    public function calculate_Bcalc() {
        return $this->Bint."=".$this->Bchar;
    }
}

class searchtestC extends searchtestB {
    
    public static $table_name = 'searchtestC';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::object('Cobject')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
    }

}

class ObjectSearchTest extends ObjectCommon
{
    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_table('searchtestA', ['Aint int,Anosearch int,Achar varchar(255)']);
        $this->create_table('searchtestB', ['Bint int,Bchar varchar(255)']);
        $this->create_table('searchtestC', []);
        
        $dummy_str = '\Sunhill\Test\ts_dummy';
        $this->insert_into('objects',['id','classname','created_at','updated_at'],
            [
                // 4 x Dummies für diverse Arrays
                [1,$dummy_str,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],

                [5,"\\Tests\\Feature\\searchtestA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [6,"\\Tests\\Feature\\searchtestA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [7,"\\Tests\\Feature\\searchtestA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [8,"\\Tests\\Feature\\searchtestA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [9,"\\Tests\\Feature\\searchtestA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                
                [10,"\\Tests\\Feature\\searchtestB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [11,"\\Tests\\Feature\\searchtestB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [12,"\\Tests\\Feature\\searchtestB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [13,"\\Tests\\Feature\\searchtestB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [14,"\\Tests\\Feature\\searchtestB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                
                [15,"\\Tests\\Feature\\searchtestC",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('dummies',['id','dummyint'],
            [[1,123],[2,234],[3,345],[4,456]]);
        $this->insert_into('searchtestA',['id','Aint','Anosearch','Achar'],
            [
                [5,111,1,'ABC'],[6,222,1,'ADE'],[7,333,1,'BCC'],[8,990,1,'XYZ'],[9,999,1,'XCX'],
                [10,500,1,'GGG'],[11,501,1,'ABC'],[12,502,1,'GGT'],[13,502,1,'GGZ'],[14,503,1,'GTG'],
                [15,503,1,'GGG']
            ]);
        $this->insert_into('searchtestB',['id','Bint','Bchar'],
            [
                [10,111,'ABC'],[11,601,'BBB'],[12,602,'CCC'],[13,602,'DDC'],[14,603,'ADD'],
                [15,603,'GGG']
            ]);
        $this->insert_into('searchtestC',['id'],[[15]]);
        $this->insert_into('caching',['id','object_id','fieldname','value'],
            [
                [1,5,'Acalc','111=ABC'], 
                [2,6,'Acalc','222=ADE'],
                [3,7,'Acalc','333=BCC'],
                [4,8,'Acalc','990=XYZ'],
                [5,9,'Acalc','999=XCX'],
                [6,10,'Acalc','500=GGG'],
                [7,11,'Acalc','501=GGF'],
                [8,12,'Acalc','502=GGT'],
                [9,13,'Acalc','502=GGZ'],
                [10,14,'Acalc','503=GTG'],
                [11,15,'Acalc','503=GGG'],
                [12,10,'Bcalc','111=ABC'],
                [13,11,'Bcalc','601=BBB'],
                [14,12,'Bcalc','602=CCC'],
                [15,13,'Bcalc','602=DDC'],
                [16,14,'Bcalc','603=ADD'],
                [17,15,'Bcalc','603=GGG'],
            ]);
        $this->insert_into('tags',['id','created_at','updated_at','name','options','parent_id'],
            [
                [1,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagA',0,0], 
                [2,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagB',0,0],
                [3,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagC',0,2],
                [4,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagD',0,0],
                [5,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagE',0,0],
                [6,'2019-05-15 10:00:00','2019-05-15 10:00:00','TagF',0,0],
            ]);
        $this->insert_into('tagcache',['id','name','tag_id','created_at','updated_at'],
            [
                [1,'TagA',1,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,'TagB',2,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,'TagC',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,'TagC.TagB',3,'2019-05-15 10:00:00','2019-05-15 10:00:00'],                
                [5,'TagD',4,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [6,'TagE',5,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [7,'TagF',6,'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('tagobjectassigns',['container_id','tag_id'],
            [
                [5,1],[5,2],[5,5], // testA(5)->TagA,TagB,TagE
                [6,1],[6,3],[6,6]  // testA(6)->TagA,TagC.TagB,TagF
            ]);
        $this->insert_into('objectobjectassigns',['container_id','element_id','field','index'],
            [
                [7,1, 'Aobject',0],
                [8,2, 'Aobject',0],
                [13,1,'Aobject',0],
                [13,1,'Bobject',0],
                [9,3, 'Aoarray',0],
                [9,4, 'Aoarray',1],
                [13,4, 'Boarray',0],
            ]);
        $this->insert_into('stringobjectassigns',['container_id','element_id','field','index'],
            [
                [7,'testA','Asarray',0],
                [7,'testB','Asarray',1],
                [8,'testA','Asarray',0],
                [8,'testC','Asarray',1],
                [13,'testA','Bsarray',0],
                [13,'testC','Asarray',0],                
            ]);
    }
    
    public function testSearchWithNoConditionSingleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestC::search()->get();
        $this->assertEquals(15,$result);
    }
    
    public function testSearchWithNoConditionMultipleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestB::search()->get();
        $this->assertEquals([10,11,12,13,14,15],$result);
    }
    
    public function searchtestCountSingleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestC::search()->count();
        $this->assertEquals(1,$result);
    }
    
    public function searchtestCountMultipleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestB::search()->count();
        $this->assertEquals(6,$result);
    }
    
    /**
     * @expectedException \Sunhill\QueryException
     */
    public function testFailSearch() {
        $this->prepare_tables();
        searchtestA::search()->where('Anosearch','=',1)->get();
    }
  
    /**
     * @dataProvider SimpleProvider
     */
    public function testSimpleSearchIDs($searchclass,$field,$relation,$value,$expect) {
        $this->prepare_tables();
        $classname = "\\Tests\\Feature\\".$searchclass;
        $result = $classname::search()->where($field,$relation,$value)->get();
        $this->assertEquals($expect,$result);
    }
    
    public function SimpleProvider() {
        return [
            ["searchtestA",'Aint','=',111,5],
            ["searchtestA",'Aint','=',5,null],
            ["searchtestA",'Aint','<',300,[5,6]],
            ["searchtestA",'Aint','>',900,[8,9]],
            ["searchtestB",'Bint','<>',602,[10,11,14,15]],
            ["searchtestA",'Aint','<',502,[5,6,7,10,11]],
            ["searchtestC",'Bint','=',603,15],
            ["searchtestA",'Aint','in',[111,222],[5,6]],
            
            ["searchtestA",'Achar','=','ADE',6],
            ["searchtestA",'Achar','=','ABC',[5,11]],
            ["searchtestB",'Achar','=','ABC',11],
            ["searchtestA",'Achar','=','NÜX',null],
            ["searchtestA",'Achar','<','B',[5,6,11]],
            ["searchtestA",'Achar','>','X',[8,9]],
            ["searchtestB",'Bchar','<>','CCC',[10,11,13,14,15]],
            ["searchtestA",'Achar','<','GGH',[5,6,7,10,11,15]],
            ["searchtestC",'Achar','=','GGG',15],
            ["searchtestA",'Achar','in',['GGT','GGZ'],[12,13]],
            
            ["searchtestA",'Achar','begins with','A',[5,6,11]],
            ["searchtestA",'Achar','begins with','B',7],
            ["searchtestA",'Achar','begins with','2',null],
            ["searchtestA",'Achar','ends with','Z',[8,13]],
            ["searchtestA",'Achar','ends with','T',12],
            ["searchtestA",'Achar','ends with','2',null],
            
            ["searchtestB",'Bchar','consists','D',[13,14]],
            ["searchtestB",'Bchar','consists','C',[10,12,13]],
            ["searchtestB",'Bchar','consists','G',15],
            ["searchtestB",'Bchar','consists','2',null],
            
            ["searchtestA",'Acalc','=','222=ADE',6],
            ["searchtestA",'Acalc','=','666=RRR',null],
            ["searchtestA",'Acalc','begins with','503',[14,15]],
            ["searchtestA",'Acalc','begins with','666',null],
            ["searchtestA",'Acalc','begins with','222',6],
            ["searchtestA",'Acalc','ends with','ADE',6], 
             
            ["searchtestA",'tags','has','TagA',[5,6]],
            ["searchtestA",'tags','has','TagC.TagB',6],
            ["searchtestA",'tags','has','TagD',null],
            ["searchtestA",'tags','has not','TagA',[7,8,9,10,11,12,13,14,15]],
            ["searchtestA",'tags','one of',['TagE','TagF'],[5,6]],
            ["searchtestA",'tags','none of',['TagE'],[6,7,8,9,10,11,12,13,14,15]],
            ["searchtestA",'tags','all of',['TagA','TagE'],5], 

            ["searchtestA",'Asarray','has','testA',[7,8]],
            ["searchtestA",'Asarray','has','testC',[8,13]],
            ["searchtestA",'Asarray','has','testC',[8,13]],
            ["searchtestB",'Asarray','has','testC',13],
            ["searchtestA",'Asarray','has','testD',null],
            ["searchtestA",'Asarray','has not','testA',[5,6,9,10,11,12,13,14,15]],
            ["searchtestA",'Asarray','one of',['testB','testC'],[7,8,13]],
            ["searchtestA",'Asarray','none of',['testC','testA'],[5,6,9,10,11,12,14,15]],
            ["searchtestA",'Asarray','all of',['testC','testA'],8], 
            ["searchtestA",'Asarray','empty',null,[5,6,9,10,11,12,14,15]],
            
            ["searchtestA",'Aobject','=',1,[7,13]],
            ["searchtestA","Aobject",'=',2,8],
            ["searchtestB","Aobject","=",1,13],
            ["searchtestA","Aobject","in",[1,2],[7,8,13]],
            ["searchtestA","Aobject","=",null,[5,6,9,10,11,12,14,15]],
            
            ["searchtestA","Aoarray","has",3,9],
            ["searchtestA","Aoarray","has",1,null],
            ["searchtestA","Aoarray","one of",[3,1],9],
            ["searchtestA","Aoarray","all of",[3,4],9],
            ["searchtestA","Aoarray","none of",[3,4],[5,6,7,8,10,11,12,13,14,15]],
            ["searchtestB","Boarray","empty",null,[10,11,12,14,15]],
        ];
    }
    
    public function testPassObject() {
        $this->prepare_tables();
        $test = \Sunhill\Objects\oo_object::load_object_of(1);
        $result = \Tests\Feature\searchtestA::search()->where('Aobject','=',$test)->get();
        $this->assertEquals([7,13],$result);
        
    }
    
    public function testGetFirst() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Achar','=','ABC')->first();
        $this->assertEquals(5,$result);        
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithOneResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','111')->first();
        $this->assertEquals(5,$result);
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithNoResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','666')->first();
        $this->assertEquals(null,$result);
    }
    
    public function testGetFirstObject() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Achar','=','ABC')->first_object();
        $this->assertEquals(5,$result->get_id());
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstObjectWithNoResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','666')->first_object();
        $this->assertEquals(null,$result);
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstObjectWithOneResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','111')->first_object();
        $this->assertEquals(5,$result->get_id());
    }
    
    public function testGetObjects() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Achar','=','ABC')->get_objects();
        $this->assertEquals([5,11],[$result[0]->get_id(),$result[1]->get_id()]);
    }

    /**
     * @group Focus
     */
    public function testGetObjectsWithOneResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','111')->get_objects();
        $this->assertEquals(5,$result);
    }
    
    /**
     * @group Focus
     */
    public function testGetObjectsWithNoResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','666')->get_objects();
        $this->assertEquals(null,$result);
    }
    
    /**
     * @dataProvider ComplexProvider
     * @group complex
     */
    public function testComplexSearchIDs($searchclass,$field1,$relation1,$value1,$field2,$relation2,$value2,$expect) {
        $this->prepare_tables();
        $classname = "\\Tests\\Feature\\".$searchclass;
        $result = $classname::search()->where($field1,$relation1,$value1)->where($field2,$relation2,$value2)->get();
        $this->assertEquals($expect,$result);
    }
    
    public function ComplexProvider() {
        return [
            ["searchtestA",'Aint','<',300,'Aint','<>','222',5],
            ["searchtestA",'Aint','<',300,'Achar','=','ABC',5],
            ["searchtestB",'Aint','>',300,'Bint','=','602',[12,13]],
            ["searchtestA",'tags','has','TagA','Aint','<>',222,5],
            ["searchtestA",'tags','has','TagA','tags','has','TagC',6],
            ["searchtestA",'Acalc','<>','111=ABC','tags','has','TagA',6],
            ["searchtestA",'Aobject','=',1,'Aint','<','502',7],
            ["searchtestB","Boarray","empty",null,'Asarray','has','testC',null],
            ["searchtestA",'Asarray','has','testA','Asarray','has','testC',8],
        ];
    }
    
    /**
     * @group regression
     */
    public function testSearcRegression() {
        $test = new searchtestA();
        $test->unify();
        $this->assertTrue(true);
    }
}

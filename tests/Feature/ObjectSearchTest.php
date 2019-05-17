<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class testA extends \Sunhill\Objects\oo_object {
   
    public static $table_name = 'testA';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('Aint')->searchable();
        self::integer('Anosearch');
        self::varchar('Achar')->searchable();
        self::calculated('Acalc')->searchable();
        self::object('Aobject')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
        self::arrayofstrings('Asarray')->searchable();
    }
    
    public function calculate_Acalc() {
        return $this->Aint."=".$this->Achar;
    }
}

class testB extends testA {

    public static $table_name = 'testB';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::integer('Bint')->searchable();
        self::varchar('Bchar')->searchable();
        self::calculated('Bcalc')->searchable();
        self::object('Bobject')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
        self::arrayofstrings('Bsarray')->searchable();
    }
    
    public function calculate_Bcalc() {
        return $this->Bint."=".$this->Bchar;
    }
}

class testC extends testB {
    
    public static $table_name = 'testC';
    
    protected static function setup_properties() {
        parent::setup_properties();
        self::object('Cobject')->set_allowed_objects(["\\Sunhill\\Test\\ts_dummy"])->searchable();
    }

}

class ObjectSearchTest extends ObjectCommon
{
    protected function prepare_tables() {
        DB::statement("drop table if exists testA");
        DB::statement("drop table if exists testB");
        DB::statement("drop table if exists testC");
        DB::statement("create table testA (id int primary key,Aint int,Anosearch int,Achar varchar(255))");
        DB::statement("create table testB (id int primary key,Bint int,Bchar varchar(255))");
        DB::statement("create table testC (id int primary key)");
        $this->insert_into('objects',['id','classname','created_at','updated_at'],
            [
                // 4 x Dummies für diverse Arrays
                [1,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [2,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [3,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [4,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],

                [5,"\\Tests\\Feature\\testA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [6,"\\Tests\\Feature\\testA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [7,"\\Tests\\Feature\\testA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [8,"\\Tests\\Feature\\testA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [9,"\\Tests\\Feature\\testA",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                
                [10,"\\Tests\\Feature\\testB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [11,"\\Tests\\Feature\\testB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [12,"\\Tests\\Feature\\testB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [13,"\\Tests\\Feature\\testB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                [14,"\\Tests\\Feature\\testB",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
                
                [15,"\\Tests\\Feature\\testC",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
            ]);
        $this->insert_into('dummies',['id','dummyint'],
            [[1,123],[2,234],[3,345],[4,456]]);
        $this->insert_into('testA',['id','Aint','Anosearch','Achar'],
            [
                [5,111,1,'ABC'],[6,222,1,'ADE'],[7,333,1,'BCC'],[8,990,1,'XYZ'],[9,999,1,'XCX'],
                [10,500,1,'GGG'],[11,501,1,'GGF'],[12,502,1,'GGT'],[13,502,1,'GGZ'],[14,503,1,'GTG'],
                [15,503,1,'GGG']
            ]);
        $this->insert_into('testB',['id','Bint','Bchar'],
            [
                [10,600,'AAA'],[11,601,'BBB'],[12,602,'CCC'],[13,602,'DDC'],[14,603,'ADD'],
                [15,603,'GGG']
            ]);
        $this->insert_into('testC',['id'],[[15]]);
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
                [12,10,'Bcalc','600=AAA'],
                [13,11,'Bcalc','601=BBB'],
                [14,12,'Bcalc','602=CCC'],
                [15,13,'Bcalc','602=DDC'],
                [16,14,'Bcalc','603=ADD'],
                [17,15,'Bcalc','603=GGG'],
            ]);
    }
    
    private function insert_into($name,$fields,$values) {
        $querystr = 'insert into '.$name.' (id';
        array_shift($fields);
        foreach ($fields as $field) {
            $querystr .= ','.$field;
        }
        $querystr .= ') values ';
        $firstset = true;
        foreach ($values as $valueset) {
            if (!$firstset) {
                $querystr .= ',';
            }
            $firstset = false;
            $querystr .= '(';
            $first = true;
            foreach ($valueset as $value) {
                if (!$first) {
                    $querystr .= ',';
                }
                $querystr .= "'".$value."'";    
                $first = false;
            }
            $querystr .= ')';
        }
        DB::statement($querystr); 
    }
    
    public function testSearchWithNoConditionSingleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\testC::search()->get();
        $this->assertEquals(15,$result);
    }
    
    public function testSearchWithNoConditionMultipleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\testB::search()->get();
        $this->assertEquals([10,11,12,13,14,15],$result);
    }
    
    public function testCountSingleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\testC::search()->count();
        $this->assertEquals(1,$result);
    }
    
    public function testCountMultipleResult() {
        $this->prepare_tables();
        $result = \Tests\Feature\testB::search()->count();
        $this->assertEquals(6,$result);
    }
    
    /**
     * @expectedException \Sunhill\QueryException
     */
    public function testFailSearch() {
        $this->prepare_tables();
        testA::search()->where('Anosearch','=',1)->get();
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
            ["testA",'Aint','=',111,5],
            ["testA",'Aint','=',5,null],
            ["testA",'Aint','<',300,[5,6]],
            ["testA",'Aint','>',900,[8,9]],
            ["testB",'Bint','<>',602,[10,11,14,15]],
            ["testA",'Aint','<',502,[5,6,7,10,11]],
            ["testC",'Bint','=',603,15],
            
            ["testA",'Achar','=','ADE',6],
            ["testA",'Achar','=','NÜX',null],
            ["testA",'Achar','<','B',[5,6]],
            ["testA",'Achar','>','X',[8,9]],
            ["testB",'Bchar','<>','CCC',[10,11,13,14,15]],
            ["testA",'Achar','<','GGH',[5,6,7,10,11,15]],
            ["testC",'Achar','=','GGG',15],
            
            ["testA",'Achar','begins with','A',[5,6]],
            ["testA",'Achar','begins with','B',7],
            ["testA",'Achar','begins with','2',null],
            ["testA",'Achar','ends with','Z',[8,13]],
            ["testA",'Achar','ends with','T',12],
            ["testA",'Achar','ends with','2',null],
            
            ["testB",'Bchar','consists','D',[13,14]],
            ["testB",'Bchar','consists','C',[12,13]],
            ["testB",'Bchar','consists','B',11],
            ["testB",'Bchar','consists','2',null],
            
        ];
    }
}

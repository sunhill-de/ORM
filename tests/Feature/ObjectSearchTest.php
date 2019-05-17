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
        self::arrayofstrings('Asarray')->searchable();
    }
    
    public function calculate_Acalc() {
        return $this->Aint."=".$this->Achar;
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
        DB::statement("drop table if exists searchtestA");
        DB::statement("drop table if exists searchtestB");
        DB::statement("drop table if exists searchtestC");
        DB::statement("create table searchtestA (id int primary key,Aint int,Anosearch int,Achar varchar(255))");
        DB::statement("create table searchtestB (id int primary key,Bint int,Bchar varchar(255))");
        DB::statement("create table searchtestC (id int primary key)");
        $this->insert_into('objects',['id','classname','created_at','updated_at'],
            [
                // 4 x Dummies für diverse Arrays
                [1,"\\Sunhill\\Test\\ts_dummy",'2019-05-15 10:00:00','2019-05-15 10:00:00'],
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
                [10,500,1,'GGG'],[11,501,1,'GGF'],[12,502,1,'GGT'],[13,502,1,'GGZ'],[14,503,1,'GTG'],
                [15,503,1,'GGG']
            ]);
        $this->insert_into('searchtestB',['id','Bint','Bchar'],
            [
                [10,600,'AAA'],[11,601,'BBB'],[12,602,'CCC'],[13,602,'DDC'],[14,603,'ADD'],
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
            
            ["searchtestA",'Achar','=','ADE',6],
            ["searchtestA",'Achar','=','NÜX',null],
            ["searchtestA",'Achar','<','B',[5,6]],
            ["searchtestA",'Achar','>','X',[8,9]],
            ["searchtestB",'Bchar','<>','CCC',[10,11,13,14,15]],
            ["searchtestA",'Achar','<','GGH',[5,6,7,10,11,15]],
            ["searchtestC",'Achar','=','GGG',15],
            
            ["searchtestA",'Achar','begins with','A',[5,6]],
            ["searchtestA",'Achar','begins with','B',7],
            ["searchtestA",'Achar','begins with','2',null],
            ["searchtestA",'Achar','ends with','Z',[8,13]],
            ["searchtestA",'Achar','ends with','T',12],
            ["searchtestA",'Achar','ends with','2',null],
            
            ["searchtestB",'Bchar','consists','D',[13,14]],
            ["searchtestB",'Bchar','consists','C',[12,13]],
            ["searchtestB",'Bchar','consists','B',11],
            ["searchtestB",'Bchar','consists','2',null],
            
        ];
    }
}

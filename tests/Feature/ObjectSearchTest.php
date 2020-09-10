<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Sunhill\ORM\Objects\oo_object;
use Tests\DBTestCase;
use Sunhill\ORM\Utils\objectlist;

class searchtestA extends oo_object {
   
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

class ObjectSearchTest extends DBTestCase
{
    
    protected function do_migration() {
        Artisan::call('migrate:fresh --path=database/migrations/');
        Artisan::call('migrate --path=database/migrations/common');
        Artisan::call('migrate --path=database/migrations/searchtests');
    }
    
    protected function do_seeding() {
        Artisan::call('db:seed --class=SearchSeeder');
    }
    
    protected function simplify_result(objectlist $result) {
        $return = [];
        for($i=0;$i<count($result);$i++) {
            $return[] = $result[$i]->get_id($i);
        }
        return $return;
    }
    
    public function testSearchWithNoConditionSingleResult() {
        $result = $this->simplify_result(\Tests\Feature\searchtestC::search()->get());
        $this->assertEquals([15],$result);
    }
    
    public function testSearchWithNoConditionMultipleResult() {
        $result = $this->simplify_result(\Tests\Feature\searchtestB::search()->get());
        $this->assertEquals([10,11,12,13,14,15],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithNoConditionOrder() {
        $result = $this->simplify_result(\Tests\Feature\searchtestB::search()->order_by('Bchar')->get());
        $this->assertEquals([10,14,11,12,13,15],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithConditionOrder() {
        $result = $this->simplify_result(\Tests\Feature\searchtestB::search()->where('Bint','<',602)->order_by('Bchar','desc')->get());
        $this->assertEquals([11,10],$result);
    }
    
    /**
     * @group order
     */
    public function testSearchWithCombinedConditionOrder() {
        $result = $this->simplify_result(\Tests\Feature\searchtestB::search()->where('Bint','<',603)->where('Aint','<',502)->order_by('Bchar',false)->get());
        $this->assertEquals([11,10],$result);
    }
    
    /**
     * @group limit
     */
    public function testSearchWithLimit() {
        $result = $this->simplify_result(\Tests\Feature\searchtestB::search()->limit(2,2)->get());
        $this->assertEquals([12,13],$result);
    }
    
    /**
     * @group count
     */
    public function testCountSingleResult() {
        $result = \Tests\Feature\searchtestC::search()->count();
        $this->assertEquals(1,$result);
    }
    
    /**
     * @group bug
     * @group count
     */
    public function testCountWithObjectCondition() {
        $result = \Tests\Feature\searchtestA::search()->where('Aobject','=',1)->count();
        $this->assertEquals(2,$result);
    }
    
    /**
     * @group count
     */   
    public function testCountMultipleResult() {
        $result = \Tests\Feature\searchtestB::search()->count();
        $this->assertEquals(6,$result);
    }
    
    public function testFailSearch() {
        $this->expectException(\Sunhill\ORM\Search\QueryException::class);
        searchtestA::search()->where('Anosearch','=',1)->get();
    }
  
    /**
     * @dataProvider SimpleProvider
     * @group simple
     */
    public function testSimpleSearchIDs($searchclass,$field,$relation,$value,$expect) {
        $classname = "\\Tests\\Feature\\".$searchclass;
        $result = $this->simplify_result($classname::search()->where($field,$relation,$value)->get());
        $this->assertEquals($expect,$result);
    }
    
    public function SimpleProvider() {
        return [
            ["searchtestA",'Aint','=',111,[5]],
            ["searchtestA",'Aint','=',5,[]],
            ["searchtestA",'Aint','<',300,[5,6]],
            ["searchtestA",'Aint','>',900,[8,9]],
            ["searchtestB",'Bint','<>',602,[10,11,14,15]],
            ["searchtestB",'Bint','!=',602,[10,11,14,15]],
            ["searchtestA",'Aint','<',502,[5,6,7,10,11]],
            ["searchtestC",'Bint','=',603,[15]],
            ["searchtestA",'Aint','in',[111,222],[5,6]],
            
            ["searchtestA",'Achar','=','ADE',[6]],
            ["searchtestA",'Achar','=','ABC',[5,11]],
            ["searchtestB",'Achar','=','ABC',[11]],
            ["searchtestA",'Achar','=','NÜX',[]],
            ["searchtestA",'Achar','<','B',[5,6,11]],
            ["searchtestA",'Achar','>','X',[8,9]],
            ["searchtestB",'Bchar','<>','CCC',[10,11,13,14,15]],
            ["searchtestA",'Achar','<','GGH',[5,6,7,10,11,15]],
            ["searchtestC",'Achar','=','GGG',[15]],
            ["searchtestA",'Achar','in',['GGT','GGZ'],[12,13]],
            
            ["searchtestA",'Achar','begins with','A',[5,6,11]],
            ["searchtestA",'Achar','begins with','B',[7]],
            ["searchtestA",'Achar','begins with','2',[]],
            ["searchtestA",'Achar','ends with','Z',[8,13]],
            ["searchtestA",'Achar','ends with','T',[12]],
            ["searchtestA",'Achar','ends with','2',[]],
            
            ["searchtestB",'Bchar','consists','D',[13,14]],
            ["searchtestB",'Bchar','consists','C',[10,12,13]],
            ["searchtestB",'Bchar','consists','G',[15]],
            ["searchtestB",'Bchar','consists','2',[]],
            
            ["searchtestA",'Acalc','=','222=ADE',[6]],
            ["searchtestA",'Acalc','=','666=RRR',[]],
            ["searchtestA",'Acalc','begins with','503',[14,15]],
            ["searchtestA",'Acalc','begins with','666',[]],
            ["searchtestA",'Acalc','begins with','222',[6]],
            ["searchtestA",'Acalc','ends with','ADE',[6]], 
             
            ["searchtestA",'tags','has','TagA',[5,6]],
            ["searchtestA",'tags','has','TagB.TagC',[6]],
            ["searchtestA",'tags','has','TagD',[]],
            ["searchtestA",'tags','has not','TagA',[7,8,9,10,11,12,13,14,15]],
            ["searchtestA",'tags','one of',['TagE','TagF'],[5,6]],
            ["searchtestA",'tags','none of',['TagE'],[6,7,8,9,10,11,12,13,14,15]],
            ["searchtestA",'tags','all of',['TagA','TagE'],[5]], 

            ["searchtestA",'Asarray','has','testA',[7,8]],
            ["searchtestA",'Asarray','has','testC',[8,13]],
            ["searchtestA",'Asarray','has','testC',[8,13]],
            ["searchtestB",'Asarray','has','testC',[13]],
            ["searchtestA",'Asarray','has','testD',[]],
            ["searchtestA",'Asarray','has not','testA',[5,6,9,10,11,12,13,14,15]],
            ["searchtestA",'Asarray','one of',['testB','testC'],[7,8,13]],
            ["searchtestA",'Asarray','none of',['testC','testA'],[5,6,9,10,11,12,14,15]],
            ["searchtestA",'Asarray','all of',['testC','testA'],[8]], 
            ["searchtestA",'Asarray','empty',null,[5,6,9,10,11,12,14,15]],
            
            ["searchtestA",'Aobject','=',1,[7,13]],
            ["searchtestA","Aobject",'=',2,[8]],
            ["searchtestB","Aobject","=",1,[13]],
            ["searchtestA","Aobject","in",[1,2],[7,8,13]],
            ["searchtestA","Aobject","=",null,[5,6,9,10,11,12,14,15]],
            
            ["searchtestA","Aoarray","has",3,[9]],
            ["searchtestA","Aoarray","has",1,[]],
            ["searchtestA","Aoarray","one of",[3,1],[9]],
            ["searchtestA","Aoarray","all of",[3,4],[9]],
            ["searchtestA","Aoarray","none of",[3,4],[5,6,7,8,10,11,12,13,14,15]],
            ["searchtestB","Boarray","empty",null,[10,11,12,14,15]],
        ];
    }
    
    /**
     * @group object
     */
    public function testPassObject() {
        $test = \Sunhill\ORM\Objects\oo_object::load_object_of(1);
        $result = $this->simplify_result(\Tests\Feature\searchtestA::search()->where('Aobject','=',$test)->get());
        $this->assertEquals([7,13],$result);
        
    }
    
    public function testGetFirst() {
         $result = \Tests\Feature\searchtestA::search()->where('Achar','=','ABC')->first();
        $this->assertEquals(5,$result);        
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithOneResult() {
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','111')->first();
        $this->assertEquals(5,$result);
    }
    
    /**
     * @group Focus
     */
    public function testGetFirstWithNoResult() {
        $result = \Tests\Feature\searchtestA::search()->where('Aint','=','666')->first();
        $this->assertEquals(null,$result);
    }
    
    /**
     * @dataProvider ComplexProvider
     * @group complex
     */
    public function testComplexSearchIDs($searchclass,$field1,$relation1,$value1,$field2,$relation2,$value2,$expect) {
         $classname = "\\Tests\\Feature\\".$searchclass;
         $result = $this->simplify_result($classname::search()->where($field1,$relation1,$value1)->where($field2,$relation2,$value2)->get());
        $this->assertEquals($expect,$result);
    }
    
    public function ComplexProvider() {
        return [
            ["searchtestA",'Aint','<',300,'Aint','<>','222',[5]],
            ["searchtestA",'Aint','<',300,'Achar','=','ABC',[5]],
            ["searchtestB",'Aint','>',300,'Bint','=','602',[12,13]], 
            ["searchtestA",'tags','has','TagA','Aint','<>',222,[5]],
            ["searchtestA",'tags','has','TagA','tags','has','TagC',[6]],
            ["searchtestA",'Acalc','<>','111=ABC','tags','has','TagA',[6]],
            ["searchtestA",'Aobject','=',1,'Aint','<','502',[7]],
            ["searchtestB","Boarray","empty",null,'Asarray','has','testC',[]],
            ["searchtestA",'Asarray','has','testA','Asarray','has','testC',[8]],
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

<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\Basic\Tests\SunhillScenarioTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithObjects;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\SimpleParent;
use Sunhill\ORM\Tests\Objects\SimpleChild;

class ScenarioWithObjectsFeatureTestScenario extends ScenarioBase{

    use ScenarioWithObjects;
             
    protected $Requirements = [
        'Objects'=>[
            'destructive'=>true,
        ],
    ];
    
    public function GetObjects() {
        return [
          'dummy'=>[
              ['dummyint'],
              [
                  'dummy1'=>[11],
                  'dummy2'=>[22],
                  [33]
              ]
          ],
          'SimpleParent'=>[
              ['parentint','parentchar','parentobject','parentoarray'],
              [
                  'parent1'=>[111,'AAA','=>dummy1',['=>dummy1']],
                  [222,'BBB',null,null]
              ]
          ],
          'SimpleChild'=>[
              ['parentint','parentchar','parentobject','parentoarray','childint','childchar'],
              [
                  'child1'=>[11,'AA','=>dummy2',['=>dummy2'],1111,'AAAA'],
                  'child2'=>[22,'BB',null,null,2222,'BBBB'],
              ]
          ]
        ];
    }
    
    public function SetupBeforeTestsObjects() {
        Classes::flushClasses();
        Classes::registerClass(Dummy::class);
        Classes::registerClass(SimpleParent::class);
        Classes::registerClass(SimpleChild::class);
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key,dummyint int)');
        DB::statement('drop table if exists simpleparents');
        DB::statement('create table simpleparents (id int primary key,parentint int,parentchar varchar(10))');
        DB::statement('drop table if exists simplechildren');
        DB::statement('create table simplechildren (id int primary key,childint int,childchar varchar(10))');        
    }
}

class ScenarioWithObjectsTest extends SunhillScenarioTestCase
{
   
    static protected $ScenarioClass = 'Sunhill\\ORM\\Tests\\Feature\\ScenarioWithObjectsFeatureTestScenario';
    
    use CreatesApplication;

    protected function GetScenarioClass() {
        return ScenarioWithObjectsFeatureTestScenario::class;    
    }
    
    public function SetupTables() {
    }
    
    public function testInit() {
        $this->SetupTables();
        $this->assertTrue(true); // Dirty hack to get the tables created
    }
    
    public function testDestructiveInit_dummy() {
        $object = Dummy::search()->where('dummyint',11)->load();
        $this->assertEquals(11,$object->dummyint);
    }

    public function testDestructiveInit_parent() {
        $object = SimpleParent::search()->where('parentchar','AAA')->load();
        $this->assertEquals(111,$object->parentint);
    }
    
    public function testDestructiveInit_child() {
        $object = SimpleChild::search()->where('childchar','AAAA')->load();
        $this->assertEquals(22,$object->parentobject->dummyint);
    }
    
}

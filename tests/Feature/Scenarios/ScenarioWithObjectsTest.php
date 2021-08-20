<?php

namespace Sunhill\Basic\Tests\Feature;

use Sunhill\Basic\Tests\SunhillTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithObjects;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;

class ScenarioWithObjectsFeatureTestScenario extends ScenarioBase{

    use ScenarioWithObjects;
             
    public function GetObjects() {
        return [
          'dummy'=>[
              ['dummyint'],
              [
                  'dummy1'=>11,
                  'dummy2'=>22,
                  33
              ]
          ]  
        ];
    }
}

class ScenarioWithObjectsTest extends SunhillTestCase
{
   
    use CreatesApplication;

    protected function SetupTables() {
        DB::statement('drop table if exists dummies');
        DB::statement('create table dummies (id int primary key,dummyint int)');
        DB::statement('drop table if exists simpleparents');
        DB::statement('create table simpleparents (id int primary key,parentint int,parentchar varchar(10))');
        DB::statement('drop table if exists simplechildren');
        DB::statement('create table simplechildren (id int primary key,childint int,childchar varchar(10))');
    }
    
    public function testSimpleSeed() {
        
    }
}

<?php

namespace Sunhill\ORM\Tests\Feature;

use Sunhill\Basic\Tests\SunhillScenarioTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithObjects;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Tests\Objects\SimpleParent;
use Sunhill\ORM\Tests\Objects\SimpleChild;

class ScenarioWithTagsFeatureTestScenario extends ScenarioBase{

    use ScenarioWithTags;
             
    protected $Requirements = [
        'Tags'=>[
            'destructive'=>true,
        ],
    ];
    
    public function GetTags() {
        return [
        ];
    }
    
    public function SetupBeforeTestsObjects() {
        Classes::flush_cache();
        Classes::create_cache(dirname(__FILE__).'/../../Objects');
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
   
    static protected $ScenarioClass = 'Sunhill\\ORM\\Tests\\Feature\\ScenarioWithTagsFeatureTestScenario';
    
    use CreatesApplication;

    public function SetupTables() {
    }
    
    public function testInit() {
        $this->SetupTables();
        $this->assertTrue(true); // Dirty hack to get the tables created
    }
    
    public function testDestructiveInit_dummy() {
        $object = ts_dummy::search()->where('dummyint',11)->load();
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

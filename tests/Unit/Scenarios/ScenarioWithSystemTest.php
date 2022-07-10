<?php

namespace Sunhill\Basic\Tests\Unit;

use Sunhill\ORM\Tests\DBTestCase_Empty;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithSystem;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;
use Sunhill\Basic\SunhillException;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Tests\Feature\ScenarioWithTagsFeatureTestScenario;

class ScenarioWithSystemUnitTestScenario extends ScenarioBase{

    use ScenarioWithSystem;
             
}

class ScenarioWithSystemTest extends DBTestCase_Empty
{
   
    use CreatesApplication;

    protected function ClearSystem() {
        $systemtables = ['attributes','attributevalues','caching','externalhooks','objectobjectassigns','objects','stringobjectassigns','tagobjectassigns'];
        foreach ($systemtables as $table) {
            DB::statement('drop table if exists '.$table);
        }        
    }
    
    public function testSetupSystem() {
        $this->ClearSystem();
        $test = new ScenarioWithSystemUnitTestScenario();
        $test->setTest($this);
        $this->callProtectedMethod($test,'SetupSystem');
        $this->assertDatabaseHasTable('attributes');
        $this->assertDatabaseHasTable('attributevalues');
        $this->assertDatabaseHasTable('caching');
        $this->assertDatabaseHasTable('externalhooks');
        $this->assertDatabaseHasTable('objectobjectassigns');
        $this->assertDatabaseHasTable('objects');
        $this->assertDatabaseHasTable('stringobjectassigns');
        $this->assertDatabaseHasTable('tagobjectassigns');
    }
    
}

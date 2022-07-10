<?php

namespace Sunhill\Basic\Tests\Unit;

use Sunhill\ORM\Tests\DBTestCase_Empty;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithTags;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;
use Sunhill\Basic\SunhillException;
use Sunhill\ORM\Facades\Tags;
use Sunhill\ORM\Tests\Feature\ScenarioWithTagsFeatureTestScenario;

class ScenarioWithTagsUnitTestScenario extends ScenarioBase{

    use ScenarioWithTags;
             
    public function GetTags() {
        return [
            'TagB',
            'TagC.TagD'
        ];
    }
}

class ScenarioWithTagsTest extends DBTestCase_Empty
{
   
    use CreatesApplication;
    
    public function testSetupTag() {
        DB::table('tags')->truncate();
        DB::table('tagcache')->truncate();
        $test = new ScenarioWithTagsUnitTestScenario();
        $this->callProtectedMethod($test,'SetupTag',['TagA']);
        $this->assertDatabaseHas('tags',['name'=>'TagA']);
        $this->assertDatabaseHas('tagcache',['name'=>'TagA']);
    }
    
    public function testSetupTags() {
        DB::table('tags')->truncate();
        DB::table('tagcache')->truncate();
        $test = new ScenarioWithTagsUnitTestScenario();
        $this->callProtectedMethod($test,'SetupTags',[['TagB','TagC.TagD']]);
        $this->assertDatabaseHas('tags',['name'=>'TagB']);        
        $this->assertDatabaseHas('tags',['name'=>'TagD']);
        $this->assertDatabaseHas('tagcache',['name'=>'TagC.TagD']);
    }
}

<?php

namespace Sunhill\Basic\Tests\Unit;

use Sunhill\Basic\Tests\SunhillTestCase;
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

class ScenarioWithTagsTest extends SunhillTestCase
{
   
    use CreatesApplication;
    
    public function testSetupTag() {
        $test = new ScenarioWithTagsUnitTestScenario();
        $this->callProtectedMethod($test,'SetupTag',['TagA']);
        $this->assertDatabaseHas('tags',['name'=>'TagA']);
        $this->assertDatabaseHas('tagcache',['name'=>'TagA']);
    }
    
    public function testSetupTags() {
        $test = new ScenarioWithTagsUnitTestScenario();
        $this->callProtectedMethod($test,'SetupTags',[]);
        $this->assertDatabaseHas('tags',['name'=>'TagA']);        
        $this->assertDatabaseHas('tags',['name'=>'TagD']);
        $this->assertDatabaseCache('tags',['name'=>'TagC.TagD']);
    }
}

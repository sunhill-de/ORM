<?php

namespace Sunhill\Basic\Tests\Unit;

use Sunhill\Basic\Tests\SunhillTestCase;
use Sunhill\Basic\Tests\Scenario\ScenarioBase;
use Sunhill\ORM\Tests\Scenario\ScenarioWithTags;
use Tests\CreatesApplication;
use Illuminate\Support\Facades\DB;
use Sunhill\Basic\SunhillException;
use Sunhill\ORM\Facades\Tags;

class ScenarioWithTagsUnitTestScenario extends ScenarioBase{

    use ScenarioWithTags;
             
    public function GetObjects() {
        return [
        ];
    }
}

class ScenarioWithTagsTest extends SunhillTestCase
{
   
    use CreatesApplication;
    

}

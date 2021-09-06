<?php

namespace Sunhill\ORM\Tests\Scenario;

use Sunhill\Basic\Tests\Scenario\ScenarioWithDatabase;
use Sunhill\Basic\Tests\Scenario\ScenarioWithTables;

class StdReadOnlyScenario extends StdScenarioBase{

    protected $Requirements = [
        'Database'=>[
            'destructive'=>false,
        ],        
        'Tables'=>[
            'destructive'=>false,
        ],        
    ];
  
}

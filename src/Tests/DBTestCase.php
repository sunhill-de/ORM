<?php

namespace Sunhill\ORM\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\ORM\Facades\Objects;
use Sunhill\ORM\Facades\Classes;
use Database\Seeders\SimpleSeeder;
use Sunhill\ORM\Tests\Objects\Dummy;
use Sunhill\ORM\Tests\Objects\TestChild;
use Sunhill\ORM\Tests\Objects\TestParent;
use Sunhill\ORM\Tests\Objects\Passthru;
use Sunhill\ORM\Tests\Objects\SecondLevelChild;
use Sunhill\ORM\Tests\Objects\ThirdLevelChild;
use Sunhill\ORM\Tests\Objects\ReferenceOnly;
use Sunhill\ORM\Tests\Objects\ObjectUnit;

abstract class DBTestCase extends DBTestCase_Empty
{

    protected function do_seeding() {
        $this->seed(SimpleSeeder::class);        
    }
}

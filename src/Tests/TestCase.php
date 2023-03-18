<?php

namespace Sunhill\ORM\Tests;

use Sunhill\Basic\Tests\SunhillOrchestraTestCase;
use Sunhill\Basic\SunhillBasicServiceProvider;
use Sunhill\ORM\SunhillServiceProvider;

class TestCase extends SunhillOrchestraTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }
    
    protected function getPackageProviders($app)
    {
        return [
            SunhillBasicServiceProvider::class,
            SunhillServiceProvider::class,
         ];
    }
    
}
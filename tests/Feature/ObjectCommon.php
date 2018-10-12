<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ObjectCommon extends \Tests\sunhill_testcase
{
    protected function setUp():void {
        parent::setUp();
        $this->BuildTestClasses();
        $this->clear_system_tables(); 
    }
    
    
}

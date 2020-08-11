<?php

namespace Tests\Feature;

use Sunhill\Test\sunhill_testcase_db;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ObjectCommon extends sunhill_testcase_db
{
    public function setUp():void {
        parent::setUp();
//        $this->prepare_tables();
    }
    
    
}

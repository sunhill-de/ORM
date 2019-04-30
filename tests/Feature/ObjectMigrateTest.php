<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sunhill\Test;
use Illuminate\Support\Facades\DB;

class testA extends \Sunhill\Objects\oo_object {
    
}

class testB extends \Sunhill\Objects\oo_object {
   
}

class ObjectMigrateTest extends ObjectCommon
{
    protected function prepare_tables() {
        DB::statement("create table testA (id int primary key,testint int,testchar varchar(255)");
        DB::statement("create table testB (id int primary key,testint int,testchar varchar(255)");
    }
    
    public function testSanity() {
        $this->prepare_tables();
    }
}

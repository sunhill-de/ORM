<?php

namespace Sunhill\ORM\Tests\Unit\Storage;

use Sunhill\ORM\Tests\DBTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Sunhill\ORM\Test\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Database\Seeders\SimpleSeeder;

class StorageBase extends DBTestCase
{
   // use RefreshDatabase;
    
    private $setup = false;
    
    public function setUp() : void {
        parent::setUp();
        if (!$this->setup) {
            $this->seed(SimpleSeeder::class);
            $this->setup = true;
        }
    } 
        
    static protected $is_prepared = false;
    
    
    protected function prepare_read() {
    }
    
    protected function prepare_write() {
    }

    
}

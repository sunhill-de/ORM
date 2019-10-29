<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crawler;
use Tests\sunhill_testcase_db;
use Illuminate\Support\Facades\DB;

class StorageBase extends sunhill_testcase_db
{

    static protected $is_prepared = false;
    
    protected function fill_externalhooks() {
        DB::table('externalhooks')->insert(
            [   ['container_id'=>1,
                'target_id'=>5,
                'action'=>'PROPERTY_UPDATED',
                'subaction'=>'testint',
                'hook'=>'testint_updated',
                'payload'=>'payload'],
                ['container_id'=>2,
                    'target_id'=>5,
                    'action'=>'PROPERTY_UPDATED',
                    'subaction'=>'testint',
                    'hook'=>'testint2_updated',
                    'payload'=>'payload']]
                );    
    }
    
    protected function prepare_tables() {
        parent::prepare_tables();
        $this->create_special_table('dummies');
        $this->create_special_table('passthrus');
        $this->create_special_table('testparents');
        $this->create_special_table('testchildren');
        $this->create_special_table('referenceonlies');
    }
    
    protected function prepare_read() {
        $this->prepare_tables();
        $this->create_load_scenario();
    }
    
    protected function prepare_write() {
        $this->prepare_tables(); 
        $this->create_write_scenario();
    }
    
}

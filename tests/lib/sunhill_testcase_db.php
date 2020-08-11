<?php

namespace Sunhill\Test;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class sunhill_testcase_db extends TestCase {
    
    use RefreshDatabase;
    
    public function setUp(): void {
        parent::setUp();
        $this->prepare_tables();
        $this->seed();         
    }
    
    protected function prepare_tables() {
        $this->create_special_table('dummies');
        $this->create_special_table('passthrus');
        $this->create_special_table('testparents');
        $this->create_special_table('testchildren');
        $this->create_special_table('referenceonlies');
    }
        
    protected function create_special_table(string $name) {
        $special_tables = ['dummies'=>['dummyint int'],
                           'referenceonlies'=>['testint int'],
                           'testparents'=>[ 'parentint int',
                                            'parentchar varchar(255)',
                                            'parentfloat float',
                                            'parenttext text',
                                            'parentdatetime datetime', 
                                            'parentdate date', 
                                            'parenttime time',
                                            "parentenum ENUM('testA','testB','testC')"],
                            'testchildren'=>['childint int',
                                            'childchar varchar(255)',
                                            'childfloat float',
                                            'childtext text',
                                            'childdatetime datetime',
                                            'childdate date',
                                            'childtime time',
                                            "childenum ENUM('testA','testB','testC')"],
                            'passthrus'=>[],
                            'secondlevelchildren'=>['childint int'],
                            'thirdlevelchildren'=>['childchildint int'],
                        ];    
        if (isset($special_tables[$name])) {
            $this->create_table($name,$special_tables[$name]);
        } else {
            die("Tabelle '$name' nicht gefunden.");
        }
    }
    
    protected function create_table(string $name,array $descriptor) {
        $querystr = 'create table if not exists '.$name.' (id int primary key';
        foreach ($descriptor as $entry) {
            $querystr .= ','.$entry;
        }
        $querystr .= ')';
        DB::statement($querystr);
    }    
}
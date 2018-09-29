<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

class sunhill_testcase extends TestCase {
    
    protected function BuildTestClasses() {
        $this->DropTestClasses();
        DB::statement("create table testparents (id int primary key,parentint int,parentchar ".
            "varchar(255), parentfloat float,parenttext text,parentdatetime datetime, parentdate date, parenttime time,".
            "parentenum ENUM('testA','testB','testC'))");
        DB::statement("create table testchildren (id int primary key,childint int,childchar ".
            "varchar(255), childfloat float, childtext text,childdatetime datetime, childdate date, childtime time,".
            "childenum ENUM('testA','testB','testC'))");
        DB::statement("create table passthrus (id int primary key)");
        DB::statement("create table secondlevelchildren (id int primary key,childint int)");        
    }
    
    protected function DropTestClasses() {
        DB::statement("drop table if exists testparents");
        DB::statement("drop table if exists testchildren");
        DB::statement("drop table if exists passthrus");
        DB::statement("drop table if exists secondlevelchildren");        
    }
    
    protected function clear_system_tables() {
        DB::statement("truncate objects");
        DB::statement("truncate tags");
        DB::statement("truncate tags");
        DB::statement("truncate tagcache");
    }
    
    protected function seed() {
        exec(dirname(__FILE__).'/../../application db:seed');
    }
}
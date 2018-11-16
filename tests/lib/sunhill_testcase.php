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
        DB::statement("create table passthrus (id int primary key) ENGINE = MEMORY");
        DB::statement("create table secondlevelchildren (id int primary key,childint int) ENGINE = MEMORY");        
        DB::statement("create table thirdlevelchildren (id int primary key,childchildint int) ENGINE = MEMORY");
        DB::statement("create table dummies (id int primary key,dummyint int) ENGINE = MEMORY");
        DB::statement("create table referenceonlies (id int primary key,testint int) ENGINE = MEMORY");
    }
    
    protected function DropTestClasses() {
        DB::statement("drop table if exists testparents");
        DB::statement("drop table if exists testchildren");
        DB::statement("drop table if exists passthrus");
        DB::statement("drop table if exists secondlevelchildren");        
        DB::statement("drop table if exists thirdlevelchildren");
        DB::statement("drop table if exists dummies");
        DB::statement("drop table if exists referenceonlies");
    }
    
    protected function clear_system_tables() {
        DB::statement("truncate objects");
        DB::statement("truncate tags");
        DB::statement("truncate tags");
        DB::statement("truncate tagcache");
        DB::statement("truncate objectobjectassigns");
        DB::statement("truncate stringobjectassigns");
        DB::statement("truncate tagobjectassigns");
    }
    
    protected function seed() {
        exec(dirname(__FILE__).'/../../application db:seed');
    }
}
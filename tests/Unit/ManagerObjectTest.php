<?php

namespace Tests\Unit;

/**
 * @file ManagerObjectTest.php
 * lang: en
 * dependencies: FilesystemComplexTestCase, objectlist
 */
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Managers\object_manager;
use Sunhill\ORM\Facades\Objects;
use \Sunhill\ORM\SunhillException;
use Illuminate\Support\Facades\DB;


class ManagerObjectTest extends DBTestCase
{
    
    public function testObjectCountNoFilter() {
        $count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,object_manager::count());
    }

    public function testObjectCountNamespaceFilter() {
        $count = DB::table('dummies')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,object_manager::count(['class'=>'\Sunhill\ORM\Test\dummies']));
    }

    public function testObject
    public function testObjectCountClassFilter_nochildren() {
        $count1 = DB::table('persons')->select(DB::raw('count(*) as count'))->first();
        $count2 = DB::table('aquaintances')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count1->count-$count2->count,object_manager::count(['namespace'=>'\Manager\Objects\dummies'],true));
    }

    public function testObjectListNoFilter() {
        $list = object_manager::get_object_list();
        $count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,count($list));
    }

    public function testObjectListClassFilter() {
        $list = object_manager::get_object_list(['class'=>'\Manager\Objects\dummies']);
        $this->assertEquals(ID_PERSON2,$list[1]->get_id());
        $count = DB::table('persons')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,count($list));
    }

    public function testObjectListClassFilter_nochildren() {
        $this->setup_scenario();
        $list = object_manager::get_object_list(['class'=>'\Manager\Objects\person'],true);
        $this->assertEquals(ID_PERSON2,$list[1]->get_id());
        $count1 = DB::table('persons')->select(DB::raw('count(*) as count'))->first();
        $count2 = DB::table('aquaintances')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count1->count-$count2->count,count($list));
    }

}

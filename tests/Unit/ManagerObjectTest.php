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
use \Sunhill\ORM\ORMException;
use Illuminate\Support\Facades\DB;


class ManagerObjectTest extends DBTestCase
{
    public function testCountObjectsViaClass() {
        $count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
        $test = new object_manager();
        $this->assertEquals($count->count,$test->count());
        return $count->count;
    }
    
    /**
     * @depends testCountObjectsViaClass
     * @return unknown
     */
    public function testCountObjectsViaApp($count) {
        $manager = app('\Sunhill\ORM\Managers\object_manager');
        $this->assertEquals($count,$manager->count());
        return $count;
    }
    
    /**
     * @depends testCountObjectsViaClass
     * @return unknown
     */
    public function testCountObjectsViaFacade($count) {
        $this->assertEquals($count,Objects::count());
    }
        
    public function testObjectCountNamespaceFilter() {
        $count = DB::table('dummies')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,Objects::count(['class'=>'Sunhill\ORM\Tests\Objects\ts_dummy']));
    }

    public function testObjectCountNameFilter() {
        $count = DB::table('dummies')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,Objects::count('dummy'));
    }
       
    public function testObjectCountClassFilter_nochildren() {
        $count1 = DB::table('testparents')->select(DB::raw('count(*) as count'))->first();
        $count2 = DB::table('testchildren')->select(DB::raw('count(*) as count'))->first();
        $count3 = DB::table('passthrus')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count1->count-$count2->count-$count3->count,Objects::count('testparent',true));
    }

    public function testObjectListNoFilter() {
        $list = Objects::get_object_list();
        $count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,count($list));
    }

    public function testObjectListClassFilter() {
        $list = Objects::get_object_list('dummy');
        $this->assertEquals(2,$list[1]->get_id());
        $count = DB::table('dummies')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,count($list));
    }

    public function testObjectListClassFilter_nochildren() {
        $list = Objects::get_object_list('testparent',true);
        $this->assertEquals(5,$list[0]->get_id());
        $count1 = DB::table('testparents')->select(DB::raw('count(*) as count'))->first();
        $count2 = DB::table('testchildren')->select(DB::raw('count(*) as count'))->first();
        $count3 = DB::table('passthrus')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count1->count-$count2->count-$count3->count,count($list));
    }

}

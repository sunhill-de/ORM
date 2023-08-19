<?php

namespace Sunhill\ORM\Tests\Unit\Managers;

use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DatabaseTestCase;
use Sunhill\ORM\Tests\TestCase;
use Sunhill\ORM\Tests\Testobjects\Dummy;
use Sunhill\ORM\Managers\ObjectManager;
use Sunhill\ORM\Facades\Objects;

class ObjectManagerTest extends DatabaseTestCase
{
 
    public function testCountObjectsViaClass() {
        $count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
        $test = new ObjectManager();
        $this->assertEquals($count->count,$test->count());
        return $count->count;
    }
    
    /**
     * @depends testCountObjectsViaClass
     * @return unknown
     */
    public function testCountObjectsViaApp($count) {
        $manager = app('\Sunhill\ORM\Managers\ObjectManager');
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
        $this->assertEquals($count->count,Objects::count(['class'=>Dummy::class]));
    }
    
    public function testObjectCountNameFilter() {
        $count = DB::table('dummies')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,Objects::count('dummy'));
    }
    
    public function testObjectCountClassFilter_nochildren() {
        $this->assertEquals(8,Objects::count('testparent',true));
    }
    
    public function testObjectListNoFilter() {
        $list = Objects::getObjectList();
        $count = DB::table('objects')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,count($list));
    }
    
    public function testObjectListClassFilter() {
        $list = Objects::getObjectList('dummy');
        $item = $list[1];
        $this->assertEquals(2,$list[1]->id);
        $count = DB::table('dummies')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,count($list));
    }
    
    public function testObjectListClassFilter2() {
        $list = Objects::getObjectList('testparent');
        $count = DB::table('testparents')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals($count->count,count($list));
    }
    
    public function testObjectListClassFilter_nochildren() {
        $list = Objects::getObjectList('testparent',true);
        $this->assertEquals(9,$list[0]->id);
        $count1 = DB::table('testparents')->select(DB::raw('count(*) as count'))->first();
        $count2 = DB::table('testchildren')->select(DB::raw('count(*) as count'))->first();
        $count3 = DB::table('testsimplechildren')->select(DB::raw('count(*) as count'))->first();
        $this->assertEquals(8,count($list));
    }
    
    public function testClearObjects_nochildren() {
        Objects::clearObjects('dummy');
        $count = DB::table('dummies')->select(DB::raw('count(*) as count'))->first()->count;
        $this->assertEquals(0,$count);
    }
    
    public function testClearObjects_children() {
        $countc_before = DB::table('testchildren')->select(DB::raw('count(*) as count'))->first()->count;
        $countp_before = DB::table('testparents')->select(DB::raw('count(*) as count'))->first()->count;
        $counto_before = DB::table('objects')->select(DB::raw('count(*) as count'))->first()->count;
        Objects::clearObjects('testchild');
        $countc_after = DB::table('testchildren')->select(DB::raw('count(*) as count'))->first()->count;
        $countp_after = DB::table('testparents')->select(DB::raw('count(*) as count'))->first()->count;
        $counto_after = DB::table('objects')->select(DB::raw('count(*) as count'))->first()->count;
        $this->assertEquals(0,$countc_after,'Number of testchildren is not 0 as expected.');
        $this->assertEquals($countp_before-$countc_before,$countp_after,'Number of testparents is not as expected.');
        $this->assertEquals($counto_before-$countc_before,$counto_after,'Number of objects is not as expected.');
    }
    
}

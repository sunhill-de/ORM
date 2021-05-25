<?php

namespace Sunhill\ORM\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sunhill\ORM\Tests\DBTestCase;
use Sunhill\ORM\Objects\oo_object;
use Sunhill\ORM\Tests\Objects\ts_dummy;
use Sunhill\ORM\Tests\Objects\ts_testparent;
use Sunhill\ORM\Tests\Objects\ts_testchild;
use Sunhill\ORM\Objects\oo_tag;
use Sunhill\ORM\Facades\Classes;
use Sunhill\ORM\Seeder\Seeder;

class TestSeeder extends Seeder {

    public function Seed() {
        $this->SeedObject(ts_dummy::class,['dummyint'],[
            [1],
            'Key'=>[2],
            [3]
        ]);
    }
}

class TestSeeder2 extends Seeder {

    public function Seed() {
        $this->SeedObject(ts_dummy::class,['dummyint'],[
            'Dummy1'=>[1],
            'Dummy2'=>[2],
            'Dummy3'=>[3]
        ]);
        $this->SeedObject(ts_testparent::class,
            ['parentint','parentchar','parentfloat','parenttext','parentdatetime','parentdate','parenttime',
             'parentenum','parentobject','parentsarray','parentoarray'],
            [
                'parent1'=>[1,'A',1.1,'AA','2020-02-02 10:00:00','2020-02-03','10:01:00','testA',null,null,null],
                'parent2'=>[2,'B',2.2,'BB','2021-02-02 10:00:00','2021-02-03','11:01:00','testB','->Dummy1',['AAA','BBB','CCC'],['->Dummy1','->Dummy2']],                
            ]);
    }
    
}

class TestSeeder3 extends Seeder {
    
    public function Seed() {
        $this->SeedObject(ts_dummy::class,['dummyint'],[
            'Dummy1'=>[1],
            'Dummy2'=>[2],
            'Dummy3'=>[3]
        ]);
        $this->SeedObject(ts_testparent::class,
            ['parentint','parentchar','parentfloat','parenttext','parentdatetime','parentdate','parenttime',
                'parentenum','parentobject','parentsarray','parentoarray'],
            [
                'parent1'=>[1,'A',1.1,'AA','2020-02-02 10:00:00','2020-02-03','10:01:00','testA',null,null,null],
                'parent2'=>[2,'B',2.2,'BB','2021-02-02 10:00:00','2021-02-03','11:01:00','testB','->Dummy1',['AAA','BBB','CCC'],['->Dummy1','->Dummy2']],
            ]);
        $this->SeedObject(ts_testchild::class,
            ['parentint','parentchar','parentfloat','parenttext','parentdatetime','parentdate','parenttime',
                'parentenum','parentobject','parentsarray','parentoarray',
                'childint','childchar','childfloat','childtext','childdatetime','childdate','childtime',
                'childenum','childobject','childsarray','childoarray'],
            [
                'child1'=>[ 1,'A',1.1,'AA','2020-02-02 10:00:00','2020-02-03','10:01:00','testA',null,null,null,
                            11,'CA',2.1,'CAA','2020-02-02 10:00:00','2020-02-03','10:01:00','testC',null,null,null],
                'child2'=>[ 2,'B',2.2,'BB','2021-02-02 10:00:00','2021-02-03','11:01:00','testB','->Dummy1',['AAA','BBB','CCC'],['->Dummy1','->Dummy2'],
                            3,'CB',3.2,'CB','2021-02-02 10:00:00','2021-02-03','11:01:00','testB','->Dummy2',['AAAA','BBBB','CCCC'],['->Dummy3','->Dummy1']
                ],
            ]);
    }
    
}

class SeederTest extends DBTestCase
{
       
	public function testSeedCreatesValue() {
	   DB::table('dummies')->truncate();
	   DB::table('objects')->truncate();
	   $test = new TestSeeder();
	   $test->Seed();
	   $this->assertDatabaseHas('dummies',['dummyint'=>1]);
	   return $test;
	}

	/**
	 * @depends testSeedCreatesValue
	 * @param unknown $test
	 */
	public function testSeedCreatesKey($test) {
	   $this->assertEquals(2,$test->GetKeyObject('Key')->dummyint); 
	}
	
	public function testComplexSeed() {
	    DB::table('dummies')->truncate();
	    DB::table('objects')->truncate();
	    DB::table('testparents')->truncate();
	    DB::table('testchildren')->truncate();
	    DB::table('objectobjectassigns')->truncate();
	    DB::table('stringobjectassigns')->truncate();
	    $test = new TestSeeder2();
	    $test->Seed();
	    $this->assertDatabaseHas('testparents',['parentint'=>1]);
	    return $test;
	}
	
	/**
	 * @depends testComplexSeed
	 */
	public function testHasObject($test) {
	    $this->assertEquals(1,$test->GetKeyObject('parent2')->parentobject->dummyint);
	}
	
	/**
	 * @depends testComplexSeed
	 */
	public function testHasSArray($test) {
	    $this->assertEquals('AAA',$test->GetKeyObject('parent2')->parentsarray[0]);
	}
	
	/**
	 * @depends testComplexSeed
	 */
	public function testHasOArray($test) {
	    $this->assertEquals(2,$test->GetKeyObject('parent2')->parentoarray[1]->dummyint);
	}
	
	public function testInheritedSeed() {
	    DB::table('dummies')->truncate();
	    DB::table('objects')->truncate();
	    DB::table('testparents')->truncate();
	    DB::table('testchildren')->truncate();
	    DB::table('objectobjectassigns')->truncate();
	    DB::table('stringobjectassigns')->truncate();
	    $test = new TestSeeder3();
	    $test->Seed();
	    $this->assertDatabaseHas('testparents',['parentint'=>1]);
	    return $test;
	}
	
	/**
	 * @depends testInheritedSeed
	 */
	public function testInheritedHasObject($test) {
	    $this->assertEquals(1,$test->GetKeyObject('child2')->parentobject->dummyint);
	    $this->assertEquals(2,$test->GetKeyObject('child2')->childobject->dummyint);
	}
	
	/**
	 * @depends testInheritedSeed
	 */
	public function testInheritedHasSArray($test) {
	    $this->assertEquals('AAA',$test->GetKeyObject('child2')->parentsarray[0]);
	    $this->assertEquals('AAAA',$test->GetKeyObject('child2')->childsarray[0]);
	}
	
	/**
	 * @depends testInheritedSeed
	 */
	public function testInheritedHasOArray($test) {
	    $this->assertEquals(2,$test->GetKeyObject('child2')->parentoarray[1]->dummyint);
	    $this->assertEquals(1,$test->GetKeyObject('child2')->childoarray[1]->dummyint);
	}
	
}
